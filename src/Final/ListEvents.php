<?php
global $CFG;
$IncludeJquery=true;
$IncludeFA=true;

require_once(dirname(__DIR__) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Various.inc.php');

$IsRunArchery=($_SESSION['TourType']==48);

$Elim=0;
if($IsRunArchery) {
	$NumCols=15;
} else {
	$NumCols=10;
	$Select = "SELECT ToElimination AS TtElimination FROM Tournament WHERE ToId={$_SESSION['TourId']}";
	$RsElim=safe_r_sql($Select);

	if ($Row=safe_fetch($RsElim)) {
		$NumCols+=$Row->TtElimination;
	    $Elim=$Row->TtElimination;
	}
}


$JS_SCRIPT=array(
    phpVars2js(array(
        'StrResetElimError' => get_text('ResetElimError', 'Tournament'),
        'MsgRowMustBeComplete' => str_replace('<br>','\n',get_text('MsgRowMustBeComplete')),
	    'MsgAreYouSure' => get_text('MsgAreYouSure'),
	    'btnCancel' => get_text('CmdCancel'),
	    'btnConfirm' => get_text('Confirm', 'Tournament'),
        )),
    '<script type="text/javascript" src="./ListEvents.js"></script>',
    '<script type="text/javascript" src="../Common/js/Fun_JS.inc.js"></script>',
	'<link href="./ListEvents.css" rel="stylesheet" type="text/css">',
    );

include('Common/Templates/head.php');


// start with the Individual events
echo '<table class="Tabella">';

echo '<thead>';
echo '<tr><th class="Title" colspan="'.($NumCols).'">'.get_text('IndEventList').'</th></tr>';
echo '<tr class="Divider"><td colspan="'.($NumCols).'"></td></tr>';
echo '<tr>
	<th class="w-5">'.get_text('EvCode').'</th>
	<th class="w-25">'.get_text('EvName').'</th>
	<th class="w-5">'.get_text('Progr').'</th>';

if($IsRunArchery) {
	// Run archery has a completely different set of features...
	echo '<th>'.get_text('QualStartList','RunArchery').'</th>';
	echo '<th>'.get_text('LapLength','RunArchery').'</th>';
	echo '<th>'.get_text('LapsNumber','RunArchery').'</th>';
	echo '<th>'.get_text('TargetToHit','RunArchery').'</th>';
	echo '<th>'.get_text('NumArrows','RunArchery').'</th>';
	echo '<th>'.get_text('PenaltyLoopLength','RunArchery').'</th>';
	echo '<th>'.get_text('ArrowPenaltyTime','RunArchery').'</th>';
	echo '<th>'.get_text('LoopPenaltyTime','RunArchery').'</th>';
	echo '<th>'.get_text('CompetitionRounds','RunArchery').'</th>';
	echo '<th>'.get_text('QualifiedSemi','RunArchery').'</th>';
	echo '<th>'.get_text('QualifiedFinal','RunArchery').'</th>';
} else {
    echo '<th class="w-5">'.get_text('Para', 'Records').'</th>';
    if($Elim) {
        echo '<th class="w-20">' . get_text('Elimination') . '</th>';
    }
    echo '<th class="w-10">'.get_text('MatchModeScoring').'</th>
        <th class="w-10">'.get_text('FirstPhase').'</th>
        <th class="w-10">'.get_text('TargetType').'</th>
        <th class="w-5">ø (cm)</th>
        <th class="w-5">'.get_text('Distance', 'Tournament').'</th>';
}
echo '<th class="w-5">&nbsp;</th>
	</tr>';
echo '</thead>';

$ComboPhase = array();
$Sql= "SELECT PhId FROM Phases WHERE (PhIndTeam & 1)!=0 AND PhId>1 and PhRuleSets in ('', '{$_SESSION['TourLocRule']}') Order by PhId DESC ";
$q=safe_r_sql($Sql);
while($r=safe_fetch($q)) {
    $ComboPhase[$r->PhId] = get_text($r->PhId . '_Phase');
}
$ComboPhase[0]='---';

$ComboMatchMode = array();
for($i=0; $i<=1; $i++) {
    $ComboMatchMode[$i]=get_text('MatchMode_' . $i);
}

$ComboTarget = array();

$Select = "SELECT * FROM Targets ORDER BY TarOrder ";
$RsT=safe_r_sql($Select);
while ($Row=safe_fetch($RsT)) {
    $ComboTarget[$Row->TarId]=get_text($Row->TarDescr);
}

$Select = "SELECT * FROM Events 
    LEFT JOIN Targets ON EvFinalTargetType=TarId 
    LEFT JOIN (SELECT EcCode, COUNT(*) as ruleCnt FROM EventClass WHERE EcTeamEvent=0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY EcCode) as sqy on EvCode=EcCode
    WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0'
    ORDER BY EvProgr, EvCode, EvTeamEvent";
$Rs=safe_r_sql($Select);

echo '<tbody id="IndTable">';
while ($MyRow=safe_fetch($Rs)) {
    print '<tr team="0" ref="' . $MyRow->EvCode . '" class="rowHover">';
    print '<td class="Center">
        <input type="button" ' . ($MyRow->ruleCnt == 0 ? 'class="red"' : '') . ' value="'.$MyRow->EvCode .'" onclick="SetEventRules(this)">
        </td>';

    print '<td class="Center">
        <input type="text" size="50" maxlength="64" name="EvEventName" value="' . $MyRow->EvEventName . '" onBlur="UpdateField(this)">
        </td>';

    print '<td class="Center">
        <input type="text" size="3" maxlength="3" name="EvProgr" value="' . $MyRow->EvProgr . '" onBlur="UpdateField(this)">
        </td>';

    if($IsRunArchery) {
		echo '<td class="Center"><select onchange="UpdateField(this)" name="EvElimType">
				<option value="0" '.($MyRow->EvElimType==0 ? ' selected="selected"' : '').'>'.get_text('GroupedStart','RunArchery').'</option>
				<option value="1" '.($MyRow->EvElimType==1 ? ' selected="selected"' : '').'>'.get_text('SingleStart','RunArchery').'</option>
			</select></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="4" value="'.$MyRow->EvTargetSize.'" name="EvTargetSize"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvFinEnds.'" name="EvFinEnds"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvE1Arrows.'" name="EvE1Arrows"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvFinArrows.'" name="EvFinArrows"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvDistance.'" name="EvDistance"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvArrowPenalty.'" name="EvArrowPenalty"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvLoopPenalty.'" name="EvLoopPenalty"></td>';
		echo '<td class="Center"><select name="EvFinalFirstPhase" onchange="UpdateField(this)">
			<option value="0"'.($MyRow->EvFinalFirstPhase==='0' ? ' selected="selected""' : '').'>'.get_text('SingleRound','RunArchery').'</option>
			<option value="1"'.($MyRow->EvFinalFirstPhase==='1' ? ' selected="selected""' : '').'>'.get_text('WithFinalsRounds','RunArchery').'</option>
			<option value="2"'.($MyRow->EvFinalFirstPhase==='2' ? ' selected="selected""' : '').'>'.get_text('WithSemiRounds','RunArchery').'</option>
			</select></td>';
	    echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvElim1.'" name="EvElim1"'.($MyRow->EvFinalFirstPhase!=2 ? ' disabled="disabled"' : '').'></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvElim2.'" name="EvElim2"'.($MyRow->EvFinalFirstPhase==0 ? ' disabled="disabled"' : '').'></td>';
    } else {
        print '<td class="Center">
            <input type="checkbox" name="EvIsPara" ' . ($MyRow->EvIsPara ? 'checked="checked"' : '') . ' onclick="UpdateField(this)">
            </td>';

        if($Elim) {
            print '<td class="Center">';
            echo '<div>';
            echo '<div><input type="button" value="'.htmlspecialchars(get_text('Edit', 'Tournament'), ENT_QUOTES).'" onclick="location=\'ListEvents-Eliminations.php?Event='.$MyRow->EvCode.'\'" /></div>';
            echo '<div>';
            if($MyRow->EvElimType == 1 OR $MyRow->EvElimType == 2) {
                if ($MyRow->EvElim1) {
                    echo '<div class="Flex-line">';
                    echo '<div><b>' . get_text('StageE1', 'ISK') . '</b></div>';
                    echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvE1Ends . '</div>';
                    echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvE1Arrows . '</div>';
                    echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvE1SO . '</div>';
                    echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim1 . '</div>';
                    echo '</div>';
                }
                if ($MyRow->EvElim2) {
                    echo '<div class="Flex-line">';
                    echo '<div><b>' . get_text('StageE2', 'ISK') . '</b></div>';
                    echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvE2Ends . '</div>';
                    echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvE2Arrows . '</div>';
                    echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvE2SO . '</div>';
                    echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim2 . '</div>';
                    echo '</div>';
                }
            } elseif($MyRow->EvElimType == 3) {
                echo '<div class="Flex-line">';
                echo '<div><b>' . get_text('StagePool2', 'ISK') . '</b></div>';
                echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvElimEnds . '</div>';
                echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvElimArrows . '</div>';
                echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvElimSO . '</div>';
                echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim2 . '</div>';
                echo '</div>';
            } elseif($MyRow->EvElimType == 4) {
                echo '<div class="Flex-line">';
                echo '<div><b>' . get_text('StagePool4', 'ISK') . '</b></div>';
                echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvElimEnds . '</div>';
                echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvElimArrows . '</div>';
                echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvElimSO . '</div>';
                echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim2 . '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            print '</td>';
        }

        print '<td class="Center">';
        print '<select name="EvMatchMode" onChange="UpdateField(this)">';
        foreach ($ComboMatchMode as $Key => $Value)
            print '<option value="' . $Key . '"' . ($Key==$MyRow->EvMatchMode ? ' selected' : '') . '>' . $Value . '</option>';
        print '</select>';
        print '</td>';

        print '<td class="Center">';
        print '<select name="EvFinalFirstPhase" onChange="UpdatePhase(this)">';
        foreach ($ComboPhase as $Key => $Value) {
			print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalFirstPhase ? ' selected' : '') . '>' . $Value . '</option>';
        }
        print '</select>';
        print '</td>';

        print '<td class="Center">';
        print '<select name="EvFinalTargetType" onChange="UpdateField(this)">';
        foreach ($ComboTarget as $Key => $Value) {
            print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalTargetType ? ' selected' : '') . '>' . $Value . '</option>';
        }
        print '</select>';
        print '</td>';

        print '<td class="Center">';
        print '<input type="text" size="3" maxlength="3" name="EvTargetSize" value="' . $MyRow->EvTargetSize . '" onBlur="UpdateField(this);">';
        print '</td>';

        print '<td class="Center">';
        print '<input type="text" size="12" maxlength="10" name="EvDistance" value="' . $MyRow->EvDistance . '" onBlur="UpdateField(this)">';
        print '</td>';
    }


    print '<td class="Center">';
    print '<i class="fa fa-lg fa-save text-success mr-3"></i>'; // does nothing, just forces user to click outside of the field!
    print '<i onclick="DeleteEvent(this)" class="far fa-lg fa-trash-can text-danger"></i>';
    print '</td>';
    print '</tr>';
}
echo '</tbody>';

