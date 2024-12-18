<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Tournament/Fun_Tournament.local.inc.php');
require_once('Common/Lib/CommonLib.php');
checkACL(AclCompetition, AclReadWrite);

if (!CheckTourSession()) {
    print get_text('CrackError');
    exit;
}

$IncludeJquery = true;
$JS_SCRIPT = array(
	phpVars2js(array(
		'MsgAreYouSure' => get_text('MsgAreYouSure'),
		'MsgRowMustBeComplete' => get_text('MsgRowMustBeComplete'),
        'MsgInvalidCharacters' => get_text('InvalidCharacters','Errors'),
        'CmdOk' => get_text('CmdOk'),
        'CmdCancel' => get_text('CmdCancel'),
        'Warning' => get_text('Warning', 'Tournament'),
	)),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManDivClass.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    );

$PAGE_TITLE=get_text('ManSubClasses','Tournament');

include('Common/Templates/head.php');

?>
<div align="center">
<div class="half">
<table class="Tabella">
<tr><th class="Title" colspan="4"><?php print get_text('ManSubClasses','Tournament');?></th></tr>
<tr><th class="Title" colspan="4"><?php print get_text('SubClasses','Tournament');?></th></tr>
<?php

$Rs=safe_r_sql("SELECT * FROM `SubClass` WHERE ScTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY ScViewOrder ASC ");

echo '<tr>
    <th class="wmin-5ch">' . get_text('SubClass','Tournament') . '</th>
    <th class="w-90">'. get_text('Descr','Tournament') . '</th>
    <th class="wmin-5ch">' . get_text('Progr') . '</th>
    <th>&nbsp;</th>
    </tr>';
echo '<tbody id="tbody_subclass" ref="SC">';
while ($MyRow=safe_fetch($Rs)) {
    echo '<tr id="SubClass_' . $MyRow->ScId .'" ref="' . $MyRow->ScId .'">'.
            '<td class="Bold Center">'. $MyRow->ScId . '</td>'.
            '<td><input type="text" ref="ScDescription" class="w-100" maxlength="32" value="'.$MyRow->ScDescription .'" onBlur="UpdateField(this)"></td>'.
            '<td class="Center"><input type="number" ref="ScViewOrder" class="w-100" maxlength="3" value="'. $MyRow->ScViewOrder . '" onBlur="UpdateField(this)"></td>'.
            '<td class="Center"><img src="'.$CFG->ROOT_DIR.'Common/Images/drop.png" border="0" alt="#" title="#" onclick="DeleteRow(this)"></td>'.
        '</tr>';
}
echo '</tbody>';
?>
<tr id="NewSubCl" class="Spacer"><td colspan="4"></td></tr>
<tr>
<td class="Center"><input type="text" id="New_ScId" class="w-100" maxlength="2"></td>
<td><input type="text" id="New_ScDescription" class="w-100" maxlength="32"></td>
<td class="Center"><input type="number" id="New_ScViewOrder" class="w-100" maxlength="3"></td>
<td class="Center"><input type="button" name="CommandSc" value="<?php print get_text('CmdSave');?>" onClick="AddSubClass()"></td>
</tr>
</table>

</div>
</div>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
