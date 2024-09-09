<?php

require_once('./config.php');
require_once(__DIR__.'/Lib.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$JSON=array('error'=>true, 'tempTable'=>array("M"=>0, "Q"=>0));

if(!CheckTourSession() or checkACL(AclISKServer, AclReadWrite, false)!=AclReadWrite) {
	JsonOut($JSON);
}



$JSON['error']=false;

if(!empty($_REQUEST['act'])) {
    switch ($_REQUEST['act']) {
        case 'CalcClDivInd':
        case 'CalcClDivTeam':
        case 'CalcFinInd':
        case 'CalcFinTeam':
            if (isset($_REQUEST['val'])) {
                setModuleParameter('ISK-NG', $_REQUEST['act'], intval($_REQUEST['val']));
            } else {
                $JSON['msg'] = get_text('MissingParameter', 'Api');
                $JSON['error'] = true;
            }
            break;
        case 'ImportQualNow':
            if (IsBlocked(BIT_BLOCK_QUAL)) {
                $JSON['msg'] = get_text('BlockedPhase', 'Tournament');
                $JSON['error'] = true;
            } else {
                $Options=array(
                    'TourId' => $_SESSION['TourId'],
                    'end' => 0,
                    'ses' => 0,
                    'dist' => 0,
                    'type' => 'Q',
                    'ClDivIndCalc' => getModuleParameter('ISK-NG','CalcClDivInd',0, $_SESSION['TourId']),
                    'ClDivTeamCalc' => getModuleParameter('ISK-NG','CalcClDivInd',0, $_SESSION['TourId']),
                    'FinIndCalc' => getModuleParameter('ISK-NG','CalcClDivInd',0, $_SESSION['TourId']),
                    'FinTeamCalc' => getModuleParameter('ISK-NG','CalcClDivInd',0, $_SESSION['TourId']),
                );
                $q = safe_r_SQL("SELECT DISTINCT LEFT(IskDtTargetNo,1) as dtSes, IskDtDistance as dtDist FROM IskData WHERE IskDtTournament={$_SESSION['TourId']} AND IskDtType='Q'");
                while($r=safe_fetch($q)) {
                    $Options['ses'] = $r->dtSes;
                    $Options['dist'] = $r->dtDist;
                    DoImportData($Options);
                }
            }
            break;
        case 'ImportMatchNow':
            if (IsBlocked(BIT_BLOCK_IND) or IsBlocked(BIT_BLOCK_TEAM)) {
                $JSON['msg'] = get_text('BlockedPhase', 'Tournament');
                $JSON['error'] = true;
            } else {
                $Options=array(
                    'TourId' => $_SESSION['TourId'],
                    'end' => 0,
                    'dist' => 1,
                    'team' => 0,
                    'allSessions' => 1,
                    'type' => 'M',
                );
                $q = safe_r_SQL("SELECT DISTINCT IskDtTeamInd as dtIndTeam, IskDtEndNo as dtEnd FROM IskData WHERE IskDtTournament={$_SESSION['TourId']} AND IskDtType='M'");
                while($r=safe_fetch($q)) {
                    $Options['team'] = $r->dtIndTeam;
                    $Options['end'] = $r->dtEnd;
                    DoImportData($Options);
                }
            }
            break;
        case 'doCalcClDivInd':
            if (!IsBlocked(BIT_BLOCK_QUAL)) {
                $qDist = safe_r_SQL("SELECT ToNumDist from Tournament WHERE ToId={$_SESSION['TourId']}");
                if ($rDist = safe_fetch($qDist)) {
                    for ($i = 0; $i <= $rDist->ToNumDist; $i++) {
                        Obj_RankFactory::create('DivClass', array('tournament' => $_SESSION['TourId'], 'dist' => $i))->calculate();
                    }
                }
                Obj_RankFactory::create('DivClass', array('tournament' => $_SESSION['TourId'], 'dist' => 0))->calculate();
                $JSON['msg'] = get_text('CalculateNowDone', 'ISK');
            } else {
                $JSON['msg'] = get_text('BlockedPhase', 'Tournament');
            }
            break;
        case 'doCalcClDivTeam':
            if (!IsBlocked(BIT_BLOCK_QUAL)) {
                $q=safe_r_sql("select ToElabTeam!=127 as MakeTeams from Tournament where ToId={$_SESSION['TourId']}");
                if($r=safe_fetch($q) and $r->MakeTeams) {
                    $JSON['error'] = (intval(MakeTeams(NULL, NULL))!=0);
                    if ($JSON['error']) {
                        $JSON['msg'] = get_text('MakeTeamsError', 'Tournament');
                    } else {
                        $JSON['msg'] = get_text('CalculateNowDone', 'ISK');
                    }
                } else {
                    $JSON['error']=0;
                }
            } else {
                $JSON['msg'] = get_text('BlockedPhase', 'Tournament');
            }
            break;
        case 'doCalcFinInd':
            if (!IsBlocked(BIT_BLOCK_QUAL)) {
                $qDist = safe_r_SQL("SELECT ToNumDist from Tournament WHERE ToId={$_SESSION['TourId']}");
                if ($rDist = safe_fetch($qDist)) {
                    for ($i = 0; $i <= $rDist->ToNumDist; $i++) {
                        Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'dist' => $i))->calculate();
                    }
                }
                Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'dist' => 0))->calculate();
                $JSON['msg'] = get_text('CalculateNowDone', 'ISK');
            } else {
                $JSON['msg'] = get_text('BlockedPhase', 'Tournament');
            }
            break;
            break;
        case 'doCalcFinTeam':
            if (!IsBlocked(BIT_BLOCK_QUAL)) {
                $q=safe_r_sql("select ToElabTeam!=127 as MakeTeams from Tournament where ToId={$_SESSION['TourId']}");
                if($r=safe_fetch($q) and $r->MakeTeams) {
                    $JSON['error'] = (intval(MakeTeamsAbs(NULL, null, null))!=0);
                    if ($JSON['error']) {
                        $JSON['msg'] = get_text('MakeTeamsError', 'Tournament');
                    } else {
                        $JSON['msg'] = get_text('CalculateNowDone', 'ISK');
                    }
                } else {
                    $JSON['error']=0;
                }
            } else {
                $JSON['msg'] = get_text('BlockedPhase', 'Tournament');
            }
            break;
        case 'DeleteDataQual':
            // removes all data from the temp table related to this competition
            safe_w_sql("delete from IskData where iskDtTournament={$_SESSION['TourId']} AND IskDtType='Q'");
            break;
        case 'DeleteDataMatch':
            // removes all data from the temp table related to this competition
            safe_w_sql("delete from IskData where iskDtTournament={$_SESSION['TourId']} AND IskDtType='M'");
            break;
    }
}
$SQL = "SELECT DISTINCT IskDtType, SUM(LENGTH(TRIM(IskDtArrowstring))) as ArrNo FROM IskData WHERE iskDtTournament={$_SESSION['TourId']}  GROUP BY IskDtType";
$q= safe_r_SQL($SQL);
while($r = safe_fetch($q)) {
    $JSON['tempTable'][$r->IskDtType] = intval($r->ArrNo);
}

JsonOut($JSON);
