<?php
// prevent browser
if(PHP_SAPI !== 'cli'){ die; }

foreach (glob('data/*.php') as $file) {
	unlink($file);
}

foreach (glob('export/html/*.html') as $file) {
	unlink($file);
}

echo "Data cleared\n";