<?php namespace RichJenks\Consol;

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
		echo $this->color($text, $color);
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