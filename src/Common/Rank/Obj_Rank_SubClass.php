<?php
/**
 * Obj_Rank_DivClass
 * Implementa l'algoritmo di default per il calcolo della rank di qualifica di classe individuale.
 *
 * La tabella in cui vengono scritti i valori è la Qualifications.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events		=> array(<ev_1>,<ev_2>,...,<ev_n>) || string,		[read]
 * 		divs		=> array(<div_1>,<div_2>,...,<div_n>)				[read]
 * 		cls			=> array(<cl_1>,<cl_2>,...,<cl_n>)					[read]
 * 		joinDivs	=> true || false									[read]
 * 		joinCls		=> true || false									[read]
 * 		dist		=> #												[read]
 * 		cutScore	=> #												[read,non influisce su calculate]
 * 		cutRank		=> #												[read,non influisce su calculate]
 * 		session		=> #												[read,non influisce su calculate]
 * 		comparedTo => #												[read]
 * 		tournament => #													[calculate/read]
 * )
 *
 * con:
 * 	 events: l'array degli eventi dove in questo caso un evento è la concatenazione di div e cl oppure se scalare, una stringa usata in LIKE
 *	 divs: l'array delle divisioni. Se presente events verrà ignorato
 *	 cls:  l'array delle classi. Se presente events verrà ignorato
 *	 joinDivs: raggruppa le divisioni (default false)
 *	 joinCls:  raggruppa le classi (default false)
 * 	 dist: la distanza con 0 per indicare la rank di qualifica totale
 *	 cutScore: punteggio (incluso) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *	 cutRank: Posizione di classifica (inclusa) a cui tagliare. Se impostato durante una calculate() il metodo ignorerà l'opzione
 *	 session: Se impostato ritorna la classifica di quella sessione, con la rank globale. Chi chiama se vuole ricalcolerà la rank in quella sessione
 *   tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * $data ha la seguente forma
 *
 * array(
 * 		meta 		=> array(
 * 			title 	=> <titolo della classifica localizzato>
 * 			numDist	=> <numero distanze>, inizializzato solo se c'è almeno una sezione
 * 			double	=> <1 se gara doppia 0 altrimenti>, inizializzato solo se c'è almeno una sezione
 *		),
 * 		sections 	=> array(
 * 			div_cl_1 => array(
 * 				meta => array(
 * 					event => <div_cl_1>, valore uguale alla chiave
 * 					descr => <descrizione evento localizzata>
 * 					fields(*1) => array(
 *						id 				=> <id della persona>
 *                      bib 			=> <codice della persona>
 *                      session 		=> <sessione>
 *                      target 			=> <piazzola>
 *                      athlete 		=> <cognome e nome>
 *                      familyname 		=> <cognome>
 *						givenname 		=> <nome>
 *						subclass 		=> <categoria>
 *                      countryCode 	=> <codice nazione>
 *                      countryName 	=> <nazione>
 *                      rank 			=> <rank in base alla distanza>
 *                      score 			=> <punti in base alla distanza>
 *                      gold 			=> <ori in base alla distanza>
 *                      xnine 			=> <xnine in base alla distanza>
 *                      arrowsShot		=> <frecce tirate (tutte se la distanza è zero oppure solo quelle della distanza passata)>
 *                      dist_1 			=> <rank|punti|ori|xnine della distanza 1>
 *                      dist_2 			=> <rank|punti|ori|xnine della distanza 2>
 *                      dist_3 			=> <rank|punti|ori|xnine della distanza 3>
 *                      dist_4 			=> <rank|punti|ori|xnine della distanza 4>
 *                      dist_5 			=> <rank|punti|ori|xnine della distanza 5>
 *                      dist_6 			=> <rank|punti|ori|xnine della distanza 6>
 *                      dist_7	 		=> <rank|punti|ori|xnine della distanza 7>
 *                      dist_8 			=> <rank|punti|ori|xnine della distanza 8>
 * 					)
 *				)
 * 				items => array(
 * 					array(id=><valore>,bib=><valore>,...,dist_8=><valore>),
 * 					...
 * 				)
 * 			)
 * 			...
 * 			div_cl_n = ...
 * 		)
 * )
 *
 * (*1) i campi contengono la localizzazione per l'etichetta di quel campo
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_SubClass extends Obj_Rank
	{
		public function __construct($opts)
		{
			parent::__construct($opts);
		}

		public function calculate()
		{
			return true;
		}

	/**
	 * safeOrder()
	 *
	 * @return mixed: lista degli elementi che compongono il sort order/MyEvent ai fini del punteggio
	 */
		protected function safeOrder()
		{
			$order=array();
            if (!empty($this->opts['joinGender']) and $this->opts['joinGender']) {
                $order[] = 'DivViewOrder, EnDivision, ClSex';
            } else {
                if (empty($this->opts['joinDivs'])) {
                    $order[] = 'DivViewOrder, EnDivision';
                }
                if (empty($this->opts['joinCls'])) {
                    $order[] = 'ClViewOrder, EnClass';
                }
            }
			$order[]='EnSubClass';
			return implode(',', $order);

		}

	/**
	 * read()
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#read()
	 *
	 */
		public function read()
		{
			$dd = ($this->opts['dist']>0 ? 'D' . $this->opts['dist'] : '');

			$safeOrder=$this->safeOrder();
			$filter=$this->safeFilter();
			$Description=$this->setDescription();

			if (array_key_exists('cutScore',$this->opts) && is_numeric($this->opts['cutScore']))
				$filter.= "AND Qu{$dd}Score>={$this->opts['cutScore']} ";

			//if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']))
			//	$filter.= "AND Qu" . ($this->opts['dist']>0 ? 'D' . $this->opts['dist'] : 'Cl') . "Rank<={$this->opts['cutRank']} ";

			if(!empty($this->opts['session']) and $ses=intval($this->opts['session'])) {
				$filter .= " AND QuSession=$ses ";
			}

			$comparedTo=0;
			if(!empty($this->opts["comparedTo"]) && is_numeric($this->opts["comparedTo"]))
				$comparedTo=$this->opts["comparedTo"];

			$tmp=null;
			if ($this->opts['dist']==0)
			{
				$tmp=array();
				foreach(range(1,8) as $n)
				{
					$tmp[]='char_length(rtrim(QuD'.$n.'Arrowstring))';
				}
				$tmp=implode('+', $tmp);
			}
			else
			{
				$tmp='char_length(rtrim(QuD'.$this->opts['dist'].'Arrowstring))';
			}

			$MyRank="Qu" . ($dd=='' ? 'Cl' : '') . $dd . "Rank";
			$orderBy="$safeOrder, if(IrmShowRank=1, 0, IrmId), Qu{$dd}Score DESC,Qu{$dd}Gold DESC, Qu{$dd}Xnine DESC ";

			$q="
				SELECT
					EnId,EnCode,EnSex,EnNameOrder, EnName AS Name, EnFirstName AS FirstName, upper(EnFirstName) AS FirstNameUpper, SUBSTRING(QuTargetNo,1,1) AS Session,
					SUBSTRING(QuTargetNo,2) AS TargetNo,
					concat($safeOrder) as MyEvent,
					concat(EnDivision, EnClass) MyTrueEvent,
					CoCode, CoName, EnClass, EnDivision,EnAgeClass, EnSubClass, ClDescription, DivDescription, ScDescription, 
					(CASE WHEN ClSex=0 THEN 'M' WHEN ClSex=1 THEN 'F' ELSE 'U' END) as GenderDescription, 
					IFNULL(Td1,'.1.') as Td1, IFNULL(Td2,'.2.') as Td2, IFNULL(Td3,'.3.') as Td3, IFNULL(Td4,'.4.') as Td4, IFNULL(Td5,'.5.') as Td5, IFNULL(Td6,'.6.') as Td6, IFNULL(Td7,'.7.') as Td7, IFNULL(Td8,'.8.') as Td8,
					QuD1Score, QuD1Rank, QuD2Score, QuD2Rank, QuD3Score, QuD3Rank, QuD4Score, QuD4Rank,
					QuD5Score, QuD5Rank, QuD6Score, QuD6Rank, QuD7Score, QuD7Rank, QuD8Score, QuD8Rank,
					QuD1Gold, QuD2Gold, QuD3Gold, QuD4Gold, QuD5Gold, QuD6Gold, QuD7Gold, QuD8Gold,
					QuD1Xnine, QuD2Xnine, QuD3Xnine, QuD4Xnine, QuD5Xnine, QuD6Xnine, QuD7Xnine, QuD8Xnine,
					{$tmp} AS Arrows_Shot,
					{$MyRank} AS `Rank`, " . (!empty($comparedTo) ? 'IFNULL(QopSubClassRank,0)' : '0') . " as OldRank, Qu{$dd}Score AS Score, Qu{$dd}Gold AS Gold,Qu{$dd}Xnine AS XNine,
					QuTimestamp,
					ToGolds AS GoldLabel, ToXNine AS XNineLabel,
					ToNumDist,ToDouble, QuIrmType, IrmType, IrmShowRank, hasShootOff, FdiDetails
				FROM Tournament
				INNER JOIN Entries ON ToId=EnTournament
				INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament AND EnTournament={$this->tournament}
				INNER JOIN Qualifications ON EnId=QuId
				INNER JOIN IrmTypes ON IrmId=QuIrmType
				INNER JOIN Classes ON EnClass=ClId AND ClTournament={$this->tournament}
				INNER JOIN Divisions ON EnDivision=DivId AND DivTournament={$this->tournament}
				inner join (
					select {$this->SoColumn} as hasShootOff, EnDivision as SoDivision, EnClass as SoClass, EnSubclass as SoSubclass 
					from Qualifications 
					inner join Entries on EnId=QuId and EnTournament={$this->tournament}
					group by EnDivision, EnClass, EnSubclass
					) hasSO on SoDivision=enDivision and SoClass=EnClass and SoSubclass=EnSubclass 
				LEFT JOIN TournamentDistances ON ToType=TdType AND TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses ";
			if(!empty($comparedTo))
				$q .= "LEFT JOIN QualOldPositions ON EnId=QopId AND QopHits=" . ($comparedTo>0 ? $comparedTo :  "(SELECT MAX(QopHits) FROM QualOldPositions WHERE QopId=EnId AND QopHits!=QuHits) ") . " ";
			$q .= "LEFT JOIN SubClass ON EnSubClass=ScId AND ScTournament={$this->tournament}
				left join (
					select DiSession as FdiSession, group_concat(concat_ws('|', DiDistance, DiEnds, DiArrows, DiScoringEnds, DiScoringOffset) order by DiDistance separator ',') as FdiDetails
					from DistanceInformation
					where DiTournament={$this->tournament} and DiType='Q'
					group by DiSession
					) FullDistanceInfo on FdiSession=QuSession
				WHERE EnAthlete=1 AND EnIndClEvent=1 AND EnStatus <= 1 AND ToId={$this->tournament}
				{$filter} "
				. (!empty($this->opts['SubClass'])? " HAVING MyEvent='{$this->opts['SubClass']}' " : '').
				"
				ORDER BY
					$orderBy, FirstName, Name
			";
			//print $q.'<br>';exit;

			$r=safe_r_sql($q);
			//print '<pre>';

			$this->data['meta']['title']=get_text('ResultClass','Tournament');
			$this->data['meta']['distance']=$this->opts['dist'];
			$this->data['meta']['numDist']=-1;
			$this->data['meta']['double']=-1;
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['sections']=array();

			$myEv='';

			$rank=1;
			$pos=0;

			$scoreOld=0;
			$goldOld=0;
			$xNineOld=0;

			if (safe_num_rows($r)>0) {
				$curEvent='';

				$section=null;

				while ($myRow=safe_fetch($r)) {

					if ($curEvent!=$myRow->MyEvent) {
					/*
					 *  se non sono all'inizio, prima di iniziare una sezione devo prendere quella appena fatta
					 *  e accodarla alle altre
					 */
						$rank=1;
						$pos=0;

						$scoreOld=0;
						$goldOld=0;
						$xNineOld=0;

						if ($curEvent!='')
						{
							$this->data['sections'][$curEvent]=$section;
							$section=null;
						}

					// al cambio creo una nuova sezione
						$curEvent=$myRow->MyEvent;

					// inizializzo i meta che son comuni a tutta la classifica
						if ($this->data['meta']['numDist']==-1)	{
							$this->data['meta']['numDist']=$myRow->ToNumDist;
							$this->data['meta']['double']=$myRow->ToDouble;
						}

						// adding the full distance info here
						$FullDistInfo=[];
						foreach(explode(',',$myRow->FdiDetails) as $d) {
							$t=explode('|', $d);
							$FullDistInfo['dist_' . $t[0]]=[
								'ends'=>$t[1],
								'arr'=>$t[2],
								'toShoot'=>($t[3]??0),
								'offset'=>($t[4]??0),
							];
						}

						// qui ci sono le descrizioni dei campi
						$NumDists=$myRow->ToNumDist;
						$distFields=array();
						foreach(range(1,8) as $n)
						{
							if($myRow->{'Td' . $n}=='-') {
								$NumDists--;
							}
							$distFields['dist_' . $n]=$myRow->{'Td' . $n};
						}

						$fields=array(
							'id'  => 'Id',
							'bib' => get_text('Code','Tournament'),
							'session' => get_text('Session'),
							'target' => get_text('Target'),
							'athlete' => get_text('Athlete'),
							'familyname' => get_text('FamilyName', 'Tournament'),
							'givenname' => get_text('Name', 'Tournament'),
							'gender' => get_text('Sex', 'Tournament'),
							'class' => get_text('Class'),
							'ageclass' => get_text('AgeCl'),
							'subclass' => get_text('SubCl','Tournament'),
							'countryCode' => '',
							'countryName' => get_text('Country'),
							'rank' => get_text('PositionShort'),
							'oldRank' => '',
							'score' => get_text('TotaleScore'),
							'gold' => $myRow->GoldLabel,
							'xnine' => $myRow->XNineLabel,
							'arrowsShot' => ''
						);

						$fields=$fields+$distFields;

						$section=array(
							'meta' => array(
								'event' => $curEvent,
								'trueEvent' => $myRow->MyTrueEvent,
								'printHeader' => '',
								'numDist' => $NumDists,
								'descr' => sprintf($Description,
									get_text($myRow->DivDescription,'','',true),
									get_text($myRow->ClDescription,'','',true),
                                    get_text($myRow->GenderDescription),
									$myRow->ScDescription ),
								'sesArrows'=> array(),
								'distanceInfo'=>$FullDistInfo,
								'fields' => $fields,
								'subClass' => $myRow->EnSubClass,
								'lastUpdate' => '0000-00-00 00:00:00',
								'shootOffStarted'=>$myRow->hasShootOff,
							)
						);
					}

					++$pos;

					if (!($myRow->Score==$scoreOld && $myRow->Gold==$goldOld  && $myRow->XNine==$xNineOld)) {
						$rank = $pos;
					}

					$myEv=$myRow->MyEvent;
					$scoreOld=$myRow->Score;
					$goldOld=$myRow->Gold;
					$xNineOld=$myRow->XNine;
					// creo un elemento per la sezione
					if($myRow->IrmShowRank) {
						$tmpRank= $rank;
					} else {
						$tmpRank= $myRow->IrmType;
					}

					if (array_key_exists('cutRank',$this->opts) && is_numeric($this->opts['cutRank']) and $rank>$this->opts['cutRank']) {
						continue;
					}

					// creo un elemento per la sezione
					$item=array(
						'id'  => $myRow->EnId,
						'bib' => $myRow->EnCode,
						'session' => $myRow->Session,
						'target' => $myRow->TargetNo,
						'athlete' => $myRow->FirstNameUpper . ' ' . $myRow->Name,
						'familyname' => $myRow->FirstName,
						'familynameUpper' => $myRow->FirstNameUpper,
						'givenname' => $myRow->Name,
						'nameOrder' => $myRow->EnNameOrder,
						'gender' => $myRow->EnSex,
						'class' => $myRow->EnClass,
						'ageclass' => $myRow->EnAgeClass,
						'subclass' => $myRow->EnSubClass,
						'countryCode' => $myRow->CoCode,
						'countryName' => $myRow->CoName,
						'rank' => $myRow->IrmShowRank ? $tmpRank : $myRow->IrmType,
						'oldRank' => $myRow->OldRank,
						'irm' => $myRow->QuIrmType,
						'irmText' => $myRow->IrmType,
						'score' => $myRow->Score,
						'gold' => $myRow->Gold,
						'xnine' => $myRow->XNine,
						'arrowsShot' => $myRow->Arrows_Shot
					);

					$distFields=array();
					foreach(range(1,8) as $n)
					{
						$distFields['dist_' . $n]= '0|' . $myRow->{'QuD' . $n . 'Score'} . '|' . $myRow->{'QuD' . $n . 'Gold'} . '|' . $myRow->{'QuD' . $n . 'Xnine'};
					}

					$item=$item+$distFields;

				// e lo aggiungo alla sezione
					//print_r($item);
					$section['items'][]=$item;

					if ($myRow->QuTimestamp>$this->data['meta']['lastUpdate']) {
						$this->data['meta']['lastUpdate']=$myRow->QuTimestamp;
					}
					if ($myRow->QuTimestamp>$section['meta']['lastUpdate']) {
						$section['meta']['lastUpdate']=$myRow->QuTimestamp;
					}

				}

			// ultimo giro
				$this->data['sections'][$curEvent]=$section;
			}
			//print '</pre>';
			/*print '<pre>';
			print_r($this->data);
			print '</pre>';*/
		}

	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events'] e genera il pezzo di query per filtrare
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter()
		{
			$filter='';

			if (!empty($this->opts['divs'])) {
				if(is_array($this->opts['divs'])) {
					$tmp=array();
					foreach ($this->opts['divs'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter.=" AND EnDivision IN (" . implode(',',$tmp). ") ";
				} else {
					$filter.=" AND EnDivision LIKE " . StrSafe_DB($this->opts['divs']) ;
				}
			}
			if (!empty($this->opts['cls'])) {
				if(is_array($this->opts['cls'])) {
					$tmp=array();
					foreach ($this->opts['cls'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter.=" AND EnClass IN (" . implode(',',$tmp). ") ";
				} else {
					$filter.=" AND EnClass LIKE " . StrSafe_DB($this->opts['cls']) ;
				}
			}

			if (!empty($this->opts['events'])) {
				// events overrides Div/Class selection
				if (is_array($this->opts['events'])) {
					$tmp=array();
					foreach ($this->opts['events'] as $e) $tmp[]=StrSafe_DB($e);
					sort($tmp);
					$filter="AND CONCAT(EnDivision,EnClass) IN (" . implode(',',$tmp) . ")";
				} else {
					$filter="AND CONCAT(EnDivision,EnClass) LIKE '" . $this->opts['events'] . "' ";
				}
			}

			if (!empty($this->opts['encode'])) {
				if (is_array($this->opts['encode'])) {
					$filter="AND EnCode IN (" . implode(',', StrSafe_DB($this->opts['encode'])) . ")";
				} else {
					$filter="AND EnCode = " . StrSafe_DB($this->opts['encode']) . " ";
				}
			}

			if (!empty($this->opts['sessions'])) {
				$filter.=" AND QuSession in (" . implode(',', $this->opts['sessions'])  . ") " ;
			}

			if (!empty($this->opts['sc'])) {
				$filter.=" AND EnSubclass=" . StrSafe_DB($this->opts['sc']) ;
			}

			return $filter;
		}
	/**
	 * setDescription()
	 *
	 * @return Sprintf_string: a string with correct %s to input in sprintf
	 */
		protected function setDescription() {
			$str='';
            if (!empty($this->opts['joinGender']) and $this->opts['joinGender']) {
                $str .= '%1$s %3$s - ';
            } else {
                if (empty($this->opts['joinDivs'])) $str .= '%1$s - ';
                if (empty($this->opts['joinCls'])) $str .= '%2$s - ';
            }
			$str.='%4$s';

			return $str;
		}
	}
