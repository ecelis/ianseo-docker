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

if($version<'2024-06-08 15:25:00') {
    safe_w_sql("alter table RoundRobinMatches add index (RrMatchTournament, RrMatchTeam, RrMatchEvent), add index (RrMatchTournament, RrMatchScheduledDate, RrMatchScheduledTime)", false, array(1146, 1060));
    db_save_version('2024-06-08 15:25:00');
}

if($version<'2024-08-26 07:00:02') {
    safe_w_sql("UPDATE `LookUpPaths` SET `LupPath` = 'https://dirigeant.ffta.fr/ianseo/download/parametres_ianseo.ffta' WHERE `LookUpPaths`.`LupIocCode` = 'FRA'");
    safe_w_sql("REPLACE INTO `LookUpPaths` (`LupIocCode`, `LupOrigin`, `LupPath`, `LupPhotoPath`, `LupFlagsPath`, `LupLastUpdate`, `LupRankingPath`, `LupClubNamesPath`, `LupRecordsPath`) VALUES ('SWE', 'SWE', '', 'https://resultat.bagskytte.se/Archer/GetIanseoImage', 'https://resultat.bagskytte.se/Club/GetIanseoImage', '0000-00-00 00:00:00', '', '', '')");
    safe_w_sql("ALTER TABLE `TournamentInvolved` ADD `TiTimeStamp` DATETIME NOT NULL AFTER `TiGender`;", false, array(1146, 1060));
    safe_w_sql("UPDATE  `TournamentInvolved` INNER JOIN `Tournament` ON `TiTournament`=`ToId` SET  `TiTimeStamp`=`ToWhenFrom` WHERE `TiTimeStamp`='0000-00-00'");
    db_save_version('2024-08-26 07:00:02');
}

