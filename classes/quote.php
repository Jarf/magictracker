<?php
class Quote{
	public int $id;
	public int $playerId;
	public string $quote;
	public string $date;
	public string $playerName;
	private $db;

	public function __construct(){
		$this->db = new db();
	}

	public function getRandomQuote(){
		$quote = false;
		$sql = 'SELECT quotes.id, quotes.playerId, player.name, quotes.quote, quotes.date FROM quotes LEFT JOIN player ON quotes.playerId = player.id ORDER BY RAND() LIMIT 1';
		$this->db->query($sql);
		$this->db->execute();
		if($this->db->rowCount() > 0){
			$return = $this->db->fetch();
			$this->id = $return->id;
			if(!empty($return->playerId)){
				$this->playerId = $return->playerId;
				$this->playerName = $return->name;
			}
			$this->quote = $return->quote;
			if(!empty($return->date)){
				$this->date = $return->date;
			}
		}

		return $this->quoteAsString();
	}

	private function quoteAsString(){
		$string = array();
		if(!empty($this->quote)){
			$string[] = '"' . $this->quote . '"';
		}
		if(!empty($this->playerName)){
			$string[] = ' - ' . $this->playerName;
		}else{
			$string[] = ' - unknown';
		}
		if(!empty($this->date)){
			$string[] = '<br/>circa ' . date('jS F, Y', strtotime($this->date));
		}else{
			$string[] = ', n.d.';
		}
		if(!empty($string)){
			$string = implode("", $string);
		}else{
			$string = false;
		}
		return $string;
	}
}
?>