<?php
require_once(dirname(__FILE__) . '/include/config.php');
require_once(dirname(__FILE__) . '/include/autoload.php');
use MatthiasMullie\Minify;
$output = $files = array();
$file = $type = $lmod = $minifier = null;
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (((60 * 60) * 24) * 365)));
if(isset($_SERVER['REQUEST_URI'])){
	$url = strtok($_SERVER['REQUEST_URI'], '?');
	if(preg_match('/^\/.*\/(.*)\.(.*)$/', $url, $match) === 1){
		$file = $match[1];
		$type = $match[2];
	}elseif($url === '/robots.txt'){
		$file = 'robots';
		$type = 'txt';
	}
}
switch ($type) {
	case 'css':
		header("Content-type: text/css; charset=utf-8");
		$minifier = new Minify\CSS;
		$default = DIR_CSS . 'global.css';
		$files[] = $default;

		switch ($file) {
			case 'stats':
				$files[] = DIR_CSS . 'stats.css';
				break;
		}

		break;

	case 'js':
		header("Content-type: application/javascript; charset=utf-8");
		$minifier = new Minify\JS;

		switch ($file) {
			default:
				$default = DIR_JS . $file . '.js';
				if(!file_exists($default)){
					display404();
				}else{
					$files[] = $default;
				}
				break;
		}

		break;

	case 'txt':
		if($file === 'robots'){
			header("Content-type: text/plain; charset=utf-8");
			$output[] = 'User-agent: *' . PHP_EOL . 'Disallow: /';
		}else{
			display404();
		}
		break;
	
	default:
		display404();
		break;
}

if(!empty($files)){
	foreach($files as $file){
		if(file_exists($file)){
			$lastmodified = filemtime($file);
			if($lastmodified > $lmod){
				$lmod = $lastmodified;
			}
			$output[] = file_get_contents($file);
		}
	}
}

if(empty($output)){
	display404();
}else{
	header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lmod));
	$etag = md5($lmod);
	header('Etag: ' . $etag);
	$output = implode(PHP_EOL, $output);
	if($minifier !== null){
		$minifier->add($output);
		$output = $minifier->minify();
	}
	print $output;
}
?>