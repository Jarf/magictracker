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
		$where = '';
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
		if(isset($this->date) && !empty($this->date)){
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
		$sql = 'SELECT kills.gameId, kills.killerId, (SELECT player.name FROM player WHERE player.id = kills.killerId) AS killerName, kills.killedId, (SELECT player.name FROM player WHERE player.id = kills.killedId) AS killedName FROM kills WHERE gameId = :gameId ORDER BY kills.timestamp ASC';
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

	public function getGamePoints(){
		$return = array();
		$sql = 'SELECT player.id, player.name, IFNULL(points.points, 0) AS points FROM player LEFT JOIN points ON points.playerId = player.id AND points.gameId = :gameId WHERE points.gameId = :gameId OR points.gameId IS NULL ORDER BY points.points DESC';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	public function updateGamePoints(array $points){
		$insert = array();
		foreach($points as $playerId => $pointsVal){
			if($pointsVal > 0){
				$bind['points' . $playerId] = intval($pointsVal);
				$insert[] = '(:gameId, ' . $playerId . ', :points' . $playerId . ')';
			}
		}
		$sql = 'DELETE FROM points WHERE points.gameId = :gameId';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->execute();
		$sql = 'INSERT INTO points (gameId, playerId, points) VALUES ' . implode(',', $insert);
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
	}

	public function startNewGame(int $seasonId){
		$sql = 'INSERT INTO game (seasonId) VALUES (:seasonId)';
		$this->db->query($sql);
		$this->db->bind('seasonId', $seasonId);
		$this->db->execute();
	}

	public function getGameConcedes(){
		$return = array();
		$sql = 'SELECT player.id, player.name, IF(concede.playerId IS NULL, 0, 1) AS concede FROM player LEFT JOIN concede ON concede.playerId = player.id AND concede.gameId = :gameId WHERE concede.gameId = :gameId OR concede.gameId IS NULL';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	public function updateGameConcedes(array $concedes){
		$sql = 'DELETE FROM concede WHERE concede.gameId = :gameId';
		$this->db->query($sql);
		$this->db->bind('gameId', $this->id);
		$this->db->execute();
		if(!empty($concedes)){
			$insert = $bind = array();
			foreach($concedes as $ckey => $concede){
				$insert[] = '(:gameId, :playerId' . $ckey . ')';
				$bind['playerId' . $ckey] = $concede;
			}
			$sql = 'INSERT INTO concede (gameId, playerId) VALUES ' . implode(',', $insert);
			$this->db->query($sql);
			$this->db->bind('gameId', $this->id);
			foreach($bind as $key => $val){
				$this->db->bind($key, $val);
			}
			$this->db->execute();
		}
	}
}
?>