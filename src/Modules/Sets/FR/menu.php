<?php

if(!empty($on) AND $_SESSION["TourLocRule"]=='FR' AND $acl[AclCompetition] >= AclReadOnly) {
    $ret['COMP']['EXPT'][] = MENU_DIVIDER;
    $ret['COMP']['EXPT'][] = get_text('MenuLM_Export-FR-Results') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/exports/';

    if($_SESSION['TourType']==50) {
        $srch=get_text('MenuLM_Scorecard Printout') . '|' . $CFG->ROOT_DIR . 'Qualification/PrintScore.php';
        foreach($ret['QUAL'] as $k=>$v) {
            if($v==$srch) {
                $ret['QUAL'][$k]=get_text('MenuLM_Scorecard Printout') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/pdf/PrintScore.php';
                break;
            }
        }
    }

	switch($_SESSION['TourLocSubRule']) {
		case 'SetFRCoupeFrance':
            $q=safe_r_sql("select EvCode, count(*) as ExAequo
                from Individuals
                inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0 and EvFinalFirstPhase=0
                inner join Qualifications on QuId=IndId
                inner join (select EvCode TieCode, count(*) as ExAequo, QuScore as TieScore
                    from Individuals
                    inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0 and EvFinalFirstPhase=0
                    inner join Qualifications on QuId=IndId
                    where IndTournament={$_SESSION['TourId']} and IndRank between 1 and 4
                    group by EvCode, QuScore
                    having ExAequo>1) Ties on TieCode=IndEvent and QuScore=TieScore
                where IndTournament={$_SESSION['TourId']}
                group by EvCode, QuScore
                having ExAequo>1
                order by EvProgr, QuScore desc");
            if(safe_num_rows($q)>0) {
                $tmp = get_text('MenuLM_Check shoot-off before Rank');
                $ev=[];
                while($r=safe_fetch($q)) {
                    $ev[]=$r->EvCode;
                }
                $tmp .= ' <b class="ShootOffMenu">(' . implode('-', $ev) . ')</b>';
                $tmp .= '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/AbsTae.php';
                $ret['FINI'][]=MENU_DIVIDER;
                $ret['FINI'][]=$tmp;
            }
            break;
		case 'SetFRChampsD1DNAP':
		case 'SetFRD12023':
			$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);

			$SubMenu=get_text($_SESSION['TourLocSubRule'], 'Install');
			$tmp= array($SubMenu);
	        $tmp[]=get_text('Setup', 'ISK'). '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/configure.php';
	        $tmp[]=get_text('MenuLM_Target Assignment') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/';
			if($_SESSION['TourLocSubRule']=='SetFRD12023') {
				if ($acl[AclRobin] >= AclReadOnly) {
					$tmp[]=get_text('ScorecardsTeams', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Modules/RoundRobin/PrintScore.php?team=1';
				}
			} else {
				if($_SESSION['MenuFinIOn']) {
		            $tmp[]=get_text('ScorecardsInd', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Final/Individual/PrintScore.php';
		        }
		        $tmp[]=get_text('ScorecardsTeams', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Final/Team/PrintScore.php';
			}
			$tmp[]=get_text('StartListbyTarget', 'Tournament') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/StartList.php|||PrintOut';
			// $tmp[]=get_text('TempRank') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/CompetitionRanking.php|||PrintOut';
			if($AllInOne) {
				$tmp[]=get_text('MenuLM_Check shoot-off before final phases') . '|' . $CFG->ROOT_DIR . 'Modules/Sets/FR/Manage/AbsTeam.php';
			}

			if(isset($ret['SetFRChampsD1DNAP'])) {
				$ret['SetFRChampsD1DNAP']=array_merge($tmp , $ret['SetFRChampsD1DNAP']);
			} else {
				$ret['SetFRChampsD1DNAP']=$tmp;
			}

			if($_SESSION['TourLocSubRule']=='SetFRD12023') {
				$ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
				if ($acl[AclRobin] == AclReadWrite) {
					$tmp = get_text('DifferentEventSoManagementButton', 'RoundRobin') . '';
					if ($_SESSION['MenuRobin']) {
						$tmp .= ' <b class="ShootOffMenu">(';
						if(isset($_SESSION['MenuRobin'][1])) {
							$tmp.=(isset($_SESSION['MenuRobin'][0]) ? ' / ' : '').get_text('Team').' '.implode('-', $_SESSION['MenuRobin'][1]);
						}
						$tmp .= ')</b>';
					}
					$tmp .= '|' . $CFG->ROOT_DIR . 'Modules/RoundRobin/AbsRobin.php?team=1';
					$ret['SetFRChampsD1DNAP'][] = $tmp;
				}
				if($_SESSION['MenuRobinOn']) {
					if ($acl[AclRobin] == AclReadWrite) {
						$ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
						// $ret['SetFRChampsD1DNAP'][] = get_text('MenuLM_Change Components') . '|' . $CFG->ROOT_DIR . 'Final/Team/ChangeComponents.php';
						// $ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
						$ret['SetFRChampsD1DNAP'][] = get_text('MenuLM_Data insert (Table view)') . '|' . $CFG->ROOT_DIR . 'Modules/RoundRobin/InsertPoint.php?team=1';
						$ret['SetFRChampsD1DNAP'][] = get_text('MenuLM_Spotting') . '|' . $CFG->ROOT_DIR . 'Modules/RoundRobin/Spotting.php?team=1';
						$ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
						$ret['SetFRChampsD1DNAP']['SCOR'][] = get_text('MenuLM_Input Score') .'|'.$CFG->ROOT_DIR.'Modules/Barcodes/GetFinScoreBarCode.php';
						$ret['SetFRChampsD1DNAP']['SCOR'][] = get_text('MenuLM_GetScoreBarcode') .'|'.$CFG->ROOT_DIR.'Modules/Barcodes/GetRobinScoreBarCode.php';
						$ret['SetFRChampsD1DNAP']['SCOR'][] = get_text('MenuLM_GetScoreBarcodeReport') .'|'.$CFG->ROOT_DIR.'Modules/Barcodes/GetScoreBarCodeReport.php|||_blank';
						$ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
						$ret['SetFRChampsD1DNAP'][] = get_text('MenuLM_Speaker') . '|' . $CFG->ROOT_DIR.'Modules/Speaker/robin.php';

					}
					if ($acl[AclRobin] >= AclReadOnly) {
						$ret['SetFRChampsD1DNAP'][] = MENU_DIVIDER;
						$ret['SetFRChampsD1DNAP'][] = get_text('MenuLM_RobinPrintout') . '|' . $CFG->ROOT_DIR . 'Modules/RoundRobin/PrintOut.php';
					}
				}
			}
		    $ret['SetFRChampsD1DNAP'][]=get_text('MenuLM_Printout') . '|' . $CFG->ROOT_DIR . 'Final/PrintOut.php';

			// unset($ret['COMP']['ROBIN']);
			unset($ret['ROBIN']);
			break;
	}
}
