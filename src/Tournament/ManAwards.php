<?php

define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');

if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACl(AclCompetition,AclReadWrite);

if(!empty($_REQUEST['delAwarder'])) {
	delModuleParameter('Awards','Aw-Awarder-1-'. intval($_REQUEST['delAwarder']));
	delModuleParameter('Awards','Aw-Awarder-2-'. intval($_REQUEST['delAwarder']));
	cd_redirect(basename(__FILE__).go_get('delAwarder', '', true));
}

if(!empty($_REQUEST['delAward'])) {
	delModuleParameter('Awards','Aw-Award-1-'. intval($_REQUEST['delAward']));
	delModuleParameter('Awards','Aw-Award-2-'. intval($_REQUEST['delAward']));
	cd_redirect(basename(__FILE__).go_get('delAward', '', true));
}

$evArray= array(
	"00"=>get_text('IndClEvent', 'Tournament'),
	"10"=>get_text('IndFinEvent', 'Tournament'),
	"01"=>get_text('TeamClEvent', 'Tournament'),
	"11"=>get_text('TeamFinEvent', 'Tournament')
);
$editRow = null;


if(!empty($_REQUEST['Prize'])) {
	$Awarders=array();
	list($Event, $FinEvent, $Team)=explode('|', $_REQUEST['id']);

	foreach($_REQUEST['Prize'] as $k => $v) {
		if($v) $Awarders[$v]=$_REQUEST['Person'][$k];
	}

	$q=safe_w_sql("update Awards
			set AwAwarderGrouping=".StrSafe_DB(serialize($Awarders))."
			where AwTournament={$_SESSION['TourId']} and AwEvent=".StrSafe_DB($Event)." and AwFinEvent=".intval($FinEvent)." and AwTeam=".intval($Team));

	header('Location: ' . $_SERVER['PHP_SELF']);
	exit;
}

if (isset($_REQUEST['Command'])) {
	if ($_REQUEST['Command']=='ADD'){
		foreach($_REQUEST["addField"] as $v) {
			if(preg_match('/^[A-Z0-9]+\|[0-1]{1}\|[0-1]{1}$/sim',$v)) {
				list($Event,$isFinal,$isTeam) = explode('|',$v);
				if($Event=='Custom') {
					$Num=1;
					$q=safe_r_sql("select AwEvent from Awards where AwTournament={$_SESSION['TourId']} and AwEvent like 'Custom-%' order by AwEvent desc");
					if($r=safe_fetch($q)) {
						$Num=(substr($r->AwEvent, 7)+1);
					}
					$Event.='-'.$Num;
					$ev=getModuleParameter('Awards','Aw-CustomEvent-1-'. $Num);
					if(empty($ev)) {
						$UseLang=($_SESSION['TourPrintLang'] ? $_SESSION['TourPrintLang'] : SelectLanguage());
						setModuleParameter('Awards','Aw-CustomEvent-1-'. $Num, get_text('PrecisionPrizeEvent', 'Awards', '$a', '', '', $UseLang));
						setModuleParameter('Awards','Aw-CustomPrize-1-'. $Num, get_text('PrecPrizePresentation', 'Awards', '$a', '', '', $UseLang));
						if($tmp=getModuleParameter('Awards', 'SecondLanguageCode')) {
							setModuleParameter('Awards','Aw-CustomEvent-2-'. $Num, get_text('PrecisionPrizeEvent', 'Awards', '$a', '', '', $tmp));
							setModuleParameter('Awards','Aw-CustomPrize-2-'. $Num, get_text('PrecPrizePresentation', 'Awards', '$a', '', '', $tmp));
						}
					}
				}
				$Insert	= "INSERT IGNORE INTO Awards set
						AwTournament=" . StrSafe_DB($_SESSION['TourId']) . ",
						AwEvent=" . StrSafe_DB($Event) . ",
						AwFinEvent=" . StrSafe_DB($isFinal) . ",
						AwTeam=" . StrSafe_DB($isTeam) . ",
						AwUnrewarded=0,
						AwPositions=" . StrSafe_DB('1,2,3') ;
				if($l=getModuleParameter('Awards', 'SecondLanguageCode')) {
					if($tmp=get_text($Event.intval($isTeam), 'Awards', '', false, '', $l, false)) {
						$Insert.= ', AwEventTrans='.StrSafe_DB($tmp) ;
					}
				}
				$RsIns=safe_w_sql($Insert);
			}
		}
	} elseif ($_REQUEST['Command']=='SWITCH') {
		if (isset($_REQUEST['EvSwitch']) && isset($_REQUEST['FinEv']) && isset($_REQUEST['TeamEv'])) {
			$Extra='';
			if($l=getModuleParameter('Awards', 'SecondLanguageCode')) {
				$q=safe_r_sql("select AwEventTrans from Awards  WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($_REQUEST['EvSwitch']) . " AND AwFinEvent=". StrSafe_DB($_REQUEST['FinEv']) . " AND AwTeam=". StrSafe_DB($_REQUEST['TeamEv']));
				if($r=safe_fetch($q) and !$r->AwEventTrans and $tmp=get_text($_REQUEST['EvSwitch'].intval($_REQUEST['TeamEv']), 'Awards', '', false, '', $l, false)) {
					$Extra=', AwEventTrans='.StrSafe_DB($tmp) ;
				}
			}
			$Switch = "UPDATE Awards SET AwGroup = (NOT AwGroup) $Extra WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($_REQUEST['EvSwitch']) . " AND AwFinEvent=". StrSafe_DB($_REQUEST['FinEv']) . " AND AwTeam=". StrSafe_DB($_REQUEST['TeamEv']);
			$RsSwitch = safe_w_sql($Switch);
		}
	} elseif ($_REQUEST['Command']=='OPTION') {
		if(isset($_REQUEST['OptSwitch']) && in_array($_REQUEST["OptSwitch"],array('RepresentCountry','PlayAnthem','SecondLanguage','ShowPoints','ShowPdfFlags'))) {
		    if($_REQUEST["OptSwitch"]=='ShowPdfFlags') {
		        // check if the RepresentCountry is on!
                if(!getModuleParameter('Awards', 'RepresentCountry', 1)) {
                    CD_redirect();
                }
            }
            $tmp = getModuleParameter('Awards', $_REQUEST["OptSwitch"], in_array($_REQUEST["OptSwitch"], array('ShowPoints','ShowPdfFlags','SecondLanguage') ? 0 : 1));
            setModuleParameter('Awards', $_REQUEST["OptSwitch"], ($tmp ? 0 : 1));
		    if($_REQUEST["OptSwitch"]=='RepresentCountry' and $tmp) {
                setModuleParameter('Awards', 'ShowPdfFlags', 0);
            }
		}
	} elseif ($_REQUEST['Command']=='DELETE') {
		if (isset($_REQUEST['EvDel']) && isset($_REQUEST['FinEv']) && isset($_REQUEST['TeamEv'])) {
			$Delete
				= "DELETE FROM Awards WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AwEvent=" . StrSafe_DB($_REQUEST['EvDel']) . " AND AwFinEvent=". StrSafe_DB($_REQUEST['FinEv']) . " AND AwTeam=". StrSafe_DB($_REQUEST['TeamEv']);
			$RsDel = safe_w_sql($Delete);
			if(strstr($_REQUEST['EvDel'], 'Custom')) {
				// delete all Custom related fields
				$Num=intval(substr($_REQUEST['EvDel'], 7));
				delModuleParameter('Awards','Aw-CustomEvent-1-'. $Num);
				delModuleParameter('Awards','Aw-CustomEvent-2-'. $Num);
				delModuleParameter('Awards','Aw-CustomPrize-1-'. $Num);
				delModuleParameter('Awards','Aw-CustomPrize-2-'. $Num);
				delModuleParameter('Awards','Aw-CustomNation-1-'. $Num);
				delModuleParameter('Awards','Aw-CustomNation-2-'. $Num);
				delModuleParameter('Awards','Aw-CustomWinner-1-'. $Num);
				delModuleParameter('Awards','Aw-CustomWinner-2-'. $Num);
			}
		}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
	exit;
}


$Awarders=array();
if($aws=getModuleParameterLike('Awards', 'Aw-Awarder-1-%')) {
    // sort them
    uksort($aws, function($a, $b) {
        $an=intval(substr($a,13));
        $bn=intval(substr($b,13));
        if($an==$bn) {
            return 0;
        }
        return ($an<$bn ? -1 : 1);
    });
}
foreach($aws as $awkey=>$awval) {
    $Awarders[]=['id'=>$awkey, 'val'=>preg_replace("/[\r\n]+/sim", ', ', $awval)];
}

$IncludeJquery = true;
$JS_SCRIPT = array(
    phpVars2js(array(
        'Awarders' => $Awarders,
        'btnDelete'=> get_text('CmdDelete','Tournament'),
        )),
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/Fun_AJAX_ManAwards.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
);

$PAGE_TITLE=get_text('MenuLM_ManAwards');

$SecondLanguage = getModuleParameter('Awards','SecondLanguage',0);
$SecondClass=($SecondLanguage ? '' : 'd-none');

include('Common/Templates/head.php');
?>
<table class="Tabella">
    <tr><th class="Title" colspan="9"><?php print get_text('MenuLM_ManAwards'); ?></th></tr>
    <tr class="Divider"><td colspan="9"></td></tr>
    <tr>
    <th style="width:25px"><?php print get_text('Print', 'Tournament'); ?></th>
    <th style="width:2em"><?php print get_text('Order', 'Tournament'); ?></th>
    <th style="width:2em"><?php print get_text('EvCode'); ?></th>
    <th style="width:4em"><?php print get_text('RankFinals', 'Tournament'); ?></th>
    <th style="width:3em"><?php print get_text('Event'); ?></th>
    <th style="width:8em"><div class="SecondLanguage <?= $SecondClass ?>"><?= get_text('EvNameTranslated', 'Tournament') ?></div></th>
    <th colspan="2"><?php print get_text('Awarders', 'Tournament'); ?></th>
    <th></th>
    </tr>
<?php

$CustomAwards=0;

$Select = "SELECT *
        FROM Awards
        WHERE AwTournament=" . StrSafe_DB($_SESSION['TourId']) . "
        ORDER BY AwGroup DESC, AwOrder, AwFinEvent DESC, AwTeam ASC, AwEvent";

$Rs=safe_r_sql($Select);

if (safe_num_rows($Rs)>0) {
    while ($MyRow=safe_fetch($Rs)) {
        if(strstr($MyRow->AwEvent,'Custom')) $CustomAwards++;
        $keyEvent = $MyRow->AwEvent.'|'.$MyRow->AwFinEvent.'|'.$MyRow->AwTeam;
        print '<tr id="'.$keyEvent.'">';

        print '<td class="Center">';
        print '<img  onclick="switchEnabled(\'' . $MyRow->AwEvent . "'," . $MyRow->AwFinEvent . "," . $MyRow->AwTeam . ')" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $MyRow->AwGroup . '.png" width="20" alt="' .  get_text($MyRow->AwGroup ? 'Yes' : 'No'). '">';
        print '</td>';

        print '<td onclick="insertInput(\'AwOrder|'.$keyEvent.'\')"><div class="Center" ref="AwOrder|'.$keyEvent.'">'.$MyRow->AwOrder.'</div></td>';

        print '<td class="Center">';
        print $MyRow->AwEvent;
        print '</td>';

        print '<td onclick="insertInput(\'AwPositions|'.$keyEvent.'\')"><div ref="AwPositions|'.$keyEvent.'">'.($MyRow->AwPositions=='1,2,4,3' ? '1,2,3-3' : $MyRow->AwPositions).'</div></td>';

        print '<td>';
        print $evArray[$MyRow->AwFinEvent. $MyRow->AwTeam];
        print '</td>';

        print '<td onclick="insertInput(\'AwEventTrans|'.$keyEvent.'\')"><div class="Center SecondLanguage '.$SecondClass.'" ref="AwEventTrans|'.$keyEvent.'">'.$MyRow->AwEventTrans.'</div></td>';

        $Awards=array();
        if($MyRow->AwAwarderGrouping) {
            $Awards=@unserialize($MyRow->AwAwarderGrouping);
        }

        echo '<td onclick="Manage(this,\'Award\')">';
        foreach($Awards as $k=>$v) {
            if(is_numeric($k)) {
                echo '<div><li>'.ManageHTML(get_text_eval(getModuleParameter('Awards', 'Aw-Award-1-'.$k), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v))).'</li></div>';
            } else {
                echo '<div><li>'.ManageHTML(get_text_eval(getModuleParameter('Awards', 'Aw-Special-1'), getModuleParameter('Awards', 'Aw-Awarder-1-'.$v))).'</li></div>';
            }
        }
        echo '</td>';
        echo '<td id="SecondLangAward['.$MyRow->AwEvent.'|'.$MyRow->AwFinEvent.'|'.$MyRow->AwTeam.']">';
        foreach($Awards as $k=>$v) {
            echo '<div class="SecondLanguage '.$SecondClass.'"><li>'.ManageHTML(get_text_eval(getModuleParameter('Awards', 'Aw-Award-2-'.$k), getModuleParameter('Awards', 'Aw-Awarder-2-'.$v))).'</li></div>';
        }
        echo '</td>';

        print '<td class="Center">';
        print '<input type="button" value="' . get_text('CmdDelete','Tournament') . '" onClick="javascript:DeleteAwards(\'' . $MyRow->AwEvent . "'," . $MyRow->AwFinEvent . "," . $MyRow->AwTeam . ',\'' . get_text('MsgAreYouSure') . '\');">';
        print '</td>';

        print '</tr>' . "\n";
    }
}

echo '<tr class="Divider"><td colspan="9"></td></tr>';
echo '<tr><th class="Title" colspan="9">' . get_text('Options','Tournament') . '</th></tr>';

$tmp = getModuleParameter('Awards','ReverseNameFunction',0);
echo '<tr>
    <td colspan="6" class="Right"><img id="ReverseNameFunction" onclick="switchOption(this)" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>
    <td colspan="3">'. get_text('ReverseNameFunction','Tournament') . '</td>
    </tr>';

$tmp = getModuleParameter('Awards','PlayAnthem',1);
echo '<tr><td colspan="6" class="Right"><img id="PlayAnthem" onclick="switchOption(this)" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>';
echo '<td colspan="3">'. get_text('AwardPlayAnthem','Tournament') . '</td></tr>';

$tmp = getModuleParameter('Awards','RepresentCountry',1);
echo '<tr><td colspan="6" class="Right"><img id="RepresentCountry" onclick="switchOption(this)" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>';
echo '<td colspan="3">'. get_text('AwardRepresentCountry','Tournament') . '</td></tr>';

$tmp = getModuleParameter('Awards','ShowPdfFlags',0);
echo '<tr><td colspan="6" class="Right"><img id="ShowPdfFlags" onclick="switchOption(this)" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>';
echo '<td colspan="3">'. get_text('ShowPdfFlags','Tournament') . '</td></tr>';

$tmp = getModuleParameter('Awards','ShowPoints', 0);
echo '<tr><td colspan="6" class="Right"><img id="ShowPoints" onclick="switchOption(this)" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $tmp. '.png" width="20" alt="' .  get_text($tmp ? 'Yes' : 'No'). '"></td>';
echo '<td colspan="3">'. get_text('AwardShowPoints','Tournament') . '</td></tr>';

$tmp = getModuleParameter('Awards','PrintPositions', array('Usher','2A','2B','2C','1A','1B','1C','3A','3B','3C', 'Tray Bearer 1', 'Tray Bearer 2', 'Tray Bearer 3', 'VIP Usher', 'V1', 'V2', 'VIP Usher'));
echo '<tr><td colspan="6" class="Right Bold" nowrap="nowrap">Print Positions</td>';
echo '<td colspan="3" onclick="insertInput(\'PrintPositions\')"><div ref="PrintPositions">'.(is_array($tmp) ? implode(', ', $tmp) : $tmp).'</div></td></tr>';

// Languages!
echo '<tr class="Divider"><td colspan="9"></td></tr>';
echo '<tr><th class="Title" colspan="9">' . get_text('Languages','Tournament') . '</th></tr>';

$tmp=getModuleParameter('Awards', 'SecondLanguageCode');
$tmp2=getModuleParameter('Awards', 'FirstLanguageCode');
$UseLang=($_SESSION['TourPrintLang'] ? $_SESSION['TourPrintLang'] : SelectLanguage());
if(empty($tmp2) or $tmp2!=$UseLang) {
    $tmp2=$UseLang;
    setModuleParameter('Awards', 'FirstLanguageCode', $UseLang);
    setModuleParameter('Awards', 'Aw-Intro-1', get_text('Award-Intro', 'IOC_Codes', '$a', '', '', $UseLang));
// 		setModuleParameter('Awards', 'Aw-Medal-1', get_text('Award-MedalGiver', 'IOC_Codes', '$a', '', '', $UseLang));
// 		setModuleParameter('Awards', 'Aw-Plaque-1', get_text('Award-PlaqueGiver', 'IOC_Codes', '$a', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Award-1-1', get_text('Award-MedalGiver', 'IOC_Codes', '$a', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Award-1-2', get_text('Award-PlaqueGiver', 'IOC_Codes', '$a', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Special-1', get_text('Award-Special', 'IOC_Codes', '$a', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Giver-1', get_text('Award-PremiumGiver', 'IOC_Codes', array('$a[0]','$a[1]'), '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Giving-1', get_text('Award-PremiumGiving', 'IOC_Codes', array('$a[0]','$a[1]'), '', '', $UseLang));
    for($n=1; $n<5; $n++) {
        setModuleParameter('Awards', 'Aw-Med'.$n.'-1', get_text('Medal-'.$n, 'IOC_Codes', '', '', '', $UseLang));
    }
    setModuleParameter('Awards', 'Aw-representing-1', get_text('Award-representing', 'IOC_Codes', '$a', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Anthem-1', get_text('Award-Anthem', 'IOC_Codes', '', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Anthem-TPE-1', get_text('Award-Anthem-TPE', 'IOC_Codes', '', '', '', $UseLang));
    setModuleParameter('Awards', 'Aw-Applause-1', get_text('Award-Applause', 'IOC_Codes', '', '', '', $UseLang));
}

echo '<tr>
    <th colspan="6" class="Right"><img id="SecondLanguage" onclick="switchOption(this)" src="' . $CFG->ROOT_DIR . 'Common/Images/Enabled' . $SecondLanguage. '.png" width="20" alt="' .  get_text($SecondLanguage ? 'Yes' : 'No'). '"></th>
    <th onclick="insertInput(\'FirstLanguageCode\')">' . get_text('AwardFirstLanguage','Tournament') . ': <span ref="FirstLanguageCode">'.($UseLang ? $UseLang : '---').'</span></th>
    <th onclick="insertInput(\'SecondLanguageCode\')">' . get_text('AwardSecondLanguage','Tournament') . ': <span ref="SecondLanguageCode">'.($tmp ? $tmp : '---').'</span></th>
    <th></th>
    </tr>';

$Lines=array(
    'Aw-Intro',
    'Aw-Med1',
    'Aw-Med2',
    'Aw-Med3',
    'Aw-Med4',
    'Aw-representing',
    'Aw-Anthem',
    'Aw-Anthem-TPE',
    'Aw-Applause',
);

foreach($Lines as $k) {
    echo '<tr>
        <th colspan="6" class="Right" nowrap="nowrap">'.substr($k,3).'</th>
        <td onclick="insertInput(\''.$k.'-1\')"><div id="'.$k.'-1" ref="'.$k.'-1">'.getModuleParameter('Awards', $k.'-1').'</div></td>
        <td onclick="insertInput(\''.$k.'-2\')"><div id="'.$k.'-2" ref="'.$k.'-2" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards', $k.'-2') : '').'</div></td>
        <td></td>
        </tr>';
}

echo '<tr><th colspan="9" class="Title"></th></tr>';

echo '<tbody id="AwardsBody">';
foreach(getModuleParameterLike('Awards', 'Aw-Award-1-%') as $key1 => $val) {
    $key2=str_replace('Aw-Award-1-','Aw-Award-2-',$key1);
    $n=substr($key1,11);
    echo '<tr>
        <th colspan="6" class="Right" nowrap="nowrap">'.get_text('AwardNum', 'Tournament', $n).'</th>
        <td onclick="insertInput(\''.$key1.'\')"><div id="'.$key1.'" ref="'.$key1.'">'.$val.'</div></td>
        <td onclick="insertInput(\''.$key2.'\')"><div id="'.$key2.'" ref="'.$key2.'" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards',$key2) : '').'</div></td>
        <td class="Center">
            <input type="button" value="' . get_text('CmdDelete','Tournament') . '" onClick="window.location.href=\'?delAward='.$n.'\'">
        </td></tr>';
}
echo '</tbody>';

echo '<tr>
    <th colspan="6" class="Right" nowrap="nowrap">'.get_text('AwardNew', 'Tournament').'</th>
    <td colspan="3" onclick="insertInput(\'Aw-Award-new\')"><div ref="Aw-Award-new" style="height:1em"></div></td>
    </tr>';

echo '<tr><th colspan="9" class="Title"></th></tr>';

echo '<tr>
    <th colspan="6" class="Right" nowrap="nowrap">'.get_text('Special', 'Tournament').'</th>
    <td onclick="insertInput(\'Aw-Special-1\')"><div ref="Aw-Special-1">'.getModuleParameter('Awards','Aw-Special-1').'</div></td>
    <td onclick="insertInput(\'Aw-Special-2\')"><div ref="Aw-Special-2" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards','Aw-Special-2') : '').'</div></td>
    <td></td></tr>';

if($CustomAwards) {
    echo '<tr><th colspan="9" class="Title"></th></tr>';
    for($n=1; $n<=$CustomAwards; $n++) {
        echo '<tr>
            <th colspan="5" rowspan="4" nowrap="nowrap">'.get_text('CustomAward', 'Awards').' '.$n.'</th>
            <th class="Right" nowrap="nowrap">'.get_text('CustomEvent', 'Awards').'</th>
            <td onclick="insertInput(\'Aw-CustomEvent-1-'.$n.'\')"><div ref="Aw-CustomEvent-1-'.$n.'">'.getModuleParameter('Awards','Aw-CustomEvent-1-'. $n).'</div></td>
            <td onclick="insertInput(\'Aw-CustomEvent-2-'.$n.'\')"><div ref="Aw-CustomEvent-2-'.$n.'" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards','Aw-CustomEvent-2-'. $n) : '').'</div></td>
            <td rowspan="4"></td></tr>';
        echo '<tr>
            <th class="Right" nowrap="nowrap">'.get_text('CustomPrize', 'Awards').'</th>
            <td onclick="insertInput(\'Aw-CustomPrize-1-'.$n.'\')"><div ref="Aw-CustomPrize-1-'.$n.'">'.getModuleParameter('Awards','Aw-CustomPrize-1-'. $n).'</div></td>
            <td onclick="insertInput(\'Aw-CustomPrize-2-'.$n.'\')"><div ref="Aw-CustomPrize-2-'.$n.'" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards','Aw-CustomPrize-2-'. $n) : '').'</div></td>
            </tr>';
        echo '<tr>
            <th class="Right" nowrap="nowrap">'.get_text('CustomNation', 'Awards').'</th>
            <td onclick="insertInput(\'Aw-CustomNation-1-'.$n.'\')"><div ref="Aw-CustomNation-1-'.$n.'">'.getModuleParameter('Awards','Aw-CustomNation-1-'. $n).'</div></td>
            <td onclick="insertInput(\'Aw-CustomNation-2-'.$n.'\')"><div ref="Aw-CustomNation-2-'.$n.'" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards','Aw-CustomNation-2-'. $n) : '').'</div></td>
            </tr>';
        echo '<tr>
            <th class="Right" nowrap="nowrap">'.get_text('CustomWinner', 'Awards').'</th>
            <td onclick="insertInput(\'Aw-CustomWinner-1-'.$n.'\')"><div ref="Aw-CustomWinner-1-'.$n.'">'.getModuleParameter('Awards','Aw-CustomWinner-1-'. $n).'</div></td>
            <td onclick="insertInput(\'Aw-CustomWinner-2-'.$n.'\')"><div ref="Aw-CustomWinner-2-'.$n.'">'.($SecondLanguage ? getModuleParameter('Awards','Aw-CustomWinner-2-'. $n) : '').'</div></td>
            </tr>';
    }
}

echo '<tr><th colspan="9" class="Title"></th></tr>';

echo '<tbody id="AwardersBody">';
foreach($Awarders as $Aw) {
	$key2=str_replace('Aw-Awarder-1-','Aw-Awarder-2-',$Aw['id']);
	$n=substr($Aw['id'],13);
	echo '<tr>
        <th colspan="6" Class="Right" nowrap="nowrap">'.get_text('AwarderNum', 'Tournament', $n).'</th>
        <td onclick="insertInput(\''.$Aw['id'].'\')"><div ref="'.$Aw['id'].'" class="w-100">'.$Aw['val'].'</div></td>
        <td onclick="insertInput(\''.$key2.'\')"><div ref="'.$key2.'" class="SecondLanguage">'.($SecondLanguage ? getModuleParameter('Awards',$key2, '') : '').'</div></td>
        <td class="Center">
            <input type="button" value="' . get_text('CmdDelete','Tournament') . '" onClick="window.location.href=\'?delAwarder='.$n.'\'">
        </td></tr>';
}
echo '</tbody>';

echo '<tr>
    <th colspan="6" class="Right" nowrap="nowrap">'.get_text('AwarderNew', 'Tournament').'</th>
    <td colspan="3" onclick="insertInput(\'Aw-Awarder-new\')"><div ref="Aw-Awarder-new" style="height:1em;"></td>
    </tr>';

echo '<tr class="Divider"><td colspan="9"></td></tr>';
echo '<tr><th class="Title" colspan="9">' . get_text('AwardAvailableEvents','Tournament') . '</th></tr>';
echo '<tr><td colspan="9"><form name="frmAdd" action="" method="get"><table class="Tabella">';
	$needSubmit = false;
	//Individual Events
	$Sql = "SELECT EvCode as Event
		FROM Events
		LEFT JOIN Awards ON EvTournament=AwTournament AND EvCode=AwEvent AND AwFinEvent=1 AND EvTeamEvent=AwTeam
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 20%">' . get_text('IndEventList') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event . '|1|0">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	//Team Events
	$Sql = "SELECT EvCode as Event
		FROM Events
		LEFT JOIN Awards ON EvTournament=AwTournament AND EvCode=AwEvent AND AwFinEvent=1 AND EvTeamEvent=AwTeam
		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 20%">' . get_text('TeamEventList') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event . '|1|1">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	//Individual Cl/div
	$Sql = "SELECT CONCAT(DivId,ClId) as Event
		FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament
		LEFT JOIN Awards ON DivTournament=AwTournament AND CONCAT(DivId,ClId)=AwEvent AND AwFinEvent=0 AND AwTeam=0
		WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 AND DivAthlete=1 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 20%">' . get_text('ResultIndClass', 'Tournament') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event . '|0|0">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}
	//Team Cl/div
	$Sql = "SELECT CONCAT(DivId,ClId) as Event
		FROM Divisions INNER JOIN Classes ON DivTournament=ClTournament
		LEFT JOIN Awards ON DivTournament=AwTournament AND CONCAT(DivId,ClId)=AwEvent AND AwFinEvent=0 AND AwTeam=1
		WHERE DivTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND ClAthlete=1 AND DivAthlete=1 AND AwEvent IS NULL";
	$Rs=safe_r_SQL($Sql);
	if(safe_num_rows($Rs)) {
		$needSubmit = true;
		echo '<tr><th style="width: 20%">' . get_text('ResultSqClass', 'Tournament') . '</th><td>';
		while($row=safe_fetch($Rs))
			echo '<input type="checkbox" name="addField[]" value="' . $row->Event. '|0|1">'. $row->Event . "&nbsp;&nbsp;&nbsp;";
		echo '</td></tr>';
	}

	echo '<tr><th style="width: 20%">' . get_text('CustomAward', 'Awards') . '</th><td>';
	echo '<input type="checkbox" name="addField[]" value="Custom|1|0">'. get_text('CustomAward', 'Awards') . "&nbsp;&nbsp;&nbsp;";
	echo '</td></tr>';

	if($needSubmit)
		echo '<tr><td colspan="2" class="Center"><input type="hidden" name="Command" value="ADD"><input type="submit" name="' . get_text('CmdAdd', 'Tournament') . '"></td></th>';

	echo '</table></td></tr>';
?>
</table>
<?php
	include('Common/Templates/tail.php');
?>