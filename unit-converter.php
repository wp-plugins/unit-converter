<?php
/*
Plugin Name: Unit Converter
Plugin URI: http://miknight.com/projects/unit-converter
Description: Detects units of measurement in your blog text and automatically displays the metric or US customary equivalent in one of several possible ways.
Version: 0.5.3
Author: Michael Knight
Author URI: http://miknight.com

Copyright 2009 Michael Knight <jedimike@gmail.com>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class UnitConverter
{
	private $units = array();
	private $maps = array();
	private $display_mode = 'mouseover';

	/**
	 * Sets up conversion units and alias mappings.
	 */
	public function __construct()
	{
		// Kilograms <-> Pounds
		$this->addConversion('kilogram', 'pound', 2.20462262);
		$this->addMaps('kilogram', array('kg', 'kgs', 'kilo', 'kilos'));
		$this->addMaps('pound', array('lb', 'lbs'));

		// Centimetres <-> Inches
		$this->addConversion('centimetre', 'inch', 0.393700787);
		$this->addMaps('centimetre', array('cm', 'cms', 'centimeter', 'centimeters'));
		$this->addMaps('inch', array('in'));

		// Metres <-> Feet
		$this->addConversion('metre', 'foot', 3.2808399);
		$this->addMaps('metre', array('m', 'meter', 'meters'));
		$this->addMaps('foot', array('ft'));

		// Kilometres <-> Miles
		$this->addConversion('kilometre', 'mile', 0.621371192);
		$this->addMaps('kilometre', array('km', 'kms', 'kilometer', 'kilometers'));
		$this->addMaps('mile', array('mi'));

		// Litres <-> Gallons
		$this->addConversion('litre', 'gallon', 0.264172052);
		$this->addMaps('litre', array('L', 'liter', 'liters'));
		$this->addMaps('gallon', array('gal', 'gals'));

		// Kilojoules <-> Calories
		$this->addConversion('kilojoule', 'calorie', 0.239005736);
		$this->addMaps('kilojoule', array('kj'));
		$this->addMaps('calorie', array('cal', 'cals'));

		// Grams <-> Ounces
		$this->addConversion('gram', 'ounce', 0.0352739619);
		$this->addMaps('gram', array('g'));
		$this->addMaps('ounce', array('oz'));

		// Millilitres <-> Fluid Ounces
		$this->addConversion('millilitre', 'fluid ounce', 0.0338140227);
		$this->addMaps('millilitre', array('mL'));
		$this->addMaps('fluid ounce', array('fl oz', 'fl. oz.', 'oz. fl.'));

		// Celsius <-> Fahrenheit
		$this->addConversionFunc(
			'degree Celsius',
			'degree Fahrenheit',
			create_function('$c', 'return $c * (9/5) + 32;'),
			create_function('$f', 'return ($f - 32) * (5/9);')
		);
		$this->addMaps('degree Celsius', array('C', '°C', '℃'));
		$this->addMaps('degree Fahrenheit', array('F', '°F', '℉'));
	}

	// PUBLIC INTERFACE

	/**
	 * Provides singleton access to this object.
	 *
	 * @return object A singleton instance of the UnitConverter object.
	 */
	public static function getInstance()
	{
		static $converter;
		if (!is_object($converter)) {
			$converter = new UnitConverter();
		}
		return $converter;
	}

	/**
	 * Insert's the plugin's stylesheet into the WordPress header.
	 *
	 * @return void Does not return anything.
	 */
	public static function style()
	{
		wp_enqueue_style('UnitConverter', WP_PLUGIN_URL . '/unit-converter/unit-converter.css');
	}

	/**
	 * Adds converted units of measurement to the supplied text.
	 * This should be used with the WordPress add_action function, e.g.
	 * add_action('the_content', 'UnitConverter::filter');
	 *
	 * @param string $content The supplied text to add converted measurements to.
	 *
	 * @uses replace
	 *
	 * @return string The supplied text with converted measurements added in.
	 */
	public static function filter($content)
	{
		$converter = self::getInstance();
		return $converter->replace($content);
	}

	/**
	 * Sets options.
	 *
	 * @param array $opts An associative array of options.
	 *
	 * @return void Does not return anything.
	 */
	public static function setOpts($opts)
	{
		$converter = self::getInstance();
		foreach ($opts as $option=>$value) {
			$converter->$option = $value;
		}
	}


	// PRIVATE

	/**
	 * Annotates the detected original measurements with converted measurements.
	 *
	 * @param String $content The original content.
	 *
	 * @return String The annotated content.
	 */
	private function replace($content)
	{
		$tokens = $this->tokenise($content);
		foreach ($tokens as $t) {
			$converted = $this->format($t['converted']['amount'], $t['converted']['unit']);
			$replacement = $this->generateReplacement($t['original'], $converted);
			$original = preg_quote($t['original']);
			$content = preg_replace("/(?<![\d\.]){$original}/", $replacement, $content);
		}
		return $content;
	}

	/**
	 * Tokenises the original measurements and provides converted measurements.
	 *
	 * @param string $content The original text.
	 *
	 * @return array A list of tokens with the appropriate converted measurement.
	 */
	private function tokenise($content)
	{
		$tokens = array();
		foreach ($this->maps as $alias=>$type) {
			$escalias = preg_quote($alias);
			if (!preg_match_all("/(\d*\.?\d+)\s*($escalias)(?!\w)/", $content, $matches, PREG_SET_ORDER)) {
				continue;
			}
			$matches = self::unique($matches);
			foreach ($matches as $match) {
				$tokens[] = array(
					'original'  => $match[0],
					'converted' => $this->convert($match[1], $match[2]),
					);
			}
		}
		return $tokens;
	}

	/**
	 * Detects the current unit being used and switches the amount to the respective metric/imperial unit.
	 * e.g. 10 metres into 32.81 feet.
	 *
	 * @param float $amount The scalar value of the measurement.
	 * @param string $unit The unit name or abbreviation, e.g. 'kilometre' or 'km'.
	 *
	 * @return Array The first element is the converted amount, the second is the canonical unit it was converted to.
	 */
	private function convert($amount, $unit)
	{
		$convert = $this->getComplementaryUnit($this->getCanonicalUnit($unit));
		if (isset($convert['multiplier'])) {
			$new_amount = $amount * $convert['multiplier'];
		} elseif ($convert['func']) {
			$new_amount = call_user_func($convert['func'], $amount);
		} else {
			die("Unit '{$unit}' has no multiplier or function for conversion.");
		}
		$new_amount = round($new_amount, 2); // TODO: configure the DP.
		return array('amount' => $new_amount, 'unit' => $convert['to']);
	}

	/**
	 * Returns the canonical unit when given an abbreviation or alias.
	 *
	 * @param string $alias An abbreviation or alias for a unit (e.g. 'km').
	 *
	 * @return string The canonical unit name.
	 */
	private function getCanonicalUnit($alias)
	{
		if (isset($this->maps[$alias])) {
			return $this->maps[$alias];
		}
		die("Can't tell what unit '{$alias}' is.\n");
	}

	/**
	 * Obtains the complementary unit and its multiplier.
	 *
	 * @param string $unit A canonical unit name, e.g. 'kilometre'.
	 *
	 * @return array The canonical unit name of the complementary unit (e.g. 'mile') and its multiplier.
	 */
	private function getComplementaryUnit($unit)
	{
		if (isset($this->units[$unit])) {
			return $this->units[$unit];
		}
		die("Unit '{$unit}' is not a valid unit.\n");
	}

	/**
	 * Adds alias mappings for the canonical name of a unit and plural version of the canonical name.
	 *
	 * @param string $metric Canonical name of the metric measurement (e.g. 'kilometre').
	 * @param string $imperial Canonical name of the imperial measurement (e.g. 'mile').
	 *
	 * @return void Does not return anything.
	 */
	private function addSelfMappings($metric, $imperial)
	{
		// Add self and plural version to the map
		$this->addMaps($metric, array($metric, self::plural($metric)));
		$this->addMaps($imperial, array($imperial, self::plural($imperial)));
	}

	/**
	 * Adds a metric <-> imperial conversion multiplier mapping.
	 *
	 * @param string $metric Canonical name of the metric measurement (e.g. 'kilometre').
	 * @param string $imperial Canonical name of the imperial measurement (e.g. 'mile').
	 * @param float $multiplier The multiplier to apply to the metric unit to obtain the imperial unit.
	 *
	 * @return void Does not return anything.
	 */
	private function addConversion($metric, $imperial, $multiplier)
	{
		$this->units[$metric] = array(
			'to' => $imperial,
			'multiplier' => $multiplier,
			);
		$this->units[$imperial] = array(
			'to' => $metric,
			'multiplier' => 1/$multiplier,
			);
		$this->addSelfMappings($metric, $imperial);
	}

	/**
	 * Adds a metric <-> imperial conversion with functions instead of a multiplier.
	 *
	 * @param string $metric Canonical name of the metric measurement (e.g. 'kilometre').
	 * @param string $imperial Canonical name of the imperial measurement (e.g. 'mile').
	 * @param string $metric The multiplier to apply to the metric unit to obtain the imperial unit.
	 *
	 * @return void Does not return anything.
	 */
	private function addConversionFunc($metric, $imperial, $met_to_imp, $imp_to_met)
	{
		$this->units[$metric] = array(
			'to' => $imperial,
			'func' => $met_to_imp,
			);
		$this->units[$imperial] = array(
			'to' => $metric,
			'func' => $imp_to_met,
			);
		$this->addSelfMappings($metric, $imperial);
	}

	/**
	 * Adds an alias mapping for either a metric or imperial unit.
	 *
	 * @param string $unit The canonical name of the unit of measurement (e.g. 'kilometre').
	 *
	 * @return void Does not return anything.
	 */
	private function addMaps($unit, $aliases)
	{
		foreach ($aliases as $alias) {
			$this->maps[$alias] = $unit;
			// In order to detect the mapping in HTML, it may be necessary to check the HTML entity version of the unit.
			$alias_html = htmlentities($alias, UTF-8);
			if ($alias_html != $alias) {
				$this->maps[$alias_html] = $unit;
			}
		}
	}

	/**
	 * Pluralises canonical unit names.
	 *
	 * @param string The singular form of the canonical unit name (e.g. 'mile').
	 *
	 * @return string The plural form of the canonical unit name (e.g. 'miles').
	 */
	private static function plural($singular)
	{
		switch ($singular) {
			case 'degree Celsius':
			case 'degree Fahrenheit':
				return str_replace('degree', 'degrees', $singular);
			case 'foot':
				return 'feet';
			case 'inch':
				return 'inches';
			default:
				return $singular . 's';
		}
	}

	/**
	 * Formats the amount with the units, e.g. '10 miles' or '1 mile'.
	 *
	 * @param float $amount The scalar value of the measurement.
	 * @param string The canonical unit name (e.g. 'mile').
	 *
	 * @return string The formatted measurement.
	 */
	private function format($amount, $unit)
	{
		$f_unit = $unit;
		if ($amount != 1) {
			$f_unit = self::plural($f_unit);
		}
		return $amount . ' ' . $f_unit;
	}

	/**
	 * Like array_unique but works for multi-dimensional arrays.
	 *
	 * @param array $array A multi-dimensional array.
	 *
	 * @return array $array with duplicate sub-arrays removed.
	 */
	private static function unique($array)
	{
		return array_intersect_key($array, array_unique(array_map('serialize', $array)));
	}

	/**
	 * Generates the text to insert in place of the original text.
	 *
	 * @param string $original The original measurement.
	 * @param string $converted The converted measurement.
	 *
	 * @return string The resulting text containing the original measurement with the converted measurement.
	 */
	private function generateReplacement($original, $converted)
	{
		$converted = htmlspecialchars($converted);
		switch ($this->display_mode) {
			case 'mouseover':
				return "<span class=\"unit-converter-help\" title=\"{$converted}\">{$original}</span>";
				break;
			default:
				return "{$original} ({$converted})";
				break;
		}
	}
}

// Hook in the plugin to the following WordPress actions.
add_action('the_content', 'UnitConverter::filter');
add_action('the_content_rss', 'UnitConverter::filter');
add_action('wp_print_styles', 'UnitConverter::style');
