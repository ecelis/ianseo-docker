<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

CheckTourSession(true);

// check competition level
$aclLevel = checkACL(AclCompetition,AclReadOnly);

if(!empty($_REQUEST['forceLUE'])) {
	safe_w_sql("update Entries inner join Tournament on ToId=EnTournament set EnIocCode=ToIocCode where EnId=".intval($_REQUEST['forceLUE']));
	CD_redirect(go_get('forceLUE', '', true));
}

if(!empty($_REQUEST['lev'])) {
	require_once('Common/Lib/ArrTargets.inc.php');
	$File=array();

	// Version
	$File[]=sprintf("VERSION : \t%s.%s\t", defined('ProgramRelease') ? ProgramRelease : 'unknown', defined('ProgramBuild') ? preg_replace('/\D/', '', ProgramBuild) : 'unknown');

	// get the judges
	$Select="SELECT TiCode Judges 
		FROM TournamentInvolved  
		inner JOIN InvolvedType ON TiType=ItId 
		WHERE TiTournament={$_SESSION['TourId']} AND (ItJudge>0 or ItDos>0)
		order by ItDos, ItJudge";
	$JudgesResp='';
	$Judges=[];
	$Coaches=[];
	$q=safe_r_sql($Select);
	while($r=safe_fetch($q)) {
        if(empty($JudgesResp)) {
            $JudgesResp=$r->Judges;
        } else {
		    $Judges[]=$r->Judges;
        }
	}
	$File[]="RARBITRES\t{$JudgesResp}";
	$File[]="ARBITRES\t".implode("\t", $Judges);
	$File[]="ENTRAINEURS\t".implode("\t", $Coaches);

	$Archers=array();
	$q=safe_r_sql("select * from Tournament where ToId={$_SESSION['TourId']}");
	$COMP=safe_fetch($q);
	$Discipline='';
	$MaxDistance='';
	$MaxArrows='';
	switch($COMP->ToCategory) {
		case 1:
			$Discipline='F';
			if($COMP->ToTypeSubRule=='SetFRChampsFederal') {
				$Discipline='E';
			}
			if($COMP->ToWhenFrom>='2019-01-01') {
                $_REQUEST['lev']=='SP' ? 'H' : 'T';
			}
			break;
		case 2:
            $Discipline= $_REQUEST['lev']=='SP' ? 'I' : 'S';
			break;
		case 4:
			$Discipline='C';
			break;
		case 8:
			$Discipline='3';
			break;
		//case 16: inveted... should be 2 more
		//	$Discipline='N'; // Nature
		//	$Discipline='B'; // Beursault
        // PARA 18m => I
        // PARA Exterieur => H
        // Jeunesse => J
        // Poussin => P
		//	break;
	}

    $Filter='';
    if($_REQUEST['lev']=='S') {
        $Filter=" and DivIsPara=0";
    } elseif($_REQUEST['lev']=='SP') {
        $Filter=" and DivIsPara=1";
        $_REQUEST['lev']='S';
    }
	$q=safe_r_sql("select EnIocCode, EnCode, ucase(EnFirstName) as EnFirstName, ucase(EnName) as EnName, EnDivision, EnAgeClass, EnClass, EnSex, 
			ifnull(IndEvent,'-') as IndEvent, EnId, 
			ucase(CoName) as CoName, CoCode, 
			QuScore, QuSession, QuD1Score, QuD2Score, QuD3Score, QuD4Score, QuHits, QuGold, QuXnine, QuD1Arrowstring,
			MaxArrows, MaxDistance, MaxTargetFace, 
			IndRank, QuClRank, IndRankFinal 
		from Qualifications
		inner join Entries on EnId=QuId
        inner join Divisions on DivId=EnDivision and DivTournament=EnTournament $Filter
		inner join Countries on CoId=EnCountry and CoTournament=EnTournament
		inner join (select TfId, greatest(TfW1, TfW2, TfW3, TfW4, TfW5, TfW6, TfW7, TfW8) as MaxTargetFace from TargetFaces where TfTournament={$_SESSION['TourId']}) TargetFaces on TfId=EnTargetFace
		inner join (select DiSession, sum(DiEnds*DiArrows) as MaxArrows from DistanceInformation where DiTournament={$_SESSION['TourId']} group by DiSession) DistanceArrows on DiSession=QuSession
		inner join (select TdClasses, greatest(Td1+0, Td2+0, Td3+0, Td4+0, Td5+0, Td6+0, Td7+0, Td8+0) as MaxDistance from TournamentDistances where TdTournament={$_SESSION['TourId']}) Distances on concat(EnDivision,EnClass) like TdClasses
		left join Individuals on IndId=EnId
		where EnTournament={$_SESSION['TourId']}
		order by EnFirstName, QuSession");
	$EnCodes=array();
	while($r=safe_fetch($q)) {
        if($r->EnClass[0]=='D') {
            // Découverte/Débutant do not go in the txt file
            continue;
        }

        if (0 == $r->QuScore && '' == trim($r->QuD1Arrowstring)) {
			// Do not send archer in the txt file if score is 0 and no arrows recorded
			continue;
		}


		// check the age class
        $AgeClass=substr($r->EnAgeClass,0,-1);

        // check the shooting class
        $Class=substr($r->EnClass,0,-1);

        // Para stuff
        switch($r->EnDivision) {
            case 'OPCL':
            case 'OPCO':
            case 'OJCL':
            case 'OJCO':
                $r->EnDivision=substr($r->EnDivision, -2);
                $Class='OP';
                break;
            case 'FECL':
            case 'FECO':
            case 'FJCL':
            case 'FJCO':
                $r->EnDivision=substr($r->EnDivision, -2);
                $Class='FED';
                break;
            case 'W1':
                $r->EnDivision='1'.($r->EnSex ? 'F' : 'H');
                $Class='W1';
                break;
            case 'HV1':
                $r->EnDivision='1'.($r->EnSex ? 'F' : 'H');
                $Class='HV1';
                break;
            case 'HV2':
                $r->EnDivision='2'.($r->EnSex ? 'F' : 'H');
                $Class='HV2';
                break;
            case 'CHCL':
            case 'CHCO':
                $r->EnDivision=substr($r->EnDivision, -2);
                $Class='CHA';
                break;
            case 'CRCL':
            case 'CRCO':
                $r->EnDivision=substr($r->EnDivision, -2);
                $Class='CRI';
                break;
            case 'HLCL':
            case 'HLCO':
                $r->EnDivision=substr($r->EnDivision, -2);
                $Class='HVL';
                break;
            case 'SU1':
            case 'SU2':
                $Class=$r->EnDivision;
                $r->EnDivision='CL';
                break;
        }

        if(empty($EnCodes["{$r->EnCode}-{$r->EnDivision}"])) {
            $EnCodes["{$r->EnCode}-{$r->EnDivision}"]=0;
        }
        $EnCodes["{$r->EnCode}-{$r->EnDivision}"]++;

		// Remove rank from archers shooting more than one session
		if ($EnCodes["{$r->EnCode}-{$r->EnDivision}"] > 1) {
			$r->QuClRank = null;
			$r->IndRankFinal = null;
		}

        $Archers[$r->IndEvent][$r->EnId]=array_fill(0, 51, '');
		$Archers[$r->IndEvent][$r->EnId][0] = $Discipline;
		$Archers[$r->IndEvent][$r->EnId][1] = $_REQUEST['lev'];
		$Archers[$r->IndEvent][$r->EnId][2] = 'I'; // E if team
		$Archers[$r->IndEvent][$r->EnId][3] = ($r->EnIocCode=='FRA' ? $r->EnCode : '999999');
		$Archers[$r->IndEvent][$r->EnId][4] = $r->EnFirstName;
		$Archers[$r->IndEvent][$r->EnId][5] = $r->EnName;
		$Archers[$r->IndEvent][$r->EnId][6] = $AgeClass;
		$Archers[$r->IndEvent][$r->EnId][7] = $Class;
		$Archers[$r->IndEvent][$r->EnId][8] = $r->EnSex ? 'F' : 'H';
		$Archers[$r->IndEvent][$r->EnId][9] = $r->EnDivision;
		$Archers[$r->IndEvent][$r->EnId][11] = $r->CoName;
		$Archers[$r->IndEvent][$r->EnId][12] = $r->CoCode;
		$Archers[$r->IndEvent][$r->EnId][13] = $r->QuScore;
		$Archers[$r->IndEvent][$r->EnId][14] = 0;
		$Archers[$r->IndEvent][$r->EnId][15] = $r->QuGold;
		$Archers[$r->IndEvent][$r->EnId][16] = $r->QuXnine;
		$Archers[$r->IndEvent][$r->EnId][17] = $r->MaxDistance;
		$Archers[$r->IndEvent][$r->EnId][18] = $r->MaxTargetFace;
		$Archers[$r->IndEvent][$r->EnId][19] = date('d/m/Y', strtotime($COMP->ToWhenFrom));
		$Archers[$r->IndEvent][$r->EnId][20] = str_replace(["\n","\r"], ' / ', trim($COMP->ToVenue));
		$Archers[$r->IndEvent][$r->EnId][21] = $r->QuClRank;
		$Archers[$r->IndEvent][$r->EnId][22] = $r->QuD1Score ? $r->QuD1Score : '';
		$Archers[$r->IndEvent][$r->EnId][23] = $r->QuD2Score ? $r->QuD2Score : '';
		$Archers[$r->IndEvent][$r->EnId][24] = $r->QuD3Score ? $r->QuD3Score : '';
		$Archers[$r->IndEvent][$r->EnId][25] = $r->QuD4Score ? $r->QuD4Score : '';
		$Archers[$r->IndEvent][$r->EnId][47] = $r->IndRankFinal;
		$Archers[$r->IndEvent][$r->EnId][48] = '1'; // will always be a valid competition... set to 1 if official FFTA Ranking Category
		$Archers[$r->IndEvent][$r->EnId][49] = $r->EnDivision;
		$Archers[$r->IndEvent][$r->EnId][50] = $EnCodes["{$r->EnCode}-{$r->EnDivision}"];
	}

	// get the matches
	$q=safe_r_sql("select fl.*, el.*, if(EvMatchMode=0, fl.FinSCore, fl.FinSetScore) as Points, er.EnCode as OppCode, GrPhase
		from Finals fl
		inner join Entries el on EnId=fl.FinAthlete
		inner join Finals fr on fr.FinMatchNo=if(fl.FinMatchNo%2=0, fl.FinMatchNo+1, fl.FinMatchNo-1) and fr.FinEvent=fl.FinEvent and fr.FinTournament=fl.FinTournament
		inner join Entries er on er.EnId=fr.FinAthlete
		inner join Events on EvTeamEvent=0 and EvTournament=fl.FinTournament and EvCode=fl.FinEvent and EvCodeParent=''
		inner join Grids on GrMatchNo=fl.FinMatchNo
		where fl.FinTournament={$_SESSION['TourId']}
		order by fl.FinMatchNo");

	while($r=safe_fetch($q)) {
		if($r->GrPhase) {
			$r->GrPhase=log($r->GrPhase, 2)+1;
		}
		$i=44-(3*$r->GrPhase);
		$Archers[$r->FinEvent][$r->FinAthlete][$i]=$r->Points;
		if(trim($r->FinTiebreak)) {
			$Archers[$r->FinEvent][$r->FinAthlete][$i+1]=ValutaArrowString($r->FinTiebreak);
		}
		$Archers[$r->FinEvent][$r->FinAthlete][$i+2]=$r->OppCode;
	}

	foreach($Archers as $Events) {
		foreach($Events as $row) {
			$File[]=implode("\t", $row);
		}
	}

	$FileToSend=implode("\r\n", $File); // Windows end of line

	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=A'.$Discipline.$COMP->ToCommitee.'.txt');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . strlen($FileToSend));
	echo $FileToSend;
	exit;

	/*
	Lines:
	F	N	I	690794N	ADICEOM	AUDREY	S	S	F	CL		RIOM	1463019	659	0	27	11	70	122	08/07/2017	ST AVERTIN	1	337	322	0	0				7	1		6	1		6	1		6	1					3	0		2	1	CL	1
	F	N	I	759848X	ALART	MARGAUX	S	S	F	CL		PERPIGNAN	2066108	589	0	8	3	70	122	08/07/2017	ST AVERTIN	28	287	302	0	0				6	1		4	0														10	1	CL	1

	1.	discipline	alpa	1	car.	"S=salle,F=fita,C=campagne N=nature,B=beursault,3=3D,E=fed."
	2.	niveau compétition	alpa	1	car.	"N=national,R=régional D=départ.,C=club,I=internat."
	3.	type chpt	alpa	1	car.	I=individuel ou E=equipe
	4.	N°licence	alpha-numerique	7	car.
	5.	nom	alpha
	6.	prénom	alpha
	7.	catégorie	alpha	2	car.	B, M, C, J, S, V et SV
	8.	cat surclassement	alpha	2	car.	"surclassement ponctuel sinon même valeur que le champ 7"
	9.	sexe	alpha	1	car.	F ou H
	10.	arme	alpha	2	car.	CL,CO,AD,AC,TL,BB
	11.	niveau	alpha-numerique	2	car.
	12.	nom club tireur	alpha
	13.	N°affiliation club tireur	numérique	7	car.
	14.	score	numérique	4	car.
	15.	paille	numérique	2	car.
	16.	dix	numérique	2	car.
	17.	neuf	numérique	2	car.
	18.	distance	numérique	2	car.	18,20,25,30,50,60,70,90 (si discipline à plusieurs distances, mettre la plus longue) 1=piquet rouge 2 =piquer bleu 3=piquet blanc
	19.	blason	numérique	3	car.	"40,60,80,122 (ne rien inscrire pour les disciplines de parcours)"
	20.	date concours	date	10	car.	JJ/MM/AAAA
	21.	lieu du concours	alpha
	22.	place qualif.	numérique	3	car.
	23.	score 1er dist.	numérique	3	car.	"si discilpline est une distance courte on écrit les valeurs dans les champs 25 et 26"
	24.	score 2e dist.	numérique	3	car.
	25.	score 3e dist.	numérique	3	car.
	26.	score 4e dist.	numérique	3	car.
	27.	score 32 finale	numérique	3	car.
	28.	flèche départage 32e	numérique	2	car.
	29.	n°lic adversaire 32e	alpha-numerique	7	car.
	30.	score 16 finale	numérique	3	car.
	31.	flèche départage 16e	numérique	2	car.
	32.	n°lic adversaire 16e	alpha-numerique	7	car.
	33.	score 8 finale	numérique	3	car.
	34.	flèche départage 8e	numérique	2	car.
	35.	n°lic adversaire 8e	alpha-numerique	7	car.
	36.	score 1/4 finale	numérique	3	car.
	37.	flèche départage 1/4e	numérique	2	car.
	38.	n°lic adversaire 1/4e	alpha-numerique	7	car.
	39.	score 1/2 finale	numérique	3	car.
	40.	flèche départage 1/2e	numérique	2	car.
	41.	n°lic adversaire 1/2e	alpha-numerique	7	car.
	42.	score petite finale	numérique	3	car.
	43.	flèche départage ptte F	numérique	2	car.
	44.	n°lic adversaire ptte F	alpha-numerique	7	car.
	45.	score finale	numérique	3	car.
	46.	flèche départage finale	numérique	2	car.
	47.	n°lic adversaire finale	alpha-numerique	7	car.
	48.	place définitives	numérique	3	car.
	49.	catégorie officielle	numérique	1	car.	"1=vrai(cat fait objet d'un clt FFTA) 0=faux(cat ne fait pas l'objet d'1clt)"
	50.	arme utilisée	alpha	2	car.	idem champ 10
	51.	nombre de tir (départ)	numérique	1	car.	numero chronologique



	 */
}

$PAGE_TITLE=get_text('MenuLM_Export-FR-Results');

$JS_SCRIPT=array('<script>
function changeLUE(id) {
    if(confirm("'.get_text('MsgAreYouSure').'")) {
        document.location.href=\'?forceLUE=\'+id;
    }
}
</script>');


include('Common/Templates/head.php');

// needs to select the level of competition
echo '<table class="Tabella2">';
echo '<tr><th colspan="2" class="Title">'.$PAGE_TITLE.'</th></tr>';
echo '<tr>
	<th>'.get_text('TourLevel', 'Tournament').'</th>
	<td><select onchange="location.href=\'?lev=\'+this.value">
		<option value="">--</option>
		<option value="S">Sélectif</option>
		<option value="SP">Sélectif Para</option>
		<option value="R">Régional</option>
		<option value="D">Départemental</option>
		<option value="N">National</option>
		<option value="E">Europe</option>
		<option value="M">Monde</option>
		<option value="O">Jeux Olympiques</option>
		</select></td>
	</tr>';
echo '</table>';

echo '<table class="Tabella2">';
echo '<tr><th colspan="5" class="Title">'.get_text('MenuLM_AthletesDiscrepancies').'</th></tr>';
echo '<tr>
	<th colspan="2">'.get_text('Athlete').'</th>
	<th>'.get_text('LookupTable', 'Tournament').'</th>
	<th>'.get_text('ChangeLookUpTable', 'Tournament').'</th>
	</tr>';
$q=safe_r_sql("select * 
	from Entries
	inner join Tournament on ToId=EnTournament and EnIocCode!=Tournament.ToIocCode
	where EnTournament={$_SESSION['TourId']}");
while($r=safe_fetch($q)) {
	echo '<tr>
		<td>'.$r->EnCode.'</td>
		<td>'.$r->EnFirstName.' '.$r->EnName.'</td>
		<td>'.$r->EnIocCode.'</td>
		<td align="center"><div class="" onclick="changeLUE('.$r->EnId.')">'.$r->ToIocCode.'</div></td>
		</tr>';
}
echo '</table>';

include('Common/Templates/tail.php');