if($version<'2024-09-20 08:00:07') {
    safe_w_sql("DROP TABLE IF EXISTS `AclFeatures`");
    safe_w_sql("ALTER TABLE `AclDetails` ADD `AclDtSubFeature` TINYINT NOT NULL AFTER `AclDtFeature`", false, array(1146, 1060));
    safe_w_sql("ALTER TABLE `AclDetails` DROP PRIMARY KEY, ADD PRIMARY KEY (`AclDtTournament`, `AclDtIP`, `AclDtFeature`, `AclDtSubFeature`) USING BTREE;", false, array(1146, 1060));
    safe_w_sql("CREATE TABLE `AclTemplates` (
        `AclTeTournament` INT NOT NULL , 
        `AclTePattern` VARCHAR(150) NOT NULL , 
        `AclTeNick` VARCHAR(50) NOT NULL ,
        `AclTeFeatures` TEXT NOT NULL ,  
        `AclTeEnabled` TINYINT NOT NULL , 
        PRIMARY KEY (`AclTeTournament`, `AclTePattern`)) ENGINE = InnoDB",false,array(1146, 1050, 1060));
    safe_w_sql("CREATE TABLE `AclUsers` (
        `AclUsUser` VARCHAR(16) NOT NULL , 
        `AclUsName` VARCHAR(100) NOT NULL , 
        `AclUsPwd` VARCHAR(64) NOT NULL , 
        `AclUsEnabled` TINYINT NOT NULL , 
        `AclUsAuthAdmin` TINYINT NOT NULL,
        PRIMARY KEY (`AclUsUser`)) ENGINE = InnoDB",false,array(1146, 1050, 1060));

    safe_w_sql("update `AclDetails` set `AclDtFeature`=22 where `AclDtFeature`=9");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=23 where `AclDtFeature`=2");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=24 where `AclDtFeature`=7");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=25 where `AclDtFeature`=3");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=26 where `AclDtFeature`=4");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=27 where `AclDtFeature`=16");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=28 where `AclDtFeature`=5");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=29 where `AclDtFeature`=6");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=30 where `AclDtFeature`=13");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=31 where `AclDtFeature`=14");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=32 where `AclDtFeature`=11");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=33 where `AclDtFeature`=12");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=34 where `AclDtFeature`=8");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=35 where `AclDtFeature`=10");
    safe_w_sql("update `AclDetails` set `AclDtFeature`=36 where `AclDtFeature`=15");
    safe_w_sql("UPDATE `AclDetails` set `AclDtFeature`=`AclDtFeature`-20 where `AclDtFeature`>20");

    safe_w_sql("INSERT IGNORE INTO AclTemplates (`AclTeTournament`, `AclTePattern`, `AclTeNick`, `AclTeFeatures`, `AclTeEnabled`)
        SELECT `AclTournament`, `AclIP`, `AclNick`, GROUP_CONCAT(CONCAT_WS('|', AclDtFeature, AclDtSubFeature, AclDtLevel) ORDER BY AclDtFeature, AclDtSubFeature SEPARATOR '#') as `Features`, AclEnabled
        FROM `ACL` 
        INNER JOIN AclDetails on AclTournament=AclDTTournament and AclIP=AclDtIP
        WHERE `AclIP` LIKE '%*%' 
        GROUP BY  `AclTournament`, `AclIP`",false,array(1146, 1060));
    safe_w_sql("DELETE FROM `ACL` WHERE `AclIP` LIKE '%*%'",false,array(1146, 1060));
    safe_w_sql("INSERT IGNORE INTO AclTemplates (`AclTeTournament`, `AclTePattern`, `AclTeNick`, `AclTeFeatures`, `AclTeEnabled`)
        SELECT `AclTournament`, `AclNick`, 'REGEXP', GROUP_CONCAT(CONCAT_WS('|', AclDtFeature, AclDtSubFeature, AclDtLevel) ORDER BY AclDtFeature, AclDtSubFeature SEPARATOR '#') as `Features`, AclEnabled
        FROM `ACL` 
        INNER JOIN AclDetails on AclTournament=AclDTTournament and AclIP=AclDtIP
        WHERE `AclIP` LIKE '0.0.0.%'
        GROUP BY  `AclTournament`, `AclIP`",false,array(1146, 1060));
    safe_w_sql("DELETE FROM `ACL` WHERE `AclIP` LIKE '0.0.0.%'",false,array(1146, 1060));
    db_save_version('2024-09-20 08:00:07');
}

if($version<'2024-09-22 10:00:04') {
    safe_w_sql("DROP TABLE IF EXISTS `AclUserFeatures`",false,array(1051));
    safe_w_sql("CREATE TABLE `AclUserFeatures` (
        `AclUFUser` VARCHAR(16) NOT NULL ,
        `AclUFPattern` VARCHAR(150) NOT NULL , 
        `AclUFFeature` TEXT NOT NULL , 
        PRIMARY KEY (`AclUFUser`, `AclUFPattern`)) ENGINE = InnoDB",false,array(1146, 1050, 1060));
    $userList = getParameter("AuthUsers", false, array(), true);
    foreach ($userList as $user) {
        safe_w_sql("INSERT INTO AclUsers (`AclUsUser`, `AclUsName`, `AclUsPwd`, `AclUsEnabled`, `AclUsAuthAdmin`) 
            VALUES (".StrSafe_DB($user["u"]).", ".StrSafe_DB($user["d"]).", '".hash("sha256",$user["p"])."', ".intval($user["e"]).", ".intval($user["r"]).")
            ON DUPLICATE KEY UPDATE  `AclUsName`=".StrSafe_DB($user["d"]).", `AclUsPwd`='".hash("sha256",$user["p"])."', `AclUsEnabled`=".intval($user["e"]).", `AclUsAuthAdmin`=".intval($user["r"]),false,array(1146, 1050, 1060));
        foreach ($user["c"] as $comp) {
            safe_w_sql("INSERT INTO `AclUserFeatures` (`AclUFUser`, `AclUFPattern`, `AclUFFeature`) VALUES (".StrSafe_DB($user["u"]).", ".StrSafe_DB($comp).", '')
                ON DUPLICATE KEY UPDATE `AclUFPattern`=".StrSafe_DB($comp).", `AclUFFeature`=''",false,array(1146, 1050, 1060));
        }
    }
    safe_w_sql("DELETE FROM Parameters WHERE ParId=" . StrSafe_DB("AuthUsers"),false,array(1146, 1050, 1060));


    db_save_version('2024-09-22 10:00:04');
}

/*

// TEMPLATE
IMPORTANT: InfoSystem related things MUST be changed in the lib.php file!!!
REMEMBER TO CHANGE ALSO Common/Lib/UpdateTournament.inc.php!!!

if($version<'2024-06-08 15:25:00') {
    safe_w_sql("alter table RoundRobinMatches add index (RrMatchTournament, RrMatchTeam, RrMatchEvent)");
	db_save_version('2024-06-08 15:25:00');
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
