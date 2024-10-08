<?php
class Player{
	private $db;

	public function __construct(){
		$this->db = new db();
	}

	public function getPlayers(){
		$return = array();
		$sql = 'SELECT player.id, player.name FROM player';
		$this->db->query($sql);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	public function getPlayerIdNameMap(){
		$players = $this->getPlayers();
		$return = array();
		foreach($players as $player){
			$return[$player->id] = $player->name;
		}
		return $return;
	}

	public function getPlayersDecks(){
		$return = array();
		$sql = 'SELECT decks.playerId, decks.deckId, decks.name, decks.colors FROM decks';
		$this->db->query($sql);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetchAll();
			foreach($result as $row){
				if(!isset($return[$row->playerId])){
					$return[$row->playerId] = array();
				}
				$return[$row->playerId][$row->deckId] = $row;
			}
		}
		return $return;
	}

	public function getPlayersWinBins(){
		$return = array();
		$sql = 'SELECT player.id, player.winbin, decks.name, decks.colors FROM player LEFT JOIN decks ON player.winbin = decks.deckId';
		$this->db->query($sql);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetchAll();
			foreach($result as $row){
				$return[$row->id] = $row->winbin;
			}
		}
		return $return;
	}

	public function updatePlayersWinBins(array $data){
		foreach($data as $pid => $did){
			if(!is_numeric($did)){
				$did = null;
			}
			$sql = 'UPDATE player SET winbin = :deckId WHERE id = :playerId';
			$this->db->query($sql);
			$this->db->bind('deckId', $did);
			$this->db->bind('playerId', $pid);
			$this->db->execute();
		}
	}
}
?>