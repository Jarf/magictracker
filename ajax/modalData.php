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
						<span class="pointslistvalues"><button class="addpoint"></button><input data-player="<?=$point->id?>" type="number" min="0" max="2" readonly value="<?=$point->points?>"/><button class="subtractpoint"></button></span>
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

		case 'concedes':
			$game = new Game($gameid);
			$concedes = $game->getGameConcedes();
			$gamecount = $game->getGameNumber();
			ob_start();
			?>
			<h1>Game <?=$gamecount?> Concedes</h1>
			<hr/>
			<ul id="concedeslist">
				<?php foreach($concedes as $ckey => $concede): ?>
					<li>
						<label for="concedeinput<?=$ckey?>"><?=$concede->name?></label>
						<input class="concedeinput" value="<?=$concede->id?>" id="concedeinput<?=$ckey?>" type="checkbox" <?=!empty($concede->concede) ? 'checked' : ''?>/>
					</li>
				<?php endforeach; ?>
			</ul>
			<hr/>
			<button id="saveconcedes">Save Concedes</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'seasonranking':
			$season = new Season();
			$ranking = $season->getSeasonRanking();
			ob_start();
			?>
			<h3>Scores</h3>
			<?php foreach($ranking as $rank): ?>
				<?=$rank->name?> - <?=$rank->seasonPoints?><br/>
			<?php endforeach;
			$html = ob_get_clean();
			print $html;
			break;

		case 'quote':
			$players = new Player();
			$players = $players->getPlayers();
			ob_start();
			?>
			<h1>Add Quote</h1>
			<hr/>
			<textarea id="quoteinput" name="quoteinput" rows="2"></textarea><br/>
			<select id="quoteauthor" name="quoteauthor">
				<option value="0" disabled selected>Who said it?</option>
				<option value="0">Unknown</option>
				<?php foreach($players as $player): ?>
					<option value="<?=$player->id?>"><?=$player->name?></option>
				<?php endforeach; ?>
			</select><br/>
			<input id="quotedate" name="quotedate" type="date"/>
			<hr/>
			<button id="savequote">Save Quote</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'games':
			$season = new Season(null, $gameid);
			$games = $season->getSeasonGames();
			ob_start();
			?>
			<h1><?=$season->name?></h1>
			<h3>Game List</h3>
			<hr/>
			Select a game to load it
			<ul>
				<?php foreach($games as $game): ?>
					<li><a href="/<?=$game->id?>"><?=$game->name?> - <?=$game->date?></a></li>
				<?php endforeach; ?>
			</ul>
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