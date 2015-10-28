<?php class Consol {

	public function __construct() {
		$files = scandir(__DIR__);
		foreach ($files as $file) {
			if (!in_array($file, ['.', '..', 'index.php'])) {
				require $file;
			}
		}
	}

	/**
	 * @var array Data for routes
	 */
	private $routes;

	/**
	 * Maps a command to a route
	 */
	public function send($command, $callback, $data = []) {
		global $argv;
		if (!empty($argv[1]) && $argv[1] === $command) {
			call_user_func($callback, $command, $data);
		}
	}

	/**
	 * Sets a handler for when no route matches
	 */
	public function default($callback)

	/**
	 * Starts the app
	 */
	public function go() {}

}