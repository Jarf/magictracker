<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$deck = false;
if(isset($_GET) && isset($_GET['id']) && is_numeric($_GET['id'])){
	$deck = new Player();
	$deck = $deck->randomDeck($_GET['id']);
}
if($deck === false){
	display404();
}else{
	$mana = $deck->colors;
	$manahtml = '';
	if(empty($mana)){
		$mana = 'C';
	}
	$mana = str_split($mana);
	foreach($mana as $mval){
		$manahtml .= '<img src="/img/mana/' . $mval . '.svg" height=15 width=15/>';
	}
	$html = '<img src="/img/decks/' . $deck->deckId . '.jpg"/><br/><p>' . $deck->name . ' ' . $manahtml . '</p><hr/>';
	print $html;
}
?>