<?php
class Season{
	public int $id;
	public string $name;
	public string $startDate;
	public string $endDate;
	private $db;

	public function __construct(int $id = null){
		$this->db = new DB();
		$this->getSeason($id);
	}

	private function getSeason(int $id = null){
		if(!empty($id)){
			$where = 'season.id = :seasonId';
		}else{
			$where = 'season.startDate <= NOW() AND season.endDate >= NOW()';
		}
		$sql = 'SELECT season.id, season.name, season.startDate, season.endDate FROM season WHERE ' . $where . ' LIMIT 1';
		$this->db->query($sql);
		if(!empty($id)){
			$this->db->bind('seasonId', $id);
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
}
?>