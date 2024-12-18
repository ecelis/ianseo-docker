<?php

require_once(dirname(__FILE__).'/config.php');

// check of the UpdateDB is moved here for performance
require_once('Common/UpdateDb-check.php');

require_once('Common/Fun_FormatText.inc.php');
panicACL();
checkGPL();

//$PAGE_TITLE=get_text('TitleTourMenu', 'Tournament');

$JS_SCRIPT[]='<script>
    var dateVar = new Date();
    var offset = dateVar.getTimezoneOffset();
    document.cookie = "offset="+(offset*-1);
        </script>';

include('Common/Templates/head.php');

echo checkPhpVersion(true);

?>
<table class="Tabella">
<tr><th class="Title" colspan="6">
        <?php
        echo '<div style="font-size: large; line-height: 2em;">' . get_text('TitleTourMenu', 'Tournament') . '</div>' . ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '')
        ?>
    </th>
</tr>

<?php if (ProgramRelease!='HEAD' && CheckLastSWUpdate()) { ?>
	<tr class="Divider"><td colspan="6"></td></tr>
	<tr><td colspan="6" class="Bold Center"><?php print get_text('CheckUpdate','Common',$CFG->ROOT_DIR.'Update/');?></td></tr>
<?php }

if(empty($_SESSION['TourCode']) and ProgramRelease!='HEAD') {
	echo '<div class="WhatIanseoDoes">';
	echo '<h1>'.get_text('WhatIanseoDoesTitle', 'Install').'</h1>';
	echo '<div>'.get_text('WhatIanseoDoes01', 'Install').'</div>';
	if(ProgramRelease!='FITARCO') {
		echo '<div>'.get_text('WhatIanseoDoes02', 'Install', '<a href="mailto:help@ianseo.net">help@ianseo.net</a>').'</div>';
		echo '<div>'.get_text('WhatIanseoDoes03', 'Install').'</div>';
		echo '<div>'.get_text('WhatIanseoDoes04', 'Install', '<a href="https://www.ianseo.net" target="_blank">www.ianseo.net</a>').'</div>';
	} else {
		echo '<div>'.get_text('WhatIanseoDoes02', 'Install', '<a href="mailto:aiuto@ianseo.net">aiuto@ianseo.net</a>').'</div>';
	}
	echo '<div>'.get_text('WhatIanseoDoes05', 'Install', '<a href="https://www.facebook.com/ianseoarchery" target="_blank">Facebook</a>, <a href="https://twitter.com/IanseoArchery" target="_blank">Twitter</a>').'</div>';
	echo '<div>'.get_text('FreeOnlineResults', 'Install', '<b>'.get_text('MenuLM_Competition').'=>'.get_text('MenuLM_Send to ianseo.net').'</b>').'</div>';
	echo '</div>';
}


?>
<tr class="Divider"><td colspan="6"></td></tr>
<tr><td colspan="6"><a class="Link" href="<?php echo $CFG->ROOT_DIR ?>Tournament/index.php?New="><?php print get_text('NewTour', 'Tournament');?></a></td></tr>
<tr class="Divider"><td colspan="6"></td></tr>
<?php
$AuthFiler = array();
if($CFG->USERAUTH){
    if(empty($_SESSION['AUTH_User'])) {
        echo '<tr><th colspan="6"><a class="Link" href="'.$CFG->ROOT_DIR .'Modules/Authentication/LogIn.php">'.get_text('Login', 'Tournament').'</a></th></tr>';
        echo '<tr class="Divider"><td colspan="6"></td></tr>';
    }
    if(!empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
        $compList = array();
        foreach (($_SESSION["AUTH_COMP"] ?? array()) as $comp) {
            if (str_contains($comp, '%')) {
                $AuthFiler[] = 'ToCode LIKE ' . StrSafe_DB($comp);
            } else {
                $compList[] = $comp;
            }
        }
        if (count($compList)) {
            $AuthFiler[] = 'FIND_IN_SET(ToCode, \'' . implode(',', $compList) . '\') != 0 ';
        } else {
            $AuthFiler[] = "ToCode IS NULL ";
        }
    }
}
$Select
    = "SELECT ToId,ToType,ToCode,ToName,ToCommitee,ToComDescr,ToWhere,DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom, "
    . "DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo,ToTypeName AS TtName,ToNumDist AS TtNumDist "
    . "FROM Tournament "
    . (count($AuthFiler) ? 'WHERE ' . implode(' OR ', $AuthFiler) : '')
    . "ORDER BY ToWhenTo DESC, ToWhenFrom DESC, ToCode ASC";
$Rs=safe_r_sql($Select);
//print $Select;
if (safe_num_rows($Rs)>0) {
    echo '<tr>
        <th class="Title w-5">&nbsp;</th>
        <th class="Title w-10">'.get_text('TourCode','Tournament').'</th>
        <th class="Title w-30">'.get_text('TourName','Tournament').'</th>
        <th class="Title w-20">'.get_text('TourCommitee','Tournament').'</th>
        <th class="Title w-20">'.get_text('TourWhere','Tournament').'</th>
        <th class="Title w-15">'.get_text('TourWhen','Tournament').'</th>
        </tr>';
    while ($MyRow=safe_fetch($Rs)) {
        print '<tr>';
        print '<td><a class="Link" href="' . $CFG->ROOT_DIR . 'Common/TourOn.php?ToId=' . $MyRow->ToId . '">' . get_text('Open') . '</a></td>';
        print '<td>' . $MyRow->ToCode . '</td>';
        print '<td>' . ManageHTML($MyRow->ToName) . '</td>';
        print '<td>' . $MyRow->ToCommitee . ' - ' . ManageHTML($MyRow->ToComDescr) . '</td>';
        print '<td>' . ManageHTML($MyRow->ToWhere) . '</td>';
        print '<td>' . get_text('From','Tournament') . ' ' . $MyRow->DtFrom . ' ' . get_text('To','Tournament') . ' ' . $MyRow->DtTo . '</td>';
        print '</tr>' . "\n";
    }
}

echo '</table>';

include('Common/Templates/tail.php');