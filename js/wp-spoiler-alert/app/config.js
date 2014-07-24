var Config = function(configKey) {
  this.configKey = configKey;
  this.load();
};

Config.prototype.load = function() {
  this.params = window[this.configKey];

  this.params.max     = parseInt(this.params.max, 10);
  this.params.partial = parseInt(this.params.partial, 10);
  this.params.custom  = this.params.custom === '1';
};

Config.prototype.getParam = function(name) {
  return this.params[name];
};

Config.prototype.getParams = function() {
  return this.params;
};

module.exports = Config;
