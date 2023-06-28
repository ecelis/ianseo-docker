<?php

if($version<'2022-01-27 10:13:01') {
	safe_w_sql("ALTER TABLE `RoundRobinLevel` ADD `RrLevMatchMode` tinyint NOT NULL after RrLevLevel",false,array(1146, 1060));
	safe_w_sql("ALTER TABLE `RoundRobinLevel` ADD `RrLevBestRankMode` tinyint NOT NULL after RrLevMatchMode",false,array(1146, 1060));

	db_save_version('2022-01-27 10:13:01');
}

if($version<'2022-02-06 10:13:02') {
	safe_w_sql("ALTER TABLE `EventClass` drop index EcClass", false, array(1091));
	safe_w_sql("ALTER TABLE `EventClass` ADD INDEX (`EcClass`, `EcDivision`, `EcTournament`, `EcSubClass`)",false,array(1146, 1060));
	db_save_version('2022-02-06 10:13:02');
}

if($version<'2022-02-10 12:22:04') {
    safe_w_sql("ALTER TABLE `Individuals` ADD INDEX `IndTournament` (`IndTournament`);", false, array(1146, 1060, 1061));
    safe_w_sql("ALTER TABLE `Individuals` ADD INDEX `IndividualCheckSo` (`IndId`, `IndEvent`, `IndTournament`, `IndSO`);", false, array(1146, 1060, 1061));
    safe_w_sql("ALTER TABLE `TournamentInvolved` ADD INDEX `TiTournament` (`TiTournament`);", false, array(1146, 1060, 1061));
    safe_w_sql("ALTER TABLE `TVContents` ADD INDEX `TVCTournament` (`TVCTournament`);", false, array(1146, 1060, 1061));
    safe_w_sql("ALTER TABLE `IrmTypes` ADD INDEX `lnkIrmShowRank` (`IrmId`, `IrmShowRank`);", false, array(1146, 1060, 1061));
    db_save_version('2022-02-10 12:22:04');
}

