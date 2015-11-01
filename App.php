<?php namespace RichJenks\Consol;

class App {

	// Input/output features
	use IO;

	/**
	 * @var array All mapped commands
	 */
	private $map = [];

	/**
	 * @var string Current command
	 */
	private $command;

	/**
	 * @var array Strings provided after command
	 */
	private $params;

	/**
	 * @var array Options prefixed with `--`
	 */
	private $options;

	/**
	 * @var callable Handler for root (no command)
	 */
	private $root;

	/**
	 * @var callable Handler for unknown command
	 */
	private $unknown;

	/**
	 * Extract params and options from request
	 */
	public function __construct() {

		// Get command
		global $argv;
		$this->command = (!empty($argv[1])) ? $argv[1] : '';

		// Set default handlers
		$this->root    = function() { global $app; return $app->root(); };
		$this->unknown = function() { global $app; return $app->unknown(); };

		// Get request minus file
		$request = $argv;
		array_shift($request);

		// Split request into params and options
		$this->parse_request($request);

	}

	/**
	 * Parses request for params and options
	 *
	 * @param array $request Command line arguments
	 */
	private function parse_request($request) {
		foreach ($request as $param) {

			// If option, grab value or set to true
			if (substr($param, 0, 2) === '--') {
				$param = substr($param, 2);
				if (strpos($param, '=')) {
					$param = explode('=', $param);
					$this->options[$param[0]] = $param[1];
				} else {
					$this->options[$param] = true;
				}
			}

			// If flag, add to options
			elseif (substr($param, 0, 1) === '-') {
				$param  = substr($param, 1);
				$params = str_split($param);
				foreach ($params as $param)
					$this->options[$param] = true;
			}

			// Not option, just store param
			else {
				$this->params[] = $param;
			}

		}

	}
	/**
	 * Maps a command
	 *
	 * @param string   $command     Console command to be mapped
	 * @param callable $callback    Code triggered by command
	 * @param string   $description Description of command
	 */
	public function map($command, $callback, $description = '') {

		// Custom handler for root
		if ($command === ':') $this->root = $callback;

		// Custom handler for unkown command
		elseif ($command === '?') $this->unknown = $callback;

		// Map known command
		else {
			$command = strtolower($command);
			$command = preg_replace('/[^A-Za-z0-9:]/', '', $command);
			$this->map[] = [
				'command'     => $command,
				'callback'    => $callback,
				'description' => $description,
			];
		}

	}

	/**
	 * Executes the command
	 */
	public function run() {

		// No command so call root handler
		if (empty($this->command)) echo call_user_func($this->root, $this);

		// Look for known command
		elseif ($callback = $this->callback($this->command, $this->map))
			echo call_user_func($callback, $this);

		// Unknown command so call unknown handler
		else echo call_user_func($this->unknown, $this);

		// Next prompt on new line
		echo PHP_EOL;

	}

	/**
	 * Gets the callback for a given command
	 *
	 * @param string $current  Current command
	 * @param array  $commands All mapped commands
	 *
	 * @return callable Callback for the given command
	 */
	private function callback($current, $commands) {
		foreach ($commands as $command) {
			if ($command['command'] === $current) {
				return $command['callback'];
			}
		}
		return false;
	}

	/**
	 * Default handler for root
	 */
	private function root() { return $this->directory(); }

	/**
	 * Default handler for unknown commands
	 */
	private function unknown() { return $this->say('Unkown command', 'red') . PHP_EOL; }

	/**
	 * Show directory of commands with descriptions
	 */
	public function directory() {
		if (empty($this->map)) {
			return false;
		} else {
			$response = $this->say('Commands:', 'orange') . PHP_EOL;
			$commands = [];
			foreach ($this->map as $command) {
				$commands[] = [
					'  ' . $this->say($command['command'], 'green'),
					$command['description'],
				];
			}
			$response .= $this->table($commands);
			return $response;
		}
	}

	/**
	 * Get param(s)
	 * Note that the command will be at position zero
	 *
	 * @param int $position Which param to get, omit for all
	 */
	public function param($position = false) {
		if ($position === false) return $this->params;
		if (isset($this->params[$position]))
			return $this->params[$position];
		return null;
	}

	/**
	 * Get option (or null) checked in the order provided
	 *
	 * @param  string $options Names of option to search for
	 * @return string Option   value or null
	 */
	public function option($options = []) {
		$options = (array) $options;
		foreach ($options as $option) {
			if (isset($this->options[$option]))
				return $this->options[$option];
		}
		return null;
	}

}