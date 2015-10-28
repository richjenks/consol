<?php

require 'consol/Consol.php';
$app = new Consol;

$app->send('foo', function () {
	echo 'BAR';
}, 'Does a thing');

$app->go();

/*
Auto-output available commands if route not found
If command provided but not found, also show error