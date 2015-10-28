<?php require '../App.php';

$app = new Consol\App([
	'name'    => 'MyApp',
	'version' => '1.0.0',
	'main'    => __FILE__,
	'code'    => 'App',
]);

$app->map('hello', function () {
	$hello = new Foo\Bar\Hello();
	$hello->greet('World');
}, 'Say "Hello"!');

$app->map('help', function () {
	$help = new Foo\Bar\Help();
	$help->get();
}, 'Show help for a command, e.g. "help hello"');
$app->run();