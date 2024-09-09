<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) printCrackError();
checkACL(AclCompetition, AclReadWrite);

require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_Various.inc.php');

$AddOnsEnabled = 0;
$listAddOns=array();
if(module_exists("ExtraAddOns")) {
    $AddOnsEnabled =  intval(getModuleParameter("ExtraAddOns","AddOnsEnable","0"));
    $listAddOns = getModuleParameter("ExtraAddOns","AddOnsList", array());
}

$IncludeJquery = true;
$JS_SCRIPT=array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Team/Fun_AJAX_SetEventRules.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    phpVars2js(array(
        'AddOnsEnabled'=>boolval($AddOnsEnabled != 0),
    )),
);

$PAGE_TITLE=get_text('TeamDefinition');

include('Common/Templates/head.php');
echo '<table class="Tabella" id="MyTable">';
echo '<tr><th class="Title" colspan="'.(6+$AddOnsEnabled).'">' .get_text('TeamDefinition') . '</th></tr>';
echo '<tr class="Divider"><td colspan="'.(6+$AddOnsEnabled).'"></td></tr>';

$Select = "SELECT Events.*, ToGoldsChars, ToGolds, ToXNineChars, ToXNine FROM Events inner join Tournament on ToId=EvTournament WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']);
$RsEv = safe_r_sql($Select);
$RowEv=null;
if (safe_num_rows($RsEv)==1) {
    $RowEv=safe_fetch($RsEv);
    echo '<tr><td class="Title" colspan="'.(6+$AddOnsEnabled).'">'.get_text($RowEv->EvEventName,'','',true).'</td></tr>';
    echo '<tr>';
    echo '<th class="w-10">'.get_text('Number').'</th>';
    echo '<th class="w-15">'.get_text('Division').'</th>';
    echo '<th class="w-15">'.get_text('Class').'</th>';
    echo '<th class="w-20">'.get_text('SubClass','Tournament').'</th>';
    if($AddOnsEnabled) {
        echo '<th class="w-15">'.get_text('ExtraAddOns','Tournament').'</th>';
    }
    echo '<th class="w-5">&nbsp;</th>';
    echo '<th class="w-5">&nbsp;</th>';
    echo '</tr>';
    $Select
        = "SELECT ec.*,Quanti "
        . "FROM EventClass AS ec INNER JOIN("
        . "SELECT COUNT(*) AS Quanti,EcCode,EcTeamEvent,EcTournament "
        . "FROM EventClass "
        . "WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent!='0' AND EcTournament= " . StrSafe_DB($_SESSION['TourId']) . " "
        . "GROUP BY EcCode,EcTeamEvent,EcTournament"
        . ") AS sq ON ec.EcCode=sq.EcCode AND ec.EcTeamEvent=sq.EcTeamEvent AND ec.EcTournament=sq.EcTournament "
        . "WHERE ec.EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND ec.EcTeamEvent<>0 AND ec.EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
        . "ORDER BY EcTeamEvent ASC,EcDivision,EcClass ";
    $Rs=safe_r_sql($Select);
    echo '<tbody id="tbody">';
    if (safe_num_rows($Rs)>0) {
        $MyGroup = -1;

        while ($MyRow=safe_fetch($Rs)) {
            if ($MyGroup!=$MyRow->EcTeamEvent AND $MyGroup!=-1)
                print '<tr id="Div_' . $MyRow->EcCode . '_' . $MyRow->EcTeamEvent . '" class="Divider"><td colspan="'.(6+$AddOnsEnabled).'"></td></tr>';

            echo '<tr id="Row_' . $MyRow->EcCode . '_' . $MyRow->EcTeamEvent . '_' . $MyRow->EcDivision . $MyRow->EcClass . $MyRow->EcSubClass . $MyRow->EcExtraAddons . '">';
            if ($MyGroup!=$MyRow->EcTeamEvent)
                print '<td rowspan="' . $MyRow->Quanti . '" class="Center">' . $MyRow->EcNumber . '</td>';
            echo '<td class="Center">' . $MyRow->EcDivision . '</td>';
            echo '<td class="Center">' . $MyRow->EcClass . '</td>';
            echo '<td class="Center">' . $MyRow->EcSubClass . '</td>';
            if($AddOnsEnabled) {
                $tmpAddOn = array();
                foreach ($listAddOns as $kAO => $vAO) {
                    if((pow(2,$kAO) & $MyRow->EcExtraAddons) !==0) {
                        $tmpAddOn[] = $vAO;
                    }
                }
                echo '<td class="Center">' . implode('<br>',$tmpAddOn) . '</td>';
            }
            echo '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" onclick="DeleteEventPartialRule(\''.$MyRow->EcCode.'\',\''.$MyRow->EcTeamEvent.'\',\''.$MyRow->EcDivision.'\',\''.$MyRow->EcClass.'\',\''.$MyRow->EcSubClass.'\',\''.$MyRow->EcExtraAddons.'\')" alt="#" title="#"></td>';
            if ($MyGroup!=$MyRow->EcTeamEvent) {
                echo '<td rowspan="' . $MyRow->Quanti . '" class="Center">'.
                    '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" onclick="DeleteEventRule(\'' . $MyRow->EcCode .'\',\'' . $MyRow->EcTeamEvent . '\')" alt="#" title="#">'.
                    '</td>';
            }
            echo '</tr>';
            $MyGroup=$MyRow->EcTeamEvent;
        }
    }
}
echo '</tbody>';

