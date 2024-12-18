<?php

if($version<'2023-01-02 10:13:02') {
	safe_w_sql("ALTER TABLE `IskData` ADD IskDtIsClosest tinyint not null",false,array(1146, 1060));

	db_save_version('2023-01-02 10:13:02');
}

if($version<'2023-02-09 10:13:02') {
	safe_w_sql("update InvolvedType set ItJudge=3 where ItDescription='Judge'");
	$SQL="ItId=%s, ItDescription='%s', ItJudge=%s, ItDoS=0, ItJury=0, ItOC=0";
	safe_w_sql("insert into InvolvedType set ".($a=sprintf($SQL, 20, 'ChairmanJudgeDeputy', 2))." on duplicate key update $a");
	safe_w_sql("insert into InvolvedType set ".($a=sprintf($SQL, 21, 'RaceOfficer', 4))." on duplicate key update $a");
	safe_w_sql("insert into InvolvedType set ".($a=sprintf($SQL, 22, 'Spotter', 5))." on duplicate key update $a");

	safe_w_sql("ALTER TABLE Events 
    	ADD EvArrowPenalty mediumint unsigned not null default 120, 
    	ADD EvLoopPenalty mediumint unsigned not null default 120",false,array(1146, 1060));

	safe_w_sql("ALTER TABLE `RunArcheryRank` 
    	add RarPool tinyint not null after RarPhase, 
    	ADD RarIrmType tinyint not null, 
    	ADD RarRankClass int unsigned not null after RarRank, 
    	ADD RarFromRank int not null, 
    	add RarFromType tinyint not null,
    	ADD RarQualified varchar(1) not null, 
    	ADD RarTarget int not null,
    	ADD RarNotes tinytext not null, 
    	add RarShift int not null, 
    	add RarDuration int not null, 
    	ADD RarCallTime time not null,
    	ADD RarWarmup time not null, 
    	ADD RarWarmupDuration int not null,
    	add INDEX (RarTournament, RarTeam, RarEvent, RarPhase, RarFromType, RarFromRank ), 
    	add index (RarTournament, RarTeam, RarEvent, RarPhase, RarRankClass)",false,array(1146, 1060));
	safe_w_sql("alter table RunArcheryRank drop primary key");
	safe_w_sql("alter table RunArcheryRank add primary key (RarTournament, RarTeam, RarEvent, RarPhase, RarPool, RarEntry, RarSubTeam)");

	safe_w_sql("ALTER TABLE `RunArchery` 
    	add RaPool tinyint not null after RaPhase, 
    	ADD RaFromRank int not null, 
    	add RaFromType tinyint not null, 
    	add INDEX (RaTournament, RaTeam, RaEvent, RaPhase, RaFromType, RaFromRank ) ",false,array(1146, 1060));
	safe_w_sql("alter table RunArchery drop primary key");
	safe_w_sql("alter table RunArchery add primary key (RaTournament, RaTeam, RaEvent, RaPhase, RaEntry, RaSubTeam, RaPool, RaLap)");

	db_save_version('2023-02-09 10:13:02');
}

if($version<'2023-03-20 05:26:04') {
    safe_w_sql("CREATE TABLE IF NOT EXISTS `TeamFinComponentLog` (
      `TfclCoId` int NOT NULL,
      `TfclSubTeam` tinyint NOT NULL,
      `TfclTournament` int NOT NULL,
      `TfclEvent` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
      `TfclIdPrev` int UNSIGNED NOT NULL,
      `TfclIdNext` int UNSIGNED NOT NULL,
      `TfclOrder` tinyint NOT NULL,
      `TfclTimeStamp` datetime NOT NULL,
      PRIMARY KEY (`TfclCoId`, `TfclSubTeam`, `TfclTournament`, `TfclEvent`, `TfclOrder`, `TfclTimeStamp`)
    ) ENGINE=InnoDB ",false,array(1146, 1060));
    updateTeamFinComponentsLog_20220320();
    db_save_version('2023-03-20 05:26:04');
}

if($version<'2023-03-22 15:26:04') {
    safe_w_sql("ALTER TABLE RoundRobinLevel add RrLevTieBreakSystem2 tinyint unsigned not null after RrLevTieBreakSystem",false,array(1146, 1060));
    db_save_version('2023-03-22 15:26:04');
}

if($version<'2023-03-23 15:26:04') {
    safe_w_sql("ALTER TABLE RoundRobinMatches add RrMatchTieBreaker2 int signed not null after RrMatchTieBreaker",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE RoundRobinParticipants add RrPartTieBreaker2 int signed not null after RrPartTieBreaker",false,array(1146, 1060));
    db_save_version('2023-03-23 15:26:04');
}

if($version<'2023-04-06 18:26:04') {
    safe_w_sql("ALTER TABLE DistanceInformation add DiScoringEnds int unsigned not null, add DiScoringOffset int unsigned not null",false,array(1146, 1060));
	// change of table engine from innodb to myisam
	$q=safe_r_sql("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$CFG->DB_NAME."' and engine='innodb';");
	while($r=safe_fetch($q)) {
		safe_w_sql("ALTER TABLE $r->TABLE_NAME ENGINE = MyISAM");
	}
	db_save_version('2023-04-06 18:26:04');
}

if($version<'2023-04-16 09:00:00') {
    safe_w_sql("ALTER TABLE `Session` ADD `SesLocation` VARCHAR(255) NOT NULL AFTER `SesOdfLocation`",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE `Scheduler` ADD `SchLocation` VARCHAR(255) NOT NULL AFTER `SchLink`",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE `EventClass` ADD `EcExtraAddons` INT UNSIGNED NOT NULL AFTER `EcSubClass`",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE `EventClass` DROP PRIMARY KEY, 
        ADD PRIMARY KEY (`EcCode`, `EcTeamEvent`, `EcTournament`, `EcClass`, `EcDivision`, `EcSubClass`, `EcExtraAddons`)",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE `EventClass` DROP INDEX `EcClass`, 
        ADD INDEX `EcClass` (`EcClass`, `EcDivision`, `EcTournament`, `EcSubClass`, `EcExtraAddons`)",false,array(1146, 1060));
    db_save_version('2023-04-16 09:00:00');
}

if($version<'2023-04-27 20:00:00') {
    safe_w_sql("INSERT INTO `TourTypes` (`TtId`, `TtType`, `TtDistance`, `TtOrderBy`, `TtWaEquivalent`) VALUES('49', 'Type_NFAA_3D', '2', '49', '0')",false,array(1146, 1060));
    safe_w_sql("INSERT INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, 
       `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, 
       `TarDummyLine`, `1_size`, `1_color`, `2_size`, `2_color`, `3_size`, `3_color`, `4_size`, `4_color`, `5_size`, `5_color`, `6_size`, `6_color`, `7_size`, `7_color`, `8_size`, `8_color`, `9_size`, `9_color`, `TarIskDefinition`) 
        VALUES ('23', 'Trg3DRedding', 'Trg3DRedding', 'afil', '22', '0', 
        '0', '', '0', 'FFFFFF', '0', 'FFFFFF', '0', '000000', '0', '000000', '0', '000000', '0', '000000', '0', 'ED2939', '30', '00A3D1', '0', 'F9E11E', '0', 'F9E11E', '15', 'ED2939', '5', 'F9E11E', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', 
        '0', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '')",false,array(1146, 1060));
	db_save_version('2023-04-27 20:00:00');
}

if($version<'2023-05-03 20:00:00') {
    safe_w_sql("REPLACE INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`, `1_size`, `1_color`, `2_size`, `2_color`, `3_size`, `3_color`, `4_size`, `4_color`, `5_size`, `5_color`, `6_size`, `6_color`, `7_size`, `7_color`, `8_size`, `8_color`, `9_size`, `9_color`, `TarIskDefinition`) VALUES
        (23, 'Trg3DReddingEnd', 'Trg3DReddingEnd', 'afil', 22, 0, 100, '', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 0, '000000', 90, '00A3D1', 0, '000000', 0, '000000', 80, '00A3D1', 70, '00A3D1', 0, '', 0, '', 0, '', 0, '', 65, 'ED2939', 20, 'F9E11E', 10, 'F9E11E', 60, 'ED2939', 55, 'F9E11E', 67, 'ED2939', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', '')");
	db_save_version('2023-05-03 20:00:00');
}

if($version<'2023-06-27 14:00:00') {
    safe_w_sql("alter table FinOdfTiming
		change FinOdfPrepare FinOdfGettingReady datetime not null,
		change FinOdfBegin FinOdfLive datetime not null,
		change FinOdfEnd FinOdfUnconfirmed datetime not null,
		change FinOdfConfirmed FinOdfOfficial datetime not null
	");
	db_save_version('2023-06-27 14:00:00');
}

if($version<'2023-07-11 14:00:00') {
    safe_w_sql("insert into LookUpPaths set LupIocCode='FRA', LupFlagsPath='http://www-2022.ffta.fr/ianseo/' on duplicate key update LupFlagsPath='http://www-2022.ffta.fr/ianseo/'");
	db_save_version('2023-07-11 14:00:00');
}

if($version<'2023-07-29 10:00:00') {
    safe_w_sql("alter table Parameters change ParId ParId varchar(32) not null");
	db_save_version('2023-07-29 10:00:00');
}

if($version<'2023-09-17 10:00:00') {
    safe_w_sql("alter table IskDevices add IskDvSetupConfirmed tinyint not null default 0");
    db_save_version('2023-09-17 10:00:00');
}

if($version<'2023-09-18 10:00:00') {
    safe_w_sql("alter table IskDevices add IskDvPersonal tinyint not null default 0");
    db_save_version('2023-09-18 10:00:00');
}

if($version<'2023-10-29 22:30:00') {
    safe_w_sql("ALTER TABLE `TournamentInvolved` ADD `TiCodeLocal` VARCHAR(32) NOT NULL AFTER `TiCode`");
    db_save_version('2023-10-29 22:30:00');
}

if($version<'2023-11-16 10:30:00') {
    safe_w_sql("UPDATE `Qualifications` SET 
        QuD1Arrowstring=REPLACE(QuD1Arrowstring,'R','5'),
        QuD2Arrowstring=REPLACE(QuD2Arrowstring,'R','5'),
        QuD3Arrowstring=REPLACE(QuD3Arrowstring,'R','5'),
        QuD4Arrowstring=REPLACE(QuD4Arrowstring,'R','5'),
        QuD5Arrowstring=REPLACE(QuD5Arrowstring,'R','5'),
        QuD6Arrowstring=REPLACE(QuD6Arrowstring,'R','5'),
        QuD7Arrowstring=REPLACE(QuD7Arrowstring,'R','5'),
        QuD8Arrowstring=REPLACE(QuD8Arrowstring,'R','5')");
    safe_w_sql("UPDATE `Qualifications` SET 
        QuD1Arrowstring=REPLACE(QuD1Arrowstring,'S','6'),
        QuD2Arrowstring=REPLACE(QuD2Arrowstring,'S','6'),
        QuD3Arrowstring=REPLACE(QuD3Arrowstring,'S','6'),
        QuD4Arrowstring=REPLACE(QuD4Arrowstring,'S','6'),
        QuD5Arrowstring=REPLACE(QuD5Arrowstring,'S','6'),
        QuD6Arrowstring=REPLACE(QuD6Arrowstring,'S','6'),
        QuD7Arrowstring=REPLACE(QuD7Arrowstring,'S','6'),
        QuD8Arrowstring=REPLACE(QuD8Arrowstring,'S','6')");
    safe_w_sql("UPDATE `Qualifications` SET 
        QuD1Arrowstring=REPLACE(QuD1Arrowstring,'T','8'),
        QuD2Arrowstring=REPLACE(QuD2Arrowstring,'T','8'),
        QuD3Arrowstring=REPLACE(QuD3Arrowstring,'T','8'),
        QuD4Arrowstring=REPLACE(QuD4Arrowstring,'T','8'),
        QuD5Arrowstring=REPLACE(QuD5Arrowstring,'T','8'),
        QuD6Arrowstring=REPLACE(QuD6Arrowstring,'T','8'),
        QuD7Arrowstring=REPLACE(QuD7Arrowstring,'T','8'),
        QuD8Arrowstring=REPLACE(QuD8Arrowstring,'T','8')");
    safe_w_sql("UPDATE `Qualifications` SET 
        QuD1Arrowstring=REPLACE(QuD1Arrowstring,'U','7'),
        QuD2Arrowstring=REPLACE(QuD2Arrowstring,'U','7'),
        QuD3Arrowstring=REPLACE(QuD3Arrowstring,'U','7'),
        QuD4Arrowstring=REPLACE(QuD4Arrowstring,'U','7'),
        QuD5Arrowstring=REPLACE(QuD5Arrowstring,'U','7'),
        QuD6Arrowstring=REPLACE(QuD6Arrowstring,'U','7'),
        QuD7Arrowstring=REPLACE(QuD7Arrowstring,'U','7'),
        QuD8Arrowstring=REPLACE(QuD8Arrowstring,'U','7')");
    safe_w_sql("UPDATE `Targets` SET `R_size` = 0,`R_color` = '', `S_size` = 0,`S_color` = '', `T_size` = 0,`T_color` = '', `U_size` = 0,`U_color` = '', 
        `5_size` = 65,`5_color` = 'ED2939',`6_size` = 20,`6_color` = 'F9E11E',`8_size` = 10,`8_color` = 'F9E11E',`7_size` = 60,`7_color` = 'ED2939' WHERE `TarId` = 23",false,array(1146, 1060));
    safe_w_sql("UPDATE `Targets` SET `R_size` = '2', `R_color` = 'f4f4f4' WHERE `TarId` = 13",false,array(1146, 1060));
    safe_w_sql("REPLACE INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`, `1_size`, `1_color`, `2_size`, `2_color`, `3_size`, `3_color`, `4_size`, `4_color`, `5_size`, `5_color`, `6_size`, `6_color`, `7_size`, `7_color`, `8_size`, `8_color`, `9_size`, `9_color`, `TarIskDefinition`) VALUES
        (24, 'TrgLancShootUp', 'TrgLancShootUp', 'a-j', 24, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, '', 10, 'F9E11E', 0, '', 2, 'FFFFFF', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 5, 'F9E11E', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', '')",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE `Teams` ADD `TeIsValidTeam` TINYINT NOT NULL AFTER `TeIrmTypeFinal`");
    safe_w_sql("CREATE TABLE `TeamEligibleComponent` (
        `TecEvent` VARCHAR(10) NOT NULL , 
        `TecId` INT NOT NULL , 
        `TecTournament` INT NOT NULL , 
        `TecECTeamEvent` TINYINT NOT NULL , 
        `TecCoId` INT NOT NULL , 
        PRIMARY KEY (`TecEvent`, `TecId`, `TecTournament`, `TecECTeamEvent`))",false,array(1146, 1060));
    db_save_version('2023-11-16 10:30:00');
}

if($version<'2023-11-19 10:00:00') {
    safe_w_sql("alter table Finals add FinGolds tinyint not null after FinScore, add FinXNines tinyint not null after FinGolds",false,array(1146, 1060));
    safe_w_sql("alter table TeamFinals add TfGolds tinyint not null after TfScore, add TfXNines tinyint not null after TfGolds",false,array(1146, 1060));
    safe_w_sql("alter table Events add EvCodeParentWinnerBranch tinyint not null after EvCodeParent, add EvCheckGolds tinyint not null after EvXNineChars, add EvCheckXNines tinyint not null after EvCheckGolds",false,array(1146, 1060));
    db_save_version('2023-11-19 10:00:00');
}

if($version<'2023-11-26 10:00:00') {
    safe_w_sql("alter table Qualifications add QuTieWeight bigint unsigned not null after QuTie, change QuTieBreak QuTieBreak varchar(50) not null, add index (QuScore, QuGold, QuXnine, QuTieWeight)",false,array(1146, 1060));
    db_save_version('2023-11-26 10:00:00');
}

if($version<'2023-11-27 10:00:00') {
    safe_w_sql("alter table Qualifications add QuTieWeightDecoded varchar(80) not null after QuTieWeight",false,array(1146, 1060));
    safe_w_sql("alter table Individuals add IndTieWeightDecoded varchar(80) not null after IndTbDecoded",false,array(1146, 1060));
    db_save_version('2023-11-27 10:00:00');
}

if($version<'2023-11-27 10:00:01') {
    safe_w_sql("alter table Qualifications add QuTieWeightDrops text not null after QuTieWeight",false,array(1146, 1060));
    db_save_version('2023-11-27 10:00:01');
}
if($version<'2023-11-28 10:00:01') {
    safe_w_sql("alter table Events change EvCodeParentWinnerBranch EvCodeParentWinnerBranch tinyint not null",false,array(1146, 1060));
    safe_w_sql("alter table RoundRobinMatches add RrMatchGolds tinyint unsigned not null, add RrMatchXNines tinyint unsigned not null",false,array(1146, 1060));
    db_save_version('2023-11-28 10:00:01');
}

if($version<'2023-12-18 10:00:01') {
    safe_w_sql("ALTER TABLE TeamEligibleComponent ADD TecSubTeam TINYINT NOT NULL AFTER TecCoId;",false,array(1146, 1060));
    safe_w_sql("alter table RoundRobinLevel add RrLevCheckGolds tinyint not null after RrLevTieBreakSystem2, add RrLevCheckXNines tinyint not null after RrLevCheckGolds",false,array(1146, 1060));
    db_save_version('2023-12-18 10:00:01');
}

if($version<'2023-12-18 17:00:01') {
    safe_w_sql("alter table RoundRobinMatches add RrMatchSwapped tinyint not null after RrMatchMatchNo",false,array(1146, 1060));
    db_save_version('2023-12-18 17:00:01');
}

if($version<'2023-12-19 16:10:00') {
    if(is_file($CFG->DOCUMENT_PATH.'Update/UpdateIanseo.php') AND is_writable($CFG->DOCUMENT_PATH.'Update/UpdateIanseo.php')) {
        file_put_contents($CFG->DOCUMENT_PATH.'Update/UpdateIanseo.php', str_replace("'Update'", "'------'", file_get_contents($CFG->DOCUMENT_PATH.'Update/UpdateIanseo.php')));
    }
    db_save_version('2023-12-19 16:10:00');
}

if($version<'2023-12-31 10:00:00') {
    safe_w_sql("alter table Individuals add IndFinTieWeightDecoded varchar(80) not null after IndTieWeightDecoded",false,array(1146, 1060));
    db_save_version('2023-12-31 10:00:00');
}
