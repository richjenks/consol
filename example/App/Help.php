<?php namespace Foo\Bar;

class Help {
	public function get() {
		global $app;

		// No command provided
		if (empty($app->args)) {
			echo $app->say('Please specify a command', 'orange');
			return;
		}

		// Invalid command provided
		if (!in_array($app->args[0], ['hello', 'null'])) {
			$command = $app->args[0];
			echo $app->say("Unknown command: '$command'", 'red');
			return;
		}

		if ($app->arg('hello', 0)) {
			echo $app->say('Says hello to the world');
			echo PHP_EOL;
			echo $app->say('Add option --shout to say hello in uppercase!');
		}

		if ($app->arg('null', 0)) { echo $app->say('Nope'); }

	}
}