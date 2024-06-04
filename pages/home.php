<?php
$pagevars['quote'] = new Quote;
$pagevars['quote'] = $pagevars['quote']->getRandomQuote();

$pagevars['season'] = new Season;
$pagevars['countdown'] = ceil((strtotime($pagevars['season']->endDate) - time()) / 86400);
$pagevars['scores'] = $pagevars['season']->getSeasonRanking();

$pagevars['game'] = new Game;
$pagevars['gamedate'] = $pagevars['game']->printDate();
$pagevars['gamecount'] = $pagevars['game']->getGameNumber();
?>