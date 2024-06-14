<?php
$title = 'Global Stats';
if(!empty($pagevars['seasonid'])){
	$season = new Season($pagevars['seasonid']);
	$title = $season->name . ' Stats';
}
$pagevars['page_title'] = $title;
$stats = new Stats($pagevars['seasonid']);
$pagevars['statgroups'] = $stats->getStats();
?>