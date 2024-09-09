<?php
	require_once(dirname(dirname(__FILE__)) . '/config.php');
	CheckTourSession(true);
    checkACL(AclParticipants, AclReadWrite);
	require_once('Common/Fun_FormatText.inc.php');
    require_once('Common/Lib/CommonLib.php');

	$JS_SCRIPT=array(
        phpVars2js(array(
            'ConfirmResetAll' => get_text('ConfirmResetAll', 'Tournament'),
            'ConfirmSetAll' => get_text('ConfirmSetAll', 'Tournament'),
            'Confirm' => get_text('Confirm', 'Tournament'),
            'CmdCancel' => get_text('CmdCancel'),
            'CmdConfirm' => get_text('Confirm', 'Tournament'),
        )),
        '<script type="text/javascript" src="./ManEventAccess.js"></script>',
        '<style>
			input.ck {
              -webkit-appearance: none; /*hides the default checkbox*/
              height: 20px;
              width: 20px;
              position: relative;
              transition: 0.10s;
              background-color: #FE0006;
              text-align: center;
              font-weight: 600;
              color: white;
              border-radius: 3px;
              outline: none;
            }
			input.ck:checked {background-color:#0E9700}
			input.ck:hover, input.ck:focus {
                cursor: pointer; 
                opacity: 0.5;
                border: 3px black solid;;   
            }
            .ckContainer:focus-within, .ckContainer:hover {
                background-color: yellow !important;
            }
            tr.data:hover {
                background-color: #ffffd0 !important;
            }
		</style>',
		);

	$PAGE_TITLE=get_text('EventAccess','Tournament');
    $IncludeJquery = true;
	$Order=empty($_REQUEST['Order']) ? '' : $_REQUEST['Order'];

	include('Common/Templates/head.php');
    echo '<table class="Tabella">'.
        '<tr><th class="Title" colspan="13">' . get_text('EventAccess','Tournament') . '</th></tr>'.
        '<tr class="Divider"><td colspan="13"></td></tr>' .
        '<tr><td colspan="13" class="Bold">' .
            '<form>'.
                '<div style="display:flex;justify-content: space-around;flex-wrap: wrap">' .
                   '<div style="margin:0 1em">' . get_text('Archer') . '<input type="text" name="Name" value="' . (empty($_REQUEST['Name']) ? '' : $_REQUEST['Name']) . '"></div>'.
                    '<div style="margin:0 1em">' . get_text('Country') . '<input type="text" name="Country" value="' . (empty($_REQUEST['Country']) ? '' : $_REQUEST['Country']) . '"></div>'.
                    '<div style="margin:0 1em">' . get_text('Division') . '<input type="text" name="Div" value="' . (empty($_REQUEST['Div']) ? '' : $_REQUEST['Div']) . '"></div>'.
                    '<div style="margin:0 1em">' . get_text('Class') . '<input type="text" name="Class" value="' . (empty($_REQUEST['Class']) ? '' : $_REQUEST['Class']) . '"></div>'.
                    '<div style="margin:0 1em">' . get_text('Event') . '<input type="text" name="Event" value="' . (empty($_REQUEST['Event']) ? '' : $_REQUEST['Event']) . '"></div>'.
                    '<div style="margin:0 1em"><input type="submit" value="' . get_text('CmdOk') . '"></div>'.
                '</div>'.
            '</form>'.
        '</td></tr>'.
        '<tr class="Divider"><td colspan="13"></td></tr>';
	$Select = "SELECT EnId, EnCode, EnFirstName, EnName, EnTournament, EnSex, EnDivision, EnClass, CoCode, CoName, EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent, EnTeamMixEvent, EnWChair, EnDoubleSpace 
		FROM Entries 
		LEFT JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
		left join EventClass on EcTournament=EnTournament and EcDivision=EnDivision and EcClass=EnClass and EcTeamEvent=0
		WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1 ";
	if(!empty($_REQUEST['Div'])) {
	    $Select.=" and EnDivision like " .StrSafe_DB($_REQUEST['Div']);
    }
	if(!empty($_REQUEST['Class'])) {
	    $Select.=" and EnClass like " .StrSafe_DB($_REQUEST['Class']);
    }
	if(!empty($_REQUEST['Country'])) {
        $Select.= " and " . assembleWhereCondition(array('CoCode','CoName'),array($_REQUEST['Country']));
    }
	if(!empty($_REQUEST['Event'])) {
	    $Select.=" and EcCode like " .StrSafe_DB($_REQUEST['Event']);
    }
	if(!empty($_REQUEST['Name'])) {
        $Select.= " and " . assembleWhereCondition(array('EnFirstName','EnName'),array($_REQUEST['Name']));
    }

    $Select.=" group by EnId ";

    $Direction=substr($Order, -4)=='Desc' ? 'desc' : '';
    switch($Order) {
        case 'ordCode':
        case 'ordCodeDesc':
		        $OrderBy = " EnCode $Direction ";
            break;
        case 'ordName':
        case 'ordNameDesc':
            $OrderBy = " EnFirstName $Direction, EnName ";
            break;
        case 'ordCountry':
        case 'ordCountryDesc':
            $OrderBy = " CoCode $Direction, EnFirstName, EnName ";
            break;
        case 'ordDiv':
        case 'ordDivDesc':
            $OrderBy = "EnDivision $Direction, EnFirstName, EnName ";
            break;
        case 'ordCl':
        case 'ordClDesc':
            $OrderBy = "EnClass $Direction, EnFirstName, EnName ";
            break;
        case 'ordIn':
        case 'ordInDesc':
            $OrderBy = "EnIndClEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordFn':
        case 'ordFnDesc':
            $OrderBy = "EnIndFEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordTm':
        case 'ordTmDesc':
            $OrderBy = "EnTeamClEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordFt':
        case 'ordFtDesc':
            $OrderBy = "EnTeamFEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordMx':
        case 'ordMxDesc':
            $OrderBy = "EnTeamMixEvent $Direction, EnFirstName, EnName ";
            break;
        case 'ordWc':
        case 'ordWcDesc':
            $OrderBy = "EnWChair $Direction, EnFirstName, EnName ";
            break;
        case 'ordXb':
        case 'ordXbDesc':
            $OrderBy = "EnDoubleSpace $Direction, EnFirstName, EnName ";
            break;
        default:
	        $OrderBy = " EnFirstName ASC,EnName ASC ";
    }

	$Select.=" ORDER BY " . $OrderBy;
	$Rs=safe_r_sql($Select);

	if (safe_num_rows($Rs)>0) {
		echo '<tr>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCode' ? 'ordCodeDesc' : 'ordCode') . '">' . get_text('Code','Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordName' ? 'ordNameDesc' : 'ordName') . '">' . get_text('Archer') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCountry' ? 'ordCountryDesc' : 'ordCountry') . '">' . get_text('Country') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCountry' ? 'ordCountryDesc' : 'ordCountry') . '">' . get_text('NationShort','Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordDiv' ? 'ordDivDesc' : 'ordDiv') . '">' . get_text('Div') . '</a></td>'.
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordCl' ? 'ordClDesc' : 'ordCl') . '">' . get_text('Cl') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordIn' ? 'ordInDesc' : 'ordIn') . '">' . get_text('IndClEvent', 'Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordFn' ? 'ordFnDesc' : 'ordFn') . '">' . get_text('IndFinEvent', 'Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordTm' ? 'ordTmDesc' : 'ordTm') . '">' . get_text('TeamClEvent', 'Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordFt' ? 'ordFtDesc' : 'ordFt') . '">' . get_text('TeamFinEvent', 'Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordMx' ? 'ordMxDesc' : 'ordMx') . '">' . get_text('MixedTeamFinEvent', 'Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordWc' ? 'ordWcDesc' : 'ordWc') . '">' . get_text('WheelChair', 'Tournament') . '</a></td>' .
                '<td class="Title"><a class="LinkRevert" href="' . $_SERVER['PHP_SELF'] . go_get('Order',$Order=='ordXb' ? 'ordXbDesc' : 'ordXb') . '">' . get_text('DoubleSpace', 'Tournament') . '</a></td>' .
            '</tr>';
        echo '<tr>'.
                '<th colspan="6">&nbsp;</th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnIndClEvent"><a onclick="UpdateAllFields(\'d_e_EnIndClEvent\')">'.get_text('ToAll').'</a></th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnIndFEvent"><a onclick="UpdateAllFields(\'d_e_EnIndFEvent\')">'.get_text('ToAll').'</a></th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnTeamClEvent"><a onclick="UpdateAllFields(\'d_e_EnTeamClEvent\')">'.get_text('ToAll').'</a></th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnTeamFEvent"><a onclick="UpdateAllFields(\'d_e_EnTeamFEvent\')">'.get_text('ToAll').'</a></th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnTeamMixEvent"><a onclick="UpdateAllFields(\'d_e_EnTeamMixEvent\')">'.get_text('ToAll').'</a></th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnWChair"><a onclick="UpdateAllFields(\'d_e_EnWChair\')">'.get_text('ToAll').'</a></th>'.
                '<th class="Center"><input type="checkbox" id="d_e_EnDoubleSpace"><a onclick="UpdateAllFields(\'d_e_EnDoubleSpace\')">'.get_text('ToAll').'</a></th>'.
            '</tr>';

        echo '<tbody id="MainBody">';
		$CurRow = 0;
		while ($MyRow=safe_fetch($Rs)) {
            $ChkIndCl = '<input type="checkbox" class="ck" name="d_e_EnIndClEvent_' . $MyRow->EnId . '" id="d_e_EnIndClEvent_' . $MyRow->EnId . '"' . ($MyRow->EnIndClEvent==1 ? 'checked="checked"' : '').' onchange="UpdateField(\'d_e_EnIndClEvent_' . $MyRow->EnId . '\');">';
            $ChkIndFin = '<input type="checkbox" class="ck" name="d_e_EnIndFEvent_' . $MyRow->EnId . '" id="d_e_EnIndFEvent_' . $MyRow->EnId . '"' .($MyRow->EnIndFEvent==1 ? 'checked="checked"' : '').' onchange="UpdateField(\'d_e_EnIndFEvent_' . $MyRow->EnId . '\');">';
            $ChkTeamCl = '<input type="checkbox" class="ck" name="d_e_EnTeamClEvent_' . $MyRow->EnId . '" id="d_e_EnTeamClEvent_' . $MyRow->EnId . '"' .($MyRow->EnTeamClEvent==1 ? 'checked="checked"' : '').' onchange="UpdateField(\'d_e_EnTeamClEvent_' . $MyRow->EnId . '\');">';
            $ChkTeamFin = '<input type="checkbox" class="ck" name="d_e_EnTeamFEvent_' . $MyRow->EnId . '" id="d_e_EnTeamFEvent_' . $MyRow->EnId . '"' .($MyRow->EnTeamFEvent==1 ? 'checked="checked"' : '').' onchange="UpdateField(\'d_e_EnTeamFEvent_' . $MyRow->EnId . '\');">';
            $ChkMixTeamFin = '<input type="checkbox" class="ck" name="d_e_EnTeamMixEvent_' . $MyRow->EnId . '" id="d_e_EnTeamMixEvent_' . $MyRow->EnId . '"' .($MyRow->EnTeamMixEvent==1 ? 'checked="checked"' : '').' onchange="UpdateField(\'d_e_EnTeamMixEvent_' . $MyRow->EnId . '\');">';
            $ChkWheelChair = '<input type="checkbox" class="ck" name="d_e_EnWChair_' . $MyRow->EnId . '" id="d_e_EnWChair_' . $MyRow->EnId . '"' .($MyRow->EnWChair==1 ? ' checked="checked"' : '').' onchange="UpdateField(\'d_e_EnWChair_' . $MyRow->EnId . '\');">';
            $ChkDoubleSpace = '<input type="checkbox" class="ck" name="d_e_EnDoubleSpace_' . $MyRow->EnId . '" id="d_e_EnDoubleSpace_' . $MyRow->EnId . '"' .($MyRow->EnDoubleSpace==1 ? 'checked="checked"' : '').' onchange="UpdateField(\'d_e_EnDoubleSpace_' . $MyRow->EnId . '\');">';

            echo '<tr id="Row_' . $MyRow->EnId . '" class="data ' . ($CurRow++ % 2 ? ' OtherColor' : '') . '">'.
                    '<td>' . $MyRow->EnCode . '</td>'.
                    '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>'.
                    '<td>' . $MyRow->CoCode . '</td>' .
                    '<td>' . $MyRow->CoName . '</td>' .
                    '<td class="Center">' . $MyRow->EnDivision . '</td>' .
                    '<td class="Center">' . $MyRow->EnClass . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('IndClEvent', 'Tournament') .'">' . $ChkIndCl . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('IndFinEvent', 'Tournament') . '">' . $ChkIndFin . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('TeamClEvent', 'Tournament') . '">' . $ChkTeamCl . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('TeamFinEvent', 'Tournament') . '">' . $ChkTeamFin . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('MixedTeamFinEvent', 'Tournament') . '">' . $ChkMixTeamFin . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('DoubleSpace', 'Tournament') . '">' . $ChkWheelChair . '</td>' .
                    '<td class="Center ckContainer" title="' . get_text('WheelChair', 'Tournament') . '">' . $ChkDoubleSpace . '</td>' .
                '</tr>';
		}
        echo '</tbody>';
	}
    echo '</table>';
	include('Common/Templates/tail.php');