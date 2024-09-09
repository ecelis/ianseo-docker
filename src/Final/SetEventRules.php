<?php
define('debug',false);	// settare a true per l'output di debug

global $CFG;

require_once(dirname(__DIR__) . '/config.php');

$TEAM=intval($_REQUEST['team']??0);
$EVENT=$_REQUEST['event']??'';

if (!CheckTourSession()) {
	printCrackError();
}

checkACL(AclCompetition, AclReadWrite);
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Various.inc.php');

$JS_SCRIPT=array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/SetEventRules.js"></script>',
	'<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
	phpVars2js([
		'orgTeam'=>$TEAM,
		'orgEvent'=>$EVENT,
		'msgNotEmpty'=>get_text('DivClasNumberNotEmpty', 'Errors'),
		]),
	'<link href="./ListEvents.css" rel="stylesheet" type="text/css">',
    );


$IncludeFA=true;
$IncludeJquery=true;

$Select = "SELECT * FROM Divisions
	WHERE DivTournament = {$_SESSION['TourId']} AND DivAthlete=1
	ORDER BY DivViewOrder ";
$RsSel = safe_r_sql($Select);
$ComboDiv = '<select class="w-90" id="EcDivision" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
while ($Row=safe_fetch($RsSel)) {
	$ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . ' - ' . $Row->DivDescription . '</option>';
}
$ComboDiv.= '</select>';

$Select = "SELECT * FROM Classes
	WHERE ClTournament = {$_SESSION['TourId']} AND ClAthlete=1 
	ORDER BY ClViewOrder ";
$RsSel = safe_r_sql($Select);
$ComboCl = '<select class="w-90" id="EcClass" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
while ($Row=safe_fetch($RsSel)) {
	$ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . ' - ' . $Row->ClDescription . '</option>';
}
$ComboCl.= '</select>';

$Select = "SELECT ScId, ScDescription, ScViewOrder FROM SubClass
	WHERE ScTournament = {$_SESSION['TourId']}
	ORDER BY ScViewOrder ";
$RsSel = safe_r_sql($Select);
$ComboSubCl = '<select class="w-90" id="EcSubClass" multiple="multiple" disabled="disabled" size="'.min(15,safe_num_rows($RsSel)+2).'">';
while ($Row=safe_fetch($RsSel)) {
	$ComboSubCl.= '<option value="' . $Row->ScId . '">' . $Row->ScId  . ' - ' . $Row->ScDescription . '</option>';
}
$ComboSubCl.= '</select>';

include('Common/Templates/head.php');

if(!$EVENT) {
	echo get_text('GenericError');
	include('Common/Templates/tail.php');
	die();
}

$Select = "SELECT * FROM Events WHERE EvCode=" . StrSafe_DB($EVENT) . " AND EvTeamEvent=$TEAM AND EvTournament={$_SESSION['TourId']}";
$RsEv = safe_r_sql($Select);
$RowEv=safe_fetch($RsEv);
if(!$RowEv) {
	echo get_text('GenericError');
	include('Common/Templates/tail.php');
	die();
}

$ColSpan=($TEAM ? 6 : 4);

echo '<div align="center">';
echo '<div class="medium">';
echo '<table class="Tabella" id="MyTable">';
echo '<tr><th class="Title" colspan="'.$ColSpan.'">'. get_text('EventClass') . '</th></tr>';
echo '<tr class="Divider"><td colspan="'.$ColSpan.'"></td></tr>';


echo '<tr><td class="Title" colspan="'.$ColSpan.'">'.get_text($TEAM ? 'Team' : 'Individual').'</td></tr>';
echo '<tr><td class="Title" colspan="'.$ColSpan.'">'.get_text($RowEv->EvEventName,'','',true).'</td></tr>';
echo '<tr>';
if($TEAM) {
	echo '<th class="w-20">'.get_text('Number').'</th>';
}
echo '<th class="w-20">'.get_text('Division').'</th>';
echo '<th class="w-20">'.get_text('Class').'</th>';
echo '<th class="w-20">'.get_text('SubClass','Tournament').'</th>';
echo '<th class="w-10">&nbsp;</th>';
if($TEAM) {
	echo '<th class="w-10">&nbsp;</th>';
}
echo '</tr>';

$TeamFilter=($TEAM ? "!=0" : "=0");
$Select = "SELECT ec.*, Quanti FROM EventClass AS ec 
    INNER JOIN(
    	SELECT COUNT(*) AS Quanti,EcCode,EcTeamEvent,EcTournament
    	FROM EventClass
    	WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent $TeamFilter AND EcTournament= {$_SESSION['TourId']}
    	GROUP BY EcCode,EcTeamEvent,EcTournament
    	) AS sq ON ec.EcCode=sq.EcCode AND ec.EcTeamEvent=sq.EcTeamEvent AND ec.EcTournament=sq.EcTournament 
    WHERE ec.EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND ec.EcTeamEvent $TeamFilter AND ec.EcTournament={$_SESSION['TourId']} 
    ORDER BY ec.EcTeamEvent, ec.EcDivision, ec.EcClass, ec.EcSubClass ";
