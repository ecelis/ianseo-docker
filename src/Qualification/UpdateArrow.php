<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

if (!CheckTourSession() || !isset($_REQUEST['Id']) || !is_numeric($_REQUEST['Id']) || !isset($_REQUEST['Index']) || !is_numeric($_REQUEST['Index']) || !isset($_REQUEST['Dist']) || !is_numeric($_REQUEST['Dist']) || !isset($_REQUEST['Point'])) {
    print get_text('CrackError');
    exit;
}
checkACL(AclQualification, AclReadWrite, false);

$QuId=intval($_REQUEST['Id']);
$Dist=intval($_REQUEST['Dist']);

$Errore=0;
$MakeTeams=1;

$MaxArrows=0;	// num massimo di frecce
$ArrowString = '';	// arrowstring da scrivere
$G = '';			// stringa che rappresenta i Gold
$X = '';			// stringa che rappresenta le X

$OldValue = 0;	// Vecchio valore $Cur...

$CurScore=0;
$CurGold=0;
$CurXNine=0;
$Score=0;
$Gold=0;
$Xnine=0;
$CurSumScores=array();
$CurEndScores=array();
$Value2Write='';

$Updated=0;

// Vars per rank e teams
$Evento = '';
$Category='';
$Societa='';
$Div="";
$Cl="";

if(empty($PageOutput)) $PageOutput='JSON';

