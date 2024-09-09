<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

if (!CheckTourSession() || !isset($_REQUEST['EvCode'])) printCrackError();
checkACL(AclCompetition, AclReadWrite);

$AddOnsEnabled = 0;
$listAddOns=array();
if(module_exists("ExtraAddOns")) {
    $AddOnsEnabled =  intval(getModuleParameter("ExtraAddOns","AddOnsEnable","0"));
    $listAddOns = getModuleParameter("ExtraAddOns","AddOnsList", array());
}

$IncludeJquery = true;
$JS_SCRIPT=array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Final/Individual/Fun_AJAX_SetEventRules.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    phpVars2js(array(
        'AddOnsEnabled'=>boolval($AddOnsEnabled != 0),
    )),
);

include('Common/Templates/head.php');

echo '<table class="Tabella" id="MyTable">';
echo '<tr><th class="Title" colspan="'.(4+$AddOnsEnabled).'">'. get_text('EventClass') . '</th></tr>';
echo '<tr class="Divider"><td colspan="'.(4+$AddOnsEnabled).'"></td></tr>';

$Select = "SELECT Events.*, ToGoldsChars, ToGolds, ToXNineChars, ToXNine FROM Events inner join Tournament on ToId=EvTournament WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$RsEv = safe_r_sql($Select);

