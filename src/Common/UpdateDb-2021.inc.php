<?php

if($version<'2021-01-05 11:13:01') {
	$q="ALTER TABLE ExtraData ADD INDEX (`EdId`, `EdType`, `EdEmail`(1));";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-01-05 11:13:01');
}

if($version<'2021-01-14 17:13:01') {
	$q="ALTER TABLE Emails ADD EmKey int not null after EmTournament, change EmTitle EmTitle varchar(50) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	$q=safe_w_sql("select * from Emails order by EmTournament, EmTitle");
	$OldTour='aaa';
	while($r=safe_fetch($q)) {
		if($OldTour!=$r->EmTournament) {
			$Key=1;
			$OldTour = $r->EmTournament;
		}
		safe_w_sql("update Emails set EmKey=".($Key++)." where EmTournament=$r->EmTournament and EmTitle=".StrSafe_DB($r->EmTitle));
	}
	safe_w_sql("alter table Emails drop primary key",false,array(1146, 1060));
	safe_w_sql("alter table Emails add primary key (EmTournament, EmKey), add index (EmTournament, EmTitle)",false,array(1146, 1060));

	db_save_version('2021-01-14 17:13:01');
}

if($version<'2021-01-24 11:13:02') {
	$q="ALTER TABLE `ExtraData` DROP INDEX `EdId`, ADD INDEX `EdId` (`EdId`, `EdType`, `EdEmail`(1), `EdEvent`)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-01-24 11:13:02');
}

if($version<'2021-03-07 19:15:00') {
    $q="UPDATE `Targets` SET `A_color` = '', `B_size` = '0', `B_color` = '', `I_color` = '', `O_size` = '9', `O_color` = 'FFFFFF', `P_size` = '18', `P_color` = '000000' WHERE `TarId` = 19";
    $r=safe_w_sql($q,false,array(1146, 1060));

    db_save_version('2021-03-07 19:15:00');
}

if($version<'2021-04-10 11:13:01') {
	$q="ALTER TABLE `TVOut` change TVOId TVOId tinyint unsigned not null, ADD `TVOSide` tinyint unsigned NOT NULL, add TVOHeight varchar(15) not null, drop primary key";
	$r=safe_w_sql($q,false,array(1146, 1060));
	$q="ALTER TABLE `TVOut` add primary key (TVOId, TVOSide)";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-04-10 11:13:01');
}

if($version<'2021-04-11 11:13:01') {
	$q="ALTER TABLE `TVOut` add TVOFile varchar(255) not null";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-04-11 11:13:01');
}

if($version<'2021-05-15 18:13:01') {
	updateContacts_20210515();
	db_save_version('2021-05-15 18:13:01');
}

