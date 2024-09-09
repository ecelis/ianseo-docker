<?php

require_once(dirname(__DIR__).'/config.php');

CheckTourSession(true);

require_once('Common/ods/ods.php');
$excel = new ods();

// Get all events
$EvQ=safe_r_SQL("select * from Events where EvTournament={$_SESSION['TourId']} order by EvTeamEvent, EvProgr");
while($EVENT=safe_fetch($EvQ)) {
    if($EVENT->EvTeamEvent) {
        $SQL="select EnCode, concat_ws('-', RarBib,TfcOrder) as Bib, EnFirstName, EnName, RarEvent, concat(CoCode, if(RaSubTeam>0, RaSubTeam+1, '')) CoCode, CoName, RaLap, 
            if(RaLap=1, date_format(RarStartlist, '%Y-%m-%d %H:%i:%s'),'') StartTime
			from RunArchery
         	inner join RunArcheryRank on RarTournament=RaTournament and RarEntry=RaEntry and RarSubTeam=RaSubTeam and RarTeam=RaTeam and RarEvent=RaEvent and RarPhase=RaPhase and RarPool=RaPool
    		inner join IrmTypes on IrmId=RarIrmType
		    inner join Events on EvTournament=RaTournament and EvCode=RaEvent and EvTeamEvent=RaTeam
    		inner join Countries on CoId=RarEntry
			inner join TeamFinComponent on TfcId=RaArcher and TfcEvent=RaEvent and TfcCoId=RaEntry and TfcSubTeam=RaSubTeam and TfcTournament=RaTournament
			inner join Entries on EnId=TfcId
			where RaTournament={$_SESSION['TourId']} and RaEvent=".StrSafe_DB($EVENT->EvCode)." and RaTeam=1
			order by RarStartlist, RarBib, RaLap";
    } else {
        $SQL="select EnCode, RarBib, EnFirstName, EnName, RarEvent, CoCode, CoName, '', date_format(RarStartlist, '%Y-%m-%d %H:%i:%s') StartTime
            from RunArcheryRank
    		inner join IrmTypes on IrmId=RarIrmType
    		inner join Events on EvTournament=RarTournament and EvCode=RarEvent and EvTeamEvent=RarTeam
    		inner join Entries on EnId=RarEntry
			inner join Countries on CoId=EnCountry
			where RarTournament={$_SESSION['TourId']} and RarEvent=".StrSafe_DB($EVENT->EvCode)." and RarTeam=0 and RarPhase=0
			order by RarStartlist, EnFirstName, EnName";
    }

    $q=safe_r_SQL($SQL);
    if(!safe_num_rows($q)) {
        continue;
    }

    $excel->setActiveSheet(($EVENT->EvTeamEvent ? 'T-' : 'I-').$EVENT->EvCode);
    $excel->addRow([
        'Entry',
        'Bib',
        'Family Name',
        'Given Name',
        'Category',
        'Country Code',
        'Country Name',
        'Position',
        'Start Time',
    ]);

    while($r=safe_fetch_assoc($q)) {
        $excel->addRow(array_values($r));
    }
}

$excel->save($_SESSION['TourCode'].'-Chips.ods', 'a');
die();
