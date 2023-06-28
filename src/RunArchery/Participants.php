<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');

$IncludeJquery=true;

CheckTourSession(true);
checkACL(AclParticipants, AclReadOnly);

$SesNo=0;
$SmallCellW=0;

$sessions=GetSessions('Q');

$SesNo=count($sessions);

switch ($SesNo)
{
	case 1:
	case 2:
	case 3:
		$SmallCellW = 20;
		break;
	case 4:
	case 5:
		$SmallCellW = 15;
		break;
	case 6:
	case 7:
		$SmallCellW = 10;
		break;
	case 8:
	case 9:
		$SmallCellW = 7;
		break;
	default:
		$SmallCellW = 4;
}

$ComboSessions= '<select name="Session">';
$ComboSessions.='<option value="All">' . get_text('AllSessions','Tournament') . '</option>';
foreach ($sessions AS $s)
{
	$ComboSessions.='<option value="' . $s->SesOrder. '">' . $s->Descr . '</option>';
}
$ComboSessions.='</select>';

$PAGE_TITLE=get_text('PrintList','Tournament');

$JS_SCRIPT[]='<script src="Participants.js"></script>';

include('Common/Templates/head.php');

echo '<table class="Tabella my-4">';
echo '<tr><th class="Title" colspan="5">'.get_text('PrintList','Tournament').'</th></tr>';
echo '<tr>';
echo '<th class="SubTitle" width="20%">'.get_text('ParticipantList','RunArchery').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('StartlistCountry','Tournament').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('StartlistAlpha','Tournament').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('StartlistTeam','Tournament').'</th>';
// echo '<th class="SubTitle" width="20%">'.get_text('PartecipantListError','Tournament').'</th>';
echo '</tr>';

echo '<tr>';
echo '<td class="Center" ref="PDFSession">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdf.gif" alt="'. get_text('ParticipantList','RunArchery').'" border="0"></div>
    </td>';

echo '<td class="Center" ref="PDFCountry">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdf.gif" alt="'. get_text('StartlistCountry','Tournament').'" border="0"></div>';
// echo '<div class="my-1">'.get_text('Country').'<input name="CountryName" type="text" size="10" class="ml-1 mr-3">'.get_text('SortBy').'<select name="MainOrder" class="ml-1">
//         <option value="0">'.get_text('CountryCode').'</option>
//         <option value="1">'.get_text('Nation').'</option>
//     </select></div>
//     <div style="display: inline-flex;align-items: end">
//         <div class="mx-3">'.get_text('SinglePage', 'Tournament').'<br/><input name="SinglePage" type="checkbox"></div>
//         <div class="mx-3">'.get_text('MissingPhoto', 'Tournament').'<br/><input name="NoPhoto" type="checkbox"></div>
//         <div class="mx-3">'.get_text('Email', 'Tournament').'<br/><input name="Email" type="checkbox"></div>
//     </div>
//     <div><div class="Button" onclick="openPDF(this,1)">'.get_text('CmdOk').'</div></div>
//     ';
echo '</td>';

echo '<td class="Center" ref="PDFAlpha">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdf.gif" alt="'. get_text('StartlistAlpha','Tournament').'" border="0"></div>';
// echo '
//     <div>'.get_text('Archers').'<input name="ArcherName" class="ml-2" type="text" size="20" maxlength="30"></div>
//     <div>'.get_text('Country').'<input name="Country" class="ml-2" type="text" size="20" maxlength="30"></div>
//     <div>'.get_text('Event').'<input name="Event" class="ml-2" type="text" size="20" maxlength="30"></div>
//     <div><div class="Button" onclick="openPDF(this,1)">'.get_text('CmdOk').'</div></div>
//     ';
echo '</td>';

