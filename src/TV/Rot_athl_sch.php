<?php
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);
// get the array of our guys
$Select = "select EnId from Entries 
    inner join Qualifications on QuId=EnId
    WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND EnTournament = " . StrSafe_DB($TourId) . "
    " . ($TVsettings->EventFilter ? " AND CONCAT(EnDivision,EnClass) " . $TVsettings->EventFilter : "") . "
    " . (($TVsettings->TVPSession??0) ? " AND QuSession= " . intval($TVsettings->TVPSession) : "") . "
    order by rand()
    limit " . ($TVsettings->TVPNumRows ? $TVsettings->TVPNumRows : "1");
$GUYS=array();

$q=safe_r_sql($Select);
while($r=safe_fetch($q)) $GUYS[]=$r->EnId;

// Check-Create the pictures of the Entries
// $fotow=min(200,intval($_SESSION['WINHEIGHT']/6)*4/3); // resized later :)
include_once('Common/CheckPictures.php');
CheckPictures($TourCode);

$options=array('tournament' => $RULE->TVRTournament, 'enids'=>$GUYS);

if(isset($TVsettings->TVPEventInd) && !empty($TVsettings->TVPEventInd))
    $options['events'] = explode('|',$TVsettings->TVPEventInd);
if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
    $options['cutRank'] = $TVsettings->TVPNumRows;
//if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
//    $options['session'] = $TVsettings->TVPSession;

$rank=Obj_RankFactory::create('DivClass',$options);
$rank->read();
$rankData=$rank->getData();

if(!empty($rankData['sections'])) {
    foreach ($rankData['sections'] as $Category=>$Items) {
        foreach ($Items['items'] as $Item) {
            // lo include nelle schede da ritornare
            $ret[$Item['id']]['head']='';
            $ret[$Item['id']]['cols']='';
            $ret[$Item['id']]['fissi']='';
            $ret[$Item['id']]['type']='DB';
            $ret[$Item['id']]['style']=$ST;
            $ret[$Item['id']]['js']=$JS;
            $ret[$Item['id']]['js'] .= 'FreshDBContent[%1$s]=\'\';'."\n";

            $Photo='';
            if(is_file(__DIR__.'/'.($pic="Photos/{$TourCode}-En-{$Item['id']}.jpg"))) {
                $Photo='<img class="athletephoto" src="'.$pic.'" style="width:100%" alternate=""/>';
            }

            $tmp ='<table width="100%" height="80%" style="font-size:4vh;">';
            // prima riga, a destra ci va la foto, a sinistra i dati
            $tmp.='<tr>';
            if($Photo) {
                $tmp.='<td style="width:30%" align="right" rowspan="5">'.$Photo.'</td>';
            }
            $tmp.='<td width="100%" align="center">' . $Item['familynameUpper'] . ' ' . ($TVsettings->TVPNameComplete==0 ? FirstLetters($Item['givenname']) : $Item['givenname']).'</td>';
            $tmp.='</tr>';

            $tmp.='<tr><td align="center" class="piccolo"><span class="piccolo">'.get_text('RankingPosition','Tournament').'</span><br/>'.$Item['rank'].'<span class="piccolo">';
            for ($i=1;$i<=$Items['meta']['numDist'];++$i) {
                $bits=explode('|', $Item['dist_'.$i]);
                $tmp .= '<br/>' . $Items['meta']['fields']['dist_'.$i] . ': ' . str_pad($bits[1],3," ",STR_PAD_LEFT) . '<span class="piccolo">/' . str_pad($bits[0],3," ",STR_PAD_LEFT) . '</span>';
//                else if($i < $SnapDistance)
//                    $tmp .= str_pad($MyRow->{'QuD' . $i . 'Score'},3," ",STR_PAD_LEFT);
//                else if($i == $SnapDistance)
//                    $tmp .= str_pad($MyRow->{'EqScore'},3," ",STR_PAD_LEFT);
//                else
//                    $tmp .= str_pad("0",3," ",STR_PAD_LEFT);
            }
            $tmp.='</span></td></tr>';

            $tmp.='<tr><td align="center" class="piccolo"><span class="piccolo">'.get_text('Division').' - '.get_text('Class').'</span><br/>'.$Item['div'] . '&nbsp;&nbsp;' . $Item['class'].'</td></tr>';

            $tmp.='<tr><td align="center" class="piccolo"><span class="piccolo">'.get_text('Target').'</span><br/>'.ltrim($Item['target'],'0').'</td></tr>';

            $tmp.='<tr>';
            $tmp.='<td align="center" class="piccolo"><span class="piccolo">'.get_text('Score','Tournament').'</span><br/>' . $Item['score'] . '</td>';
            $tmp.='</tr>';
            $tmp.='';
            $tmp.='';
            $tmp.='</table>';

            $ret[$Item['id']]['basso']=$tmp;
        }
    }

}