if (safe_num_rows($RsEv)==1 and $RowEv=safe_fetch($RsEv)) {

    $Select
        = "SELECT * "
        . "FROM Divisions "
        . "WHERE DivTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND DivAthlete=1 "
        . "ORDER BY DivViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboDiv = '<select name="New_EcDivision" class="w-90" id="New_EcDivision" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel))
            $ComboDiv.= '<option value="' . $Row->DivId . '">' . $Row->DivId . ' - ' . $Row->DivDescription . '</option>';
    }
    $ComboDiv.= '</select>';

    $Select
        = "SELECT * "
        . "FROM Classes "
        . "WHERE ClTournament = " . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 "
        . "ORDER BY ClViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboCl = '<select name="New_EcClass" class="w-90" id="New_EcClass" multiple="multiple" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel))
            $ComboCl.= '<option value="' . $Row->ClId . '">' . $Row->ClId . ' - ' . $Row->ClDescription . '</option>';
    }
    $ComboCl.= '</select>';


    $Select
        = "SELECT ScId, ScDescription, ScViewOrder  "
        . "FROM SubClass "
        . "WHERE ScTournament = " . StrSafe_DB($_SESSION['TourId'])
        . "ORDER BY ScViewOrder ASC ";
    $RsSel = safe_r_sql($Select);
    $ComboSubCl = '<select name="New_EcSubClass" class="w-90" id="New_EcSubClass" multiple="multiple" disabled="disabled" size="'.min(15,safe_num_rows($RsSel)+2).'">';
    if (safe_num_rows($RsSel)>0) {
        while ($Row=safe_fetch($RsSel))
            $ComboSubCl.= '<option value="' . $Row->ScId . '">' . $Row->ScId  . ' - ' . $Row->ScDescription . '</option>';
    }
    $ComboSubCl.= '</select>';
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

    echo '<tr><td class="Title" colspan="'.(4+$AddOnsEnabled).'">'.get_text($RowEv->EvEventName,'','',true).'</td></tr>';
    echo '<tr>';
    echo '<td class="Center" colspan="'.(4+$AddOnsEnabled).'"><select name="d_EvTeamCreationMode" id="fld=teamode&team=0&event='.$_REQUEST['EvCode'].'" onChange="UpdateData(this)">'.
        '<option value="0"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==0) ? ' selected' : '').'>'.get_text('SelectedClub-0','Tournament').'</option>'.
        '<option value="1"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==1) ? ' selected' : '').'>'.get_text('SelectedClub-1','Tournament').'</option>'.
        '<option value="2"'.(($RowEv!=null AND $RowEv->EvTeamCreationMode==2) ? ' selected' : '').'>'.get_text('SelectedClub-2','Tournament').'</option>'.
        '</select></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th class="w-25">'.get_text('Division').'</th>';
    echo '<th class="w-25">'.get_text('Class').'</th>';
    echo '<th class="w-25">'.get_text('SubClass','Tournament').'</th>';
    if($AddOnsEnabled) {
        echo '<th class="w-15">'.get_text('ExtraAddOns','Tournament').'</th>';
    }
    echo '<th>&nbsp;</th>';
    echo '</tr>';

    $Select = "SELECT * FROM EventClass 
        WHERE EcCode=" . StrSafe_DB($RowEv->EvCode) . " AND EcTeamEvent='0' AND EcTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
        ORDER BY EcDivision,EcClass,EcSubClass ";
    $Rs=safe_r_sql($Select);

	echo '<tbody id="tbody">';
    if (safe_num_rows($Rs)>0) {
        while ($MyRow=safe_fetch($Rs)) {
            print '<tr id="Row_' . $RowEv->EvCode . '_' . $MyRow->EcDivision . $MyRow->EcClass . $MyRow->EcSubClass . $MyRow->EcExtraAddons  . '">';
            print '<td class="Center">' . $MyRow->EcDivision . '</td>';
            print '<td class="Center">' . $MyRow->EcClass . '</td>';
            print '<td class="Center">' . $MyRow->EcSubClass . '</td>';
            if($AddOnsEnabled) {
                $tmpAddOn = array();
                foreach ($listAddOns as $kAO => $vAO) {
                    if((pow(2,$kAO) & $MyRow->EcExtraAddons) !==0) {
                        $tmpAddOn[] = $vAO;
                    }
                }
                print '<td class="Center">' . implode('<br>',$tmpAddOn) . '</td>';
            }
            print '<td class="Center">';
            print '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="Delete" title="Delete" onclick="DeleteEventRule(\'' . $RowEv->EvCode . '\',\'' . $MyRow->EcDivision . '\',\'' . $MyRow->EcClass . '\',\'' . $MyRow->EcSubClass . '\',\'' . $MyRow->EcExtraAddons. '\')">';
            print '</td>';
            print '</tr>';
        }
    }
    echo '</tbody>';
    print '<tr id="RowDiv" class="Divider"><td colspan="'.(4+$AddOnsEnabled).'"></td></tr>';

    echo '</table>';

    echo '<br/>';
    echo '<table class="Tabella">';
    echo '<tr><td colspan="'.(4+$AddOnsEnabled).'" class="Center">'.get_text('PressCtrl2SelectAll').'</td></tr>';
    echo '<tr id="NewRow">';
    echo '<td class="w-25 Center Top">'.$ComboDiv.'<br/><br/>
        <a class="Link" href="javascript:SelectAllOpt(\'New_EcDivision\');">'.get_text('SelectAll').'</a></td>';
    echo '<td  class="w-25 Center Top">'.$ComboCl.'<br/><br/>
        <a class="Link" href="javascript:SelectAllOpt(\'New_EcClass\');">'.get_text('SelectAll').'</a></td>';
    echo '<td class="w-25 Center Top">'.$ComboSubCl.'<br/><br/>
        <input type="checkbox" id="enableSubClass" onclick="enableSubclass(this)">'.get_text('UseSubClasses','Tournament').'</td>';
    if($AddOnsEnabled) {
        echo '<td class="w-15 Center Top">'.$ComboAddOns.'<br/><br/>
            <input type="checkbox" id="enableAddOns" onclick="enableAddOns(this)">'.get_text('UseAddOns','Tournament').'</td>';

    }
    echo '<td class="Center Top">
        <input type="button" name="Command" id="Command" value="'.get_text('CmdSave').'" onclick="AddEventRule(\''.$RowEv->EvCode.'\');"></td>';
    echo '</tr>';
    echo '</table>';

    echo '<br/>';

	// ADVANCED TABLE
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
    echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvNumQualified.'" id="fld=num&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
    echo '<td class="Center"><input type="number" min="0" max="9999" value="'.$RowEv->EvFirstQualified.'" id="fld=first&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
    echo '<td class="Center"><select onchange="UpdateData(this)" id="fld=medal&team=0&event='.$_REQUEST['EvCode'].'">
            <option value="1" '.($RowEv->EvMedals ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>
            <option value="0" '.($RowEv->EvMedals ? '' : ' selected="selected"').'>'.get_text('No').'</option>
        </select></td>';
    echo '<td class="Center"><select name="ParentRule" onchange="UpdateData(this)" id="fld=parent&team=0&event='.$_REQUEST['EvCode'].'">';
    echo '<option value="">'.get_text('Select', 'Tournament').'</option>';
    $q=safe_r_sql("select EvCode, EvEventName from Events where EvTeamEvent=0 and EvFinalFirstPhase>$RowEv->EvFinalFirstPhase and EvCode!='$RowEv->EvCode' and EvTournament={$_SESSION['TourId']}");
    while($r=safe_fetch($q)) {
        echo '<option value="'.$r->EvCode.'" '.($RowEv->EvCodeParent==$r->EvCode ? ' selected="selected"' : '').'>'.$r->EvCode.' - '.$r->EvEventName.'</option>';
    }
    echo '</select></td>';
	echo '<td class="Center"><select onchange="UpdateData(this)" id="fld=parentWinner&team=0&event='.$_REQUEST['EvCode'].'">
            <option value="1" '.($RowEv->EvCodeParentWinnerBranch ? ' selected="selected"' : '').'>'.get_text('Yes').'</option>
            <option value="0" '.($RowEv->EvCodeParentWinnerBranch ? '' : ' selected="selected"').'>'.get_text('No').'</option>
        </select></td>';
	echo '<td class="Center"><input min="0" max="9999" type="number" value="'.$RowEv->EvWinnerFinalRank.'" id="fld=final&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="12" maxlength="10" type="text" value="'.$RowEv->EvWaCategory.'" id="fld=wacat&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="12" maxlength="10" type="text" value="'.$RowEv->EvRecCategory.'" id="fld=reccat&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input type="text" value="'.$RowEv->EvOdfCode.'" id="fld=odfcode&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="5" type="text" value="'.($RowEv->EvGolds ?: '').'" id="fld=golds&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="5" type="text" value="'.($RowEv->EvXNine ?: '').'" id="fld=xnines&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="5" type="text" value="'.implode(',', DecodeFromString($RowEv->EvGoldsChars ?: '', false, true)).'" id="fld=goldschars&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="5" type="text" value="'.implode(',', DecodeFromString($RowEv->EvXNineChars ?: '', false, true)).'" id="fld=xninechars&team=0&event='.$_REQUEST['EvCode'].'" onchange="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="5" type="checkbox" '.($RowEv->EvCheckGolds ? 'checked="checked"' : ''). ' id="fld=checkGolds&team=0&event='.$_REQUEST['EvCode'].'" onclick="UpdateData(this)"></td>';
	echo '<td class="Center"><input size="5" type="checkbox" '.($RowEv->EvCheckXNines ? 'checked="checked"' : ''). ' id="fld=checkXnines&team=0&event='.$_REQUEST['EvCode'].'" onclick="UpdateData(this)"></td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';

    echo '<br/>';
    echo '<table class="Tabella">';
    echo '<tr><td class="Center"><a class="Link" href="ListEvents.php">'.get_text('Back').'</a></td></tr>';
    echo '</table>';
    echo '<div id="idOutput"></div>';
}

include('Common/Templates/tail.php');
