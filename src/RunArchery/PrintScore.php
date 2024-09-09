<?php

global $CFG;
$IncludeJquery=true;
$IncludeFA=true;

require_once(dirname(__DIR__) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Sessions.inc.php');
// require_once('Common/Fun_FormatText.inc.php');
// require_once('Common/Fun_Modules.php');
CheckTourSession(true);
checkACL(AclQualification, AclReadOnly);

// $Select = "SELECT ToCategory, ToId,ToNumDist AS TtNumDist,ToElabTeam AS TtElabTeam FROM Tournament WHERE ToId={$_SESSION['TourId']}";
// $RsTour=safe_r_sql($Select);
// $RowTour=safe_fetch($RsTour);

$JS_SCRIPT=array(
	'<script type="text/javascript" src="./PrintScore.js"></script>',
	);

$PAGE_TITLE=get_text('PrintScore', 'Tournament');

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="2">' . get_text('PrintScore','Tournament')  . '</th></tr>';
echo '<tr><th class="SubTitle" colspan="2">' . get_text('ScorePrintMode','Tournament')  . '</th></tr>';

echo '<tr>';
echo '<td colspan="2" class="Center">';
echo '<div style="display: inline-block;margin:0.5em;"><input name="ScoreHeader" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreTournament','Tournament') . '</div>';
echo '<div style="display: inline-block;margin:0.5em;"><input name="ScoreLogos" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreLogos','Tournament') . '</div>';
echo '<div style="display: inline-block;margin:0.5em;"><input name="ScoreFlags" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreFlags','Tournament') . '</div>';
echo '</td>';
echo '</tr>';

echo '<tr><th class="SubTitle" colspan="2">&nbsp;</th></tr>';

// There are only 2 types of scores: spotter and loop checker
echo '<tr>';
echo '<td colspan="2" class="Center">';
echo '<div class="Button" onclick="printSpotter()">' . get_text('PrintSpotterScore','RunArchery') . '</div>';
echo '<div class="Button" onclick="printLoop()">' . get_text('PrintLoopScore','RunArchery') . '</div>';
echo '<div class="Button" onclick="printDelays()">' . get_text('PrintDelays','RunArchery') . '</div>';
echo '</td>';
echo '</tr>';


echo '</table>';
include('Common/Templates/tail.php');
