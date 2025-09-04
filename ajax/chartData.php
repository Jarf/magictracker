<?php
header('Content-type: application/json; charset=utf-8');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$chartType = $seasonId = $lastgameid = null;
$return = array(
	'labels' => array(),
	'datasets' => array()
);
if(isset($_GET) && isset($_GET['chart'])){
	$chartType = $_GET['chart'];
	if(isset($_GET['season']) && !empty($_GET['season']) && is_numeric($_GET['season'])){
		$seasonId = intval($_GET['season']);
	}
}
if(!is_null($chartType)){
	$stats = new Stats($seasonId);
	$players = new Player();
	$players = $players->getPlayerIdNameMap();
	$playersums = $playerdatasets = array();
}

switch ($chartType) {
	case 'points':
		foreach($players as $pid => $player){
			$playersums[$pid] = 0;
			$playerdatasets[$pid] = array(
				'label' => $player,
				'data' => array()
			);
		}
		$data = $stats->getPointsChartData();
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
		break;

	case 'kd':
		foreach($players as $pid => $player){
			$playersums[$pid] = array('k' => 0, 'd' => 0);
			$playerdatasets[$pid] = array(
				'label' => $player,
				'data' => array()
			);
		}
		$data = $stats->getKDChartData();
		foreach($data as $row){
			$playersums[$row->killerId]['k']++;
			$playersums[$row->killedId]['d']++;
			foreach($players as $pid => $player){
				$kd = 0;
				if($playersums[$pid]['k'] !== 0 && $playersums[$pid]['d'] !== 0){
					$kd = round($playersums[$pid]['k'] / $playersums[$pid]['d'], 2);
				}
				$playerdatasets[$pid]['data'][$row->id] = $kd;
			}
			if(!isset($return['labels'][$row->id])){
				$return['labels'][$row->id] = $row->date;
			}
		}
		break;

	case 'wins':
		$data = $stats->getWinsChartData();
		$return['datasets'][0]['data'] = array();
		foreach($players as $pid => $player){
			$return['labels'][$pid] = $player;
		}
		foreach($data as $pid => $wins){
			$playerdatasets[0]['data'][$pid] = $wins;
		}
		break;
	
	default:
		display404();
		break;
}

if(isset($return['datasets'])){
	$return['datasets'] = array_values($playerdatasets);
	foreach($return['datasets'] as $did => $data){
		$return['datasets'][$did]['data'] = array_values($data['data']);
	}
}
$return['labels'] = array_values($return['labels']);
print json_encode($return);

?>