if($version<'2022-03-04 10:13:01') {
	safe_w_sql("delete from ClubTeamScore where CTSTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from FinWarmup where FwTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from IndOldPositions where IopTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from Logs where LogTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from OdfDocuments where OdfDocTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from OdfMessageStatus where OmsTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from OdfTranslations where OdfTrTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from RoundRobinGrids where RrGridTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from RoundRobinGroup where RrGrTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from RoundRobinLevel where RrLevTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from RoundRobinMatches where RrMatchTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("delete from RoundRobinParticipants where RrPartTournament not in (select ToId from Tournament)",false, array(1146, 1060));
	safe_w_sql("DELETE FROM QualOldPositions WHERE QopId NOT IN (SELECT EnId From Entries)",false, array(1146, 1060));

	db_save_version('2022-03-04 10:13:01');
}

if($version<'2022-05-15 18:13:01') {
	safe_w_sql("insert into TourTypes set TtId=48, TtType='Type_WA_RunArchery', TtDistance=1, TtOrderBy=48, TtWaEquivalent=22
		on duplicate key update TtType='Type_WA_RunArchery', TtDistance=1, TtOrderBy=48, TtWaEquivalent=22",false,array(1146, 1060));

	safe_w_sql("drop TABLE if exists RunArcheryRank");
	safe_w_sql("create table RunArcheryRank (
		RarTournament int unsigned default 0 not null,
		RarEntry int unsigned default 0 not null,
		RarSubTeam tinyint unsigned default 0 not null,
		RarTeam tinyint unsigned default 0 not null,
		RarEvent varchar(10) default '' not null,
		RarPhase tinyint not null,
		RarStartlist datetime not null,
		RarGroup tinyint unsigned not null,
		RarBib tinyint unsigned not null,
		RarDateTimeStart decimal(15,3) not null,
		RarDateTimeFinish decimal(15,3) not null,
		RarTimeTotal decimal (8,3) not null,
		RarArrowTotalPenalty decimal(5,1) not null,
		RarLoopTotalPenalty decimal(5,1) not null,
		RarTimeAdjustPlus decimal(5,1) not null,
		RarTimeAdjustMinus decimal(5,1) not null,
		RarTimeFinal decimal (8,3) not null,
		RarLaps tinyint not null,
		RarRank int not null,
		RarPoints int not null,
		RarLastUpdate datetime not null,
	    primary key (RarTournament, RarTeam, RarEvent, RarPhase, RarEntry, RarSubTeam),
	    index (RarTournament, RarTeam, RarEvent, RarPhase, RarLaps, RarTimeFinal),
	    index (RarTournament, RarTeam, RarEvent, RarPhase, RarRank)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	safe_w_sql("drop TABLE if exists RunArchery");
	safe_w_sql("create table RunArchery (
		RaTournament int unsigned default 0 not null,
		RaEntry int unsigned default 0 not null,
		RaSubTeam tinyint unsigned default 0 not null,
		RaTeam tinyint unsigned default 0 not null,
		RaEvent varchar(10) default '' not null,
		RaPhase tinyint not null,
		RaLap tinyint not null,
		RaLapTime decimal (8,3) not null,
		RaArrowsShot tinyint not null,
		RaHits tinyint not null,
		RaLoopAssigned tinyint not null,
		RaLoopDone tinyint not null,
		RaArrowPenalty decimal(5,1) not null,
		RaLoopPenalty decimal(5,1) not null,
		RaLastUpdate datetime not null,
	    primary key (RaTournament, RaTeam, RaEvent, RaPhase, RaEntry, RaSubTeam, RaLap)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	db_save_version('2022-05-15 18:13:01');
}

if($version<'2022-07-03 12:00:00') {
    safe_w_sql("ALTER TABLE `TargetFaces` 
        ADD `TfGoldsChars1` VARCHAR(16) NOT NULL AFTER `TfWaTarget`, 
        ADD `TfXNineChars1` VARCHAR(16) NOT NULL AFTER `TfGoldsChars1`,
        ADD `TfGoldsChars2` VARCHAR(16) NOT NULL AFTER `TfXNineChars1`, 
        ADD `TfXNineChars2` VARCHAR(16) NOT NULL AFTER `TfGoldsChars2`,
        ADD `TfGoldsChars3` VARCHAR(16) NOT NULL AFTER `TfXNineChars2`, 
        ADD `TfXNineChars3` VARCHAR(16) NOT NULL AFTER `TfGoldsChars3`,
        ADD `TfGoldsChars4` VARCHAR(16) NOT NULL AFTER `TfXNineChars3`, 
        ADD `TfXNineChars4` VARCHAR(16) NOT NULL AFTER `TfGoldsChars4`,
        ADD `TfGoldsChars5` VARCHAR(16) NOT NULL AFTER `TfXNineChars4`, 
        ADD `TfXNineChars5` VARCHAR(16) NOT NULL AFTER `TfGoldsChars5`,
        ADD `TfGoldsChars6` VARCHAR(16) NOT NULL AFTER `TfXNineChars5`, 
        ADD `TfXNineChars6` VARCHAR(16) NOT NULL AFTER `TfGoldsChars6`,
        ADD `TfGoldsChars7` VARCHAR(16) NOT NULL AFTER `TfXNineChars6`, 
        ADD `TfXNineChars7` VARCHAR(16) NOT NULL AFTER `TfGoldsChars7`,
        ADD `TfGoldsChars8` VARCHAR(16) NOT NULL AFTER `TfXNineChars7`, 
        ADD `TfXNineChars8` VARCHAR(16) NOT NULL AFTER `TfGoldsChars8`;", false, array(1146, 1060));

    safe_w_sql("ALTER TABLE `Targets` ADD `TarIskDefinition` TEXT NOT NULL AFTER `9_color`;", false, array(1146, 1060));

    db_save_version('2022-07-03 12:00:00');
}

if($version<'2022-07-09 10:13:02') {
	safe_w_sql("drop TABLE if exists RunArcheryParticipants");
	safe_w_sql("create table RunArcheryParticipants (
		RapTournament int unsigned default 0 not null,
		RapEntry int unsigned default 0 not null,
		RapEvent varchar(10) default '' not null,
		RapTeamEvent tinyint unsigned default 0 not null,
		RapParticipates tinyint unsigned default 0 not null,
		RapLastUpdate datetime not null,
	    primary key (RapTournament, RapEntry, RapEvent, RapTeamEvent)
		) DEFAULT CHARSET=utf8",false,array(1146, 1060));

	db_save_version('2022-07-09 10:13:02');
}

if($version<'2022-07-24 16:10:01') {
    safe_w_sql("ALTER TABLE `Qualifications` 
        CHANGE `QuD5Arrowstring` `QuD5Arrowstring` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `QuD6Arrowstring` `QuD6Arrowstring` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `QuD7Arrowstring` `QuD7Arrowstring` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
        CHANGE `QuD8Arrowstring` `QuD8Arrowstring` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
    db_save_version('2022-07-24 16:10:01');
}

if($version<'2022-07-29 12:19:01') {
    safe_w_sql("REPLACE INTO `LookUpPaths` (`LupIocCode`, `LupOrigin`, `LupPath`, `LupPhotoPath`, `LupFlagsPath`, `LupLastUpdate`, `LupRankingPath`, `LupClubNamesPath`, `LupRecordsPath`) 
        VALUES 
            ('SLO', '', 'https://slo.service.ianseo.net/IanseoData.php', 'https://slo.service.ianseo.net/GetPhoto.php', 'https://slo.service.ianseo.net/GetFlags.php', '0000-00-00 00:00:00', '', '', '')");
    db_save_version('2022-07-29 12:19:01');
}

if($version<'2022-08-09 10:13:01') {
	safe_w_sql("truncate table ClassWaEquivalents");
	safe_w_sql("ALTER TABLE `ClassWaEquivalents` ADD ClWaEqTournament int not null first, 
    	drop primary key, 
    	add primary key (ClWaEqTournament,ClWaEqTourRule, ClWaEqEvent, ClWaEqGender, ClWaEqDivision, ClWaEqAgeClass)",false,array(1146, 1060));

	db_save_version('2022-08-09 10:13:01');
}

if($version<'2022-08-11 10:13:01') {
	safe_w_sql("ALTER TABLE `ClassWaEquivalents` ADD ClWaEqNoEquivalences tinyint not null default 0",false,array(1146, 1060));
	db_save_version('2022-08-11 10:13:01');
}

if($version<'2022-09-17 10:13:01') {
	safe_w_sql("ALTER TABLE `RunArcheryRank` change RarBib RarBib varchar(10) not null",false,array(1146, 1060));

	db_save_version('2022-09-17 10:13:01');
}

if($version<'2022-09-18 10:13:02') {
	safe_w_sql("ALTER TABLE `RunArcheryParticipants` add RapSubTeam tinyint not null after RapTeamEvent, drop primary key, add primary key (RapTournament, RapEntry, RapEvent, RapTeamEvent, RapSubTeam) ",false,array(1146, 1060));
	db_save_version('2022-09-18 10:13:02');
}

if($version<'2022-09-19 10:13:02') {
	safe_w_sql("ALTER TABLE `RunArchery` add RaArcher int unsigned not null after RaLap",false,array(1146, 1060));
	db_save_version('2022-09-19 10:13:02');
}
if($version<'2022-09-26 19:40:01') {
    safe_w_sql("ALTER TABLE `Classes` CHANGE `ClValidClass` `ClValidClass` VARCHAR(255) NOT NULL DEFAULT ''", false, array(1146, 1060));
    db_save_version('2022-09-26 19:40:00');
}

if($version<'2022-10-13 22:25:00') {
    safe_w_sql("ALTER TABLE `ModulesParameters` CHANGE `MpValue` `MpValue` MEDIUMTEXT NOT NULL", false, array(1146, 1060));
    db_save_version('2022-10-13 22:25:00');
}

if($version<'2022-12-05 10:13:02') {
	safe_w_sql("ALTER TABLE Emails ADD EmIcs tinyint not null default 0",false,array(1146, 1060));

	db_save_version('2022-12-05 10:13:02');
}

if($version<'2022-12-05 16:13:02') {
	safe_w_sql("ALTER TABLE Scheduler ADD SchUID varchar(32) not null default ''",false,array(1146, 1060));
	safe_w_sql("update Scheduler set SchUID=md5(concat(SchTournament,SchOrder,SchDay,SchStart))");
	db_save_version('2022-12-05 16:13:02');
}
