<?php
$title = 'Global Stats';
$season = new Season($pagevars['seasonid']);
if(!empty($pagevars['seasonid'])){
	$title = $season->name . ' Stats';
}
$pagevars['seasons'] = $season->getAllSeasons();
foreach($pagevars['seasons'] as $skey => $sval){
	$pagevars['seasons'][$skey]->startDate = date('jS F, Y', strtotime($pagevars['seasons'][$skey]->startDate));
	$pagevars['seasons'][$skey]->endDate = date('jS F, Y', strtotime($pagevars['seasons'][$skey]->endDate));
}
$pagevars['page_title'] = $title;
$stats = new Stats($pagevars['seasonid']);
$pagevars['statgroups'] = $stats->getStats();
?>