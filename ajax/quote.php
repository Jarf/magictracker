<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
if(isset($_GET['quote']) && isset($_GET['author']) && isset($_GET['date'])){
	$author = $date = null;
	$quotetext = $_GET['quote'];
	$quotetext = urldecode($quotetext);
	$quotetext = nl2br($quotetext);
	$quotetext = trim($quotetext);
	if(!empty($quotetext)){
		if(!empty($_GET['author']) && is_numeric($_GET['quote'])){
			$author = intval($_GET['quote']);
		}
		if(!empty($_GET['date']) && preg_match('/(\d{4})\-(\d{2})\-(\d{2})/', $_GET['date']) === 1){
			$date = $_GET['date'];
		}
		$quote = new Quote();
		$quote->addQuote($quotetext, $author, $date);
	}
}
?>