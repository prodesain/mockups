<?php
// prevent browser
if(PHP_SAPI !== 'cli'){ die; }

require 'vendor/autoload.php';
require 'helpers.php';

echo "=> generating xml for wp\n";

file_put_contents('export/wp.wxr', view('export.wp', [
	'keywords' => keywords(),
	'argv' => $argv
], false));