$Rs=safe_r_sql($Select);
$MyGroup='-1';

echo '<tbody id="tbody">';
while ($MyRow=safe_fetch($Rs)) {
	if ($MyGroup!=$MyRow->EcTeamEvent AND $MyGroup!=-1) {
		echo '<tr class="Divider"><td colspan="'.$ColSpan.'"></td></tr>';
	}

    echo '<tr t="'.$MyRow->EcTeamEvent.'" d="' . $MyRow->EcDivision . '" c="' . $MyRow->EcClass . '" sc="' . $MyRow->EcSubClass . '">';
	if ($TEAM and $MyGroup!=$MyRow->EcTeamEvent) {
		echo '<td rowspan="' . $MyRow->Quanti . '" class="Center">' . $MyRow->EcNumber . '</td>';
	}
	echo '<td class="Center">' . $MyRow->EcDivision . '</td>';
	echo '<td class="Center">' . $MyRow->EcClass . '</td>';
	echo '<td class="Center">' . $MyRow->EcSubClass . '</td>';
    echo '<td class="Center"><i class="far fa-lg fa-trash-can text-danger" title="Delete" onclick="DeleteEventRule(this)"></i></td>';
	if ($TEAM and $MyGroup!=$MyRow->EcTeamEvent) {
	    echo '<td rowspan="' . $MyRow->Quanti . '" class="Center"><i class="far fa-lg fa-trash-can text-danger" title="Delete" onclick="DeleteEventRuleTot(this)"></i></td>';
	}

	echo '</tr>';
	$MyGroup=$MyRow->EcTeamEvent;
}
echo '</tbody>';

if($TEAM) {
	echo '<tr class="Divider"><th colspan="6"></th></tr>';
	echo '<tr>';
	echo '<td class="Right">'.get_text('MixedTeamEvent').'</td>';
	echo '<td class="Left" colspan="5">
		<select id="EvMixedTeam" onChange="UpdateData(this)">
		<option value="0" '.(($RowEv!=null AND $RowEv->EvMixedTeam==0) ? 'selected="selected"' : '').'>'.get_text('No').'</option>
		<option value="1" '.(($RowEv!=null AND $RowEv->EvMixedTeam==1) ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
		</select>
		</td>';
	echo '</tr>';

	echo '<tr class="Divider"><th colspan="6"></th></tr>';

	echo '<tr>';
	echo '<td class="Right w-20">'.get_text('AllowMultiTeam').'</td>';
	echo '<td class="Left">
		<select id="EvMultiTeam" onChange="UpdateData(this)">
		<option value="0" '.(($RowEv!=null AND $RowEv->EvMultiTeam==0) ? 'selected="selected"' : '').'>'.get_text('No').'</option>
		<option value="1" '.(($RowEv!=null AND $RowEv->EvMultiTeam==1) ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
		</select>
		</td>';
	echo '<td class="Right w-20" colspan="2">'.get_text('MultiTeamMaxNo').'</td>';
	echo '<td class="Left" colspan="2">
		<input type="number" step="1" min="0" max="999" id="EvMultiTeamNo"  onChange="UpdateData(this)" value="'.($RowEv->EvMultiTeamNo??0).'">
		</td>';
	echo '</tr>';

	$comboTeamCreationMode=ComboFromRs(
		array(
			array('id'=>0,'descr'=>get_text('TeamCreationMode_0','Tournament')),
			array('id'=>1,'descr'=>get_text('TeamCreationMode_1','Tournament')),
			array('id'=>2,'descr'=>get_text('TeamCreationMode_2','Tournament')),
			array('id'=>3,'descr'=>get_text('TeamCreationMode_3','Tournament')),
		),
		'id',
		'descr',
		1,
		$RowEv->EvTeamCreationMode,
		null,
		'EvTeamCreationMode',
		null,
		array(
			'onChange'=>'UpdateData(this)'
		)
	);
	echo '<tr>';
	echo '<td class="Right w-20">'.get_text('TeamCreationMode','Tournament').'</td>';
	echo '<td class="Left" colspan="2">'.$comboTeamCreationMode.'</td>';
	echo '<td class="Right w-20">'.get_text('AllowPartialTeams').'</td>';
	echo '<td class="Left" colspan="2">
		<select id="EvPartialTeam" onChange="UpdateData(this)">
		<option value="0" '.(($RowEv!=null AND $RowEv->EvPartialTeam==0) ? 'selected="selected"' : '').'>'.get_text('No').'</option>
		<option value="1" '.(($RowEv!=null AND $RowEv->EvPartialTeam==1) ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
		</select>
		</td>';
	echo '</tr>';
} else {
    $comboTeamCreationMode=ComboFromRs(
        array(
            array('id'=>0,'descr'=>get_text('SelectedClub-0','Tournament')),
            array('id'=>1,'descr'=>get_text('SelectedClub-1','Tournament')),
            array('id'=>2,'descr'=>get_text('SelectedClub-2','Tournament')),
        ),
        'id',
        'descr',
        1,
        $RowEv->EvTeamCreationMode,
        null,
        'EvTeamCreationMode',
        null,
        array(
            'onChange'=>'UpdateData(this)'
        )
    );
    echo '<tr>';
    echo '<td class="Center" colspan="4">'.$comboTeamCreationMode.'</td>';
    echo '</tr>';

}

