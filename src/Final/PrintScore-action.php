<?php
require_once(dirname(__FILE__, 2) . '/config.php');
require_once('Common/Lib/CommonLib.php');

$Json=array('error'=>true, 'schedule'=>array());
if(!(CheckTourSession() AND checkACL((empty($_REQUEST["indTeam"]) ? AclIndividuals : AclTeams), AclReadOnly, false))) {
    JsonOut($Json);
    die();
}

$options=array('Type'=>array($_REQUEST["indTeam"]));
if($_REQUEST['today']) {
    $options['OnlyToday']=true;
}
if($_REQUEST['unfinished']) {
    $options['Unfinished']=true;
}
foreach(getApiScheduledSessions($options) as $r) {
    $Json['schedule'][$r->keyValue] = array(
        'key'=>$r->keyValue,
        'value'=>$r->Description,
        'type'=>(empty($_REQUEST["indTeam"]) ? 'I' : 'T')
    );
}
$Json['error']=0;

JsonOut($Json);