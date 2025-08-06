<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$player = false;
$html = '';
$db = new db();
$db->query('SELECT combo, name FROM deckColors ORDER BY LENGTH(combo) DESC, combo DESC');
$db->execute();
$rs = $db->fetchAll();
$colorcombos = array();
foreach($rs as $colorcombo){
	$colorcombos[$colorcombo->combo] = array(
		'name' => $colorcombo->name,
		'decks' => array()
	);
}
if(isset($_GET) && isset($_GET['id']) && is_numeric($_GET['id'])){
	$player = intval($_GET['id']);
	$decks = new Player();
	$decks = $decks->getPlayersDecks($player);
	if(isset($decks[$player])){
		$decks = $decks[$player];

		foreach($decks as $deck){
			$colorcombos[$deck->colors]['decks'][] = $deck->name;
		}
		ob_start();
		?>
		<table id="deckColorTable">
			<thead>
				<tr>
					<th>Colours</th><th>Name</th><th>Decks</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($colorcombos as $colors => $decks): ?>
				<?php
				$mana = $colors;
				$manahtml = '';
				if(empty($mana)){
					$mana = 'C';
				}
				$mana = str_split($mana);
				foreach($mana as $mval){
					$manahtml .= '<img src="/img/mana/' . $mval . '.svg" height=15 width=15/>';
				}
				$deckshtml = empty($decks['decks']) ? '-' : implode('<br/>', $decks['decks']);
				?>
				<tr>
					<td><?=$manahtml?></td>
					<td><?=$decks['name']?></td>
					<td><?=$deckshtml?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<hr/>
		<?php
		$html = ob_get_clean();
	}else{
		$html = 'No decks';
	}
}
if($player === false){
	display404();
}else{
	print $html;
}
?>