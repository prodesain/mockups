<?php
// prevent browser
if(PHP_SAPI !== 'cli'){ die; }

require 'vendor/autoload.php';
require 'helpers.php';

echo "=> generating html export\n";

file_put_contents('export/html/index.html', view('home', [],false));

foreach (keywords() as $keyword) {
	$slug = str_slug($keyword);
	$data = get_data($slug);

	$data['keyword'] = str_replace('-', ' ', $slug);

    
	file_put_contents('export/html/' . $slug . '.html', view('image', $data, false));
}
