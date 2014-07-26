/** @jsx React.DOM */

require('./styles/app.css');

var ArrowApi = require('./ext/ArrowApi').ArrowApi;
var Config   = require('./config');
var Options  = require('./stores/options');

/* app initialization */
var pluginSlug   = 'wp_spoiler_alert';
var config       = new Config(pluginSlug);
var configParams = config.getParams();
var api          = new ArrowApi(configParams);
var optionsStore = new Options(configParams, api);

module.exports = {
  pluginSlug   : pluginSlug,
  api          : api,
  config       : config,
  optionsStore : optionsStore
};
