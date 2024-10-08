<?php
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_FormatText.inc.php');

function rotQuals($TVsettings, $RULE) {
	global $CFG, $IsCode, $TourId, $SubBlock;
	$CSS=unserialize($TVsettings->TVPSettings);
	getPageDefaults($CSS);
	$Return=array(
		'CSS' => $CSS,
		'html' => '',
		'Block' => 'QualRow',
		'BlockCss' => 'height:2em; width:100%; padding-right:0.5rem; overflow:hidden; font-size:2em; display:flex; flex-direction:row; justify-content:space-between; align-items:center; box-sizing:border-box;',
		'NextSubBlock' => 1,
		'SubBlocks' => 1);
	$ret=array();

	$TourType=getTournamentType($TourId);

	$TVsettings->EventFilter=MakeEventFilter($TVsettings->TVPEventInd);

	$options=array('tournament' => $RULE->TVRTournament);
	$options['records'] = 1;
	$options['subFamily'] = 'DivClass';

	if(isset($TVsettings->TVPEventInd) && !empty($TVsettings->TVPEventInd))
		$options['events'] = explode('|',$TVsettings->TVPEventInd);
	if(isset($TVsettings->TVPNumRows) && $TVsettings->TVPNumRows>0)
		$options['cutRank'] = $TVsettings->TVPNumRows;
	if(isset($TVsettings->TVPSession) && $TVsettings->TVPSession>0)
		$options['session'] = $TVsettings->TVPSession;

	$Columns=(isset($TVsettings->TVPColumns) && !empty($TVsettings->TVPColumns) ? explode('|',$TVsettings->TVPColumns) : array());
	$ViewTeams=(in_array('TEAM', $Columns) or in_array('ALL', $Columns));
	$ViewFlag=(in_array('FLAG', $Columns) or in_array('ALL', $Columns));
	$ViewCode=(in_array('CODE', $Columns) or in_array('ALL', $Columns));
	$ViewDists=((in_array('DIST', $Columns) or in_array('ALL', $Columns)) and $TVsettings->TVPViewPartials);
	$View10s=(in_array('10', $Columns) or in_array('ALL', $Columns));
	$ViewX9s=(in_array('X9', $Columns) or in_array('ALL', $Columns));
	$ViewArrows=(in_array('ARROWS', $Columns) or in_array('ALL', $Columns));
	$Title2Rows=(in_array('TIT2ROWS', $Columns) ? '<br/>' : ': ');
	$Title2Arrows=(in_array('TIT2ROWS', $Columns) ? ' / ' : '<br/>' );
	$ViewTOT=(in_array('TOT', $Columns) or in_array('ALL', $Columns));

	$comparedTo=preg_grep('/^COMP:/', $Columns);
	if(!empty($comparedTo)) {
		list(,$comparedTo) = explode(":",reset($comparedTo));
		$options['comparedTo'] = $comparedTo;
	}

	$Fixed=preg_grep('/^FIXED:/', $Columns);
	$FixedDone=false;
	if(!empty($Fixed)) {
		list(,$Fixed) = explode(":",reset($Fixed));
	}

	$rank=Obj_RankFactory::create('Snapshot',$options);
	$rank->read();
	$rankData=$rank->getData();

	if(count($rankData['sections'])==0) return $Return;

	$Return['SubBlocks']=count($rankData['sections']);
	$Return['NextSubBlock']=($SubBlock+1);

	if($SubBlock>count($rankData['sections'])) $SubBlock=1;

	foreach($rankData['sections'] as $IdEvent => $data) {
		$SubBlock--;
		if(!$SubBlock) {
			break;
		}
	}

	// TITLE
	$tmp = '';

	$ret[]='<div class="Title">
				<div class="TitleImg" style="float:left;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToLeft.jpg"></div>
				<div class="TitleImg" style="float:right;"><img src="'.$CFG->ROOT_DIR.'TV/Photos/'.$IsCode.'-ToRight.jpg"></div>
		'.$rankData['meta']['title'].$Title2Rows.$data['meta']['descr']
		.($ViewArrows ? $Title2Arrows.$data['meta']['printHeader'] : '')
		.'</div>';

	// Header header;
	$tmp ='<div class="QualRow Headers">';


	$tmp.='<div class="Rank Headers">' . $data['meta']['fields']['rank'] . '</div>';
	if(!empty($comparedTo)) {
		$tmp.='<div class="RankOld">&nbsp;</div>';
	}
	if($ViewCode) {
		$tmp.='<div class="CountryCode Rotate">&nbsp;</div>';
	}
	if($ViewFlag) {
		$tmp.='<div class="FlagDiv">&nbsp;</div>';
	}

	$tmp.='<div class="Target Headers">' . $data['meta']['fields']['target'] . '</div>';
	$tmp.='<div class="Athlete Headers">'.$data['meta']['fields']['athlete'].'</div>';

	if($ViewTeams) {
		$tmp.='<div class="CountryDescr Headers">' . $data['meta']['fields']['countryName'] . '</div>';
	}

	if($ViewDists) {
		for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
			if($data['meta']['fields']['dist_'.$i]=='-') continue;
			$tmp.='<div class="DistScore Headers">' . $data['meta']['fields']['dist_'.$i] . '</div>';
			$tmp.='<div class="DistPos">&nbsp;</div>';
		}
	}

	$tmp.='<div class="Score Headers">' . get_text('SnapShort', 'Tournament', $data['meta']['snapArrows']) . '</div>';

	if($ViewTOT) {
		$tmp.='<div class="Score Headers">' . $data['meta']['fields']['score'] . '</div>';
	}
	if($View10s) $tmp.='<div class="Gold Headers">' . $data['meta']['fields']['gold'] . '</div>';
	if($ViewX9s) $tmp.='<div class="XNine Headers">' . $data['meta']['fields']['xnine'] . '</div>';
	$tmp.='</div>';
	$ret[]=$tmp;

	// al 4° devo interrompere la tabella e aprire il resto in un div separato
	if(!$Fixed) {
		$ret[]='<div id="content" data-direction="up">';
		$FixedDone=true;
	}

	// Inserisci adesso le singole righe
	foreach($data['items'] as $key => $archer) {
		if($Fixed and $archer['rank']>$Fixed and !$FixedDone) {
			$ret[]='<div id="content" data-direction="up">';
			$FixedDone=true;
		}
		$Class=($key%2 == 0 ? 'e': 'o');
		$tmp ='<div class="QualRow Font1'.$Class.' Back1'.$Class.'">';


		$tmp.='<div class="Rank">' . $archer['rank'] . '</div>';
		if(!empty($comparedTo)) {
			$cl="RankNone";
			if($archer['oldRank']) {
				if($archer['rank']==$archer['oldRank']) {
					$cl='RankMinus';
				} elseif($archer['rank']<$archer['oldRank']) {
					$cl='RankUp';
				} else {
					$cl='RankDown';
				}
			}
			$tmp.='<div class="RankOld '.$cl.'">' . ($archer['oldRank']&& $archer['oldRank']!=$archer['rank'] ? $archer['oldRank']:'&nbsp;'). '</div>';
		}
		if($ViewCode) {
			$tmp.='<div class="CountryCode Rotate Rev1'.$Class.'">' . $archer['countryCode'] . '</div>';
		}
		if($ViewFlag) {
			$tmp.='<div class="FlagDiv">'.get_flag_ianseo($archer['countryCode'], '', '', $IsCode).'</div>';
		}

		$tmp.='<div class="Target">' . ltrim($archer['target'],'0') . '</div>';
		$tmp.='<div class="Athlete">'.$archer['familynameUpper'].' '.($TVsettings->TVPNameComplete==0 ? FirstLetters($archer['givenname']) : $archer['givenname']).'</div>';
		if($ViewTeams) {
			$tmp.='<div class="CountryDescr">' . $archer['countryName'] . '</div>';
		}
		if($ViewDists) {
			for ($i=1; $i<=$rankData['meta']['numDist']; ++$i) {
				if($data['meta']['fields']['dist_'.$i]=='-') continue;
				list($rank, $score, $gold, $xnine)=explode('|', $archer['dist_'.$i]);
				$tmp.='<div class="DistScore">' . $score . '</div>';
				$tmp.='<div class="DistPos">/'.(($TourType != 14 and $TourType != 32) ? $rank : $xnine).'</div>';
			}
		}
		$tmp.='<div class="Score">' . $archer['scoreSnap'] . '</div>';
		if($ViewTOT) $tmp.='<div class="Score">' . $archer['score'] . '</div>';
		if($View10s) $tmp.='<div class="Gold">' . $archer['gold'] . '</div>';
		if($ViewX9s) $tmp.='<div class="XNine">' . $archer['xnine'] . '</div>';
		$tmp.='</div>';
		$ret[]=$tmp;
	}
	if($FixedDone) $ret[]='</div>';

	$Return['html']=implode('', $ret);

	return $Return;
}