echo '<tr class="Divider"><th colspan="'.(6+$AddOnsEnabled).'"></th></tr>';
echo '<tr><td class="Right w-20">'.get_text('MixedTeamEvent').'</td><td class="Left" colspan="'.(5+$AddOnsEnabled).'">'.
    '<select name="d_EvMixedTeam" id="d_EvMixedTeam" onChange="SetMixedTeam(\''.$RowEv->EvCode.'\')">'.
        '<option value="0"'. (($RowEv!=null AND $RowEv->EvMixedTeam==0) ? ' selected' : '').'>'.get_text('No').'</option>'.
        '<option value="1"'. (($RowEv!=null AND $RowEv->EvMixedTeam==1) ? ' selected' : '').'>'.get_text('Yes').'</option>'.
    '</select></td></tr>';
echo '<tr class="Divider"><th colspan="'.(6+$AddOnsEnabled).'"></th></tr>';
echo '<tr><td class="Right w-20">'.get_text('AllowMultiTeam').'</td><td class="Left" >'.
        '<select name="d_EvMultiTeam" id="d_EvMultiTeam" onChange="SetMultiTeam(\''.$RowEv->EvCode.'\')">'.
            '<option value="0"'.(($RowEv!=null AND $RowEv->EvMultiTeam==0) ? ' selected' : '').'>'.get_text('No').'</option>'.
            '<option value="1"'.(($RowEv!=null AND $RowEv->EvMultiTeam!=0) ? ' selected' : '').'>'.get_text('Yes').'</option>'.
        '</select>'.
    '</td>'.
    '<td class="Right w-20" colspan="2">'.get_text('MultiTeamMaxNo').'</td><td class="Left" colspan="'.(2+$AddOnsEnabled).'">'.
        '<input type="number" step="1" min="0" max="999" name="d_EvMultiTeamNo" id="d_EvMultiTeamNo"  onChange="SetMultiTeam(\''.$RowEv->EvCode.'\');" value="'.($RowEv->EvMultiTeam!=0 ? $RowEv->EvMultiTeamNo : 0).'">'.
    '</td></tr>';
echo '<tr><td class="Right w-20">'.get_text('TeamCreationMode','Tournament').'</td><td class="Left" colspan="2">'.
        '<select name="d_EvTeamCreationMode" id="d_EvTeamCreationMode" onChange="SetTeamCreationMode(\''.$RowEv->EvCode.'\')">'.
            '<option value="0"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==0) ? ' selected' : '').'>'.get_text('TeamCreationMode_0','Tournament').'</option>'.
            '<option value="1"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==1) ? ' selected' : '').'>'.get_text('TeamCreationMode_1','Tournament').'</option>'.
            '<option value="2"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==2) ? ' selected' : '').'>'.get_text('TeamCreationMode_2','Tournament').'</option>'.
            '<option value="3"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==3) ? ' selected' : '').'>'.get_text('TeamCreationMode_3','Tournament').'</option>'.
        '</select>'.
    '</td>'.
    '<td class="Right w-20">'.get_text('AllowPartialTeams').'</td><td class="Left" colspan="'.(2+$AddOnsEnabled).'">'.
        '<select name="d_EvPartialTeam" id="d_EvPartialTeam" onChange="SetPartialTeam(\''. $RowEv->EvCode.'\')">'.
            '<option value="0"'.(($RowEv!=null AND $RowEv->EvPartialTeam==0) ? ' selected' : '').'>'. get_text('No').'</option>'.
            '<option value="1"'.(($RowEv!=null AND $RowEv->EvPartialTeam==1) ? ' selected' : '').'>'. get_text('Yes').'</option>'.
        '</select>'.
    '</td></tr>';
echo '<tr class="Divider"><th colspan="'.(6+$AddOnsEnabled).'"></th></tr>';

