=== wp-spoiler-alert ===
Contributors: dsawardekar
Donate link: http://pressing-matters.io/
Tags: spoiler alert, spoiler
Requires at least: 3.5.0
Tested up to: 3.9
Stable tag: 0.2.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shortcode to hide Spoilers in your Posts.

== Description ==

This plugin adds the shortcode `[spoiler]` to WordPress. Any content
wrapped inside this shortcode is blurred out. It can be revealed by
clicking on the blurred content.

The shortcode takes a `mode` parameter, which defaults to `block`. This
wraps your content inside a div. To add spoilers that are inside to your
text use the mode `inline`.

Eg:- Some [spoiler mode='inline']super secret stuff[/spoiler] here!

== Installation ==

1. Click Plugins > Add New in the WordPress admin panel.
1. Search for "wp-spoiler-alert" and install.

###Customization###

The Partial and Max blur options control the amount of the blur effect
applied. A value of 0 applies no blur, 20 applies a very large blur
effect, etc.

The Custom option allows you to override the manner in which to hide the
spoilers. This is useful to tune the effect to your site's theme.

The plugin checks for a `custom.css` in your current
theme's directory at *{current_theme}/wp-spoiler-alert/custom.css*.

If this CSS file is present it will be added to the page automatically.

For eg:- the CSS below makes all spoilers into red blocks.

    .spoiler-hidden {
      color: red;
      background: red;
    }

    .spoiler-hidden:hover {
      color: white;
      background: white;
      outline: 1px solid red;
    }

    .spoiler-hidden img {
      opacity: 0;
    }

    .spoiler-visible {
      color: inherit;
      background: inherit;
      outline: none;
    }

== Screenshots ==

1. Screenshot 1
2. Screenshot 2

== Credits ==

* Inspired by the [spoilerAlert](https://github.com/joshbuddy/spoiler-alert) jQuery plugin by [Joshua Hull](https://github.com/joshbuddy)

== Upgrade Notice ==

* WP-Spoiler-Alert requires PHP 5.3.2+

== Frequently Asked Questions ==

* Can I change the style of the spoiler?

Yes, see the customization section under Installation.

== Changelog ==

= 0.2.1 =

* Removes debug messages from 0.2.0

= 0.2.0 =

* Switches to Arrow 0.4.1.

= 0.1.2 =

* First release on wordpress.org

= 0.1.0 =

* Initial Release
