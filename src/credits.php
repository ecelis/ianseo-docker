<?php
require_once('./config.php');
require_once('Common/Fun_Modules.php');
require_once('Common/GlobalsLanguage.inc.php');

$PAGE_TITLE='Credits';
$IncludeFA=true;

include('Common/Templates/head.php');

echo '<table class="Tabella">';

echo '<tr><th class="Title" colspan="3">' . get_text('Credits-IanseoTeam', 'Install') . '</th></tr>'
	. '<tr>'
		. '<th class="SubTitle" nowrap="nowrap" colspan="2">'
		. '<a href="https://www.ianseo.net/" target="_blank"><img src="Common/Images/ianseo-logo.png" width="150" hspace="10" alt="Ianseo Logo" border="0"/></a>'
		. '</th>'
		. '<td width="100%" style="font-size:120%">'
		. '<div><b>Version: </b>'.ProgramVersion.'</div>'
		. (defined('ProgramBuild') ? '<div><b>Build: </b>'.ProgramBuild.'</div>' : '')
		. '<div>' . get_text('Credits-FitarcoCredits', 'Install') . '</div>'
		. '<div>' . get_text('Credits-IanseoWorld', 'Install') . '</div>'
		. '</td>'
	. '</tr>';
/** Credits Beiter **/
if(module_exists("Barcodes")) {
	echo '<tr>'
			. '<th class="SubTitle" nowrap="nowrap" colspan="2">'
			. '<a href="http://www.wernerbeiter.com/" target="_blank"><img src="Modules/Barcodes/beiter.png" width="150" hspace="10" alt="Beiter Logo" border="0"/></a>'
			. '</th>'
			. '<td width="100%" style="font-size:120%">'
			. '<div>' . get_text('Credits-BeiterCredits', 'Install') . '</div>'
			. '</td>'
		. '</tr>';
}

echo '<tr><th class="Title" colspan="3">IANSEO</th></tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-Coordination', 'Install') . '</th>'
	. '<td><b>Matteo Pisani</b></td>'
	. '</tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-Development', 'Install') . '</th>'
	. '<td><b>Matteo Pisani</b>
			<br/><b>Christian "Doc" Deligant</b></td>'
	. '</tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-ISK', 'Install') . '</th>'
	. '<td><b>Ken Sentell</b><br/><b>Matteo Pisani</b><br/><b>Christian "Doc" Deligant</b></td>'
	. '</tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-DebugManager', 'Install') . '</th>'
	. '<td><b>Andrea Gabardi</b></td>'
	. '</tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-TranslationCoordination', 'Install') . '</th>'
	. '<td class="Bold">Christian Deligant</td>'
	. '</tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-Support', 'Install') . '</th>'
	. '<td>
		<b><a href="mailto:help@ianseo.net"><i class="fa fa-envelope-open"></i>&nbsp;English</a>: </b> Andrea Gabardi, Christian Deligant, Ken Sentell, 
		<b><a href="mailto:ayuda@ianseo.net"><i class="fa fa-envelope-open"></i>&nbsp;Español</a>: </b> Alex Vecchio Passerini, 
		<b><a href="mailto:ajuda@ianseo.net"><i class="fa fa-envelope-open"></i>&nbsp;Portugues</a>: </b> Alex Vecchio Passerini, 
		<b><a href="mailto:aiuto@ianseo.net"><i class="fa fa-envelope-open"></i>&nbsp;Italiano</a>: </b> Andrea Gabardi, Christian Deligant
		</td>'
	. '</tr>';
echo '<tr><th class="SubTitle" nowrap="nowrap" colspan="2">' . get_text('Credits-Documentation', 'Install') . '</th>'
	. '<td class="Bold">Ardingo Scarzella, Luca Gallarate, Alessandra Pandolfi, et al.</td>'
	. '</tr>';

/**  get the contributed credits **/
// First the localised rules
// $glob=glob($CFG->DOCUMENT_PATH . 'Modules/Sets/*/credits.php');
// if($glob) {
// 	foreach($glob as $file) {
// 		if($credit = get_credit_details($file)) {
// 			echo '<tr>'
// 					. '<th class="SubTitle" nowrap="nowrap" colspan="2">'
// 					. $credit->img
// 					. '</th>'
// 					. '<td width="100%" style="font-size:120%">'
// 					. $credit->txt
// 					. '</td>'
// 				. '</tr>';
// 		}
// 	}
// }

echo '<tr><th class="Title" colspan="3">' . get_text('Credits-Translators', 'Install') . '</th></tr>';

// check if the new mechanism is in place
if(file_exists($CFG->LANGUAGE_PATH.'/en/translators.json')) {
	echo '<tr>
		<th colspan="2" class="SubTitle" width="2%"><img src="Common/Languages/en/en.png" alt="English" Title="English" /></th>
		<td><b>Ianseo Team</b></td>
		</TR>';
	foreach(glob($CFG->LANGUAGE_PATH.'/*/translators.json') as $File) {
		if(basename(dirname($File))=='en') {
			continue;
		}
		$Tr=json_decode(file_get_contents($File), true);
		echo '<tr>
			<th colspan="2" class="SubTitle" align="right"><img src="Common/Languages/'.$Tr['id'].'/'.$Tr['id'].'.png" alt="'.$Tr['lang'].'" title="'.$Tr['lang'].'" /></th>
			<td><b>'.implode('&nbsp;- ', $Tr['peop']).'</b></td>
			</tr>';
	}
} else {
	include('Common/Languages/credits.php');
}

echo '<tr class="Divider"><td colspan="3"></td></tr>'
	. '<tr><th class="SubTitle" colspan="3">' . get_text('Credits-License', 'Install') . '</th></tr>'
	. '<tr><td class="Center" colspan="3"><a href="http://www.gnu.org" target="_blank"><img src="Common/Images/gplv3.png" alt="GPLv3" border="0"></a></td></tr>'
	. '</table>';

include('Common/Templates/tail.php');

function get_credit_details($file) {
	include($file);
	if(!empty($credit)) {
		return $credit;
	}
}
