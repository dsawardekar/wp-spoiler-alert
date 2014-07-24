/** @jsx React.DOM */
var ArrowApi    = require('./ext/ArrowApi').ArrowApi;
var Config      = require('./config');
var Options     = require('./stores/options');

/* app initialization */
/* TODO: Fix this madness */
var pluginSlug   = 'wp_spoiler_alert_dist_assets_wp_spoiler_alert';
var config       = new Config(pluginSlug);
var configParams = config.getParams();
var api          = new ArrowApi(configParams);
var optionsStore = new Options(configParams, api);

module.exports = {
  api: api,
  config: config,
  optionsStore: optionsStore
};
