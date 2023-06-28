<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error' => 1, 'msg'=>get_text('NoPrivilege', 'Errors'), 'rows'=>[]);

$Type=intval($_REQUEST['type'] ?? 0);
$NumDist=intval($_REQUEST['numDist'] ?? 0);
$Classes=($_REQUEST['cl']??'');

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act']) or !$Type or !$NumDist or IsBlocked(BIT_BLOCK_TOURDATA)) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'list':
		// do nothing, see bottom of file
		break;
	case 'delete':
		if(!$Classes) {
			JsonOut($JSON);
		}
		safe_w_sql("DELETE FROM TournamentDistances
			WHERE TdTournament={$_SESSION['TourId']} AND TdClasses=" . StrSafe_DB($Classes) . " AND TdType=$Type");
		break;
	case 'save':
	case 'update':
		if (!$Classes or empty($_REQUEST['td']) or !is_array($_REQUEST['td'])) {
			$JSON['msg']=get_text('FilterNotEmpty','Errors');
			JsonOut($JSON);
		}

		// check if there are Divs and class that obey the rule
		$select = "SELECT DivId, ClId, TdClasses is not null as Conflicting
			FROM Divisions 
		    INNER JOIN Classes ON ClTournament=DivTournament AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed)) AND ClAthlete=DivAthlete 
			left join TournamentDistances on TdTournament=DivTournament and TdType=$Type and CONCAT(DivId,ClId) LIKE TdClasses ".(empty($_REQUEST['oldCl']) ? '' : ' and TdClasses!='.StrSafe_DB($_REQUEST['oldCl']))."
			WHERE CONCAT(DivId,ClId) LIKE " . StrSafe_DB($Classes) . " 
				AND DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
				AND DivAthlete=1 
			ORDER BY DivViewOrder, ClViewOrder";
		$rs = safe_r_sql($select);

		if (safe_num_rows($rs) == 0) {
			// rule does not select any category!
			$JSON['msg']=get_text('ManDistance-EmptySelection', 'Errors');
			JsonOut($JSON);
		}

		$Conflicting=[];
		while ($row = safe_fetch($rs)) {
			if($row->Conflicting) {
				// this category is already matched by another rule!
				$Conflicting[]=$row->DivId.$row->ClId;
			}
		}

		if($Conflicting) {
			$JSON['msg']=get_text('ConflictingCategories', 'Errors', implode(', ', $Conflicting));
			JsonOut($JSON);
		}

		// check if the new classes is not already there!
		if($_REQUEST['act']=='update' and $_REQUEST['oldCl']!=$Classes) {
			$q=safe_r_sql("select TdClasses from TournamentDistances where TdTournament={$_SESSION['TourId']} and TdType=$Type and TdClasses=".StrSafe_DB($Classes));
			if(safe_num_rows($q)) {
				$JSON['msg']=get_text('FilterAlreadyExists', 'Errors');
				JsonOut($JSON);
			}
		}

		$SQL=[];
		foreach($_REQUEST['td'] as $Dist=>$Name) {
			if(($Dist=intval($Dist)) and $Dist>0 and $Dist<=$NumDist) {
				$SQL[]="Td{$Dist}=".StrSafe_DB($Name ?: ".{$Dist}.");
			}
		}

		if($_REQUEST['act']=='update') {
			safe_w_sql("update ignore TournamentDistances 
				set  TdClasses=".StrSafe_DB($Classes).", ".implode(', ', $SQL)." 
				where TdTournament={$_SESSION['TourId']} and TdType=$Type and TdClasses=".StrSafe_DB($_REQUEST['oldCl']));
		} else {
			safe_w_sql("insert into TournamentDistances 
				set TdTournament={$_SESSION['TourId']}, TdType=$Type, TdClasses=".StrSafe_DB($Classes).", ".implode(', ', $SQL)." 
				on duplicate key update ".implode(', ', $SQL));
		}

		break;
	case 'update':
		JsonOut($JSON);
		if(empty($_REQUEST['d']) or empty($_REQUEST['r']) or empty($_REQUEST['val']) or !($Dist=intval($_REQUEST['d']))) {
		}
		safe_w_sql("update TournamentDistances 
    		inner join Tournament on TdTournament=ToId and TdType=ToType 
			set Td{$Dist}=".StrSafe_DB($_REQUEST['val'])."
			where TdClasses=".StrSafe_DB($_REQUEST['r'])." and TdTournament={$_SESSION['TourId']}");
		$JSON['error']=0;
		break;
	default:
		JsonOut($JSON);
}

$JSON['error']=0;

$AvDiv=array();
$q=safe_r_sql("select DivId, ClId 
	from Divisions 
    inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
	where DivTournament='{$_SESSION['TourId']}' and DivAthlete=1
	order by DivViewOrder, ClViewOrder");
while($r=safe_fetch($q)) {
	$AvDiv[$r->DivId][$r->ClId]=$r->ClId;
}

foreach(($DefinedDistances=getDistances(false)) as $Dist=>$divs) {
	foreach($divs as $Div=>$cl) {
		foreach($cl as $Class=>$default) {
			unset ($AvDiv[$Div][$Class]);
		}
	}
}
$JSON['categories']='';
$JSON['NoMoreClasses']=true;
foreach($AvDiv as $Div=>$Cl) {
	if($Cl) {
		$JSON['categories'].='<div><b>'.$Div.':</b> '.implode(', ',$Cl).'</div>';
		$JSON['NoMoreClasses']=false;
	}
}

// gets all the rows and the categories left to filter
$select = "SELECT DISTINCT t.*
	FROM Divisions 
    inner join Classes on ClTournament=DivTournament and ClAthlete=DivAthlete AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
	INNER JOIN TournamentDistances AS t ON TdType=$Type and TdTournament=DivTournament AND CONCAT(TRIM(DivId),TRIM(ClId)) LIKE TdClasses
	WHERE DivTournament={$_SESSION['TourId']}";

$rsDist=safe_r_sql($select);
$ToSave=array();
$k=0;
while ($myRow=safe_fetch($rsDist)) {
	$row=[
		'id'=>$myRow->TdClasses,
		'categories'=>print_distances($DefinedDistances[$myRow->TdClasses]??[]),
		'cl'=>$myRow->TdClasses,
		'td'=>[],
	];
	foreach(range(1, $NumDist) as $i) {
		$row['td'][]=[
			'id'=>$i,
			'val'=>$myRow->{'Td' . $i},
		];
	}
	$JSON['rows'][]=$row;
	$ToSave[]=StrSafe_DB($myRow->TdClasses);
}

// removes the unfit matches...
$SQL="delete from TournamentDistances where TdTournament={$_SESSION['TourId']}";
if($ToSave) {
	$SQL.=" and (TdType!=$Type or TdClasses not in (".implode(',', $ToSave)."))";
}
safe_w_sql($SQL);

JsonOut($JSON);

function getDistances($ByDist=true) {
	$ar=array();

	$MySql="select DivId, ClId, TdClasses
		from Divisions
		inner join Classes on DivTournament=ClTournament and DivAthlete=ClAthlete AND (ClDivisionsAllowed='' or find_in_set(DivId, ClDivisionsAllowed))
		inner join TournamentDistances on DivTournament=TdTournament and concat(trim(DivId),trim(ClId)) like TdClasses ".($ByDist ? "and TdClasses='$ByDist'" : '')."
		WHERE DivTournament={$_SESSION['TourId']} AND DivAthlete='1'
		order by DivViewOrder, ClViewOrder";

	$q=safe_r_sql($MySql);
	if($ByDist) {
		while($r=safe_fetch($q)) {
			$ar[]=$r->DivId.$r->ClId;
		}
	} else {
		while($r=safe_fetch($q)) {
			$ar[$r->TdClasses][$r->DivId][$r->ClId] = $r->ClId;
		}
	}

	return $ar;
}

function print_distances($DefinedDistances) {
	$ret='';
	foreach($DefinedDistances as $Div=>$Cl) {
		$ret.= '<div><b>'.$Div . ':</b> '.implode(', ', $Cl).'</div>';
	}
	return $ret;
}
