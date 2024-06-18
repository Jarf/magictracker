<?php
class Season{
	public int $id;
	public string $name;
	public string $startDate;
	public string $endDate;
	private $db;

	public function __construct(int $id = null, int $gameid = null){
		$this->db = new DB();
		$this->getSeason($id, $gameid);
	}

	private function getSeason(int $id = null, int $gameid = null){
		$bind = $where = array();
		if(!empty($id)){
			$where[] = 'season.id = :seasonId';
			$bind['seasonId'] = $id;
		}
		if(!empty($gameid)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $gameid;
		}
		if(empty($id) && empty($gameid)){
			$where[] = 'season.startDate <= :startDate';
			$where[] = 'season.endDate >= :endDate';
			$bind['startDate'] = date('Y-m-d') . ' 00:00:00';
			$bind['endDate'] = date('Y-m-d') . ' 23:59:59';
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}
		$sql = 'SELECT season.id, season.name, season.startDate, season.endDate FROM season LEFT JOIN game ON season.id = game.seasonId ' . $where . ' LIMIT 1';
		$this->db->query($sql);
		if(!empty($bind)){
			foreach($bind as $key => $val){
				$this->db->bind($key, $val);
			}
		}
		$this->db->execute();
		$return = false;
		if($this->db->rowCount() > 0){
			$return = $this->db->fetch();
			foreach($return as $key => $val){
				$this->$key = $val;
			}
		}
		return $return;
	}

	public function getSeasonRanking(){
		$return = false;
		if(!empty($this->id)){
			$sql = 'SELECT player.name, IFNULL(SUM(points.points), 0) AS seasonPoints FROM player LEFT JOIN points ON player.id = points.playerId LEFT JOIN game ON points.gameId = game.id WHERE game.seasonId = :seasonId OR game.seasonId IS NULL GROUP BY player.id ORDER BY SUM(points.points) DESC';
			$this->db->query($sql);
			$this->db->bind('seasonId', $this->id);
			$this->db->execute();
			if($this->db->rowCount() > 0){
				$return = $this->db->fetchAll();
			}
		}
		return $return;
	}

	public function getSeasonGames(){
		$return = array();
		if(!empty($this->id)){
			$sql = 'SELECT game.id, game.date FROM game WHERE game.seasonId = :seasonId ORDER BY game.id ASC';
			$this->db->query($sql);
			$this->db->bind('seasonId', $this->id);
			$this->db->execute();
			if($this->db->rowCount() > 0){
				$return = $this->db->fetchAll();
				foreach($return as $rkey => $row){
					$return[$rkey]->name = 'Game ' . ($rkey + 1);
				}
			}
		}
		$return = array_reverse($return);
		return $return;
	}

	public function getLatestSeason(){
		$sql = 'SELECT season.id FROM season ORDER BY season.id DESC LIMIT 1';
		$this->db->query($sql);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetch();
			$this->getSeason($return->id);
		}
	}

	public function updateDates(string $startDate, string $endDate){
		$update = $bind = array();
		if(isset($this->id) && !empty($this->id)){
			$bind['seasonId'] = $this->id;
			foreach(array('startDate', 'endDate') as $datetype){
				$$datetype .= ($datetype === 'startDate') ? ' 00:00:00' : ' 23:59:59';
				if($$datetype !== $this->$datetype){
					$update[] = $datetype . '= :' . $datetype;
					$bind[$datetype] = $$datetype;
					$this->$datetype = $$datetype;
				}
			}
			if(!empty($update)){
				$sql = 'UPDATE season SET ' . implode(', ', $update) . ' WHERE season.id = :seasonId';
				$this->db->query($sql);
				foreach($bind as $key => $val){
					$this->db->bind($key, $val);
				}
				$this->db->execute();
			}
		}
	}
}
?>