echo '<td class="Center" ref="PDFEvent">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdf.gif" alt="'. get_text('StartlistTeam','Tournament').'" border="0"></div>';
// echo '
//     <div style="display: inline-flex;align-items: end">
//         <div class="mx-3">'.get_text('SinglePage', 'Tournament').'<br/><input name="SinglePage" type="checkbox"></div>
//         <div>'.get_text('SortBy').'<br/>
//         <select name="MainOrder">
//             <option value="0">'.get_text('Athlete').'</option>
//             <option value="1">'.get_text('CountryCode').'</option>
//             <option value="2">'.get_text('Nation').'</option>
//         </select></div>
//     </div>
//     <div><div class="Button" onclick="openPDF(this,1)">'.get_text('CmdOk').'</div></div>
//     ';
echo '</td>';

// echo '<td class="Center" ref="PDFErrors">
//     <div onclick="openPDF(this,0)"><img src="../Common/Images/pdf.gif" alt="'. get_text('PartecipantListError','Tournament').'" border="0"></div>
//     </td>';
echo '</tr>';
echo '</table>';

/*
echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="5">'.get_text('StdORIS','Tournament').'</th></tr>';

echo '<tr>';
echo '<th class="SubTitle" width="20%">'.get_text('ParticipantList','RunArchery').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('StartlistCountry','Tournament').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('StartlistAlpha','Tournament').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('StartlistTeam','Tournament').'</th>';
echo '<th class="SubTitle" width="20%">'.get_text('ListCountries','Tournament').'</th>';
echo '</tr>';


echo '<tr>';

echo '<td class="Center" ref="OrisSession">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdfOris.gif" alt="'. get_text('StartlistSession','Tournament').'" border="0"></div>
    </td>';

echo '<td class="Center" ref="OrisCountry">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdfOris.gif" alt="'. get_text('StartlistCountry','Tournament').'" border="0"></div>
    <div class="my-2" style="display: inline-flex;align-items: flex-end">
        <div class="mx-3">'.get_text('SinglePage', 'Tournament').'<br/><input name="SinglePage" type="checkbox" checked="checked"></div>
        <div class="mx-2">'. get_text('DOB', 'Tournament').'<br><input type="checkbox" id="CoDoB" checked="checked"></div>
        <div class="mx-2">'. get_text('Contacts', 'Tournament').'<br><input type="checkbox" id="CoContacts" checked="checked"></div>
        <div class="mx-2">'. get_text('MissingPhoto', 'Tournament').'<br><input type="checkbox" id="CoMissing" checked="checked"></div>
        <div class="mx-2">'. get_text('PhotoRetake', 'Tournament').'<br><input type="checkbox" id="CoPictures" checked="checked"></div>
    </div>
    <div class="Button" onclick="openPDF(this,1)">'.get_text('Print', 'Tournament').'</div>
    </td>';

echo '<td class="Center" ref="OrisAlpha">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdfOris.gif" alt="'. get_text('StartlistAlpha','Tournament').'" border="0"></div>
    </td>';

$Events='';
$OldTeam=-1;
$q=safe_r_sql("select * from Events where EvTournament={$_SESSION['TourId']} order by EvTeamEvent,EvProgr");
while($r=safe_fetch($q)) {
    if($OldTeam!=$r->EvTeamEvent) {
        $Events.= '<option value="" disabled="disabled">'.get_text('IndTeam-'.$r->EvTeamEvent).'</option>';
	    $OldTeam=$r->EvTeamEvent;
    }
	$Events.= '<option value="'.$r->EvCode.'">'.$r->EvEventName.'</option>';
}
echo '<td class="Center" ref="OrisEvent">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdfOris.gif" alt="'. get_text('OrisTeamList','Tournament').'" border="0"></div>
    <div class="my-2"><select id="TeamEvents[]" multiple="multiple" size="8">'.$Events.'</select> </div>
    <div class="Button" onclick="openPDF(this,1)">'.get_text('Print', 'Tournament').'</div>
    </td>';

echo '<td class="Center" ref="OrisCountryList">
    <div onclick="openPDF(this,0)"><img src="../Common/Images/pdfOris.gif" alt="'. get_text('ListCountries','Tournament').'" border="0"></div>
    </td>';

echo '</tr>';

echo '</table>';
*/
include('Common/Templates/tail.php');

