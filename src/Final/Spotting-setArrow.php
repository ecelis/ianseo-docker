<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Fun_MatchTotal.inc.php');
//require_once('Common/Lib/Fun_Final.local.inc.php');
//require_once('Common/Lib/Fun_Modules.php');

$Event = isset($_REQUEST['Event']) ? $_REQUEST['Event'] : null;
$TeamEvent = isset($_REQUEST['Team']) ? $_REQUEST['Team'] : null;
$Target = isset($_REQUEST['ArrowPosition']);
$Arrow = isset($_REQUEST['Arrow']) ? $_REQUEST['Arrow'] : null;

$JSON=array('error'=>1, 'changed' => 0, 'confirm' => '', 'winner' => 0, 'newSOPossible'=>false);

checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite, false);
$isBlocked=($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM));

if(is_null($Event) or is_null($TeamEvent) or is_null($Arrow) or $isBlocked) {
	JsonOut($JSON);
}

foreach($Arrow as $MatchId => $SOs) {
	$MainMatch=$MatchId%2 ? $MatchId-1 : $MatchId;
	$OppMatch=$MainMatch+1;
	$JSON['confirm']='confirm['.$MatchId.']';
	$JSON['DontMove']=false;
	foreach($SOs as $isSO=>$Ends) {
		foreach($Ends as $End=>$Arrows) {
			foreach($Arrows as $ArrowIndex => $ArrowValue) {
				$validData=GetMaxScores($Event, $MatchId, $TeamEvent);
				// if spotter sends a "0" it is changed into an "M"
				if($ArrowValue==="0") {
					$ArrowValue="M";
				}
				// Check the arrow value is OK
                $ArrowLetter = GetLetterFromPrint($ArrowValue, $validData["Arrows"]);
                if($ArrowLetter == ' ') {
					if(strlen($ArrowValue)) {
						$JSON['DontMove']=true;
					}
				}
				$ArrowValue=trim($ArrowValue) ? DecodeFromLetter($ArrowLetter) : '';

				// index of arrow
				$obj=getEventArrowsParams($Event, getPhase($MatchId), $TeamEvent);
				if($isSO) {
					$Index = $obj->arrows*$obj->ends + ($obj->so * $End) + $ArrowIndex + 1;
				} else {
					$Index = ($obj->arrows * $End) + $ArrowIndex + 1;
				}

				$Position=null;
				$Wind=null;
				$Time=null;
				if(isset($_REQUEST['x'])) {
					// received also arrow position
					$R=3;
					$X=$_REQUEST['x'];
					$Y=$_REQUEST['y'];
					$D=round(sqrt($X*$X + $Y*$Y)-$R,1);
					$Position=[
						'X' => $X,
						'Y' => $Y,
						'R' => $R,
						'D' => $D,
						];

					if(empty($_REQUEST['noValue'])) {
						$Values=$validData['Arrows'];
						unset($Values['A']);
						uasort($Values, function($a, $b) {
							if($a['size']<$b['size']) return -1;
							if($a['size']>$b['size']) return 1;
							return 0;
						});

						// we need to set the "M" letter here
						$tmp='A';
						foreach($Values as $Letter => $data) {
							//$dist=$data['size']*$validData['TargetSize']*5/($validData['FullSize']);
							if($D<=$data['radius']) {
								$tmp=$Letter;
								break;
							}
						}
						if($tmp!=$ArrowLetter) {
							$JSON['changed']=1;
						}
						$ArrowLetter=$tmp;
						$ArrowValue=DecodeFromLetter($tmp);
					}
				}

				if(isset($_REQUEST['Ws']) and isset($_REQUEST['Wd']) and is_numeric($_REQUEST['Ws']) and is_numeric($_REQUEST['Wd'])) {
					$Wind=[
						'Ws'=>round(floatval($_REQUEST["Ws"]), 1),
						'Wd'=>intval($_REQUEST["Wd"]),
					];
				}

				if(isset($_REQUEST['T']) and is_numeric($_REQUEST['T'])) {
					$Time=intval($_REQUEST['T']);
				}

				if($Position or $Wind or $Time) {
					UpdateArrowPosition($MatchId, $Event, $TeamEvent, $Index, $Position, $Wind, $Time);
				}

				$JSON['p']=array();
				$JSON['t']=array();
				$JSON['resetFirst']=false;
                $Table='Finals';
                $TablePrefix='Fin';
                if($TeamEvent) {
                    $Table='TeamFinals';
                    $TablePrefix='Tf';
                }
                $Closest=(isset($_REQUEST['Closest']) and $_REQUEST['Closest']==$MatchId);

				if(!trim($ArrowValue)) {
					// means the arrow has been deleted...
					// needs to delete from positions as well
                    // and reset the winner flag as well
					DeleteArrowPosition($MatchId, $Event, $TeamEvent, $Index);
					$JSON['p'] = array(
						'id' => 'SvgArrow[' . $MatchId . '][' . $isSO . '][' . $End . '][' . $ArrowIndex . ']',
						'data' => array('X' => -2000, 'Y' => -2000, 'D' => 0, 'R' => 3),
					);

					// define the shooting first maximum value, that is 2^end - 1
					$ShootingFirst=pow(2, ($isSO ? $obj->ends-1 : 0) + $End+1)-1;
					safe_w_sql("update {$Table} 
						set {$TablePrefix}ShootFirst={$TablePrefix}ShootFirst&$ShootingFirst" . (($isSO and !empty($_REQUEST['Changed']))  ? ", {$TablePrefix}TbClosest=0, {$TablePrefix}WinLose=0 " : " ").
						"where {$TablePrefix}Tournament={$_SESSION['TourId']} and {$TablePrefix}Event='$Event' and {$TablePrefix}Matchno in ($MainMatch, $OppMatch)");
                    $Closest=false;
				}

				if(!empty($_REQUEST['Changed'])) {
					$IsFinished=UpdateArrowString($MatchId, $Event, $TeamEvent, $ArrowLetter, $Index, $Index, 0, $Closest, $CHANGES);
					$JSON['hasChanges']=$CHANGES;
				}

				// we need to send back the arrow value, the set total, the winner, etc
				$options=array();
				$options['tournament']=$_SESSION['TourId'];
				$options['events']=$Event;
				$options['matchno']=($MatchId%2 ? $MatchId-1 : $MatchId);
				$options['extended']=isset($_REQUEST['x']);

				if($TeamEvent) {
					$rank=Obj_RankFactory::create('GridTeam',$options);
				} else {
					$rank=Obj_RankFactory::create('GridInd',$options);
				}
				$rank->read();
				$Data=$rank->getData();

				if(empty($Data['sections'])) {
					JsonOut($JSON);
				}
				$Section=end($Data['sections']);

				if(empty($Section['phases'])) {
					JsonOut($JSON);
				}
				$Phase=end($Section['phases']);

				if(empty($Phase['items'])) {
					JsonOut($JSON);
				}
				$Match=end($Phase['items']);

				$JSON['ShootOff']=strlen(trim($Match['tiebreak'].$Match['oppTiebreak']))>0;

				$JSON['arrowID']='Arrow['.$MatchId.']['.intval($isSO).']['.$End.']['.$ArrowIndex.']';
				$JSON['arrowValue']=$ArrowValue;
				$JSON['winner']=$Match['winner'] ? 'L' : ($Match['oppWinner'] ? 'R' : '');
                $JSON['newSOPossible'] = (
                    !($Match['winner'] OR $Match['oppWinner']) AND
                    (strlen(trim($Match['tiebreak'])) > 0 AND (strlen(trim($Match['tiebreak']))%$obj->so == 0)) AND
                    (strlen(trim($Match['oppTiebreak'])) > 0 AND (strlen(trim($Match['oppTiebreak']))%$obj->so == 0)) AND
                    (strlen(trim($Match['tiebreak'])) ==strlen(trim($Match['oppTiebreak'])))
                );
				$JSON['showClosest']=(($JSON['ShootOff'] and $Match['tiebreakDecoded']==$Match['oppTiebreakDecoded'] and !$JSON['winner']) or $Match['closest'] or $Match['oppClosest']);
                // Left Side
                $Match['setPoints']=array_pad(explode('|', $Match['setPoints']), $obj->ends,'');
                $Match['setPointsByEnd']=array_pad(explode('|', $Match['setPointsByEnd']), $obj->ends,'');
                $Match['tiebreakDecoded']=array_pad(explode(',', $Match['tiebreakDecoded']), 3,'');
                // Right side
                $Match['oppSetPoints']=array_pad(explode('|', $Match['oppSetPoints']), $obj->ends,'');
                $Match['oppSetPointsByEnd']=array_pad(explode('|', $Match['oppSetPointsByEnd']), $obj->ends,'');
                $Match['oppTiebreakDecoded']=array_pad(explode(',', $Match['oppTiebreakDecoded']), 3,'');

                $soEnds=ceil(max(strlen(trim($Match['tiebreak'])), strlen(trim($Match['oppTiebreak'])))/$obj->so);
                $TotL=0;
                $TotR=0;
				if($MatchId%2) {
    				if($isSO) {
					    $JSON['t'][]=array(
						    'id' => 'EndTotalR_SO_0',
						    'val' => $Match['oppTiebreakDecoded'][0]??'',
					    );
						for($i=1;$i<$soEnds;$i++) {
							$JSON['t'][]=array(
								'id' => 'EndTotalR_SO_'.$i,
								'val' => $Match['oppTiebreakDecoded'][$i],
							);
						}
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][1]['.$End.']['.$ArrowIndex.']',
                                'data'=> (array_key_exists(($Index - $obj->arrows*$obj->ends -1),$Match['oppTiePosition']) ? $Match['oppTiePosition'][$Index - $obj->arrows*$obj->ends -1] : array())
							);
						}
					} else {
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][0]['.$End.']['.$ArrowIndex.']',
								'data'=> (array_key_exists(($Index - 1),$Match['oppArrowPosition']) ? $Match['oppArrowPosition'][$Index - 1] : array())
							);
						}
					}
				} else {

					$JSON['t'][]=array(
						'id' => 'EndTotalL_SO_0',
						'val' => $Match['tiebreakDecoded'][0]??'',
					);
					if($isSO) {
						for($i=1;$i<$soEnds;$i++) {
							$JSON['t'][]=array(
								'id' => 'EndTotalL_SO_'.$i,
								'val' => $Match['tiebreakDecoded'][$i],
							);
						}
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][1]['.$End.']['.$ArrowIndex.']',
                                'data'=> (array_key_exists(($Index - $obj->arrows*$obj->ends -1),$Match['tiePosition']) ? $Match['tiePosition'][$Index - $obj->arrows*$obj->ends -1] : array())
								);
						}
					} else {
						if(isset($_REQUEST['x'])) {
							$JSON['p']=array(
								'id'=>'SvgArrow['.$MatchId.'][0]['.$End.']['.$ArrowIndex.']',
                                'data'=> (array_key_exists(($Index - 1),$Match['arrowPosition']) ? $Match['arrowPosition'][$Index - 1] : array())
								);
						}
					}
				}

                $q=safe_r_sql("select f1.{$TablePrefix}ShootFirst as ShootFirst,f2.{$TablePrefix}ShootFirst as OppShootFirst
					    from {$Table} f1
						inner join {$Table} f2 on f2.{$TablePrefix}Tournament=f1.{$TablePrefix}Tournament and f2.{$TablePrefix}Event=f1.{$TablePrefix}Event and f2.{$TablePrefix}Matchno=f1.{$TablePrefix}Matchno+1 
						where f1.{$TablePrefix}Tournament={$_SESSION['TourId']} and f1.{$TablePrefix}Event='$Event' and f1.{$TablePrefix}Matchno = $MainMatch and f1.{$TablePrefix}ShootFirst+f2.{$TablePrefix}ShootFirst > 0");
                if($r=safe_fetch($q)) {
                    $JSON['resetFirst']=true;
                    $JSON['starters']=[];
                    $Initial="first[$TeamEvent][$Event]";
                    for($i=0;$i<=$obj->ends;$i++) {
                        if($r->ShootFirst & pow(2,$i)) {
                            $JSON['starters'][]=$Initial."[{$MainMatch}][{$i}]";
                        }
                        if($r->OppShootFirst & pow(2,$i)) {
                            $JSON['starters'][]=$Initial."[{$OppMatch}][{$i}]";
                        }
                    }
                }

                $JSON['t'][]=array(
                    'id' => 'EndSetR_SO',
                    'val' => $Match['oppSetScore'],
                );
                $JSON['t'][]=array(
                    'id' => 'EndSetL_SO',
                    'val' => $Match['setScore'],
                );
                for($i=0;$i<$obj->ends;$i++) {
                    if($Section['meta']['matchMode']) {
                        $TotL+=($Match['setPointsByEnd'][$i] ? $Match['setPointsByEnd'][$i] : 0);
                        $TotR+=($Match['oppSetPointsByEnd'][$i] ? $Match['oppSetPointsByEnd'][$i] : 0);
                    } else {
                        $TotL+=($Match['setPoints'][$i] ? $Match['setPoints'][$i] : 0);
                        $TotR+=($Match['oppSetPoints'][$i] ? $Match['oppSetPoints'][$i] : 0);
                    }
                    $JSON['t'][]=array(
                        'id' => 'EndTotalR_'.$i,
                        'val' => $Match['oppSetPoints'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'EndTotalL_'.$i,
                        'val' => $Match['setPoints'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'EndSetL_'.$i,
                        'val' => $Match['setPointsByEnd'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'EndSetR_'.$i,
                        'val' => $Match['oppSetPointsByEnd'][$i],
                    );
                    $JSON['t'][]=array(
                        'id' => 'TotalL_'.$i,
                        'val' => $TotL,
                    );
                    $JSON['t'][]=array(
                        'id' => 'TotalR_'.$i,
                        'val' => $TotR,
                    );
                }


                $JSON['error']=0;

                $JSON['ClosestL']=(int) $Match['closest'];
                $JSON['ClosestR']=(int) $Match['oppClosest'];

                // evaluates the last valid arrows to check for stars on both sides!
				$MatchIdR=$MainMatch+1;
				$JSON['stars']=array();
				for($i=0;$i<$obj->arrows;$i++) {
					$JSON['stars']['L'.$i]=array(
						'id'=>'Star-'.$MainMatch.'-'.$i,
						'ref' => '',
						'isStar'=>false,
						'nextValue'=>'');
					$JSON['stars']['R'.$i]=array(
						'id'=>'Star-'.$MatchIdR.'-'.$i,
						'ref' => '',
						'isStar'=>false,
						'nextValue'=>'');
				}
				for($i=0;$i<$obj->so;$i++) {
					$JSON['stars']['LS'.$i]=array(
						'id'=>'StarSO-'.$MainMatch.'-'.$i,
						'ref' => '',
						'isStar'=>false,
						'nextValue'=>'');
					$JSON['stars']['RS'.$i]=array(
						'id'=>'StarSO-'.$MatchIdR.'-'.$i,
						'ref' => '',
						'isStar'=>false,
						'nextValue'=>'');
				}
                // Check if there are stars
				if($Match['arrowstring']!=strtoupper($Match['arrowstring']) or $Match['oppArrowstring']!=strtoupper($Match['oppArrowstring'])) {
					$ArrowstringL = rtrim($Match['arrowstring']);
					$ArrowstringR = rtrim($Match['oppArrowstring']);
					$EndLength = $obj->arrows;
					$j=0;
					while(max(strlen($ArrowstringL), strlen($ArrowstringR))>$EndLength) {
						$ArrowstringL=substr($ArrowstringL, $EndLength);
						$ArrowstringR=substr($ArrowstringR, $EndLength);
						$j++;
					}
	                $JSON['starsL']=false;
					$JSON['starsR']=false;
					for($i=0;$i<max(strlen($ArrowstringL), strlen($ArrowstringR));$i++) {
						$ar=substr($ArrowstringL,$i,1);
						if(strlen($ar)) {
			                $JSON['stars']['L'.$i]=array(
			                    'id'=>'Star-'.$MainMatch.'-'.$i,
				                'ref' => "Arrow[{$MainMatch}][0][{$j}][{$i}]",
				                'isStar'=>strtolower($ar)==$ar,
				                'nextValue'=>GetHigerArrowValue($Event, $TeamEvent, ValutaArrowString($ar)));
			                if(strtolower($ar)==$ar) {
				                $JSON['starsL']=true;
			                }
						}

						$ar=substr($ArrowstringR,$i,1);
						if(strlen($ar)) {
			                $JSON['stars']['R'.$i]=array(
			                    'id'=>'Star-'.$MatchIdR.'-'.$i,
				                'ref' => "Arrow[{$MatchIdR}][0][{$j}][{$i}]",
				                'isStar'=>strtolower($ar)==$ar,
				                'nextValue'=>GetHigerArrowValue($Event, $TeamEvent, ValutaArrowString($ar)));
							if(strtolower($ar)==$ar) {
								$JSON['starsR']=true;
							}
						}
					}
				} elseif ($Match['tiebreak']!=strtoupper($Match['tiebreak']) or $Match['oppTiebreak']!=strtoupper($Match['oppTiebreak'])) {
					$ArrowstringL = rtrim($Match['tiebreak']);
					$ArrowstringR = rtrim($Match['oppTiebreak']);
					$EndLength = $obj->so;
					$j=0;
					while(max(strlen($ArrowstringL), strlen($ArrowstringR))>$EndLength) {
						$ArrowstringL=substr($ArrowstringL, $EndLength);
						$ArrowstringR=substr($ArrowstringR, $EndLength);
						$j++;
					}
	                $JSON['starsL']=false;
					$JSON['starsR']=false;
					for($i=0;$i<max(strlen($ArrowstringL), strlen($ArrowstringR));$i++) {
						$ar=substr($ArrowstringL,$i,1);
						if(strlen($ar)) {
			                $JSON['stars']['LS'.$i]=array(
			                    'id'=>'StarSO-'.$MainMatch.'-'.$i,
				                'ref' => "Arrow[{$MainMatch}][1][{$j}][{$i}]",
				                'isStar'=>strtolower($ar)==$ar,
				                'nextValue'=>GetHigerArrowValue($Event, $TeamEvent, ValutaArrowString($ar)));
			                if(strtolower($ar)==$ar) {
				                $JSON['starsL']=true;
			                }
						}

						$ar=substr($ArrowstringR,$i,1);
						if(strlen($ar)) {
			                $JSON['stars']['RS'.$i]=array(
			                    'id'=>'StarSO-'.$MatchIdR.'-'.$i,
				                'ref' => "Arrow[{$MatchIdR}][1][{$j}][{$i}]",
				                'isStar'=>strtolower($ar)==$ar,
				                'nextValue'=>GetHigerArrowValue($Event, $TeamEvent, ValutaArrowString($ar)));
							if(strtolower($ar)==$ar) {
								$JSON['starsR']=true;
							}
						}
					}
				}
			}
		}
	}
}

JsonOut($JSON);
