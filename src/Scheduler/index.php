<?php
/*

// TO ADD
get_text('Freetext', 'Tournament')

NEW VERSION

Each schedule line can be of type:
- Free text
--- has a date & time attribute for start
--- has a duration attribute
--- has a title and a text, at least one should be filled
--- has a "show time" flag
- Tournament Object
--- same as above
--- Q: Qualification
----- Session (name)
----- Distance and Category
----- group flag
--- E: Eliminiation
----- Session (name)
----- Categories
----- group flag
--- M: Matches
----- Events
----- Phase
----- Group flag

*/
require_once(dirname(dirname(__FILE__)) . '/config.php');
$aclLevel = checkACL(AclCompetition, AclReadOnly);

CheckTourSession(true);

if(!empty($_REQUEST['Activate'])) {
	if(empty($_SESSION['ActiveSession'])) {
		$_SESSION['ActiveSession']=array();
	}
	if(in_array($_REQUEST['Activate'], $_SESSION['ActiveSession'])) {
		unset($_SESSION['ActiveSession'][array_search($_REQUEST['Activate'], $_SESSION['ActiveSession'])]);
	} else {
		$_SESSION['ActiveSession'][]=$_REQUEST['Activate'];
	}
	Set_Tournament_Option('ActiveSession', $_SESSION['ActiveSession']);
    runJack("ScheduleRunUpdate", $_SESSION['TourId'], array("ActiveSession"=>$_SESSION['ActiveSession'], "TourId"=>$_SESSION['TourId']));
	CD_redirect(basename(__FILE__));
}

require_once('Common/Lib/Fun_Scheduler.php');
require_once('./LibScheduler.php');
require_once('Common/Lib/Fun_Modules.php');

if(!empty($_REQUEST['fop'])) {
	$Sched=new Scheduler();

	// defines the days
	if(!empty($_REQUEST['Days'])) {
		$DaysToPrint=array();
		foreach($_REQUEST['Days'] as $k => $v) {
			$Sched->DaysToPrint[]=date('Y-m-d', $_SESSION['ToWhenFromUTS'] + $k*86400);
		}
	}

	// defines the Locations (these will be printed on a single page)
	if(!empty($_REQUEST['Locations'])) {
		foreach($_REQUEST['Locations'] as $k=>$v) {
			$Sched->LocationsToPrint[]=$Sched->FopLocations[$k];
		}
		$Sched->SplitLocations=true;
	}

	if(!empty($_REQUEST['day'])) {
		if(strtolower(substr($_REQUEST['day'], 0, 1))=='d') {
			$Date=date('Y-m-d', strtotime(sprintf('%+d days', substr($_REQUEST['day'], 1) -1), $_SESSION['ToWhenFromUTS']));
		} else {
			$Date=CleanDate($_REQUEST['day']);
		}
		if($Date) $Sched->SingleDay=$Date;
	}
	$Sched->FOP();
	die();
}

if(!empty($_REQUEST['ods'])) {
	$Schedule=new Scheduler();
	$Schedule->exportODS($_SESSION['TourCode'].'.ods', 'a');
	die();
}

if(!empty($_REQUEST['ics'])) {
	$Schedule=new Scheduler();
	$Schedule->getScheduleICS(true);
	die();
}

$edit=(empty($_REQUEST['key']) ? '' : preg_replace('#[^0-9:| -]#sim', '', $_REQUEST['key']));

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Scheduler/Fun_AJAX_Scheduler.js"></script>',
	'<link href="'.$CFG->ROOT_DIR.'Scheduler/Scheduler.css" media="screen" rel="stylesheet" type="text/css">',
	phpVars2js([
		'titAdvanced' => get_text('Advanced'),
		'labelTargets' => get_text('Targets', 'Tournament').': #1-#N@Dist[@Cat[@Face]]',
		'labelLocation' => get_text('Location', 'Tournament'),
		'btnSubmit' => get_text('CmdUpdate'),
		'btnCancel' => get_text('CmdCancel'),
	]),
);

