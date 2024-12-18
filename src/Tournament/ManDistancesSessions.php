<?php

// DISTANCE INFORMATION MANAGEMENT
// Based on SESSIONS!!!!

$DistInfo=array();
$q=safe_r_sql("select * from DistanceInformation where DiTournament={$_SESSION['TourId']} and DiType='Q'");
while($r=safe_fetch($q)) {
	$DistInfo[$r->DiSession][$r->DiDistance]=array(
		$r->DiEnds,
		$r->DiArrows,
		$r->DiScoringEnds,
		$r->DiScoringOffset,
		$r->DiDay=='0000-00-00' ? '' : $r->DiDay,
		$r->DiStart=='00:00:00' ? '' : substr($r->DiStart, 0, 5),
		$r->DiDuration,
		$r->DiWarmStart=='00:00:00' ? '' : substr($r->DiWarmStart, 0, 5),
		$r->DiWarmDuration,
		$r->DiOptions,
		$r->DiScoringEnds,
		$r->DiScoringOffset,
		);
}

echo '<table class="Tabella">';
$q=safe_r_sql("select ToNumDist, SesOrder, SesName from Session inner join Tournament on SesTournament=ToId where SesTournament={$_SESSION['TourId']} and SesType='Q' order by SesOrder");
while($r=safe_fetch($q)) {
	echo '<tr><th class="Title" colspan="11">'.($r->SesName ? $r->SesName : get_text('Session').': '.$r->SesOrder).'</th></tr>';
	echo '<tr>
		<th>'.get_text('Distance', 'Tournament').'</th>
		<th>'.get_text('Ends', 'Tournament').'</th>
		<th>'.get_text('ArrowsPerEnd', 'Tournament').'</th>
		<th class="d-none advanced">'.get_text('EndsToShoot', 'Tournament').'</th>
		<th class="d-none advanced">'.get_text('EndsOffset', 'Tournament').'</th>
		<th>'.get_text('Date', 'Tournament').'</th>
		<th>'.get_text('WarmUp', 'Tournament').'</th>
		<th>'.get_text('Length', 'Tournament').'</th>
		<th>'.get_text('Time', 'Tournament').'</th>
		<th>'.get_text('Length', 'Tournament').'</th>
		<th>'.get_text('ScheduleNotes', 'Tournament').'</th>
		</tr>';
		foreach(range(1, $r->ToNumDist) as $dist) {
			if(empty($DistInfo[$r->SesOrder][$dist])) $DistInfo[$r->SesOrder][$dist]=array('', '', '', '', '', '', '', '', '', '');
			echo '<tr>
				<th>.'.$dist.'.</th>
				<td class="Center"><input size="4" maxlength="3" type="text" name="end['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][0].'"></td>
				<td class="Center"><input size="4" maxlength="3" type="text" name="arr['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][1].'"></td>
				<td class="Center d-none advanced"><input size="4" maxlength="3" type="text" name="shoot['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][2].'"></td>
				<td class="Center d-none advanced"><input size="4" maxlength="3" type="text" name="offset['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][3].'"></td>
				<td class="Center"><input size="10" maxlength="10" type="date" name="startday['.$r->SesOrder.']['.$dist.']" onblur="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][4].'"></td>
				<td class="Center"><input size="6" maxlength="5" type="time" name="warmtime['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][7].'"></td>
				<td class="Center"><input size="4" maxlength="3" type="text" name="warmduration['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][8].'"></td>
				<td class="Center"><input size="6" maxlength="5" type="time" name="starttime['.$r->SesOrder.']['.$dist.']" onblur="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][5].'"></td>
				<td class="Center"><input size="5" maxlength="3" type="text" name="duration['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][6].'"></td>
				<td class="Center"><input size="70" type="text" name="comment['.$r->SesOrder.']['.$dist.']" onchange="ChangeInfo(this)" value="'.$DistInfo[$r->SesOrder][$dist][9].'"></td>
				</tr>';
		}
	echo '<tr></tr>';
}

echo '<tr><td colspan="11"></td></tr>';
echo '<tr><th colspan="11" style="padding:0.5em"><div class="Button" onclick="$(\'.advanced\').toggleClass(\'d-none\')">Advanced</div></th></tr>';

echo '</table>';
