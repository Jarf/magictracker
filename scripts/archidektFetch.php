<?php
print 'Init...';
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$db = new db();
print 'Done' . PHP_EOL . 'Get players...';
$db->query('SELECT id, archidektName FROM player WHERE archidektName IS NOT NULL');
$db->execute();
$players = $db->fetchAll();
$insert = $bind = array();
$i = 0;
$deckids = array();
print 'Done' . PHP_EOL . 'Fetch decks';
foreach($players as $player){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, 'https://archidekt.com/api/decks/v3/?ownerUsername=' . $player->archidektName . '&deckFormat=3');
	$response = curl_exec($ch);
	$response = @json_decode($response);
	curl_close($ch);
	if(!empty($response) && isset($response->results)){
		foreach($response->results as &$row){
			if(isset($row->owner) && isset($row->owner->username) && $row->owner->username === $player->archidektName){
				if(isset($row->featured) && !empty($row->featured)){
					$imgpath = DIR_IMG . 'decks/' . $row->id . '.jpg';
					if(file_exists($imgpath)){
						unlink($imgpath);
					}
					$fp = fopen(DIR_IMG . 'decks/' . $row->id . '.jpg', 'w+');
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $row->featured);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				    curl_setopt($ch, CURLOPT_FILE, $fp);
				    curl_exec($ch);
				    curl_close($ch);
				    fclose($fp);
				}
				$colors = '';
				if(isset($row->colors) && !empty($row->colors)){
					foreach($row->colors as $color => $count){
						if($count !== 0){
							$colors .= $color;
						}
					}
				}
				if(empty($colors)){
					$colors = null;
				}
				$deckids[] = $row->id;
				$insert[] = '(' . $player->id . ', :deckId' . $i . ', :name' . $i . ', :colors' . $i . ')';
				$bind['deckId' . $i] = $row->id;
				$bind['name' . $i] = $row->name;
				$bind['colors' . $i] = $colors;
				$i++;
			}
			print '.';
		}
	}
	print '.';
}
print 'Done' . PHP_EOL . 'Store data...';
if(!empty($insert)){
	$sql = 'INSERT INTO decks (playerId, deckId, name, colors) VALUES ' . implode(',', $insert) . ' ON DUPLICATE KEY UPDATE name=VALUES(name),colors=VALUES(colors)';
	$db->query($sql);
	if(!empty($bind)){
		foreach($bind as $bkey => $bval){
			$db->bind($bkey, $bval);
		}
	}
	$db->execute();
}
if(!empty($deckids)){
	$sql = 'DELETE FROM decks WHERE deckId NOT IN (' . implode(',', $deckids) . ')';
	$db->query($sql);
	$db->execute();
}
print 'Done' . PHP_EOL . 'Removing images not associated with a deck...';
$sql = 'SELECT deckId FROM decks';
$db->query($sql);
$db->execute();
$rs = $db->fetchAll();
$deckIds = array();
foreach($rs as $row){
	$deckIds[] = $row->deckId;
}
if(!empty($deckIds)){
	$imageList = getDirectoryFiles(DIR_IMG . 'decks/');
	foreach($imageList as $img){
		$imgId = basename($img, '.jpg');
		if(is_numeric($imgId) && !in_array($imgId, $deckIds)){
			unlink($img);
			print '.';
		}
	}
}
print 'Done' . PHP_EOL;
?>