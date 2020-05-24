<?php
require 'vendor/autoload.php';
require 'helpers.php';

Flight::route('/', function(){
	if(isset($_GET['nerd'])){
		echo home_url() . random_post();
		die;
	}

    view('home');
});

Flight::route('/pages/@page', function($page){
    view('pages.page', ['page' => $page]);
});

Flight::route('/@slug.jpg', function($slug){
	$data = get_data($slug);

	return Flight::redirect(collect($data['images'])->random()['url']);
});

Flight::route('/@slug.html', function($slug){
	$data = get_data($slug);

	if($data === false){
		return Flight::redirect(random_post());
	}
	
	$data['keyword'] = str_replace('-', ' ', $slug);

    view('image', $data);
});

Flight::start();