=== Plugin Name ===
Contributors: miknight
Donate link: http://miknight.com
Tags: metric, imperial, english, standard, unit, convert
Requires at least: 2.7
Tested up to: 2.7
Stable tag: trunk

Detects units of measurement in your blog text and automatically displays the metric or imperial equivalent in one of several possible ways.

== Description ==

This plugin detects units of measurement (e.g. "10 lbs") and will add the metric or imperial equivalent (e.g. "4.5 kg"). The converted measurement can be added in several ways:

* In brackets after the original measurement, e.g. "10 lbs (4.5 kg)",
* As a mouse-over.

It is useful if you use lots of measurements and are writing for an international audience.

== Installation ==

1. Upload the `unit-converter/` directory to the `/wp-content/plugins/` directory,
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Currently Supported Units ==

* Kilograms/pounds
* Centimetres/inches
* Metres/feet
* Kilometres/miles
* Litres/gallons

== Bugs ==

* No mouse-over support yet.
* Needs an options page to configure various aspects of the plugin.
* Cannot handle quoted aliases (e.g. for inches & feet) or composite measurements (e.g. 5'9").