echo '<tr>'.
        '<th class="w-10">'.get_text('Number').'</th>'.
        '<th class="w-25">'.get_text('Division').'</th>'.
        '<th class="w-25">'.get_text('Class').'</th>'.
        '<th class="w-15">'.get_text('SubClass','Tournament').'</th>';
        if($AddOnsEnabled) {
            echo '<th class="w-15">'.get_text('ExtraAddOns','Tournament').'</th>';
        }
echo '<th class="w-5">&nbsp;</th><th class="w-5">&nbsp;</th></tr>';
echo '<tr><td colspan="'.(6+$AddOnsEnabled).'" class="Center">' . get_text('PressCtrl2SelectAll') . '</td></tr>';
echo '<tr><td class="Center Top w-10"><input type="number" step="1" min="0" max="999" name="New_EcNumber" id="New_EcNumber"  value=""></td>';
$Select = "SELECT * "
    . "FROM Divisions "
    . "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete=1 "
    . "ORDER BY DivViewOrder ASC ";
$RsSel = safe_r_sql($Select);
$ComboDiv = '<select name="New_EcDivision" id="New_EcDivision" class="w-90" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
if (safe_num_rows($RsSel)>0) {
    while ($Row=safe_fetch($RsSel)) {
        $ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . ' - ' . $Row->DivDescription . '</option>';
    }
}
$ComboDiv.= '</select>';
echo '<td class="Center Top w-25">' . $ComboDiv . '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_EcDivision\');">' . get_text('SelectAll') . '</a></td>';

$Select = "SELECT * "
    . "FROM Classes "
    . "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 "
    . "ORDER BY ClViewOrder ASC ";
$RsSel = safe_r_sql($Select);
$ComboCl = '<select name="New_EcClass" id="New_EcClass" class="w-90" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
if (safe_num_rows($RsSel)>0) {
    while ($Row=safe_fetch($RsSel)) {
        $ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . ' - ' . $Row->ClDescription . '</option>';
    }
}
$ComboCl.= '</select>';
echo '<td class="Center Top w-25">' . $ComboCl.  '<br><br><a class="Link" href="javascript:SelectAllOpt(\'New_EcClass\');">' . get_text('SelectAll') . '</a></td>';

$Select
    = "SELECT  ScId, ScDescription, ScViewOrder "
    . "FROM SubClass "
    . "WHERE ScTournament = " . StrSafe_DB($_SESSION['TourId'])
    . "ORDER BY ScViewOrder ASC ";
$RsSel = safe_r_sql($Select);
$ComboSubCl = '<select name="New_EcSubClass" id="New_EcSubClass" class="w-90" multiple="multiple" disabled="disabled" size="'.min(15,safe_num_rows($RsSel)+2).'">';
if (safe_num_rows($RsSel)>0) {
    while ($Row=safe_fetch($RsSel)) {
        $ComboSubCl.= '<option value="' . $Row->ScId . '">' . $Row->ScId . ' - ' . $Row->ScDescription . '</option>';
    }
}
$ComboSubCl.= '</select>';
echo '<td class="Center Top w-15">' . $ComboSubCl . '<br><br><input type="checkbox" id="enableSubClass" onclick="enableSubclass(this)">'. get_text('UseSubClasses','Tournament') . '</td>';

if($AddOnsEnabled) {
    $ComboAddOns = '';
    $cntAO=0;
    foreach ($listAddOns as $kAO => $vAO) {
        if(!empty($vAO)) {
            $cntAO++;
            $ComboAddOns .= '<option value="' . pow(2, $kAO) . '">' . $vAO . '</option>';
        }
    }
    if($cntAO) {
        $ComboAddOns = '<select name="New_EcExtraAddons" class="w-90" id="New_EcExtraAddons" multiple="multiple" disabled="disabled" size="' . $cntAO . '">' . $ComboAddOns . '</select>';
    }
    echo '<td class="Center Top w-15">' . $ComboAddOns . '<br><br><input type="checkbox" id="enableAddOns" onclick="enableAddOns(this)">'. get_text('UseAddOns','Tournament') . '</td>';
}

echo '<td colspan="2" class="Center Top w-10">'.
        '<input type="button" name="Command" id="Command" value="'.get_text('CmdSave').'" onclick="javascript:AddEventRule(\''.$RowEv->EvCode.'\');">'.
    '</td></tr></table>';

