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
			'Game Stats' => $this->getGameStats(),
			'Points' => $this->getPointsScored(),
			'Time Lost' => $this->getGameLengths(),
			'Killed By Counts' => $this->getKilledByCount(),
			'Clean Sweeps' => $this->getCleanSweeps(),
			'Attendance' => $this->getAttendance(),
			'Win Streaks' => $this->getConsecutiveWins(),
			'Loss Streaks' => $this->getConsecutiveLosses(),
			'Last Scored Point' => $this->getLastPointScored()
		);
		if(!isset($this->seasonId)){
			$return['Season Wins'] = $this->getSeasonWins();
		}else{
			$return['Season Winner'] = $this->getSeasonWinner();
		}
		return $return;
	}

	private function getTopTrumps(){
		$return = array(
			$this->getMostKills(),
			$this->getMostDeaths(),
			$this->getMostSuicides(),
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
		$sql = 'SELECT player.name, COUNT(kills.gameId) AS kills FROM player JOIN kills ON player.id = kills.killerId JOIN game ON kills.gameId = game.id WHERE ' . $where . ' GROUP BY player.id ORDER BY COUNT(kills.gameId) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			$names = array();
			$mostkills = null;
			foreach($results as $result){
				$kills = intval($result->kills);
				if(empty($mostkills)){
					$mostkills = $kills;
				}
				if($kills === $mostkills){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostkills . ' kills';
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
		$sql = 'SELECT player.name, COUNT(kills.gameId) AS deaths FROM player JOIN kills ON player.id = kills.killedId JOIN game ON kills.gameId = game.id ' . $where . ' GROUP BY player.id ORDER BY COUNT(kills.gameId) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$mostdeaths = null;
			$names = array();
			$results = $this->db->fetchAll();
			foreach($results as $result){
				$deaths = intval($result->deaths);
				if(empty($mostdeaths)){
					$mostdeaths = $deaths;
				}
				if($deaths === $mostdeaths){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostdeaths . ' deaths';
		}
		$return = 'Most Deaths: '  . $return;
		return $return;
	}

	private function getMostSuicides(){
		$return = 'N/A';
		$where = $bind = array();
		$where[] = 'kills.killedId = kills.killerId';
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
		$sql = 'SELECT player.name, COUNT(kills.gameId) AS suicides FROM player JOIN kills ON player.id = kills.killedId JOIN game ON kills.gameId = game.id ' . $where . ' GROUP BY player.id ORDER BY COUNT(kills.gameId) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			$mostsuicides = null;
			$names = array();
			foreach($results as $result){
				$suicides = intval($result->suicides);
				if(empty($mostsuicides)){
					$mostsuicides = $suicides;
				}
				if($suicides === $mostsuicides){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostsuicides . ' suicides';
		}
		$return = 'Most Suicides: '  . $return;
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
		$sql = 'SELECT player.name, COUNT(concede.gameId) AS concedes FROM concede JOIN player ON concede.playerId = player.id JOIN game ON concede.gameId = game.id ' . $where . ' GROUP BY concede.playerId ORDER BY COUNT(concede.gameId) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			$mostconcedes = null;
			$names = array();
			foreach($results as $result){
				$concedes = intval($result->concedes);
				if(empty($mostconcedes)){
					$mostconcedes = $concedes;
				}
				if($concedes === $mostconcedes){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostconcedes . ' concedes';
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
		$sql = 'SELECT player.name, COUNT(points.gameId) AS wins FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.playerId ORDER BY COUNT(points.gameId) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			$mostwins = null;
			$names = array();
			foreach($results as $result){
				$wins = intval($result->wins);
				if(empty($mostwins)){
					$mostwins = $wins;
				}
				if($wins === $mostwins){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostwins . ' wins';
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
		$sql = 'SELECT player.name, COUNT(points.gameId) AS runnerups FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.playerId ORDER BY COUNT(points.gameId) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			$mostrunnerups = null;
			$names = array();
			foreach($results as $result){
				$runnerups = intval($result->runnerups);
				if(empty($mostrunnerups)){
					$mostrunnerups = $runnerups;
				}
				if($runnerups === $mostrunnerups){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostrunnerups . ' runner ups';
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
		$sql = 'SELECT player.name, SUM(points.points) AS points FROM points JOIN player ON points.playerId = player.id JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY points.playerId ORDER BY SUM(points.points) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$mostpoints = null;
			$names = array();
			$results = $this->db->fetchAll();
			foreach($results as $result){
				$points = intval($result->points);
				if(empty($mostpoints)){
					$mostpoints = $points;
				}
				if($points === $mostpoints){
					$names[] = $result->name;
				}else{
					break;
				}
			}
			$return = implode(', ', $names) . ' with ' . $mostpoints . ' points';
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

	private function getKilledByCount(){
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
		$sql = 'SELECT kills.killedId, kills.killerId FROM kills JOIN game ON kills.gameId = game.id ' . $where . ' ORDER BY kills.killedId, kills.killerId';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		$result = $this->db->fetchAll();
		$players = new Player();
		$players = $players->getPlayerIdNameMap();
		$killed = array();
		foreach($result as $row){
			if(!isset($killed[$row->killedId])){
				$killed[$row->killedId] = array();
			}
			if(!isset($killed[$row->killedId][$row->killerId])){
				$killed[$row->killedId][$row->killerId] = 1;
			}else{
				$killed[$row->killedId][$row->killerId]++;
			}
		}
		foreach($killed as $killedId => $killers){
			$return[] = $players[$killedId] . ' killed by:';
			arsort($killers);
			foreach($killers as $killerId => $killCount){
				$return[] = $players[$killerId] . ' - ' . $killCount;
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
		$where[] = 'TIMESTAMPDIFF(SECOND, game.date, kills.timestamp) > 360';
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
		$sql = 'SELECT game.id, IF(points.points IS NULL, 0, 1) AS complete FROM game LEFT JOIN points ON game.id = points.gameId ' . $where . ' GROUP BY game.id, IF(points.points IS NULL, 0, 1)';
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

	private function getPointsScored(){
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
		$sql = 'SELECT player.id, player.name, IFNULL(SUM(points.points), 0) AS points FROM player LEFT JOIN points ON player.id = points.playerId LEFT JOIN game ON points.gameId = game.id ' . $where . ' GROUP BY player.id ORDER BY SUM(points.points) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		$return = array();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetchAll();
			foreach($result as $row){
				$return[] = $row->name . ' - ' . $row->points;
			}
		}
		return $return;
	}

	private function getGameLengths(){
		$where = $bind = $gamelengths = array();
		if(!empty($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($this->gameId)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $this->gameId;
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT game.id, game.date FROM game JOIN points ON game.id = points.gameId ' . $where . ' GROUP BY game.id ORDER BY game.id ASC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		$min = $average = $max = 'N/A';
		if($this->db->rowCount() > 0){
			$result = $this->db->fetchAll();
			foreach($result as $key => $row){
				$date = substr($row->date, 0, 10);
				$nextkey = $key + 1;
				$endgame = $date . ' 23:59:59';
				if(isset($result[$nextkey]) && substr($result[$nextkey]->date, 0, 10) === $date){
					$endgame = $result[$nextkey]->date;
				}
				$start = new DateTime($row->date);
				$end = new DateTime($endgame);
				$length = $end->getTimestamp() - $start->getTimestamp();
				if($length < 21600 && $length > 360){
					$gamelengths[] = $length;
				}
			}
		}
		$min = $max = $avg = $total = 'N/A';
		if(!empty($gamelengths)){
			$min = convertSecondsToHumanReadable(min($gamelengths));
			$max = convertSecondsToHumanReadable(max($gamelengths));
			$avg = convertSecondsToHumanReadable(array_sum($gamelengths) / count($gamelengths));
			$total = convertSecondsToHumanReadable(array_sum($gamelengths));
		}
		return array(
			'Shortest Game: ' . $min,
			'Longest Game: ' . $max,
			'Average: ' . $avg,
			'Total: ' . $total
		);
	}

	private function getCleanSweeps(){
		$where = $bind = $cleansweeps = $return = array();
		$players = new Player();
		$players = $players->getPlayers();
		foreach($players as $player){
			$cleansweeps[$player->id] = 0;
		}
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
		$sql = 'SELECT game.id, GROUP_CONCAT(DISTINCT(kills.killerId)) AS killers FROM game JOIN kills ON kills.gameId = game.id ' . $where . ' GROUP BY game.id ORDER BY game.id ASC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$result = $this->db->fetchAll();
			foreach($result as $row){
				if(is_numeric($row->killers) && !empty($row->killers)){
					$cleansweeps[$row->killers]++;
				}
			}
		}
		arsort($cleansweeps);
		foreach($cleansweeps as $pid => $cs){
			foreach($players as $player){
				if($pid === $player->id){
					$return[] = $player->name . ': ' . $cs;
					continue;
				}
			}
		}
		return $return;
	}

	private function getAttendance(){
		$where = $bind = $return = $attendance = $gamenights = array();
		$players = new Player();
		$players = $players->getPlayers();
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
		$sql = 'SELECT DATE(game.date) AS gamenight, COUNT(game.id) AS gamecount FROM game JOIN season ON game.seasonId = season.id ' . $where . ' GROUP BY DATE(game.date)';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$gamenights = $this->db->fetchAll();
		}
		$totalnights = 0;
		if(!empty($gamenights)){
			$totalnights = count($gamenights);
			foreach($players as $player){
				$attendance[$player->id] = $totalnights;
			}
			$sql = 'SELECT player.id, COUNT(concede.playerId) AS concedecount, DATE(game.date) AS gamenight FROM player JOIN concede ON concede.playerId = player.id JOIN game ON concede.gameId = game.id ' . $where . ' GROUP BY concede.playerId, DATE(game.date)';
			$this->db->query($sql);
			foreach($bind as $key => $val){
				$this->db->bind($key, $val);
			}
			$this->db->execute();
			$concedes = $this->db->fetchAll();
			foreach($concedes as $concede){
				foreach($gamenights as $gamenight){
					if($concede->gamenight === $gamenight->gamenight && $concede->concedecount === $gamenight->gamecount){
						$attendance[$concede->id]--;
					}
				}
			}
			arsort($attendance);
			foreach($attendance as $akey => $aval){
				foreach($players as $player){
					if($akey === $player->id){
						$return[] = $player->name . ': ' . round(($aval / ($totalnights / 100))) . '%';
					}
				}
			}
		}else{
			$return = 'N/A';
		}

		return $return;
	}

	private function getConsecutiveWins(){
		$where = $bind = $return = array();
		$players = new Player();
		$players = $players->getPlayers();
		foreach($players as $player){
			$return[$player->id] = 0;
		}
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
		$sql = 'SELECT points.playerId FROM game JOIN points ON game.id = points.gameId AND points.points = 2 JOIN season ON game.seasonId = season.id ' . $where . ' ORDER BY game.date ASC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		$lastwinner = null;
		$consecutiveswins = 0;
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			foreach($results as $row){
				if($row->playerId !== $lastwinner){
					$consecutivewins = 1;
				}else{
					$consecutivewins++;
				}
				if($consecutivewins > $return[$row->playerId]){
					$return[$row->playerId] = $consecutivewins;
				}
				$lastwinner = $row->playerId;
			}
		}
		arsort($return);
		foreach($return as $key => $val){
			foreach($players as $player){
				if($key === $player->id){
					$return[$key] = $player->name . ': ' . $val;
					break;
				}
			}
		}
		return $return;
	}

	private function getConsecutiveLosses(){
		$where = $bind = $return = $consecutivelosses = $games = array();
		$players = new Player();
		$players = $players->getPlayers();
		foreach($players as $player){
			$return[$player->id] = $consecutivelosses[$player->id] = $return[$player->id] = 0;
		}
		$where[] = 'points.points = 2';
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
		$sql = 'SELECT game.id, points.playerId, points.points FROM game JOIN points ON game.id = points.gameId JOIN season ON game.seasonId = season.id ' . $where . ' GROUP BY game.id, points.playerId ORDER BY game.date ASC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			foreach($results as $rs){
				foreach($consecutivelosses as $playerid => $concount){
					if($playerid === $rs->playerId){
						$consecutivelosses[$playerid] = 0;
					}else{
						$consecutivelosses[$playerid]++;
					}
					if($consecutivelosses[$playerid] > $return[$playerid]){
						$return[$playerid] = $consecutivelosses[$playerid];
					}
				}
			}
		}
		arsort($return);
		foreach($return as $key => $val){
			foreach($players as $player){
				if($key === $player->id){
					$return[$key] = $player->name . ': ' . $val;
					break;
				}
			}
		}
		return $return;
	}

	private function getLastPointScored(){
		$where = $bind = $return = array();
		$players = new Player();
		$players = $players->getPlayers();
		foreach($players as $player){
			$return[$player->id] = 'N/A';
		}
		$where[] = 'points.points IS NOT NULL';
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

		$sql = 'SELECT player.id, player.name, TIMESTAMPDIFF(HOUR, MAX(game.date), NOW()) AS lastScored FROM player JOIN points ON player.id = points.playerId JOIN game ON game.id = points.gameId ' . $where . ' GROUP BY player.id, points.playerId ORDER BY MAX(game.date) DESC';
		$this->db->query($sql);
		foreach($bind as $key => $val){
			$this->db->bind($key, $val);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			foreach($results as $rs){
				$return[$rs->id] = $rs->lastScored;
			}
		}
		asort($return);
		foreach($return as $rkey => $rval){
			foreach($players as $player){
				if($rkey === $player->id){
					$values = array();
					$days = 0;
					$hours = $rval;
					if(is_int($rval)){
						$hours = $rval % 24;
						$days = floor($rval/24);
						if($days > 0){
							$values[] = $days . ' day' . ($days > 1 ? 's' : '');
						}
						if($hours > 0){
							$values[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
						}
					}else{
						$values[] = 'N/A';
					}
					$return[$rkey] = $player->name . ': ' . implode(', ', $values);
				}
			}
		}
		return $return;
	}

	private function getSeasonWins(){
		$return = array();
		$players = new Player();
		$players = $players->getPlayers();
		foreach($players as $player){
			$return[$player->id] = 0;
		}
		$seasonwins = array();
		$activeseason = new Season();
		$activeseason->getLatestSeason();
		$ignoreseason = null;
		$where = '';
		if(isset($activeseason->id) && date('Y-m-d H:i:s') < $activeseason->endDate){
			$ignoreseason = $activeseason->id;
			$where = ' WHERE season.id != :seasonId';
		}
		$sql = 'SELECT season.id, points.playerId, SUM(points.points) FROM points JOIN game ON points.gameId = game.id JOIN season ON game.seasonId = season.id ' . $where . ' GROUP BY season.id, points.playerId ORDER BY season.id ASC, SUM(points.points) DESC';
		$this->db->query($sql);
		if(!empty($where) && !empty($ignoreseason)){
			$this->db->bind('seasonId', $ignoreseason);
		}
		$this->db->execute();
		$lastSeason = null;
		if($this->db->rowCount() > 0){
			$results = $this->db->fetchAll();
			foreach($results as $rs){
				if($lastSeason === $rs->id){
					continue;
				}
				$return[$rs->playerId]++;
				$lastSeason = $rs->id;
			}
		}
		if(!empty($return)){
			arsort($return);
			foreach($return as $rkey => $rval){
				foreach($players as $player){
					if($rkey === $player->id){
						$return[$rkey] = $player->name . ': ' . $rval;
						break;
					}
				}
			}
		}else{
			$return = array('N/A');
		}
		return $return;
	}

	private function getSeasonWinner(){
		$return = 'N/A';
		if(isset($this->seasonId)){
			$season = new Season($this->seasonId);
			if(date('Y-m-d H:i:s') >= $season->endDate){
				$points = $this->getPointsScored();
				if(!empty($points)){
					$return = current($points) . ' points';
				}
			}
		}
		return array($return);
	}

	public function getPointsChartData(){
		$return = array();
		$sql = 'SELECT game.id, game.date, points.points, points.playerId FROM points JOIN game ON points.gameId = game.id';
		if(isset($this->seasonId)){
			$sql .= ' WHERE game.seasonId = :seasonId';
		}
		$sql .= ' ORDER BY game.id ASC';
		$this->db->query($sql);
		if(isset($this->seasonId)){
			$this->db->bind('seasonId', $this->seasonId);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	public function getKDChartData(){
		$return = array();
		$sql = 'SELECT game.id, game.date, kills.killerId, kills.killedId FROM kills JOIN game ON kills.gameId = game.id';
		if(isset($this->seasonId)){
			$sql .= ' WHERE game.seasonId = :seasonId';
		}
		$sql .= ' ORDER BY game.id ASC';
		$this->db->query($sql);
		if(isset($this->seasonId)){
			$this->db->bind('seasonId', $this->seasonId);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetchAll();
		}
		return $return;
	}

	public function getWinsChartData(){
		$return = array();
		$players = new player();
		$players = $players->getPlayerIdNameMap();
		foreach($players as $pid => $player){
			$return[$pid] = 0;
		}
		$sql = 'SELECT points.playerId, COUNT(points.gameId) AS wins FROM points JOIN game on points.gameId = game.id';
		$where = $bind = array();
		$where[] = 'points.points = 2';
		if(isset($this->seasonId)){
			$where[] = 'game.seasonId = :seasonId';
			$bind['seasonId'] = $this->seasonId;
		}
		if(!empty($where)){
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}
		$sql .= ' GROUP BY points.playerId';
		$this->db->query($sql);
		if(!empty($bind)){
			foreach($bind as $bkey => $bval){
				$this->db->bind($bkey, $bval);
			}
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$rs = $this->db->fetchAll();
			foreach($rs as $row){
				$return[$row->playerId] = $row->wins;
			}
		}
		return $return;
	}

	public function getKillsChartData(){
		$return = array();
		$players = new player();
		$players = $players->getPlayerIdNameMap();
		foreach($players as $pid => $player){
			$return[$pid] = array();
			foreach($players as $npid => $nplayer){
				$return[$pid][$npid] = 0;
			}
		}
		$sql = 'SELECT kills.killerId, kills.killedId, COUNT(kills.killedId) AS killedCount FROM kills JOIN game ON kills.gameId = game.id';
		if(isset($this->seasonId)){
			$sql .= ' WHERE game.seasonId = :seasonId';
		}
		$sql .= ' GROUP BY kills.killerId, kills.killedId ORDER BY kills.killerId';
		$this->db->query($sql);
		if(isset($this->seasonId)){
			$this->db->bind('seasonId', $this->seasonId);
		}
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$rs = $this->db->fetchAll();
			foreach($rs as $row){
				$return[$row->killerId][$row->killedId] = $row->killedCount;
			}
		}
		return $return;
	}
}
?>