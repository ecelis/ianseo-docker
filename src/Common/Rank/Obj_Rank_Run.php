<?php
require_once('Common/Lib/ArrTargets.inc.php');
/**
 * Obj_Rank_Run
 * Implements Run Archery
 *
 * Tables involved are RunArechery and RunArcheryRank.
 *
 * $opts is as:
 *
 * array(
 * 		events	=> array([team] => array(<ev_1>,<ev_2>...<ev_n>))   [calculate/read]
 * 		tournament => #												[calculate/read]
 * 		phase => #											        [calculate/read]
 * )
 *
 * with:
 * 	 events: array of Team=>Events
 *	 tournament: optional, the ToID of the competition.
 *	 phase: phase of the event(s)
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 * 			numDist	=> <numero distanze>, inizializzato solo se c'è almeno una sezione
 * 			double	=> <1 se gara doppia 0 altrimenti>, inizializzato solo se c'è almeno una sezione
 * 			lastUpdate => timestamp dell'ultima modifica (il max tra tutte le righe)
 *		),
 * 		sections 	=> array(
 * 			event_1 => array(
 * 				meta => array(
 * 					event => <event_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					qualifiedNo => <numero di persone qualificate per l'evento>
 * 					printHeader => <testa stampa>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
 *                      session 		=> <sessione>
 *                      target 			=> <piazzola>
 *                      athlete 		=> <cognome e nome>
 *                      familyname 		=> <cognome>
 *						givenname 		=> <nome>
 *                      div				=> <codice divisione>
 *                      cl				=> <codice classe>
 *                      subclass 		=> <categoria>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      rank 			=> <rank in base alla distanza>
 *                      rankBeforeSO	=> <rank prima degli shootoff (ha senso sulla dist 0)>
 *                      score 			=> <punti in base alla distanza>
 *                      gold 			=> <ori in base alla distanza>
 *                      xnine 			=> <xnine in base alla distanza>
 *                      tiebreak		=> <frecce di tie>					(distanza 0)
 *                      ct				=> <numero di cointoss (gialli)>	(distanza 0)
 *                      so				=> <1 se shootoff (rosso)>			(distanza 0)
 *                      dist_1 			=> <rank|punti|ori|xnine della distanza 1>
 *                      dist_2 			=> <rank|punti|ori|xnine della distanza 2>
 *                      dist_3 			=> <rank|punti|ori|xnine della distanza 3>
 *                      dist_4 			=> <rank|punti|ori|xnine della distanza 4>
 *                      dist_5 			=> <rank|punti|ori|xnine della distanza 5>
 *                      dist_6 			=> <rank|punti|ori|xnine della distanza 6>
 *                      dist_7	 		=> <rank|punti|ori|xnine della distanza 7>
 *                      dist_8 			=> <rank|punti|ori|xnine della distanza 8>
 *                      hits			=> <frecce tirate (tutte se la distanza è zero oppure solo quelle della distanza passata)>
 * 					)
 *				)
 * 				items => array(
 * 					array(id=><valore>,bib=><valore>,...,dist_8=><valore>),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			event_n = ...
 * 		)
 * )
 *
 */
class Obj_Rank_Run extends Obj_Rank{
	var $Phase=0;
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
	protected function safeFilter($details=false) {
		$ret=array();

		$f=array();
		$tabPrefix=($details ? 'Ra' : 'Rar');
		if(!empty($this->opts['allEvents'])) {
			foreach ($this->opts['allEvents'] as $team => $event) {
				$f[]="({$tabPrefix}Team=$team and {$tabPrefix}Event in (".implode(',', StrSafe_DB($event))."))";
			}

			$ret[]="(" . implode(' OR ', $f) . ")";
		}

		if(!empty($this->opts['events'])) {
			$f[]="{$tabPrefix}Event in (".implode(',', StrSafe_DB($this->opts['events'])).")";

			$ret[]="(" . implode(' OR ', $f) . ")";
		}

		if(isset($this->opts['phase'])) {
			// keep this as $this->>Phase is only for calculation!
			$ret[]="{$tabPrefix}Phase={$this->opts['phase']}";
		}

		if(isset($this->opts['team'])) {
			$ret[]="{$tabPrefix}Team={$this->opts['team']}";
		}

		if($ret) {
			return ' AND ' . implode(' AND ', $ret);
		}

		return '';
	}

	public function __construct($opts){
		parent::__construct($opts);
		$this->Phase=($this->opts['phase']??0);
	}

