(function($) {

  var Spoiler = function() {

  };

  Spoiler.prototype.enable = function(element) {
    var self = this;
    if (typeof element === 'undefined') {
      element = $(document);
    }

    element.on('click', function(event) {
      self.toggle($(event.target));
    });
  };

  Spoiler.prototype.toggle = function(element) {
    var block = this.findSpoiler(element, 'spoiler-hidden');

    if (block !== null) {
      this.showBlock(block);
      return;
    }

    block = this.findSpoiler(element, 'spoiler-visible');

    if (block !== null) {
      this.hideBlock(block);
      return;
    }
  };

  Spoiler.prototype.findSpoiler = function(element, name) {
    if (element.hasClass(name)) {
      return element;
    }

    var selector = 'div[class^=' + name + ']';
    var closest = element.closest(selector);

    if (closest.hasClass('spoiler-hidden')) {
      return closest;
    } else if (closest.hasClass('spoiler-visible')) {
      return closest;
    }

    return null;
  };

  Spoiler.prototype.show = function(blocks) {
    var n = blocks.length;
    for (var i = 0; i < n; i++) {
      this.showBlock(blocks[i]);
    }
  };

  Spoiler.prototype.hide = function(blocks) {
    var n = blocks.length;
    for (var i = 0; i < n; i++) {
      this.hideBlock(blocks[i]);
    }
  };

  Spoiler.prototype.showBlock = function(block) {
    this.switchClass(block, 'spoiler-hidden', 'spoiler-visible');
  };

  Spoiler.prototype.hideBlock = function(block) {
    this.switchClass(block, 'spoiler-visible', 'spoiler-hidden');
  };

  Spoiler.prototype.switchClass = function(block, from, to) {
    $(block).toggleClass(from);
    $(block).toggleClass(to);
  };

  $.fn.spoilerAlert = function() {
    var spoiler = new Spoiler();
    spoiler.enable();
  };

}(jQuery));

