(function($) {

  var StylesheetBuilder = function() {
    this.value = '';
    this.currentSelector = '';
  };

  StylesheetBuilder.prototype.selector = function(name) {
    if (name !== this.currentSelector && this.currentSelector !== '') {
      this.value += "}\n\n";
    }

    this.currentSelector = name;
    this.value += name + " {\n";
  };

  StylesheetBuilder.prototype.property = function(name, value, prefixes) {
    var n = 0;
    var prefix;
    if (typeof prefixes !== 'undefined') {
      n = prefixes.length;
    }

    for (var i = 0; i < n; i++) {
      prefix = prefixes[i];
      this.value += this.toProperty('-' + prefix + '-' + name, value);
    }

    this.value += this.toProperty(name, value);
  };

  StylesheetBuilder.prototype.toProperty = function(name, value) {
    return "  " + name + ': ' + value + ";\n";
  };

  StylesheetBuilder.prototype.build = function() {
    this.value += '}';
    return this.value;
  };

  var SpoilerStylesheet = function() {

  };

  SpoilerStylesheet.prototype.buildCSS = function() {
    var options     = this.getOptions();
    var b           = new StylesheetBuilder();
    var maxBlur     = 'blur(' + options.max + 'px)';
    var partialBlur = 'blur(' + options.partial + 'px)';
    var noBlur      = 'blur(0px)';

    b.selector('.spoiler-hidden');
    b.property('-webkit-transition-property', '-webkit-filter');
    b.property('-webkit-transition-duration', '0.1s');
    b.property('filter', maxBlur, ['webkit', 'moz']);
    b.property('filter', this.mozBlur(options.max));
    b.property('filter', this.ieBlur(options.max));

    b.selector('.spoiler-hidden:hover');
    b.property('filter', partialBlur, ['webkit', 'moz']);
    b.property('filter', this.mozBlur(options.partial));
    b.property('filter', this.ieBlur(options.partial));

    b.selector('.spoiler-visible');
    b.property('-webkit-transition-propertyerty', '-webkit-filter');
    b.property('-webkit-transition-duration', '0.2s');
    b.property('filter', noBlur, ['webkit', 'moz']);
    b.property('filter', this.mozBlur(0));
    b.property('filter', this.ieBlur(0));

    return b.build();
  };

  SpoilerStylesheet.prototype.mozBlur = function(radius) {
    var value = "url(\"";
    value += "data:image/svg+xml;utf8,";
    value += "<svg xmlns='http://www.w3.org/2000/svg'>";
    value += "<filter id='blur'>";
    value += "<feGaussianBlur stdDeviation='" + radius + "' />";
    value += "</filter>";
    value += "</svg>#blur";
    value += "\")";

    return value;
  };

  SpoilerStylesheet.prototype.ieBlur = function(radius) {
    var value = "progid:DXImageTransform.Microsoft.Blur";
    value += "(";
    value += "pixelradius=" + radius;
    value += ")";

    return value;
  };

  SpoilerStylesheet.prototype.build = function() {
    var style = document.createElement('style');
    style.type = 'text/css';
    style.media = 'all';
    style.innerHTML = this.buildCSS();

    return style;
  };

  SpoilerStylesheet.prototype.append = function(parent) {
    var options = this.getOptions();
    if (options.custom) {
      return;
    }

    if (typeof parent === 'undefined') {
      parent = $('head');
    }

    parent.append(this.build());
  };

  SpoilerStylesheet.prototype.getOptions = function() {
    var options = window.wp_spoiler_alert_plugin;
    this.toInteger('max', options);
    this.toInteger('partial', options);
    this.toBoolean('custom', options);

    return options;
  };

  SpoilerStylesheet.prototype.toInteger = function(name, options) {
    if (options.hasOwnProperty(name)) {
      options[name] = parseInt(options[name], 10);
    }
  };

  SpoilerStylesheet.prototype.toBoolean = function(name, options) {
    if (options.hasOwnProperty(name)) {
      options[name] = options[name] === '1' ? true : false;
    }
  };

  $(document).ready(function() {
    var stylesheet = new SpoilerStylesheet();
    stylesheet.append();

    $(document).spoilerAlert();
  });

}(jQuery));