	/**
	 * calculate().
	 * La classifica abs viene calcolata quando si calcola quella di classe e l'evento
	 * prevede la div/cl della persona coinvolta
	 * e quando si fanno gli spareggi per passare alle eliminatorie o alle finali.
	 * Nel primo caso questo è il metodo da chiamare perchè calcolerà l'IndRank o l'IndD[1-8]Rank lavorando su tutto l'evento
	 * (utilizza setRow()) altrimenti occorre usare setRow() direttamente.
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
	public function calculate() {
		return true;
	}

	/**
	 * read()
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#read()
	 */
	public function read() {
		$this->data['meta']['title']=get_text('ResultRunArchery','Tournament');
		$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
		$this->data['meta']['byClass']=false;
		$this->data['sections']=array();
		$this->data['fields']=array(
			'id'  => 'Id',
			'bib' => get_text('Code','Tournament'),
			'session' => get_text('Session'),
			'target' => get_text('Target'),
			'athlete' => get_text('Athlete'),
			'familyname' => get_text('FamilyName', 'Tournament'),
			'givenname' => get_text('Name', 'Tournament'),
			'gender' => get_text('Sex', 'Tournament'),
			'div' => get_text('Division'),
			'class' => get_text('Class'),
			'ageclass' => get_text('AgeCl'),
			'subclass' => get_text('SubCl','Tournament'),
			'countryId'  => 'CoId',
			'countryCode' => get_text('CountryCode'),
			'countryName' => get_text('Country'),
			'rank' => get_text('PositionShort'),
			'oldRank' => '',
			'rankBeforeSO' => '',
			'hits' => get_text('Arrows','Tournament'),
			'FinalTime' => get_text('FinalTime','RunArchery'),
			'RunningTime' => get_text('RunningTime','RunArchery'),
			'PenaltyTime' => get_text('PenaltyTime','RunArchery'),
			'ArrowShort' => get_text('ArrowShort','RunArchery'),
			'LoopShort' => get_text('LoopShort','RunArchery'),
			'AdjustedTime' => get_text('AdjustedTime','RunArchery'),
		);

		if(empty($this->opts['byclass']) or count($this->opts['events']??[])==1) {
			$this->read_normal();
		}
		if(!empty($this->opts['byclass'])) {
			$this->read_byclass();
		}

		return $this->data;
	}
	public function read_normal() {
		$ConfirmStatus=0;
		$filter=$this->safeFilter();

		if(!isset($this->opts['team']) or $this->opts['team']==0) {
			// First Individuals
			$SQL="SELECT EnId, EnCode, EnTournament, coalesce(EdExtra, EnCode) as LocalBib, RarBib, if(EnDob=0, '', EnDob) as BirthDate, EnOdfShortname, EnSex, EnNameOrder, upper(EnIocCode) EnIocCode, EnName AS Name, EnFirstName AS FirstName, upper(EnFirstName) AS FirstNameUpper,
					EnClass, EnDivision,EnAgeClass,  EnSubClass,
					RarEvent, RarPhase, RarStartlist, RarDateTimeStart, RarDateTimeFinish, RarTimeTotal, RarArrowTotalPenalty, RarLoopTotalPenalty, RarTimeAdjustPlus, RarTimeAdjustMinus,
					RarTimeFinal, RarLaps, RarRank, RarPoints, RarLastUpdate, RarPool, RarQualified,
					RaLap, RaLapTime, RaArrowsShot, RaHits, RaLoopAssigned, RaLoopDone, RaArrowPenalty, RaLoopPenalty, RaLastUpdate,
					CoId, CoCode, CoName, CoMaCode, CoCaCode, ClDescription,
					FlContAssoc, IrmType, IrmShowRank, RarIrmType,
					EvProgr, EvCode,EvEventName,EvRunning, EvFinalFirstPhase, EvElim1, EvElim2, EvIsPara,EvTargetSize,EvFinEnds,EvFinArrows,EvFinSO,EvE1Arrows,EvDistance,EvShootOff,EvE1ShootOff,EvE2ShootOff,
					EvFirstQualified, EvQualPrintHead as PrintHeader,
					ToNumEnds,ToNumDist,ToMaxDistScore, ToGolds AS GoldLabel, ToXNine AS XNineLabel, ToDouble,
					concat(DvMajVersion, '.', DvMinVersion) as DocVersion, date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate, DvNotes as DocNotes
				FROM RunArcheryRank
				INNER JOIN Events ON EvCode=RarEvent AND EvTeamEvent=RarTeam AND EvTournament=RarTournament
			    inner join RunArchery on RaTournament=RarTournament and RaEntry=RarEntry and RaSubTeam=RarSubTeam and RaTeam=RarTeam and RaEvent=RarEvent and RaPhase=RarPhase
			    inner join IrmTypes on IrmId=RarIrmType
			    inner join Entries on EnId=RarEntry and EnTournament=RarTournament and EnAthlete=1 AND EnStatus <= 1
				left JOIN Countries NOC ON NOC.CoId=
				    case EvTeamCreationMode 
				        when 0 then EnCountry
				        when 1 then EnCountry2
				        when 2 then EnCountry3
				        else EnCountry
                    end
                    AND NOC.CoTournament={$this->tournament}
			    inner join Classes on ClTournament=EnTournament and ClId=EnClass
				left join ExtraData on EdId=EnId and EdType='Z'
				INNER JOIN Tournament ON ToId=RarTournament
				LEFT JOIN DocumentVersions on EvTournament=DvTournament AND DvFile = 'RUN-0' and DvEvent=RarEvent
				LEFT JOIN Flags ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=ToId
				WHERE RarRank > 0 and RarTeam=0
					AND RarTournament = {$this->tournament}
					{$filter}
				ORDER BY
					EvProgr, EvCode, if(RarPhase=0, 1, if(RarPhase=2, 2, 3)), RarPool, RarRank, RaLap";
			$q=safe_r_sql($SQL);
			while ($myRow=safe_fetch($q)) {
				$KEY=$myRow->EvCode;
				// an event is made of one or more phases (Qual, semi and finals)
				if (empty($this->data['sections'][0][$KEY])) {
				    // Inserts the section
					$this->data['sections'][0][$KEY]['phases']= array();
					$this->data['sections'][0][$KEY]['meta']= array(
						'event' => $KEY,
						'evCode' => $myRow->EvCode,
						'class' => '',
						'className' => '',
						'lastPhase' => $myRow->EvFinalFirstPhase,
						'lapDistance' => $myRow->EvTargetSize,
						'lapNum' => $myRow->EvFinEnds,
						'descr' => $myRow->EvEventName,
						'arrowsAvailable' => $myRow->EvFinArrows,
						'targets' => $myRow->EvE1Arrows,
						'penaltyLoopDistance'=> $myRow->EvDistance,
						'qualifiedSemifinals' => $myRow->EvElim1,
						'qualifiedFinals' => $myRow->EvElim2,
						'qualEnded' => $myRow->EvShootOff,
						'semiEnded' => $myRow->EvE2ShootOff,
						'finalEnded' => $myRow->EvE1ShootOff,
						// 'scheduledStart' => $myRow->RarStartlist,
						// 'actualStart' => $myRow->RarStart,
						// 'actualFinish' => $myRow->RarFinish,
						// 'rawTime' => $myRow->RarTimeTotal,
						// 'arrPenalties' => $myRow->RarArrowTotalPenalty,
						// 'loopPenalties' => $myRow->RarLoopTotalPenalty,
						// 'plusTime' => $myRow->RarTimeAdjustPlus,
						// 'minusTime' => $myRow->RarTimeAdjustMinus,
						// 'finalTime' => $myRow->RarTimeFinal,
						// 'rank' => $myRow->RarRank,
						'version' => $myRow->DocVersion,
						'versionDate' => $myRow->DocVersionDate,
						'versionNotes' => $myRow->DocNotes,
						'lastUpdate' => '0000-00-00 00:00:00',
						);

				}
				if(empty($this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase])) {
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['name']=get_text('PhaseName-'.$myRow->RarPhase, 'RunArchery');
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['lastUpdate']='';
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['Pools']=[];
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']=array();
				}
				if($myRow->RarPool and empty($this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['Pools'][$myRow->RarPool])) {
					if($myRow->RarPhase==1) {
						// finals
						$Name=get_text('Final'.$myRow->RarPool, 'RunArchery');
					} else {
						$Name=get_text('PoolName', 'Tournament', $myRow->RarPool);
					}
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['Pools'][$myRow->RarPool]=$Name;
				}

				if(empty($this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"])) {
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]=array(
						'id'  => $myRow->EnId,
						'encode' => $myRow->EnCode,
						'localbib' => $myRow->LocalBib,
						'bib' => trim($myRow->RarBib),
						'tvname' => $myRow->EnOdfShortname,
						'birthdate' => $myRow->BirthDate,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'div' => $myRow->EnDivision,
						'class' => $myRow->EnClass,
						'className' => $myRow->ClDescription,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
						'countryId' => $myRow->CoId,
						'countryCode' => $myRow->CoCode,
						'contAssoc' => $myRow->CoCaCode,
						'memberAssoc' => $myRow->CoMaCode,
						'countryIocCode' => $myRow->EnIocCode,
						'countryName' => $myRow->CoName,
						'rank' => $myRow->RarRank,
						'FinalTime' => ltrim(date_format(date_create_from_format('U.u', $myRow->RarTimeFinal), 'H:i:s.v'),'0:'),
						'RunningTime' => ltrim(date_format(date_create_from_format('U.u', $myRow->RarTimeTotal), 'H:i:s.v'),'0:'),
						'PenaltyTime' => '',
						'ArrowShort' => '',
						'LoopShort' => '',
						'AdjustedTime' => '',
						'irm' => $myRow->IrmType,
						'Pool' => $myRow->RarPool,
						'Qualified' => $myRow->RarQualified,
						'laps' => array(),
					);
					if($myRow->IrmShowRank) {
						if($myRow->RarArrowTotalPenalty>0) {
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['ArrowShort']=ltrim(date_format(date_create_from_format('U.u', $myRow->RarArrowTotalPenalty), 'H:i:s.v'),'0:');
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['PenaltyTime']=ltrim(date_format(date_create_from_format('U.u', number_format($myRow->RarArrowTotalPenalty+$myRow->RarLoopTotalPenalty, 3,'.','')), 'H:i:s.v'),'0:');
						}
						if($myRow->RarLoopTotalPenalty>0) {
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['LoopShort']=ltrim(date_format(date_create_from_format('U.u', $myRow->RarLoopTotalPenalty), 'H:i:s.v'),'0:');
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['PenaltyTime']=ltrim(date_format(date_create_from_format('U.u', number_format($myRow->RarArrowTotalPenalty+$myRow->RarLoopTotalPenalty, 3,'.','')), 'H:i:s.v'),'0:');
						}
						if($myRow->RarTimeAdjustPlus+$myRow->RarTimeAdjustMinus) {
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['AdjustedTime']=($myRow->RarTimeAdjustPlus<$myRow->RarTimeAdjustMinus ? '-' : '').ltrim(date_format(date_create_from_format('U.u', number_format(abs($myRow->RarTimeAdjustPlus - $myRow->RarTimeAdjustMinus),3,'.','')), 'H:i:s.v'),'0:');
						}
					} else {
						$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['rank']=$myRow->IrmType;
						$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['FinalTime']='';
						$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['RunningTime']='';
					}
				}

				$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['laps'][$myRow->RaLap]=[
					'lap' => $myRow->RaLap,
					'time' => $myRow->RaLapTime==0 ? '' : ltrim(date_format(date_create_from_format('U.u', $myRow->RaLapTime), 'H:i:s.v'),'0:'),
					'shots' => $myRow->RaArrowsShot,
					'hits' => $myRow->RaHits,
					'loopsToDo' => $myRow->RaLoopAssigned,
					'loopsDone' => $myRow->RaLoopDone,
					'ArrPenalty' => $myRow->RaArrowPenalty==0 ? '' : ltrim(date_format(date_create_from_format('U.u', $myRow->RaArrowPenalty), 'H:i:s.v'),'0:'),
					'LoopPenalty' => $myRow->RaLoopPenalty==0 ? '' : ltrim(date_format(date_create_from_format('U.u', $myRow->RaLoopPenalty), 'H:i:s.v'),'0:'),
					'lastUpdate' => $myRow->RaLastUpdate,
					];

				if ($myRow->RarLastUpdate>$this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate']=$myRow->RarLastUpdate;
				}
				if ($myRow->RarLastUpdate>$this->data['sections'][0][$KEY]['meta']['lastUpdate']) {
					$this->data['sections'][0][$KEY]['meta']['lastUpdate']=$myRow->RarLastUpdate;
				}
				if ($myRow->RarLastUpdate>$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['lastUpdate']) {
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['lastUpdate']=$myRow->RarLastUpdate;
				}
			}
		}

