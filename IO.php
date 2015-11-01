<?php namespace Consol;

/**
 * User-facing input/output functions
 */
trait IO {

	/**
	 * Color text
	 *
	 * @param string $text  Text to output
	 * @param string $color Color of text
	 *
	 * @return string Colored text
	 */
	public function color($text, $color = 'none') {
		$colors = [ 'none' => '0', 'aqua' => '1;36', 'black' => '0;30', 'blue' => '1;34', 'cyan' => '0;36', 'emerald' => '1;32', 'gray' => '1;30', 'lilac' => '1;35', 'green' => '0;32', 'maroon' => '0;31', 'navy' => '0;34', 'orange' => '0;33', 'purple' => '0;35', 'red' => '1;31', 'silver' => '0;37', 'white' => '1;37', 'yellow' => '1;33' ];
		$text = sprintf("\033[%sm%s\033[0m", $colors[$color], $text);
		return $text;
	}

	/**
	 * Outputs text
	 *
	 * @param string $text  Text to output
	 * @param string $color Color of text
	 */
	public function say($text, $color = 'none') {
		fwrite(STDOUT, $this->color($text, $color));
	}

	/**
	 * Gets input from user
	 *
	 * @param string $question Text to display
	 * @param string $pattern Regex pattern against which input is tested
	 * @param string $default If input is empty use this value. `null` for no default
	 */
	function ask($question, $default = null, $pattern = '.+') {
		do {
			fwrite(STDOUT, $question);
			$answer = trim(fgets(STDIN));
			if (!is_null($default) && empty($answer)) $answer = $default;
		} while (!preg_match('/' . $pattern . '/', $answer));
		return $answer;
	}

	/**
	 * @param array $table Multi-array to be output as a table
	 */
	public function table($data) {

		// Find longest string in each column
		$columns = [];
		foreach ($data as $row_key => $row) {
			foreach ($row as $cell_key => $cell) {
				$length = strlen($cell);
				if (empty($columns[$cell_key]) || $columns[$cell_key] < $length) {
					$columns[$cell_key] = $length;
				}
			}
		}

		// Output table, padding columns
		$table = '';
		foreach ($data as $row_key => $row) {
			foreach ($row as $cell_key => $cell)
				$table .= str_pad($cell, $columns[$cell_key]) . '   ';
			$table .= PHP_EOL;
		}

		return $table;

	}

	/**
	 * Shows a progress bar
	 *
	 * Adapted from this answer to be shorter (50 chars rather than 100):
	 * @see http://stackoverflow.com/a/27147177/1562799
	 *
	 * @param int $done Numerator
	 * @param int $total Denominator
	 */
	public function progress($done, $total) {
		$perc = round(($done / $total) * 100);
		$bar = "[" . ($perc > 0 ? str_repeat("=", ($perc - 1) / 2) : "") . ">";
		$bar .= str_repeat(" ", (100 - $perc) / 2) . "] - $perc% - $done/$total ";
		echo "\033[0G$bar"; // Note the \033[0G. Put the cursor at the beginning of the line
	}

	/**
	 * Suggests an item from a list similar to the input
	 *
	 * @param string $needle   Provided option
	 * @param array  $haystack All options
	 */
	public function suggest($needle, $haystack) {
		$needle_metaphone = metaphone($needle);
		foreach ($haystack as $word) {
			$word_metaphone = metaphone($word);
			$lev = levenshtein($word_metaphone, $needle_metaphone);
			if ($lev < 2) return $word;
		}
		return false;
	}

	/**
	 * For testing purposes
	 *
	 * @return array Commands, current command, params and options
	 */
	public function get_request() {
		return [
			'command'  => $this->command,
			'params'   => $this->params,
			'options'  => $this->options,
			'map'      => $this->map,
		];
	}

}