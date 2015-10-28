<?php namespace Consol;

class App {

	/**
	 * @var array App config
	 */
	private $config;

	/**
	 * @var array Data for routes
	 */
	private $routes;

	/**
	 * @var array Additional arguments
	 */
	public $args;

	/**
	 * Registers the app
	 */
	public function __construct($config) {

		// Store args and config
		global $argv;
		$args = $argv;
		array_shift($args);
		array_shift($args);
		$this->args = $args;
		$this->config = $config;

		// Include files
		$path = dirname($this->config['main']) . DIRECTORY_SEPARATOR . $this->config['code'];
		$files = scandir($path);
		foreach ($files as $file) {
			if (!in_array($file, ['.', '..'])) {
				require $path . DIRECTORY_SEPARATOR . $file;
			}
		}

	}

	/**
	 * Maps a command to a route
	 */
	public function map($command, $callback, $description = '') {
		$this->routes[] = [
			'command'     => $command,
			'callback'    => $callback,
			'description' => $description,
		];
	}

	/**
	 * Starts the app
	 */
	public function run() {
		global $argv;

		// If command is set, check for mapped route
		if (!empty($argv[1])) {
			$found = false;
			foreach ($this->routes as $key => $route) {

				// If command is mapped, run it and stop looking
				if ($route['command'] === $argv[1]) {
					call_user_func($route['callback']);
					$found = true;
					break;
				}

			}

			// Command not found, show error
			if (!$found) {
				echo $this->say("Unkown command: '$argv[1]'", 'red');
			}

		// No command given, show command directory
		} else { $this->directory(); }

		// End with a newline
		echo PHP_EOL;

	}

	/**
	 * Prepares text to be output to the console
	 *
	 * @param string $text  Text to output
	 * @param string $color Color of text
	 *
	 * @return string Colored text
	 */
	public function say($text, $color = 'none') {
		$colors = [
			'none'    => '0',
			'aqua'    => '1;36',
			'black'   => '0;30',
			'blue'    => '1;34',
			'cyan'    => '0;36',
			'emerald' => '1;32',
			'gray'    => '1;30',
			'lilac'   => '1;35',
			'green'   => '0;32',
			'maroon'  => '0;31',
			'navy'    => '0;34',
			'orange'  => '0;33',
			'purple'  => '0;35',
			'red'     => '1;31',
			'silver'  => '0;37',
			'white'   => '1;37',
			'yellow'  => '1;33',
		];
		return sprintf("\033[%sm%s\033[0m", $colors[$color], $text);
	}

	/**
	 * Shows command directory
	 */
	private function directory() {

		// Header
		echo $this->say($this->config['name'], 'green');
		echo $this->say(' version ');
		echo $this->say($this->config['version'], 'green');

		// Get list of commands
		$commands = [];
		foreach ($this->routes as $route) {
			$commands[$route['command']] = $route['description'];
		}
		ksort($commands);

		// Show commands directory
		echo PHP_EOL;
		echo PHP_EOL;
		echo $this->say('Commands:', 'orange');
		echo PHP_EOL;
		foreach ($commands as $command => $description) {
			echo $this->say('  ' . $command . "\t\t", 'green');
			echo $this->say($description);
			echo PHP_EOL;
		}

	}

	/**
	 * Checks if a given argument is provided
	 * optionally specify a position
	 *
	 * @param string $argument Argument to check
	 * @param int    $position Position of argument
	 *
	 * @return bool Whether argument was provided
	 */
	public function arg($argument, $position = false) {
		// if ($position) {
		// 	return (!empty($this->args[$position])
		// 		&& $this->args[$position] === $argument);
		// }
		return in_array($argument, $this->args);
	}

	/**
	 * Checks if a given option is provided
	 *
	 * @param string $option Option to check
	 * @return bool Whether option was provided
	 */
	public function option($option) {
		return in_array('--' . $option, $this->args);
	}

}