		if(!isset($this->opts['team']) or $this->opts['team']==1) {
			// then the teams
			$SQL="SELECT E1.EnId, E1.EnCode, E1.EnTournament, RarBib, if(E1.EnDob=0, '', E1.EnDob) as BirthDate, 
	                E1.EnOdfShortname, E1.EnSex, E1.EnNameOrder, upper(E1.EnIocCode) EnIocCode, E1.EnName AS Name, E1.EnFirstName AS FirstName, upper(E1.EnFirstName) AS FirstNameUpper,
					E1.EnClass, E1.EnDivision, E1.EnAgeClass, E1.EnSubClass,
					E2.EnFirstName as RaFamName, E2.EnName as RaGivName, E2.EnCode as RaEnCode,
					TeCoId, TeSubTeam,
					RarEvent, RarPhase, RarStartlist, RarDateTimeStart, RarDateTimeFinish, RarTimeTotal, RarArrowTotalPenalty, RarLoopTotalPenalty, RarTimeAdjustPlus, RarTimeAdjustMinus,
					RarTimeFinal, RarLaps, RarRank, RarPoints, RarLastUpdate, 
					RaLap, RaLapTime, RaArrowsShot, RaHits, RaLoopAssigned, RaLoopDone, RaArrowPenalty, RaLoopPenalty, RaLastUpdate,
					CoId, CoCode, CoName, CoMaCode, CoCaCode, 
					FlContAssoc, IrmType, IrmShowRank, RarIrmType,
					EvProgr, EvCode,EvEventName,EvRunning, EvFinalFirstPhase, EvElim1, EvElim2, EvIsPara,EvTargetSize,EvFinEnds,EvFinArrows,EvFinSO,EvE1Arrows,EvDistance,EvShootOff,EvE1ShootOff,EvE2ShootOff,
					EvFirstQualified, EvQualPrintHead as PrintHeader,
					ToNumEnds,ToNumDist,ToMaxDistScore, ToGolds AS GoldLabel, ToXNine AS XNineLabel, ToDouble,
					concat(DvMajVersion, '.', DvMinVersion) as DocVersion, date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate, DvNotes as DocNotes
				FROM RunArcheryRank
			    inner join IrmTypes on IrmId=RarIrmType
				INNER JOIN Events ON EvCode=RarEvent AND EvTeamEvent=RarTeam AND EvTournament=RarTournament
			    inner join RunArchery on RaTournament=RarTournament and RaEntry=RarEntry and RaSubTeam=RarSubTeam and RaTeam=RarTeam and RaEvent=RarEvent and RaPhase=RarPhase
			    inner join Teams on TeCoId=RarEntry and TeSubTeam=RarSubTeam and TeTournament=RarTournament and TeEvent=RarEvent and TeFinEvent=1
			    inner join TeamFinComponent on TfcCoId=TeCoId and TfcSubTeam=TeSubTeam and TfcTournament=TeTournament and TfcEvent=TeEvent
			    inner join Entries E1 on E1.EnId=TfcId and E1.EnTournament=TfcTournament
				INNER JOIN Countries ON CoId=RarEntry AND CoTournament={$this->tournament}
				INNER JOIN Tournament ON ToId=RarTournament
				LEFT JOIN DocumentVersions on EvTournament=DvTournament AND DvFile = 'RUN-1' and DvEvent=RarEvent
				LEFT JOIN Flags ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=ToId
				LEFT JOIN Entries E2 ON E2.EnId=RaArcher and E2.EnTournament=RaTournament
				WHERE RarRank > 0 and RarTeam=1
					AND RarTournament = {$this->tournament}
					{$filter}
				ORDER BY
					EvProgr, EvCode, RarPhase, RarRank, RaLap, CoCode, TeSubTeam";
			$q=safe_r_sql($SQL);
			while ($myRow=safe_fetch($q)) {
				// an event is made of one or more phases (Qual, semi and finals)
				if (empty($this->data['sections'][1][$myRow->EvCode])) {
					// Inserts the section
					$this->data['sections'][1][$myRow->EvCode]['phases']= array();
					$this->data['sections'][1][$myRow->EvCode]['teams']= array();
					$this->data['sections'][1][$myRow->EvCode]['meta']= array(
						'event' => $myRow->EvCode,
						'lastPhase' => $myRow->EvFinalFirstPhase,
						'lapDistance' => $myRow->EvTargetSize,
						'lapNum' => $myRow->EvFinEnds,
						'descr' => $myRow->EvEventName,
						'arrowsAvailable' => $myRow->EvFinArrows,
						'arrowsSpare' => $myRow->EvFinSO,
						'targets' => $myRow->EvE1Arrows,
						'penaltyLoopDistance'=> $myRow->EvDistance,
						'qualifiedSemifinals' => $myRow->EvElim1,
						'qualifiedFinals' => $myRow->EvElim2,
						'qualEnded' => $myRow->EvShootOff,
						'semiEnded' => $myRow->EvE2ShootOff,
						'finalEnded' => $myRow->EvE1ShootOff,
						// 'scheduledStart' => $myRow->RarStartlist,
						// 'actualStart' => $myRow->RarStart,
						// 'actualFinish' => $myRow->RarFinish,
						// 'rawTime' => $myRow->RarTimeTotal,
						// 'arrPenalties' => $myRow->RarArrowTotalPenalty,
						// 'loopPenalties' => $myRow->RarLoopTotalPenalty,
						// 'plusTime' => $myRow->RarTimeAdjustPlus,
						// 'minusTime' => $myRow->RarTimeAdjustMinus,
						// 'finalTime' => $myRow->RarTimeFinal,
						// 'rank' => $myRow->RarRank,
						'version' => $myRow->DocVersion,
						'versionDate' => $myRow->DocVersionDate,
						'versionNotes' => $myRow->DocNotes,
						'lastUpdate' => '0000-00-00 00:00:00',
					);

				}
				if(empty($this->data['sections'][1][$myRow->RarEvent]['teams']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"][$myRow->EnId])) {
					$this->data['sections'][1][$myRow->RarEvent]['teams']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"][$myRow->EnId]=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'tvname' => $myRow->EnOdfShortname,
						'birthdate' => $myRow->BirthDate,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'div' => $myRow->EnDivision,
						'class' => $myRow->EnClass,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
					);
				}
				if(empty($this->data['sections'][1][$myRow->EvCode]['phases'][$myRow->RarPhase])) {
					$this->data['sections'][1][$myRow->EvCode]['phases'][$myRow->RarPhase]['name']=get_text('PhaseName-'.$myRow->RarPhase, 'RunArchery');
					$this->data['sections'][1][$myRow->EvCode]['phases'][$myRow->RarPhase]['lastUpdate']='';
					$this->data['sections'][1][$myRow->EvCode]['phases'][$myRow->RarPhase]['items']=array();
				}

				if(empty($this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"])) {
					$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]=array(
						'id'  => $myRow->TeCoId,
						'subteam'  => $myRow->TeSubTeam,
						'bib' => $myRow->RarBib,
						'tvname' => $myRow->CoName,
						'countryCode' => $myRow->CoCode.($myRow->TeSubTeam?($myRow->TeSubTeam+1):''),
						'contAssoc' => $myRow->CoCaCode,
						'memberAssoc' => $myRow->CoMaCode,
						'countryName' => $myRow->CoName.($myRow->TeSubTeam?' ('.($myRow->TeSubTeam+1).')':''),
						'rank' => $myRow->RarRank,
						'FinalTime' => ltrim(date_format(date_create_from_format('U.u', $myRow->RarTimeFinal), 'H:i:s.v'),'0:'),
						'RunningTime' => ltrim(date_format(date_create_from_format('U.u', $myRow->RarTimeTotal), 'H:i:s.v'),'0:'),
						'PenaltyTime' => '',
						'ArrowShort' => '',
						'LoopShort' => '',
						'AdjustedTime' => '',
						'laps' => array(),
					);
					if($myRow->IrmShowRank) {
						if($myRow->RarArrowTotalPenalty>0) {
							$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['ArrowShort']=ltrim(date_format(date_create_from_format('U.u', $myRow->RarArrowTotalPenalty), 'H:i:s.v'),'0:');
							$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['PenaltyTime']=ltrim(date_format(date_create_from_format('U.u', number_format($myRow->RarArrowTotalPenalty+$myRow->RarLoopTotalPenalty,3,'.','')), 'H:i:s.v'),'0:');
						}
						if($myRow->RarLoopTotalPenalty>0) {
							$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['LoopShort']=ltrim(date_format(date_create_from_format('U.u', $myRow->RarLoopTotalPenalty), 'H:i:s.v'),'0:');
							$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['PenaltyTime']=ltrim(date_format(date_create_from_format('U.u', number_format($myRow->RarArrowTotalPenalty+$myRow->RarLoopTotalPenalty,3,'.','')), 'H:i:s.v'),'0:');
						}
						if($myRow->RarTimeAdjustPlus+$myRow->RarTimeAdjustMinus) {
							$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['AdjustedTime']=($myRow->RarTimeAdjustPlus<$myRow->RarTimeAdjustMinus ? '-' : '').ltrim(date_format(date_create_from_format('U.u', number_format(abs($myRow->RarTimeAdjustPlus - $myRow->RarTimeAdjustMinus),3,'.','')), 'H:i:s.v'),'0:');
						}
					} else {
						$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['rank']=$myRow->IrmType;
						$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['FinalTime']='';
						$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['RunningTime']='';
					}
				}

				$this->data['sections'][1][$myRow->RarEvent]['phases'][$myRow->RarPhase]['items']["id-{$myRow->TeCoId}-{$myRow->TeSubTeam}"]['laps'][$myRow->RaLap]=[
					'lap' => $myRow->RaLap,
					'time' => $myRow->RaLapTime==0 ? '' : ltrim(date_format(date_create_from_format('U.u', $myRow->RaLapTime), 'H:i:s.v'),'0:'),
					'shots' => $myRow->RaArrowsShot,
					'hits' => $myRow->RaHits,
					'loopsToDo' => $myRow->RaLoopAssigned,
					'loopsDone' => $myRow->RaLoopDone,
					'ArrPenalty' => $myRow->RaArrowPenalty==0 ? '' : ltrim(date_format(date_create_from_format('U.u', $myRow->RaArrowPenalty), 'H:i:s.v'),'0:'),
					'LoopPenalty' => $myRow->RaLoopPenalty==0 ? '' : ltrim(date_format(date_create_from_format('U.u', $myRow->RaLoopPenalty), 'H:i:s.v'),'0:'),
					'lastUpdate' => $myRow->RaLastUpdate,
					'famName' => $myRow->RaFamName,
					'givName' => $myRow->RaGivName,
					'enCode' => $myRow->RaEnCode,
				];
				//Gestisco il numero di frecce tirate per sessione
				// if(empty($section["meta"]["arrowsShot"][$myRow->Session]) or $section["meta"]["arrowsShot"][$myRow->Session]<=$myRow->RarLaps)
				// 	$section["meta"]["arrowsShot"][$myRow->Session] = $myRow->Arrows_Shot;


				if ($myRow->RarLastUpdate>$this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate']=$myRow->RarLastUpdate;
				}
				if ($myRow->RarLastUpdate>$this->data['sections'][1][$myRow->EvCode]['meta']['lastUpdate']) {
					$this->data['sections'][1][$myRow->EvCode]['meta']['lastUpdate']=$myRow->RarLastUpdate;
				}
				if ($myRow->RarLastUpdate>$this->data['sections'][1][$myRow->EvCode]['phases'][$myRow->RarPhase]['lastUpdate']) {
					$this->data['sections'][1][$myRow->EvCode]['phases'][$myRow->RarPhase]['lastUpdate']=$myRow->RarLastUpdate;
				}
			}
		}

