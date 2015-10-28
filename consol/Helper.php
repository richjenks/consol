<?php class Helper {

	public static function intro() {
		echo self::say('Squish', 'green') . ' v1.0.0 (c) Rich Jenks 2015';
		echo PHP_EOL;
		echo PHP_EOL;
		echo self::say('Commands:', 'orange');
		echo PHP_EOL;
		$commands = [
			'init' => 'Generates a squishfile from source code',
			'squish' => 'Processes squishfile',
			'watch' => 'Processes squishfile when files are changed',
		];
		foreach ($commands as $command => $description) {
			echo self::say($command, 'green') . "\t" . $description;
			echo PHP_EOL;
		}
	}

	public static function say($text, $color = 'white') {
		$colors = [
			'black' => '0;30',
			'red' => '0;31',
			'green' => '0;32',
			'orange' => ';33',
			'blue' => '0;34',
			'purple' => '0;35',
			'cyan' => '0;36',
			'light-gray' => '0;37',
			'dark-gray' => '1;30',
			'light-red' => '1;31',
			'light-green' => '1;32',
			'yellow' => '1;33',
			'light-blue' => '1;34',
			'light-purple' => '1;35',
			'light-cyan' => '1;36',
			'white' => '1;37',
		];
		return sprintf("\033[%sm%s\033[0m", $colors[$color], $text);
		// return "\033[" . $colors[$color] . 'm' . $text . "\033[0m";
	}

}