if (!IsBlocked(BIT_BLOCK_QUAL))	{
    $Select	= "SELECT ToType, ToGoldsChars,ToXNineChars,(ToMaxDistScore/ToGolds) AS MaxArrows, ToElabTeam!=127 as MakeTeams FROM Tournament WHERE ToId=" . intval($_SESSION['TourId']);
    $Rs=safe_r_sql($Select);

    if (safe_num_rows($Rs)!=1) {
        $Errore = 1;
    } else {
        $MyRow=safe_fetch($Rs);
        $MakeTeams=$MyRow->MakeTeams;
        $MaxArrows=$MyRow->MaxArrows;
        $G=$MyRow->ToGoldsChars;
        $X=$MyRow->ToXNineChars;
    // Estraggo l'arrowstring
        $Select = "SELECT QuD{$Dist}ArrowString AS ArrowString, DiEnds*DiArrows as MaxArrows, DiEnds, DiArrows, TfT{$Dist} as TargetId, TfGoldsChars, TfXNineChars, TfGoldsChars{$Dist}, TfXNineChars{$Dist}
            FROM Qualifications
            INNER JOIN Entries ON QuId=EnId 
            LEFT JOIN DistanceInformation on DiTournament=EnTournament and QuSession=DiSession and DiDistance={$Dist} and DiType='Q'
            LEFT JOIN TargetFaces ON TfTournament=EnTournament and EnTargetFace=TfId
            WHERE EnId={$QuId} AND EnTournament={$_SESSION['TourId']}";
        $Rs=safe_r_sql($Select);

        if (safe_num_rows($Rs)!=1) {
            $Errore = 1;
        } else {
            $MyRow=safe_fetch($Rs);
            if($MyRow->MaxArrows) {
                $MaxArrows=$MyRow->MaxArrows;
            }
            if(!empty($MyRow->{"TfGoldsChars{$Dist}"})) {
                $G=$MyRow->{"TfGoldsChars{$Dist}"};
            } else if(!empty($MyRow->TfGoldsChars)) {
                $G=$MyRow->TfGoldsChars;
            }
            if(!empty($MyRow->{"TfXNineChars".$Dist})) {
                $X=$MyRow->{"TfXNineChars".$Dist};
            } else if(!empty($MyRow->TfXNineChars)) {
                $X=$MyRow->TfXNineChars;
            }
            $ArrowString=str_pad($MyRow->ArrowString,$MaxArrows,' ',STR_PAD_RIGHT);
            $xx=GetLetterFromPrint($_REQUEST['Point'],'T', $MyRow->TargetId);

            $Value2Write = ($_REQUEST['Point']!='' ? ($xx!=' ' ? $xx : '') : ' ');

            if ($Value2Write=='') {
                $Errore = 1;
            } else {
                $MustUpdateZeroValue=(($Value2Write=='A' OR $Value2Write==' ') and $ArrowString[$_REQUEST['Index']]!=$Value2Write);
                $forceUpdate = ($MyRow->TargetId==18 and !($Value2Write=='A' OR $Value2Write==' '));
                $ArrowString[$_REQUEST['Index']]=$Value2Write;

            // Ricalcolo i totali della distanza usando $ArrowString
                list($CurScore,$CurGold,$CurXNine) = ValutaArrowStringGX($ArrowString,$G,$X);

                for($scoreEnd=0; $scoreEnd<$MyRow->DiEnds;$scoreEnd++) {
                    $CurEndScores[] = ValutaArrowString(substr($ArrowString, $scoreEnd*$MyRow->DiArrows, $MyRow->DiArrows));
                }
                if($MyRow->DiArrows>3 and !($MyRow->DiArrows%3)) {
                    for($scoreEnd=0; $scoreEnd<$MyRow->DiEnds*($MyRow->DiArrows/3);$scoreEnd++) {
                        $CurSumScores[] = ValutaArrowString(substr($ArrowString, $scoreEnd*3, 3));
                    }
                } else {
                    $CurSumScores = $CurEndScores;
                }

            // Estraggo il vecchio valore
                $Select
                    = "SELECT QuD{$Dist}Score AS OldScore, QuD{$Dist}Gold AS OldGold, QuD{$Dist}Xnine AS OldXnine, QuD{$Dist}Hits AS OldHits  "
                    . "FROM Qualifications "
                    . "WHERE QuId={$QuId}";
                $Rs=safe_r_sql($Select);

                if (safe_num_rows($Rs)!=1) {
                    $Errore = 1;
                } else {
                    $MyRow=safe_fetch($Rs);
                    $OldValue=$MyRow->OldScore;
                    $OldGold=$MyRow->OldGold;
                    $OldXNine=$MyRow->OldXnine;
                    $OldHits=$MyRow->OldHits;

                    if($forceUpdate or $MustUpdateZeroValue or $OldValue != $CurScore OR $OldGold != $CurGold OR $OldXNine != $CurXNine OR $OldHits != strlen(str_replace(' ', '', $ArrowString))) {
                // Aggiorno i totali della distanza
                        $Update = "UPDATE Qualifications SET
                            QuD{$Dist}ArrowString=" . StrSafe_DB($ArrowString) . ",
                            QuD{$Dist}Score=" . StrSafe_DB($CurScore) . ",
                            QuD{$Dist}Gold=" . StrSafe_DB($CurGold) . ",
                            QuD{$Dist}Xnine=" . StrSafe_DB($CurXNine) . ",
                            QuD{$Dist}Hits=" . StrSafe_DB(strlen(str_replace(' ','', $ArrowString))) . ",
                            QuConfirm = QuConfirm & (255-".pow(2, $Dist) ."),
                            QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,
                            QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,
                            QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine,
                            QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits,
                            QuTimestamp=QuTimestamp,
                            QuArrow=length(replace(concat(trim(QuD1ArrowString),trim(QuD2ArrowString),trim(QuD3ArrowString),trim(QuD4ArrowString),trim(QuD5ArrowString),trim(QuD6ArrowString),trim(QuD7ArrowString),trim(QuD8ArrowString)), 'A', ''))
                            WHERE QuId={$QuId}";
                        $RsUp=safe_w_sql($Update);
                        if($Updated=safe_w_affected_rows()) {
                            safe_w_SQL("UPDATE Qualifications SET QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where QuId={$QuId}");

                            runJack("QualArrUpdate", $_SESSION['TourId'], array("Dist"=>$Dist ,"Index"=>$_REQUEST['Index'] ,"Id"=>$QuId ,"Point"=>$_REQUEST['Point'] ,"TourId"=>$_SESSION['TourId']));

                            if ($Updated AND $OldValue != $CurScore) {
                                // calculate weight
                                if($_SESSION['TourLocRule']=='LANC') {
                                    CalculateDropWeight($QuId, $G);
                                }

                                $q = "SELECT DISTINCT EvCode,EvTeamEvent
                                    FROM Events
                                    INNER JOIN EventClass ON EvCode=EcCode AND if(EvTeamEvent=0, EcTeamEvent=0, EcTeamEvent>0) AND EcTournament={$_SESSION['TourId']}
                                    INNER JOIN Entries ON EcDivision=EnDivision AND EcClass=EnClass and if(EcSubClass='', true, EcSubClass=EnSubClass) AND EnId={$QuId}
                                    WHERE (EvTeamEvent='0' AND EnIndFEvent='1') OR (EvTeamEvent='1' AND EnTeamFEvent+EnTeamMixEvent>0) AND EvTournament={$_SESSION['TourId']}
                                ";
                                //print $q;exit;
                                $Rs = safe_r_sql($q);
                                while ($row = safe_fetch($Rs)) {
                                    ResetShootoff($row->EvCode, $row->EvTeamEvent, 0);
                                }
                            }

                            if (empty($_REQUEST["NoRecalc"])) {
                                // Calcolo la rank della distanza per l'evento
                                $Evento = '*#*#';

                                $Select
                                    = "SELECT CONCAT(EnDivision,EnClass) AS MyEvent, EnCountry as MyTeam,EnDivision,EnClass "
                                    . "FROM Entries "
                                    . "WHERE EnId={$QuId} AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
                                $Rs = safe_r_sql($Select);

                                if (safe_num_rows($Rs) == 1) {
                                    $rr = safe_fetch($Rs);
                                    $Evento = $rr->MyEvent;
                                    $Category = $rr->MyEvent;
                                    $Societa = $rr->MyTeam;
                                    $Div = $rr->EnDivision;
                                    $Cl = $rr->EnClass;

                                    if (CalcQualRank($Dist, $Evento))
                                        $Errore = 1;
                                } else {
                                    $Errore = 1;
                                }

                                if ($Errore == 0) {
                                    // se non ho errori calcolo la rank globale per l'evento
                                    if (CalcQualRank(0, $Evento))
                                        $Errore = 1;
                                }

                                // eventi di cui calcolare le rank assolute
                                $events4abs = array();
                                $q = "SELECT distinct IndEvent FROM Individuals WHERE IndTournament={$_SESSION['TourId']} AND IndId={$QuId}";
                                $r = safe_r_sql($q);

                                if ($r) {
                                    while ($tmp = safe_fetch($r)) {
                                        $events4abs[] = $tmp->IndEvent;
                                    }
                                } else {
                                    $Errore = 1;
                                }

                                // rank abs di distanza
                                if ($Errore == 0 and $events4abs) {
                                    if (!Obj_RankFactory::create('Abs', array('events' => $events4abs, 'dist' => $Dist))->calculate()) {
                                        $Errore = 1;
                                    }
                                }

                                // rank abs totale
                                if ($Errore == 0 and $events4abs) {
                                    if (!Obj_RankFactory::create('Abs', array('events' => $events4abs, 'dist' => 0))->calculate()) {
                                        $Errore = 1;
                                    } else {
                                        foreach ($events4abs as $eventAbs) {
                                            runJack("QRRankUpdate", $_SESSION['TourId'], array("Event" => $eventAbs, "Team" => 0, "TourId" => $_SESSION['TourId']));
                                        }
                                    }
                                }

                                if($MakeTeams) {
                                    if ($Errore == 0) {
                                        // se non ho errori calcolo le squadre
                                        if (MakeTeams($Societa, $Category))
                                            $Errore = 1;
                                    }

                                    if ($Errore == 0) {
                                        // se non ho errori calcolo le squadre assolute
                                        if (MakeTeamsAbs($Societa, $Div, $Cl))
                                            $Errore = 1;
                                    }
                                }

                            }
                        }
                    }
                    // tiro fuori lo score totale
                    $Rs = safe_r_sql("SELECT QuScore, QuGold, QuXnine FROM Qualifications WHERE QuId={$QuId}");

                    if (safe_num_rows($Rs) == 1) {
                        $MyRow = safe_fetch($Rs);
                        $Score = $MyRow->QuScore;
                        $Gold = $MyRow->QuGold;
                        $Xnine = $MyRow->QuXnine;
                    }
                }
            }
        }
    }
} else {
    $Errore = 1;
}

$JsonResult=array();
$JsonResult['error']      = $Errore;
$JsonResult['id']         = $QuId;
$JsonResult['dist']       = $Dist;
$JsonResult['index']      = $_REQUEST['Index'] ;
$JsonResult['arrowsymbol']= $Value2Write ? strtoupper($_REQUEST['Point']) : '';
$JsonResult['curscore']   = $CurScore ;
$JsonResult['curgold']    = $CurGold ;
$JsonResult['curxnine']   = $CurXNine;
$JsonResult['score']      = $Score ;
$JsonResult['gold']       = $Gold ;
$JsonResult['xnine']      = $Xnine;
$JsonResult['sumscore']   = $CurSumScores ;
$JsonResult['endscore']   = $CurEndScores;
$JsonResult['runscore']   = array_map(function ($entry) use (&$actual_sum) { return strval($actual_sum += intval($entry)??0); }, $CurEndScores);
$JsonResult['updated']    = $Updated;

JsonOut($JsonResult);

