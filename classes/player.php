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
}
?>