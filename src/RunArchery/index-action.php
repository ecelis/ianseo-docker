<?php
/*
Run Archery events are made of a "qualification", then based on the settings of the events,
a semifinal (eventually) and a final.

All "rounds" are made of Laps, after each lap a series of arrows: missing the target means a penalty "loop".
Any error in arrow number or loops made adds a time penalty

 *
 *
 *
 * */

global $CFG;
require_once(dirname(__DIR__) . '/config.php');
$JSON=array(
    'error'=>1,
    'msg'=>get_text('ErrGenericError', 'Errors'),
    );

	// require_once('Common/Lib/CommonLib.php');
	// require_once('Common/Fun_FormatText.inc.php');
	// require_once('Common/Fun_Various.inc.php');
	// require_once('Common/Fun_Sessions.inc.php');

if(!CheckTourSession() or !hasACL(AclQualification, AclReadWrite) or empty($_REQUEST['act'])) {
    JsonOut($JSON);
}

switch($_REQUEST['act']) {
    case 'getEvents':
	    $q=safe_w_SQL("select EvTeamEvent, EvCode, EvEventName from Events where EvTournament={$_SESSION['TourId']} order by EvTeamEvent, EvProgr");
        $JSON['events']=[];
		$OldTeam=-1;
        while($r=safe_fetch($q)) {
			if($OldTeam!=$r->EvTeamEvent) {
		        $JSON['events'][]=['id'=>'','val'=>get_text($r->EvTeamEvent ? 'Team' : 'Individual'),'disabled'=>1];
				$OldTeam=$r->EvTeamEvent;
			}
	        $JSON['events'][]=['id'=>"{$r->EvTeamEvent}-{$r->EvCode}",'val'=>$r->EvEventName];
        }
        $JSON['error']=0;
        break;
    case 'getPhases':
		$Events=$_REQUEST['event']??'';
		if(!$Events) {
			JsonOut($JSON);
		}
		list($Team, $Event)=explode('-', $Events, 2);
		$Team=intval($Team);
	    $q=safe_w_SQL("select EvFinEnds as Laps, EvElim1 as HasSemi, EvElim2 as HasFinals 
			from Events 
			where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
        $JSON['phases']=[];
        $JSON['laps']=[];
        $JSON['phases'][]=['id'=>"0",'val'=>get_text('Q-Session', 'Tournament')];
        if($r=safe_fetch($q)) {
			if($r->HasSemi) {
				$JSON['phases'][]=['id'=>"2",'val'=>get_text('SF_Phase', 'Tournament')];
			}
			if($r->HasFinals) {
				$JSON['phases'][]=['id'=>"1",'val'=>get_text('Finals', 'Tournament')];
			}
			for($i=1;$i<=$r->Laps;$i++) {
				$JSON['laps'][]=['id'=>$i,'val'=>get_text('LapNum', 'RunArchery', $i)];
			}
        }
        $JSON['error']=0;
        break;
	case 'sendNextPhase':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Lap=intval($_REQUEST['lap']??0);
		if(!$Event or $Phase==-1) {
			JsonOut($JSON);
		}
		$Team=intval($Team);

		// get details of event to push athletes in the correct slots
		$q=safe_r_sql("select EvElim1, EvElim2 from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvElimType>0 and EvCode=".StrSafe_DB($Event));
		if(!($r=safe_fetch($q))) {
			JsonOut($JSON);
		}
		// first removes the qualified flag to everybody
		safe_w_SQL("update RunArcheryRank set RarQualified='' where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarPhase=$Phase and RarEvent=".StrSafe_DB($Event));
		if($Phase==0) {
			// goes from Qual
			if($r->EvElim1) {
				// to semifinal
				$NewPhase=2;
				safe_w_sql("update RunArcheryRank r2
					inner join RunArcheryRank r1 
    					on r1.RarTournament=r2.RarTournament 
				        and r1.RarTeam=r2.RarTeam 
				        and r1.RarEvent=r2.RarEvent 
                        and r1.RarPhase=0
                        and r1.RarRank=r2.RarFromRank
					set r2.RarEntry=r1.RarEntry, r2.RarSubTeam=r1.RarSubTeam, r2.RarBib=r1.RarBib
					where r2.RarTournament={$_SESSION['TourId']} and r2.RarTeam=$Team and r2.RarPhase=2 and r2.RarEvent=".StrSafe_DB($Event));
				safe_w_sql("update RunArcheryRank r2
					inner join RunArcheryRank r1 
    					on r1.RarTournament=r2.RarTournament 
				        and r1.RarTeam=r2.RarTeam 
				        and r1.RarEvent=r2.RarEvent 
                        and r1.RarPhase=0
                        and r1.RarRank=r2.RarFromRank
					set r1.RarQualified='Q'
					where r2.RarTournament={$_SESSION['TourId']} and r2.RarTeam=$Team and r2.RarPhase=2 and r2.RarEvent=".StrSafe_DB($Event));
			} else {
				// straight to finals
				$NewPhase=1;
				safe_w_sql("update RunArcheryRank r2
					inner join RunArcheryRank r1 
    					on r1.RarTournament=r2.RarTournament 
				        and r1.RarTeam=r2.RarTeam 
				        and r1.RarEvent=r2.RarEvent 
                        and r1.RarPhase=0
                        and r1.RarRank=r2.RarFromRank
					set r2.RarEntry=r1.RarEntry, r2.RarSubTeam=r1.RarSubTeam, r2.RarBib=r1.RarBib
					where r2.RarTournament={$_SESSION['TourId']} and r2.RarTeam=$Team and r2.RarPhase=1 and r2.RarEvent=".StrSafe_DB($Event));
				safe_w_sql("update RunArcheryRank r2
					inner join RunArcheryRank r1 
    					on r1.RarTournament=r2.RarTournament 
				        and r1.RarTeam=r2.RarTeam 
				        and r1.RarEvent=r2.RarEvent 
                        and r1.RarPhase=0
                        and r1.RarRank=r2.RarFromRank
					set r1.RarQualified='Q'
					where r2.RarTournament={$_SESSION['TourId']} and r2.RarTeam=$Team and r2.RarPhase=1 and r2.RarEvent=".StrSafe_DB($Event));
			}
			// updates the SO status
			safe_w_sql("update Events set EvShootOff=1 where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		} elseif($Phase==2) {
			// goes from semi to finals
			$NewPhase=1;
			// So first assigns the pool winners
			safe_w_sql("update RunArcheryRank r2
					inner join RunArcheryRank r1 
    					on r1.RarTournament=r2.RarTournament 
				        and r1.RarTeam=r2.RarTeam 
				        and r1.RarEvent=r2.RarEvent 
                        and r1.RarPhase=2
                        and r1.RarRank=r2.RarFromRank
						and r1.RarPool=r2.RarFromType
						and r1.RarPool!=0
					set r2.RarEntry=r1.RarEntry, r2.RarSubTeam=r1.RarSubTeam, r2.RarBib=r1.RarBib
					where r2.RarTournament={$_SESSION['TourId']} and r2.RarTeam=$Team and r2.RarPhase=1 and r2.RarEvent=".StrSafe_DB($Event));
			safe_w_sql("update RunArcheryRank r2
					inner join RunArcheryRank r1 
    					on r1.RarTournament=r2.RarTournament 
				        and r1.RarTeam=r2.RarTeam 
				        and r1.RarEvent=r2.RarEvent 
                        and r1.RarPhase=2
                        and r1.RarRank=r2.RarFromRank
						and r1.RarPool=r2.RarFromType
						and r1.RarPool!=0
					set r1.RarQualified='Q'
					where r2.RarTournament={$_SESSION['TourId']} and r2.RarTeam=$Team and r2.RarPhase=1 and r2.RarEvent=".StrSafe_DB($Event));
			// extracts an overall rank from the remainers
			$Rank=1;
			$MaxRank=$r->EvElim2-6;
			$q=safe_r_sql("select RarEntry, RarSubTeam, RarBib
				from RunArcheryRank
				where RarEntry<4000000000 and RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarPhase=$Phase and RarEvent=".StrSafe_DB($Event)." and RarQualified=''
				order by RarTimeFinal, rand()");
			while($r=safe_fetch($q) and $Rank<=$MaxRank) {
				safe_w_sql("update RunArcheryRank
					set RarEntry=$r->RarEntry, RarSubTeam=$r->RarSubTeam, RarBib='$r->RarBib'
					where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarPhase=1 and RarFromRank=$Rank and RarFromType=0 and RarEvent=".StrSafe_DB($Event));
				// set the qualified
				safe_w_sql("update RunArcheryRank
					set RarQualified='q'
					where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarPhase=2 and RarEntry=$r->RarEntry and RarSubTeam=$r->RarSubTeam and RarEvent=".StrSafe_DB($Event));
				$Rank++;
			}
			// updates the SO status
			safe_w_sql("update Events set EvE2ShootOff=1 where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		} else {
			JsonOut($JSON);
		}
		// updates the laps
		safe_w_sql("update RunArchery
			inner join RunArcheryRank
                on RarTournament=RaTournament 
		        and RarTeam=RaTeam 
		        and RarEvent=RaEvent 
                and RarPhase=RaPhase
                and RarFromRank=RaFromRank
                and RarFromType=RaFromType
			set RaEntry=RarEntry, RaSubTeam=RarSubTeam
			where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarPhase=$NewPhase and RarEvent=".StrSafe_DB($Event));

		$JSON['NewPhase']=$NewPhase;

		// NO BREAK AS IT CONTINUES TO SEND THE NEW PHASE!!!
	case 'getData':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($NewPhase??$_REQUEST['phase']??-1);
		$Pool=($Phase ? intval($_REQUEST['pool']??0) : 0);
		$Lap=intval($_REQUEST['lap']??0);
		if(!$Event or $Phase==-1) {
			JsonOut($JSON);
		}
		$Team=intval($Team);

        $JSON['isTeam']=$Team;
		$JSON['headers']=[
			'FamName' => get_text('FamilyName','Tournament'),
			'GivName' => get_text('GivenName','Tournament'),
			'Bib' => get_text('BibNumber', 'BackNumbers'),
			'CoCode'=>get_text('CountryCode'),
			'CoName'=>get_text('Nation'),
			'StartDay' => get_text('ScheduledDay', 'RunArchery'),
			'StartTime' => get_text('ScheduledTime', 'RunArchery'),
			'LapNum' => get_text('Lap', 'RunArchery'),
			'TimeStart' => get_text('StartTime', 'RunArchery'),
			'TimeFinish' => get_text('FinishTime', 'RunArchery'),
			'RunningTime' => get_text('RunningTime', 'RunArchery'),
			'TotArPen' => get_text('PenaltyTotArrows', 'RunArchery'),
			'TotLoopPen' => get_text('PenaltyTotLoops', 'RunArchery'),
			'PlusTime' => get_text('PlusTime', 'RunArchery'),
			'MinusTime' => get_text('MinusTime', 'RunArchery'),
			'FinTime' => get_text('FinalTime', 'RunArchery'),
			'FinRank' => get_text('FinalRank', 'RunArchery'),
			'Lap' => get_text('LapTime', 'RunArchery'),
			'Ars' => get_text('ArrowsShot', 'RunArchery'),
			'Hits' => get_text('Hits', 'RunArchery'),
			'LoopToDo' => get_text('LoopToDo', 'RunArchery'),
			'LoopDone' => get_text('LoopDone', 'RunArchery'),
			'ArPen' => get_text('PenaltyArrow', 'RunArchery'),
			'LoopPen' => get_text('PenaltyLoop', 'RunArchery'),
		];
		$JSON['pools']=[];

		if($Phase) {
			// need to send the number of pools!
			$q=safe_r_sql("select distinct RarPool from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarEvent=".StrSafe_DB($Event)." and RarTeam=$Team and RarPhase=$Phase order by RarPool");
			if(!$Pool or $Pool>safe_num_rows($q)) {
				$Pool=1;
			}
			while($r=safe_fetch($q)) {
				$JSON['pools'][]=['k'=>$r->RarPool, 's'=>($Pool==$r->RarPool), 'v'=>($Phase==1 ? get_text('Final'.$r->RarPool, 'RunArchery') : get_text('SemiFinalName', 'RunArchery', $r->RarPool))];
			}
		}
		$SoStatusField='EvShootOff';
		if($Phase==2) {
			$SoStatusField='EvE2ShootOff';
		} elseif($Phase==1) {
			$SoStatusField='EvE1ShootOff';
		}
		if($Team) {
			$SQL="select RaLap, concat(CoId,'-',RarSubTeam) as EnId, '' as FamName, '' as GivName, RarBib, concat_ws('-', RarBib,TfcOrder) as Bib, EvFinArrows as MaxArs2Shoot, EvE1Arrows as TargetsToHit, EvFinEnds as NumLaps,
       			concat(CoCode, if(RaSubTeam>0, RaSubTeam+1, '')) CoCode, CoName, date_format(RarStartlist, '%Y-%m-%d') as StartDay,
       			RarGroup, RarTarget, RarIrmType, IrmShowRank, IrmType, date_format(RarStartlist, '%H:%i:%s') StartTime, RaLapTime=0 and RaArrowsShot=0 and RaLoopAssigned=0 and RaLoopDone=0 and RaLap=1 and RaArrowPenalty=0 and RaLoopPenalty=0 as EditStart,
       			left(from_unixtime(RarDateTimeStart, '%H:%i:%s.%f'),12) TimeStart, left(from_unixtime(RarDateTimeFinish, '%H:%i:%s.%f'),12) TimeFinish, RarTimeTotal TimeTotal, RarArrowTotalPenalty TotArPen, RarLoopTotalPenalty TotLoopPen, RarTimeAdjustPlus PlusTime, RarTimeAdjustMinus MinusTime, RarTimeFinal FinTime, RarRank FinRank,
       			RaLapTime Lap, RaArrowsShot Ars, RaHits Hits, RaLoopAssigned LoopToDo, RaLoopDone LoopDone, RaArrowPenalty ArPen, RaLoopPenalty LoopPen,
       			$SoStatusField as SoStatus
			from RunArchery
         	inner join RunArcheryRank on RarTournament=RaTournament and RarEntry=RaEntry and RarSubTeam=RaSubTeam and RarTeam=RaTeam and RarEvent=RaEvent and RarPhase=RaPhase and RarPool=RaPool
    		inner join IrmTypes on IrmId=RarIrmType
		    inner join Events on EvTournament=RaTournament and EvCode=RaEvent and EvTeamEvent=RaTeam
    		inner join Countries on CoId=RarEntry
			inner join TeamFinComponent on TfcId=RaArcher and TfcEvent=RaEvent and TfcCoId=RaEntry and TfcSubTeam=RaSubTeam and TfcTournament=RaTournament
			where RaTournament={$_SESSION['TourId']} and RaEvent=".StrSafe_DB($Event)." and RaTeam=$Team and RaPhase=$Phase and RaPool=$Pool
			order by RarBib+0, RarBib, RarStartlist, RarBib, RaLap";
		} else {
			$SQL="select RaLap, EnFirstName FamName, EnName GivName, EnId, RarBib, RarBib as Bib, EvFinArrows as MaxArs2Shoot, EvE1Arrows as TargetsToHit, EvFinEnds as NumLaps,
       			CoCode, CoName, date_format(RarStartlist, '%Y-%m-%d') as StartDay,
       			RarGroup, RarTarget, RarIrmType, IrmShowRank, IrmType, date_format(RarStartlist, '%H:%i:%s') StartTime, RaLapTime=0 and RaArrowsShot=0 and RaLoopAssigned=0 and RaLoopDone=0 and RaLap=1 and RaArrowPenalty=0 and RaLoopPenalty=0 as EditStart,
       			left(from_unixtime(RarDateTimeStart, '%H:%i:%s.%f'),12) TimeStart, left(from_unixtime(RarDateTimeFinish, '%H:%i:%s.%f'),12) TimeFinish, RarTimeTotal TimeTotal, RarArrowTotalPenalty TotArPen, RarLoopTotalPenalty TotLoopPen, RarTimeAdjustPlus PlusTime, RarTimeAdjustMinus MinusTime, RarTimeFinal FinTime, RarRank FinRank,
       			RaLapTime Lap, RaArrowsShot Ars, RaHits Hits, RaLoopAssigned LoopToDo, RaLoopDone LoopDone, RaArrowPenalty ArPen, RaLoopPenalty LoopPen,
       			$SoStatusField as SoStatus
			from RunArchery
         	inner join RunArcheryRank on RarTournament=RaTournament and RarEntry=RaEntry and RarSubTeam=RaSubTeam and RarTeam=RaTeam and RarEvent=RaEvent and RarPhase=RaPhase and RarPool=RaPool
    		inner join IrmTypes on IrmId=RarIrmType
    		inner join Events on EvTournament=RaTournament and EvCode=RaEvent and EvTeamEvent=RaTeam
    		inner join Entries on EnId=RaEntry
			inner join Countries on CoId=EnCountry
			where RaTournament={$_SESSION['TourId']} and RaEvent=".StrSafe_DB($Event)." and RaTeam=$Team and RaPhase=$Phase and RaPool=$Pool
			order by RarBib+0, RarBib, RarStartlist, RarBib, RaLap";
		}
		$q=safe_r_sql($SQL);
		$Rows=[];
		while($r=safe_fetch($q)) {
			if(empty($Rows[$r->RarBib])) {
				$Rows[$r->RarBib]=[
					'FamName' => $r->FamName,
					'GivName' => $r->GivName,
					'EnId' => $r->EnId,
					'RarGroup' => ($r->RarGroup?:''),
					'RarTarget' => ($r->RarTarget?:''),
					'MaxArs2Shoot' => $r->MaxArs2Shoot,
					'TargetsToHit' => $r->TargetsToHit,
					'NumLaps' => intval($r->NumLaps),
					'CoCode' => $r->CoCode,
					'CoName' => $r->CoName,
					// 'StartDay' => $r->StartDay,
					// 'StartTime' => $r->StartTime,
					// 'TimeStart' => $r->TimeStart,
					// 'TimeFinish' => $r->TimeFinish,
					'TimeTotal' => date_format(date_create_from_format('U.u', $r->TimeTotal), 'H:i:s.v'),
					'TotArPen' => $r->TotArPen>0 ? date_format(date_create_from_format('U.u', $r->TotArPen), 'H:i:s.v') : '-',
					'TotLoopPen' => $r->TotLoopPen>0 ? date_format(date_create_from_format('U.u', $r->TotLoopPen), 'H:i:s.v') : '-',
					'PlusTime' => $r->PlusTime>0 ? date_format(date_create_from_format('U.u', $r->PlusTime), 'H:i:s.v') : '',
					'MinusTime' => $r->MinusTime>0 ? date_format(date_create_from_format('U.u', $r->MinusTime), 'H:i:s.v') : '',
					'FinTime' => $r->FinTime>0 ? date_format(date_create_from_format('U.u', $r->FinTime), 'H:i:s.v') : '',
					'FinRank' => $r->IrmShowRank ? $r->FinRank : $r->IrmType,
					'Bib' => $r->Bib,
					'RarBib' => $r->RarBib,
					'Irm' => $r->RarIrmType,
					'Laps'=>[],
				];
				$JSON['SoStatus'] = $r->SoStatus;
			}
			$Rows[$r->RarBib]['Laps'][]=[
				'Bib' => $r->Bib,
                'RarBib' => $r->RarBib,
				'LapNum' => $r->RaLap,
				'EditStart' => $r->EditStart,
				'LapTime' => date_format(date_create_from_format('U.u', $r->Lap), 'H:i:s.v'), // DateTime::createFromFormat('U.u', $r->Lap)->format('H:i:s.u', ),
				'LapArShot' => $r->Ars,
				'LapHits' => $r->Hits,
				'LoopToDo' => $r->LoopToDo,
				'LoopDone' => $r->LoopDone,
				'ArPen' => $r->ArPen>0 ? date_format(date_create_from_format('U.u', $r->ArPen), 'H:i:s.v') : '',
				'LoopPen' => $r->LoopPen>0 ? date_format(date_create_from_format('U.u', $r->LoopPen), 'H:i:s.v') : '',
			];
		}
		$JSON['rows']=array_values($Rows);
		$JSON['error']=0;
		break;
	case 'setTimeSheet':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Data=preg_split('/[\r\n]/', $_REQUEST['data']??'');
		if(!$Event or $Phase==-1 or !$Data) {
			JsonOut($JSON);
		}
		$Team=intval($Team);
		$JSON['rows']=[];
		$JSON['html']=[];
		// get the equivalence bib<=>entry
		$Bibs=[];
		$q=safe_r_sql("select RarBib as Bib, RarEntry as Entry, RarSubTeam as SubTeam 
			from RunArcheryRank 
           	where RarTournament={$_SESSION['TourId']} 
				and RarTeam=$Team
				and RarPhase=$Phase
				and RarEvent=".StrSafe_DB($Event));
		while($r=safe_fetch($q)) {
			$Bibs[$r->Bib]=$r;
		}
		$JSON['error']=0;
		$JSON['msg']=get_text('TimeSheetImportedSuccess', 'RunArchery');
		foreach($Data as $line) {
			if(!trim($line)) {
				continue;
			}
			$items=preg_split('/[\t;]/', trim($line));
			if(count($items)<2 or preg_match('/[^0-9:.,]/', $items[1]) or !isset($Bibs[$items[0]])) {
				$JSON['error']=1;
				$JSON['msg']=get_text('TimeSheetImportedFailure', 'RunArchery');
				continue;
			}
			$SQL="update RunArcheryRank set RarTimeTotal=". getFloatFromTime(str_replace(',','.', $items[1]))." 
				where RarTournament={$_SESSION['TourId']} 
					and RarTeam=$Team
					and RarPhase=$Phase
					and RarEvent=".StrSafe_DB($Event)."
					and RarEntry={$Bibs[$items[0]]->Entry}
					and RarSubTeam={$Bibs[$items[0]]->SubTeam}";
			safe_w_sql($SQL);
			for($i=2;$i<count($items);$i++) {
				$SQL="update RunArchery set RaLapTime=". getFloatFromTime(str_replace(',','.', $items[$i]))." 
					where RaTournament={$_SESSION['TourId']} 
						and RaTeam=$Team
						and RaPhase=$Phase
						and RaEvent=".StrSafe_DB($Event)."
						and RaEntry={$Bibs[$items[0]]->Entry}
						and RaSubTeam={$Bibs[$items[0]]->SubTeam}
						and RaLap=".($i-1);
				safe_w_sql($SQL);
			}
		}

		// calculates the ranking anyway with what was correctly entered
		require_once('Common/Lib/Obj_RankFactory.php');
		$RankData=Obj_RankFactory::create('Run', array('allEvents' => [$Team=>[$Event]], 'phase' => $Phase));
		$RankData->calculate();

		JsonOut($JSON);
		break;
	case 'updateField':
		require_once('Common/Lib/Obj_RankFactory.php');
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Lap=intval($_REQUEST['lap']??0);
		$Team=intval($Team);
		$Field=$_REQUEST['fld']??'';
		$Entry=$_REQUEST['id']??0;
		$SubTeam=0;
		if(strstr($Entry, '-')) {
			list($Entry, $SubTeam)=@explode('-', $Entry);
		}
		if(!$Entry or !$Event or $Phase==-1 or !$Lap or !$Field or !isset($_REQUEST['val'])) {
			JsonOut($JSON);
		}
		// get all the values we will need
		$SQL="select EvFinArrows as  MaxArs2Shoot, EvE1Arrows as TargetsToHit, EvArrowPenalty, EvLoopPenalty, EvFinEnds as NumLaps, RarIrmType,
       			date_format(RarStartlist, '%Y-%m-%d') as StartDay,
       			date_format(RarStartlist, '%H:%i:%s') StartTime, RaLapTime=0 and RaArrowsShot=0 and RaLoopAssigned=0 and RaLoopDone=0 and RaLap=1 and RaArrowPenalty=0 and RaLoopPenalty=0 as EditStart,
       			left(from_unixtime(RarDateTimeStart, '%H:%i:%s.%f'),12) TimeStart, left(from_unixtime(RarDateTimeFinish, '%H:%i:%s.%f'),12) TimeFinish, RarTimeTotal TimeTotal, RarArrowTotalPenalty TotArPen, RarLoopTotalPenalty TotLoopPen, RarTimeAdjustPlus PlusTime, RarTimeAdjustMinus MinusTime, RarTimeFinal FinTime, RarRank FinRank,
       			RaLapTime Lap, RaArrowsShot Ars, RaHits Hits, RaLoopAssigned LoopToDo, RaLoopDone LoopDone, RaArrowPenalty ArPen, RaLoopPenalty LoopPen
			from RunArcheryRank
    		inner join RunArchery ra on RaTournament=RarTournament and RaEntry=RarEntry and RaSubTeam=RarSubTeam and RaTeam=RarTeam and RaEvent=RarEvent and RaPhase=RarPhase and RaLap=$Lap
    		inner join Events on EvTournament=RaTournament and EvCode=RaEvent and EvTeamEvent=RaTeam
			inner join Tournament on ToId=RarTournament
    		where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase";
		$q=safe_r_SQL($SQL);
		$ENTRY=safe_fetch($q);
		if(!$ENTRY) {
			JsonOut($JSON);
		}
		$JSON['val']=$_REQUEST['val'];
		$Update=0;
		switch($Field) {
			case 'startTime':
				if($Lap==1) {
					if(!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}\.[0-9]{3}$/sim', $_REQUEST['val'])) {
						JsonOut($JSON);
					}
					$ENTRY->TimeStart=$_REQUEST['val'];
					safe_w_sql("update RunArcheryRank set RarDateTimeStart=unix_timestamp('{$ENTRY->StartDay} {$_REQUEST['val']}') 
                    	where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
					if(safe_w_affected_rows()) {
						$Update=1;
						safe_w_sql("update RunArcheryRank set RarLastUpdate=now() 
                    		where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
					}
				}
				break;
			case 'lapTime':
				$val=getFloatFromTime($_REQUEST['val']);
				safe_w_sql("update RunArchery set RaLapTime=$val 
                    where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArchery set RaLastUpdate=now() 
						where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				}
				$JSON['val']=$val>0 ? date_format(date_create_from_format('U.u', $val), 'H:i:s.v') : '';
				break;
			case 'arrows':
				$ArrowPenalty=0;
				$LoopsToDo=0;
				$ArrowsShot=intval($_REQUEST['val']);
				if($ArrowsShot>$ENTRY->MaxArs2Shoot) {
					$ArrowPenalty=($ArrowsShot-$ENTRY->MaxArs2Shoot)*$ENTRY->EvArrowPenalty;
				} elseif($ArrowsShot<$ENTRY->MaxArs2Shoot and $ENTRY->Hits<$ENTRY->TargetsToHit) {
					$ArrowPenalty=($ENTRY->MaxArs2Shoot-$ArrowsShot)*$ENTRY->EvArrowPenalty;
				}
				safe_w_sql("update RunArchery set RaArrowsShot=$ArrowsShot, RaArrowPenalty=$ArrowPenalty 
                    where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArchery set RaLastUpdate=now() 
						where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				}
				break;
			case 'hits':
				$Hits=intval($_REQUEST['val']);
				$LoopsToDo=0;
				$LoopPenalty=0;
				$ArrowPenalty=0;
				if($Hits<$ENTRY->TargetsToHit) {
					$LoopsToDo=($ENTRY->TargetsToHit-$Hits);
					$LoopPenalty=max(0,$LoopsToDo-$ENTRY->LoopDone)*$ENTRY->EvLoopPenalty;
					if($ENTRY->Ars<$ENTRY->MaxArs2Shoot) {
						$ArrowPenalty=($ENTRY->MaxArs2Shoot-$ENTRY->Ars)*$ENTRY->EvArrowPenalty;
					}
				}
				safe_w_sql("update RunArchery set RaHits=$Hits, RaLoopAssigned=$LoopsToDo, RaLoopPenalty=$LoopPenalty, RaArrowPenalty=$ArrowPenalty
                    where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArchery set RaLastUpdate=now() 
						where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				}
				break;
			case 'loopsdone':
				$LoopsDone=intval($_REQUEST['val']);
				$LoopPenalty=0;
				if($LoopsDone<=$ENTRY->LoopToDo) {
					$LoopPenalty=max(0,$ENTRY->LoopToDo-$LoopsDone)*$ENTRY->EvLoopPenalty;
				}
				safe_w_sql("update RunArchery set RaLoopDone=$LoopsDone, RaLoopPenalty=$LoopPenalty
                    where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArchery set RaLastUpdate=now() 
						where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase and RaLap=$Lap");
				}
				break;
			case 'TimeTotal':
				$val=getFloatFromTime($_REQUEST['val']);
				safe_w_sql("update RunArcheryRank set RarTimeTotal=$val 
                    where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				if(safe_w_affected_rows()) {
					$Update=1;
					// if we update the global time, voids the lap times
					safe_w_sql("update RunArchery set RaLapTime=0, RaLastUpdate=now()
	                    where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaTeam=$Team and RaEvent=".StrSafe_DB($Event)." and RaPhase=$Phase");
					safe_w_sql("update RunArcheryRank set  RarLastUpdate=now()
						where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				}
				$JSON['val']=$val>0 ? date_format(date_create_from_format('U.u', $val), 'H:i:s.v') : '';
				$JSON['resetLapsTimes']=1;
				break;
			case 'MinusTime':
				$val=getFloatFromTime($_REQUEST['val']);
				safe_w_sql("update RunArcheryRank set RarTimeAdjustMinus=$val, RarTimeFinal=RarTimeFinal-$val 
                    where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArcheryRank set  RarLastUpdate=now()
						where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				}
				$JSON['val']=$val>0 ? date_format(date_create_from_format('U.u', $val), 'H:i:s.v') : '';
				break;
			case 'AddedTime':
				$val=getFloatFromTime($_REQUEST['val']);
				safe_w_sql("update RunArcheryRank set RarTimeAdjustPlus=$val, RarTimeFinal=RarTimeFinal+$val 
                    where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArcheryRank set  RarLastUpdate=now()
						where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				}
				$JSON['val']=$val>0 ? date_format(date_create_from_format('U.u', $val), 'H:i:s.v') : '';
				break;
			case 'Irm':
				$NewIrm=intval($_REQUEST['val']);
				$NewPosition=0;
				switch($NewIrm) {
					case 20: $NewPosition=$CFG->DERANKING; break; //DQB)
					case 15: $NewPosition=$CFG->DISQUALIFIED; break; // DSQ)
					case 10: $NewPosition=$CFG->DIDNOTSTART; break; // (DNS)
					case 7: $NewPosition=$CFG->DIDNOTFINISH; break; // (DNF-7)
				}
				safe_w_sql("update RunArcheryRank set RarIrmType=$NewIrm, RarRankClass=$NewPosition, RarRank=$NewPosition
                    where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				if(safe_w_affected_rows()) {
					$Update=1;
					safe_w_sql("update RunArcheryRank set RarLastUpdate=now() 
	                    where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
				}
				break;
			default:
				$JSON['msg']=$_REQUEST['fld'];
				JsonOut($JSON);
		}

		$RankData=Obj_RankFactory::create('Run', array('allEvents' => [$Team=>[$Event]], 'phase' => $Phase));
		if($Update) {
			$RankData->calculate();
		}

		// TODO: updates the rank!
		$Data=$RankData->read();

		$JSON['ranks']=[];
		foreach($Data['sections'][$Team][$Event]['phases'][$Phase]['items'] as $item) {
			$JSON['ranks'][]=['id'=>$item['id'].($Team ? '-'.$item['subteam'] : ''), 'rank'=>$item['rank']];
		}

		// refetches the data
		$q=safe_r_SQL($SQL);
		$ENTRY=safe_fetch($q);
		$JSON['values']=[
			['key'=>'.ArrowPenalty', 'value'=>$ENTRY->ArPen>0 ? date_format(date_create_from_format('U.u', $ENTRY->ArPen), 'H:i:s.v') : ''],
			['key'=>'.LoopPenalty', 'value'=>$ENTRY->LoopPen>0 ? date_format(date_create_from_format('U.u', $ENTRY->LoopPen), 'H:i:s.v') : ''],
			['key'=>'.loops2do', 'value'=>$ENTRY->LoopToDo,],
			];
		$JSON['valuesGen']=[
			['key'=>'.TimeFinish', 'value'=>$ENTRY->TimeFinish],
			['key'=>'.startTime', 'value'=>$ENTRY->TimeStart],
			['key'=>'.TotArPen', 'value'=>$ENTRY->TotArPen>0 ? date_format(date_create_from_format('U.u', $ENTRY->TotArPen), 'H:i:s.v') : '-',],
			['key'=>'.TotLoopPen', 'value'=>$ENTRY->TotLoopPen>0 ? date_format(date_create_from_format('U.u', $ENTRY->TotLoopPen), 'H:i:s.v') : '-',],
			['key'=>'.FinTime', 'value'=>$ENTRY->FinTime>0 ? date_format(date_create_from_format('U.u', $ENTRY->FinTime), 'H:i:s.v') : '',],
			];
		$JSON['timeTotal']=$ENTRY->TimeTotal>0 ? date_format(date_create_from_format('U.u', $ENTRY->TimeTotal), 'H:i:s.v') : '';
		$JSON['error']=0;
		break;
}

JsonOut($JSON);

function getFloatFromTime($TIME) {
	if (preg_match('/^[0-9:.,]+$/', $TIME)) {
		// format is like xx:xx.034
		// first split on the [.,] to get decimals if any
		$decs = preg_split('/[.,]/', $TIME);
		$Decimals = 0;
		if (count($decs) == 2) {
			$Decimals = $decs[1];
		}
		$Seconds = 0;
		$Bits = explode(':', $decs[0]);
		while ($bit = array_shift($Bits)) {
			$Seconds = ($Seconds * 60) + $bit;
		}
		$val = $Seconds . '.' . $Decimals;
	} else {
		$_REQUEST['val'] = str_replace(',', '.', $_REQUEST['val']);
		$val = floatval($_REQUEST['val']);
	}

	return $val;
}