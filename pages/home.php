<?php
$pagevars['quote'] = new Quote;
$pagevars['quote'] = $pagevars['quote']->getRandomQuote();
$pagevars['seasonexpired'] = false;
$pagevars['season'] = new Season($seasonid, $gameid);
if(!isset($pagevars['season']->id)){
	$pagevars['season']->getLatestSeason();
}
if(isset($pagevars['season']->endDate)){
	if(date('Y-m-d H:i:s') > $pagevars['season']->endDate){
		$pagevars['seasonexpired'] = true;
	}
}else{
	$pagevars['seasonexpired'] = true;
}
if(!empty($pagevars['season']->endDate)){
	$pagevars['countdown'] = floor((strtotime($pagevars['season']->endDate) - time()) / 86400);
	$endDate = DateTime::createFromFormat('Y-m-d H:i:s', $pagevars['season']->endDate);
	if($pagevars['countdown'] <= 2){
		$pagevars['countdown'] = convertSecondsToHumanReadable($endDate->getTimestamp() - time()) . ' remain';
	}else{
		$pagevars['countdown'] = $pagevars['countdown'] . ' days remain';
	}
	$pagevars['countdown'] = 'Ends ' . $endDate->format('jS F, Y') . ' - ' . $pagevars['countdown'];
}else{
	$pagevars['countdown'] = 'N/A';
}

$pagevars['scores'] = $pagevars['season']->getSeasonRanking();

if($pagevars['seasonexpired'] === false){
	$pagevars['game'] = new Game($gameid, $pagevars['season']->id);
	$pagevars['gamedate'] = $pagevars['game']->printDate();
	$pagevars['gamecount'] = $pagevars['game']->getGameNumber();
}elseif(!empty($pagevars['scores'])){
	$pagevars['scores'] = current($pagevars['scores']);
}
?>