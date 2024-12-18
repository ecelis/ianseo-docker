<?php
require_once('./config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/CommonLib.php');

// Check if the new UUID has been called
if(!GetParameter('UUID2')) {
    require_once('Common/Lib/UpdateTournament.inc.php');
    GetNewUuidOnce();
}

CheckTourSession(true);
$aclLevel = checkACL(AclCompetition,AclNoAccess);

if(!empty($_REQUEST['redraw'])) {
    include_once('Common/CheckPictures.php');
    CheckPictures('', true, false, !empty($_REQUEST['force'])); // cancella le foto più vecchie di 1 giorno
}
$IncludeFA=true;
$PAGE_TITLE=get_text('TourMainInfo', 'Tournament');

include('Common/Templates/head.php');

$Countries=get_Countries();

$MyRow=NULL;
$Select = "SELECT *, 
    DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom,
    DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo, 
    DATE_FORMAT(ToWhenFrom,'%d') AS DtFromDay,
    DATE_FORMAT(ToWhenFrom,'%m') AS DtFromMonth,
    DATE_FORMAT(ToWhenFrom,'%Y') AS DtFromYear,
    DATE_FORMAT(ToWhenTo,'%d') AS DtToDay,
    DATE_FORMAT(ToWhenTo,'%m') AS DtToMonth,
    DATE_FORMAT(ToWhenTo,'%Y') AS DtToYear, 
    ToTypeName AS TtName,
    ToNumDist AS TtNumDist
    FROM Tournament
    WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

$Rs=safe_r_sql($Select);

