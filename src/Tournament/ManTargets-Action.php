<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Partecipants/Fun_Targets.php');
require_once('Common/Lib/ArrTargets.inc.php');

$JSON=array('error'=>1, 'warning'=>'', 'msg'=>'');

if(!hasACL(AclCompetition, AclReadWrite) or !CheckTourSession() or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

$Advanced = (ProgramRelease!='FITARCO' AND ProgramRelease!='STABLE');

$AllowedNullTargets=array(
	6,//TrgField
	8,//Trg3DComplete
	11,//TrgHunterNor
	12,//TrgForestSwe
	18,//TrgNfaa3D
	22,//TrgNfaaAnimal
    23,//Trg3DRedding
	);

switch($_REQUEST['act']) {
	case 'list':
		// do nothing, just fills in the data
		break;
	case 'new':
		if(IsBlocked(BIT_BLOCK_TOURDATA) or empty($_REQUEST['TfName']) or (empty($_REQUEST['cl']) and empty($_REQUEST['RegExp'])) or (!$Advanced and !empty($_REQUEST['RegExp']))) {
			JsonOut($JSON);
		}

		$RegExp=(empty($_REQUEST['RegExp']) ? '' : $_REQUEST['RegExp']);
		$cl=((empty($_REQUEST['cl']) or $RegExp) ? '' : $_REQUEST['cl']);
		$TfName=$_REQUEST['TfName'];

		// check if we already have the same name or selector
        // the selector uniqueness applies only if this is a default target!
        $filter='';
        if(!empty($_REQUEST['isDefault'])) {
            if($cl) {
                $filter="or TfClasses=".StrSafe_DB($cl);
            } else {
                $filter="or TfRegExp=".StrSafe_DB($RegExp);
            }
        }
		$q=safe_r_sql("select TfId from TargetFaces where TfTournament={$_SESSION['TourId']} and (TfName=".StrSafe_DB($TfName)." {$filter})");
		if(safe_num_rows($q)) {
			JsonOut($JSON);
		}

		$targets=array();
		foreach($_REQUEST['tdface'] as $dist=>$face) {
			if(!$face or (empty($_REQUEST['tddiam'][$dist]) and !in_array($face, $AllowedNullTargets))) {
				JsonOut($JSON);
			}
			$targets[$face]='';
		}

		// check if the rule hits one or more div/cl
		$select = "SELECT CONCAT(trim(DivId),trim(ClId)) as Ev
			FROM Divisions 
		    INNER JOIN Classes ON DivTournament=ClTournament and (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
		    WHERE
		        CONCAT(trim(DivId),trim(ClId)) " . ($RegExp ? "RLIKE " . StrSafe_DB($RegExp) : "LIKE " . StrSafe_DB($cl)) . " 
		        AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) .  " ";
		$rs=safe_r_sql($select);

		if(!safe_num_rows($rs)) {
			JsonOut($JSON);
		}

		$TfId=1;
		$q=safe_r_sql("select max(TfId) MaxId from TargetFaces where TfTournament={$_SESSION['TourId']}");
		if($r=safe_fetch($q)) {
			$TfId = $r->MaxId + 1;
		}

		// get the name of the targets involved
		ksort($targets);
		$q=safe_r_sql("select TarId, TarDescr from Targets where TarId in (".implode(',', array_keys($targets)).")");
		while($r=safe_fetch($q)) {
			$targets[$r->TarId] = get_text($r->TarDescr);
		}


		$insert = "Insert ignore INTO TargetFaces set "
			. "TfTournament={$_SESSION['TourId']}"
			. ", TfId=$TfId"
			. ", TfDefault=" . ($_REQUEST['isDefault']?'1':'0')
			. ", TfClasses=" . StrSafe_DB($RegExp ? '' : $cl)
			. ", TfRegExp=" . StrSafe_DB($RegExp)
			. ", TfName=" . StrSafe_DB($_REQUEST['TfName']);
		foreach($_REQUEST['tdface'] as $dist => $face) {
			$insert.= ", TfT$dist = " . intval($face);
			$insert.= ", TfW$dist = " . intval($_REQUEST['tddiam'][$dist] );
		}

		$rs=safe_w_sql($insert);
		break;
	case 'update':
		if(IsBlocked(BIT_BLOCK_TOURDATA) or empty($_REQUEST['row'])) {
			JsonOut($JSON);
		}

		$Row=intval($_REQUEST['row']);
		// check that the targetface we are about to update exists
		$q=safe_r_sql("select TargetFaces.*, ToNumDist, ToCategory&8 as Is3D from TargetFaces inner join Tournament on ToId=TfTournament where TfTournament={$_SESSION['TourId']} and TfId=$Row");
		$TOUR=safe_fetch($q);
		if(safe_num_rows($q)!=1) {
			JsonOut($JSON);
		}

		$SQL=[];
		$errors=[];
		foreach($_REQUEST as $tmp=>$Value) {
			$tmp=explode('-', $tmp);
			$Field=$tmp[0];
			$Dist=intval($tmp[1]??0);
			switch($Field) {
				case 'act':
				case 'row':
					// intercepts the unused requests
					break;
				case 'name':
					if(empty($Value)) {
						$errors[]=get_text('NameNotEmpty', 'Errors');
					}
					$SQL[]="TfName=".StrSafe_DB($Value);
					break;
				case 'filter':
					if(!$Value and empty($TOUR->TfRegExp) and empty($_REQUEST['regexp'])) {
						$errors[]='<div>'.get_text('FilterNotEmpty', 'Errors').'</div>'.nl2br(get_text('SqlJolly', 'Errors'));
						if($Advanced) {
							$errors[]='<div>'.get_text('RegExp', 'Errors', '<a href="https://dev.mysql.com/doc/refman/en/regexp.html">Mysql Regular Expressions</a>').'</div>';
						}
					}
					$SQL[]="TfClasses=".StrSafe_DB($Value);
					break;
				case 'regexp':
					if(!$Advanced) {
						$errors[]=get_text('NoPrivilege', 'Errors');
					}
					if(!$Value and !$TOUR->TfClasses and empty($_REQUEST['filter'])) {
						$errors[]='<div>'.get_text('FilterNotEmpty', 'Errors').'</div>'.nl2br(get_text('SqlJolly', 'Errors'));
						if($Advanced) {
							$errors[].='<div>'.get_text('RegExp', 'Errors', '<a href="https://dev.mysql.com/doc/refman/en/regexp.html">Mysql Regular Expressions</a>').'</div>';
						}
					}
					$SQL[]="TfRegExp=".StrSafe_DB($Value);
					break;
				case 'target':
					$Value=intval($Value);
					// check the target type exists and distance is in the range
					$q=safe_r_sql("select * from Targets where TarId=$Value");
					if(safe_num_rows($q)!=1 or $Dist<1 or $Dist>$TOUR->ToNumDist) {
						$errors[]=get_text('IllegalTarget', 'Errors');
					}
					$SQL[]="TfT{$Dist}=$Value";
					if(!in_array($Value, $AllowedNullTargets) and !$TOUR->{'TfW'.$Dist}) {
						$JSON['warning']='[name="diameter-'.$Dist.'"]';
					}
					break;
				case 'diameter':
					$Value=intval($Value);
					// check distance is in the range
					if($Dist<1 or $Dist>$TOUR->ToNumDist) {
						$errors[]=get_text('DistanceOutRange', 'Errors');
					}
					if(!$Value and !in_array($TOUR->{'TfT'.$Dist}, $AllowedNullTargets) and !$TOUR->Is3D) {
						$errors[]=get_text('DiameterMandatory', 'Errors');
					}
					$SQL[]="TfW{$Dist}=$Value";
					break;
				case 'default':
					$Value=intval($Value)?1:0;
					$SQL[]="TfDefault=$Value";
					break;
				case 'golds':
					$SQL[]="TfGolds=".StrSafe_DB($Value);
					break;
				case 'xnine':
					$SQL[]="TfXNine=".StrSafe_DB($Value);
					break;
				case 'goldschars':
					// defaults to the first target defined!
					$Value=strtoupper(str_replace(' ','',$Value));
					if($Dist) {
						$Chars=getLettersFromPrintList($Value, $TOUR->{'TfT'.$Dist});
						if(implode(',', DecodeFromString($Chars, false, true))!=$Value) {
							$errors[]=get_text('IllegalTargetChars','Errors');
						}
						$SQL[]="TfGoldsChars{$Dist}=".StrSafe_DB($Chars);
					} else {
						$Chars=[];
						for($i=1;$i<=8;$i++) {
							if(!$TOUR->{'TfT'.$i}) {
								continue;
							}
							$Chars=array_unique($Chars+preg_split('//', getLettersFromPrintList($Value, $TOUR->{'TfT'.$i}), -1, PREG_SPLIT_NO_EMPTY));
						}
						$Chars=implode('', $Chars);
						if(implode(',', DecodeFromString($Chars, false, true))!=$Value) {
							$errors[]=get_text('IllegalTargetChars','Errors');
						}
						$SQL[]="TfGoldsChars=".StrSafe_DB($Chars);
					}
					break;
				case 'xninechars':
					// defaults to the first target defined!
					$Value=strtoupper(str_replace(' ','',$Value));
					if($Dist) {
						$Chars=getLettersFromPrintList($Value, $TOUR->{'TfT'.$Dist});
						if(implode(',', DecodeFromString($Chars, false, true))!=$Value) {
							$errors[]=get_text('IllegalTargetChars','Errors');
						}
						$SQL[]="TfXNineChars{$Dist}=".StrSafe_DB($Chars);
					} else {
						$Chars=[];
						for($i=1;$i<=8;$i++) {
							if(!$TOUR->{'TfT'.$i}) {
								continue;
							}
							$Chars=array_unique($Chars+preg_split('//', getLettersFromPrintList($Value, $TOUR->{'TfT'.$i}), -1, PREG_SPLIT_NO_EMPTY));
						}
						$Chars=implode('', $Chars);
						if(implode(',', DecodeFromString($Chars, false, true))!=$Value) {
							$errors[]=get_text('IllegalTargetChars','Errors');
						}
						$SQL[]="TfXNineChars=".StrSafe_DB($Chars);
					}
					break;
				default:
					JsonOut($JSON);
			}
		}
		if($errors) {
			$JSON['msg']='<div>'.implode('</div><div>', array_unique($errors)).'</div>';
			JsonOut($JSON);
		}
		safe_w_sql("update TargetFaces set ".implode(',', $SQL)." where TfTournament={$_SESSION['TourId']} and TFId=$Row");
		break;
	case 'delete':
		if(empty($_REQUEST['row'])) {
			JsonOut($JSON);
		}

		safe_w_sql("delete from TargetFaces where TfTournament={$_SESSION['TourId']} and TfId=".intval($_REQUEST['row']));
		break;
	default:
		$JSON['error']=1;
}

$JSON['error']=0;
$JSON['categories']='';
// $JSON['table']='';
$JSON['rows']=[];


$numDist=0;
$colspan=0;
$rsDist='';


$AvDiv=array();
$q=safe_r_sql("select DivId, ClId from Divisions inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1 AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) order by DivViewOrder, ClViewOrder");
while($r=safe_fetch($q)) {
    $AvDiv[$r->DivId][$r->ClId]='<a name="" onclick="document.getElementById(\'TdClasses\').value=\''.$r->DivId.$r->ClId.'\'">'.$r->DivId.$r->ClId.'</a>';
}

$AvTargets=array();
$SelTargets='<option value="">---</option>';
$q=safe_r_sql("select * from Targets order by TarOrder");
while($r=safe_fetch($q)) {
    $AvTargets[$r->TarId]= get_text($r->TarDescr);
    $SelTargets.='<option value="'.$r->TarId.'">'.get_text($r->TarDescr).'</option>';
}

$select = "SELECT ToType,ToNumDist AS TtNumDist, ToGolds, ToXNine, ToGoldsChars, ToXNineChars
    FROM Tournament
    WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";
$rs=safe_r_sql($select);

if ($r=safe_fetch($rs) and $r->TtNumDist) {
    $numDist=$r->TtNumDist;
    $colspan=2+$numDist+$Advanced;

    $select = "SELECT DISTINCT *
        FROM TargetFaces
        WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
    $rsDist=safe_r_sql($select);
}

foreach(($DefinedTargets=getTargets(false)) as $Target=>$divs) {
    foreach($divs as $Div=>$cl) {
        foreach($cl as $Class=>$default) {
            if($default) unset ($AvDiv[$Div][$Class]);
        }
    }
}

foreach($AvDiv as $Div=>$Cl) {
    if($Cl) {
	    $JSON['categories'].='<div>'.implode(', ',$Cl).'</div>';
    }
}

if ($rsDist) {
    $k=0;
    while ($myRow=safe_fetch($rsDist)) {
		$line=[
			'id'=>$myRow->TfId,
			'categories'=>print_targets($myRow->TfId),
			'name'=>get_text($myRow->TfName,'Tournament','',true),
			'filter'=>$myRow->TfClasses,
			'targets'=>[],
			'default'=>$myRow->TfDefault,
			'golds'=>$myRow->TfGolds,
			'xnine'=>$myRow->TfXNine,
			'goldschars'=>implode(',', array_unique(DecodeFromString($myRow->TfGoldsChars, false, true))),
			'xninechars'=>implode(',', array_unique(DecodeFromString($myRow->TfXNineChars, false, true))),
		];
		if($Advanced) {
			$line['regexp']=$myRow->TfRegExp;
		}
	    for ($i=1;$i<=$numDist;++$i) {
			$line['targets'][]=[
				'type'=>$myRow->{'TfT' . $i},
				'diam'=>$myRow->{'TfW' . $i},
				'warning'=>(in_array($myRow->{'TfT' . $i}, $AllowedNullTargets) or $myRow->{'TfW' . $i}) ? '' : 'alert-warning',
				'goldschars'=>implode(',', array_unique(DecodeFromString($myRow->{'TfGoldsChars'.$i}, false, true))),
				'xninechars'=>implode(',', array_unique(DecodeFromString($myRow->{'TfXNineChars'.$i}, false, true))),
			];
	    }
		$JSON['rows'][]=$line;

		// $JSON['table'].= '<tr ref="'.$myRow->TfId.'">';
        // $JSON['table'].= '<td>'.print_targets($myRow->TfId).'</td>';
        // $JSON['table'].= '<td class="Center">'.get_text($myRow->TfName,'Tournament','',true).'</td>';
        // $JSON['table'].= '<td class="Center" style="width:20%;">'.$myRow->TfClasses.'</td>';
        // $JSON['table'].= '<td class="Center" style="width:20%;">'.$myRow->TfRegExp.'</td>';
        // for ($i=1;$i<=$numDist;++$i) {
        //     $JSON['table'].= '<td class="Center" ref="'.$i.'">
		// 		<select onchange="updateTarget(this)" name="target">'.preg_replace('/(value="'.($myRow->{'TfT' . $i} ? $myRow->{'TfT' . $i} : '').'")/','$1 selected="selected"',$SelTargets).'</select>
		// 		<br/>ø (cm) <input name="diameter" value="'.($myRow->{'TfW' . $i}) . '" onchange="updateTarget(this)" size="3" maxlength="3">';
        // }
        // $JSON['table'].= '<td class="Center">'.($myRow->TfDefault?get_text('Yes'):'').'</td>';
        // $JSON['table'].= '<td class="Center"><i class="far fa-2x fa-trash-can text-danger" onclick="deleteTarget(this)"></i></td>';
        // $JSON['table'].= '</tr>';
        ++$k;
    }
}

JsonOut($JSON);

function print_targets($TfId) {
	global $DefinedTargets;
	$ret='';
	if(empty($DefinedTargets[$TfId])) return '&nbsp;';
	foreach($DefinedTargets[$TfId] as $Div=>$Cl) {
		$ret.= $Div . ':&nbsp;';
		foreach($Cl as $class=>$def) {
			if($def) $class="<b>$class</b>";
			$ret.= $class . '&nbsp;';
		}
		$ret.='<br/>';
	}
	return $ret;
}