$IncludeFA=true;
$IncludeJquery=true;

include('Common/Templates/head.php');

$PageBreaks=getModuleParameter('Schedule', 'PageBreaks', '');

echo '<table class="Tabella">
	<tr><th class="Main" colspan="10">'.get_text('Scheduler').'</th></tr>
	<tr class="Divider"><td colspan="10"></td></tr>
	<tr class="Divider"><td colspan="3"><form action="./PrnScheduler.php" target="PDF">
			<b>'.get_text('MenuLM_PrintScheduling').' (<a href="'.$CFG->ROOT_DIR.'Final/ManTraining.php" target="Warmup">'.get_text('MenuLM_Training').'</a>):</b><br/>
			<input type="checkbox" name="Finalists" checked="checked">'.get_text('SchIncFinalists','Tournament').'&nbsp;&nbsp;
			<input type="checkbox" name="Ranking">'.get_text('SchAddRank','Tournament').'&nbsp;&nbsp;
			<input type="checkbox" name="Daily">'.get_text('DailySchedule', 'Tournament').'&nbsp;&nbsp;
			<input type="checkbox" name="NoLocations">'.get_text('NoLocations', 'Tournament').'&nbsp;&nbsp;
			<input type="button" name="ODS" value="'.get_text('MenuLM_OdsExport').'" onclick="location.href=\'?ods=1\'">&nbsp;&nbsp;
			<input type="button" name="ICS" value="'.get_text('ExportICS', 'Tournament').'" onclick="location.href=\'?ics=1\'">&nbsp;&nbsp;
			<input type="button" name="FOP" value="'.get_text('PrintFOP', 'Tournament').'" onclick="window.open(\'./?fop=1&day=\'+document.getElementById(\'FromDayDay\').value)">&nbsp;&nbsp;<br/>
			<input type="submit" name="Complete" value="'.get_text('CompleteSchedule', 'Tournament').'">&nbsp;&nbsp;
			<input type="submit" name="DailySchedule" value="'.get_text('DailySchedule', 'Tournament').'">&nbsp;&nbsp;
			<input type="submit" name="FinalSchedule" value="'.get_text('FinalSchedule', 'Tournament').'">&nbsp;&nbsp;
			'.get_text('PageBreakBeforeDays', 'Tournament').' (yyyy-mm-dd[,yyyy-mm-dd...])&nbsp;&nbsp;<input type="text" size="40" name="PageBreaks" value="'.$PageBreaks.'"><br>&nbsp;<br>
			<input type="submit" name="Today" value="'.get_text('ScheduleToday', 'Tournament').'">&nbsp;&nbsp;
			<input type="submit" name="FromDay" value="'.get_text('ScheduleFromDay', 'Tournament').'">&nbsp;&nbsp;
			<input type="text" name="FromDayDay" id="FromDayDay">
			</form></td></tr>
	<tr class="Divider"><td colspan="10"></td></tr>
	<tr valign="top"><td id="Manager">';

