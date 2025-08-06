<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$type = null;
if(isset($_GET) && isset($_GET['type'])){
	$type = $_GET['type'];
	$gameid = $seasonid = null;
	if(isset($_GET['game']) && is_numeric($_GET['game'])){
		$gameid = intval($_GET['game']);
	}
	if(isset($_GET['season']) && is_numeric($_GET['season'])){
		$seasonid = intval($_GET['season']);
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
			$season = new Season($seasonid);
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
			$season = new Season($seasonid, $gameid);
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

		case 'seasondate':
			$season = new Season($seasonid);
			ob_start();
			?>
			<h1><?=$season->name?></h1>
			<h3>Start/End Dates</h3>
			<hr/>
			<label for="startdate">Start:</label>
			<input id="startdate" name="startdate" type="date" disabled readonly value="<?=substr($season->startDate,0,10)?>"/><br/>
			<label for="enddate">End:</label>
			<input id="enddate" name="enddate" type="date" min="<?=date('Y-m-d')?>" value="<?=substr($season->endDate,0,10)?>"/>
			<hr/>
			<button id="saveseasondates">Save Dates</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'newseason':
			$season = new Season($seasonid);
			$enddate = DateTime::createFromFormat('Y-m-d H:i:s', $season->endDate);
			$mindate = date('Y-m-d',strtotime(date('Y-m-d', strtotime($season->endDate)) . ' 00:00:00 +1 day'));
			$today = date('Y-m-d');
			$estimateddates = $season->calculateNextEndDate();
			ob_start();
			?>
			<h1>New Season</h1>
			Last season ended on <?=date('jS F, Y', $enddate->getTimestamp())?><br/>
			<hr/>
			<label for="seasonname">Name:</label>
			<input id="seasonname" type="text" value="<?=$estimateddates['name']?>"/><br/>
			<label for="startdate">Start:</label>
			<input id="startdate" name="startdate" type="date" min="<?=$mindate?>" value="<?=$today?>"/><br/>
			<label for="enddate">End:</label>
			<input id="enddate" name="enddate" type="date" min="<?=$today?>" value="<?=$estimateddates['end']?>"/>
			<hr/>
			<button id="createnewseason">Create New Season</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'winbin':
			$player = new Player();
			$decks = $player->getPlayersDecks();
			$winbins = $player->getPlayersWinBins();
			$playernames = $player->getPlayerIdNameMap();
			ob_start();
			?>
			<h1>Win Bin</h1>
			<hr/>
			<?php foreach($playernames as $pid => $pname): ?>
			<label for="winbin<?=$pid?>"><?=$pname?></label>
			<?php if(isset($decks[$pid])): ?>
			<select id="winbin<?=$pid?>" class="winbinselect" data-player="<?=$pid?>">
				<option>None</option>
				<?php foreach($decks[$pid] as $deck): ?>
				<option value="<?=$deck->deckId?>" <?=($winbins[$pid] == $deck->deckId ? 'selected' : '')?>><?=$deck->name?> - <?=empty($deck->colors) ? 'Colourless' : $deck->colors?></option>
				<?php endforeach; ?>
			</select>
			<?php else: ?>
			hates Archidekt
			<?php endif; ?>
			<hr/>
			<?php endforeach; ?>
			<button id="savewinbin">Save Win Bin</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'deckpicker':
			$player = new Player();
			$playernames = $player->getPlayerIdNameMap();
			ob_start();
			?>
			<h1>Deck Picker</h1>
			<h3>Excludes winbin decks</h3>
			<select id="deckpickerplayer" class="deckpickerplayerselect">
				<?php foreach($playernames as $playerid => $playername): ?>
					<option value="<?=$playerid?>"><?=$playername?></option>
				<?php endforeach; ?>
			</select>
			<hr/>
			<div id="deckpickerresult"></div>
			<button id="pickdeck">Pick A Random Deck</button>
			<hr/>
			<button id="closemodal">Close</button>
			<?php
			$html = ob_get_clean();
			print $html;
			break;

		case 'deckinfo':
			$player = new Player();
			$playernames = $player->getPlayerIdNameMap();
			ob_start();
			?>
			<h1>Deck Colours</h1>
			<h3>Show combos you have decks in</h3>
			<select id="deckinfoplayer" class="deckpickerplayerselect">
				<?php foreach($playernames as $playerid => $playername): ?>
					<option value="<?=$playerid?>"><?=$playername?></option>
				<?php endforeach; ?>
			</select>
			<hr/>
			<button id="deckinfo">Show Deck Colours</button>
			<hr/>
			<div id="deckinforesult"></div>
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