print '<tr id="RowDiv" class="Divider"><td colspan="'.$ColSpan.'"></td></tr>';
echo '</table>';

// New subrule
echo '<br/>';
echo '<table class="Tabella">';
echo '<tr><td colspan="'.$ColSpan.'" class="Center">'.get_text('PressCtrl2SelectAll').'</td></tr>';
echo '<tr id="NewRow">';
if($TEAM) {
	echo '<td class="Center Top w-20"><input type="number" step="1" min="0" max="999" id="EcNumber"  value=""></td>';
} else {
	echo '<input type="hidden" id="EcNumber" value="1">';
}

echo '<td class="Center Top w-20">'.$ComboDiv.'<br/><br/><div class="Link" onclick="SelectAll(this)">'.get_text('SelectAll').'</div></td>';
echo '<td class="Center Top w-20">'.$ComboCl.'<br/><br/><div class="Link" onclick="SelectAll(this)">'.get_text('SelectAll').'</div></td>';
echo '<td class="Center Top w-20">'.$ComboSubCl.'<br/><br/><input type="checkbox" onclick="enableSubclass(this)">'.get_text('UseSubClasses','Tournament').'</td>';
echo '<td class="Center Top w-10"><div class="Button" onclick="AddEventRule()">'.get_text('CmdSave').'</div></td>';
echo '</tr>';
echo '</table>';

echo '<br/>';
echo '<table class="Tabella">';
echo '<tr id="AdvancedButton"><th colspan="'.$ColSpan.'"><input type="button" onclick="showAdvanced()" value="'.get_text('Advanced').'"></th></tr>';
echo '<tbody id="Advanced" class="d-none">';
echo '<tr>';
echo '<th>'.get_text('EventNumQualified', 'Tournament').'</th>';
echo '<th>'.get_text('EventStartPosition', 'Tournament').'</th>';
echo '<th>'.get_text('EventHasMedal', 'Tournament').'</th>';
echo '<th>'.get_text('EventParentCode', 'Tournament').'</th>';
echo '<th>'.get_text('EventWinnerFinalRank', 'Tournament').'</th>';
if($TEAM) {
	echo '<th>'.get_text('MaxTeamPersons', 'Tournament').'</th>';
}
echo '<th>'.get_text('WaCategory', 'Tournament').'</th>';
echo '<th>'.get_text('RecordCategory', 'Tournament').'</th>';
echo '<th>'.get_text('OdfEventCode', 'ODF').'</th>';
echo '</tr>';

echo '<tr>';
echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvNumQualified.'" id="EvNumQualified" onchange="UpdateData(this)"></td>';
echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvFirstQualified.'" id="EvFirstQualified" onchange="UpdateData(this)"></td>';
echo '<td class="Center"><select onchange="UpdateData(this)" id="EvMedals">
        <option value="1" '.($RowEv->EvMedals ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>
        <option value="0" '.($RowEv->EvMedals ? '' : ' selected="selected"').'>'.get_text('No').'</option>
    </select></td>';
echo '<td class="Center"><select onchange="UpdateData(this)" id="EvCodeParent">';
echo '<option value="">'.get_text('Select', 'Tournament').'</option>';
$q=safe_r_sql("select EvCode, EvEventName from Events where EvTeamEvent=$TEAM and EvFinalFirstPhase>$RowEv->EvFinalFirstPhase and EvCode!='$RowEv->EvCode' and EvTournament={$_SESSION['TourId']}");
while($r=safe_fetch($q)) {
    echo '<option value="'.$r->EvCode.'" '.($RowEv->EvCodeParent==$r->EvCode ? ' selected="selected"' : '').'>'.$r->EvCode.' - '.$r->EvEventName.'</option>';
}
echo '</select></td>';
echo '<td class="Center"><input min="0" max="9999" type="number" value="'.$RowEv->EvWinnerFinalRank.'" id="EvWinnerFinalRank" onchange="UpdateData(this)"></td>';
if($TEAM) {
	echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvMaxTeamPerson.'" id="EvMaxTeamPerson" onchange="UpdateData(this)"></td>';
}
echo '<td class="Center"><input type="text" size="12" maxlength="10" value="'.$RowEv->EvWaCategory.'" id="EvWaCategory" onchange="UpdateData(this)"></td>';
echo '<td class="Center"><input size="12" maxlength="10" type="text" value="'.$RowEv->EvRecCategory.'" id="EvRecCategory" onchange="UpdateData(this)"></td>';
echo '<td class="Center"><input type="text" value="'.$RowEv->EvOdfCode.'" id="EvOdfCode" onchange="UpdateData(this)"></td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';

echo '<br/>';
echo '<table class="Tabella">';
echo '<tr><td class="Center"><a class="Link" href="ListEvents.php">'.get_text('Back').'</a></td></tr>';
echo '</table>';
echo '</div>';
echo '</div>';

include('Common/Templates/tail.php');
