/** @jsx React.DOM */
var $            = require('jquery');
var React        = require('react');
var OptionsPage  = require('./components/OptionsPage');
var optionsStore = require('./app').optionsStore;

$(document).ready(function() {
  $('.wrap-static').remove();

  React.renderComponent(
    <OptionsPage options={optionsStore.getOptions()} />,
    document.getElementById('wp-spoiler-alert-app')
  );
});