function rotQualsSettings($Settings) {
	global $CFG;
	$ret='<br/>';
	$ret.= '<table class="Tabella Css3">';
	$ret.= '<tr><th colspan="3">'.get_text('TVCss3SpecificSettings','Tournament').'</th></tr>';

	// defaults for fonts, colors, size
	$RMain=array();
	if(!empty($Settings)) {
		$RMain=unserialize($Settings);
	}

	$PageDefaults=getPageDefaults($RMain);

// 	if(!isset($RMain[''])) $RMain['']='';
	// if(!isset($RMain[''])) $RMain['']='';

	foreach($PageDefaults as $key => $Value) {
		$ret.= '<tr ref="'.$Value.'">
			<th nowrap="nowrap">
			    <div class="d-flex">
                    <div class="CssResetButton '.($Value==$RMain[$key] ? 'CssResetDisabled' : '').'" onclick="SetDefaults(this)">Default</div>
                    <div class="CssTitle">
                        '.get_text('TVCss3'.$key,'Tournament').'
                        <i class="fa fa-pencil-alt ml-1" onclick="editCss(this)"></i>
                    </div>
                </div>
            </th>
			<td width="100%"><input type="text" name="P-Main['.$key.']" id="P-Main['.$key.']" value="'.$RMain[$key].'"></td>
			</tr>';
	}
	return $ret;
}