echo '<tr class="Divider"><td colspan="'.$NumCols.'"></td></tr>';

// new event row
echo '<tr team="0" ref="NewRow">';
echo '<td class="Center"><input type="text" name="event" size="12" maxlength="10"></td>';
echo '<td class="Center"><input type="text" size="50" maxlength="64" name="EvEventName"></td>';
echo '<td class="Center"><input type="text" size="3" maxlength="3" name="EvProgr"></td>';
if($IsRunArchery) {
	echo '<td class="Center"><select onchange="UpdateField(this)" name="EvElimType">
				<option value="0">'.get_text('GroupedStart','RunArchery').'</option>
				<option value="1">'.get_text('SingleStart','RunArchery').'</option>
			</select></td>';
	echo '<td class="Center"><input type="number" size="4" name="EvTargetSize"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvFinEnds"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvE1Arrows"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvFinArrows"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvDistance"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvArrowPenalty"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvLoopPenalty"></td>';
	echo '<td class="Center"><select name="EvFinalFirstPhase">
			<option value="0">'.get_text('SingleRound','RunArchery').'</option>
			<option value="1">'.get_text('WithFinalsRounds','RunArchery').'</option>
			<option value="2">'.get_text('WithSemiRounds','RunArchery').'</option>
			</select></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvElim1" disabled="disabled"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvElim2" disabled="disabled"></td>';
} else {
	echo '<td class="Center"><input type="checkbox" name="EvIsPara"></td>';
	echo '<td class="Center">';
	echo '<select name="EvMatchMode">';
	foreach ($ComboMatchMode as $Key => $Value) {
	    print '<option value="' . $Key . '">' . $Value . '</option>';
	}
	echo '</select>';
	echo '</td>';
	echo '<td class="Center">';
	echo '<select name="EvFinalFirstPhase">';
	foreach ($ComboPhase as $Key => $Value) {
	    print '<option value="' . $Key . '">' . $Value . '</option>';
	}
	echo '</select>';
	echo '</td>';
	echo '<td class="Center">';
	echo '<select name="NewEvFinalTargetType">';
	foreach ($ComboTarget as $Key => $Value) {
	    print '<option value="' . $Key . '">' . $Value . '</option>';
	}
	echo '</select>';
	echo '</td>';
	echo '<td class="Center"><input type="text" name="EvTargetSize" size="3" maxlength="3"></td>';
	echo '<td class="Center"><input type="text" name="EvDistance" size="12" maxlength="10"></td>';
}
echo '<td class="Center">
    <div class="Button" onClick="AddEvent(this)">'.get_text('CmdSave').'</div>
    </td>';
