<?php
include_once('UpdateFunctions.inc.php');

/*
ogni step viene salvato separatamente al proprio numero di versione...
creato un numero di versione DB apposito...
Se la versione è troppo vecchia include i vecchi file

*/

if($version <= '2011-01-01 00:00:00') require_once('Common/UpdateDb-2010.inc.php');
if($version <= '2012-01-01 00:00:00') require_once('Common/UpdateDb-2011.inc.php');
if($version <= '2013-01-01 00:00:00') require_once('Common/UpdateDb-2012.inc.php');
if($version <= '2014-01-01 00:00:00') require_once('Common/UpdateDb-2013.inc.php');
if($version <= '2015-01-01 00:00:00') require_once('Common/UpdateDb-2014.inc.php');
if($version <= '2016-01-01 00:00:00') require_once('Common/UpdateDb-2015.inc.php');
if($version <= '2017-01-01 00:00:00') require_once('Common/UpdateDb-2016.inc.php');
if($version <= '2018-01-01 00:00:00') require_once('Common/UpdateDb-2017.inc.php');
if($version <= '2019-01-01 00:00:00') require_once('Common/UpdateDb-2018.inc.php');
if($version <= '2020-01-01 00:00:00') require_once('Common/UpdateDb-2019.inc.php');
if($version <= '2021-01-01 00:00:00') require_once('Common/UpdateDb-2020.inc.php');
if($version <= '2022-01-01 00:00:00') require_once('Common/UpdateDb-2021.inc.php');
if($version <= '2023-01-01 00:00:00') require_once('Common/UpdateDb-2022.inc.php');
if($version <= '2024-01-01 00:00:00') require_once('Common/UpdateDb-2023.inc.php');

