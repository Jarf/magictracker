<?php
$pagevars['quote'] = new Quote;
$pagevars['quote'] = $pagevars['quote']->getRandomQuote();
$pagevars['seasonexpired'] = false;
$pagevars['season'] = new Season(null, $gameid);
if(empty($pagevars['season']->id)){
	$pagevars['season']->getLatestSeason();
	$pagevars['seasonexpired'] = true;
}
if(!empty($pagevars['season']->endDate)){
	$pagevars['countdown'] = floor((strtotime($pagevars['season']->endDate) - time()) / 86400);
	if($pagevars['countdown'] <= 2){
		$endDate = DateTime::createFromFormat('Y-m-d H:i:s', $pagevars['season']->endDate);
		$pagevars['countdown'] = convertSecondsToHumanReadable($endDate->getTimestamp() - time()) . ' remain';
	}else{
		$pagevars['countdown'] = $pagevars['countdown'] . ' days remain';
	}
}else{
	$pagevars['countdown'] = 'N/A';
}
$pagevars['scores'] = $pagevars['season']->getSeasonRanking();

if($pagevars['seasonexpired'] === false){
	$pagevars['game'] = new Game($gameid);
	$pagevars['gamedate'] = $pagevars['game']->printDate();
	$pagevars['gamecount'] = $pagevars['game']->getGameNumber();
}else{
	$pagevars['scores'] = current($pagevars['scores']);
}
?>