if (safe_num_rows($Rs)==1)
{
    $MyRow=safe_fetch($Rs);
    echo '<table class="Tabella">
        <tr><th class="Title" colspan="2">'.(isset($_REQUEST['New']) ? get_text('NewTour', 'Tournament') : $MyRow->ToName).'</th></tr>
        <tr class="Divider"><td colspan="2"></td></tr>
        <tr><td class="Title" colspan="2">'.get_text('TourMainInfo', 'Tournament').'</td></tr>
        <tr>
            <th class="TitleLeft w-15">'.get_text('TourCode','Tournament').'</th>
            <td class="Bold">'.$MyRow->ToCode.'</td>
        </tr>
        <tr>
            <th class="TitleLeft w-15">'.get_text('TourName','Tournament').'</th>
            <td class="Bold">'.$MyRow->ToName.'</td>
        </tr>
        <tr>
            <th class="TitleLeft w-15">'.get_text('TourShortName','Tournament').'</th>
            <td class="Bold">'.$MyRow->ToNameShort.'</td>
        </tr>
        <tr>
            <th class="TitleLeft w-15">'.get_text('TourCommitee','Tournament').'</th>
            <td>'. $MyRow->ToCommitee . ' - ' . $MyRow->ToComDescr. '</td>
        </tr>';

        echo '<tr>
            <th class="TitleLeft w-15">'.get_text('TourType','Tournament').'</th>
            <td>'.get_text($MyRow->TtName, 'Tournament') . ', ' . $MyRow->TtNumDist . ' ' . get_text($MyRow->TtNumDist==1?'Distance':'Distances','Tournament').'</td>
            </tr>';
        echo '<tr>
            <th class="TitleLeft w-15">'.get_text('TourIsOris','Tournament').'</th>
            <td>'.get_text($_SESSION['ISORIS'] ? 'Yes' : 'No').'</td>
            </tr>';
        
    if(empty($_REQUEST['New']) and ($Anomalies=checkCompetitionAnomalies())) {
        echo '<tr class="Divider"><td colspan="2"></td></tr>';
        echo '<tr class="Dsq text-white"><td class="Center Bold" colspan="2">'.get_text('Anomalies', 'Errors').'</td></tr>';
        foreach($Anomalies as $v) {
            echo '<tr class="Dsq text-white">
                    <td class="Bold">'.$v['Msg'].'</td>
                    <td class="TargetKo"><i class="fa fa-link mr-2" onclick="location.href=\''.$v['Lnk'].'\'"></i>'.implode(', ', $v['Cats']).'</td>
                    </tr>';
        }
        echo '<tr class="Divider"><td colspan="2"></td></tr>';
    }

    echo '<tr>
            <th class="TitleLeft w-15">'.get_text('TourWhere','Tournament').'</th>
            <td>'.$MyRow->ToWhere.'</td>
            </tr>';
    
    echo '<tr>
        <th class="TitleLeft w-15">'.get_text('CompVenue','Tournament').'</th>
        <td>'.$MyRow->ToVenue.'</td>
        </tr>';

    echo '<tr>
        <th class="TitleLeft w-15">'.get_text('Natl-Nation','Tournament').'</th>
        <td>'.($MyRow->ToCountry ? $MyRow->ToCountry.' - '.$Countries[$MyRow->ToCountry] : $MyRow->ToCountry).'</td>
        </tr>';

    echo '<tr>
        <th class="TitleLeft w-15">'.get_text('TourWhen','Tournament').'</th>
        <td>'.get_text('From','Tournament') . ' ' . $MyRow->DtFrom . '<br>' . get_text('To','Tournament') . '&nbsp;&nbsp;&nbsp;' . $MyRow->DtTo.'</td>
        </tr>';

    echo '<tr>
        <th class="TitleLeft w-15">'.get_text('NumSession', 'Tournament').'</th>
        <td>'.$MyRow->ToNumSession.'</td>
        </tr>';

    echo '<tr>
        <th class="TitleLeft w-15">'.get_text('SessionDescr', 'Tournament').'</th>
        <td>';
    if ($MyRow->ToNumSession>0) {
        // info sessioni
        $sessions=GetSessions('Q');

        foreach ($sessions as $s)
        {
            echo get_text('Session') . ' ' . $s->SesOrder . ': ' . $s->SesName . ' --> ' . $s->SesTar4Session . ' ' . get_text('Targets', 'Tournament') . ', ' . $s->SesAth4Target . ' ' . get_text('Ath4Target', 'Tournament')  . '<br>';
        }
    } else {
        echo get_text('NoSession','Tournament');
    }
    echo '</td>
        </tr>';
        
    echo '<tr>
        <th class="TitleLeft w-15">'.get_text('StaffOnField','Tournament').'</th>
        <td>';
    $Select = "SELECT TiCode, TiName, TiGivenName, CoCode, ItDescription
		    FROM TournamentInvolved AS ti 
			LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId 
            left join Countries on CoId=TiCountry and CoTournament=TiTournament
			WHERE ti.TiTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
			ORDER BY ti.TiType ASC,ti.TiName ASC ";
    $RsS = safe_r_sql($Select);
    if (safe_num_rows($RsS)>0) {
        while ($Row=safe_fetch($RsS)) {
            echo (empty($Row->TiCode) ? '' : $Row->TiCode . '&nbsp;-&nbsp;') .
                $Row->TiName . ' ' . $Row->TiGivenName . (is_null($Row->CoCode) ? '' : ' (' . $Row->CoCode . ')') .
                (empty($Row->ItDescription) ? '' : ', ' . get_text($Row->ItDescription,'Tournament')) . '<br>';
        }
    }
    else
    {
        print get_text('NoStaffOnField','Tournament');
    }
    echo '</td>
        </tr>';

    if($aclLevel == AclReadWrite) {
        echo '<tr>';
        echo '<th class="TitleLeft w-15">' . get_text('Photo', 'Tournament') . '</th>';
        echo '<td><a href="?redraw=1" class="Link">' . get_text('RedrawPictures', 'Tournament') . '</a><br/><a href="?redraw=1&force=1" class="Link">' . get_text('RecreatePictures', 'Tournament') . '</a></td>';
        echo '</tr>';
    }
    if($INFO->ACLEnabled and ($_SERVER["REMOTE_ADDR"]!='::1' AND $_SERVER["REMOTE_ADDR"]!='127.0.0.1')) {
        echo '<tr class="Divider"><td colspan="2"></td></tr>';
        echo '<tr>';
        echo '<th class="TitleLeft w-15">'.get_text('Block_IP','Tournament').'</th>';
        echo '<td style="font-size: 200%;">'.$_SERVER["REMOTE_ADDR"].'</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '';
    echo '';
}

include('Common/Templates/tail.php');

