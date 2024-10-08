<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
$IncludeJquery = true;
$JS_SCRIPT[]='<script src="./configure.js"></script>';
$JS_SCRIPT[]=phpVars2js(array(
	'MsgConfirm' => get_text('ConfirmDescr', 'Tournament'),
	'CmdCancel' => get_text('CmdCancel'),
	'CmdConfirm' => get_text('Confirm', 'Tournament'),
));

include('Common/Templates/head.php');


// foreach category sets the winnings of previous year
$Winners=array();
$Ranking=array();
$Teams=array();


$q=safe_r_sql("select EvCode, EvNumQualified
	from Events 
	where EvTeamEvent=1 and EvTournament={$_SESSION['TourId']} and EvNumQualified!=4
	order by EvProgr");
while($r=safe_fetch($q)) {
	foreach(range(1,$r->EvNumQualified) as $k) {
		$Winners[$r->EvCode][$k]='';
		$Bonus[$r->EvCode][$k]=0;
		$Teams[$r->EvCode]=$r->EvNumQualified;
	}
}

$SavedBonus=getModuleParameter('FFTA', 'D1Bonus', $Winners);
$SavedWinners=getModuleParameter('FFTA', 'D1Winners', $Winners);
$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);

if(!$SavedBonus) {
	$SavedBonus=$Winners;
}
if(!$SavedWinners) {
	$SavedWinners=$Winners;
}
setModuleParameter('FFTA', 'D1Bonus', $SavedBonus);
setModuleParameter('FFTA', 'D1Winners', $SavedWinners);

echo '<table class="Tabella" style="margin:auto;width:auto;margin-bottom:1em">';

if($_SESSION['TourLocSubRule']=='SetFRD12023') {
	$TourDates=getModuleParameter('FFTA', 'D1TourDates', ['D1' => [], 'D2' => [], 'D3' => []]);
	echo '<tr><th></th>
			<th>Date</th>
			<th>Début</th>
			<th>Lieu</th>
			</tr>';
	for($i=1;$i<4;$i++) {
		echo '<tr><th class="Title">'.get_text('FlightsDay','Tournament', $i).'</th>
			<td><input type="date" pos="'.$i.'" cat="date" item="TOURDATE" onblur="confUpdate(this)" value="'.($TourDates['D'.$i]['date'] ?? '').'"></td>
			<td><input type="text" pos="'.$i.'" cat="time" item="TOURDATE" onblur="confUpdate(this)" size="10" value="'.($TourDates['D'.$i]['time'] ?? '').'"></td>
			<td><input type="text" pos="'.$i.'" cat="comp" item="TOURDATE" onblur="confUpdate(this)" size="30" value="'.($TourDates['D'.$i]['comp'] ?? '').'"></td>
			</tr>';
	}

} else {
	echo '<tr><th class="Title">'.get_text('ConnectedCodes','Tournament').'</th><td><input type="text" pos="" cat="" item="CONNECTED" onblur="confUpdate(this)" size="30" value="'.implode(', ', getModuleParameter('FFTA', 'ConnectedCompetitions', array($_SESSION['TourCode']))).'"></td></tr>';
	echo '<tr><th class="Title">'.get_text('D1AllInOne','Tournament').'</th><td><input type="checkbox" pos="" cat="" item="ALLONE" onclick="alertUpdate(this)" '.(getModuleParameter('FFTA', 'D1AllInOne', 0) ? ' checked="checked"' : '' ).'></td></tr>';
	if(!$AllInOne) {
		echo '<tr><th class="Title">'.get_text('StdIndMatchLength','Tournament').'</th><td><input type="text" pos="" cat="" item="DEFIND" onblur="confUpdate(this)" size="10" value="'.getModuleParameter('FFTA', 'DefaultMatchIndividual', 40).'"></td></tr>';
	}
}
// default match duration
echo '<tr><th class="Title">'.get_text('StdTeamMatchLength','Tournament').'</th><td colspan="3"><input type="text" pos="" cat="" item="DEFTEAM" onblur="confUpdate(this)" size="10" value="'.getModuleParameter('FFTA', 'DefaultMatchTeam', 30).'"></td></tr>';
echo '</table>';

echo '<table class="Tabella" style="margin:auto;width:auto">';

if($_SESSION['TourLocSubRule']=='SetFRD12023') {
	echo '<tr>';
	echo '<th class="Title"></th>';
	foreach($Teams as $Cat => $Rank) {
		echo '<th class="Title">' . $Cat . '</th>';
	}
	echo '</tr>';

	foreach(range(1, max($Teams)) as $pos) {
		echo '<tr><th class="Title">'.$pos.'</th>';
		foreach($Teams as $Cat => $MaxRows) {
			if($pos<=$MaxRows) {
				echo '<td><input class="w-100" type="text" pos="'.$pos.'" cat="'.$Cat.'" item="CLUB" onblur="confUpdate(this)" value="'.(isset($SavedWinners[$Cat][$pos]) ? $SavedWinners[$Cat][$pos] : '').'" size="10"></td>';
			} else {
				echo '<td></td>';
			}
		}
		echo '</tr>';
	}
	// list load of teams
	echo '<tr><th class="Title"></th>';
	foreach($Teams as $Cat => $MaxRows) {
		echo '<td><textarea cat="'.$Cat.'" item="ALLCLUBS" onblur="confUpdate(this)" style="width:10em;height:15em;"></textarea></td>';
	}
	echo '</tr>';

} else {
	$Heading2='<tr>';
	echo '<tr>';
	echo '<th class="Title" rowspan="2"></th>';
	foreach($Teams as $Cat => $Rank) {
		echo '<th class="Title" colspan="'.($AllInOne ? 1 : 2).'">' . $Cat . '</th>';
		$Heading2.='<th class="Title">'.get_text('RankYear', 'Tournament', substr($_SESSION['TourRealWhenFrom'], 0, 4)-1).'</th>';
		if(!$AllInOne) {
			$Heading2 .= '<th class="Title">' . get_text('Bonus', 'Tournament') . '</th>';
		}
	}
	echo '</tr>';
	echo $Heading2.'</tr>';

	foreach(range(1, max($Teams)) as $pos) {
		echo '<tr><th class="Title">'.$pos.'</th>';
		foreach($Teams as $Cat => $MaxRows) {
			if($pos<=$MaxRows) {
				echo '<td><input type="text" pos="'.$pos.'" cat="'.$Cat.'" item="CLUB" onblur="confUpdate(this)" value="'.(isset($SavedWinners[$Cat][$pos]) ? $SavedWinners[$Cat][$pos] : '').'" size="10"></td>';
				if(!$AllInOne) {
					echo '<td><input type="text" pos="'.$pos.'" cat="'.$Cat.'" item="BONUS" onblur="confUpdate(this)" value="'.(isset($SavedBonus[$Cat][$pos]) ? intval($SavedBonus[$Cat][$pos]) : 0).'" size="3"></td>';
				}
			} else {
				echo '<td></td>';
				if(!$AllInOne) {
					echo '<td></td>';
				}
			}
		}
		echo '</tr>';
	}
}
echo '</table>';

include('Common/Templates/tail.php');