echo '</tr>';
if($Elim) {
    echo '<tr><td colspan="' . ($NumCols) . '" class="Bold Center">' . get_text('ChangeElimWarning') . '</td></tr>';
}
echo '</table>';
echo '<div id="idOutput"></div>';

// followed by the Team events
echo '<table class="Tabella" id="TeamTable">';

echo '<thead>';
echo '<tr><th class="Title" colspan="'.($NumCols).'">'.get_text('TeamEventList').'</th></tr>';
echo '<tr class="Divider"><td colspan="'.($NumCols).'"></td></tr>';
echo '<tr>
	<th class="w-5">'.get_text('EvCode').'</th>
	<th class="w-25">'.get_text('EvName').'</th>
	<th class="w-5">'.get_text('Progr').'</th>';

if($IsRunArchery) {
	// Run archery has a completely different set of features...
	echo '<th>'.get_text('QualStartList','RunArchery').'</th>';
	echo '<th>'.get_text('LapLength','RunArchery').'</th>';
	echo '<th>'.get_text('LapsNumber','RunArchery').'</th>';
	echo '<th>'.get_text('TargetToHit','RunArchery').'</th>';
	echo '<th>'.get_text('NumArrows','RunArchery').'</th>';
	echo '<th>'.get_text('PenaltyLoopLength','RunArchery').'</th>';
	echo '<th>'.get_text('ArrowPenaltyTime','RunArchery').'</th>';
	echo '<th>'.get_text('LoopPenaltyTime','RunArchery').'</th>';
	echo '<th>'.get_text('CompetitionRounds','RunArchery').'</th>';
	echo '<th>'.get_text('QualifiedSemi','RunArchery').'</th>';
	echo '<th>'.get_text('QualifiedFinal','RunArchery').'</th>';
} else {
    echo '<th class="w-5">'.get_text('Para', 'Records').'</th>';
    if($Elim) {
        echo '<th class="w-20">' . get_text('Elimination') . '</th>';
    }
    echo '<th class="w-10">'.get_text('MatchModeScoring').'</th>
        <th class="w-10">'.get_text('FirstPhase').'</th>
        <th class="w-10">'.get_text('TargetType').'</th>
        <th class="w-5">ø (cm)</th>
        <th class="w-5">'.get_text('Distance', 'Tournament').'</th>';
}
echo '<th class="w-5">&nbsp;</th>
	</tr>';
