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

	public function createNewSeason(string $startDate, string $endDate, string $name){
		$startDate .= ' 00:00:00';
		$endDate .= ' 23:59:59';
		$sql = 'INSERT INTO season (name, startDate, endDate) VALUES (:name, :startDate, :endDate)';
		$this->db->query($sql);
		$this->db->bind('name', $name);
		$this->db->bind('startDate', $startDate);
		$this->db->bind('endDate', $endDate);
		$this->db->execute();
		$id = $this->db->lastInsertId();
		$this->getSeason($id);
	}

	private function getSeason(int $id = null, int $gameid = null, bool $latest = false){
		$bind = $where = array();
		if(!empty($id)){
			$where[] = 'season.id = :seasonId';
			$bind['seasonId'] = $id;
		}
		if(!empty($gameid)){
			$where[] = 'game.id = :gameId';
			$bind['gameId'] = $gameid;
		}
		if(empty($id) && empty($gameid) && $latest === false){
			$where[] = 'season.startDate <= :startDate';
			$where[] = 'season.endDate >= :endDate';
			$bind['startDate'] = date('Y-m-d') . ' 00:00:00';
			$bind['endDate'] = date('Y-m-d') . ' 23:59:59';
		}
		if(!empty($where)){
			$where = 'WHERE ' . implode(' AND ', $where);
		}else{
			$where = null;
		}
		$sql = 'SELECT season.id, season.name, season.startDate, season.endDate FROM season LEFT JOIN game ON season.id = game.seasonId ' . $where . ' ORDER BY season.id DESC LIMIT 1';
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
			$sql = 'SELECT player.id, player.name, IFNULL(SUM(points.points), 0) AS seasonPoints FROM player LEFT JOIN points ON player.id = points.playerId LEFT JOIN game ON points.gameId = game.id WHERE game.seasonId = :seasonId OR game.seasonId IS NULL GROUP BY player.id ORDER BY SUM(points.points) DESC';
			$this->db->query($sql);
			$this->db->bind('seasonId', $this->id);
			$this->db->execute();
			if($this->db->rowCount() > 0){
				$return = $this->db->fetchAll();
			}
			$players = new Player();
			$players = $players->getPlayerIdNameMap();
			if(empty($return)){
				$return = array();
			}
			foreach($return as $rkey => $rval){
				if(isset($players[$rval->id])){
					unset($players[$rval->id]);
				}
			}
			foreach($players as $pid => $pname){
				$return[] = array(
					'id' => $pid,
					'name' => $pname,
					'seasonPoints' => 0
				);
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
		$this->getSeason(null, null, true);
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

	public function calculateNextEndDate(){
		/* Constants */
		define('MAR', 0);
		define('JUN', 1);
		define('SEP', 2);
		define('DEC', 3);

		/* Tables from Meeus, page 178 */
		/* Table 27.A, for the years -1000 to 1000 */
		

		$month = intval(date('n'));
		$month = (round($month / 3) - 1);
		$year = intval(date('Y'));
		$nextseasonstart = date('Y-m-d', $this->getAstronomicalSeason($year, $month));
		$nextseasonend = date('Y-m-d', ($this->getAstronomicalSeason($year, $month + 1) - 86400));
		$seasonname = $this->generateSeasonName($year, $month);

		return array(
			'start' => $nextseasonstart,
			'end' => $nextseasonend,
			'name' => $seasonname
		);
	}

	private function calculateJDE0($year, $which)
	{
		$yearTable0 = [
			MAR => [ 1721139.29189, 365242.13740, +0.06134, +0.00111, -0.00071 ],
			JUN => [ 1721233.25401, 365241.72562, -0.05323, +0.00907, +0.00025 ],
			SEP => [ 1721325.70455, 365242.49558, -0.11677, -0.00297, +0.00074 ],
			DEC => [ 1721414.39987, 365242.88257, -0.00769, -0.00933, -0.00006 ],
		];

		/* Table 27.B, for the years 1000 to 3000 */
		$yearTable2000 = [
			MAR => [ 2451623.80984, 365242.37404, +0.05169, -0.00411, -0.00057 ],
			JUN => [ 2451716.56767, 365241.62603, +0.00325, +0.00888, -0.00030 ],
			SEP => [ 2451810.21715, 365242.01767, -0.11575, +0.00337, +0.00078 ],
			DEC => [ 2451900.05952, 365242.74049, -0.06223, -0.00823, +0.00032 ],
		];

		$table = $year < 1000 ? $yearTable0 : $yearTable2000;
		$Y     = $year < 1000 ? ($year / 1000) : (($year - 2000) / 1000);
		$terms = $table[$which];

		$JDE0 = $terms[0] +
			($terms[1] * $Y) +
			($terms[2] * $Y * $Y) +
			($terms[3] * $Y * $Y * $Y) +
			($terms[4] * $Y * $Y * $Y * $Y);

		return $JDE0;
	}

	/* Meeus, Table 27.C, page 179 */
	private function calculateS($T)
	{
		$table = [
			[ 485, 324.96,   1934.136 ],
			[ 203, 337.23,  32964.467 ],
			[ 199, 342.08,     20.186 ],
			[ 182,  27.85, 445267.112 ],
			[ 156,  73.14,  45036.886 ],
			[ 136, 171.52,  22518.443 ],
			[  77, 222.54,  65928.934 ],
			[  74, 296.72,   3034.906 ],
			[  70, 243.58,   9037.513 ],
			[  58, 119.81,  33718.147 ],
			[  52, 297.17,    150.678 ],
			[  50,  21.02,   2281.226 ],
			[  45, 247.54,  29929.562 ],
			[  44, 325.15,  31555.956 ],
			[  29,  60.93,   4443.417 ],
			[  18, 155.12,  67555.328 ],
			[  17, 288.79,   4562.452 ],
			[  16, 198.04,  62894.029 ],
			[  14, 199.76,  31436.921 ],
			[  12,  95.39,  14577.848 ],
			[  12, 287.11,  31931.756 ],
			[  12, 320.81,  34777.259 ],
			[   9, 227.73,   1222.114 ],
			[   8,  15.45,  16859.074 ],
		];
		
		$sum = 0;
		foreach( $table as $term ) {
			$c = $term[0] * cos(deg2rad( $term[1] + ($term[2] * $T) ));

			$sum += $c;
		}
		return $sum;
	}

	/* Meeus, chapter 10 */
	private function deltaDTtoUT($year)
	{
		$t = ($year - 2000) / 100;

		if ($year < 948) {
			$dT = 2177 + (497 * $t) + (44.1 * $t * $t);
		}
		
		/* There is a table on page 79 for the years 1620 to 1998, which I didn't
		 * bother to include here */
		
		if (($year >= 948 && $year < 1600) || $year >= 2000) {
			$dT = 102 + (102 * $t) + (25.3 * $t * $t);
		}

		/* to avoid a discontinuity at A.D. 2000, it is advised to add the
		 * correction +0.37 x (year - 2100) for the years 2000 to 2100 */
		if ($year >= 2000 && $year < 2100) {
			$dT += 0.37 * ($year - 2100);
		}

		return $dT;
	}

	private function JDEtoTimestamp($JDE)
	{
		$tmp = $JDE;
		$tmp -= 2440587.5;
		$tmp *= 86400;

		return $tmp;
	}

	private function getAstronomicalSeason($year, $which){
		if($which > 3){
			$which = 0;
			$year++;
		}
		/* Meeus, page 177 */
		$JDE0 = $this->calculateJDE0($year, $which);
		$T   = ($JDE0 - 2451545.0) / 36525;
		$W = (35999.373 * $T) - 2.47;
		$L = 1 + 0.0334 * cos(deg2rad($W)) + 0.0007 * cos(2 * deg2rad($W));
		$S = $this->calculateS($T);

		/* Meeus, page 178 */
		$JDE = $JDE0 + ((0.00001 * $S) / $L);

		/* Convert TD to PHP Date */
		$date = $this->JDEtoTimestamp($JDE);
		return $date;
	}

	private function generateSeasonName($year, $which){
		if($which > 3){
			$which = 0;
			$year++;
		}
		$seasons = array(
			'Spring',
			'Summer',
			'Autumn',
			'Winter'
		);
		$name = $seasons[$which] . ' ' . $year;
		return $name;
	}
}
?>