echo '<br/>';
echo '<table class="Tabella">';
    echo '<tr id="AdvancedButton"><th colspan="4"><input type="button" onclick="showAdvanced()" value="'.get_text('Advanced').'"></th></tr>';
    echo '<tbody id="Advanced" style="display: none;">';
    echo '<tr>';
        echo '<th>'.get_text('EventNumQualified', 'Tournament').'</th>';
        echo '<th>'.get_text('EventStartPosition', 'Tournament').'</th>';
        echo '<th>'.get_text('EventHasMedal', 'Tournament').'</th>';
        echo '<th>'.get_text('EventParentCode', 'Tournament').'</th>';
        echo '<th>'.get_text('EventParentWinningBranch', 'Tournament').'</th>';
        echo '<th>'.get_text('EventWinnerFinalRank', 'Tournament').'</th>';
        echo '<th>'.get_text('MaxTeamPersons', 'Tournament').'</th>';
        echo '<th>'.get_text('WaCategory', 'Tournament').'</th>';
        echo '<th>'.get_text('RecordCategory', 'Tournament').'</th>';
        echo '<th>'.get_text('OdfEventCode', 'ODF').'</th>';
        echo '<th>'.get_text('GoldLabel','Tournament').'</th>';
        echo '<th>'.get_text('XNineLabel','Tournament').'</th>';
        echo '<th>'.get_text('PointsAsGold','Tournament').'<br/><span style="font-weight: normal">'.get_text('CommaSeparatedValues').'</span></th>';
        echo '<th>'.get_text('PointsAsXNine','Tournament').'<br/><span style="font-weight: normal">'.get_text('CommaSeparatedValues').'</span></th>';
        echo '<th>'.get_text('CheckGoldsInMatch','Tournament').'</th>';
        echo '<th>'.get_text('CheckXNinesInMatch','Tournament').'</th>';
        echo '</tr>';

    echo '<tr>';
        echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvNumQualified.'" id="fld=num&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvFirstQualified.'" id="fld=first&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><select onchange="UpdateData(this)" id="fld=medal&team=1&event='.$_REQUEST['EvCode'].'">
                <option value="1" '.($RowEv->EvMedals ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>
                <option value="0" '.($RowEv->EvMedals ? '' : ' selected="selected"').'>'.get_text('No').'</option>
            </select></td>';
        echo '<td class="Center"><select name="ParentRule" onchange="UpdateData(this)" id="fld=parent&team=1&event='.$_REQUEST['EvCode'].'">';
                echo '<option value="">'.get_text('Select', 'Tournament').'</option>';
                $q=safe_r_sql("select EvCode, EvEventName from Events where EvTeamEvent=1 and EvFinalFirstPhase>$RowEv->EvFinalFirstPhase and EvCode!='$RowEv->EvCode' and EvTournament={$_SESSION['TourId']}");
                while($r=safe_fetch($q)) {
                    echo '<option value="'.$r->EvCode.'" '.($RowEv->EvCodeParent==$r->EvCode ? ' selected="selected"' : '').'>'.$r->EvCode.' - '.$r->EvEventName.'</option>';
                }
                echo '</select></td>';
        echo '<td class="Center"><input size="5" type="checkbox" '.($RowEv->EvCodeParentWinnerBranch ? 'checked="checked"' : ''). ' id="fld=parentWinner&team=1&event='.$_REQUEST['EvCode'].'" onclick="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvWinnerFinalRank.'" id="fld=final&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvMaxTeamPerson.'" id="fld=persons&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="text" size="12" maxlength="10" value="'.$RowEv->EvWaCategory.'" id="fld=wacat&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="text" size="12" maxlength="10" value="'.$RowEv->EvRecCategory.'" id="fld=reccat&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input type="text" value="'.$RowEv->EvOdfCode.'" id="fld=odfcode&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input size="5" type="text" value="'.($RowEv->EvGolds ?: '').'" id="fld=golds&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input size="5" type="text" value="'.($RowEv->EvXNine ?: '').'" id="fld=xnines&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input size="5" type="text" value="'.implode(',', DecodeFromString($RowEv->EvGoldsChars ?: '', false, true)).'" id="fld=goldschars&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input size="5" type="text" value="'.implode(',', DecodeFromString($RowEv->EvXNineChars ?: '', false, true)).'" id="fld=xninechars&team=1&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
        echo '<td class="Center"><input size="5" type="checkbox" '.($RowEv->EvCheckGolds ? 'checked="checked"' : ''). ' id="fld=checkGolds&team=1&event='.$_REQUEST['EvCode'].'" onclick="UpdateData(this)"></td>';
        echo '<td class="Center"><input size="5" type="checkbox" '.($RowEv->EvCheckXNines ? 'checked="checked"' : ''). ' id="fld=checkXnines&team=1&event='.$_REQUEST['EvCode'].'" onclick="UpdateData(this)"></td>';
        echo '</tr>';
    echo '</tbody>';
    echo '</table>';

echo '<br/>';

echo '<table class="Tabella">';
echo '<tr><td class="Center"><a class="Link" href="ListEvents.php">'.get_text('Back').'</a></td></tr>';
echo '</table>';
echo '<div id="idOutput"></div>';

include('Common/Templates/tail.php');
