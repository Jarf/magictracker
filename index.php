<?php
require_once(dirname(__FILE__) . '/include/config.php');
require_once(dirname(__FILE__) . '/include/autoload.php');

// Page Vars
$urlpath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$pagevars = array(
	'page_title' => '',
	'page_args' => array()
);
$urlpath = explode('/', $urlpath);
$page = current($urlpath);
$gameid = null;
switch ($page) {
	case '':
	default:
		if(is_numeric($page)){
			$gameid = intval($page);
		}
		$page = 'home';
		break;
}
$pagevars['gameid'] = $gameid;
if(isset($urlpath[0])){
	unset($urlpath[0]);
}
$pagevars['current_page'] = $page;
$pagevars['page_title'] = ucwords(str_replace('-', ' ', $page));
if(!empty($urlpath)){
	$pagevars['page_args'] = $urlpath;
}

// Page Controller
$codepage = DIR_PAGES . $page . '.php';
$tplfile = $page . '.twig';
$tplpage = DIR_TPL . $tplfile;
if(file_exists($codepage) && file_exists($tplpage)){
	require_once($codepage);
}elseif(!file_exists($tplpage)){
	display404();
}

// Page Output
$loader = new \Twig\Loader\FilesystemLoader(array(DIR_TPL, DIR_TPL . 'include/'));
$twig = new \Twig\Environment($loader, array(
	'cache' => false,
	'debug' => ISDEV
));
$output = array();
$output[] = $twig->render('header.twig', $pagevars);
if(file_exists($tplpage)){
	$output[] = $twig->render($tplfile, $pagevars);
}
$output[] = $twig->render('footer.twig', $pagevars);
$output = implode('', $output);
print $output;
?>