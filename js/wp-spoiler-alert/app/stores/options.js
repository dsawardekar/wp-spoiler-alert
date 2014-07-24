var OptionsStore = function(options, api) {
  this.options = options;
  this.api     = api;
};

OptionsStore.prototype.getOptions = function() {
  return this.options;
};

OptionsStore.prototype.save = function(options) {
  var promise = this.api.patch('options', options);
  var self = this;

  return promise.then(function(json) {
    self.options = json;
  });
};

OptionsStore.prototype.reset = function() {
  var promise = this.api.delete('options', {});
  var self = this;

  return promise.then(function(json) {
    self.options = json;
  });
};

module.exports = OptionsStore;
