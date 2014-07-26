var Config = function(configKey) {
  this.configKey = configKey;
  this.load();
};

Config.prototype = {

  load: function() {
    this.params = window[this.configKey].options;
  },

  getParam: function(name) {
    return this.params[name];
  },

  getParams: function() {
    return this.params;
  },

  translate: function(name) {
    if (this.params.hasOwnProperty(name)) {
      return this.params[name];
    } else {
      return name;
    }
  }

};


module.exports = Config;
