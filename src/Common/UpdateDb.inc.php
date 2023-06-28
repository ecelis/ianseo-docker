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


/*

// TEMPLATE
IMPORTANT: InfoSystem related things MUST be changed in the lib.php file!!!
REMEMBER TO CHANGE ALSO Common/Lib/UpdateTournament.inc.php!!!

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
