<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

if (defined('hideSpeaker')) {
	header('location: /index.php');
	exit;
}

if(!CheckTourSession()) {
	// opens a session based on the competition saved in InfoSystem Setup
	if($IsCode=GetIsParameter('IsCode') and $TourId=getIdFromCode($IsCode)) {
		CreateTourSession($TourId);
	}
}
checkACL(AclSpeaker, AclReadOnly);

$IncludeJquery=true;
$IncludeFA=true;
$PAGE_TITLE=get_text('MenuLM_Speaker');
$JS_SCRIPT=array(
	phpVars2js(array(
		'UpdateTimeout'=>(empty($CFG->ONLINE) ? 2500 : 30000),
		'RootDir'=>$CFG->ROOT_DIR
	)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Modules/Speaker/robin.js"></script>',
	'<link href="speaker.css" media="screen" rel="stylesheet" type="text/css">',
);

if(empty($CFG->IS)) {
	$JS_SCRIPT[]='<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
	include('Common/Templates/head' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
} else {
	include_once ("Common/Styles/head.php");
}
?>


	<table class="Tabella Speaker">
		<tr onClick="showOptions();"><th class=Title colspan="4"><?php print get_text('MenuLM_Speaker');?></th></tr>
		<tr class="Divider"><td colspan="4"></td></tr>
		<tbody id="options">
		<tr>
			<th class="Title" width="30%"><?php print get_text('Schedule', 'Tournament');?></th>
			<th class="Title" width="30%"><?php print get_text('Events', 'Tournament');?></th>
			<th class="Title" width="30%"><?php print get_text('Options', 'Tournament');?></th>
			<th class="Title" width="10%">&nbsp;</th>
		</tr>
		<tr>
			<td class="Center">
				<input type="hidden" id="lu" value="0">
				<select name="x_Schedule" id="x_Schedule" onChange="GetEvents();"></select><br>
				<?php
				if($IskSequence=getModuleParameter('ISK', 'Sequence') OR $IskSequence=getModuleParameter('ISK-NG', 'Sequence')) {
					echo '<input type="button" id="currentSession" onClick="GetSchedule(true);" value="'.get_text('GoToRunning','Tournament').'"><br>';
				}
				?>
				<input type="checkbox" id="onlyToday" checked onClick="GetSchedule();"><?php print get_text('OnlyToday','Tournament');?><br>
			</td>
			<td class="Center"><select name="x_Events" id="x_Events" multiple="multiple" onChange="GetMatches(true)"></select><br><div class="Link" onclick="SelectAllOpt(this);"><?php print get_text('SelectAll');?></div></td>
			<td class="Center">
				<?php
				if(empty($CFG->IS)) {
					echo '<input type="checkbox" id="showMenu" ' . (isset($_REQUEST["showMenu"]) ? 'checked' : '') .
						' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. (isset($_REQUEST["showMenu"]) ? '' : '?showMenu') . '\';"' .
						'>&nbsp;';
					echo get_text('ShowIanseoMenu', 'Tournament');
				}
				?>
				<br>&nbsp;<br>
				<input type="checkbox" id="pauseUpdate" onClick="pauseRefresh(this);"><?php  print get_text('StopRefresh','Tournament');?>
			</td>
			<td class="Center"><input type="button" value="<?php  print get_text('CmdOk');?>" onClick="GetMatches();"></td>
		</tr>
		<tr>
		</tr>
		</tbody>
	</table>
	<table class="Tabella Speaker">
		<tr>
			<th class="Title" width="5%"><?php print get_text('Status', 'Tournament');?></th>
			<th class="Title" width="10%"><?php print get_text('Event');?></th>
			<th class="Title" width="5%"><?php print get_text('Target');?></th>
			<th class="Title" width="25%">&nbsp;</th>
			<th class="Title" width="10%"><?php print get_text('TotalShort', 'Tournament');?></th>
			<th class="Title" width="15%"><?php print get_text('SetPoints', 'Tournament');?></th>
			<th class="Title" width="5%"><?php print get_text('Target');?></th>
			<th class="Title" width="25%">&nbsp;</th>
		</tr>
		<tbody id="tbody">
		<tr id="RowDiv" class="Divider"><td colspan="8"></tr>
		</tbody>
	</table>

<?php

if(empty($CFG->IS)) {
	include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
} else {
	include_once ($CFG->DOCUMENT_PATH . "Common/Styles/tail.php");
}