echo '</thead>';

$ComboPhase = array();

$Sql= "SELECT PhId FROM Phases WHERE (PhIndTeam & 2)!=0 AND PhId>1 and PhRuleSets in ('', '{$_SESSION['TourLocRule']}') Order by PhId DESC ";
$q=safe_r_sql($Sql);
while($r=safe_fetch($q)) {
    $ComboPhase[$r->PhId] = get_text($r->PhId . '_Phase');
}
$ComboPhase[0]='---';

$ComboMatchMode = array();
for($i=0; $i<=1; $i++) {
    $ComboMatchMode[$i]=get_text('MatchMode_' . $i);
}

$ComboTarget = array();

$Select = "SELECT * FROM Targets ORDER BY TarOrder ";
$RsT=safe_r_sql($Select);
while ($Row=safe_fetch($RsT)) {
    $ComboTarget[$Row->TarId]=get_text($Row->TarDescr);
}

$Select = "SELECT * FROM Events 
    LEFT JOIN Targets ON EvFinalTargetType=TarId 
    LEFT JOIN (SELECT EcCode, COUNT(*) as ruleCnt FROM EventClass WHERE EcTeamEvent>0 AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " GROUP BY EcCode) as sqy on EvCode=EcCode
    WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1'
    ORDER BY EvProgr ASC,EvCode ASC, EvTeamEvent ASC ";
$Rs=safe_r_sql($Select);

echo '<tbody id="TeamTable">';
while ($MyRow=safe_fetch($Rs)) {
    print '<tr team="1" ref="' . $MyRow->EvCode . '" class="rowHover">';
    print '<td class="Center">
        <input type="button" ' . ($MyRow->ruleCnt == 0 ? 'class="red"' : '') . ' value="'.$MyRow->EvCode .'" onclick="SetEventRules(this)">
        </td>';

    print '<td class="Center">
        <input type="text" size="50" maxlength="64" name="EvEventName" value="' . $MyRow->EvEventName . '" onBlur="UpdateField(this)">
        </td>';

    print '<td class="Center">
        <input type="text" size="3" maxlength="3" name="EvProgr" value="' . $MyRow->EvProgr . '" onBlur="UpdateField(this)">
        </td>';

    if($IsRunArchery) {
	    echo '<td class="Center"><select onchange="UpdateField(this)" name="EvElimType">
				<option value="0" '.($MyRow->EvElimType==0 ? ' selected="selected"' : '').'>'.get_text('GroupedStart','RunArchery').'</option>
				<option value="1" '.($MyRow->EvElimType==1 ? ' selected="selected"' : '').'>'.get_text('SingleStart','RunArchery').'</option>
			</select></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="4" value="'.$MyRow->EvTargetSize.'" name="EvTargetSize"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvFinEnds.'" name="EvFinEnds"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvE1Arrows.'" name="EvE1Arrows"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvFinArrows.'" name="EvFinArrows"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvDistance.'" name="EvDistance"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvArrowPenalty.'" name="EvArrowPenalty"></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvLoopPenalty.'" name="EvLoopPenalty"></td>';
		echo '<td class="Center"><select name="EvFinalFirstPhase" onchange="UpdateField(this)">
			<option value="0"'.($MyRow->EvFinalFirstPhase==='0' ? ' selected="selected""' : '').'>'.get_text('SingleRound','RunArchery').'</option>
			<option value="1"'.($MyRow->EvFinalFirstPhase==='1' ? ' selected="selected""' : '').'>'.get_text('WithFinalsRounds','RunArchery').'</option>
			<option value="2"'.($MyRow->EvFinalFirstPhase==='2' ? ' selected="selected""' : '').'>'.get_text('WithSemiRounds','RunArchery').'</option>
			</select></td>';
	    echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvElim1.'" name="EvElim1"'.($MyRow->EvFinalFirstPhase!=2 ? ' disabled="disabled"' : '').'></td>';
		echo '<td class="Center"><input type="number" onBlur="UpdateField(this)" size="3" value="'.$MyRow->EvElim2.'" name="EvElim2"'.($MyRow->EvFinalFirstPhase==0 ? ' disabled="disabled"' : '').'></td>';
    } else {
        print '<td class="Center">
            <input type="checkbox" name="EvIsPara" ' . ($MyRow->EvIsPara ? 'checked="checked"' : '') . ' onclick="UpdateField(this)">
            </td>';

        if($Elim) {
            print '<td class="Center">';
            echo '<div>';
            echo '<div><input type="button" value="'.htmlspecialchars(get_text('Edit', 'Tournament'), ENT_QUOTES).'" onclick="location=\'ListEvents-Eliminations.php?Event='.$MyRow->EvCode.'\'" /></div>';
            echo '<div>';
            if($MyRow->EvElimType == 1 OR $MyRow->EvElimType == 2) {
                if ($MyRow->EvElim1) {
                    echo '<div class="Flex-line">';
                    echo '<div><b>' . get_text('StageE1', 'ISK') . '</b></div>';
                    echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvE1Ends . '</div>';
                    echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvE1Arrows . '</div>';
                    echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvE1SO . '</div>';
                    echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim1 . '</div>';
                    echo '</div>';
                }
                if ($MyRow->EvElim2) {
                    echo '<div class="Flex-line">';
                    echo '<div><b>' . get_text('StageE2', 'ISK') . '</b></div>';
                    echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvE2Ends . '</div>';
                    echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvE2Arrows . '</div>';
                    echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvE2SO . '</div>';
                    echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim2 . '</div>';
                    echo '</div>';
                }
            } elseif($MyRow->EvElimType == 3) {
                echo '<div class="Flex-line">';
                echo '<div><b>' . get_text('StagePool2', 'ISK') . '</b></div>';
                echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvElimEnds . '</div>';
                echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvElimArrows . '</div>';
                echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvElimSO . '</div>';
                echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim2 . '</div>';
                echo '</div>';
            } elseif($MyRow->EvElimType == 4) {
                echo '<div class="Flex-line">';
                echo '<div><b>' . get_text('StagePool4', 'ISK') . '</b></div>';
                echo '<div>' . get_text('Ends', 'Tournament') . ': ' . $MyRow->EvElimEnds . '</div>';
                echo '<div>' . get_text('Arrows', 'Tournament') . ': ' . $MyRow->EvElimArrows . '</div>';
                echo '<div>' . get_text('ShotOff', 'Tournament') . ': ' . $MyRow->EvElimSO . '</div>';
                echo '<div>' . get_text('Archers') . ': ' . $MyRow->EvElim2 . '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            print '</td>';
        }

        print '<td class="Center">';
        print '<select name="d_EvMatchMode_' . $MyRow->EvCode . '" id="d_EvMatchMode_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvMatchMode_' . $MyRow->EvCode . '\');">';
        foreach ($ComboMatchMode as $Key => $Value)
            print '<option value="' . $Key . '"' . ($Key==$MyRow->EvMatchMode ? ' selected' : '') . '>' . $Value . '</option>';
        print '</select>';
        print '</td>';

        print '<td class="Center">';
        print '<select name="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" id="d_EvFinalFirstPhase_' . $MyRow->EvCode . '" onChange="javascript:UpdatePhase(\'' . $MyRow->EvCode . '\',' . $MyRow->EvFinalFirstPhase . ',\'' . get_text('MsgAreYouSure') . '\');">';
        foreach ($ComboPhase as $Key => $Value)
            print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalFirstPhase ? ' selected' : '') . '>' . $Value . '</option>';
        print '</select>';
        print '</td>';

        print '<td class="Center">';
        print '<select name="d_EvFinalTargetType_' . $MyRow->EvCode . '" id="d_EvFinalTargetType_' . $MyRow->EvCode . '" onChange="javascript:UpdateField(\'d_EvFinalTargetType_' . $MyRow->EvCode . '\');">';
        foreach ($ComboTarget as $Key => $Value)
        {
            print '<option value="' . $Key . '"' . ($Key==$MyRow->EvFinalTargetType ? ' selected' : '') . '>' . $Value . '</option>';
        }
        print '</select>';
        print '</td>';

        print '<td class="Center">';
        print '<input type="text" size="3" maxlength="3" name="d_EvTargetSize_' . $MyRow->EvCode . '" id="d_EvTargetSize_' . $MyRow->EvCode . '" value="' . $MyRow->EvTargetSize . '" onBlur="javascript:UpdateField(\'d_EvTargetSize_' . $MyRow->EvCode . '\');">';
        print '</td>';

        print '<td class="Center">';
        print '<input type="text" size="12" maxlength="10" name="d_EvDistance_' . $MyRow->EvCode . '" id="d_EvDistance_' . $MyRow->EvCode . '" value="' . $MyRow->EvDistance . '" onBlur="javascript:UpdateField(\'d_EvDistance_' . $MyRow->EvCode . '\');">';
        print '</td>';
    }


    print '<td class="Center">';
	print '<i class="fa fa-lg fa-save text-success mr-3"></i>'; // does nothing, just forces user to click outside of the field!
    print '<i onclick="DeleteEvent(this)" class="far fa-lg fa-trash-can text-danger"></i>';
    print '</td>';
    print '</tr>';
}
echo '</tbody>';

echo '<tr class="Divider"><td colspan="'.$NumCols.'"></td></tr>';

// new event row
echo '<tr team="1" ref="NewRow">';
echo '<td class="Center"><input type="text" name="event" size="12" maxlength="10"></td>';
echo '<td class="Center"><input type="text" size="50" maxlength="64" name="EvEventName"></td>';
echo '<td class="Center"><input type="text" size="3" maxlength="3" name="EvProgr"></td>';
if($IsRunArchery) {
	echo '<td class="Center"><select onchange="UpdateField(this)" name="EvElimType">
				<option value="0">'.get_text('GroupedStart','RunArchery').'</option>
				<option value="1">'.get_text('SingleStart','RunArchery').'</option>
			</select></td>';
	echo '<td class="Center"><input type="number" size="4" name="EvTargetSize"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvFinEnds"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvE1Arrows"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvFinArrows"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvDistance"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvArrowPenalty"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvLoopPenalty"></td>';
	echo '<td class="Center"><select name="EvFinalFirstPhase">
			<option value="0">'.get_text('SingleRound','RunArchery').'</option>
			<option value="1">'.get_text('WithFinalsRounds','RunArchery').'</option>
			<option value="2">'.get_text('WithSemiRounds','RunArchery').'</option>
			</select></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvElim1" disabled="disabled"></td>';
	echo '<td class="Center"><input type="number" size="3" name="EvElim2" disabled="disabled"></td>';
} else {
	echo '<td class="Center"><input type="checkbox" name="New_EvIsPara" id="New_EvIsPara"></td>';
	echo '<td class="Center">';
	echo '<select name="New_EvMatchMode" id="New_EvMatchMode">';
	foreach ($ComboMatchMode as $Key => $Value) {
	    print '<option value="' . $Key . '">' . $Value . '</option>';
	}
	echo '</select>';
	echo '</td>';
	echo '<td class="Center">';
	echo '<select name="New_EvFinalFirstPhase" id="New_EvFinalFirstPhase">';
	foreach ($ComboPhase as $Key => $Value) {
	    print '<option value="' . $Key . '">' . $Value . '</option>';
	}
	echo '</select>';
	echo '</td>';
	echo '<td class="Center">';
	echo '<select name="New_EvFinalTargetType" id="New_EvFinalTargetType">';
	foreach ($ComboTarget as $Key => $Value) {
	    print '<option value="' . $Key . '">' . $Value . '</option>';
	}
	echo '</select>';
	echo '</td>';
	echo '<td class="Center"><input type="text" name="New_EvTargetSize" id="New_EvTargetSize" size="3" maxlength="3"></td>';
	echo '<td class="Center"><input type="text" name="New_EvDistance" id="New_EvDistance" size="12" maxlength="10"></td>';
}
echo '<td class="Center">
    <div class="Button" onClick="AddEvent(this)">'.get_text('CmdSave').'</div>
    </td>';
echo '</tr>';
if($Elim) {
    echo '<tr><td colspan="' . ($NumCols) . '" class="Bold Center">' . get_text('ChangeElimWarning') . '</td></tr>';
}
echo '</table>';
echo '<div id="idOutput"></div>';

include('Common/Templates/tail.php');
