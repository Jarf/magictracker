<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$type = null;
if(isset($_GET) && isset($_GET['type'])){
	$type = $_GET['type'];
	$gameid = null;
	if(isset($_GET['game']) && is_numeric($_GET['game'])){
		$gameid = intval($_GET['game']);
	}
	switch ($type) {
		case 'kills':
			$game = new Game($gameid);
			$players = new Player();
			$players = $players->getPlayers();
			$kills = $game->getGameKills();
			$gamecount = $game->getGameNumber();
			ob_start();
			?>
			<h1>Game <?=$gamecount?> Kills</h1>
			<hr/>
			<ul id="killlist">
				<?php foreach($kills as $kill): ?>
					<li class="killitem"><span class="killer"><?=$kill->killerName?></span><img src="/img/killicon.svg"/><span class="killed"><?=$kill->killedName?></span><button class="killremove" data-killerId="<?=$kill->killerId?>" data-killedId="<?=$kill->killedId?>" data-text="<?=$kill->killerName?> killed <?=$kill->killedName?> from Game <?=$gamecount?>"></button></li>
				<?php endforeach; ?>
			</ul>
			<hr/>
			<div id="killform">
				<div>
					<select id="killkiller">
						<option disabled selected value=''>Select Killer</option>
						<?php foreach($players as $player): ?>
							<option value="<?=$player->id?>"><?=$player->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div>
					<select id="killkilled">
						<option disabled selected value=''>Select Killed</option>
						<?php foreach($players as $player): ?>
							<option value="<?=$player->id?>"><?=$player->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<button id="killsubmit">Add Kill</button>
			</div>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'points':
			$game = new Game($gameid);
			$points = $game->getGamePoints();
			$gamecount = $game->getGameNumber();
			ob_start();
			?>
			<h1>Game <?=$gamecount?> Points</h1>
			<hr/>
			<ul id="pointslist">
				<?php foreach($points as $point): ?>
					<li>
						<span><?=$point->name?></span>
						<span class="pointslistvalues"><button class="addpoint"></button><input type="number" min="0" max="2" readonly value="<?=$point->points?>"/><button class="subtractpoint"></button></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<hr/>
			<button id="savepoints">Save Points</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;
		
		default:
			display404();
			break;
	}
}
?>