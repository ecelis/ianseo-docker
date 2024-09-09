<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once("Common/Obj_Target.php");
require_once('Common/Fun_FormatText.inc.php');
require_once('Fun_MatchTotal.inc.php');
require_once('Fun_Final.local.inc.php');

$JSON=['error'=>1, 'msg'=>get_text('ErrGenericError', 'Errors'),];

if(!CheckTourSession() or BlockExperimental or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'getArrows':
		// if ends or arrows is not > 0 return
		$End=max(0, intval($_REQUEST['end']??0));
		$Arrows=max(0, intval($_REQUEST['arrows']??0));
		$Schedule=($_REQUEST['schedule']??'');
		$Events=($_REQUEST['events']??[]);
		$Phases=($_REQUEST['phases']??[]);

		if(!$End or !$Arrows or (!$Schedule and !$Events)) {
			JsonOut($JSON);
		}

		require_once('Common/Lib/Obj_RankFactory.php');

		$rank=[];

		$options=[
			'tournament'=>$_SESSION['TourId'],
		];
		if($Schedule) {
			$options['schedule']=substr($Schedule, 1);
			if(strlen($options['schedule'])==16) {
				$options['schedule'].=':00';
			}
			if($Events) {
				$options['events']=$Events;
			}
			if($Team=$Schedule[0]) {
				if(!hasACL(AclTeams, AclReadWrite)) {
					JsonOut($JSON);
				}
				$rank[$Team]=Obj_RankFactory::create('GridTeam',$options);
			} else {
				if(!hasACL(AclIndividuals, AclReadWrite)) {
					JsonOut($JSON);
				}
				$rank[$Team]=Obj_RankFactory::create('GridInd',$options);
			}
		} else {
			foreach($Events as $Team => $Event) {
				$options['events']=$Event;
				if($Phases[$Team]??[]) {
					$options['events']=[];
					foreach($Event as $e) {
						foreach($Phases[$Team] as $p) {
							$options['events'][]=$e.'@'.$p;
						}
					}
				}
				if($Team) {
					if(!hasACL(AclTeams, AclReadWrite)) {
						JsonOut($JSON);
					}
					$rank[$Team]=Obj_RankFactory::create('GridTeam',$options);
				} else {
					if(!hasACL(AclIndividuals, AclReadWrite)) {
						JsonOut($JSON);
					}
					$rank[$Team]=Obj_RankFactory::create('GridInd',$options);
				}
			}
			// $SQL="(".implode(") union (", $tmp).")";
		}

		// IRM/Tie options
		$TieSelect ='<option value="0">'.get_text('NoTie', 'Tournament').'</option>';
		$TieSelect.='<option value="1">'.get_text('TieWinner', 'Tournament').'</option>';
		$TieSelect.='<option value="2">'.get_text('Bye').'</option>';
		$q=safe_r_SQL("select * from IrmTypes where IrmId>0 order by IrmId");
		while($irm=safe_fetch($q)) {
			$TieSelect.= '<option value="irm-'.$irm->IrmId . '">' . $irm->IrmType .' - '. get_text('IRM-'.$irm->IrmId, 'Tournament'). '</option>';
		}

		$JSON['html']='';
		$StartIdx=$Arrows*($End-1);
		foreach($rank as $Team => $r) {
			$r->read();
			$Data=$r->getData();

			foreach($Data['sections'] as $EvCode => $Phases) {
				if(empty($Phases['phases'])) {
					continue;
				}
				foreach($Phases['phases'] as $Phase => $Items) {
					$MaxArrows=$Phases['meta'][$Items['meta']['FinElimChooser']?'elimEnds':'finEnds']*$Phases['meta'][$Items['meta']['FinElimChooser']?'elimArrows':'finArrows'];
					$EndIdx=min($StartIdx+$Arrows, $MaxArrows);
					$First=true;
					$Cols=8+$EndIdx+($EndIdx==$MaxArrows ? (3*$Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO']) : 0)-$StartIdx;
					if($StartIdx > $MaxArrows) {
						// too many arrows :)
						continue;
					}
					$JSON['html'].='<tr><th colspan="'.$Cols.'" class="Title">'.$EvCode.' - '.$Phases['meta']['eventName'].' - '.$Items['meta']['phaseName'].'</th></tr>';
					$JSON['html'].='<tr><th>'.get_text('Target').'</th><th>'.get_text('Athlete').'</th><th>'.get_text('Country').'</th>';
					for($n=$StartIdx; $n<$EndIdx; $n++) {
						$JSON['html'].='<th>'.($n+1).'</th>';
					}
					$JSON['html'].='<th>'.get_text('Total').'</th>';
                    if($Phases['meta']['checkGolds']) {
					    $JSON['html'].='<th>'.get_text('TieBreak-1-Short', 'RoundRobin').'</th>';
                    }
                    if($Phases['meta']['checkXnines']) {
					    $JSON['html'].='<th>'.get_text('TieBreak-2-Short', 'RoundRobin').'</th>';
                    }
					$JSON['html'].='<th>'.get_text('SetPoints', 'Tournament').'</th>';
					if($EndIdx==$MaxArrows) {
						for($n=0; $n<3*$Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO']; $n++) {
							$JSON['html'].='<th>SO '.($n+1).'</th>';
						}
						$JSON['html'].='<th>'.get_text('ClosestShort', 'Tournament').'</th>';
					}
					$JSON['html'].='<th colspan="3"></th></tr>';

					$Html=['',''];

					$Offset=1 + count($Items['items'])*2*($Arrows+1+(3*$Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO']));
					$TabIndex=1;

					foreach($Items['items'] as $Match) {
						$Class=(($Match['winner'] or $Match['oppWinner'] or ($Match['irm'] and $Match['oppIrm'])) ? 'disabled'.($Match['winner'] ? ' win' : '') : '');
						$ByeChooser=intval($Match['tie']==2 or $Match['irm'] or $Match['oppTie']==2 or $Match['oppIrm']);
						if($ByeChooser) {
							$Class= 'Bye';
						}
						$Athlete1=($Team ? 'countryName' : 'athlete');
						$Athlete2=($Team ? 'oppCountryName' : 'oppAthlete');
						if($Match[$Athlete1]) {
							$Arrowstring=str_pad($Match['arrowstring'], $MaxArrows, ' ', STR_PAD_RIGHT);

							$id=$Team.'_'.$EvCode.'_'.$Match['matchNo'];

							$Html[$ByeChooser].='<tr class="'.$Class.'" id="row_'.$id.'">
								<td id="tgt_'.$id.'">'.ltrim($Match['target'], '0').'</td>
								<td id="nam_'.$id.'">'.$Match[$Athlete1].'</td>
								<td id="cty_'.$id.'">'.$Match['countryCode'].'</td>';
							for($n=$StartIdx; $n<$EndIdx; $n++) {
								$a='';
								if(strlen(trim($Arrowstring[$n]))) $a=DecodeFromLetter($Arrowstring[$n]);
								$Html[$ByeChooser].='<td><input type="text" size="2" id="s_'.$id.'_'.$n.'" value="'.$a.'" onblur="updateScore(this)" onfocus="this.select()" tabindex="'.($TabIndex++).'"></td>';
							}
							$Html[$ByeChooser].='<td id="tot_'.$id.'" class="Right">'.$Match['score'].'</td>';
                            if($Phases['meta']['checkGolds']) {
                                $Html[$ByeChooser].='<td id="tb1_'.$id.'" class="Right">'.$Match['golds'].'</td>';
                            }
                            if($Phases['meta']['checkXnines']) {
                                $Html[$ByeChooser].='<td id="tb2_'.$id.'" class="Right">'.$Match['xnines'].'</td>';
                            }
							$Html[$ByeChooser].='<td id="set_'.$id.'" class="Center">'.($Phases['meta']['matchMode']?$Match['setScore']:'').'</td>';
							if($EndIdx==$MaxArrows) {
								$TieBreak=str_pad($Match['tiebreak'], $Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO'], ' ', STR_PAD_RIGHT);
								$SoArrs=$Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO'];

								for($pSo=0; $pSo<3; $pSo++ ) {
									for ($n = 0; $n < $SoArrs; $n++) {
										$ArrI = $n+($pSo*$SoArrs);
										$Html[$ByeChooser] .= '<td><input type="text" size="2" id="s_' . $id . '_' . ($MaxArrows+$ArrI) . '" value="' . (!empty($TieBreak[$ArrI]) ? DecodeFromLetter($TieBreak[$ArrI]):'') . '" onblur="updateScore(this)" onfocus="this.select()" tabindex="' . ($TabIndex++) . '"></td>';
									}
								}
								$Html[$ByeChooser] .= '<td align="center"><input type="checkbox" id="cl_' . $id . '" ' . ($Match['closest'] ? ' checked="checked"' : '') . ' onclick="updateScore(this)" tabindex="' . ($TabIndex++) . '"></td>';
							}
							$Html[$ByeChooser].='<td>';
							$Html[$ByeChooser].='<input '.(($Match['winner'] or $Match['oppWinner'] or ($Match['irm'] and $Match['oppIrm']))?'':'class="d-none"').' type="button" value="'.get_text('NextPhase').'" id="next_'.$id.'" onclick="move2next(this)" tabindex="'.($Offset++).'">';
							$Html[$ByeChooser].='</td>';
							$Val=$Match['tie'];
							if($Match['irm']) {
								if(strstr($TieSelect, 'irm-'.$Match['irm'])) {
									$Val='irm-'.$Match['irm'];
								} else {
									$Val='man';
								}
							}
							$Val='value="'.$Val.'"';
							$Html[$ByeChooser].='<td><select id="irm_' . $id .'" onChange="SendToServer(this, this.value)" tabindex="'.($Offset++).'">'.str_replace($Val, $Val.' selected="selected"', $TieSelect).'</select></td>';
							// $Html[$ByeChooser].='<td><input value="'.$Match['notes'].'" id="note_' . $id .'" onChange="SendToServer(this, this.value)" tabindex="'.($Offset++).'"></td>';
							$Html[$ByeChooser].='</tr>';
						}

						if($Match[$Athlete2]) {
                            $Class=(($Match['winner'] or $Match['oppWinner'] or ($Match['irm'] and $Match['oppIrm'])) ? 'disabled'.($Match['oppWinner'] ? ' win' : '') : '');
                            $ByeChooser=intval($Match['tie']==2 or $Match['irm'] or $Match['oppTie']==2 or $Match['oppIrm']);
                            if($ByeChooser) {
                                $Class= 'Bye';
                            }
							$Arrowstring=str_pad($Match['oppArrowstring'], $MaxArrows, ' ', STR_PAD_RIGHT);

							$id=$Team.'_'.$EvCode.'_'.$Match['oppMatchNo'];

							$Html[$ByeChooser].='<tr class="'.$Class.'" id="row_'.$id.'">
								<td id="tgt_'.$id.'">'.ltrim($Match['oppTarget'], '0').'</td>
								<td id="nam_'.$id.'">'.$Match[$Athlete2].'</td>
								<td id="cty_'.$id.'">'.$Match['oppCountryCode'].'</td>';
							for($n=$StartIdx; $n<$EndIdx; $n++) {
								$a='';
								if(strlen(trim($Arrowstring[$n]))) $a=DecodeFromLetter($Arrowstring[$n]);
								$Html[$ByeChooser].='<td><input type="text" size="2" id="s_'.$id.'_'.$n.'" value="'.$a.'" onblur="updateScore(this)" onfocus="this.select()" tabindex="'.($TabIndex++).'"></td>';
							}
							$Html[$ByeChooser].='<td id="tot_'.$id.'" class="Right">'.$Match['oppScore'].'</td>';
                            if($Phases['meta']['checkGolds']) {
                                $Html[$ByeChooser].='<td id="tb1_'.$id.'" class="Right">'.$Match['oppGolds'].'</td>';
                            }
                            if($Phases['meta']['checkXnines']) {
                                $Html[$ByeChooser].='<td id="tb2_'.$id.'" class="Right">'.$Match['oppXnines'].'</td>';
                            }
							$Html[$ByeChooser].='<td id="set_'.$id.'" class="Center">'.($Phases['meta']['matchMode']?$Match['oppSetScore']:'').'</td>';
							if($EndIdx==$MaxArrows) {
								$TieBreak=str_pad($Match['oppTiebreak'], $Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO'], ' ', STR_PAD_RIGHT);
								$SoArrs=$Phases['meta'][$Items['meta']['FinElimChooser']?'elimSO':'finSO'];
								for($pSo=0; $pSo<3; $pSo++ ) {
									for ($n = 0; $n < $SoArrs; $n++) {
										$ArrI = $n+($pSo*$SoArrs);
										$Html[$ByeChooser] .= '<td><input type="text" size="2" id="s_' . $id . '_' . ($MaxArrows+$ArrI) . '" value="' . (!empty($TieBreak[$ArrI]) ? DecodeFromLetter($TieBreak[$ArrI]):'') . '" onblur="updateScore(this)" onfocus="this.select()" tabindex="' . ($TabIndex++) . '"></td>';
									}
								}
								$Html[$ByeChooser] .= '<td align="center"><input type="checkbox" id="cl_' . $id . '" ' . ($Match['oppClosest'] ? ' checked="checked"' : '') . ' onclick="updateScore(this)" tabindex="' . ($TabIndex++) . '"></td>';
							}
							$Html[$ByeChooser].='<td>';
							if(!$Match[$Athlete1]) {
								$Html[$ByeChooser].='<input '.(($Match['winner'] or $Match['oppWinner'] or ($Match['irm'] and $Match['oppIrm']))?'':'class="d-none"').' type="button" value="'.get_text('NextPhase').'" id="next_'.$id.'" onclick="move2next(this)" tabindex="'.($Offset++).'">';
							}
							$Html[$ByeChooser].='</td>';
							$Val=$Match['oppTie'];
							if($Match['oppIrm']) {
								if(strstr($TieSelect, 'irm-'.$Match['oppIrm'])) {
									$Val='irm-'.$Match['oppIrm'];
								} else {
									$Val='man';
								}
							}
							$Val='value="'.$Val.'"';
							$Html[$ByeChooser].='<td><select id="irm_' . $id .'" onChange="SendToServer(this, this.value)" tabindex="'.($Offset++).'">'.str_replace($Val, $Val.' selected="selected"', $TieSelect).'</select></td>';
							// $Html[$ByeChooser].='<td><input value="'.$Match['oppNotes'].'" id="note_' . $id .'" onChange="SendToServer(this, this.value)" tabindex="'.($Offset++).'"></td>';
							$Html[$ByeChooser].='</tr>';
						}

						$Html[$ByeChooser].='<tr><td class="divider" colspan="'.$Cols.'"></td></tr>';
					}
					$JSON['html'].=implode('',$Html);
				}

			}
			if($JSON['html']) {
				$JSON['html']='<table class="Tabella" style="width:auto; margin:auto;">'.$JSON['html'].'</table>';
				$JSON['error']=0;
			}
			JsonOut($JSON);
		}
		break;
	case 'updateArrow':
		if(!(isset($_REQUEST['event']) and isset($_REQUEST['team']) and isset($_REQUEST['match']) and isset($_REQUEST['what']) and isset($_REQUEST['arrow']))) {
			if($_REQUEST['what']!='cl' and !isset($_REQUEST['index'])) {
				JsonOut($JSON);
			}
		}
		$TeamEvent = intval($_REQUEST['team']);
		if(!hasACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite) or ($TeamEvent==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM))) {
			JsonOut($JSON);
		}
		$event = $_REQUEST['event'];
		$match = intval($_REQUEST['match']);
		$what = $_REQUEST['what'];
		$arrow = $_REQUEST['arrow'];
		$index = intval($_REQUEST['index']??-1);

		// check what is being called!
		switch($what) {
			case 's':
				$validData=GetMaxScores($event, $match, $TeamEvent);
				// if spotter sends a "0" it is changed into an "M"
				if($arrow==="0") {
					$arrow="M";
				}
				// Check the arrow value is OK
                $ArrowLetter = GetLetterFromPrint(strtoupper($arrow), $validData["Arrows"]);
                if(!$ArrowLetter) {
					$ArrowLetter = ' ';
				}

				$JSON['arrow']=trim($arrow) ? DecodeFromLetter($ArrowLetter) : '';

				UpdateArrowString($match, $event, $TeamEvent, $ArrowLetter, $index+1, $index+1);
				$JSON['error']=0;
				break;
			case 'cl':
				if($TeamEvent) {
					$prefix='Tf';
					$table='TeamFinals';
				} else {
					$prefix='Fin';
					$table='Finals';
				}
				$JSON['error']=0;
				if($arrow) {
					$oppMatch=($match%2 ? $match-1 : $match+1);
					// we need to remove the other closest in case
					safe_w_sql("update $table set {$prefix}TbClosest=0 where {$prefix}tournament={$_SESSION['TourId']} and {$prefix}Event='$event' and {$prefix}Matchno=$oppMatch");
				}
				safe_w_sql("update $table set {$prefix}TbClosest={$arrow} where {$prefix}tournament={$_SESSION['TourId']} and {$prefix}Event='$event' and {$prefix}Matchno=$match");
				EvaluateMatch($event,$TeamEvent,$match);
				break;
			default:
				JsonOut($JSON);
		}

		// get all the data to send back
		$options=array();
		$options['tournament']=$_SESSION['TourId'];
		$options['events']=$event;
		$options['matchno']=($match%2 ? $match-1 : $match);

		if($TeamEvent) {
			$rank=Obj_RankFactory::create('GridTeam',$options);
		} else {
			$rank=Obj_RankFactory::create('GridInd',$options);
		}
		$rank->read();
		$Data=$rank->getData();

		$tmp='';
		$JSON['updates']=[];
		if(!empty($Data['sections'])) {
			$tmp=end($Data['sections']);
			if(!empty($tmp['phases'])) {
				$tmp=end($tmp['phases']);
				if(!empty($tmp['items'])) {
					$tmp=end($tmp['items']);
				}
			}
		}
		if($tmp) {
			$JSON['updates'][]=[
				'id'=>"#tot_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
				'k'=>'html',
				'val'=>$tmp['score']
			];
			$JSON['updates'][]=[
				'id'=>"#tot_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
				'k'=>'html',
				'val'=>$tmp['oppScore']
			];

			$JSON['updates'][]=[
				'id'=>"#tb1_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
				'k'=>'html',
				'val'=>$tmp['golds']
			];
			$JSON['updates'][]=[
				'id'=>"#tb1_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
				'k'=>'html',
				'val'=>$tmp['oppGolds']
			];

			$JSON['updates'][]=[
				'id'=>"#tb2_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
				'k'=>'html',
				'val'=>$tmp['xnines']
			];
			$JSON['updates'][]=[
				'id'=>"#tb2_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
				'k'=>'html',
				'val'=>$tmp['oppXnines']
			];

			$JSON['updates'][]=[
				'id'=>"#set_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
				'k'=>'html',
				'val'=>$tmp['setScore']
			];
			$JSON['updates'][]=[
				'id'=>"#set_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
				'k'=>'html',
				'val'=>$tmp['oppSetScore']
			];

			if($tmp['irm']==5) {
				$JSON['updates'][]=[
					'id'=>"#irm_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
					'k'=>'value',
					'val'=>'irm-5'
				];

			} elseif(!$tmp['irm']) {
				$JSON['updates'][]=[
					'id'=>"#irm_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
					'k'=>'value',
					'val'=>$tmp['tie']
				];
			}
			if($tmp['oppIrm']==5) {
				$JSON['updates'][]=[
					'id'=>"#irm_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
					'k'=>'value',
					'val'=>'irm-5'
				];

			} elseif(!$tmp['oppIrm']) {
				$JSON['updates'][]=[
					'id'=>"#irm_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
					'k'=>'value',
					'val'=>$tmp['oppTie']
				];
			}

			$JSON['updates'][]=[
				'id'=>"#next_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
				'k'=>'class',
				'val'=>(($tmp['winner'] or $tmp['oppWinner'] or ($tmp['irm'] and $tmp['oppIrm']))?'':'d-none')
			];
			$JSON['updates'][]=[
				'id'=>"#next_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
				'k'=>'class',
				'val'=>(($tmp['winner'] or $tmp['oppWinner'] or ($tmp['irm'] and $tmp['oppIrm']))?'':'d-none')
			];

            // finished match
			$JSON['updates'][]=[
				'id'=>"#row_{$TeamEvent}_{$event}_{$tmp['matchNo']}",
				'k'=>'class',
				'val'=>(($tmp['winner'] or $tmp['oppWinner'] or ($tmp['irm'] and $tmp['oppIrm'])) ? ($tmp['winner'] ? 'disabled win' : 'disabled') : '')
			];
			$JSON['updates'][]=[
				'id'=>"#row_{$TeamEvent}_{$event}_{$tmp['oppMatchNo']}",
				'k'=>'class',
				'val'=>(($tmp['winner'] or $tmp['oppWinner'] or ($tmp['irm'] and $tmp['oppIrm'])) ? ($tmp['oppWinner'] ? 'disabled win' : 'disabled') : '')
			];
		}

		break;
}

JsonOut($JSON);
