<?php class Init {
	public function __construct() {
		if (file_exists(getcwd().'/squishfile')) {
			echo Helper::say('Error: squishfile already exists!', 'red');
			echo PHP_EOL;
			die;
		}
		touch(getcwd().'/squishfile');
		file_put_contents('squishfile', '# Generated: ' . date('Y-m-d H:i:s'));
	}
}