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

	public function resetPlayersWinBins(){
		$players = $this->getPlayers();
		$winbindata = array();
		foreach($players as $player){
			$winbindata[$player->id] = null;
		}
		$this->updatePlayersWinBins($winbindata);
	}

	public function randomDeck(int $playerid, bool $excludeWinBin = true){
		$return = false;
		$sql = 'SELECT decks.deckId, decks.name, decks.colors FROM decks LEFT JOIN player ON decks.playerId = player.id WHERE player.id = :playerId';
		if($excludeWinBin === true){
			$sql .= ' AND (player.winbin IS NULL OR decks.deckId != player.winbin)';
		}
		$sql .= ' ORDER BY RAND() LIMIT 1';
		$this->db->query($sql);
		$this->db->bind('playerId', $playerid);
		$this->db->execute();
		if($this->db->rowCount() === 1){
			$return = $this->db->fetch();
		}
		return $return;
	}
}
?>