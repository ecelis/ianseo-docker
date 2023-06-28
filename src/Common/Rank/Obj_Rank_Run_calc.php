<?php
/**
 * Obj_Rank_Run
 * Implementa l'algoritmo di default per il calcolo della rank di qualificazione assoluta individuale
 *
 * La tabella in cui vengono scritti i valori è la Individuals.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events	=> array(<ev_1>,<ev_2>...<ev_n>) || string,			[calculate/read]
 * 		dist	=> #												[calculate/read]
 * 		runningDist	=> #											[read]
 * 		tournament => #												[calculate/read]
 * 		cutRank => #												[read]
 * 		session => #												[read,non influisce su calculate]
 * 		skipExisting => #											[calculate]
 * )
 *
 * con:
 * 	 events: l'array degli eventi assoluti oppure se scalare, una stringa usata in LIKE
 * 	 dist: la distanza con 0 per indicare la rank assoluta totale totale.
 * 	 runningDist: Restituisce la classifica dopo "X" distanze a non della distanza "x" (e rimuove le impostazioni di "dist" se presenti)
 *	 tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *	 session: Se impostato ritorna la classifica di quella sessione, con la rank globale. Chi chiama se vuole ricalcolerà la rank in quella sessione
 *	 skipExisting: Se 1 non sovrascrive posizione e frecce di SO dove sono già valorizzati - Solo per Distanza = 0
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
 * Estende Obj_Rank
 */

class Obj_Rank_Run_calc extends Obj_Rank_Run {
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
		global $CFG;
		$filterDet=$this->safeFilter(true);
		$filter=$this->safeFilter();

		// resets the rank details of everybody
		safe_w_sql("update RunArcheryRank
    		inner join IrmTypes on IrmId=RarIrmType and IrmShowRank=1
			inner join (
				select sum(RaLapTime) as LapTime, sum(if(RaLapTime>0, 1, 0)) as Laps, sum(RaArrowPenalty) as TotArrowPenalty, sum(RaLoopPenalty) as TotLoopPenalty, RaEntry, RaSubTeam, RaTeam, RaEvent, RaPhase, RaTournament 
	            from RunArchery
	            where RaTournament=$this->tournament {$filterDet}
	            group by RaEntry, RaTeam, RaSubTeam, RaEvent, RaPhase
	            ) laps on RaTournament=RarTournament and RaEntry=RarEntry and RaSubTeam=RarSubTeam and RaTeam=RarTeam and RaEvent=RarEvent and RaPhase=RarPhase 
			set RarDateTimeFinish=RarDateTimeStart+greatest(LapTime, RarTimeTotal), RarLaps=Laps, RarTimeTotal=greatest(LapTime, RarTimeTotal), RarArrowTotalPenalty=TotArrowPenalty, RarLoopTotalPenalty=TotLoopPenalty, RarRank=0, RarRankClass=0, RarTimeFinal=greatest(LapTime, RarTimeTotal)+TotArrowPenalty+TotLoopPenalty+RarTimeAdjustPlus-RarTimeAdjustMinus
			where RarTournament=$this->tournament {$filter}");

		// now selects entries ordering them
		$q="SELECT RarEntry, RarSubTeam, RarTeam, RarEvent, RarPhase, RarLaps, RarTimeFinal, ClViewOrder, RarPool
			FROM RunArcheryRank
		    inner join IrmTypes on IrmId=RarIrmType and IrmShowRank=1
			left join (
			    select EnId, ClViewOrder
		        from Entries
				left join Classes on ClTournament=EnTournament and ClId=EnClass
		        where EnTournament={$this->tournament}
			) Entries on EnId=RarEntry and RarTeam=0
			WHERE RarTournament={$this->tournament}
		        AND RarTimeFinal>0
				{$filter}
			ORDER BY RarTeam, RarEvent, RarPhase, RarPool, RarLaps DESC, RarTimeFinal, ClViewOrder";
			//print $q.'<br><br>';
		$r=safe_r_sql($q);

		$curGroup = "";
		$myRank = 1;
		$myPos = 0;
		$myRankCl = [];
		$myPosCl = [];

		$myLapsOld = 0;
		$myTimeOld = 0;

		while($myRow=safe_fetch($r)) {
			$Key="{$myRow->RarTeam}-{$myRow->RarEvent}-{$myRow->RarPhase}";
			if($this->Phase!=1) {
				$Key.="-{$myRow->RarPool}";
			}
			$ClassKey=$Key."-{$myRow->ClViewOrder}";
			if ($curGroup != $Key) {
				$curGroup = $Key;

				$myRank = 1;
				$myPos = 0;
				$myLapsOld = 0;
				$myTimeOld = 0;
			}

			if(empty($myRankCl[$ClassKey])) {
				$myRankCl[$ClassKey]=1;
				$myPosCl[$ClassKey]=0;
			}
			$myPos++;
			$myPosCl[$ClassKey]++;

			if (!($myRow->RarLaps==$myLapsOld AND $myRow->RarTimeFinal==$myTimeOld)) {
                $myRank = $myPos;
				$myRankCl[$ClassKey]=($myRow->RarTeam ? $myPos : $myPosCl[$ClassKey]);
            }

            safe_w_SQL("UPDATE RunArcheryRank 
				SET RarRank=$myRank, RarRankClass={$myRankCl[$ClassKey]}
				WHERE RarTournament={$this->tournament} and RarEntry={$myRow->RarEntry} and RarSubTeam= {$myRow->RarSubTeam} and RarTeam= {$myRow->RarTeam} and RarEvent='{$myRow->RarEvent}' and RarPhase={$myRow->RarPhase}");

			$myLapsOld = $myRow->RarLaps;
			$myTimeOld = $myRow->RarTimeFinal;
		}
	}
}
