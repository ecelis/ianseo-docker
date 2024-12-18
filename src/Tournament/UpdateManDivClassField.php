<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

define('debug',false);

require_once(dirname(dirname(__FILE__)) . '/config.php');
$JSON=array('error' => 1);

/*
	- $Arr_Tables
	Array of tables
	each key corresponds of an array where 1st item is the table and the following are the keys for the update
*/
$Arr_Tables = array(
	'D' => array('Divisions','DivId','DivTournament','','DivTourRules'),
	'C' => array('Classes','ClId','ClTournament','','ClTourRules'),
	'SC'=> array('SubClass','ScId','ScTournament','','')
	);

if(checkACL(AclCompetition, AclReadWrite, false)!=AclReadWrite
		or !CheckTourSession()
		or IsBlocked(BIT_BLOCK_TOURDATA)
		or defined('dontEditClassDiv')
		or empty($_REQUEST['Tab'])
		or empty($_REQUEST['Field'])
		or empty($_REQUEST['Id'])
		or !isset($_REQUEST['Value'])
		or !array_key_exists($_REQUEST['Tab'],$Arr_Tables)
		) {
	JsonOut($JSON);
}

$Id=$_REQUEST['Id'];
$Field=$_REQUEST['Field'];
$Value=$_REQUEST['Value'];

switch($Field) {
    case 'DivDescription':
    case 'DivIsPara':
    case 'DivAthlete':
    case 'DivViewOrder':
    case 'ClSex':
    case 'ClDescription':
    case 'ClIsPara':
    case 'ClAthlete':
    case 'ClViewOrder':
    case 'ScDescription':
    case 'ScViewOrder':
//    case '':
//    case '':
//    case '':
//    case '':
        break;
    default:
        JsonOut($JSON);
}

$tt=$Arr_Tables[$_REQUEST['Tab']][0];	// tabella su cui fare l'update
$kk=$Arr_Tables[$_REQUEST['Tab']][1];	// campo 1 da usare come chiave per l'update
$kk2=$Arr_Tables[$_REQUEST['Tab']][2];	// campo 2 da usare come chiave per l'update
$kk3=$Arr_Tables[$_REQUEST['Tab']][3];	// campo 3 da usare come chiave per l'update
$kk4=$Arr_Tables[$_REQUEST['Tab']][4];	// campo 4 per resettare info del default

$ClDivAllowed='';

//$tmp=explode('_',$Key);
//$Field=$tmp[1];
//$Id=$tmp[2];
//if(!empty($tmp[3])) $ClDivAllowed=$tmp[3];

$Update
	= "UPDATE " . $tt . " SET "
	. $Field . "=" . StrSafe_DB($Value) . " "
	. "WHERE " . $kk . "=" . StrSafe_DB($Id)
	. " AND " . $kk2 . "=" . StrSafe_DB($_SESSION['TourId']) . " "
	. ($kk3 ? " AND $kk3 = ".StrSafe_DB($ClDivAllowed) : '');
$Rs=safe_w_sql($Update);
if(safe_w_affected_rows() and (($Field=='ClAthlete' or $Field=='DivAthlete'))) {
	safe_w_sql("UPDATE {$tt} SET {$kk4}='' WHERE {$kk}=" . StrSafe_DB($Id) . " AND {$kk2}=" . StrSafe_DB($_SESSION['TourId']) . ($kk3 ? " AND $kk3 = ".StrSafe_DB($ClDivAllowed) : ''));
	// avvenuto un cambio di status di atleta!!!
	if($Value) {
		if($Field=='ClAthlete') safe_w_sql("Update Entries left join Divisions on EnTournament=DivTournament and EnDivision=DivId set EnAthlete=DivAthlete+0 where EnTournament={$_SESSION['TourId']} and EnClass='$Id'");
		elseif($Field=='DivAthlete') safe_w_sql("Update Entries left join Classes on EnTournament=ClTournament and EnClass=ClId set EnAthlete=ClAthlete+0 where EnTournament={$_SESSION['TourId']} and EnDivision='$Id'");
	} else {
		if($Field=='ClAthlete') safe_w_sql("Update Entries set EnAthlete=0 where EnTournament={$_SESSION['TourId']} and EnClass='$Id'");
		elseif($Field=='DivAthlete') safe_w_sql("Update Entries set EnAthlete=0 where EnTournament={$_SESSION['TourId']} and EnDivision='$Id'");
	}
}

$JSON['error']=0;
$JSON['value']=$Value;

JsonOut($JSON);
