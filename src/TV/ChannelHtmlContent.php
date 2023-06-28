<?php

if(empty($_REQUEST['id'])) die();

$Channel=intval($_REQUEST['id']);
$Side=intval($_REQUEST['side']??0);

require_once(dirname(dirname(__FILE__)) . '/config.php');

$NOSTYLE=true;

$q=safe_r_sql("SELECT TVOId , TVOName, TVOUrl, TVOMessage, TVORuleId, TVOTourCode, TVORuleType
		FROM TVOut
		where TVORuleType>0 and TVOId=$Channel and TVOSide=$Side");
$r=safe_fetch($q);


include('Common/Templates/head-caspar.php');

echo $r->TVOMessage;

include('Common/Templates/tail-min.php');
