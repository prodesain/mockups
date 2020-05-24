<?php
// prevent browser
if(PHP_SAPI !== 'cli'){ die; }

require 'vendor/autoload.php';
require 'helpers.php';

echo "=> generating xml for blogspot\n";

file_put_contents('export/blogspot.xml', view('export.blogspot', [
	'keywords' => keywords(),
	'argv' => $argv
], false));