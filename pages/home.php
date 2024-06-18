<?php
$pagevars['quote'] = new Quote;
$pagevars['quote'] = $pagevars['quote']->getRandomQuote();

$pagevars['season'] = new Season(null, $gameid);
if(!empty($pagevars['season']->endDate)){
	$pagevars['countdown'] = floor((strtotime($pagevars['season']->endDate) - time()) / 86400);
	if($pagevars['countdown'] <= 2){
		$pagevars['countdown'] = convertSecondsToHumanReadable(strtotime($pagevars['season']->endDate) - time()) . ' remain';
	}else{
		$pagevars['countdown'] = $pagevars['countdown'] . ' days remain';
	}
}else{
	$pagevars['countdown'] = 'N/A';
}
$pagevars['scores'] = $pagevars['season']->getSeasonRanking();

$pagevars['game'] = new Game($gameid);
$pagevars['gamedate'] = $pagevars['game']->printDate();
$pagevars['gamecount'] = $pagevars['game']->getGameNumber();
?>