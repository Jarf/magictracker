<?php
class Season{
	public int $id;
	public string $name;
	public string $startDate;
	public string $endDate;
	private $db;

	public function __construct(int $id = null){
		$this->db = new DB();
		if(!empty($id)){
			$this->getSeason($id);
		}
	}

	private function getSeason(int $id){
		$sql = 'SELECT season.id, season.name, season.startDate, season.endDate FROM season WHERE season.id = :seasonId LIMIT 1';
		$this->db->query($sql);
		$this->db->bind('seasonId', $id);
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
}
?>