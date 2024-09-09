<?php
require_once(dirname(__FILE__, 2) .'/config.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

require_once('Common/Lib/CommonLib.php');

$ClDivIndCalc=getModuleParameter('ISK-NG','CalcClDivInd',0, 0, true);
$ClDivTeamCalc=getModuleParameter('ISK-NG','CalcClDivTeam',0, 0, true);
$FinIndCalc=getModuleParameter('ISK-NG','CalcFinInd',0, 0, true);
$FinTeamCalc=getModuleParameter('ISK-NG','CalcFinTeam',0, 0, true);

$PAGE_TITLE=get_text('RankCalcSettings', 'Api');
$IncludeJquery = true;
$IncludeFA = true;
$JS_SCRIPT=array(
	phpVars2js(array(
		'MsgConfirm'=>htmlspecialchars(get_text('MsgAreYouSure')),
        'CalcDivClI'=>$ClDivIndCalc,
        'CalcDivClT'=>$ClDivTeamCalc,
        'CalcFinI'=>$FinIndCalc,
        'CalcFinT'=>$FinTeamCalc,
    )),
	'<script type="text/javascript" src="./RankCalcSettings.js"></script>',
	'<link href="isk.css" rel="stylesheet" type="text/css">',
);


include('Common/Templates/head.php');

echo '<table class="Tabella LiteIndex">';
echo '<tr><th class="Main" colspan="5">' . get_text('RankCalcSettings', 'Api') . '</th></tr>';

echo '<tr><th colspan="5" class="Title">' . get_text('RecalcMode', 'Api') . '</th></tr>';
echo '<tr class="warning"><td colspan="5" class="">'.get_text('AutoImportSettings', 'Help').'</td></tr>';
echo '<tr class="rowHover">'.
        '<th class="Title"></th>'.
        '<th class="Title">' . get_text('RecalcAfterImport', 'Api') . '</th>'.
        '<th class="Title">' . get_text('RecalcFullDist', 'Api') . '</th>'.
        '<th class="Title">' . get_text('RecalcManually', 'Api') . '</th>'.
        '<th class="Title">&nbsp;</th>'.
    '</tr>';
echo '<tr class="rowHover">'.
        '<th class="Left">'.get_text('CalcClDivInd', 'ISK').'</th>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivInd" value="0"'.($ClDivIndCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivInd" value="1"'.($ClDivIndCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivInd" value="2"'.($ClDivIndCalc==2 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="opButton(this)" id="doCalcClDivInd">'.get_text('CalculateNow','ISK').'</div></td>'.
    '</tr>';
echo '<tr class="rowHover">'.
        '<th class="Left">'.get_text('CalcFinInd', 'ISK').'</th>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinInd" value="0"'.($FinIndCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinInd" value="1"'.($FinIndCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinInd" value="2"'.($FinIndCalc==2 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="opButton(this)" id="doCalcFinInd">'.get_text('CalculateNow','ISK').'</div></td>'.
    '</tr>';
echo '<tr class"divider"><td colspan="5"></td></tr>';
echo '<tr class="rowHover">'.
        '<th class="Left">'.get_text('CalcClDivTeam', 'ISK').'</th>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivTeam" value="0"'.($ClDivTeamCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivTeam" value="1"'.($ClDivTeamCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcClDivTeam" value="2"'.($ClDivTeamCalc==2 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="opButton(this)" id="doCalcClDivTeam">'.get_text('CalculateNow','ISK').'</div></td>'.
    '</tr>';
echo '<tr class="rowHover">'.
        '<th class="Left">'.get_text('CalcFinTeam', 'ISK').'</th>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinTeam" value="0"'.($FinTeamCalc==0 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinTeam" value="1"'.($FinTeamCalc==1 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><input type="radio" onclick="LiteAction(this)" name="CalcFinTeam" value="2"'.($FinTeamCalc==2 ? ' checked="checked"' : '').'></td>'.
        '<td class="Center"><div class="Button" onclick="opButton(this)" id="doCalcFinTeam">'.get_text('CalculateNow','ISK').'</div></td>'.
    '</tr>';
echo '<tr class"divider"><td colspan="5"></td></tr>';
echo '<tr><th class="Title" colspan="5">'.get_text('StatusTemporaryArrows', 'Api').'</th></tr>'.
    '<tr>'.
        '<th class="Left w-20">'.get_text('TempArrowsQ', 'Api').'</th>'.
        '<td rowspan="2" class="Center"><div class="Button" onclick="updateTemporaryTableStatus()" id="cmdRefreshStatus">'.get_text('DataRefresh','Api').'</div></td>'.
        '<td class="tempArrows w-20"><span id="TempArrowsQ"></span><i id="taIcoQ" class="fas ml-2 fa-lg"></i></td>'.
        '<td class="Center w-20"><div class="Button btnQual d-none" onclick="opConfirmButton(this)" id="ImportQualNow">'.get_text('TempArrowsImport','Api').'</div></td>'.
        '<td class="Center w-20"><div class="Button btnQual d-none" onclick="opConfirmButton(this)" id="DeleteDataQual">'.get_text('TempArrowsDelete','Api').'</div></td>'.
    '</tr>'.
    '<tr>'.
        '<th class="Left">'.get_text('TempArrowsM', 'Api').'</th>'.
        '<td class="tempArrows"><span id="TempArrowsM"></span><i id="taIcoM" class="fas ml-2 fa-lg"></i></td>'.
        '<td class="Center"><div class="Button btnMatch d-none" onclick="opConfirmButton(this)" id="ImportMatchNow">'.get_text('TempArrowsImport','Api').'</div></td>'.
        '<td class="Center"><div class="Button btnMatch d-none" onclick="opConfirmButton(this)" id="DeleteDataMatch">'.get_text('TempArrowsDelete','Api').'</div></td>'.
    '</tr>';
echo '</table>';

include('Common/Templates/tail.php');