if($aclLevel == AclReadWrite) {
// management
    echo '<table id="ScheduleTexts">';

// Get all the texts from the scheduler
    echo getScheduleTexts();

    echo '</table>';


    echo '<table>';

// Get all the qualification items with date & time
	if($_SESSION['TourType']==48) {
		// Run ARchery uses totally different tables!
		$q = safe_r_sql("select RarPhase,
			RarPool,
			min(RarGroup) as RarGroup,
			if(RarStartlist=0, '', date(RarStartlist)) RarDay,
			if(RarStartlist=0, '', date_format(min(RarStartlist), '%H:%i')) RarStart,
			RarDuration,
			RarWarmup,
			RarWarmupDuration,
			RarNotes,
			RarShift,
			RarCallTime,
			min(RarStartList) as RarStartList,
			EvElimType,
			group_concat(distinct RarEvent separator ', ') as RarEvents,
			group_concat(distinct concat_ws('-', RarTeam, RarEvent) separator ',') as RarEventCodes
		from RunArcheryRank
		inner join Events on EvTournament=RarTournament and EvTeamEvent=RarTeam and EvCode=RarEvent
		where RarTournament={$_SESSION['TourId']}
		group by if(EvElimType=0 or RarPhase>0, RarStartlist, EvCode), RarPhase, RarPool, if(EvElimType=0 or RarPhase>0, '', RarGroup)
		order by RarStartlist");

		echo '<tr>
			<th class="Title" colspan="9">' . get_text('RA-Session', 'Tournament') . '</th>
			<th class="Title w-30" rowspan="2">' . get_text('ScheduleNotes', 'Tournament') . '</th>
		</tr>
		<tr>
			<th class="Title w-5">' . get_text('Session') . '</th>
			<th class="Title w-5">' . get_text('Distance', 'Tournament') . '</th>
			<th class="Title w-5">' . get_text('Date', 'Tournament') . '</th>
			<th class="Title w-5">' . get_text('Time', 'Tournament') . '</th>
			<th class="Title w-5">' . get_text('Length', 'Tournament') . '</th>
			<th class="Title w-5">' . get_text('Delayed', 'Tournament') . '</th>
			<th class="Title w-5">' . get_text('CallTime', 'RunArchery') . '</th>
			<th class="Title w-5">' . get_text('WarmUp', 'Tournament') . '</th>
			<th class="Title w-5">' . get_text('WarmUpMins', 'Tournament') . '</th>
		</tr>';
		while ($r = safe_fetch($q)) {
			$Session='';
			if($r->RarPhase==1) {
				$Session=get_text('Final'.$r->RarPool, 'RunArchery');
			} elseif($r->RarPhase==2) {
				$Session=get_text('PoolName', 'Tournament', $r->RarPool);
			} elseif($r->RarGroup) {
				$Session=get_text('GroupNum','RoundRobin', $r->RarGroup);
			} else {
				$Session=get_text('AllEntries','Tournament');
			}
			echo '<tr>
				<th nowrap="nowrap">' . $r->RarEvents . '</td>
				<th nowrap="nowrap">' . $Session . '</td>
				<td>'.$r->RarDay.'</td>
				<td>'.$r->RarStart.'</td>
				<td><input size="3" max="999" min="0" type="number" name="Fld[RA][Duration][' . $r->RarEventCodes . '][' . $r->RarStartList . ']" value="' . $r->RarDuration . '" onchange="DiUpdate(this)"></td>
				<td><input size="3" max="999" min="-1" type="number" name="Fld[RA][Shift][' . $r->RarEventCodes . '][' . $r->RarStartList . ']" value="' . $r->RarShift . '" onchange="DiUpdate(this)"></td>
				<td><input size="3" name="Fld[RA][Calltime][' . $r->RarEventCodes . '][' . $r->RarStartList . ']" value="' . ($r->RarCallTime=='00:00:00' ? '' : substr($r->RarCallTime, 0, 5)) . '" onchange="DiUpdate(this)"></td>
				<td><input size="3" name="Fld[RA][Warmtime][' . $r->RarEventCodes . '][' . $r->RarStartList . ']" value="' . ($r->RarWarmup=='00:00:00' ? '' : substr($r->RarWarmup, 0, 5)) . '" onchange="DiUpdate(this)"></td>
				<td><input size="3" name="Fld[RA][WarmtimeDuration][' . $r->RarEventCodes . '][' . $r->RarStartList . ']" value="' . ($r->RarWarmupDuration=='0' ? '' : $r->RarWarmupDuration) . '" onchange="DiUpdate(this)"></td>
				<td><input style="width:100%" size="35" type="text" name="Fld[RA][Options][' . $r->RarEventCodes . '][' . $r->RarStartList . ']" value="' . $r->RarNotes . '" onchange="DiUpdate(this)"></td>
				</tr>';
		}
	} else  {
	    $q = safe_r_sql("select DiSession,
			DiDistance,
			if(DiDay=0, '', DiDay) DiDay,
			if(DiStart=0, '', date_format(DiStart, '%H:%i')) DiStart,
			DiDuration,
			DiTargets,
			if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) DiWarmStart,
			DiWarmDuration,
			DiOptions,
			if(SesName!='', SesName, DiSession) Session,
			DiShift
		from DistanceInformation
		inner join Session on SesTournament=DiTournament and SesOrder=DiSession and SesType=DiType and SesType='Q'
		where DiTournament={$_SESSION['TourId']}
		order by DiSession, DiDistance");
	    echo '<tr>
			<th class="Title" colspan="6">' . get_text('Q-Session', 'Tournament') . '</th>
			<th class="Title w-10" colspan="3">' . get_text('WarmUp', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Targets', 'Tournament') . '</th>
		</tr>
		<tr>
			<th class="Title w-10">' . get_text('Session') . '</th>
			<th class="Title w-10">' . get_text('Distance', 'Tournament') . '</th>
			<th class="Title w-10"><img src="' . $CFG->ROOT_DIR . 'Common/Images/Tip.png" title="' . get_Text('TipDate', 'Tournament') . '" align="right">' . get_text('Date', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Time', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Length', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Delayed', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Time', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Length', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('ScheduleNotes', 'Tournament') . '</th>
			<th class="Title w-10">#1-#N@Dist<br>[@Cat[@Face]]</th>
		</tr>';
	    while ($r = safe_fetch($q)) {
	        echo '<tr>
			<th nowrap="nowrap">' . $r->Session . '</td>
			<th nowrap="nowrap">' . $r->DiDistance . '</td>
			<td><input size="10" type="date" name="Fld[Q][Day][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiDay . '" onblur="DiUpdate(this)"></td>
			<td><input size="5" type="time" name="Fld[Q][Start][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiStart . '" onblur="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="0" type="number" name="Fld[Q][Duration][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiDuration . '" onchange="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="-1" type="number" name="Fld[Q][Shift][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiShift . '" onchange="DiUpdate(this)"></td>
			<td><input size="5" type="text" name="Fld[Q][WarmTime][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiWarmStart . '" onchange="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="0" type="number" name="Fld[Q][WarmDuration][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiWarmDuration . '" onchange="DiUpdate(this)"></td>
			<td><input size="45" type="text" name="Fld[Q][Options][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiOptions . '" onchange="DiUpdate(this)"></td>
			<td><input size="15" type="text" name="Fld[Q][Targets][' . $r->DiSession . '][' . $r->DiDistance . ']" value="' . $r->DiTargets . '" onchange="DiUpdate(this)"></td>
			</tr>';
	    }
	}

// Get all the Elimination items with date & time
    $q = safe_r_sql("select SesOrder,
		ElElimPhase,
		if(DiDay=0, '', DiDay) DiDay,
		if(DiStart=0, '', date_format(DiStart, '%H:%i')) DiStart,
		DiDuration,
		if(DiWarmStart=0, '', date_format(DiWarmStart, '%H:%i')) DiWarmStart,
		DiWarmDuration,
		DiOptions,
		if(SesName!='', SesName, SesOrder) Session, Events, DiShift
	from Session
	inner join (select distinct ElSession, ElTournament, ElElimPhase, group_concat(distinct ElEventCode order by ElEventCode separator ', ') Events from Eliminations where ElTournament={$_SESSION['TourId']} group by ElTournament, ElSession, ElElimPhase) Phase on ElSession=SesOrder and ElTournament=SesTournament
	left join DistanceInformation on SesTournament=DiTournament and SesOrder=DiSession and ElElimPhase=DiDistance and DiType='E'
	where SesTournament={$_SESSION['TourId']}
	and SesType='E'
	order by SesOrder, ElElimPhase");
    if (safe_num_rows($q)) {
        echo '<tr class="Divider"><td colspan="10"></td></tr>
		<tr>
			<th class="Title" colspan="6">' . get_text('E-Session', 'Tournament') . '</th>
			<th class="Title w-10" colspan="4"' . get_text('WarmUp', 'Tournament') . '</th>
		</tr>
		<tr>
			<th class="Title w-10">' . get_text('Session') . '</th>
			<th class="Title w-10">' . get_text('Eliminations') . '</th>
			<th class="Title w-10">' . get_text('Date', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Time', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Length', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Delayed', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Time', 'Tournament') . '</th>
			<th class="Title w-10">' . get_text('Length', 'Tournament') . '</th>
			<th class="Title w-10" colspan="2">' . get_text('ScheduleNotes', 'Tournament') . '</th>
		</tr>';
        while ($r = safe_fetch($q)) {
            echo '<tr>
			<th nowrap="nowrap">' . $r->Session . '<br/>' . $r->Events . '</td>
			<th nowrap="nowrap">' . get_text('Eliminations_' . ($r->ElElimPhase + 1)) . '</td>
			<td><input size="10" type="date" name="Fld[E][Day][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiDay . '" onblur="DiUpdate(this)"></td>
			<td><input size="5" type="time" name="Fld[E][Start][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiStart . '" onblur="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="0" type="number" name="Fld[E][Duration][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiDuration . '" onchange="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="-1" type="number" name="Fld[E][Shift][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiShift . '" onchange="DiUpdate(this)"></td>
			<td><input size="5" type="text" name="Fld[E][WarmTime][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiWarmStart . '" onchange="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="0" type="number" name="Fld[E][WarmDuration][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiWarmDuration . '" onchange="DiUpdate(this)"></td>
			<td colspan="2"><input size="45" type="text" name="Fld[E][Options][' . $r->SesOrder . '][' . $r->ElElimPhase . ']" value="' . $r->DiOptions . '" onchange="DiUpdate(this)"></td>
			</tr>';
        }
    }

// Get all the Matches items with date & time
    $SQL = "select
		FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime,
		if(FsScheduledDate=0, '', FsScheduledDate) ScheduledDate,
		if(FsScheduledTime=0, '', date_format(FsScheduledTime, '%H:%i')) ScheduledTime,
		FsScheduledLen,
		EvFinalFirstPhase,
		FwTime,
		FwDuration,
		FwOptions,
		group_concat(distinct FsEvent order by FsEvent separator ', ') Events, FsShift
	from FinSchedule
	inner join Events on FsEvent=EvCode and FsTeamEvent=EvTeamEvent and FsTournament=EvTournament
	inner join Grids on FsMatchNo=GrMatchNo
	left join (
		select
		FwTeamEvent, FwDay, FwMatchTime, FwEvent, FwTournament,
		group_concat( date_format(FwTime, '%H:%i') order by FwTime separator '|') FwTime,
		group_concat( FwDuration order by FwTime separator '|') FwDuration,
		group_concat( FwOptions order by FwTime separator '|') FwOptions
		from FinWarmup
		where FwTournament={$_SESSION['TourId']}
		group by FwTeamEvent, FwDay, FwMatchTime, FwEvent
		) FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
	where FsTournament={$_SESSION['TourId']}
	group by FsTeamEvent, GrPhase, FsScheduledDate, FsScheduledTime
	order by FsScheduledDate, FsScheduledTime, FwTime, FsTeamEvent, GrPhase desc";
    $q = safe_r_sql($SQL);
    if (safe_num_rows($q)) {
        $OldHeader = '';
        $TeamEvent = 'I';
        while ($r = safe_fetch($q)) {
            if ($OldHeader != $r->FsTeamEvent) {
                $TeamEvent = ($r->FsTeamEvent ? 'T' : 'I');
                echo '<tr class="Divider"><td colspan="10"></td></tr>
				<tr>
					<th class="Title" colspan="6">' . get_text(($r->FsTeamEvent ? 'T' : 'I') . '-Session', 'Tournament') . '</th>
					<th class="Title w-10" colspan="4">' . get_text('WarmUp', 'Tournament') . '</th>
				</tr>
				<tr>
					<th class="Title w-10">' . get_text('Events', 'Tournament') . '</th>
					<th class="Title w-10">' . get_text('Phase') . '</th>
					<th class="Title w-10">' . get_text('Date', 'Tournament') . '</th>
					<th class="Title w-10">' . get_text('Time', 'Tournament') . '</th>
					<th class="Title w-10">' . get_text('Length', 'Tournament') . '</th>
					<th class="Title w-10">' . get_text('Delayed', 'Tournament') . '</th>
					<th class="Title w-10">' . get_text('Time', 'Tournament') . '</th>
					<th class="Title w-10">' . get_text('Length', 'Tournament') . '</th>
					<th class="Title w-10" colspan="2">' . get_text('ScheduleNotes', 'Tournament') . '</th>
				</tr>';
                $OldHeader = $r->FsTeamEvent;
            }
            echo '<tr>
			<th nowrap="nowrap">' . $r->Events . '</td>
			<th nowrap="nowrap">' . get_text(namePhase($r->EvFinalFirstPhase, $r->GrPhase) . '_Phase') . '</td>
			<td><input size="10" type="date" name="Fld[' . $TeamEvent . '][Day][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . ']" value="' . $r->ScheduledDate . '" onblur="DiUpdate(this)"></td>
			<td><input size="5"  type="time" name="Fld[' . $TeamEvent . '][Start][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . ']" value="' . $r->ScheduledTime . '" onblur="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="0" type="number" name="Fld[' . $TeamEvent . '][Duration][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . ']" value="' . $r->FsScheduledLen . '" onchange="DiUpdate(this)"></td>
			<td><input size="3" max="999" min="-1" type="number" name="Fld[' . $TeamEvent . '][Shift][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . ']" value="' . $r->FsShift . '" onchange="DiUpdate(this)"></td>
			<td>';
            $FwTimes = explode('|', ($r->FwTime ?? ''));
            foreach ($FwTimes as $k => $FwTime) {
                if ($k) echo '<br/>';
                echo '<input size="5"  type="text" name="Fld[' . $TeamEvent . '][WarmTime][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . '][' . $FwTime . ']" value="' . $FwTime . '" onchange="DiUpdate(this)">';
            }
            echo '</td>
			<td>';
            foreach (explode('|', ($r->FwDuration ?? '')) as $k => $FwDuration) {
                if ($k) echo '<br/>';
                echo '<input size="3" max="999" min="0" type="number" name="Fld[' . $TeamEvent . '][WarmDuration][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . '][' . $FwTimes[$k] . ']" value="' . $FwDuration . '" onchange="DiUpdate(this)">';
            }
            echo '</td>
			<td>';
            foreach (explode('|', ($r->FwOptions ?? '')) as $k => $FwOption) {
                if ($k) echo '<br/>';
                echo '<input size="45" type="text" name="Fld[' . $TeamEvent . '][Options][' . $r->GrPhase . '][' . $r->FsScheduledDate . '][' . $r->FsScheduledTime . '][' . $FwTimes[$k] . ']" value="' . $FwOption . '" onchange="DiUpdate(this)">';
            }
            echo '</td>
			<td>';
            foreach ($FwTimes as $k => $FwTime) {
                if ($k) {
                    echo '<br/>';
                    echo '<input type="button" value="' . get_text('CmdDelete', 'Tournament') . '" onclick="DiDelSubRow(this, \'' . $TeamEvent . '|' . $r->GrPhase . '|' . $r->FsScheduledDate . '|' . $r->FsScheduledTime . '|' . $FwTime . '\')">';
                } else {
                    echo '<input type="button" value="' . get_text('CmdAdd', 'Tournament') . '" onclick="DiAddSubRow(this)">';
                }
            }
            echo '</td>
			</tr>';
        }
    }
    echo '</table>';
    echo '</td>';
}

// Schedule
echo '<td id="TrueScheduler" class="w-100">';
$Schedule=new Scheduler();
$Schedule->ROOT_DIR=$CFG->ROOT_DIR;
echo $Schedule->getScheduleHTML('SET');
echo '</td></tr>';



echo '</table>';
include('Common/Templates/tail.php');

