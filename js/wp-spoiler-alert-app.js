Ember.EasyForm.Config.registerWrapper('wp-input', {
  inputTemplate: 'wp-input',
  errorClass: 'validation-error'
});

Ember.EasyForm.Error.reopen({
  classNameBindings: ['errorText:nohide:hide']
});

Arrow = Em.Namespace.create({
  version: '0.0.0'
});

Arrow.Application = Em.Application.extend({
  slug: null,

  config: function() {
    var configKey = this.get('slug') + '-app';
    configKey = configKey.replace(/-/g, '_');

    return window[configKey];
  }.property(),

  apiEndpoint: function() {
    return this.get('config').apiEndpoint;
  }.property('config'),

  nonce: function() {
    return this.get('config').nonce;
  }.property('config'),

  rootElement: function() {
    return "#" + this.get('slug');
  }.property()
});

Arrow.Adapter = Em.Object.extend({
  apiEndpoint: null,
  nonce: null,

  urlFor: function(controller, operation) {
    var params        = {};
    params.controller = controller;
    params.operation  = operation;
    params.nonce      = this.get('nonce');

    return this.get('apiEndpoint') + '&' + jQuery.param(params);
  },

  ajax: function(controller, operation, params) {
    params.url = this.urlFor(controller, operation);

    return new Ember.RSVP.Promise(function(resolve, reject) {
      return jQuery.ajax(params)
      .then(function(response) {
        if (response === '0') {
          reject('Not Logged In');
        } else if (response.success) {
          resolve(response.data);
        } else {
          reject(response.data.error);
        }
      })
      .fail(function(response) {
        var error;
        if (response.statusText === 'timeout') {
          error = 'Request Timed Out.';
        } else if (response.responseJSON) {
          error = response.responseJSON.data.error;
        } else {
          error = 'Unknown Response.';
        }

        reject(error);
      });
    });
  }
});

Arrow.OptionsModel = Em.Object.extend(Ember.Validations.Mixin, {
  fields: [],
  resourceName: 'options',

  serialize: function() {
    var json   = {};
    var fields = this.get('fields');
    var self   = this;

    fields.forEach(function(field) {
      json[field] = self.get(field);
    });

    return JSON.stringify(json);
  },

  deserialize: function(json) {
    var fields = this.get('fields');
    var self   = this;

    fields.forEach(function(field) {
      self.set(field, json[field]);
    });
  },

  save: function() {
    var self   = this;
    var params = {
      type: 'POST',
      data: this.serialize(),
      dataType: 'json'
    };

    return this.get('adapter').ajax(
      this.resourceName, 'update', params
    )
    .then(function(json) {
      var model  = self.instance();
      model.deserialize(json);

      return model;
    });
  },

  reset: function() {
    var self   = this;
    var params = {
      type: 'GET'
    };

    return this.get('adapter').ajax(
      this.resourceName, 'delete', params
    )
    .then(function(json) {
      self.deserialize(json);
      return true;
    });
  },

  load: function() {
    var self = this;
    var params = {
      type: 'GET'
    };

    return this.get('adapter').ajax(
      this.resourceName, 'index', params
    )
    .then(function(json) {
      var model  = self.instance();
      model.deserialize(json);

      return model;
    });
  }
});

Arrow.NoticeModel = Em.Object.extend({
  type: '',
  messages: Ember.A(),

  change: function(type, value) {
    var messages = this.toMessages(value);
    this.set('type', type);
    this.set('messages', messages);
  },

  toMessages: function(value) {
    var messages = Ember.A();
    var type = typeof(value);

    if (type === 'string') {
      messages.push(value);
    } else if (type === 'object') {
      for (var field in value) {
        if (value.hasOwnProperty(field)) {
          var errors = value[field];
          var n = errors.length;
          var i;

          for (i = 0; i < n; i++) {
            messages.push(errors[i]);
          }
        }
      }
    } else {
      messages.push('Unknown response from server.');
    }

    return messages;
  },

  available: function() {
    return this.get('type') !== '' && this.get('message') !== '';
  }.property('type', 'message'),

  progress: function() {
    return this.get('type') === 'progress';
  }.property('type'),

  cssClass: function() {
    var type = this.get('type');
    if (type === 'progress') {
      return 'updated progress';
    } else {
      return type;
    }
  }.property('type')
});

Arrow.ApplicationRoute = Em.Route.extend({
  model: function(params) {
    return {
      notice:Arrow.NoticeModel.create({})
    };
  },

  redirect: function() {
    this.transitionTo('options');
  },

  actions: {
    notice: function(type, value) {
      var notice = this.modelFor('application').notice;
      notice.change(type, value);
    },
    error: function(reason) {
      var element = jQuery('.progress-stub');
      element.attr('class', 'error');

      var content = jQuery('p strong', element);
      content.text("Error: " + reason);
    }
  }
});

Arrow.OptionsRoute = Em.Route.extend({
  model: function(params) {
    return Arrow.OptionsModel.create({}).load();
  },
  afterModel: function(model) {
    jQuery('.progress-stub').remove();
  }
});

Arrow.OptionsController = Em.ObjectController.extend({
  saveModel: function(model) {
    var self = this;
    self.send('notice', 'progress', 'Saving Settings ...');

    model.save()
    .then(function(model) {
      self.send('notice', 'updated', 'Settings Saved.');
    })
    .catch(function(error) {
      self.send('notice', 'error', error);
    });
  },

  actions: {
    save: function() {
      var model = this.get('content');
      var self  = this;

      model.validate()
      .then(function() {
        return self.saveModel(model);
      })
      .catch(function(error) {
        self.send('notice', 'error', 'Validation failed.');
      });
    },

    reset: function() {
      var confirmed = confirm('Restore Defaults: Are you sure?');
      if (!confirmed) return;

      var self = this;
      var model = this.get('content');
      self.send('notice', 'progress', 'Restoring Defaults ...');

      model.reset()
      .then(function(success) {
        self.send('notice', 'updated', 'Restored Defaults.');
      })
      .catch(function(error) {
        self.send('notice', 'error', error);
      });
    }
  }
});

/**************************************************************/
/* WpSpoilerAlert App                                         */
/**************************************************************/

App = WpSpoilerAlert = Arrow.Application.create({
  slug: 'wp-spoiler-alert'
});

App.OptionsModel = Arrow.OptionsModel.extend({
  fields: [
    'max',
    'partial',
    'tooltip',
    'custom'
  ],

  validations: {
    max: {
      presence: true,
      numericality: {
        onlyInteger: true,
        greaterThanOrEqualTo: 0,
        lessThanOrEqualTo: 20
      }
    },
    partial: {
      presence: true,
      numericality: {
        onlyInteger: true,
        greaterThanOrEqualTo: 0,
        lessThanOrEqualTo: 20
      }
    },
    tooltip: {
      presence: false
    },
    custom: {
      presence: true
    }
  },

  instance: function() {
    return App.OptionsModel.create({});
  },

  adapter: Arrow.Adapter.create({
    apiEndpoint: App.get('apiEndpoint'),
    nonce: App.get('nonce')
  })
});

App.Router.map(function() {
  this.resource('options', { path: '/options' });
});

App.ApplicationRoute  = Arrow.ApplicationRoute.extend();
App.OptionsRoute      = Arrow.OptionsRoute.extend({
  model: function() {
    return App.OptionsModel.create({}).load();
  }
});

App.OptionsController = Arrow.OptionsController.extend();
