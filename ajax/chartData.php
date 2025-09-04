<?php
header('Content-type: application/json; charset=utf-8');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$return = array(
	'labels' => array(),
	'datasets' => array()
);
$seasonId = null;
if(isset($_GET['season']) && !empty($_GET['season']) && is_numeric($_GET['season'])){
	$seasonId = intval($_GET['season']);
}
$stats = new Stats($seasonId);
$players = new Player();
$playersums = $playerdatasets = array();
$players = $players->getPlayerIdNameMap();
foreach($players as $pid => $player){
	$playersums[$pid] = 0;
	$playerdatasets[$pid] = array(
		'label' => $player,
		'data' => array()
	);
}
$data = $stats->getPointsChartData();
$lastgameid = null;
foreach($data as $row){
	if($lastgameid !== $row->id){
		$lastgameid = $row->id;
		foreach($players as $pid => $player){
			$playerdatasets[$pid]['data'][$row->id] = $playersums[$pid];
		}
	}
	if(!isset($return['labels'][$row->id])){
		$return['labels'][$row->id] = $row->date;
	}
	$playersums[$row->playerId] += $row->points;
	$playerdatasets[$row->playerId]['data'][$row->id] = $playersums[$row->playerId];
}
$return['datasets'] = array_values($playerdatasets);
foreach($return['datasets'] as $did => $data){
	$return['datasets'][$did]['data'] = array_values($data['data']);
}
$return['labels'] = array_values($return['labels']);
print json_encode($return);
?>