		return $this->data;
	}
	public function read_byclass() {
		$ConfirmStatus=0;
		$filter=$this->safeFilter();

		if(!isset($this->opts['team']) or $this->opts['team']==0) {
			// First Individuals
			$SQL="SELECT EnId, EnCode, EnTournament, coalesce(EdExtra, EnCode) as LocalBib, RarBib, if(EnDob=0, '', EnDob) as BirthDate, EnOdfShortname, EnSex, EnNameOrder, upper(EnIocCode) EnIocCode, EnName AS Name, EnFirstName AS FirstName, upper(EnFirstName) AS FirstNameUpper,
					EnClass, EnDivision,EnAgeClass,  EnSubClass,
					RarEvent, RarPhase, RarStartlist, RarDateTimeStart, RarDateTimeFinish, RarTimeTotal, RarArrowTotalPenalty, RarLoopTotalPenalty, RarTimeAdjustPlus, RarTimeAdjustMinus,
					RarTimeFinal, RarLaps, concat(RarRankClass, ' (', RarRank, ')') as RarRank, RarPoints, RarLastUpdate, RarPool, RarQualified,
					RaLap, RaLapTime, RaArrowsShot, RaHits, RaLoopAssigned, RaLoopDone, RaArrowPenalty, RaLoopPenalty, RaLastUpdate,
					CoId, CoCode, CoName, CoMaCode, CoCaCode, ClDescription, 
					FlContAssoc, IrmType, IrmShowRank, RarIrmType,
					EvProgr, EvCode,EvEventName,EvRunning, EvFinalFirstPhase, EvElim1, EvElim2, EvIsPara,EvTargetSize,EvFinEnds,EvFinArrows,EvFinSO,EvE1Arrows,EvDistance,EvShootOff,EvE1ShootOff,EvE2ShootOff,
					EvFirstQualified, EvQualPrintHead as PrintHeader,
					ToNumEnds,ToNumDist,ToMaxDistScore, ToGolds AS GoldLabel, ToXNine AS XNineLabel, ToDouble,
					concat(DvMajVersion, '.', DvMinVersion) as DocVersion, date_format(DvPrintDateTime, '%e %b %Y %H:%i UTC') as DocVersionDate, DvNotes as DocNotes
				FROM RunArcheryRank
				INNER JOIN Events ON EvCode=RarEvent AND EvTeamEvent=RarTeam AND EvTournament=RarTournament
			    inner join RunArchery on RaTournament=RarTournament and RaEntry=RarEntry and RaSubTeam=RarSubTeam and RaTeam=RarTeam and RaEvent=RarEvent and RaPhase=RarPhase
			    inner join IrmTypes on IrmId=RarIrmType
			    inner join Entries on EnId=RarEntry and EnTournament=RarTournament and EnAthlete=1 AND EnStatus <= 1
				left JOIN Countries NOC ON NOC.CoId=
				    case EvTeamCreationMode 
				        when 0 then EnCountry
				        when 1 then EnCountry2
				        when 2 then EnCountry3
				        else EnCountry
                    end
                    AND NOC.CoTournament={$this->tournament}
			    inner join Classes on ClTournament=EnTournament and ClId=EnClass
				left join ExtraData on EdId=EnId and EdType='Z'
				INNER JOIN Tournament ON ToId=RarTournament
				LEFT JOIN DocumentVersions on EvTournament=DvTournament AND DvFile = 'RUN-0' and DvEvent=RarEvent
				LEFT JOIN Flags ON FlIocCode='FITA' and FlCode=CoCode and FlTournament=ToId
				WHERE RarRank > 0 and RarTeam=0
					AND RarTournament = {$this->tournament}
					{$filter}
				ORDER BY
					EvProgr, EvCode, ClViewOrder, if(RarPhase=0, 1, if(RarPhase=2, 2, 3)), RarPool, RarRankClass, RaLap";
			$q=safe_r_sql($SQL);
			while ($myRow=safe_fetch($q)) {
				$KEY="{$myRow->EvCode}-{$myRow->EnClass}";
				// an event is made of one or more phases (Qual, semi and finals)
				if (empty($this->data['sections'][0][$KEY])) {
				    // Inserts the section
					$this->data['sections'][0][$KEY]['phases']= array();
					$this->data['sections'][0][$KEY]['meta']= array(
						'event' => $KEY,
						'evCode' => $myRow->EvCode,
						'class' => $myRow->EnClass,
						'className' => $myRow->ClDescription,
						'lastPhase' => $myRow->EvFinalFirstPhase,
						'lapDistance' => $myRow->EvTargetSize,
						'lapNum' => $myRow->EvFinEnds,
						'descr' => $myRow->EvEventName,
						'arrowsAvailable' => $myRow->EvFinArrows,
						'targets' => $myRow->EvE1Arrows,
						'penaltyLoopDistance'=> $myRow->EvDistance,
						'qualifiedSemifinals' => $myRow->EvElim1,
						'qualifiedFinals' => $myRow->EvElim2,
						'qualEnded' => $myRow->EvShootOff,
						'semiEnded' => $myRow->EvE2ShootOff,
						'finalEnded' => $myRow->EvE1ShootOff,
						// 'scheduledStart' => $myRow->RarStartlist,
						// 'actualStart' => $myRow->RarStart,
						// 'actualFinish' => $myRow->RarFinish,
						// 'rawTime' => $myRow->RarTimeTotal,
						// 'arrPenalties' => $myRow->RarArrowTotalPenalty,
						// 'loopPenalties' => $myRow->RarLoopTotalPenalty,
						// 'plusTime' => $myRow->RarTimeAdjustPlus,
						// 'minusTime' => $myRow->RarTimeAdjustMinus,
						// 'finalTime' => $myRow->RarTimeFinal,
						// 'rank' => $myRow->RarRank,
						'version' => $myRow->DocVersion,
						'versionDate' => $myRow->DocVersionDate,
						'versionNotes' => $myRow->DocNotes,
						'lastUpdate' => '0000-00-00 00:00:00',
						);

				}
				if(empty($this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase])) {
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['name']=get_text('PhaseName-'.$myRow->RarPhase, 'RunArchery');
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['lastUpdate']='';
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['Pools']=[];
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']=array();
				}
				if($myRow->RarPool and empty($this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['Pools'][$myRow->RarPool])) {
					if($myRow->RarPhase==1) {
						// finals
						$Name=get_text('Final'.$myRow->RarPool, 'RunArchery');
					} else {
						$Name=get_text('PoolName', 'Tournament', $myRow->RarPool);
					}
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['Pools'][$myRow->RarPool]=$Name;
				}

				if(empty($this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"])) {
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]=array(
						'id'  => $myRow->EnId,
						'encode' => $myRow->EnCode,
						'localbib' => $myRow->LocalBib,
						'bib' => trim($myRow->RarBib),
						'tvname' => $myRow->EnOdfShortname,
						'birthdate' => $myRow->BirthDate,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'div' => $myRow->EnDivision,
						'class' => $myRow->EnClass,
						'className' => $myRow->ClDescription,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
						'countryId' => $myRow->CoId,
						'countryCode' => $myRow->CoCode,
						'contAssoc' => $myRow->CoCaCode,
						'memberAssoc' => $myRow->CoMaCode,
						'countryIocCode' => $myRow->EnIocCode,
						'countryName' => $myRow->CoName,
						'rank' => $myRow->RarRank,
						'FinalTime' => ltrim(date_format(date_create_from_format('U.u', $myRow->RarTimeFinal), 'H:i:s.v'),'0:'),
						'RunningTime' => ltrim(date_format(date_create_from_format('U.u', $myRow->RarTimeTotal), 'H:i:s.v'),'0:'),
						'PenaltyTime' => '',
						'ArrowShort' => '',
						'LoopShort' => '',
						'AdjustedTime' => '',
						'irm' => $myRow->IrmType,
						'Pool' => $myRow->RarPool,
						'Qualified' => $myRow->RarQualified,
						'laps' => array(),
					);
					if($myRow->IrmShowRank) {
						if($myRow->RarArrowTotalPenalty>0) {
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['ArrowShort']=ltrim(date_format(date_create_from_format('U.u', $myRow->RarArrowTotalPenalty), 'H:i:s.v'),'0:');
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['PenaltyTime']=ltrim(date_format(date_create_from_format('U.u', number_format($myRow->RarArrowTotalPenalty+$myRow->RarLoopTotalPenalty, 3,'.','')), 'H:i:s.v'),'0:');
						}
						if($myRow->RarLoopTotalPenalty>0) {
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['LoopShort']=ltrim(date_format(date_create_from_format('U.u', $myRow->RarLoopTotalPenalty), 'H:i:s.v'),'0:');
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['PenaltyTime']=ltrim(date_format(date_create_from_format('U.u', number_format($myRow->RarArrowTotalPenalty+$myRow->RarLoopTotalPenalty, 3,'.','')), 'H:i:s.v'),'0:');
						}
						if($myRow->RarTimeAdjustPlus+$myRow->RarTimeAdjustMinus) {
							$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['AdjustedTime']=($myRow->RarTimeAdjustPlus<$myRow->RarTimeAdjustMinus ? '-' : '').ltrim(date_format(date_create_from_format('U.u', number_format(abs($myRow->RarTimeAdjustPlus - $myRow->RarTimeAdjustMinus),3,'.','')), 'H:i:s.v'),'0:');
						}
					} else {
						$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['rank']=$myRow->IrmType;
						$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['FinalTime']='';
						$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['RunningTime']='';
					}
				}

				$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['items']["id-{$myRow->EnId}"]['laps'][$myRow->RaLap]=[
					'lap' => $myRow->RaLap,
					'time' => $myRow->RaLapTime,
					'shots' => $myRow->RaArrowsShot,
					'hits' => $myRow->RaHits,
					'loopsToDo' => $myRow->RaLoopAssigned,
					'loopsDone' => $myRow->RaLoopDone,
					'ArrPenalty' => $myRow->RaArrowPenalty,
					'LoopPenalty' => $myRow->RaLoopPenalty,
					'lastUpdate' => $myRow->RaLastUpdate,
					];

				if ($myRow->RarLastUpdate>$this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate']=$myRow->RarLastUpdate;
				}
				if ($myRow->RarLastUpdate>$this->data['sections'][0][$KEY]['meta']['lastUpdate']) {
					$this->data['sections'][0][$KEY]['meta']['lastUpdate']=$myRow->RarLastUpdate;
				}
				if ($myRow->RarLastUpdate>$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['lastUpdate']) {
					$this->data['sections'][0][$KEY]['phases'][$myRow->RarPhase]['lastUpdate']=$myRow->RarLastUpdate;
				}
			}
		}

		return $this->data;
	}
}
