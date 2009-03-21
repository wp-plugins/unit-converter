=== Plugin Name ===
Contributors: miknight
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4076962
Tags: metric, imperial, english, standard, unit, convert
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 0.2

Detects units of measurement in your blog text and automatically displays the metric or imperial equivalent in one of several possible ways.

== Description ==

This plugin detects units of measurement (e.g. "10 lbs") and will add the metric or imperial equivalent (e.g. "4.5 kilograms"). The converted measurement can be added in several ways:

* as a mouse-over, or
* in brackets after the original measurement, e.g. "10 lbs (4.5 kilograms)".

It is useful if you use lots of measurements and are writing for an international audience.

== Installation ==

1. Upload the `unit-converter/` directory to the `/wp-content/plugins/` directory,
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= The mouseover method is the default. How do I use the parentheses? =

As there is no options page (yet), you will need to edit the `/wp-content/plugins/unit-converter.php` file around line 31 and change:

`$display_mode = 'mouseover';`
to

`$display_mode = 'parentheses';`

= Does it really require WordPress 2.7? =

I'm not sure because I haven't tested it on anything earlier. If you use an earlier version and it works, let me know!

== Currently Supported Conversions ==

* Kilograms <-> pounds
* Centimetres <-> inches
* Metres <-> feet
* Kilometres <-> miles
* Litres <-> gallons
* Kilojoules <-> calories

== Bugs ==

* Converted measurements will always be written in their canonical/long name, e.g. 'kilograms' instead of 'kg'.
* Cannot handle quoted aliases (e.g. 5' or 9") or composite measurements, e.g. 5'9".

To report any bugs please do so from the [plugin homepage](http://miknight.com/projects/unit-converter/ "Visit the plugin home page").

== Plans ==

* An options page to configure various aspects of the plugin.
* Auto-detection for which measurement abbreviation to use for the converted unit (lbs -> kg, pounds -> kilograms).

== ChangeLog ==

= 0.2 - 2009-03-22 =
* Added kilojoule <-> calorie converter.
* Now require the match text to be case sensitive (e.g. 2L or 5kg only, not 2l or 5KG).

= 0.1 - 2009-03-19 =
* Initial version.
