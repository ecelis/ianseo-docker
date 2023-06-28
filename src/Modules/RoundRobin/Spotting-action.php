<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error'=>1, 'msg'=>get_text('ErrGenericError', 'Errors'));
$Team=intval($_REQUEST['team'] ?? -1);

if($Team==-1 or !CheckTourSession() or !hasACL(AclRobin, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

require_once('Common/Lib/CommonLib.php');

$Event=($_REQUEST['event']??'');
$Level=($_REQUEST['level']??'0');
$Group=($_REQUEST['group']??'0');
$Round=($_REQUEST['round']??'0');
$Match=($_REQUEST['match']??'0');

$JSON['items']=[];
switch($_REQUEST['act']) {
	case 'getEvents':
		$q=safe_r_sql("select EvCode as k, concat_ws(' - ', EvCode, EvEventName) as v from Events where EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']} order by EvProgr");
		while($r=safe_fetch($q)) {
			$JSON['items'][]=$r;
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'getLevels':
		$q=safe_r_sql("select RrLevLevel as k, RrLevName as v from RoundRobinLevel where RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevTournament={$_SESSION['TourId']} order by RrLevName");
		while($r=safe_fetch($q)) {
			$JSON['items'][]=$r;
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'getGroups':
		$JSON['g']=[];
		$JSON['r']=[];
		$q=safe_r_sql("select RrGrGroup as k, RrGrName as v from RoundRobinGroup where RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrTournament={$_SESSION['TourId']} and RrGrLevel=$Level order by RrGrName");
		while($r=safe_fetch($q)) {
			$JSON['g'][]=$r;
		}
		$q=safe_r_sql("select distinct RrMatchRound as k from RoundRobinMatches where RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchTournament={$_SESSION['TourId']} and RrMatchLevel=$Level order by RrMatchRound");
		while($r=safe_fetch($q)) {
			$r->v=get_text('RoundNum', 'RoundRobin', $r->k);
			$JSON['r'][]=$r;
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'getMatches':
		$options=[
			'team'=>$Team,
			'events'=>[$Event],
			'levels'=>[$Level],
			'groups'=>[$Group],
			'rounds'=>[$Round],
		];

		require_once('Common/Lib/Obj_RankFactory.php');
		$MyQuery = Obj_RankFactory::create('Robin', $options)->getQuery(true);

		$q=safe_r_sql($MyQuery);
		while($r=safe_fetch($q)) {
			$JSON['items'][]=array('k'=>$r->M1MatchNo, 'v'=>$r->Athlete1.' ('.$r->CoShort1.') - '.$r->Athlete2.' ('.$r->CoShort2.')');
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'getScorecard':
		$JSON=array(
			'error'=>1, 'isLive' => 0, 'isAlternate' => 0, 'winner'=>'', 'msg'=>'', 'config'=>array(), 'nameL' => '', 'nameR'=>'', 'scoreL'=>'', 'scoreR' => '', 'target' => '', 'targetSize' => 0);

		$options=[
			'team'=>$Team,
			'events'=>[$Event],
			'levels'=>[$Level],
			'groups'=>[$Group],
			'rounds'=>[$Round],
			'matchno'=>$Match,
		];
		$MatchId=$Match;

		require_once('Common/Lib/Obj_RankFactory.php');
		$rank = Obj_RankFactory::create('Robin', $options);
		$rank->read();
		$Data=$rank->getData();

		if(empty($Data['sections'])) {
			JsonOut($JSON);
		}
		$Section=end($Data['sections']);

		if(empty($Section['levels'])) {
			JsonOut($JSON);
		}
		$L=end($Section['levels']);

		if(!$L['matches']) {
			JsonOut($JSON);
		}
		$G=end($L['matches']);

		if(!$G['rounds']) {
			JsonOut($JSON);
		}
		$R=end($G['rounds']);

		if(!$R['items']) {
			JsonOut($JSON);
		}
		$MatchIdL=$Match;
		$MatchIdR=$MatchId+1;

		$Match=end($R['items']);

		$JSON['error']=0;
		$JSON['winner']=$Match['winner'] ? 'L' : ($Match['oppWinner'] ? 'R' : '');
		$JSON['confirmed']=($Match['status']==1 and $Match['oppStatus']==1);

		$JSON['irmL']=$Match['irm'];
		$JSON['irmR']=$Match['oppIrm'];

		$JSON['matchnoL']=$Match['matchNo'];
		$JSON['matchnoR']=$Match['oppMatchNo'];

		$JSON['isAlternate']=($Match['shootFirst'] or $Match['oppShootFirst']);
		$JSON['isLive']=($Match['liveFlag']>0);

		$JSON['nameL']=$Match['athlete'].' ('.$Match['countryCode'].')';
		$JSON['nameR']=$Match['oppAthlete'].' ('.$Match['oppCountryCode'].')';

		$JSON['config']['arrows']=$L['arrows'];
		$JSON['config']['ends']=$L['ends'];
		$JSON['config']['so']=$L['soNumArrows'];
		$JSON['config']['soEnds']=(ceil(min(strlen(trim($Match['tiebreak'])), strlen(trim($Match['oppTiebreak'])))/$JSON['config']['so']))+1;

		// build score grids
		$JSON['scoreL']='<table class="Scorecard" matchno="'.$MatchIdL.'"><tr><th class="Alternate AlternateTitle">'.get_text('ShootsFirst', 'Tournament').'</th><th></th>';
		$JSON['scoreR']='<table class="Scorecard" matchno="'.$MatchIdR.'"><tr><th class="Alternate AlternateTitle">'.get_text('ShootsFirst', 'Tournament').'</th><th></th>';

		for($i=1; $i<=$JSON['config']['arrows']; $i++) {
			$JSON['scoreL'].='<th>'.$i.'</th>';
			$JSON['scoreR'].='<th>'.$i.'</th>';
		}

		if($Section['meta']['matchMode']) {
			$JSON['scoreL'].='<th>'.get_text('SetTotal','Tournament').'</th>';
			$JSON['scoreR'].='<th>'.get_text('SetTotal','Tournament').'</th>';
			$JSON['scoreL'].='<th>' . get_text('SetPoints','Tournament'). '</th>';
			$JSON['scoreR'].='<th>' . get_text('SetPoints','Tournament'). '</th>';
			$JSON['scoreL'].='<th>' . get_text('TotalShort','Tournament'). '</th>';
			$JSON['scoreR'].='<th>' . get_text('TotalShort','Tournament'). '</th>';
		} else {
			$JSON['scoreL'].='<th>'.get_text('TotalProg','Tournament').'</th>';
			$JSON['scoreR'].='<th>'.get_text('TotalProg','Tournament').'</th>';
			$JSON['scoreL'].='<th>'.get_text('RunningTotal','Tournament').'</th>';
			$JSON['scoreR'].='<th>'.get_text('RunningTotal','Tournament').'</th>';
		}
		$JSON['scoreL'].='</tr>';
		$JSON['scoreR'].='</tr>';

		$Match['arrowstring']=str_pad($Match['arrowstring'],$JSON['config']['ends']*$JSON['config']['arrows'], ' ', STR_PAD_RIGHT);
		$Match['oppArrowstring']=str_pad($Match['oppArrowstring'],$JSON['config']['ends']*$JSON['config']['arrows'], ' ', STR_PAD_RIGHT);
		$Match['setPoints']=array_pad(explode('|', $Match['setPoints']), $JSON['config']['ends'],'');
		$Match['oppSetPoints']=array_pad(explode('|', $Match['oppSetPoints']), $JSON['config']['ends'],'');
		$Match['setPointsByEnd']=array_pad(explode('|', $Match['setPointsByEnd']), $JSON['config']['ends'],'');
		$Match['oppSetPointsByEnd']=array_pad(explode('|', $Match['oppSetPointsByEnd']), $JSON['config']['ends'],'');
		$totL=0;
		$totR=0;

		$TabIndexOffset=100;

		for($i=0;$i<$JSON['config']['ends'];$i++) {
			$ShootsFirstL=$Match['shootFirst'] & pow(2, $i);
			$ShootsFirstR=$Match['oppShootFirst'] & pow(2, $i);
			$JSON['scoreL'].='<tr so="0" end="'.$i.'">';
			$JSON['scoreR'].='<tr so="0" end="'.$i.'">';
			$JSON['scoreL'].='<th class="Alternate"><input class="ShootsFirst" so="0" type="radio" id="first['.$Team.']['.$Event.']['.$Level.']['.$Group.']['.$Round.']['.$MatchIdL.']['.$i.']" name="first['.$i.']" onclick="setShootingFirst(this)" '.($ShootsFirstL ? 'checked="checked"' : '').'></th>';
			$JSON['scoreR'].='<th class="Alternate"><input class="ShootsFirst" so="0" type="radio" id="first['.$Team.']['.$Event.']['.$Level.']['.$Group.']['.$Round.']['.$MatchIdR.']['.$i.']" name="first['.$i.']" onclick="setShootingFirst(this)" '.($ShootsFirstR ? 'checked="checked"' : '').'></th>';
			$JSON['scoreL'].='<th>'.($i+1).'</th>';
			$JSON['scoreR'].='<th>'.($i+1).'</th>';
			for($j=0;$j<$JSON['config']['arrows'];$j++) {
				if($JSON['isAlternate']) {
					if(empty($Section['meta']['maxTeamPerson'])){
						if($ShootsFirstR) {
							$tabIndexL=$i*$JSON['config']['arrows']*2 + $j*2 + 2;
							$tabIndexR=$i*$JSON['config']['arrows']*2 + $j*2 + 1;
						} else {
							$tabIndexL=$i*$JSON['config']['arrows']*2 + $j*2 + 1;
							$tabIndexR=$i*$JSON['config']['arrows']*2 + $j*2 + 2;
						}
					} else {
						if($ShootsFirstR) {
							$tabIndexL=$i*$JSON['config']['arrows']*2 + intval($j/$Section['meta']['maxTeamPerson'])*$JSON['config']['arrows'] + $Section['meta']['maxTeamPerson'] + $j%$Section['meta']['maxTeamPerson'] + 1;
							$tabIndexR=$i*$JSON['config']['arrows']*2 + intval($j/$Section['meta']['maxTeamPerson'])*$JSON['config']['arrows'] + $j%$Section['meta']['maxTeamPerson'] + 1;
						} else {
							$tabIndexL=$i*$JSON['config']['arrows']*2 + intval($j/$Section['meta']['maxTeamPerson'])*$JSON['config']['arrows'] + $j%$Section['meta']['maxTeamPerson'] + 1;
							$tabIndexR=$i*$JSON['config']['arrows']*2 + intval($j/$Section['meta']['maxTeamPerson'])*$JSON['config']['arrows'] + $Section['meta']['maxTeamPerson'] + $j%$Section['meta']['maxTeamPerson'] + 1;
						}
						//$JSON['debug'][]= "$i:$j (".($ShootsFirstR ? 'R' : 'L').") $offset = $tabIndexL - $tabIndexR";
					}
				} else {
					$tabIndexL=$i*$JSON['config']['arrows'] + $j + 1;
					$tabIndexR=$JSON['config']['arrows']*$JSON['config']['ends'] + 3*$JSON['config']['so'] + $i*$JSON['config']['arrows'] + $j + 1;
				}
				$arIndex=$i*$JSON['config']['arrows'] + $j;
				$JSON['scoreL'].='<td class="arrowcell"><input type="text" tabindex="'.($TabIndexOffset + $tabIndexL).'" id="Arrow['.$MatchIdL.'][0]['.$i.']['.$j.']" onfocus="selectArrow(this)" onblur="updateArrow(this)" value="'.trim(DecodeFromLetter($Match['arrowstring'][$arIndex])).'"></td>';
				$JSON['scoreR'].='<td class="arrowcell"><input type="text" tabindex="'.($TabIndexOffset + $tabIndexR).'" id="Arrow['.$MatchIdR.'][0]['.$i.']['.$j.']" onfocus="selectArrow(this)" onblur="updateArrow(this)" value="'.trim(DecodeFromLetter($Match['oppArrowstring'][$arIndex])).'"></td>';
			}
			$JSON['scoreL'].='<td id="EndTotalL_'.$i.'">'.$Match['setPoints'][$i].'</td>';
			$JSON['scoreR'].='<td id="EndTotalR_'.$i.'">'.$Match['oppSetPoints'][$i].'</td>';
			if($Section['meta']['matchMode']) {
				$JSON['scoreL'].='<td id="EndSetL_'.$i.'">'.$Match['setPointsByEnd'][$i].'</td>';
				$JSON['scoreR'].='<td id="EndSetR_'.$i.'">'.$Match['oppSetPointsByEnd'][$i].'</td>';
				$totL+=($Match['setPointsByEnd'][$i] ? $Match['setPointsByEnd'][$i] : 0);
				$totR+=($Match['oppSetPointsByEnd'][$i] ? $Match['oppSetPointsByEnd'][$i] : 0);
			} else {
				$totL+=($Match['setPoints'][$i] ? $Match['setPoints'][$i] : 0);
				$totR+=($Match['oppSetPoints'][$i] ? $Match['oppSetPoints'][$i] : 0);
			}
			$JSON['scoreL'].='<td id="TotalL_'.$i.'">'.$totL.'</td>';
			$JSON['scoreR'].='<td id="TotalR_'.$i.'">'.$totR.'</td>';
			$JSON['scoreL'].='</tr>';
			$JSON['scoreR'].='</tr>';
		}

		// Shoot Offs
		$Match['tiebreak']=str_pad($Match['tiebreak'],3*$JSON['config']['so'], ' ', STR_PAD_RIGHT);
		$Match['oppTiebreak']=str_pad($Match['oppTiebreak'],3*$JSON['config']['so'], ' ', STR_PAD_RIGHT);
		$Match['tiebreakDecoded']=array_pad(explode(',', $Match['tiebreakDecoded']), 3,'');
		$Match['oppTiebreakDecoded']=array_pad(explode(',', $Match['oppTiebreakDecoded']), 3,'');
		$totL=0;
		$totR=0;
		$ShootsFirstL=$Match['shootFirst'] & pow(2, $JSON['config']['ends']);
		$ShootsFirstR=$Match['oppShootFirst'] & pow(2, $JSON['config']['ends']);

		// Shoot Off ends/arrows, one more than necessary
		for($pSo=0; $pSo<$JSON['config']['soEnds']; $pSo++ ) {
			$JSON['scoreL'].='<tr class="SO" so="1" end="'.$pSo.'">';
			$JSON['scoreR'].='<tr class="SO" so="1" end="'.$pSo.'">';
			if($pSo==0) {
				$JSON['scoreL'].='<th class="Alternate" rowspan="'.($JSON['config']['soEnds']).'"><input class="ShootsFirst" so="1" type="radio" id="first[' . $Team.']['.$Event.']['.$Level.']['.$Group.']['.$Round.']['.$MatchIdL.']['.$JSON['config']['ends'].']" name="first[so]" onclick="setShootingFirst(this)" '.($ShootsFirstL ? 'checked="checked"' : '').'></th>';
				$JSON['scoreR'].='<th class="Alternate" rowspan="'.($JSON['config']['soEnds']).'"><input class="ShootsFirst" so="1" type="radio" id="first[' . $Team.']['.$Event.']['.$Level.']['.$Group.']['.$Round.']['.$MatchIdR.']['.$JSON['config']['ends'].']" name="first[so]" onclick="setShootingFirst(this)" '.($ShootsFirstR ? 'checked="checked"' : '').'></th>';
				$JSON['scoreL'].='<th rowspan="'.($JSON['config']['soEnds']).'">S.O.</th>';
				$JSON['scoreR'].='<th rowspan="'.($JSON['config']['soEnds']).'">S.O.</th>';

			}

			//$JSON['scoreL'].='<td class="Center" colspan="' . $JSON['config']['arrows'] . '">';
			//$JSON['scoreR'].='<td class="Center" colspan="' . $JSON['config']['arrows'] . '">';
			for ($i = 0; $i < $JSON['config']['so']; $i++) {
				if($JSON['isAlternate']) {
					if($ShootsFirstR) {
						$tabIndexL=$JSON['config']['ends']*$JSON['config']['arrows']*2 + $pSo*$JSON['config']['so']*2 + $i*2 + 2;
						$tabIndexR=$JSON['config']['ends']*$JSON['config']['arrows']*2 + $pSo*$JSON['config']['so']*2 + $i*2 + 1;
					} else {
						$tabIndexL=$JSON['config']['ends']*$JSON['config']['arrows']*2 + $pSo*$JSON['config']['so']*2 + $i*2 + 1;
						$tabIndexR=$JSON['config']['ends']*$JSON['config']['arrows']*2 + $pSo*$JSON['config']['so']*2 + $i*2 + 2;
					}
				} else {
					$tabIndexL=$JSON['config']['ends']*$JSON['config']['arrows']*2 + 3*$JSON['config']['so'] + $pSo*$JSON['config']['so'] + $i +1;
					$tabIndexR=$JSON['config']['ends']*$JSON['config']['arrows']*2 + 3*$JSON['config']['so'] + $pSo*$JSON['config']['so'] + $i + $JSON['config']['so'] +1;
				}
				$arIndex=$pSo*$JSON['config']['so'] + $i;
				//$JSON['scoreL'].='<td class="arrowcell" colspan="'.($JSON['config']['arrows']/$JSON['config']['so']).'"><input type="text" id="Arrow['.$MatchIdL.'][1]['.$pSo.']['.$i.']" tabindex="'.($TabIndexOffset + $tabIndexL).'" onfocus="selectArrow(this)" onblur="updateArrow(this)" value="'.(strlen($Match['tiebreak'])>$arIndex ? trim(DecodeFromLetter($Match['tiebreak'][$arIndex])) : '').'">'.
				//    ($pSo == ($JSON['config']['soEnds']-1) ? '<div class="newSoNeeded" style="display: none;"><input type="checkbox" onclick="toggleClosest(this)" ref="'.$MatchIdL.'">'.get_text('ClosestShort', 'Tournament').'<input type="button" value="+1" onclick="addPoint(\'Arrow['.$MatchIdL.'][1]['.$pSo.']['.$i.']\')"></div>' : ''). '</td>';
				//$JSON['scoreR'].='<td class="arrowcell" colspan="'.($JSON['config']['arrows']/$JSON['config']['so']).'"><input type="text" id="Arrow['.$MatchIdR.'][1]['.$pSo.']['.$i.']" tabindex="'.($TabIndexOffset + $tabIndexR).'" onfocus="selectArrow(this)" onblur="updateArrow(this)" value="'.(strlen($Match['oppTiebreak'])>$arIndex ? trim(DecodeFromLetter($Match['oppTiebreak'][$arIndex])):'').'">'.
				//    ($pSo == ($JSON['config']['soEnds']-1) ? '<div class="newSoNeeded" style="display: none;"><input type="checkbox" onclick="toggleClosest(this)" ref="'.$MatchIdR.'">'.get_text('ClosestShort', 'Tournament').'<input type="button" value="+1"  onclick="addPoint(\'Arrow['.$MatchIdR.'][1]['.$pSo.']['.$i.']\')"></div>' : ''). '</td>';
				$JSON['scoreL'].='<td class="arrowcell" colspan="'.($JSON['config']['arrows']/$JSON['config']['so']).'"><input type="text" id="Arrow['.$MatchIdL.'][1]['.$pSo.']['.$i.']" tabindex="'.($TabIndexOffset + $tabIndexL).'" onfocus="selectArrow(this)" onblur="updateArrow(this)" value="'.(strlen($Match['tiebreak'])>$arIndex ? trim(DecodeFromLetter($Match['tiebreak'][$arIndex])) : '').'"></td>';
				$JSON['scoreR'].='<td class="arrowcell" colspan="'.($JSON['config']['arrows']/$JSON['config']['so']).'"><input type="text" id="Arrow['.$MatchIdR.'][1]['.$pSo.']['.$i.']" tabindex="'.($TabIndexOffset + $tabIndexR).'" onfocus="selectArrow(this)" onblur="updateArrow(this)" value="'.(strlen($Match['oppTiebreak'])>$arIndex ? trim(DecodeFromLetter($Match['oppTiebreak'][$arIndex])):'').'"></td>';
			}
			$JSON['scoreL'] .= '<td class="Bold" id="EndTotalL_SO_'.$pSo.'">'.(array_key_exists($pSo,$Match['tiebreakDecoded']) ? $Match['tiebreakDecoded'][$pSo] : '').'</td>';
			$JSON['scoreR'] .= '<td class="Bold" id="EndTotalR_SO_'.$pSo.'">'.(array_key_exists($pSo,$Match['oppTiebreakDecoded']) ? $Match['oppTiebreakDecoded'][$pSo] : '').'</td>';
			//$JSON['scoreL'].='</td>';
			//$JSON['scoreR'].='</td>';

			if($pSo==0) {
				if ($Section['meta']['matchMode']) {
					$JSON['scoreL'] .= '<td></td>'.
						'<td class="Bold" rowspan="' . ($JSON['config']['soEnds']) . '" id="EndSetL_SO">' . $Match['setScore'] . '</td>';
					$JSON['scoreR'] .= '<td></td>'.
						'<td class="Bold" rowspan="' . ($JSON['config']['soEnds']) . '" id="EndSetR_SO">' . $Match['oppSetScore'] . '</td>';
				} else {
					$JSON['scoreL'] .= '<td class="Bold" rowspan="' . ($JSON['config']['soEnds']) . '" id="TotalL_SO"></td>';
					$JSON['scoreR'] .= '<td class="Bold" rowspan="' . ($JSON['config']['soEnds']) . '" id="TotalR_SO"></td>';
				}
			}

			$JSON['scoreL'].='</tr>';
			$JSON['scoreR'].='</tr>';
		}

		// Star Raising row for normal arrows
		$JSON['scoreL'].='<tbody class="StarRaiserArrows">';
		$JSON['scoreR'].='<tbody class="StarRaiserArrows">';
		$JSON['scoreL'].='<tr class="SoRaiser"><td class="Alternate"></td><td></td>';
		$JSON['scoreR'].='<tr class="SoRaiser"><td class="Alternate"></td><td></td>';
		for($i=0;$i<$JSON['config']['arrows'];$i++) {
			$JSON['scoreL'].='<td><input type="button" ref="" value="+1" id="Star-'.$MatchIdL.'-'.$i.'" class="Hidden" onclick="raiseStar(this)"></td>';
			$JSON['scoreR'].='<td><input type="button" ref="" value="+1" id="Star-'.$MatchIdR.'-'.$i.'" class="Hidden" onclick="raiseStar(this)"></td>';
		}
		$JSON['scoreL'].='<td colspan="'.($Section['meta']['matchMode'] ? 3 : 2).'"><input type="button" id="StarRemoveL" ref="ScorecardL" class="Hidden" onclick="removeStars(this)" value="'.get_text('RemoveStars', 'Tournament').'"></td>';
		$JSON['scoreR'].='<td colspan="'.($Section['meta']['matchMode'] ? 3 : 2).'"><input type="button" id="StarRemoveR" ref="ScorecardR" class="Hidden" onclick="removeStars(this)" value="'.get_text('RemoveStars', 'Tournament').'"></td>';
		$JSON['scoreL'].='</tr>';
		$JSON['scoreR'].='</tr>';
		$JSON['scoreL'].='</tbody>';
		$JSON['scoreR'].='</tbody>';

		// Star Raising row for SO arrows and Closest to Center
		$JSON['scoreL'].='<tbody class="StarRaiserSO">';
		$JSON['scoreR'].='<tbody class="StarRaiserSO">';
		$JSON['scoreL'].='<tr class="SoRaiser"><td class="Alternate"></td><td></td>';
		$JSON['scoreR'].='<tr class="SoRaiser"><td class="Alternate"></td><td></td>';
		for($i=0;$i<$JSON['config']['so'];$i++) {
			$JSON['scoreL'].='<td colspan="'.($JSON['config']['arrows']/$JSON['config']['so']).'"><input type="button" ref="" value="+1" id="StarSO-'.$MatchIdL.'-'.$i.'" class="Hidden" onclick="raiseStar(this)"></td>';
			$JSON['scoreR'].='<td colspan="'.($JSON['config']['arrows']/$JSON['config']['so']).'"><input type="button" ref="" value="+1" id="StarSO-'.$MatchIdR.'-'.$i.'" class="Hidden" onclick="raiseStar(this)"></td>';
		}
		$JSON['scoreL'].='<td colspan="'.($Section['meta']['matchMode'] ? 3 : 2).'">
	<input type="button" id="StarSORemoveL" ref="ScorecardL" class="Hidden" onclick="removeStars(this)" value="'.get_text('RemoveStars', 'Tournament').'">
	<span class="ClosestSpan"><input id="ClosestL" class="Closest" type="checkbox" onclick="toggleClosest(this)" value="'.$MatchIdL.'"'.($Match['closest'] ? ' checked="checked"' : '').'>'.get_text('ClosestShort', 'Tournament').'</span>
	</td>';
		$JSON['scoreR'].='<td colspan="'.($Section['meta']['matchMode'] ? 3 : 2).'">
	<input type="button" id="StarSORemoveR" ref="ScorecardR" class="Hidden" onclick="removeStars(this)" value="'.get_text('RemoveStars', 'Tournament').'">
	<span class="ClosestSpan"><input id="ClosestR" class="Closest" type="checkbox" onclick="toggleClosest(this)" value="'.$MatchIdR.'"'.($Match['oppClosest'] ? ' checked="checked"' : '').'>'.get_text('ClosestShort', 'Tournament').'</span>
	</td>';
		$JSON['scoreL'].='</tr>';
		$JSON['scoreR'].='</tr>';
		$JSON['scoreL'].='</tbody>';
		$JSON['scoreR'].='</tbody>';

		// Last row: Confirm End, New SO...
		$JSON['scoreL'].='<tr><td align="center" colspan="'.(6+$JSON['config']['arrows']).'">'.
			'<input '.(($Match['status'] & 1) ? 'disabled="disabled"' : '').' type="button" id="confirmL" ref="ConfirmL" onclick="ConfirmEnd(this)" value="'.get_text('ConfirmEnd', 'Tournament').'">'.
			'<input class="newSoNeeded" style="display: none; margin-left: 10px;" type="button" onclick="buildScorecard()" value="'.get_text('NewSORequired', 'Tournament').'"></td>'.
			'</td></tr>';
		$JSON['scoreR'].='<tr><td align="center" colspan="'.(6+$JSON['config']['arrows']).'">'.
			'<input '.(($Match['oppStatus'] & 1) ? 'disabled="disabled"' : '').' type="button" id="confirmR" ref="ConfirmR" onclick="ConfirmEnd(this)" value="'.get_text('ConfirmEnd', 'Tournament').'">'.
			'<input class="newSoNeeded" style="display: none; margin-left: 10px;" type="button" onclick="buildScorecard()" value="'.get_text('NewSORequired', 'Tournament').'"></td>'.
			'</td></tr>';

		$JSON['scoreL'].='</table>';
		$JSON['scoreR'].='</table>';

		// if($Arrows) {
		// 	// builds an empty target
		// 	require_once('Common/Obj_Target.php');
		// 	$target = new Obj_Target();
		//
		// 	// we already have most of the data needed for the target!
		// 	$target->initSVG($_SESSION['TourId'], $Event, $MatchId, $Team);
		// 	$target->setSVGHeader('', '');
		// 	$target->setTarget();
		//
		// 	for($i=0;$i<$JSON['config']['ends'];$i++) {
		// 		$tmpL=array();
		// 		$tmpR=array();
		// 		for($j=0;$j<$JSON['config']['arrows'];$j++) {
		// 			if(empty($Match['arrowPosition'][$i*$JSON['config']['arrows']+$j])) {
		// 				$tmpL['SvgArrow['.$MatchIdL.'][0]['.$i.']['.$j.']']=array('D' => 999, 'X'=>-2000, 'Y'=>-2000, 'R'=>3);
		// 			} else {
		// 				$tmpL['SvgArrow['.$MatchIdL.'][0]['.$i.']['.$j.']']=$Match['arrowPosition'][$i*$JSON['config']['arrows']+$j];
		// 			}
		// 			if(empty($Match['oppArrowPosition'][$i*$JSON['config']['arrows']+$j])) {
		// 				$tmpR['SvgArrow['.$MatchIdR.'][0]['.$i.']['.$j.']']=array('D' => 999, 'X'=>-2000, 'Y'=>-2000, 'R'=>3);
		// 			} else {
		// 				$tmpR['SvgArrow['.$MatchIdR.'][0]['.$i.']['.$j.']']=$Match['oppArrowPosition'][$i*$JSON['config']['arrows']+$j];
		// 			}
		// 		}
		// 		$target->drawSVGArrowsGroups('SvgEndL_'.$i, $tmpL);
		// 		$target->drawSVGArrowsGroups('SvgEndR_'.$i, $tmpR);
		// 	}
		// 	for($i=0;$i<$JSON['config']['soEnds'];$i++) {
		// 		$tmpL=array();
		// 		$tmpR=array();
		// 		for($j=0;$j<$JSON['config']['so'];$j++) {
		// 			if(empty($Match['tiePosition'][$i*$JSON['config']['so']+$j])) {
		// 				$tmpL['SvgArrow['.$MatchIdL.'][1]['.$i.']['.$j.']']=array('D' => 999, 'X'=>-2000, 'Y'=>-2000,'R'=>3);
		// 			} else {
		// 				$tmpL['SvgArrow['.$MatchIdL.'][1]['.$i.']['.$j.']']=$Match['tiePosition'][$i*$JSON['config']['so']+$j];
		// 			}
		// 			if(empty($Match['oppTiePosition'][$i*$JSON['config']['so']+$j])) {
		// 				$tmpR['SvgArrow['.$MatchIdR.'][1]['.$i.']['.$j.']']=array('D' => 999, 'X'=>-2000, 'Y'=>-2000,'R'=>3);
		// 			} else {
		// 				$tmpR['SvgArrow['.$MatchIdR.'][1]['.$i.']['.$j.']']=$Match['oppTiePosition'][$i*$JSON['config']['so']+$j];
		// 			}
		// 		}
		// 		$target->drawSVGArrowsGroups('SvgEndL_SO_'.$i, $tmpL);
		// 		$target->drawSVGArrowsGroups('SvgEndR_SO_'.$i, $tmpR);
		// 	}
		//
		// 	$target->DrawSVGSighter(end($tmpR) ?? end($tmpL) ?? array());
		//
		// 	$JSON['targetSize']=$target->Diameter;
		// 	$JSON['targetZoom']=round(sqrt($target->TargetRadius)/7, 1);
		// 	$JSON['target']=$target->OutputStringSVG();
		// }

		// check if it is a show match...
		$JSON['move2next']='';

		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'updateArrow':
		$JSON=array('error'=>1, 'changed' => 0, 'confirm' => '', 'winner' => 0, 'newSOPossible'=>false);

		if(empty($_REQUEST['Arrow'])) {
			JsonOut($JSON);
		}

		// get the ends, arrows, etc
		$q=safe_r_sql("select RrLevArrows as arrows, RrLevSO as so, RrLevEnds as ends from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$Level and RrLevEvent=".StrSafe_DB($Event));
		$obj=safe_fetch($q);
		if(!$obj) {
			JsonOut($JSON);
		}

		require_once('Common/Lib/ArrTargets.inc.php');
		require_once('Common/Lib/Obj_RankFactory.php');
		require_once('./Lib.php');
		foreach($_REQUEST['Arrow'] as $MatchId => $SOs) {
			$MainMatch=$MatchId%2 ? $MatchId-1 : $MatchId;
			$JSON['confirm']='confirm['.$MatchId.']';
			$JSON['DontMove']=false;
			foreach($SOs as $isSO=>$Ends) {
				foreach($Ends as $End=>$Arrows) {
					foreach($Arrows as $ArrowIndex => $ArrowValue) {
						$validData=GetMaxScores($Event, $MatchId, $Team);
						// if spotter sends a "0" it is changed into an "M"
						if($ArrowValue==="0") {
							$ArrowValue="M";
						}
						// Check the arrow value is OK
						if(array_key_exists(strtoupper(GetLetterFromPrint($ArrowValue)) , $validData["Arrows"])) {
							$ArrowLetter = GetLetterFromPrint($ArrowValue);
						} else {
							$ArrowLetter = ' ';
							if(strlen($ArrowValue)) {
								$JSON['DontMove']=true;
							}
						}
						$ArrowValue=trim($ArrowValue) ? DecodeFromLetter($ArrowLetter) : '';

						// index of arrow
						if($isSO) {
							$Index = $obj->arrows*$obj->ends + ($obj->so * $End) + $ArrowIndex + 1;
						} else {
							$Index = ($obj->arrows * $End) + $ArrowIndex + 1;
						}

						$JSON['p']=array();
						$JSON['t']=array();

						if(empty($_REQUEST['noUpdate'])) {
							$Closest=(isset($_REQUEST['Closest']) and $_REQUEST['Closest']==$MatchId);
							$IsFinished=RobinUpdateArrowString($MatchId, $Event, $Team, $Level, $Group, $Round, $ArrowLetter, $Index, $Index, $Closest);
						}

						// we need to send back the arrow value, the set total, the winner, etc
						$options=array();
						$options['tournament']=$_SESSION['TourId'];
						$options['team']=$Team;
						$options['events']=[$Event];
						$options['levels']=[$Level];
						$options['groups']=[$Group];
						$options['rounds']=[$Round];
						$options['matchno']=($MatchId%2 ? $MatchId-1 : $MatchId);

						$rank=Obj_RankFactory::create('Robin',$options);
						$rank->read();
						$Data=$rank->getData();

						if(empty($Data['sections'])) {
							JsonOut($JSON);
						}
						$Section=end($Data['sections']);

						if(empty($Section['levels'])) {
							JsonOut($JSON);
						}
						$L=end($Section['levels']);

						if(!$L['matches']) {
							JsonOut($JSON);
						}
						$G=end($L['matches']);

						if(!$G['rounds']) {
							JsonOut($JSON);
						}
						$R=end($G['rounds']);

						if(!$R['items']) {
							JsonOut($JSON);
						}
						$Match=end($R['items']);

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
								for($i=0;$i<$soEnds;$i++) {
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

							if($isSO) {
								for($i=0;$i<$soEnds;$i++) {
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

						$JSON['ClosestL']=$Match['closest'];
						$JSON['ClosestR']=$Match['oppClosest'];

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
										'nextValue'=>GetHigerArrowValue($Event, $Team, ValutaArrowString($ar)));
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
										'nextValue'=>GetHigerArrowValue($Event, $Team, ValutaArrowString($ar)));
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
										'nextValue'=>GetHigerArrowValue($Event, $Team, ValutaArrowString($ar)));
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
										'nextValue'=>GetHigerArrowValue($Event, $Team, ValutaArrowString($ar)));
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
		break;
	case 'setIRM':
		$ok=true;
		$irm=$_REQUEST['value'];
		$MainFilter="RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchTeam=$Team and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round";
		safe_w_sql("update RoundRobinMatches set RrMatchIrmType=$irm where $MainFilter and RrMatchMatchNo=$Match");
		$JSON['msg']="To FInish: what happens to participant, rank etc?";
		// switch($irm) {
		// 	case 5: // DNF: rank is OK, lost the match, move opponent to next phase
		// 	case 10: // DNS: rank is OK, lost the match, move opponent to next phase
		// 		break;
		// 	case 15: // DSQ: rank is OK but not shown, lost the match, goes last of his phase
		// 		$q=safe_r_sql("select FinAthlete
		// 	from Finals
		// 	where FinEvent='$event' and FinMatchNo=$match and FinTournament={$_SESSION['TourId']}");
		// 		if($r=safe_fetch($q)) {
		// 			// updates the Individual Final IRM with DSQ!
		// 			safe_w_sql("update Individuals set IndIrmTypeFinal=15 where (IndId, IndEvent, IndTournament) = ($r->FinAthlete, '$event', {$_SESSION['TourId']})");
		// 		}
		// 		break;
		// 	case 20: // DQB: Disqualified by behaviour, Virtually removed from any rank, TODO: other stay where they are? If Silver gets DQB, what happens to Bronze and 4th?
		// 		// gets the Team ID
		// 		$q=safe_r_sql("select FinAthlete
		// 	from Finals
		// 	where FinEvent='$event' and FinMatchNo=$match and FinTournament={$_SESSION['TourId']}");
		// 		if($r=safe_fetch($q)) {
		// 			// updates all the Individual result with DQB!
		// 			safe_w_sql("update Finals set FinIrmType=20 where (FinAthlete, FinEvent, FinTournament) = ($r->FinAthlete, '$event', {$_SESSION['TourId']})");
		// 			safe_w_sql("update Individuals set IndRank=$CFG->DERANKING, IndRankFinal=$CFG->DERANKING, IndIrmType=20, IndIrmTypeFinal=20 where (IndId, IndEvent, IndTournament) = ($r->FinAthlete, '$event', {$_SESSION['TourId']})");
		// 			safe_w_sql("update Qualifications set QuIrmType=20 where QuId=$r->FinAthlete");
		//
		// 			// check if he is part of a team in qualifications
		// 			$t=safe_r_sql("select * from TeamComponent where TcId=$r->FinAthlete");
		// 			while($u=safe_fetch($t)) {
		// 				// The team gets a DQB too!!!
		// 				safe_w_sql("update Teams set TeRank=$CFG->DERANKING, TeIrmType=20 where (TeCoId, TeSubTeam, TeEvent, TeTournament) = ($u->TcCoId, $u->TcSubTeam, '$u->TcEvent', {$_SESSION['TourId']})");
		// 			}
		//
		// 			// check if he is part of a team in Matches
		// 			$t=safe_r_sql("select * from TeamFinComponent where TfcId=$r->FinAthlete");
		// 			while($u=safe_fetch($t)) {
		// 				// The team gets a DQB too!!!
		// 				safe_w_sql("update TeamFinals set TfIrmType=20 where (TfTeam, TfSubTeam, TfEvent, TfTournament) = ($u->TfcCoId, $u->TfcSubTeam, '$u->TfcEvent', {$_SESSION['TourId']})");
		// 				safe_w_sql("update Teams set TeRankFinal=$CFG->DERANKING, TeIrmTypeFinal=20 where (TeCoId, TeSubTeam, TeEvent, TeTournament) = ($u->TfcCoId, $u->TfcSubTeam, '$u->TfcEvent', {$_SESSION['TourId']})");
		// 			}
		// 			$JSON['msg']='Please Check teams and individual rankings';
		// 		}
		// 		break;
		// 	default:
		// 		$JSON['msg']=get_text('Error');
		// 		$ok=false;
		// }

		if ($ok) {
			$JSON['error']=0;
			$JSON['msg']=get_text('CmdOk');
		}
		break;
	case 'confirmEnd':
		require_once('./Lib.php');
		$TabIndexOffset=100;

		$Params=getRobinArrowsParams($Team, $Event, $Level);
		$m=array($Match, ($Match%2) ? $Match-1 : $Match+1);

		// updates the confirmation of the arrows
		safe_w_sql("update RoundRobinMatches set RrMatchStatus=3, RrMatchDateTime=now()
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchTeam=$Team and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Match");

		$q=safe_r_sql("select
				RrMatchWinLose Winner,
				RrMatchStatus `Status`,
				RrMatchMatchNo MatchNo,
				floor((length(rtrim(RrMatchArrowString))-1)/{$Params->arrows})+1 as CurrentEnd,
				length(rtrim(RrMatchTiebreak)) as ShootOffShot,
				RrMatchShootFirst&1 as MatchStarter,
				RrMatch".($Params->RrLevMatchMode ? 'SetScore' : 'Score')." as Points
			from RoundRobinMatches
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchTeam=$Team and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($m[0], $m[1])");

		if($r1=safe_fetch($q) and $r2=safe_fetch($q) and $r1->Status==3 and $r2->Status==3) {
			// both ends confirmed so based on the rules lower starts first
			if($r1->Points<$r2->Points) {
				// archer 1 shoots first
				$m=array($r1->MatchNo, $r2->MatchNo);
			} elseif($r2->Points<$r1->Points) {
				// archer 2 shoots first
				$m=array($r2->MatchNo, $r1->MatchNo);
			} elseif($r1->MatchStarter) {
				// Archer1 started the match
				$m=array($r1->MatchNo, $r2->MatchNo);
			} elseif($r2->MatchStarter) {
				// Archer2 started the match
				$m=array($r2->MatchNo, $r1->MatchNo);
			}

			$Winner=($r1->Winner+$r2->Winner);
			if(!$Winner) {
				$JSON['starter']='first['.$Team.']['.$Event.']['.$Level.']['.$Group.']['.$Round.']['.$m[0].']['.$r1->CurrentEnd.']';
				$JSON['tabindex']=$TabIndexOffset + ($r1->CurrentEnd-1)*$Params->arrows*2 + 1;
				if($r1->CurrentEnd==$Params->ends) {
					// we are in the SO so we must add the number of arrows shot
					$JSON['tabindex']+=$r1->ShootOffShot*2+$Params->arrows*2;
				}
				safe_w_sql("update RoundRobinMatches set RrMatchShootFirst=(RrMatchShootFirst | ".pow(2, $r1->CurrentEnd).") where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo={$m[0]}");
				safe_w_sql("update RoundRobinMatches set RrMatchShootFirst=(RrMatchShootFirst & ~".pow(2, $r1->CurrentEnd).") where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo={$m[1]}");
			} else {
				if($r1->Winner) {
					$JSON['winner']=($r1->MatchNo%2 ? 'R' : 'L');
				} elseif($r2->Winner) {
					$JSON['winner']=($r2->MatchNo%2 ? 'R' : 'L');
				}
			}
		}

		$JSON['error']=0;

		// runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event , "Team"=>$Team, "MatchNo"=>min($m), "TourId"=>$_SESSION['TourId']));
		break;
	case 'setShootingFirst':
		require_once('./Lib.php');
		$TabIndexOffset=100;
		$TabIndex=100;

		$End=($_REQUEST['end']??0);
		$IsSO=($_REQUEST['so']??0);

		$Params=getRobinArrowsParams($Team, $Event, $Level);
		$m=array($Match, ($Match%2) ? $Match-1 : $Match+1);

		$Sql1="update RoundRobinMatches set RrMatchShootFirst=(RrMatchShootFirst | ".pow(2, $End)."), RrMatchDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "  
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo={$m[0]}";
		$Sql2="update RoundRobinMatches set RrMatchShootFirst=(RrMatchShootFirst & ~".pow(2, $End)."), RrMatchDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "  
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo={$m[1]}";
		if($End==$Params->ends) {
			// Setting SO
			for($i=0; $i<3; $i++) {
				// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
				for($j=0; $j < $Params->so; $j++) {
					$tabIndex=$TabIndexOffset + $Params->ends*$Params->arrows*2 + $i*$Params->so*2 + $j + 1;

					$JSON['t'][]=array(
						'id' => 'Arrow['.$m[0].']['.$Team.']['.$i.']['.$j.']',
						'val' => $tabIndex,
					);
					$JSON['t'][]=array(
						'id' => 'Arrow['.$m[1].']['.$Team.']['.$i.']['.$j.']',
						'val' => $tabIndex+1,
					);
				}
			}
		} else {
			// setting regular arrows
			for($i=$End; $i<$Params->ends; $i++) {
				// alternate teams each component shoots 1 arrow, so we iterate for as many arrows per archer!
				for($j=0; $j < $Params->arrows; $j++) {
					$tabIndex=$TabIndexOffset + $i*$Params->arrows*2 + $j*2 + 1;

					$JSON['t'][]=array(
						'id' => 'Arrow['.$m[0].'][0]['.$i.']['.$j.']',
						'val' => $tabIndex,
					);
					$JSON['t'][]=array(
						'id' => 'Arrow['.$m[1].'][0]['.$i.']['.$j.']',
						'val' => $tabIndex+1,
					);
				}
			}
		}
		// if($Start=='y') {
		// } else {
		// 	$Sql1="update Finals set FinShootFirst=FinShootFirst & ~".pow(2, $End)." where FinTournament={$_SESSION['TourId']} and FinEvent='$Event' and FinMatchNo=$Matchno";
		// 	if($Team) $Sql1="update TeamFinals set TfShootFirst=TfShootFirst & ~".pow(2, $End)." where TfTournament={$_SESSION['TourId']} and TfEvent='$Event' and TfMatchNo=$Matchno";
		// }
		safe_w_sql($Sql1);
		if($Sql2) {
			safe_w_sql($Sql2);
		}

		$JSON['error']=0;
		break;
	case 'confirmMatch':
		$JSON=array('error'=>0, 'winner'=>'');

		$Error=1;
		$Out='';

		$m=array($Match, ($Match%2) ? $Match-1 : $Match+1);

		// updates the confirmation of the match
		safe_w_sql("update RoundRobinMatches set RrMatchStatus=1, RrMatchConfirmed=1, RrMatchDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " 
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($m[0],$m[1])");

		$q=safe_r_sql("select RrMatchWinLose Winner from RoundRobinMatches where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='$Event' and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($m[0],$m[1]) order by RrMatchMatchNo");

		$Winner='';
		$Loser='';
		if($r1=safe_fetch($q) and $r2=safe_fetch($q) and $r1->Winner+$r2->Winner) {
			$JSON['winner']=$r1->Winner ? 'L' : 'R';
		}

		$JSON['error']=0;

		// runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>min($m) ,"TourId"=>$_SESSION['TourId']));
		// runJack("MatchFinished", $_SESSION['TourId'], array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>min($m) ,"TourId"=>$_SESSION['TourId']));

		JsonOut($JSON);
		break;
}

JsonOut($JSON);
