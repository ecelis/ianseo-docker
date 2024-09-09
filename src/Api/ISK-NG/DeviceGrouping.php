<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

// get targets from qualification and matches
// Field and 3D CAN NOT SCORE TOGETHER!!!

$IskGroup=[];
if($_SESSION['UseApi']==ISK_NG_LITE_CODE) {
    $IskGroup[] = 0;
} else {
    $q = safe_r_sql("select IskDvGroup from IskDevices where IskDvTournament={$_SESSION['TourId']} group by IskDvGroup order by IskDvGroup");
    while ($r = safe_fetch($q)) {
        $IskGroup[] = $r->IskDvGroup;
    }
}

$SQL="(select min(ifnull(SesFirstTarget, QuTarget)) as TargetMin, max(ifnull(SesTar4Session+SesFirstTarget-1, QuTarget)) as TargetMax
	from Qualifications
	inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
	left join Session on SesOrder=QuSession and SesTournament={$_SESSION['TourId']}
	where QuTarget>0 and QuSession>0)
	union
	(select min(FsTarget+0) as TargetMin, max(FsTarget+0) as TargetMax
	from FinSchedule
	where FsTournament={$_SESSION['TourId']} and FsTarget+0>0)";

$Min=100000;
$Max=0;
$q=safe_r_sql($SQL);
while($r=safe_fetch($q)) {
	if(is_null($r->TargetMin) and is_null($r->TargetMax)) {
		continue;
	}
	if($Min==-1 or ($r->TargetMin>0 and $Min > $r->TargetMin)) $Min=$r->TargetMin;
	if($Max < $r->TargetMax) $Max=$r->TargetMax;
}
if($Min==-1) {
	$Min=0;
}


$Range=range($Min, $Max);
$colSpan = min((count($Range) % 32 === 0 ? 32 : 30),count($Range));

$PAGE_TITLE=get_text('API-DeviceGrouping', 'Api');
$IncludeJquery = true;
$IncludeFA = true;
$JS_SCRIPT=array(
    phpVars2js(
        array(
            'colSpan' => $colSpan,
            'ConfirmDeleteRow'=> get_text('API-ConfirmDeleteRow', 'Api')
        )
    ),
    '<link href="isk.css" rel="stylesheet" type="text/css">',
    '<script type="text/javascript" src="DeviceGrouping.js"></script>',
);
$Grouping=getModuleParameter('ISK-NG', 'Grouping', []);

include('Common/Templates/head.php');

echo '<table id="Groups" class="Tabella">';
echo '<tr><th class="Title" colspan="'.($colSpan+2).'">'.$PAGE_TITLE.'</th></tr>';

foreach($IskGroup as $Group) {
    echo '<tr class="spacingGroup" colspan="'.($colSpan+2).'"></tr>';
	echo '<tbody ref="'.$Group.'">';
	echo '<tr>
		<th class="Title" rowspan="'. (intdiv(count($Range),$colSpan+1)+2) .'" colspan="2"><div class="deviceGroup">'.get_text('API-Group', 'Api').($_SESSION['UseApi']!=ISK_NG_LITE_CODE ? ' '.chr(65+$Group) : '').'</div></th>
		<th class="Title" colspan="'.$colSpan.'">'.get_text('API-Targets', 'Api').'</th>
		</tr>';

	$cnt=1;
	foreach($Range as $Target) {
	    if($cnt % $colSpan+1 === 0) {
	        echo '<tr>';
	    }
	    echo '<th class="gTgt Title" target="'.$Target.'" onclick="toggleTgt(this,\''.$Target.'\')">'.($Target).'</th>';
	    if($cnt++ % $colSpan === 0) {
	        echo '</tr>';
	    }
	}
    echo '<tr>
		<th class="Title" colspan="2">'.get_text('API-EnableDeviceGrouping', 'Api').'</th>
		<td colspan="'.$colSpan.'">
			<select onchange="ActivateDeviceGrouping(this)">
				<option value="0" '.(($Grouping[$Group]??0)==0 ? 'selected="selected"' : '').'>'.get_text('No').'</option>
				<option value="1" '.(($Grouping[$Group]??0)==1 ? 'selected="selected"' : '').'>'.get_text('Yes').'</option>
			</select>
		</td>
		</tr>';
	echo '<tr>';
	echo '<th rowspan="'. (intdiv(count($Range),$colSpan+1)+1) .'"><i title="'.get_text('CmdAdd', 'Tournament').'" class="fa fa-2x fa-save"  onclick="SaveGroup(this)"></i></th>';
	echo '<th rowspan="'. (intdiv(count($Range),$colSpan+1)+1) .'"><input type="text" name="grpId"></th>';
	$cnt=1;
	foreach($Range as $Target) {
	    echo '<td class="gTgt Center" target="'.$Target.'"><input type="hidden" ref="tgtChk_'.$Target.'" value="0"><i class="far fa-2x fa-circle" ref="lblChk_'.$Target.'" onclick="toggleTgt(this,\''.$Target.'\')"></i></td>';
	    if($cnt++ % $colSpan === 0) {
	        echo '</tr><tr>';
	    }
	}
	echo '</tr>';
	echo '<tr class="divider"><td colspan="'.($colSpan+2).'"></td></tr>';
	echo '</tbody>';
	echo '<tbody class="bGroups" ref="'.$Group.'"></tbody>';
	echo '<tr class="divider"><td colspan="'.($colSpan+2).'"></td></tr>';
	echo '<tr ref="'.$Group.'"><th colspan="2"><input type="button" onclick="autoGroupTargets(this)" value="'.get_text('API-AutoGroup', 'Api').'"></th><td colspan="'.$colSpan.'">'.get_text('GroupBy', 'Tournament').'&nbsp;<input type="number" name="autoGrNo" min="0" max="'.$Max.'">&nbsp;&nbsp;&nbsp;&nbsp;'.get_text('From', 'Tournament').'&nbsp;<input type="number" name="autoGrFrom" min="'.$Min.'" max="'.$Max.'">&nbsp;&nbsp;&nbsp;&nbsp;'.get_text('To', 'Tournament').'&nbsp;<input type="number" name="autoGrTo" min="'.$Min.'" max="'.$Max.'"></td></tr>';
	echo '</tbody>';
}
echo '</table>';

include('Common/Templates/tail.php');

