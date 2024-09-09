<?php
/**
 * Obj_Rank_Abs
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
class Obj_Rank_Robin_3_SetFRD12023_calc extends Obj_Rank_Robin_3_SetFRD12023 {
	public function calculate() {
		$Team=$this->opts['team'];
		$Event=$this->opts['event'];
		$Level=$this->opts['level'];
		if($Level==0) {
			if($Team) {
				Obj_RankFactory::create('FinalTeam', array('eventsC' => [$Event.'@-3']))->calculate();
			} else {
				Obj_RankFactory::create('FinalInd', array('eventsC' => [$Event.'@-3']))->calculate();
			}
			return;
		}

		// Rank is always the sum of the points of all levels
		$q=safe_r_sql("select RrMatchAthlete, RrMatchSubTeam, sum(RrMatchRoundPoints) as Points, sum(RrMatchTieBreaker) as TieBreaker, sum(RrMatchTieBreaker2) as TieBreaker2
			from RoundRobinMatches
			where RrMatchTeam=$Team and RrMatchEvent='$Event' and RrMatchLevel>0 and RrMatchTournament={$this->tournament} and RrMatchAthlete!=0
			group by RrMatchAthlete, RrMatchSubTeam
			order by Points desc, TieBreaker desc, TieBreaker2 desc");
		$rank=0;
		$i=0;
		$oldStatus='';
		while($r=safe_fetch($q)) {
			if($rank or $r->Points) {
				$i++;
			}
			if($oldStatus!="{$r->Points}-{$r->TieBreaker}-{$r->TieBreaker2}") {
				$rank+=$i;
				$i=0;
			}
			$oldStatus="{$r->Points}-{$r->TieBreaker}-{$r->TieBreaker2}";
			if($Team) {
				safe_w_sql("UPDATE Teams
					SET TeRankFinal=$rank, TeScore=$r->Points, TeGold=$r->TieBreaker, TeXnine=$r->TieBreaker2, TeTimestampFinal=TeTimestampFinal
					WHERE TeTournament={$this->tournament} AND TeEvent='{$Event}' AND TeFinEvent=1 and TeCoId=$r->RrMatchAthlete and TeSubTeam=$r->RrMatchSubTeam");

				if(safe_w_affected_rows()) {
					safe_w_sql("UPDATE Teams
						SET TeTimestampFinal=now()
						WHERE TeTournament={$this->tournament} AND TeEvent='{$Event}' AND TeFinEvent=1 and TeCoId=$r->RrMatchAthlete and TeSubTeam=$r->RrMatchSubTeam");
				}
			} else {
				safe_w_sql("UPDATE Individuals
					SET IndRankFinal=$rank, IndTimestampFinal=IndTimestampFinal
					WHERE IndTournament={$this->tournament} AND IndEvent='{$Event}' AND IndId=$r->RrMatchAthlete");
				if(safe_w_affected_rows()) {
					safe_w_sql("UPDATE Individuals
						SET IndTimestampFinal=now()
						WHERE IndTournament={$this->tournament} AND IndEvent='{$Event}' AND IndId=$r->RrMatchAthlete");
				}
			}
		}

		return true;
	}

	public function calculateGroup() {
		$MatchFilter=$this->safeFilter('Match', 'C');
		$PartFilter=$this->safeFilter('Participants');
		$PartFilterTrunc=$this->safeFilter('Participants', 'P');
		$PartFilterSource=$this->safeFilter('Participants', 'S');
		// creates the group rank
		$q=safe_r_sql("select RrMatchAthlete, RrMatchSubTeam, sum(RrMatchRoundPoints) as Points, sum(RrMatchTieBreaker) as TieBreaker, sum(RrMatchTieBreaker2) as TieBreaker2
			from RoundRobinMatches
			where $MatchFilter and RrMatchLevel>0 and RrMatchAthlete!=0
			group by RrMatchAthlete, RrMatchSubTeam
			order by Points desc, TieBreaker desc, TieBreaker2 desc");
		$rank=0;
		$i=0;
		$oldStatus='';
		$Update=false;
		while($r=safe_fetch($q)) {
			if($rank or $r->Points) {
				$i++;
			}
			if($oldStatus!="{$r->Points}-{$r->TieBreaker}-{$r->TieBreaker2}") {
				$rank+=$i;
				$i=0;
			}
			$oldStatus="{$r->Points}-{$r->TieBreaker}-{$r->TieBreaker2}";
			safe_w_sql("update RoundRobinParticipants 
				set RrPartPoints=$r->Points, RrPartTieBreaker=$r->TieBreaker, RrPartTieBreaker2=$r->TieBreaker2, RrPartGroupRank=$rank, RrPartGroupRankBefSO=$rank 
				where $PartFilter and RrPartParticipant=$r->RrMatchAthlete and RrPartSubTeam=$r->RrMatchSubTeam");
			if(safe_w_affected_rows()) {
				$Update=true;
				safe_w_sql("update RoundRobinParticipants 
					set RrPartDateTime=now() 
					where $PartFilter and RrPartParticipant=$r->RrMatchAthlete and RrPartSubTeam=$r->RrMatchSubTeam");
			}
		}
		// check Group SO/CT status
		if($Update) {
			// reset level rank
			safe_w_sql("update RoundRobinParticipants 
				set RrPartDateTime=now(), RrPartLevelTiesForSO=0, RrPartLevelTiesForCT=0, RrPartLevelRank=0, RrPartLevelRankBefSO=0, RrPartLevelTieBreak='', RrPartLevelTbClosest=0, RrPartLevelTbDecoded=''
				where $PartFilterTrunc");
			// reset group SO status
			safe_w_sql("update RoundRobinParticipants 
				set RrPartDateTime=now(), RrPartGroupTiesForSO=0, RrPartGroupTiesForCT=0 
				where $PartFilter");
			// select how many same rank we have in this group...
			$q=safe_r_sql("select RrPartGroupRankBefSO, count(*) as NumTied, NumQualified
				from RoundRobinParticipants
				left join (
				    select count(*) as NumQualified, 1 as selector
				    from RoundRobinParticipants
					where $PartFilterSource
					group by RrPartTournament, RrPartTeam, RrPartEvent, RrPartSourceLevel, RrPartSourceGroup
				    ) sqy on selector=1
				where NumQualified>=RrPartGroupRankBefSO and $PartFilter
				group by RrPartGroupRankBefSO
				having count(*)>1");
			while($r=safe_fetch($q)) {
				if($r->RrPartGroupRankBefSO+$r->NumTied-1 > $r->NumQualified) {
					// Shoot Off
					safe_w_sql("update RoundRobinParticipants 
						set RrPartDateTime=now(), RrPartGroupTiesForSO=$r->NumTied
						where RrPartGroupRankBefSO=$r->RrPartGroupRankBefSO and $PartFilter
						");
				} else {
					// Coin Toss
					safe_w_sql("update RoundRobinParticipants 
						set RrPartDateTime=now(), RrPartGroupTiesForCT=$r->NumTied
						where RrPartGroupRankBefSO=$r->RrPartGroupRankBefSO and $PartFilter
						");
				}
			}

			// apply the final rank
			$this->calculate();
		}
		return true;
	}

	function calculateLevel() {
		$PartFilter=$this->safeFilter('Participants');
		$PartFilterSource=$this->safeFilter('Source');
		// realculates the level rank!
		safe_w_sql("update RoundRobinParticipants
			set RrPartLevelRank=0, RrPartLevelRankBefSO=0
			where $PartFilter");
		$q=safe_r_SQL("select RrPartParticipant, RrPartSubTeam, RrPartPoints, RrPartTieBreaker, RrPartTieBreaker2, RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup, RrPartDestItem
			from RoundRobinParticipants
		    inner join RoundRobinLevel on RrLevTournament=RrPartTournament and RrLevEvent=RrPartEvent and RrLevTeam=RrPartTeam and RrLevLevel=RrPartLevel
			left join (
			    select RrPartParticipant as sqyPart, RrPartSubTeam as sqySub 
				from RoundRobinParticipants
			    where RrPartSourceGroup!=0 and $PartFilter
			    ) sqy on sqyPart=RrPartParticipant and sqySub=RrPartSubTeam
			where sqyPart IS NULL and $PartFilter
			order by if(RrLevBestRankMode=0, RrPartPoints, 0) desc, RrPartTieBreaker desc, RrPartTieBreaker2 desc");
		$curRank=0;
		$i=0;
		$oldPoints=0;
		$oldTieBreaker=0;
		$oldTieBreaker2=0;
		$Update=false;
		while($r=safe_fetch($q)) {
			$i++; // this will always increase as it takes track of the evolution
			if($oldPoints!=$r->RrPartPoints or $oldTieBreaker!=$r->RrPartTieBreaker or $oldTieBreaker2!=$r->RrPartTieBreaker2) {
				$curRank=$i;
				$oldPoints=$r->RrPartPoints;
				$oldTieBreaker=$r->RrPartTieBreaker;
				$oldTieBreaker2=$r->RrPartTieBreaker2;
			}
			safe_w_SQL("update RoundRobinParticipants
				set RrPartLevelRankBefSO=$curRank
				where $PartFilter and RrPartGroup=$r->RrPartGroup and RrPartParticipant=$r->RrPartParticipant and RrPartSubTeam=$r->RrPartSubTeam");
			if(safe_w_affected_rows()) {
				$Update=true;
				safe_w_SQL("update RoundRobinParticipants
					set RrPartDateTime=now(), RrPartLevelRank=$curRank
					where $PartFilter and RrPartGroup=$r->RrPartGroup and RrPartParticipant=$r->RrPartParticipant and RrPartSubTeam=$r->RrPartSubTeam");
			}
		}

		// check the LEVEL SO/CT status
		if($Update) {
			safe_w_sql("update RoundRobinParticipants 
			set RrPartDateTime=now(), RrPartLevelTiesForSO=0, RrPartLevelTiesForCT=0
			where $PartFilter");

			// select how many same rank we have in this group...
			$q=safe_r_sql("select RrPartLevelRankBefSO, count(*) as NumTied, NumQualified
				from RoundRobinParticipants
				left join (
				    select count(*) as NumQualified, 1 as selector
				    from RoundRobinParticipants
					where $PartFilterSource and RrPartSourceGroup=0 and RrPartLevelRankBefSO=0
					group by RrPartTournament, RrPartTeam, RrPartEvent, RrPartSourceLevel, RrPartSourceGroup
				    ) sqy on selector=1
				where NumQualified>=RrPartLevelRankBefSO and $PartFilter and RrPartLevelRankBefSO>0
				group by RrPartLevelRankBefSO
				having count(*)>1");
			while($r=safe_fetch($q)) {
				if($r->RrPartLevelRankBefSO+$r->NumTied-1 > $r->NumQualified) {
					// Shoot Off
					safe_w_sql("update RoundRobinParticipants 
					set RrPartDateTime=now(), RrPartLevelTiesForSO=$r->NumTied
					where RrPartLevelRankBefSO=$r->RrPartLevelRankBefSO and $PartFilter
					");
				} else {
					// Coin Toss
					safe_w_sql("update RoundRobinParticipants 
					set RrPartDateTime=now(), RrPartLevelTiesForCT=$r->NumTied
					where RrPartLevelRankBefSO=$r->RrPartLevelRankBefSO and $PartFilter
					");
				}
			}
		}
	}
}
