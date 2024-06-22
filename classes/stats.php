<?php
class Stats{
	public int $gameId;
	public int $seasonId;
	public int $playerId;
	private $db;

	public function __construct(int $seasonId = null, int $gameId = null, int $playerId = null){
		$this->db = new DB();
		if(!empty($seasonId)){
			$this->seasonId = $seasonId;
		}
		if(!empty($gameId)){
			$this->gameId = $gameId;
		}
		if(!empty($playerId)){
			$this->playerId = $playerId;
		}
	}

	public function getStats(){
		$return = array(
			'Top Trumps' => $this->getTopTrumps(),
			'Kill Death Ratios' => $this->getKDRatios(),
			'Dynamic Duos' => $this->getDuoStats(),
			'Most Killed By' => $this->getKilledBy(),
			'Game Stats' => $this->getGameStats()
		);
		return $return;
	}

	private function getTopTrumps(){
		$return = array(
			$this->getMostKills(),
			$this->getMostDeaths(),
			$this->getMostConcedes(),
			$this->getMostWins(),
			$this->getMostRunnerUps(),
			$this->getMostPoints()
		);
		return $return;
	}

	private function getDuoStats(){
		return array(
			$this->getDuoTeamup(),
			$this->getDuoFighters()
		);
	}

	private function getMostKills(){
		$return = 'N/A';
		$where = $bind = array();
		$where[] = 'kills.killerId != kills.killedId';
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'kills.killerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		$where = implode(' AND ', $where);
		$sql = 'SELECT player.name, COUNT(kills.gameId) AS kills FROM player JOIN kills ON player.id = kills.killerId JOIN game ON kills.gameId = game.id WHERE ' . $where . ' GROUP BY player.id ORDER BY COUNT(kills.gameId) DESC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$return = $result->name . ' with ' . $result->kills . ' kills';
		}
		$return = 'Most Kills: '  . $return;
		return $return;
	}

	private function getMostDeaths(){
		$return = 'N/A';
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'kills.killerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT player.name, COUNT(kills.gameId) AS deaths FROM player JOIN kills ON player.id = kills.killedId JOIN game ON kills.gameId = game.id ' . $where . ' GROUP BY player.id ORDER BY COUNT(kills.gameId) DESC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$return = $result->name . ' with ' . $result->deaths . ' deaths';
		}
		$return = 'Most Deaths: '  . $return;
		return $return;
	}

	private function getMostConcedes(){
		$return = 'N/A';
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'concede.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'concede.playerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT player.name, COUNT(concede.gameId) AS concedes FROM concede JOIN player ON concede.playerId = player.id JOIN game ON concede.gameId = game.id ' . $where . ' GROUP BY concede.playerId ORDER BY COUNT(concede.gameId) DESC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$return = $result->name . ' with ' . $result->concedes . ' concedes';
		}
		$return = 'Most Concedes: '  . $return;
		return $return;
	}

	private function getMostWins(){
		$return = 'N/A';
		$where = $bind = array();
		$where[] = 'points.points = 2';
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'points.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'points.playerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT player.name, COUNT(points.gameId) AS wins FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.playerId ORDER BY COUNT(points.gameId) DESC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$return = $result->name . ' with ' . $result->wins . ' wins';
		}
		$return = 'Most Wins: '  . $return;
		return $return;
	}

	private function getMostRunnerUps(){
		$return = 'N/A';
		$where = $bind = array();
		$where[] = 'points.points = 1';
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'points.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'points.playerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT player.name, COUNT(points.gameId) AS runnerups FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.playerId ORDER BY COUNT(points.gameId) DESC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$return = $result->name . ' with ' . $result->runnerups . ' runner ups';
		}
		$return = 'Most Runner Ups: '  . $return;
		return $return;
	}

	private function getMostPoints(){
		$return = 'N/A';
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'points.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'points.playerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT player.name, SUM(points.points) AS points FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.playerId ORDER BY SUM(points.points) DESC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$return = $result->name . ' with ' . $result->points . ' points';
		}
		$return = 'Most Points: '  . $return;
		return $return;
	}

	private function getKDRatios(){
		$return = $where = $bind = $kdratios = array();
		$kills = $this->getKills();
		$deaths = $this->getDeaths();
		$players = new Player();
		$players = $players->getPlayers();
		foreach($players as $player){
			if(!isset($return[$player->id])){
				$pkkey = $pdkey = null;
				$pkmatch = $pdmatch = false;
				foreach($kills as $pkkey => $playerkills){
					if($playerkills->id === $player->id){
						$pkmatch = true;
						break;
					}
				}
				foreach($deaths as $pdkey => $playerdeaths){
					if($playerdeaths->id === $player->id){
						$pdmatch = true;
						break;
					}
				}

				$pkills = $pdeaths = $pkd = 0;
				if($pkmatch && $pkkey !== null){
					$pkills = $playerkills->kills;
				}
				if($pdmatch && $pdkey !== null){
					$pdeaths = $playerdeaths->deaths;
				}

				if($pdeaths === 0){
					$pkd = '∞';
				}elseif($pkills === 0){
					$pkd = 0;
				}else{
					$pkd = round($pkills / $pdeaths, 2);
				}

				$kdratios[$player->id] = array(
					'name' => $player->name,
					'kills' => $pkills,
					'deaths' => $pdeaths,
					'kd' => $pkd
				);
			}
		}
		usort($kdratios, function($a ,$b){
			if($a['kd'] === $b['kd']){
				return ($a['kills'] < $b['kills']) ? 1 : -1;
			}else{
				return ($a['kd'] > $b['kd']) ? -1 : 1;
			}
		});
		foreach($kdratios as $kdratio){
			$return[] = $kdratio['name'] . ': ' . $kdratio['kd'] . ' (K:' . $kdratio['kills'] . ' D:' . $kdratio['deaths'] . ')';
		}
		return $return;
	}

	private function getKills(){
		$where = $bind = $return = array();
		$where[] = 'kills.killerId != kills.killedId';
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'kills.killerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		$where = implode(' AND ', $where);
		$sql = 'SELECT player.id, player.name, COUNT(kills.gameId) AS kills FROM player JOIN kills ON player.id = kills.killerId JOIN game ON kills.gameId = game.id WHERE ' . $where . ' GROUP BY player.id';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	private function getDeaths(){
		$where = $bind = $return = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'kills.killedId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT player.id, player.name, COUNT(kills.gameId) AS deaths FROM player JOIN kills ON player.id = kills.killedId JOIN game ON kills.gameId = game.id ' . $where . ' GROUP BY player.id';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	private function getDuoTeamup(){
		$return = 'N/A';
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'points.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'points.playerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT GROUP_CONCAT(player.id) AS playerids FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.gameId';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$matches = array();
			$return = $this->db->fetchAll();
			foreach($return as $row){
				$matches[] = $row->playerids;
			}
			$matches = array_count_values($matches);
			arsort($matches);
			$matches = array_slice(array_keys($matches),0,1,true);
			$matches = current($matches);
			$sql = 'SELECT player.name FROM player WHERE player.id IN (' . $matches . ')';
			$this->db->query($sql);
			$this->db->execute();
			$result = $this->db->fetchAll();
			$playernames = array();
			foreach($result as $row){
				$playernames[] = $row->name;
			}
			$return = implode(' & ', $playernames);
		}
		$return = 'Team Players: ' . $return;
		return $return;
	}

	private function getDuoFighters(){
		$return = 'N/A';
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'kills.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = '(kills.killerId = :playerId OR kills.killedId = :playerId)';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT kills.killerId, kills.killedId FROM kills JOIN game ON kills.gameId = game.id ' . $where . ' ORDER BY kills.killerId, kills.killedId';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$matches = array();
			$return = $this->db->fetchAll();
			foreach($return as $row){
				$row = (array) $row;
				sort($row);
				$matches[] = implode(',',$row);
			}
			$matches = array_count_values($matches);
			arsort($matches);
			$matches = array_slice(array_keys($matches),0,1,true);
			$matches = current($matches);
			$sql = 'SELECT player.name FROM player WHERE player.id IN (' . $matches . ')';
			$this->db->query($sql);
			$this->db->execute();
			$result = $this->db->fetchAll();
			$playernames = array();
			foreach($result as $row){
				$playernames[] = $row->name;
			}
			$return = implode(' & ', $playernames);
		}
		$return = 'Mud Buds: ' . $return;
		return $return;
	}

	private function getKilledBy(){
		$return = array();
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'kills.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'kills.killedId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT kills.killedId, GROUP_CONCAT(kills.killerId) AS killer FROM kills JOIN game ON kills.gameId = game.id ' . $where . ' GROUP BY killedId';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		$result = $this->db->fetchAll();
		$players = new Player();
		$players = $players->getPlayerIdNameMap();
		foreach($result as $row){
			$killer = explode(',', $row->killer);
			$values = array_count_values($killer);
			arsort($values);
			$killer = array_key_first($values);
			if(isset($players[$killer]) && isset($players[$row->killedId])){
				$return[] = $players[$row->killedId] . ' always dies to ' . $players[$killer];
			}
		}
		return $return;
	}

	private function getGameStats(){
		$return = array(
			$this->getQuickestKill(),
			$this->getNumberOfIncompleteGames()
		);
		return $return;
	}

	private function getQuickestKill(){
		$return = 'N/A';
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'kills.gameId = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'kills.killedId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT kills.killerId, kills.killedId, TIMESTAMPDIFF(SECOND, game.date, kills.timestamp) AS killTime FROM kills JOIN game ON kills.gameId = game.id ' . $where . ' ORDER BY TIMESTAMPDIFF(SECOND, game.date, kills.timestamp) ASC LIMIT 1';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetch();
			$time = convertSecondsToHumanReadable($result->killTime);
			$players = new Player();
			$players = $players->getPlayerIdNameMap();
			if(isset($players[$result->killerId]) && isset($players[$result->killedId])){
				$return = $players[$result->killerId] . ' killed ' . $players[$result->killedId] . ' in ' . $time;
			}
		}
		return 'Quickest Kill: ' . $return;
	}

	private function getNumberOfIncompleteGames(){
		$where = $bind = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($this->playerId)){
			$where[] = 'points.playerId = :playerId';
			$bind['playerId'] = $this->playerId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}

		$total = $incomplete = 0;
		$sql = 'SELECT COUNT(game.id) AS games, IF(points.points IS NULL, 0, 1) AS complete FROM game LEFT JOIN points ON game.id = points.gameId ' . $where . ' GROUP BY IF(points.points IS NULL, 0, 1)';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		$total = $incomplete = 0;
		if($this->db->rowCount() > 0){
			$result = $this->db->fetchAll();
			foreach($result as $row){
				$total++;
				if($row->complete === 0){
					$incomplete++;
				}
			}
		}
		return 'No Point Games: ' . $incomplete . ' out of ' . $total;
		
	}
}
?>