if($version<'2024-01-08 10:30:01') {
    // create the regular lancaster target
    safe_w_sql("REPLACE INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`, `1_size`, `1_color`, `2_size`, `2_color`, `3_size`, `3_color`, `4_size`, `4_color`, `5_size`, `5_color`, `6_size`, `6_color`, `7_size`, `7_color`, `8_size`, `8_color`, `9_size`, `9_color`, `TarIskDefinition`) VALUES
        (25, 'TrgLancaster', 'TrgLancaster', 'a-l', 24, 100, 0, '', 100, 'FFFFFF', 90, 'FFFFFF', 80, '000000', 70, '000000', 60, '00A3D1', 50, '00A3D1', 40, 'ED2939', 30, 'ED2939', 20, 'F9E11E', 0, '', 10, 'F9E11E', 5, 'F9E11E', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', '')",false,array(1146, 1060));
    // change X => M (X => 11 print value)
    safe_w_sql("UPDATE `Targets` SET `X_size` = 0, `X_color` = '', M_size=5, M_color='F9E11E' WHERE `TarId` = 24",false,array(1146, 1060));

    $q=safe_r_sql("select ToId from Tournament where ToLocRule='LANC'");
    while($r=safe_fetch($q)) {
        updateLancaster_20240108($r->ToId);
    }
    db_save_version('2024-01-08 10:30:01');
}

if($version<'2024-01-14 12:55:00') {
    safe_w_sql("UPDATE `TargetFaces` 
        inner join `Tournament` on ToId=TfTournament and ToLocRule!='LANC'
        SET TfGolds='', TfGoldsChars=''
        WHERE `TfGolds` LIKE '11' AND `TfXNine` = '' AND `TfGoldsChars` LIKE 'M' AND `TfXNineChars` = ''",false,array(1146, 1060));
    db_save_version('2024-01-14 12:55:00');
}

if($version<'2024-01-26 12:55:00') {
    safe_w_sql("ALTER TABLE `Qualifications` CHANGE `QuTieWeight` `QuTieWeight` CHAR(50) NOT NULL",false,array(1146, 1060));
    db_save_version('2024-01-26 12:55:00');
}

if($version<'2024-03-14 14:20:00') {
    safe_w_sql("ALTER TABLE `Divisions` CHANGE `DivDescription` `DivDescription` VARCHAR(50)",false,array(1146, 1060));
    safe_w_sql("ALTER TABLE `Classes` CHANGE `ClDescription` `ClDescription` VARCHAR(50)",false,array(1146, 1060));
    db_save_version('2024-03-14 14:20:00');
}

if($version<'2024-03-15 15:25:00') {
    safe_w_sql("UPDATE `Targets` SET `TarDescr` = 'TrgNfaaIndIX', `TarArray` = 'TrgNfaaIndIX' WHERE `Targets`.`TarId` = 13");
    safe_w_sql("INSERT INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`, `F_size`, `F_color`, `G_size`, `G_color`, `H_size`, `H_color`, `I_size`, `I_color`, `J_size`, `J_color`, `K_size`, `K_color`, `L_size`, `L_color`, `M_size`, `M_color`, `N_size`, `N_color`, `O_size`, `O_color`, `P_size`, `P_color`, `Q_size`, `Q_color`, `R_size`, `R_color`, `S_size`, `S_color`, `T_size`, `T_color`, `U_size`, `U_color`, `V_size`, `V_color`, `W_size`, `W_color`, `X_size`, `X_color`, `Y_size`, `Y_color`, `Z_size`, `Z_color`, `TarDummyLine`, `1_size`, `1_color`, `2_size`, `2_color`, `3_size`, `3_color`, `4_size`, `4_color`, `5_size`, `5_color`, `6_size`, `6_color`, `7_size`, `7_color`, `8_size`, `8_color`, `9_size`, `9_color`, `TarIskDefinition`) 
        VALUES (26, 'TrgNfaaInd', 'TrgNfaaInd', '', '13', '40', '0', '', '40', '000080', '32', '000080', '24', '000080', '16', '000080', '8', 'f4f4f4', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '4', 'f4f4f4', '0', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '')", false, array(1146, 1060));
    db_save_version('2024-03-15 15:25:00');
}
if($version<'2024-03-15 22:35:00') {
    safe_w_sql("ALTER TABLE `Vegas` 
        CHANGE `VeArrowstring` `VeArrowstring` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL, 
        CHANGE `VeScore` `VeScore` INT NOT NULL, 
        CHANGE `VeX` `VeX` INT NOT NULL;", false, array(1146, 1060));
    safe_w_sql("ALTER TABLE `Vegas` ADD `VeG` INT NOT NULL AFTER `VeScore`;", false, array(1146, 1060));
    db_save_version('2024-03-15 22:35:00');
}

if($version<'2024-04-13 15:25:00') {
    safe_w_sql("INSERT INTO `Targets` (`TarId`, `TarDescr`, `TarArray`, `TarStars`, `TarOrder`, `TarFullSize`, `A_size`, `A_color`, `B_size`, `B_color`, `C_size`, `C_color`, `D_size`, `D_color`, `E_size`, `E_color`) VALUES 
        (27, 'TrgFrBeursault', 'TrgFrBeursault', 'a-d', 27, 450, 0, 'FFFFFF', 450, 'FFFFFF', 290, 'FFFFFF', 125, '000080', 40, '000000'),
        (28, 'TrgFrBouquet', 'TrgFrBouquet',   'a-c',  28, 450, 0, 'FFFFFF', 450, 'FFFFFF', 125, 'FFFFFF', 56, '000000', 0, '')", false, array(1146, 1060));
    safe_w_sql("insert into TourTypes (TtId, TtType, TtDistance, TtOrderBy) values (50, 'Type_FR_Beursault', 1, 50)", false, array(1146, 1060));
    db_save_version('2024-04-13 15:25:00');
}

if($version<'2024-05-13 15:25:00') {
    safe_w_sql("alter table Finals 
        add FinStatTotal int not null default 0, 
        add FinStatHits int not null default 0, 
        add FinStatSetWon int not null default 0, 
        add FinStatSetLost int not null default 0, 
        add FinStatMatchWon int not null default 0, 
        add FinStatMatchLost int not null default 0,
        add FinHits int not null default 0 after FinScore", false, array(1146, 1060));
    safe_w_sql("alter table TeamFinals 
        add TfStatTotal int not null default 0, 
        add TfStatHits int not null default 0, 
        add TfStatSetWon int not null default 0, 
        add TfStatSetLost int not null default 0, 
        add TfStatMatchWon int not null default 0, 
        add TfStatMatchLost int not null default 0,
        add TfHits int not null default 0 after TfScore", false, array(1146, 1060));
    safe_w_sql("create table TeamFinComponentStats (
        TfcStatCoId int not null default 0,
        TfcStatSubTeam tinyint not null default 0,
        TfcStatTournament int not null default 0,
        TfcStatEvent varchar(10) not null default '',
        TfcStatId int not null default 0,
        TfcStatMatchNo tinyint not null default 0,
        TfcStatTotal int not null default 0,
        TfcStatHits int not null default 0, 
        TfcStatTens int not null default 0, 
        TfcStatXNines int not null default 0, 
        TfcStatSetWon int not null default 0, 
        TfcStatSetLost int not null default 0, 
        TfcStatMatchWon int not null default 0, 
        TfcStatMatchLost int not null default 0,
        primary key (TfcStatCoId, TfcStatSubTeam, TfcStatTournament, TfcStatEvent, TfcStatId, TfcStatMatchNo))", false, array(1146, 1060, 1050));
    safe_w_sql("insert ignore into LookUpPaths set
        LupIocCode='GBR',
        LupOrigin='GBR',
        LupPath='https://records.agbextranet.org.uk/athletes/getathletes.php',
        LupPhotoPath='https://records.agbextranet.org.uk/athletes/getphotos.php',
        LupFlagsPath='https://records.agbextranet.org.uk/logos/getflags.php',
        LupLastUpdate='',
        LupRankingPath='',
        LupClubNamesPath='https://records.agbextranet.org.uk/clubs/getnames.php',
        LupRecordsPath=''", false, array(1146, 1060));
    safe_w_sql("delete from LookUpPaths where LupIocCode='SUI'");
    safe_w_sql("update LookUpPaths set
        LupPath=replace(LupPath,'http:','https:'),
        LupPhotoPath=replace(LupPhotoPath,'http:','https:'),
        LupFlagsPath=replace(LupFlagsPath,'http:','https:')
       where LupIocCode like 'ITA%'");
    db_save_version('2024-05-13 15:25:00');
}

/*

// TEMPLATE
IMPORTANT: InfoSystem related things MUST be changed in the lib.php file!!!
REMEMBER TO CHANGE ALSO Common/Lib/UpdateTournament.inc.php!!!

if($version<'2024-04-13 15:25:00') {
    safe_w_sql("alter table Parameters change ParId ParId varchar(32) not null");
	db_save_version('2024-04-13 15:25:00');
}

*/

db_save_version($newversion);

function db_save_version($newversion) {
	global $CFG;
	//Aggiorno alla versione attuale SOLO le gare che erano alla versione immediatamente precedente
	$oldDbVersion = GetParameter('DBUpdate');
	safe_w_sql("UPDATE Tournament SET ToDbVersion='{$newversion}' WHERE ToDbVersion='{$oldDbVersion}'");

	SetParameter('DBUpdate', $newversion);
	SetParameter('SwUpdate', ProgramVersion);

	foreach(glob($CFG->DOCUMENT_PATH.'TV/Photos/*.ser') as $file) {
		@unlink($file);
		@unlink(substr($file, 0, -3).'check');
	}
}