if($version<'2021-07-08 11:46:05') {
	safe_w_sql("UPDATE `TourTypes` SET `TtDistance` = '2' WHERE `TtId` = 44",false,array(1146, 1060));

	// extpand D1-D3 arrowstring to 255 chars
	safe_w_sql("alter table Qualifications 
    	change QuD1Arrowstring QuD1Arrowstring varchar(255) not null, 
    	change QuD2Arrowstring QuD2Arrowstring varchar(255) not null, 
    	change QuD3Arrowstring QuD3Arrowstring varchar(255) not null",false,array(1146, 1060));

	// expand targets to fit 9 new rings
	$sql=array();
	foreach(range(1,9) as $i) {
		$sql[]="add {$i}_size int(3) not null default 0";
		$sql[]="add {$i}_color varchar(6) not null default ''";
	}
	safe_w_sql("alter table Targets ".implode(',', $sql), false, array(1060));

	// add 3 target types for NFAA
	safe_w_sql("delete from Targets where TarId between 20 and 22");
	// Hunter Target 3-X with X as 5
	safe_w_sql("insert into Targets set
		TarId=20,
        TarDescr='TrgNfaaHunt5',
        TarArray='TrgNfaaHunt5',
        TarStars='adef',
        TarOrder='20',
        TarFullSize='50',
        D_size='50',
        D_color='000000',
        E_size='30',
        E_color='000000',
        F_size='10',
        F_color='FFFFFF',
        Z_size='5',
        Z_color='FFFFFF'");
	// Hunter Target 3-X with X as 6
	safe_w_sql("insert into Targets set
		TarId=21,
        TarDescr='TrgNfaaHunt6',
        TarArray='TrgNfaaHunt6',
        TarStars='adef',
        TarOrder='21',
        TarFullSize='50',
        D_size='50',
        D_color='000000',
        E_size='30',
        E_color='000000',
        F_size='10',
        F_color='FFFFFF',
        Y_size='5',
        Y_color='FFFFFF'");
	// Animal Round Target
	// 10 => L
	// 12 => N,
	// 13 => 1,
	// 14 => 2,
	// 16 => 3,
	// 17 => 4,
	// 18 => 5,
	// 20 => V,
	// 21 => 6,
	safe_w_sql("insert into Targets set
		TarId=22,
        TarDescr='TrgNfaaAnimal',
        TarArray='TrgNfaaAnimal',
        TarStars='alnstvw',
        TarOrder='22',
        TarFullSize='0',
        L_size='50',
        N_size='30',
        1_size='10',
        2_size='50',
        3_size='30',
        4_size='10',
        5_size='50',
        V_size='30',
        6_size='10',
        L_color='888888',
        N_color='888888',
        1_color='888888',
        2_color='888888',
        3_color='888888',
        4_color='888888',
        5_color='888888',
        V_color='888888',
        6_color='888888'
        ");

	// add the 2 Tournament types
	safe_w_sql("insert into TourTypes set TtId=46, TtType='Type_NFAA_Target', TtDistance=6, TtOrderBy=46, TtWaEquivalent=0 on duplicate key update TtType='Type_NFAA_Target', TtDistance=6, TtOrderBy=46, TtWaEquivalent=0");
	safe_w_sql("insert into TourTypes set TtId=47, TtType='Type_NFAA_Field', TtDistance=3, TtOrderBy=47, TtWaEquivalent=0 on duplicate key update TtType='Type_NFAA_Field', TtDistance=3, TtOrderBy=47, TtWaEquivalent=0");
    db_save_version('2021-07-08 11:46:05');
}

if($version<'2021-07-10 11:13:01') {
	$q="ALTER TABLE TargetFaces change TfWaTarget TfWaTarget varchar(25) NOT NULL";
	$r=safe_w_sql($q,false,array(1146, 1060));

	db_save_version('2021-07-10 11:13:01');
}

if($version<'2021-08-09 12:21:00') {
    $q="ALTER TABLE `Finals` ADD `FinCoach` INT UNSIGNED NOT NULL AFTER `FinIrmType`";
    $r=safe_w_sql($q,false,array(1146, 1060));
    $q="ALTER TABLE `TeamFinals` ADD `TfCoach` INT UNSIGNED NOT NULL AFTER `TfIrmType`";
    $r=safe_w_sql($q,false,array(1146, 1060));
    db_save_version('2021-08-09 12:21:00');
}

if($version<'2021-08-18 11:13:01') {
	safe_w_sql("ALTER TABLE Tournament ADD ToCountry varchar(3) NOT NULL after ToVenue",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE TournamentInvolved ADD TiGivenName varchar(64) NOT NULL, add TiCountry varchar(3) not null, add TiGender varchar(1) not null",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE FinSchedule ADD FsJLine int(11) NOT NULL, add FsTJudge int(11) not null",false,array(1146, 1060));

	safe_w_sql("insert into InvolvedType values (0, 'ADOfficer',0,0,0,9), (0, 'MedOfficer',0,0,0,10), (0, 'CompManager',0,0,0,11), (0, 'ResVerifier',0,0,0,12) ");

	db_save_version('2021-08-18 11:13:01');
}

if($version<'2021-08-20 11:13:01') {
	safe_w_sql("ALTER TABLE `TournamentInvolved` change TiGender TiGender tinyint not null default 0",false,array(1146, 1060));
	db_save_version('2021-08-20 11:13:01');
}

if($version<'2021-08-21 01:26:00') {
    safe_w_sql("ALTER TABLE `FinSchedule` CHANGE `FsJLine` `FsLJudge` INT NOT NULL;",false,array(1146, 1060));
    db_save_version('2021-08-21 01:26:00');
}
if($version<'2021-08-21 10:13:01') {
	safe_w_sql("ALTER TABLE TournamentInvolved change TiCountry TiCountry int unsigned NOT NULL",false,array(1146, 1060));

	db_save_version('2021-08-21 10:13:01');
}
if($version<'2021-12-05 14:18:01') {
    safe_w_sql("DELETE FROM `LookUpPaths` WHERE `LupIocCode` LIKE 'NOR%';",false,array());
    safe_w_sql("DELETE FROM `LookUpEntries` WHERE `LueIocCode` LIKE 'NOR%';",false,array());
    db_save_version('2021-12-05 14:18:01');
}
if($version<'2021-12-21 17:20:00') {
    safe_w_sql("ALTER TABLE `AccColors` CHANGE `AcDivClass` `AcDivClass` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `AccPrice` CHANGE `APDivClass` `APDivClass` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Awarded` CHANGE `AwDivision` `AwDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `AwClass` `AwClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `CasScore` CHANGE `CaSEventCode` `CaSEventCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `CasTeam` CHANGE `CaEventCode` `CaEventCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `CasTeamFinal` CHANGE `CTFEvent` `CTFEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `CasTeamTarget` CHANGE `CTTEvent` `CTTEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Classes` CHANGE `ClId` `ClId` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `ClassWaEquivalents` CHANGE `ClWaEqEvent` `ClWaEqEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `ClWaEqDivision` `ClWaEqDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `ClWaEqAgeClass` `ClWaEqAgeClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `ClubTeam` CHANGE `CTEventCode` `CTEventCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `ClubTeamScore` CHANGE `CTSEventCode` `CTSEventCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Divisions` CHANGE `DivId` `DivId` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Eliminations` CHANGE `ElEventCode` `ElEventCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Entries` CHANGE `EnDivision` `EnDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EnClass` `EnClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EnAgeClass` `EnAgeClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("DROP VIEW `EventCategories`",false,array(1051, 1227));
    safe_w_sql("ALTER TABLE `EventClass` CHANGE `EcCode` `EcCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EcClass` `EcClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EcDivision` `EcDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Events` CHANGE `EvCode` `EvCode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EvRecCategory` `EvRecCategory` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EvWaCategory` `EvWaCategory` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `EvCodeParent` `EvCodeParent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `ExtraData` CHANGE `EdEvent` `EdEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `ExtraDataCountries` CHANGE `EdcEvent` `EdcEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Finals` CHANGE `FinEvent` `FinEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `FinOdfTiming` CHANGE `FinOdfEvent` `FinOdfEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `FinSchedule` CHANGE `FSEvent` `FSEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `FinWarmup` CHANGE `FwEvent` `FwEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `HeartBeat` CHANGE `HbEvent` `HbEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `HhtData` CHANGE `HdEvent` `HdEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",false,array());
    safe_w_sql("ALTER TABLE `HTTData` CHANGE `HtdEvent` `HtdEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",false,array());
    safe_w_sql("ALTER TABLE `Individuals` CHANGE `IndEvent` `IndEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `IndOldPositions` CHANGE `IopEvent` `IopEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `IskData` CHANGE `IskDtEvent` `IskDtEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `LookUpEntries` CHANGE `LueDivision` `LueDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `LueClass` `LueClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Rankings` CHANGE `RankEvent` `RankEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `RecBroken` CHANGE `RecBroRecCategory` `RecBroRecCategory` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `RecBroRecEvent` `RecBroRecEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `RecTournament` CHANGE `RtRecDivision` `RtRecDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `RtRecCategory` `RtRecCategory` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `RtRecLocalCategory` `RtRecLocalCategory` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Reviews` CHANGE `RevEvent` `RevEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `TargetFaces` CHANGE `TfClasses` `TfClasses` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `TeamComponent` CHANGE `TcEvent` `TcEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `TeamDavis` CHANGE `TeDaEvent` `TeDaEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `TeamFinals` CHANGE `TfEvent` `TfEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `TeamFinComponent` CHANGE `TfcEvent` `TfcEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `Teams` CHANGE `TeEvent` `TeEvent` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `TournamentDistances` CHANGE `TdClasses` `TdClasses` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    safe_w_sql("ALTER TABLE `VegasAwards` CHANGE `VaDivision` `VaDivision` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `VaClass` `VaClass` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",false,array());
    db_save_version('2021-12-21 17:20:00');
}
if($version<'2021-12-25 16:00:05') {
    safe_w_sql("DROP TABLE if exists `F2FEntries`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `F2FFinal`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `F2FGrid`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `FinGroups`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `GuessWho`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `GuessWhoData`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `PrintOuts`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `PrintOutsRules`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `Raspberries`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `VxA`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `FinTraining`", false,array(1051));
    safe_w_sql("DROP TABLE if exists `FinTrainingEvent`", false,array(1051));
    db_save_version('2021-12-25 16:00:05');
}

if($version<'2021-12-26 10:13:01') {
	safe_w_sql("drop TABLE if exists RoundRobinLevel");
	safe_w_sql("Create TABLE RoundRobinLevel (
		RrLevTournament int unsigned not null,
		RrLevTeam tinyint unsigned not null,
		RrLevEvent varchar(10) not null,
		RrLevLevel int unsigned not null,
		RrLevName varchar(100) not null,
		RrLevGroups int unsigned not null,
		RrLevGroupArchers int unsigned not null,
		RrLevArrows tinyint unsigned not null,
		RrLevEnds tinyint unsigned not null,
		RrLevSO tinyint unsigned not null,
		RrLevTieAllowed tinyint unsigned not null,
		RrLevWinPoints tinyint unsigned not null,
		RrLevTiePoints tinyint unsigned not null,
		RrLevTieBreakSystem tinyint unsigned not null,
		RrLevSoSolved tinyint unsigned not null,
		RrLevDateTime date default '0000-00-00 00:00:00' not null,
		primary key (RrLevTournament, RrLevLevel, RrLevEvent, RrLevTeam)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("drop TABLE if exists RoundRobinGroup");
	safe_w_sql("Create TABLE RoundRobinGroup (
		RrGrTournament int unsigned not null,
		RrGrTeam tinyint unsigned not null,
		RrGrEvent varchar(10) not null,
		RrGrLevel int unsigned not null,
		RrGrGroup int unsigned not null,
		RrGrName varchar(100) not null,
		RrGrSession tinyint unsigned not null,
		RrGrTargetArchers tinyint unsigned not null default 1,
		RrGrArcherWaves tinyint unsigned not null default 0,
		RrGrSoSolved tinyint unsigned not null,
		RrGrDateTime date default '0000-00-00 00:00:00' not null,
		primary key (RrGrTournament, RrGrLevel, RrGrGroup, RrGrEvent, RrGrTeam)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("drop TABLE if exists RoundRobinMatches");
	safe_w_sql("create table RoundRobinMatches (
		RrMatchTournament int unsigned default '0' not null,
		RrMatchTeam tinyint unsigned default '0' not null,
		RrMatchEvent varchar(10) not null,
		RrMatchLevel int unsigned default '0' not null,
		RrMatchGroup int unsigned default '0' not null,
		RrMatchRound tinyint unsigned default '0' not null,
		RrMatchMatchNo tinyint unsigned default '0' not null,
		RrMatchTarget varchar(5) not null,
		RrMatchScheduledDate date default '0000-00-00' not null,
		RrMatchScheduledTime time default '00:00:00' not null,
		RrMatchScheduledLength smallint default '0' not null,
		RrMatchAthlete int unsigned default '0' not null,
		RrMatchSubTeam tinyint unsigned default '0' not null,
		RrMatchRank tinyint unsigned default '0' not null,
		RrMatchScore smallint default 0 not null,
		RrMatchSetScore tinyint default 0 not null,
		RrMatchSetPoints varchar(36) not null,
		RrMatchSetPointsByEnd varchar(36) not null,
		RrMatchWinnerSet tinyint default 0 not null,
		RrMatchTie tinyint(1) default 0 not null,
		RrMatchArrowstring varchar(60) not null,
		RrMatchTiebreak varchar(10) not null,
		RrMatchTbClosest tinyint not null,
		RrMatchTbDecoded varchar(15) not null,
		RrMatchArrowPosition text not null,
		RrMatchTiePosition text not null,
		RrMatchWinLose tinyint unsigned default '0' not null,
		RrMatchFinalRank tinyint unsigned default '0' not null,
		RrMatchDateTime datetime default '0000-00-00 00:00:00' not null,
		RrMatchSyncro datetime not null,
		RrMatchLive tinyint default 0 not null,
		RrMatchStatus tinyint default 0 not null,
		RrMatchShootFirst tinyint not null,
		RrMatchVxF tinyint default 0 not null,
		RrMatchConfirmed int not null,
		RrMatchNotes varchar(30) not null,
		RrMatchRecordBitmap tinyint unsigned not null,
		RrMatchIrmType tinyint not null,
		RrMatchCoach int unsigned not null,
		RrMatchRoundPoints int unsigned not null,
		RrMatchTieBreaker int signed not null,
		primary key (RrMatchTournament, RrMatchLevel, RrMatchGroup, RrMatchEvent, RrMatchTeam, RrMatchRound, RrMatchMatchNo)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("drop TABLE if exists RoundRobinParticipants");
	safe_w_sql("create table RoundRobinParticipants (
		RrPartTournament int unsigned default '0' not null,
		RrPartTeam tinyint unsigned default '0' not null,
		RrPartEvent varchar(10) default '' not null,
		RrPartLevel int unsigned default '0' not null,
		RrPartGroup int unsigned default '0' not null,
		RrPartSourceLevel int unsigned default '0' not null,
		RrPartSourceGroup int unsigned default '0' not null,
		RrPartSourceRank int unsigned default '0' not null,
		RrPartDestItem int unsigned default '0' not null,
		RrPartParticipant int unsigned default '0' not null,
		RrPartSubTeam int unsigned default '0' not null,
		RrPartIrmType tinyInt default '0' not null,
		RrPartPoints int unsigned default '0' not null,
		RrPartTieBreaker int signed default '0' not null,
		RrPartGroupRankBefSO int signed default '0' not null,
		RrPartGroupTiesForSO tinyint unsigned default '0' not null,
		RrPartGroupTiesForCT tinyint unsigned default '0' not null,
		RrPartGroupRank int signed default '0' not null,
		RrPartGroupTieBreak varchar(24) default '' not null,
		RrPartGroupTbDecoded varchar(24) default '' not null,
		RrPartGroupTbClosest tinyInt default '0' not null,
		RrPartLevelRankBefSO int signed default '0' not null,
		RrPartLevelTiesForSO tinyint unsigned default '0' not null,
		RrPartLevelTiesForCT tinyint unsigned default '0' not null,
		RrPartLevelRank int signed default '0' not null,
		RrPartLevelTieBreak varchar(24) default '' not null,
		RrPartLevelTbDecoded varchar(24) default '' not null,
		RrPartLevelTbClosest tinyInt default '0' not null,
		RrPartDateTime datetime default '0000-00-00 00:00:00' not null,
		primary key (RrPartTournament, RrPartLevel, RrPartGroup, RrPartEvent, RrPartTeam, RrPartDestItem)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("drop TABLE if exists RoundRobinGrids");
	safe_w_sql("create table RoundRobinGrids (
		RrGridTournament int unsigned default '0' not null,
		RrGridTeam tinyint unsigned default '0' not null,
		RrGridEvent varchar(10) not null,
		RrGridLevel int unsigned default '0' not null,
		RrGridGroup int unsigned default '0' not null,
		RrGridRound int unsigned default '0' not null,
		RrGridItem int unsigned default '0' not null,
		RrGridMatchno int unsigned default '0' not null,
		primary key (RrGridTournament, RrGridLevel, RrGridGroup, RrGridEvent, RrGridTeam, RrGridRound, RrGridItem),
		unique (RrGridTournament, RrGridLevel, RrGridGroup, RrGridEvent, RrGridTeam, RrGridRound, RrGridMatchno)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	db_save_version('2021-12-26 10:13:01');
}
