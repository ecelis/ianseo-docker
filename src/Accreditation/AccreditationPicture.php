<?php
require_once(dirname(__FILE__) . '/config.php');
checkACL(AclAccreditation, AclReadWrite);
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Modules.php');
require_once('Common/Fun_Sessions.inc.php');

CheckTourSession(true);

$param = array("source"=>0, "minW"=>400, "minH" => 400);
if(file_exists($CFG->DOCUMENT_PATH."Modules/Accreditation/includeAccreditationPicture.php")) {
	require_once(dirname(dirname(__FILE__)) . '/Modules/Accreditation/includeAccreditationPicture.php');
}

$TourIds = ((isset($_SESSION['AccreditationTourIds']) and !empty($_SESSION['AccreditationTourIds'])) ? $_SESSION['AccreditationTourIds'] : $_SESSION['TourId']);
// loads how many accreditation types there are
$Accreditations=array();
$Sessions=array();
foreach (explode(',',$TourIds) as $ToId) {
    $q = safe_r_sql("select IcTournament, IcNumber, IcName, ToCode
        FROM IdCards
        INNER JOIN Tournament on IcTournament = ToId 
        where IcTournament={$ToId} and IcType='A' order by IcNumber");
    while ($r = safe_fetch($q)) {
        $Accreditations[$r->IcTournament . "|" . $r->IcNumber] = $r->ToCode . ' - ' . $r->IcName;
    }
    $tmpSess = GetSessions('Q', false, null, $ToId);
    foreach ($tmpSess as $sess) {
        if(!array_key_exists($sess->SesOrder, $Sessions)) {
            $Sessions[$sess->SesOrder] = array();
        }
        $Sessions[$sess->SesOrder][$sess->SesTournament] = $sess->SesName;
    }
}

$SpecificCards=array();
foreach (explode(',',$TourIds) as $ToId) {
    $SpecificCards[$ToId]=array();
    if ($Specific = getModuleParameterLike('Accreditation', 'Matches-A-%', $ToId)) {
        foreach ($Specific as $Id => $Name) {
            $tmp = explode('-', $Id);
            foreach(explode(',', $Name) as $tmpCat) {
                $SpecificCards[$ToId][$tmpCat] = end($tmp);
            }
        }
    }
}

$PAGE_TITLE=get_text('TakePicture', 'Tournament');
$JS_SCRIPT[] = phpVars2js(array('ROOT_DIR' => $CFG->ROOT_DIR, 'AreYouSure'=>get_text('MsgAreYouSure'), 'msgPictureThere' => get_text('PictureThere', 'Tournament')));
$JS_SCRIPT[] = '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>';
$JS_SCRIPT[] = '<script type="text/javascript" src="./Fun_AJAX_AccreditationPicture.js"></script>';
$JS_SCRIPT[] = phpVars2js($param);
$JS_SCRIPT[] = '<script type="text/javascript">
    let cardsByCat='.json_encode($SpecificCards).';
    let sessByToId='.json_encode($Sessions).';
    </script>';
if($param["source"]==0) {
	$JS_SCRIPT[] = '<script type="text/javascript" src="./TakePicture.js"></script>';
} else {
	$JS_SCRIPT[] = '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Modules/Accreditation/TakePicture.js"></script>';
}
$JS_SCRIPT[]='<style>
	.Reverse td {background-color:#ddd; color:black;}
.rotate {
    position: absolute;
    width:20vmin;
    height:auto;
    top:50%;
    left:50%;
    margin-left:-10vmin;
    margin-top:-10vmin;
    -webkit-animation:spin 4s linear infinite;
    -moz-animation:spin 4s linear infinite;
    animation:spin 4s linear infinite;
}
@-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
@-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }
</style>';

$IncludeJquery=true;
$IncludeFA=true;
$ONLOAD = ' onLoad="javascript:searchAthletes();'.($param["source"]==0 ? 'setupVideo();':'').'"';
include('Common/Templates/head' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');

?>
<table class="Tabella Speaker">
<tr onClick="showOptions();"><th class=Title colspan="4"><?php echo get_text('TakePicture', 'Tournament');?></th></tr>
<tr class="Divider"><td colspan="2"></td></tr>
<tbody id="options">

<tr>
<th class="Title w-30"><?php echo get_text('Options', 'Tournament');?></th>
<th class="Title w-70"><?php echo get_text('FilterRules');?></th>
</tr>

<tr>
<td class="Center">

<?php
if($param["source"]==0) {
	echo get_text('Camera', 'Tournament');
	echo '<select id="videoSource"></select><br>';
}
echo '<div class="mb-2"><input type="checkbox" id="showMenu" ' . (isset($_REQUEST["showMenu"]) ? 'checked' : '') .
	' onClick="document.location=\'' . $_SERVER["PHP_SELF"]. (isset($_REQUEST["showMenu"]) ? '' : '?showMenu') . '\';">'.get_text('ShowIanseoMenu', 'Tournament').'<div>';
if(file_exists($CFG->DOCUMENT_PATH."Modules/Accreditation/AccreditationPictureParameters.php") and empty($_SESSION['ShortMenu']['ACCR'])) {
	echo '<br><span class="mx-2"><a href="'.$CFG->ROOT_DIR.'Modules/Accreditation/AccreditationPictureParameters.php">'.get_text('AdvancedParams', 'Tournament'). '</a></span>';
	echo '<span class="mx-2"><a onclick="window.BigPicture=window.open(\''.$CFG->ROOT_DIR.'Modules/Accreditation/BigPicture.php\', \'picture\', \'height=1024,width=1024\')">'.get_text('OpenPictureScreen', 'BackNumbers'). '</a></span>';
}
?>
</td>
<td class="Center">
<input type="text" name="x_Search" id="x_Search" style="width: 80%;" maxlength="50" onBlur="searchAthletes();" onkeyup="searchAthletes();"><br>
<input type="checkbox" id="x_Country" name="x_Country" value="1" checked onChange="searchAthletes();"><?php echo get_text('Country') ?>&nbsp;&nbsp;&nbsp;
<input type="checkbox" id="x_Athlete" name="x_Athlete" value="1" checked onChange="searchAthletes();"> <?php echo get_text('Name', 'Tournament') ?><br>
<?php

$TourId=$_SESSION['TourId'];
if($_SESSION['AccreditationTourIds']) {
	echo '<br/>';
	foreach(explode(',', $_SESSION['AccreditationTourIds']) as $id) {
		$Code=getCodeFromId($id);
		echo '<input type="checkbox" class="x_Tours" tourid="'.$id.'" id="x_Tour['.$id.']" value="'.$Code.'" onChange="searchAthletes();">'.$Code.'&nbsp;&nbsp;&nbsp;';
	}
	$TourId=$_SESSION['AccreditationTourIds'];
}

echo '<br/>';
echo '<input type="checkbox" class="x_Sessions" id="x_Sessions[0]" onChange="searchAthletes();">'.get_text('Session').' 0&nbsp;&nbsp;&nbsp;';
$q=safe_r_sql("select distinct SesOrder from Session where SesTournament in ($TourId) and SesType='Q' order by SesOrder");
while($r=safe_fetch($q)) {
	echo '<span id="sesBlock'.$r->SesOrder.'"><input type="checkbox" class="x_Sessions" id="x_Sessions['.$r->SesOrder.']" onChange="searchAthletes();">'.get_text('Session').' '.$r->SesOrder.'<span id="lblSess'.$r->SesOrder.'"></span>&nbsp;&nbsp;&nbsp;</span>';
}
echo '<div class="Flex-line w-100 mt-2">'.
    '<div class="w-20"><input type="checkbox" id="x_2BPrinted" name="ToBePrinted" onChange="searchAthletes();">'. get_text('BadgeOnlyNotPrinted', 'Tournament'). '</div>'.
    '<div class="w-20"><input type="radio" id="x_All" name="PhotoStatus" onChange="searchAthletes();">'. get_text('AllEntries','Tournament'). '</div>'.
    '<div class="w-20"><input type="radio" id="x_noPhoto" name="PhotoStatus" onChange="searchAthletes();">'.get_text('OnlyWithoutPhoto', 'Tournament'). '</div>'.
    '<div class="w-20"><input type="radio" id="x_NoPrint" name="PhotoStatus" onChange="searchAthletes();">'.get_text('OnlyPhoto', 'Tournament'). '</div>'.
    '<div class="w-20"><input type="radio" id="x_noAcc" name="PhotoStatus" checked onChange="searchAthletes();">'.get_text('OnlyWithoutAcc','Tournament'). '</div>'.
    '</div>';
?>
</td>
</tr>
</tbody>
</table>

<table class="Tabella Speaker">
	<tr>
		<th class="Title w-35"><?php echo get_text('Camera', 'Tournament');?></th>
		<th class="Title w-30"><?php echo get_text('Photo', 'Tournament');?></th>
		<th class="Title w-35"><?php echo get_text('MenuLM_Partecipant List');?> <span id="missingPhotos"></span></th>
	</tr>
<tbody id="tbody">
	<tr>
		<td style="vertical-align: top; text-align: center;">
			<input type="button" id="stop-button" value="<?php echo get_text('StopCamera', 'Tournament')?>" onClick="stopVideo();" style="display: none; margin-bottom: 5px;">
			<input type="button" id="start-button" value="<?php echo get_text('StartCamera', 'Tournament')?>" onClick="startVideo();" style="display: none; margin-bottom: 5px;">
			<br><i class="fas fa-2x fa-minus-circle mr-3" onclick="subZoom();"></i><input id="zoom" type="range" min="1" max="20" style="display:none;" value="1" onChange="changeZoom()"/><i class="fas fa-2x fa-plus-circle ml-3" onclick="addZoom();"></i><br>
			<div id="cameraContainer" style="position: relative;" onClick="takePicture();">
				<video id="CamVideo" crossOrigin="Anonymous" width="100%" autoplay style="position: absolute; top: 0px; left: 0px;" ></video>
 				<img id="ImgCamVideo" crossOrigin="Anonymous" width="100%" style="position: absolute; top: 0px; left: 0px; display:none;">
				<svg id="face" version="1.1" xmlns="http://www.w3.org/2000/svg" style="position: absolute; display:none; top: 0px; left: 0px; width:400px; height: 400px;" viewBox="0 0 400 400" >
					<rect width="400" height="400"  style="fill:none;stroke:orange;stroke-width:2"/>
					<rect x="50" width="300" height="400"  style="fill:none;stroke:yellow;stroke-width:2"/>
					<line x1="125" y1="172" x2="275" y2="172" style="stroke:rgb(255,0,0);stroke-width:1" />
					<line x1="200" y1="160" x2="200" y2="180" style="stroke:rgb(255,0,0);stroke-width:1" />
					<text font-size="10" fill="red" x="200" y="172" text-anchor="middle">eyes line</text>
					<line x1="145" y1="270" x2="255" y2="270" style="stroke:rgb(255,0,0);stroke-width:1" />
					<text font-size="10" fill="red" x="200" y="270" text-anchor="middle">lips line</text>
					<path d="M 50,400 L 150,355 l 0,-50 C 115,280 90,230 90,178 c 0,-75 50,-137 112,-137 c 62,0 112,60 112,137 c -0,50 -25,101 -62,124 L 250,355 L 350,400"  style="fill:none;stroke:yellow;stroke-width:2"/>
				</svg>
			</div>

		</td>
		<td style="vertical-align: top; text-align: center;">
			<input type="hidden" id="selId">
			<table class="Tabella">
				<tr><th id="selAth" class="w-40"></th><td id="selCat" class="w-20"></td><td id="selTeam" class="w-40"></td></tr>
			</table>
			<div id="loadingBar" class="blue LetteraGrande" style="display: none;"></div>
			<br><img id="athPic" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" width="150">
			<div id="ManBlock" style="display: none;">
			<div class="mt-2 mb-3"><input type="button" id="delete-button" value="<?php echo get_text('PhotoDelete', 'Tournament')?>" onClick="deletePicture();" ></div>
            </div>
            <div id="PrnBlock" style="display: none;">
                <?php

            if($Accreditations) {
				if(count($Accreditations)>1) {
					echo '<select id="accreditation-number" class="m-2">';
					foreach($Accreditations as $k => $v) {
						echo '<option value="'.$k.'">'.$v.'</option>';
					}
					echo '</select>';
				} else {
					foreach($Accreditations as $k => $v) {
						echo '<input type="hidden" id="accreditation-number" value="'.$k.'">';
					}
				}
				echo '<input type="button" id="print-button" class="m-2" value="'.get_text('Print', 'Tournament').'" onClick="printAccreditation()" >';
                if(module_exists('Automator')) {
                    $AccPrinters = getModuleParameter('Automator', 'AutomatorAccPrint','');
                    if(!empty($AccPrinters)) {
                        foreach(explode(',', $AccPrinters) as $kPrn => $printer) {
                            $tmp=explode('|',$printer);
                            echo '<br><input type="button" id="prnAuto'.$kPrn.'" class="m-2" value="'.$tmp[0].'" onClick="printAccreditationAuto(\''.$tmp[1].'\')" >';
                        }
                    }
                }
			}

			?>

			&nbsp;&nbsp;<div class="m-3"><input type="button" id="confirm-button" value="<?php echo get_text('BadgeConfirmPrinted', 'Tournament')?>" onClick="ConfirmPrinted()" style="display: none;"></div>
			</div>
			<canvas id="screenshot-canvas" style="display: none;"></canvas>
		</td>
		<td style="vertical-align: top;">
			<table class="Tabella" id="List">
			<thead><tr>
				<th colspan="2"><?php echo get_text('Name','Tournament')?></th>
                <th><?php echo get_text('Code','Tournament'); ?></th>
				<th><?php echo get_text('DivisionClass'); ?></th>
				<th><?php echo get_text('Country')?></th>
                <th colspan="3"><?php echo 	get_text('TourCode', 'Tournament'); ?></th></tr></thead>
			<tbody id="ListBody"></tbody>
			</table>
		</td>
	</tr>
</tbody>
</table>

<?php

include('Common/Templates/tail' . (isset($_REQUEST["showMenu"]) ? '': '-min') . '.php');
?>
