<?php
$JSON=array('error' => 1, 'matches' => array(), 'msg' => '');
require_once(dirname(dirname(__FILE__)) . '/config.php');

if(!CheckTourSession()) {
	$JSON['msg']=get_text('CrackError');
	JsonOut($JSON);
}

checkACL(array(AclIndividuals, AclTeams), AclReadWrite);

if(empty($_REQUEST['ev']) or !isset($_REQUEST['ph'])) {
	$JSON['msg']=get_text('BadParams', 'Tournament');
	JsonOut($JSON);
}

list($Team, $Event)=explode('-', $_REQUEST['ev'], 2);
$JSON['swap']=get_text('SwapOpponents', 'Tournament');

if($Team[0]=='R') {
    // Round Robin Stuff
    $Team=substr($Team,1);
    $Phase=intval($_REQUEST['ph']);

    if($Team) {
        $SQL="select concat(RrMatchScheduledDate, ' ', left(RrMatchScheduledTime, 5)) as `schedule`, if(RrMatchSwapped=1, concat(Athlete2, ' - ', Athlete1), concat(Athlete1, ' - ', Athlete2)) as opponents, MatchNo1 as `match`, 0 as closed
            from (select RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 as MatchNo1, CoName as Athlete1, 
                RrMatchScheduledDate, RrMatchScheduledTime, RrMatchTarget, RrMatchSwapped
                from RoundRobinMatches 
                inner join Teams on TeCoId=RrMatchAthlete and TeSubTeam=RrMatchSubTeam and TeEvent=RrMatchEvent and TeTournament=RrMatchTournament
                inner join Countries on CoId=TeCoId and CoTournament=TeTournament
                where RrMatchMatchNo%2=0 and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=1 and RrMatchLevel=$Phase) tf1
            inner join (select RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 as MatchNo2, CoName as Athlete2
                from RoundRobinMatches 
                inner join Teams on TeCoId=RrMatchAthlete and TeSubTeam=RrMatchSubTeam and TeEvent=RrMatchEvent and TeTournament=RrMatchTournament
                inner join Countries on CoId=TeCoId and CoTournament=TeTournament
                where RrMatchMatchNo%2=1 and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=1 and RrMatchLevel=$Phase) tf2 on MatchNo2=MatchNo1+1
            order by RrMatchScheduledDate, RrMatchScheduledTime, RrMatchTarget, MatchNo1";
    } else {
        $SQL="select concat(RrMatchScheduledDate, ' ', left(RrMatchScheduledTime, 5)) as `schedule`, if(RrMatchSwapped=1, concat(Athlete2, ' - ', Athlete1), concat(Athlete1, ' - ', Athlete2)) as opponents, MatchNo1 as `match`, 0 as closed
            from (select RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 as MatchNo1, concat(ucase(EnFirstName), ' ', EnName) as Athlete1, 
                RrMatchScheduledDate, RrMatchScheduledTime, RrMatchTarget, RrMatchSwapped
                from RoundRobinMatches 
                inner join Entries on EnId=RrMatchAthlete
                where RrMatchMatchNo%2=0 and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=0 and RrMatchLevel=$Phase) tf1
            inner join (select RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 as MatchNo2, concat(ucase(EnFirstName), ' ', EnName) as Athlete2
                from RoundRobinMatches 
                inner join Entries on EnId=RrMatchAthlete
                where RrMatchMatchNo%2=1 and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=0 and RrMatchLevel=$Phase) tf2 on MatchNo2=MatchNo1+1
            order by RrMatchScheduledDate, RrMatchScheduledTime, RrMatchTarget, MatchNo1";
    }

    $q=safe_r_sql($SQL);
    while($r=safe_fetch($q)) {
        $JSON['matches'][]=$r;
    }
    $JSON['error']=0;

    JsonOut($JSON);
}

// resume regular matches

$Phase=intval($_REQUEST['ph']);

if($Team) {
	$SQL="select concat(FSScheduledDate, ' ', left(FSScheduledTime, 5)) as `schedule`, concat(Athlete1, ' - ', Athlete2) as opponents, MatchNo1 as `match`, WinLose1+WinLose2>0 as closed
		from (select TfMatchNo as MatchNo1, CoName as Athlete1, TfSetScore+TfScore+TfWinLose as WinLose1, TfTournament, TfEvent
			from TeamFinals 
			inner join Teams on TfTeam=TeCoId and TfSubTeam=TeSubTeam and TfEvent=TeEvent and TfTournament=TeTournament and TeFinEvent=1
			inner join Countries on CoId=TeCoId and CoTournament=TeTournament
			where TfMatchNo%2=0 and TfEvent=".StrSafe_DB($Event)." and TfTournament={$_SESSION['TourId']}) tf1
		inner join (select TfMatchNo as MatchNo2, CoName as Athlete2, TfSetScore+TfScore+TfWinLose as WinLose2
			from TeamFinals 
			inner join Teams on TfTeam=TeCoId and TfSubTeam=TeSubTeam and TfEvent=TeEvent and TfTournament=TeTournament and TeFinEvent=1
			inner join Countries on CoId=TeCoId and CoTournament=TeTournament
			where TfEvent=".StrSafe_DB($Event)." and TfTournament={$_SESSION['TourId']}) tf2 on MatchNo2=MatchNo1+1
		inner join Grids on GrMatchNo=MatchNo1 and GrPhase=$Phase
		inner join FinSchedule on FSTeamEvent=1 and FSMatchNo=MatchNo1 and FSTournament=TfTournament and FSEvent=TfEvent
		where TfEvent=".StrSafe_DB($Event)." and TfTournament={$_SESSION['TourId']}
		order by FSScheduledDate, FSScheduledTime, FsTarget, `match`";
} else {
	$SQL="select concat(FSScheduledDate, ' ', left(FSScheduledTime, 5)) as `schedule`, concat(Athlete1, ' - ', Athlete2) as opponents, MatchNo1 as `match`, WinLose1+WinLose2>0 as closed
		from (select FinMatchNo as MatchNo1, concat(ucase(EnFirstName), ' ', EnName) as Athlete1, FinSetScore+FinScore+FinWinLose as WinLose1, FinTournament, FinEvent
			from Finals
			inner join Entries on EnId=FinAthlete and EnTournament=FinTournament
			inner join Countries on CoId=EnCountry and CoTournament=FinTournament
			where FinMatchNo%2=0 and FinEvent=".StrSafe_DB($Event)." and FinTournament={$_SESSION['TourId']}) tf1
		inner join (select FinMatchNo as MatchNo2, concat(ucase(EnFirstName), ' ', EnName) Athlete2, FinSetScore+FinScore+FinWinLose as WinLose2
			from Finals
			inner join Entries on EnId=FinAthlete and EnTournament=FinTournament
			inner join Countries on CoId=EnCountry and CoTournament=FinTournament
			where FinEvent=".StrSafe_DB($Event)." and FinTournament={$_SESSION['TourId']}) tf2 on MatchNo2=MatchNo1+1
		inner join Grids on GrMatchNo=MatchNo1 and GrPhase=$Phase
		inner join FinSchedule on FSTeamEvent=0 and FSMatchNo=MatchNo1 and FSTournament=FinTournament and FSEvent=FinEvent
		where FinEvent=".StrSafe_DB($Event)." and FinTournament={$_SESSION['TourId']}
		order by FSScheduledDate, FSScheduledTime, FsTarget, `match`";
}

$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	$JSON['matches'][]=$r;
}
$JSON['error']=0;

JsonOut($JSON);