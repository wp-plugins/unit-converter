=== Plugin Name ===
Contributors: miknight
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4076962
Tags: metric, customary, imperial, standard, unit, convert, text, cooking, nutrition, fitness
Requires at least: 2.7
Tested up to: 2.8.2
Stable tag: 0.5

Detects units of measurement in your blog text and automatically displays the metric or US customary equivalent in one of several possible ways.

== Description ==

This plugin detects units of measurement (e.g. "10 lbs") and will add the metric or non-metric equivalent (e.g. "4.5 kilograms"). The converted measurement can be added in several ways:

* as a mouse-over, or
* in brackets after the original measurement, e.g. "10 lbs (4.5 kilograms)".

It is useful if you use lots of measurements and are writing for an international audience.

= Currently Supported Conversions =

* Celsius <-> Fahrenheit
* Centimetres <-> inches
* Grams <-> ounces
* Kilograms <-> pounds
* Kilometres <-> miles
* Kilojoules <-> (food) calories
* Litres <-> gallons
* Metres <-> feet
* Millilitres <-> fluid ounces

Note that these non-metric units are in [US customary units](http://en.wikipedia.org/wiki/United_States_customary_units).

== Installation ==

1. Upload the `unit-converter/` directory to the `/wp-content/plugins/` directory,
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 0.5 - 2009-07-26 =
* Added Celsius <-> Fahrenheit converter.
* Tested up to WordPress 2.8.2.

= 0.4 - 2009-06-12 =
* Added millilitre <-> fluid ounce converter.
* Tested up to WordPress 2.8.

= 0.3 - 2009-03-24 =
* Fixed the kilojoule <-> calorie converter (was using calories instead of food calories).
* Added grams <-> ounces converter.
* Increased the precision of all conversions.

= 0.2 - 2009-03-22 =
* Added kilojoule <-> calorie converter.
* Now require the match text to be case sensitive (e.g. 2L or 5kg only, not 2l or 5KG).

= 0.1 - 2009-03-19 =
* Initial version.

== Frequently Asked Questions ==

= The mouseover method is the default. How do I use the parentheses? =

As there is no options page (yet), you will need to edit the `/wp-content/plugins/unit-converter.php` file around line 31 and change:

`$display_mode = 'mouseover';`

to

`$display_mode = 'parentheses';`

= Does it really require WordPress 2.7? =

I'm not sure because I haven't tested it on anything earlier. If you use an earlier version and it works, let me know!

= Why does it require PHP 5 when WordPress supports PHP 4? =

It's time to move forward. See [GoPHP5.org](http://www.gophp5.org/) for more.

== Bugs ==

* Converted measurements will always be written in their canonical/long name, e.g. 'kilograms' instead of 'kg'.
* Cannot handle quoted aliases (e.g. 5' or 9") or composite measurements, e.g. 5'9".

To report any bugs please do so from the [plugin homepage](http://miknight.com/projects/unit-converter/ "Visit the plugin home page").

== Plans ==

* An options page to configure various aspects of the plugin.
* Auto-detection for which measurement abbreviation to use for the converted unit (lbs -> kg, pounds -> kilograms).
* An option to use Imperial measurements as distinct from US customary units.
