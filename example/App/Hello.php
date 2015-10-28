<?php namespace Foo\Bar;

class Hello {
	public function greet($name) {
		global $app;
		$greeting = "Hello, $name!";
		if ($app->option('shout')) $greeting = strtoupper($greeting);
		echo $app->say($greeting, 'blue');
	}
}