function getPageDefaults(&$RMain) {
	global $CFG;
	$ret=array(
		'Title' => '',
		'RankOld' => 'background-repeat:no-repeat; background-size: contain; background-position:center;color:#FFFFFF; font-weight:bold; font-size:60%;',
		'RankNone' => '',
		'RankUp' => 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/Up.png\');',
		'RankDown' => 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/Down.png\');',
		'RankMinus' => 'background: url(\'' . $CFG->ROOT_DIR . 'Common/Images/Minus.png\');',
		'Rank' => 'flex: 0 0 4vw; text-align:right;',
		'CountryCode' => 'flex: 0 0 3.5vw; font-size:0.8vw; margin-left:-3.75ch',
		'FlagDiv' => 'flex: 0 0 4.35vw;',
		'Flag' => 'height:2.8vw; border:0.05vw solid #888;box-sizing:border-box;',
		'Target' => 'flex: 0 0 6vw; text-align:right;margin-right:0.5em;',
		'Athlete' => 'flex: 1 1 20vw;white-space:nowrap;overflow:hidden;',
		'CountryDescr' => 'flex: 0 1 20vw;white-space:nowrap;overflow:hidden;',
		'Arrows' => 'flex: 0 0 5vw; text-align:right; font-size:1em;margin-right:0.5rem;',
		'DistScore' => 'flex: 0 0 5vw; text-align:right; font-size:0.8em;',
		'DistPos' => 'flex: 0 0 3vw; text-align:left; font-size:0.7em;',
		'Score' => 'flex: 0 0 6vw; text-align:right; font-size:1.25em;margin-right:0.5rem;',
		'Gold' => 'flex: 0 0 3vw; text-align:right; font-size:1em;',
		'XNine' => 'flex: 0 0 3vw; text-align:right; font-size:1em;',
	);
	foreach($ret as $k=>$v) {
		if(!isset($RMain[$k])) $RMain[$k]=$v;
	}
	return $ret;
}

