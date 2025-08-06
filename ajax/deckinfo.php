<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$player = false;
$html = '';
$colorcombos = array(
	'WUBRG' => array(),
	'UBRG' => array(),
	'WBRG' => array(),
	'WURG' => array(),
	'WUBG' => array(),
	'WUBR' => array(),
	'WUB' => array(),
	'UBR' => array(),
	'BRG' => array(),
	'WRG' => array(),
	'WUG' => array(),
	'WBG' => array(),
	'URG' => array(),
	'UBG' => array(),
	'WUR' => array(),
	'WBR' => array(),
	'WU' => array(),
	'WB' => array(),
	'WR' => array(),
	'WG' => array(),
	'UB' => array(),
	'UR' => array(),
	'UG' => array(),
	'BR' => array(),
	'BG' => array(),
	'RG' => array(),
	'W' => array(),
	'U' => array(),
	'B' => array(),
	'R' => array(),
	'G' => array(),
	'C' => array()
);
if(isset($_GET) && isset($_GET['id']) && is_numeric($_GET['id'])){
	$player = intval($_GET['id']);
	$decks = new Player();
	$decks = $decks->getPlayersDecks($player);
	if(isset($decks[$player])){
		$decks = $decks[$player];

		foreach($decks as $deck){
			$colorcombos[$deck->colors][] = $deck->name;
		}
		ob_start();
		?>
		<table>
			<thead>
				<tr>
					<th>Colours</th><th>Decks</th>
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
				$decks = empty($decks) ? '-' : implode('<br/>', $decks);
				?>
				<tr>
					<td><?=$manahtml?></td>
					<td><?=$decks?></td>
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