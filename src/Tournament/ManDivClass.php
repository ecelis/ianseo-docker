<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');
checkACL(AclCompetition, AclReadWrite);

if (!CheckTourSession())
{
    print get_text('CrackError');
    exit;
}

global $CFG;

$IncludeJquery = true;
$JS_SCRIPT = array(
    phpVars2js(array(
        'MsgAreYouSure' => get_text('MsgAreYouSure'),
        'MsgRowMustBeComplete' => get_text('MsgRowMustBeComplete'),
        'MsgInvalidCharacters' => get_text('InvalidCharacters','Errors', '<span class="text-danger">a-z A-Z 0-9</span>'),
        'CmdOk' => get_text('CmdOk'),
        'CmdCancel' => get_text('CmdCancel'),
        'Warning' => get_text('Warning', 'Tournament'),
    )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDivClass.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    );
$PAGE_TITLE=get_text('ManDivClass','Tournament');

include('Common/Templates/head.php');

$Disabled=(defined('dontEditClassDiv') ? ' disabled="disabled"' : '');

echo '<div align="center">
    <div class="medium">
    <table class="Tabella">
        <tr><th class="Title">'.get_text('ManDivClass','Tournament').'</th></tr>
    </table>
    <br>
    <table class="Tabella">
    <tr><th class="Title" colspan="6">'.get_text('Divisions','Tournament').'</th></tr>
    <tr>
        <th class="wmin-5ch">'.get_text('Division').'</th>
        <th class="w-75">'.get_text('Descr','Tournament').'</th>
        <th class="wmin-5ch">'.get_text('Para','Records').'</th>
        <th class="wmin-5ch">'.get_text('Athlete').'</th>
        <th class="wmin-5ch">'.get_text('Progr').'</th>
        <th>&nbsp;</th>
    </tr>
    <tbody id="tbody_div" ref="D">';


$Select
    = "SELECT * "
    . "FROM Divisions "
    . "WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
    . "ORDER BY DivViewOrder ASC ";
$Rs=safe_r_sql($Select);

while ($MyRow=safe_fetch($Rs)) {
    echo '<tr id="Div_'.$MyRow->DivId.'" ref="'.$MyRow->DivId.'">
        <td class="Bold Center">'.$MyRow->DivId.'</td>
        <td><input type="text" '.$Disabled.' class="w-100" ref="DivDescription" maxlength="50" value="'.ManageHTML($MyRow->DivDescription).'" onBlur="UpdateField(this)"></td>
        <td class="Center"><select '.$Disabled.' ref="DivIsPara" class="w-100" onBlur="UpdateField(this)">
            <option value="0">'.get_text('No').'</option>
            <option value="1"'.($MyRow->DivIsPara?' selected':'').'>'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><select '.$Disabled.' ref="DivAthlete" class="w-100" onBlur="UpdateField(this)">
            <option value="0">'.get_text('No').'</option>
            <option value="1"'.($MyRow->DivAthlete?' selected':'').'>'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><input '.$Disabled.' type="number" ref="DivViewOrder" class="w-100" value="'.ManageHTML($MyRow->DivViewOrder).'" onBlur="UpdateField(this)"></td>
        <td class="Center">'.(defined('dontEditClassDiv') ? '&nbsp;' : '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(this)">').'</td>
        </tr>';
}

echo '</tbody>';
echo '<tr id="NewDiv" class="Spacer"><td colspan="6"></td></tr>';

if(!defined('dontEditClassDiv')) {
    echo '<tr>
        <td class="Center"><input type="text" id="New_DivId" class="w-100" maxlength="4"></td>
        <td><input type="text" id="New_DivDescription" class="w-100" maxlength="50"></td>
        <td class="Center"><select class="w-100" id="New_DivIsPara">
            <option value="0">'.get_text('No').'</option>
            <option value="1">'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><select class="w-100" id="New_DivAthlete">
            <option value="0">'.get_text('No').'</option>
            <option value="1">'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><input type="number" id="New_DivViewOrder" class="w-100"></td>
        <td class="Center"><input type="button" value="'.get_text('CmdSave').'" class="w-100" onClick="AddDiv();"></td>
        </tr>';
}

echo '</table>
    <br>
    <table class="Tabella">
    <tr><th class="Title" colspan="11">'.get_text('Classes','Tournament').'</th></tr>
    <tr>
    <th class="wmin-7ch">'.get_text('AgeClass', 'Tournament').'</th>
    <th class="wmin-5ch">'.get_text('Sex','Tournament').'</th>
    <th class="w-70">'.get_text('Descr','Tournament').'</th>
    <th class="wmin-5ch">'.get_text('Para', 'Records').'</th>
    <th class="wmin-5ch">'.get_text('Athlete').'</th>
    <th class="wmin-5ch">'.get_text('Progr').'</th>
    <th class="wmin-5ch">'.get_text('YearStart','Tournament').'</th>
    <th class="wmin-5ch">'.get_text('YearEnd','Tournament').'</th>
    <th class="w-15">'.get_text('ValidClass','Tournament').'</th>
    <th class="w-15">'.get_text('ValidDivisions','Tournament').'</th>
    <th>&nbsp;</th>
    </tr>
    <tbody id="tbody_cl" ref="C">';
	$Select
		= "SELECT * "
		. "FROM Classes "
		. "WHERE ClTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
		. "ORDER BY ClViewOrder ASC ";
	$Rs=safe_r_sql($Select);

while ($MyRow=safe_fetch($Rs)) {
    echo '<tr id="Cl_'.$MyRow->ClId.'" ref="'.$MyRow->ClId.'">
        <td class="Bold Center">'.$MyRow->ClId.'</td>
        <td><select '.$Disabled.' class="w-100" ref="ClSex" onChange="UpdateField(this)">
            <option value="0"'.($MyRow->ClSex==0 ? ' selected' : '').'>'.get_text('ShortMale','Tournament').'</option>
            <option value="1"'.($MyRow->ClSex==1 ? ' selected' : '').'>'.get_text('ShortFemale','Tournament').'</option>
            <option value="-1"'.($MyRow->ClSex==-1 ? ' selected' : '').'>'.get_text('ShortUnisex','Tournament').'</option>
            </select></td>
        <td><input '.$Disabled.' type="text" ref="ClDescription" class="w-100" maxlength="50" value="'.ManageHTML($MyRow->ClDescription).'" onBlur="UpdateField(this)"></td>
        <td class="Center"><select '.$Disabled.' ref="ClIsPara" class="w-100" onChange="UpdateField(this)">
            <option value="0">'.get_text('No').'</option>
            <option value="1"'.($MyRow->ClIsPara?' selected':'').'>'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><select '.$Disabled.' ref="ClAthlete" class="w-100" onChange="UpdateField(this)">
            <option value="0">'.get_text('No').'</option>
            <option value="1"'.($MyRow->ClAthlete?' selected':'').'>'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><input '.$Disabled.' type="number" ref="ClViewOrder" min="1" class="w-100" maxlength="3" value="'.ManageHTML($MyRow->ClViewOrder).'" onBlur="UpdateField(this)"></td>
        <td class="Center"><input '.$Disabled.' type="number" min="1" max="125" ref="ClAgeFrom" class="w-100" maxlength="3" value="'.$MyRow->ClAgeFrom.'" onBlur="UpdateClassAge(this)"></td>
        <td class="Center"><input '.$Disabled.' type="number" min="1" max="125" ref="ClAgeTo" class="w-100" maxlength="3" value="'.$MyRow->ClAgeTo.'" onBlur="UpdateClassAge(this)"></td>
        <td class="Center"><input '.$Disabled.' type="text" ref="ClValidClass" class="w-100" size="8" maxlength="255" value="'.$MyRow->ClValidClass.'" onBlur="UpdateValidClass(this)"></td>
        <td class="Center"><input '.$Disabled.' type="text" ref="ClValidDivision" class="w-100 ClValidDivision" maxlength="255" value="'.$MyRow->ClDivisionsAllowed.'" onBlur="UpdateValidDivision(this)"></td>
        <td class="Center">'. (defined('dontEditClassDiv') ? '&nbsp;' : '<img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(this)">').'</td>
    </tr>';
}

echo '</tbody>';

if(!defined('dontEditClassDiv')) {
    echo '<tr id="NewCl" class="Spacer"><td colspan="11"></td></tr>
        <tr>
        <td class="Bold Center"><input type="text" id="New_ClId" class="w-100" maxlength="6"></td>
        <td>
        <select class="w-100" id="New_ClSex">
        <option value="">---</option>
        <option value="0">'.get_text('ShortMale','Tournament').'</option>
        <option value="1">'.get_text('ShortFemale','Tournament').'</option>
        <option value="-1">'.get_text('ShortUnisex','Tournament').'</option>
        </select>
        </td>
        <td><input type="text" id="New_ClDescription" class="w-100" maxlength="50"></td>
        <td class="Center"><select class="w-100" id="New_ClIsPara">
            <option value="0">'.get_text('No').'</option>
            <option value="1">'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><select class="w-100" id="New_ClAthlete">
            <option value="0">'.get_text('No').'</option>
            <option value="1">'.get_text('Yes').'</option>
            </select></td>
        <td class="Center"><input type="text" id="New_ClViewOrder" class="w-100" maxlength="3"></td>
        <td class="Center"><input type="text" id="New_ClAgeFrom" class="w-100" maxlength="3"></td>
        <td class="Center"><input type="text" id="New_ClAgeTo" class="w-100" maxlength="3"></td>
        <td class="Center"><input type="text" id="New_ClValidClass" class="w-100" maxlength="16"></td>
        <td class="Center"><input type="text" id="New_ClValidDivision" class="w-100" maxlength="16"></td>
        <td class="Center"><input type="button" class="w-100" value="'.get_text('CmdSave').'" onClick="AddCl()"></td>
        </tr>';
}

echo '<tr  class="Spacer"><td colspan="11"></td></tr>
    <tr><td colspan="11" class="Center">
        <a class="Link mx-3" href="ManDistances.php">'.get_text('ManDistances','Tournament').'</a>
        <a class="Link mx-3" href="ManSubClass.php">'.get_text('ManSubClasses','Tournament').'</a>
    </td></tr>
    </table>
    </div>
    </div>
    <div id="idOutput"></div>';

include('Common/Templates/tail.php');
