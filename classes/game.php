<?php
class Game{
	public int $id;
	public int $seasonId;
	public string $date;
	private $db;

	public function __construct(int $id = null){
		$this->db = new DB();
		$this->getGame($id);
	}

	private function getGame(int $id = null){
		if(!empty($id)){
			$where = 'WHERE game.id = :gameId ';
		}
		$sql = 'SELECT game.id, game.seasonId, game.date FROM game ' . $where . 'ORDER BY game.date DESC LIMIT 1';
		$this->db->query($sql);
		if(!empty($id)){
			$this->db->bind('gameId', $id);
		}
		$this->db->execute();
		$return = false;
		if($this->db->rowCount() > 0){
			$return = $this->db->fetch();
			foreach($return as $key => $val){
				if(!empty($val)){
					$this->$key = $val;
				}
			}
		}
		return $return;
	}

	public function printDate(){
		$return = false;
		if(!empty($this->date)){
			$return = date('jS F, Y', strtotime($this->date));
		}
		return $return;
	}

	public function getGameNumber(){
		$return = false;
		if(!empty($this->seasonId)){
			$sql = 'SELECT COUNT(game.id) AS gamecount FROM game WHERE game.seasonId = :seasonId GROUP BY game.seasonId LIMIT 1';
			$this->db->query($sql);
			$this->db->bind('seasonId', $this->seasonId);
			$this->db->execute();
			if($this->db->rowCount() > 0){
				$return = $this->db->fetch();
				$return = $return->gamecount;
			}
		}
		return $return;
	}

	public function getGameKills(){
		$return = array();
		$sql = 'SELECT kills.gameId, kills.killerId, (SELECT player.name FROM player WHERE player.id = kills.killerId) AS killerName, kills.killedId, (SELECT player.name FROM player WHERE player.id = kills.killedId) AS killedName FROM kills WHERE gameId = :gameId';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	public function addKill(int $killerId, int $killedId){
		if($this->isKillValid($killedId)){
			$sql = 'INSERT INTO kills (gameId, killerId, killedId) VALUES (:gameId, :killerId, :killedId)';
			$this->db->query($sql);
			$this->db->bind('gameId', $this->id);
			$this->db->bind('killerId', $killerId);
			$this->db->bind('killedId', $killedId);
			$this->db->execute();
		}
	}

	public function removeKill(int $killerId, int $killedId){
		$sql = 'DELETE FROM kills WHERE kills.gameId = :gameId AND kills.killerID = :killerId AND kills.killedId = :killedId';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->bind('killerId', $killerId);
		$this->db->bind('killedId', $killedId);
		$this->db->execute();
	}

	private function isKillValid(int $killedId){
		$sql = 'SELECT
			kills.gameId, kills.killerId, kills.killedId
		FROM
			kills
		WHERE
			kills.gameId = :gameId AND
			kills.killedId = :killedId
		';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->bind('killedId', $killedId);
		$this->db->execute();
		return $this->db->rowCount() === 0;
	}
}
?>