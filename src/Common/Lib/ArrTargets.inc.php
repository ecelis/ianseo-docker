<?php
/*
 * Il vettore serve come riferimento per avere la corrispondenza lettera-punto nelle arrowstring.
 * La struttura è:
 * chiave => [stampa,valore]
 *
 * con la 'chiave' la lettera che rappresenta il punto per tutti i target, 'stampa' la stringa che viene visualizzata
 * nelle viste e 'valore' il valore numerico della lettera usato per i conti.
 */
	$LetterPoint=array
	(
		"A" => array("P" => "M", "N" => "0", 'W' => 0),
		"B" => array("P" => "1", "N" => "1", 'W' => 110),
		"C" => array("P" => "2", "N" => "2", 'W' => 120),
		"D" => array("P" => "3", "N" => "3", 'W' => 130),
		"E" => array("P" => "4", "N" => "4", 'W' => 140),
		"F" => array("P" => "5", "N" => "5", 'W' => 150),
		"G" => array("P" => "6", "N" => "6", 'W' => 160),
		"H" => array("P" => "7", "N" => "7", 'W' => 170),
		"I" => array("P" => "8", "N" => "8", 'W' => 180),
		"J" => array("P" => "9", "N" => "9", 'W' => 190),
		"K" => array("P" => "X", "N" => "10", 'W' => 205),
		"L" => array("P" => "10", "N" => "10", 'W' => 200),
		"M" => array("P" => "11", "N" => "11", 'W' => 210),
		"N" => array("P" => "12", "N" => "12", 'W' => 220),
		"O" => array("P" => "O", "N" => "1", 'W' =>110),
		"P" => array("P" => "X", "N" => "0", 'W' => 0),
		"Q" => array("P" => "X", "N" => "15", 'W' => 250),
        "R" => array("P" => "IX", "N" => "5", 'W' => 265),
       // "S" => array("P" => "21", "N" => "0", 'W' => 300),
        //"T" => array("P" => "22", "N" => "22", 'W' => 305),
		//"U" => array("P" => "19", "N" => "19", 'W' => 285),
		"V" => array("P" => "20", "N" => "20", 'W' => 290),
		"W" => array("P" => "16", "N" => "16", 'W' => 260),
		"X" => array("P" => "X", "N" => "11", 'W' => 205),
		"Y" => array("P" => "X", "N" => "6", 'W' => 205),
		"Z" => array("P" => "X", "N" => "5", 'W' => 205),
		"1" => array("P" => "13", "N" => "13", 'W' => 225),
		"2" => array("P" => "14", "N" => "14", 'W' => 230),
		"3" => array("P" => "16", "N" => "16", 'W' => 255),
		"4" => array("P" => "17", "N" => "17", 'W' => 260),
		"5" => array("P" => "18", "N" => "18", 'W' => 265),
		"6" => array("P" => "21", "N" => "21", 'W' => 295),
		"7" => array("P" => "19", "N" => "19", 'W' => 270),
		"8" => array("P" => "22", "N" => "22", 'W' => 300),
		//"9" => array("P" => "X", "N" => "5", 'W' => 160),
        "(" => array("P" => "M$", "N" => "0", 'W' => 0),
        ")" => array("P" => "5$", "N" => "5", 'W' => 150),
        "[" => array("P" => "8$", "N" => "8", 'W' => 180),
        "]" => array("P" => "10$", "N" => "10", 'W' => 200),
	);
	$GLOBALS['LetterPoint']=$LetterPoint;

    $extraLetter = array(
        "A" => "(",
        "F" => ")",
        "I" => "[",
        "L" => "]",
    );
    $GLOBALS['extraLetter']=$extraLetter;

/*
	- GetTargetType($EventCode,$TeamEvent=0)
	Ritorna la variabile che contiene il target.
	$EventCode � l'evento.
	$TeamEvent vale 1 se l'evento � a squadre oppure 0 se � individuale
	$TourId vale -1 se va usato $_SESSION['TourId'] altrimenti è l'id del torneo
*/
	function GetTargetType($EventCode,$TeamEvent=0,$TourId=-1)
	{
		$Target = 'TrgOutdoor';

		$ToId=($TourId!=-1 ? $TourId : StrSafe_DB($_SESSION['TourId']));

		$Select = "SELECT EvCode,EvFinalTargetType,TarArray
			FROM Events 
		    INNER JOIN Targets ON EvFinalTargetType=TarId AND EvTeamEvent=" . StrSafe_DB($TeamEvent) . "
			WHERE EvTournament=" . $ToId . " AND EvCode=" . StrSafe_DB($EventCode) . " ";
		$Rs=safe_r_sql($Select);
		//print $Select;exit;
		if (safe_num_rows($Rs)==1)
		{
			$MyRow=safe_fetch($Rs);
			$Target = $MyRow->TarArray ;
		}

		return $Target;
	}

/*
- GetHigerArrowValue($EventCode,$TeamEvent=0,$curValue='',$TourId=-1)
Returns the next Higher value than the passed one
@param String $EventCode: Event Code.
@param String $TeamEvent: 0 if Indivudual Event, 1 if Team Event.
@param String $curValue: actual arrow value (print value)
@param String $TourId: Tournament id, if -1 $_SESSION['TourId'] is used
*/
function GetHigerArrowValue($EventCode,$TeamEvent=0,$curValue='',$TourId=-1)
{
    global $LetterPoint;
    $ToId=($TourId!=-1 ? $TourId : StrSafe_DB($_SESSION['TourId']));

    $Sql = "SELECT Targets.* 
        FROM Events INNER JOIN Targets ON EvFinalTargetType=TarId AND EvTeamEvent=" . StrSafe_DB($TeamEvent) . "
        WHERE EvTournament=" . $ToId . " AND EvCode=" . StrSafe_DB($EventCode);
    $q=safe_r_sql($Sql);
    if(!($MyRow=safe_fetch($q))) {
        return $curValue;
    }

    $ret=array();
    $CurValueWeight=0;
    foreach(range('Z','B') as $key) {
        if($MyRow->{$key.'_size'}) {
            if($LetterPoint[$key]['P'] == $curValue) {
                $CurValueWeight = $LetterPoint[$key]['W'];
            }
            if($LetterPoint[$key]['W']>$CurValueWeight) {
                $ret[$LetterPoint[$key]['W']] = $LetterPoint[$key]['P'];
            }
        }
    }
    ksort($ret,SORT_NUMERIC);

    foreach (array_keys($ret) as $v) {
        if($v>$CurValueWeight) {
            return $ret[$v];
        }
    }
    return $curValue;
}


/**
 * Ritorna true se il bersaglio $Target è completo
 *
 * @param String $Target: Nome della variabile che rappresenta il bersaglio
 *
 * @return boolean: true se il bersaglio è completo; false altrimenti
 */
	function TargetIsComplete($Target)
	{

		$Ret=true;
		//print substr($Target,-5,5);exit;
		if (substr($Target,-5,5)=='Small')
		{
			$Ret=false;
		}
		return $Ret;
	}

/**
 *  Nuova ver di ValutaArrowString.
 *  Valuta l'arrowstring usando $LetterPoint
 *
 *  @param string $Letter: lettera chiave di $LetterPoint
 *  @return string: stringa vuota in caso di problemi oppure la somma dei punti di $LetterPoint
 */
//XXX Tolto il parametro $MySym e sganciato dal sorgente
	function ValutaArrowString($MyStr)
	{
		global $LetterPoint;
		/*
		 * converto in maiuscolo perchè tanto il valore numerico
		 * del dubbio è uguale a quello del non dubbio
		 */
		$MyStr=strtoupper($MyStr);
		$Tot=0;
		for ($i=0;$i<strlen($MyStr);++$i)
		{
			$letter=$MyStr[$i];

			if(array_key_exists($letter,$LetterPoint)) {
                $Tot += $LetterPoint[$letter]["N"];
            }
		}

		return $Tot;
	}

/**
 *  Nuova(2) ver di ValutaArrowStringGX.
 *  Valuta l'arrowstring contanto ori e x usando $LetterPoint
 *
 *  @param string $MyStr: lettera chiave di $LetterPoint
 *  @param string $G: string di chiavi di $LetterPoint da usare come gold
 *  @param string $X: string di chiavi di $LetterPoint da usare come xnine
 *  @param string $asArray: if true returns an array of print letters instead of the total
 *  @return int[]: Array di 3 elementi: [Score,Gold,XNine]
 */
	function ValutaArrowStringGX($MyStr,$G=null,$X=null,$asArray=false)
	{
		global $LetterPoint;

		$TotScore=0;
		$TotGold=0;
		$TotXNine=0;
        $array=[];

		if(is_null($G) or is_null($X)) {
			$q=safe_r_sql("select ToGoldsChars, ToXNineChars from Tournament where ToId={$_SESSION['TourId']}");
			$r=safe_fetch($q);
			if(is_null($G)) $G=$r->ToGoldsChars;
			if(is_null($X)) $X=$r->ToXNineChars;
		}
	// trasformo in array $G e $X per cercarli meglio
		$G = preg_split('//', $G, -1, PREG_SPLIT_NO_EMPTY);
		$X = preg_split('//', $X, -1, PREG_SPLIT_NO_EMPTY);
//print_r($G).'<br>';
//print_r($X).'<br>';
//exit;
		for ($i=0;$i<strlen($MyStr);++$i)
		{
		/*
		 * tutto in maiuscolo perchè tanto il valore numerico del punto dubbio è uguale
		 * al punto non dubbio
		 */
			$letter=strtoupper($MyStr[$i]);

		// se la lettera nell'arrowstring è una chiave buona
			if(array_key_exists($letter,$LetterPoint))
			{
			// score
				$TotScore+=$LetterPoint[$letter]["N"];
                $array[]=$LetterPoint[$letter]["P"];

			/* gold e xnine */

			// gold
				if (in_array($letter,$G))
					++$TotGold;

//				foreach ($G as $g)
//				{
//					if (array_key_exists($g,$LetterPoint))
//						++$TotGold;
//				}

			// xnine
				if (in_array($letter,$X))
					++$TotXNine;

//				foreach ($X as $x)
//				{
//					if (array_key_exists($x,$LetterPoint))
//						++$TotXNine;
//				}
			}
		}
        if($asArray) {
            return array($TotScore,$TotGold,$TotXNine,$array);
        }

		return array($TotScore,$TotGold,$TotXNine);
	}

/**
 * Calculates the weight of the "drops" Lancaster style, up to 8th drop, higher value wins
 * @param $QuId int of the archer
 * @param $X string the letter representing the X
 * @return void
*/
function CalculateDropWeight($QuId, $X='M') {
    // there is a fake miss (A) at the end to get the last sequence
    $q=safe_r_sql("select concat(rtrim(QuD1Arrowstring),rtrim(QuD2Arrowstring),rtrim(QuD3Arrowstring),rtrim(QuD4Arrowstring),rtrim(QuD5Arrowstring),rtrim(QuD6Arrowstring),rtrim(QuD7Arrowstring),rtrim(QuD8Arrowstring),'A') as FullArrowString
        from Qualifications 
        where QuId={$QuId}");
    if($r=safe_fetch($q)) {
        $drops=[];
        $totLen=strlen($r->FullArrowString)-1;
        preg_match_all('/'.$X.'*[^'.$X.']/sim', $r->FullArrowString, $match);
        $n=0;
        $Weight='';
        foreach(array_slice($match[0], 0, 10) as $num => $item) {
            $n+=strlen($item);
            $len=strlen($item)-1;
            $val=ValutaArrowString(substr($item,-1));
            if($n>$totLen) {
                $drops[]=[$len];
            } else {
                $drops[]=[$len,$val];
            }
            // 1 ^ 14
            $Weight.=str_pad(($len+1)*100 + $val, 4, '0', STR_PAD_LEFT);
        }
        $Weight=str_pad($Weight, 50, '0', STR_PAD_RIGHT);
        safe_w_sql("update Qualifications set QuTieWeight='$Weight', QuTieWeightDrops=".StrSafe_DB(json_encode($drops))." where QuId={$QuId}");
    }
}

	/**
	 *  Nuova(2) ver di ValutaArrowStringGX.
	 *  Valuta l'arrowstring contanto ori e x usando $LetterPoint
	 *
	 *  @param string $MyStr: string to decode
     * @param string $XChar: Char to be evaluated as "closer to center", defaults to X as printed value
     * @param string $LetterPointKey: if 'P' $XChar is evaluated as the printed value, otherwise as the key of $LetterPoint
	 *  @return int[]: Array of 6 elements: <ul><li>Points</li><li>Max Weight</li><li>Number of stars</li><li>Number of Xs</li><li>SOArrows in Order of value</li><li>SOArrows in Order of input</li></ul>
	 */
	function ValutaArrowStringSO($MyStr, $XChar=null, $LetterPointKey=null) {
		global $LetterPoint;

		$TotScore = 0;
		$MaxWeight= 0;
		$TotStars = 0;
		$TotX = 0;
		$Letters=array();
        if(is_null($XChar)) $XChar='X';
        if(is_null($LetterPointKey)) $LetterPointKey='P';

		for ($i=0;$i<strlen($MyStr);++$i) {
			/*
			 * tutto in maiuscolo perchè tanto il valore numerico del punto dubbio è uguale
			 * al punto non dubbio
			 */
			$letter=strtoupper($MyStr[$i]);
			if($letter != $MyStr[$i]) $TotStars++;

			// se la lettera nell'arrowstring è una chiave buona
			if(array_key_exists($letter, $LetterPoint)) {
				// score
				$TotScore += $LetterPoint[$letter]["N"];
				$MaxWeight=max($MaxWeight, $LetterPoint[$letter]["W"]);
				$Letters[]=$LetterPoint[$letter]["N"];
                if($LetterPointKey=='P') {
                    if($LetterPoint[$letter]["P"]==$XChar) {
                        $TotX++;
                    }
                } else {
                    if($letter==$XChar) {
                        $TotX++;
                    }
                }
            }
		}
        $LettersSorted = $Letters;
		rsort($LettersSorted);

		return array($TotScore, $MaxWeight, $TotStars, $TotX, $LettersSorted, $Letters);
	}

/**
 *  Nuova ver di DecodeFromLetter.
 *  Data la lettera ritorna il suo valore di stampa.
 *
 *  @param string $Letter: lettera chiave di $LetterPoint
 *  @return string: stringa vuota in caso di problemi oppure colonna "P" di $LetterPoint
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function DecodeFromLetter($Letter='', $IsSO=false)
	{
		global $LetterPoint;

		IF(!$Letter) RETURN '';
		$maybe=false;

	/*
	 * Se non esiste la chiave nel vettore potrebbe essere un dubbio.
	 * Se una volta convertita in maiuscola non trovo la lettera
	 * allora ho un errore
	 */
		if (!array_key_exists($Letter,$LetterPoint))
		{
			$Letter=strtoupper($Letter);
			$maybe=true;
		}

		if (array_key_exists($Letter,$LetterPoint))
		{
			return $LetterPoint[$Letter]['P'] . ($maybe ? ($IsSO ? '+' : '*') : '');
		}
		else
			return '';

	}

	/**
	 *  DecodeFromString.
	 *  Data la Stringa ritorna un array del suo valore di stampa.
	 *
	 *  @param string $Letter: stringa di lettere chiave di $LetterPoint
	 *  @return string: stringa vuota in caso di problemi oppure colonna "P" di $LetterPoint
	 */
	//XXX tolto il parametro $Target e sganciato dal sorgente
	function DecodeFromString($Letter='', $sum=false, $forceArray=false)
	{
		global $LetterPoint;
		$SumMaybe='';
		$SumReturn=0;
		//$Letter=rtrim($Letter);
		IF(!$Letter) RETURN array();
		$maybe=false;

		$ret=array();
		foreach(range(1,strlen($Letter)) as $n) {
			$maybe=false;
			/*
			 * Se non esiste la chiave nel vettore potrebbe essere un dubbio.
			* Se una volta convertita in maiuscola non trovo la lettera
			* allora ho un errore
			*/
			if (!array_key_exists($Letter[$n-1],$LetterPoint))
			{
				$Letter[$n-1]=strtoupper($Letter[$n-1]);
				$maybe=true;
				$SumMaybe=true;
			}

			if (array_key_exists($Letter[$n-1],$LetterPoint))
			{
				$ret[]= $LetterPoint[$Letter[$n-1]]['P'] . ($maybe ? '*' : '');
			}
			else
				$ret[]= ' ';

		}
		if($sum) return ValutaArrowString($Letter).($maybe ? '*' : '');
		if(!$forceArray and strlen($Letter)==1) return $ret[0];
		return $ret;

	}


/**
 * Nuova ver di DecodeFromPrint
 * Dato il valore di stampa ritorna il valore numerico.
 *
 * @param string $Value: valore di stampa da cercare
 * @return int: valore numerico. Ritorna 0 se non trova nulla.
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function DecodeFromPrint($Value)
	{
		global $LetterPoint;

	/*
	 * Tutto diventa maiuscolo e rimuovo '*' perchè tanto
	 * il valore numerico del dubbio è quello del non dubbio.
	 */
		$P = strtoupper($Value);
		$P = str_replace('*','',$P);

		foreach ($LetterPoint as $Key => $Value)
		{
			if ($P==$Value['P'])
			{
				return $Value['N'];
			}
		}

		return 0;
	}

/*
	- GetLetterFromSearch($Value,$Target)
	Dato $Value il valore di Ricerca sul bersaglio, ritorna la chiave di $Target
*/
	function GetLetterFromSearch($Value,$Target)
	{
		$R = strtoupper($Value);
		//print 'R->' . $R . '<br>';
		foreach ($Target as $Key => $Value)
		{
			/*print '<pre>';
			print_r($Value);
			print '</pre>';*/
			if ($R==$Value['R'])
			{
				return $Key;
			}
		}
	}
/**
 * Nuova ver di GetLetterFromPrint
 * Dato il valore di stampa ritorna la lettera di codifica (la chiave di $LetterPoint).
 * NOTA: se nel valore di stampa c'è l' "*" la funzione ritorna la chiave in minuscolo
 * che significa punto dubbio.
 *
 * @param string $Value: valore di stampa da cercare
 * @return string: chiave di $LetterPoint. Se c'è qualche problema ritorna ' '
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function old_GetLetterFromPrint($Value)
	{
		global $LetterPoint;

		$maybe=false;

		$P = strtoupper($Value);
		if (strpos($P,'*')!==false)
			$maybe=true;

		$P = str_replace('*','',$P);

		foreach ($LetterPoint as $Key => $Value)
		{
			if ($P==$Value['P'])
			{
				return ($maybe ? strtolower($Key) : $Key);
			}
		}

		return ' ';
	}

/**
 * Nuova ver di GetLetterFromPrint
 * Dato il valore di stampa ritorna la lettera di codifica (la chiave di $LetterPoint)
 * e nel caso vengano passati gli ultimi due parametri, lavora solo sul subset di lettere per il tipo
 * di bersaglio (vedi GetGoodLettersFromDist).
 *
 * NOTA: se nel valore di stampa c'è l' "*" la funzione ritorna la chiave in minuscolo
 * che significa punto dubbio.
 *
 * NOTA2: se $entry è un array, rappresenta un bersaglio valido subset di $LetterPoint!!!
 *
 * NOTA3: se $entry = "T" il $dist è l'ID del bersaglio
 *
 * @param string $Value valore di stampa da cercare
 * @param int $entry <ul>
 *     <li>array: the available target (subset of $LetterPoint)</li>
 *     <li>"T": $dist must be the distance shot</li>
 * </ul>if array
 * @param int $dist distanza tirata
 * @return string chiave di $LetterPoint. Se c'è qualche problema ritorna uno spazio
 */
//XXX tolto il parametro $Target e sganciato dal sorgente
	function GetLetterFromPrint($Value, $entry=null, $dist=null) {
		global $LetterPoint;
		static $TargetTypes=array();

		// le lettere sono quelle di LetterPoint se non specifico il subset
		if(is_array($entry)) {
			$Letters=array_keys($entry);
		} elseif($entry=='T') {
			if(empty($TargetTypes[$dist])) {
				$TargetTypes[$dist]=GetGoodLettersFromTgtId($dist);
			}
			$Letters=$TargetTypes[$dist];
		} elseif(!($entry and $dist and $Letters=GetGoodLettersFromDist($entry,$dist))) {
			$Letters=array_keys($LetterPoint);
		}

		$maybe=false;

		$P = strtoupper($Value);
		if (strpos($P,'*')!==false)
			$maybe=true;

		$P = str_replace('*','',$P);

		foreach ($Letters as $l)
		{
			if ($P==$LetterPoint[$l]['P'])
			{
				return ($maybe ? strtolower($l) : $l);
			}
		}

//		foreach ($LetterPoint as $Key => $Value)
//		{
//			if ($P==$Value['P'])
//			{
//				return ($maybe ? strtolower($Key) : $Key);
//			}
//		}

		return ' ';
	}
/**
 * GetHigherTargetValue
 * ritorna il valore numerico più alto per un dato bersaglio
 */
	function GetHigherTargetValue($Target) {
		global $LetterPoint;
		$ret=0;

		foreach(array_keys($LetterPoint) as $index) if($Target[$index]['N']>$ret) $ret=$Target[$index]['N'];

		return $ret;
	}

/*
	- GetMaxScores($EventCode, $MatchNo, $TeamEvent=0, $TourId=-1)
	Returns an array of Maxvalues for that match.
	$EventCode is the event.
	$MatchNo is the number of the match (refer to table Grid)
	$TeamEvent = 1 for team events, 0 for individual
	$TourId: if -1 will use $_SESSION['TourId']
	The return array is
	Arrows => array of accepted arrows:
		'key': same code as $LetterPoint
		val[0] = size of ring
		val[1] = fill color
		val[2] = border color
	MaxPoint = maximum arrow point value;
	MaxEnd   = maximum per end
	MaxMatch = maximum per match
	MaxSetPoints = 0 if no SET system, Setpoints to win if SET system
	MaxSO    = maximum TOTAL ShootOff arrows

*/
function GetMaxScores($EventCode, $MatchNo=0, $TeamEvent=0, $TourId=-1){
	global $LetterPoint;

	$ret = array();

	$ToId=($TourId!=-1 ? $TourId : StrSafe_DB($_SESSION['TourId']));

    if($MatchNo>256) {
        $Select = "SELECT Targets.*, EvMatchMode, EvFinalTargetType, EvTargetSize, EvDistance,
			RrLevEnds CalcEnds, 
			RrLevArrows CalcArrows, 
			RrLevSO CalcSO 
		FROM Events
		INNER JOIN Targets ON EvFinalTargetType=TarId 
		inner join RoundRobinMatches on RrMatchTournament=EvTournament and RrMatchTeam=EvTeamEvent and RrMatchEvent=EvCode and (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo = $MatchNo
		inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel		
		WHERE EvTournament=" . $ToId . "
			AND EvCode=" . StrSafe_DB($EventCode) . " 
			AND EvTeamEvent=" . StrSafe_DB($TeamEvent);
        $Rs=safe_r_sql($Select);

        if ($MyRow=safe_fetch($Rs)) {
            $ret['Arrows']=array('A' => array(0, '', ''));
            $ret['HasDot']=($MyRow->TarId==24);
            $ret['MaxPoint']=0;
            $ret['MinPoint']=999;
            if(isset($GLOBALS['CurrentTarget'])) {
                $GLOBALS['CurrentTarget']['A'] = $LetterPoint['A'];
            }
            $size=0;
            $targetRings=array(PHP_INT_MAX => array(
                'size'=>0,
                'fillColor'=>'',
                'lineColor'=>'',
                'letter'=>'A',
                'value'=>$LetterPoint['A']['N'],
                'print'=>$LetterPoint['A']['P'],
                'radius'=>-1)
            );
            foreach(range('A','Z') as $key) {
                if($MyRow->{$key.'_size'}) {
                    $targetRings[$MyRow->{$key.'_size'}] = array(
                        "size"=>$MyRow->{$key.'_size'},
                        "fillColor"=>$MyRow->{$key.'_color'},
                        "lineColor"=>'000000',
                        "letter"=>$key,
                        "value"=>$LetterPoint[$key]['N'],
                        "print"=>$LetterPoint[$key]['P'],
                        "radius"=>0
                    );

                    if($size < $MyRow->{$key.'_size'}) {
                        $size = $MyRow->{$key . '_size'};
                    }
                    /*
                    // fills the accepted arrows array
                    $ret['Arrows'][$key]=array(
                        $MyRow->{$key.'_size'},
                        $MyRow->{$key.'_color'},
                        ($MyRow->{$key.'_color'}=='000000' && $oldcolor=='000000')?'FFFFFF':'000000'
                    );
                    $oldcolor=$MyRow->{$key.'_color'};
    */
                    // check the maxpoint
                    if($LetterPoint[$key]['N']>$ret['MaxPoint']) {
                        $ret['MaxPoint']=$LetterPoint[$key]['N'];
                    }

                    // check the minpoint
                    if($LetterPoint[$key]['N'] and $LetterPoint[$key]['N']<$ret['MinPoint']) {
                        $ret['MinPoint']=$LetterPoint[$key]['N'];
                    }

                    if(isset($GLOBALS['CurrentTarget'])) {
                        $GLOBALS['CurrentTarget'][$key] = $LetterPoint[$key];
                    }
                }
            }
            foreach(range('1','9') as $key) {
                if($MyRow->{$key.'_size'}) {
                    $targetRings[$MyRow->{$key.'_size'}] = array(
                        "size"=>$MyRow->{$key.'_size'},
                        "fillColor"=>$MyRow->{$key.'_color'},
                        "lineColor"=>'000000',
                        "letter"=>$key,
                        "value"=>$LetterPoint[$key]['N'],
                        "print"=>$LetterPoint[$key]['P'],
                        "radius"=>0
                    );

                    if($size < $MyRow->{$key.'_size'}) {
                        $size = $MyRow->{$key . '_size'};
                    }
                    /*
                    // fills the accepted arrows array
                    $ret['Arrows'][$key]=array(
                        $MyRow->{$key.'_size'},
                        $MyRow->{$key.'_color'},
                        ($MyRow->{$key.'_color'}=='000000' && $oldcolor=='000000')?'FFFFFF':'000000'
                    );
                    $oldcolor=$MyRow->{$key.'_color'};
    */
                    // check the maxpoint
                    if($LetterPoint[$key]['N']>$ret['MaxPoint']) {
                        $ret['MaxPoint']=$LetterPoint[$key]['N'];
                    }

                    // check the minpoint
                    if($LetterPoint[$key]['N'] and $LetterPoint[$key]['N']<$ret['MinPoint']) {
                        $ret['MinPoint']=$LetterPoint[$key]['N'];
                    }

                    if(isset($GLOBALS['CurrentTarget'])) {
                        $GLOBALS['CurrentTarget'][$key] = $LetterPoint[$key];
                    }
                }
            }
            $ExtraPoint=0;
            if($MyRow->TarId=='25') {
                $ret['MaxPoint']--;
                $ExtraPoint=1;
            }
            $ret['MaxEnd']=$ret['MaxPoint']*$MyRow->CalcArrows + $ExtraPoint;
            $ret['MaxMatch']=$ret['MaxEnd']*$MyRow->CalcEnds;
            $ret['MaxSetPoints']=($MyRow->EvMatchMode ? $MyRow->CalcEnds+2 : 0);
            $ret['MaxSO']=$ret['MaxPoint']*$MyRow->CalcSO + $ExtraPoint;
            $ret['ArrowsPerEnd']=$MyRow->CalcArrows;
            $ret['Ends']=$MyRow->CalcEnds;
            $ret['SO']=$MyRow->CalcSO;
            $ret['Distance']=$MyRow->EvDistance;
            $ret['TargetRadius'] = ($MyRow->TarFullSize ? ($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122) * ($size/$MyRow->TarFullSize) * 5 : 0);
            $ret['Size']=($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122) * ($size/2);
            $ret['TargetSize']= ($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122);
            $ret['FullSize']= $MyRow->TarFullSize;
            $ret['MaxSize']= $size;
            krsort($targetRings);
            $oldColor='';

            if($MyRow->TarId=='25') {
                $ret['MaxPoint']++;
            }

            foreach ($targetRings as $k=>$v) {
                $v['radius'] = ($ret['TargetRadius'] / $size) * $v['size'];
                if($oldColor == $v['fillColor'] AND $v['fillColor']=='000000') {
                    $v['lineColor'] = 'FFFFFF';
                }
                $ret['Arrows'][$v['letter']] = $v;

                $oldColor= $v['fillColor'];
            }
        }

        return $ret;

    }

    if($MatchNo<=1) {
		$Phase=0;
	} else {
		$Phase= pow(2, intval(log($MatchNo, 2)));
	}
	$Select = "SELECT Targets.*, EvMatchMode, EvFinalTargetType, EvTargetSize, EvDistance,
			@PhaseMatch:=($Phase & EvMatchArrowsNo), 
			if(@PhaseMatch, EvElimEnds, EvFinEnds) CalcEnds, 
			if(@PhaseMatch, EvElimArrows, EvFinArrows) CalcArrows, 
			if(@PhaseMatch, EvElimSO, EvFinSO) CalcSO 
		FROM Events
		INNER JOIN Targets ON EvFinalTargetType=TarId 
		WHERE EvTournament=" . $ToId . "
			AND EvCode=" . StrSafe_DB($EventCode) . " 
			AND EvTeamEvent=" . StrSafe_DB($TeamEvent);
		$Rs=safe_r_sql($Select);

	if ($MyRow=safe_fetch($Rs))
	{
        $ret['TargetId']=$MyRow->TarId;
		$ret['Arrows']=array('A' => array(0, '', ''));
		$ret['HasDot']=($MyRow->TarId==24);
		$ret['MaxPoint']=0;
		$ret['MinPoint']=999;
		if(isset($GLOBALS['CurrentTarget'])) {
			$GLOBALS['CurrentTarget']['A'] = $LetterPoint['A'];
		}
		$size=0;
		$targetRings=array(PHP_INT_MAX => array(
            'size'=>0,
            'fillColor'=>'',
            'lineColor'=>'',
            'letter'=>'A',
            'value'=>$LetterPoint['A']['N'],
            'print'=>$LetterPoint['A']['P'],
            'radius'=>-1)
        );
		foreach(range('A','Z') as $key) {
			if($MyRow->{$key.'_size'}) {
			    $targetRings[$MyRow->{$key.'_size'}] = array(
			        "size"=>$MyRow->{$key.'_size'},
                    "fillColor"=>$MyRow->{$key.'_color'},
                    "lineColor"=>'000000',
                    "letter"=>$key,
			        "value"=>$LetterPoint[$key]['N'],
			        "print"=>$LetterPoint[$key]['P'],
                    "radius"=>0
                );

				if($size < $MyRow->{$key.'_size'}) {
                    $size = $MyRow->{$key . '_size'};
                }
                /*
				// fills the accepted arrows array
				$ret['Arrows'][$key]=array(
				    $MyRow->{$key.'_size'},
                    $MyRow->{$key.'_color'},
                    ($MyRow->{$key.'_color'}=='000000' && $oldcolor=='000000')?'FFFFFF':'000000'
                );
				$oldcolor=$MyRow->{$key.'_color'};
*/
				// check the maxpoint
				if($LetterPoint[$key]['N']>$ret['MaxPoint']) {
				    $ret['MaxPoint']=$LetterPoint[$key]['N'];
                }

				// check the minpoint
				if($LetterPoint[$key]['N'] and $LetterPoint[$key]['N']<$ret['MinPoint']) {
				    $ret['MinPoint']=$LetterPoint[$key]['N'];
                }

				if(isset($GLOBALS['CurrentTarget'])) {
					$GLOBALS['CurrentTarget'][$key] = $LetterPoint[$key];
				}
			}
		}
		foreach(range('1','9') as $key) {
			if($MyRow->{$key.'_size'}) {
			    $targetRings[$MyRow->{$key.'_size'}] = array(
			        "size"=>$MyRow->{$key.'_size'},
                    "fillColor"=>$MyRow->{$key.'_color'},
                    "lineColor"=>'000000',
                    "letter"=>$key,
			        "value"=>$LetterPoint[$key]['N'],
			        "print"=>$LetterPoint[$key]['P'],
                    "radius"=>0
                );

				if($size < $MyRow->{$key.'_size'}) {
                    $size = $MyRow->{$key . '_size'};
                }
                /*
				// fills the accepted arrows array
				$ret['Arrows'][$key]=array(
				    $MyRow->{$key.'_size'},
                    $MyRow->{$key.'_color'},
                    ($MyRow->{$key.'_color'}=='000000' && $oldcolor=='000000')?'FFFFFF':'000000'
                );
				$oldcolor=$MyRow->{$key.'_color'};
*/
				// check the maxpoint
				if($LetterPoint[$key]['N']>$ret['MaxPoint']) {
				    $ret['MaxPoint']=$LetterPoint[$key]['N'];
                }

				// check the minpoint
				if($LetterPoint[$key]['N'] and $LetterPoint[$key]['N']<$ret['MinPoint']) {
				    $ret['MinPoint']=$LetterPoint[$key]['N'];
                }

				if(isset($GLOBALS['CurrentTarget'])) {
					$GLOBALS['CurrentTarget'][$key] = $LetterPoint[$key];
				}
			}
		}
		$ret['MaxEnd']=$ret['MaxPoint']*$MyRow->CalcArrows;
		$ret['MaxMatch']=$ret['MaxEnd']*$MyRow->CalcEnds;
		$ret['MaxSetPoints']=($MyRow->EvMatchMode ? $MyRow->CalcEnds+2 : 0);
		$ret['MaxSO']=$ret['MaxPoint']*$MyRow->CalcSO;
		$ret['ArrowsPerEnd']=$MyRow->CalcArrows;
		$ret['Ends']=$MyRow->CalcEnds;
		$ret['SO']=$MyRow->CalcSO;
		$ret['Distance']=$MyRow->EvDistance;
		$ret['TargetRadius'] = ($MyRow->TarFullSize ? ($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122) * ($size/$MyRow->TarFullSize) * 5 : 0);
		$ret['Size']=($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122) * ($size/2);
		$ret['TargetSize']= ($MyRow->EvTargetSize ? $MyRow->EvTargetSize : 122);
		$ret['FullSize']= $MyRow->TarFullSize;
		$ret['MaxSize']= $size;
        krsort($targetRings);
        $oldColor='';

        foreach ($targetRings as $k=>$v) {
            $v['radius'] = ($ret['TargetRadius'] / $size) * $v['size'];
            if($oldColor == $v['fillColor'] AND $v['fillColor']=='000000') {
                $v['lineColor'] = 'FFFFFF';
            }
            $ret['Arrows'][$v['letter']] = $v;

            $oldColor= $v['fillColor'];
        }
	}

	return $ret;
}


function GetTarget($TourId, $TrgName='', $Value=false) {
	global $LetterPoint;
	if($TrgName) {
		$q=safe_r_SQL("select Targets.* from Targets where TarDescr='$TrgName'");
	} else {
		$q=safe_r_SQL("select Targets.* from Targets inner join TargetFaces on TfT1=TarId where TfTournament={$TourId} and TfDefault='1'");
	}

	if(!($MyRow=safe_fetch($q))) return false;

	$ret=array();

	foreach(range('Z','B') as $key) {
		if($MyRow->{$key.'_size'}) {
			// fills the accepted arrows array
            if($Value) {
                $ret[] = $LetterPoint[$key]['N'];
            } else {
                $ret[] = $LetterPoint[$key]['P'];
            }
		}
	}
	foreach(range('9','1') as $key) {
		if($MyRow->{$key.'_size'}) {
			// fills the accepted arrows array
            if($Value) {
                $ret[] = $LetterPoint[$key]['N'];
            } else {
                $ret[] = $LetterPoint[$key]['P'];
            }
		}
	}
    if($Value) {
        $ret[] = $LetterPoint['A']['N'];
        arsort($ret);
        $ret=array_unique($ret);
    } else {
        $ret[] = $LetterPoint['A']['P'];
    }

	return $ret;
}

function GetTargetColors($TourId, $TrgName='') {
	global $LetterPoint;
	if($TrgName) {
		$q=safe_r_SQL("select Targets.* from Targets where TarDescr='$TrgName'");
	} else {
		$q=safe_r_SQL("select Targets.* from Targets inner join TargetFaces on TfT1=TarId where TfTournament={$TourId} and TfDefault='1'");
	}

	if(!($MyRow=safe_fetch($q))) return false;

	$ret=array();

	$X='';
	foreach(range('Z','A') as $key) {
		if($MyRow->{$key.'_size'}) {
			// fills the accepted arrows array
			if($LetterPoint[$key]['P']=='X') $X=array($key=>$MyRow->{$key.'_color'});
			else $ret[$key] = $MyRow->{$key.'_color'};
		}
	}
	foreach(range('9','1') as $key) {
		if($MyRow->{$key.'_size'}) {
			// fills the accepted arrows array
			if($LetterPoint[$key]['P']=='X') $X=array($key=>$MyRow->{$key.'_color'});
			else $ret[$key] = $MyRow->{$key.'_color'};
		}
	}
	if($X) $ret=$X+$ret;

	return $ret;
}



/**
 * Ritorna le lettere che esistono nel target di qualifica data la persona
 * e la distanza a cui sta tirando
 * @param int $entry: persona
 * @param int $dist: distanza
 * @return chars[]: lettere presenti nel bersaglio. La 'A' (zero) ci sarà sempre
 */
	function GetGoodLettersFromDist($entry,$dist=1,&$TgtId=0)
	{
		$ret=array();

		if (!preg_match('/[1-8]{1,1}/',$dist))
			return $ret;

		$safeEntry=StrSafe_DB($entry);

		$q="
			SELECT
				Targets.*
			FROM
				Entries
				INNER JOIN
					TargetFaces
				ON EnTargetFace=TfId AND EnTournament=TfTournament
				INNER JOIN
					Targets
				ON TfT{$dist}=TarId
			WHERE
				EnId={$safeEntry}
		";
		$r=safe_r_SQL($q);

		if ($r && safe_num_rows($r)==1)
		{
			$ret[]='A';	// lo zero lo metto sempre

			$row=safe_fetch($r);
            $TgtId=$row->TarId;
			foreach (range('B','Z') as $letter)
			{
				if ($row->{$letter . '_size'}!=0)
					$ret[]=$letter;
			}
			foreach (range('1','9') as $letter)
			{
				if ($row->{$letter . '_size'}!=0)
					$ret[]=$letter;
			}
		}

		//print_r($ret);
		return $ret;
	}

/**
 * Ritorna le lettere che esistono nel target
 * @param int $target : target ID
 * @param int $SortByWeight: 0 nothing, 1 ascending, -1 descending
 * @return chars[]: lettere presenti nel bersaglio. La 'A' (zero) ci sarà sempre
 */
	function GetGoodLettersFromTgtId($target, $SortByWeight=0) {
        global $extraLetter;
		$ret=array();

		$q="SELECT * FROM Targets where TarId=$target";
		$r=safe_r_SQL($q);

		if ($row=safe_fetch($r)) {
			$ret[]='A';	// lo zero lo metto sempre
            $ret[]=$extraLetter['A'];
            foreach (range('B','Z') as $letter) {
				if ($row->{$letter . '_size'}!=0) {
                    $ret[]=$letter;
                    if($target==18 AND $letter!='N') {
                        $ret[]=$extraLetter[$letter];
                    }
                }
			}
			foreach (range('1','9') as $letter) {
				if ($row->{$letter . '_size'}!=0) $ret[]=(string) $letter;
			}

		}

		if($SortByWeight>0) {
			usort($ret, function($a, $b) {
				global $LetterPoint;
				if($LetterPoint[$a]['W']<$LetterPoint[$b]['W']) {
					return -1;
				}
				if($LetterPoint[$a]['W']>$LetterPoint[$b]['W']) {
					return 1;
				}
				return 0;
			});
		} elseif($SortByWeight<0) {
			usort($ret, function($a, $b) {
				global $LetterPoint;
				if($LetterPoint[$a]['W']<$LetterPoint[$b]['W']) {
					return 1;
				}
				if($LetterPoint[$a]['W']>$LetterPoint[$b]['W']) {
					return -1;
				}
				return 0;
			});
		}
		return $ret;
	}

	function GetTargetInfo($TrgId, $size=0) {
		global $LetterPoint;
		$q=safe_r_SQL("select Targets.* from Targets where TarId=$TrgId");

		if(!($MyRow=safe_fetch($q))) return false;

		$ret=array();

		foreach(range('9','1') as $key) {
			if($MyRow->{$key.'_size'}) {
				$ret[]=array("value"=>$LetterPoint[$key]['N'], "display"=>$LetterPoint[$key]['P'], "color"=>$MyRow->{$key.'_color'}, "diameter"=> strval($MyRow->{$key.'_size'} * $size /10));
			}
		}
		foreach(range('Z','B') as $key) {
			if($MyRow->{$key.'_size'}) {
				$ret[]=array("value"=>$LetterPoint[$key]['N'], "display"=>$LetterPoint[$key]['P'], "color"=>$MyRow->{$key.'_color'}, "diameter"=> strval($MyRow->{$key.'_size'} * $size /10));
			}
		}
		$ret[] = array("value"=>$LetterPoint['A']['N'], "display"=>$LetterPoint['A']['P'], "color"=>"FFFFFF", "diameter"=>0);

		return $ret;
	}

	function GetTargetNgInfo($TrgId, $size=0) {
		global $LetterPoint, $extraLetter;
		$q=safe_r_SQL("select Targets.* from Targets where TarId=$TrgId");

		if(!($MyRow=safe_fetch($q))) return false;

		$ret=array();

		foreach(range('9','1') as $key) {
			if($MyRow->{$key.'_size'}) {
				$col=IsDarkBackground([
					min(255,round(hexdec(substr($MyRow->{$key.'_color'},0,2))*1.5)),
					min(255,round(hexdec(substr($MyRow->{$key.'_color'},2,2))*1.5)),
					min(255,round(hexdec(substr($MyRow->{$key.'_color'},-2))*1.5))
					]) ? '#FFFFFF' : '#000000';
				$ret["$key"]=["letter" => "$key", "point" => $LetterPoint[$key]['P'], "num" => (int)$LetterPoint[$key]['N'], "bg" => "#".$MyRow->{$key.'_color'}, "fg" => $col];
			}
		}
		foreach(range('Z','A') as $key) {
			if($MyRow->{$key.'_size'}) {
				$col=IsDarkBackground([
					min(255,round(hexdec(substr($MyRow->{$key.'_color'},0,2))*1.5)),
					min(255,round(hexdec(substr($MyRow->{$key.'_color'},2,2))*1.5)),
					min(255,round(hexdec(substr($MyRow->{$key.'_color'},-2))*1.5))
				]) ? '#FFFFFF' : '#000000';
				$ret[$key]=["letter" => $key, "point" => $LetterPoint[$key]['P'], "num" => (int)$LetterPoint[$key]['N'], "bg" => "#".$MyRow->{$key.'_color'}, "fg" => $col];
                if($TrgId==18 AND $key!='N') {
                   $ret[strtolower($key)] = ["letter" => $extraLetter[$key], "point" => $LetterPoint[$extraLetter[$key]]['P'], "num" => (int)$LetterPoint[$extraLetter[$key]]['N'], "bg" => "#" . $MyRow->{$key . '_color'}, "fg" => $col];
                }
			}
		}
		if(empty($ret['A'])) {
			$ret['A'] = ["letter" => "A", "point" => "M", "num" => 0, "bg" => "#999999", "fg" => "#000000"];
            if($TrgId==18) {
               $ret['a'] = ["letter" => $extraLetter["A"], "point" => $LetterPoint[$extraLetter["A"]]['P'], "num" => 0, "bg" => "#999999", "fg" => "#000000"];
            }
		}

		// need to sort based on the weight of the $LetterPoint
		uksort($ret, function($a,$b) {
			global $LetterPoint;
			return $LetterPoint[strtoupper($b)]['W']<=>$LetterPoint[strtoupper($a)]['W'];
		});
		return array_values($ret);
	}

function GetMaxTargetValue($TargetLetters) {
	global $LetterPoint;
	$ret=0;

	foreach($TargetLetters as $Letter) {
		if($LetterPoint[$Letter]['N']>$ret) $ret=$LetterPoint[$Letter]['N'];
	}

	return $ret;
}
function GetMinTargetValue($TargetLetters) {
	global $LetterPoint;
	$ret=99999;

	foreach($TargetLetters as $Letter) {
		if($LetterPoint[$Letter]['N']>0 and $LetterPoint[$Letter]['N']<$ret) $ret=$LetterPoint[$Letter]['N'];
	}

	return $ret;
}
/**
 * returns how many points to add to the original value if all stars are hit!
 * @param string $ArrowString the arrowstring to evaluate with all stars raised to the upper value
 * @param string $Regexp the class of callable stars (will be set if an empty string is passed)
 * @param string $Event Event where this funziont is needed
 * @param number $TeamEvent idem
 * @param number $TourId if none the current comp is used
 * @return number
 */
function RaiseStars($ArrowString, &$Regexp='', $Event='', $TeamEvent=0, $TourId=0, $TargetId=0) {
	static $Targets=[];
	global $LetterPoint;
	if(!$Regexp) {
		if(!$TourId) $TourId=$_SESSION['TourId'];
		$q=safe_r_sql("select TarStars from Targets inner join Events on EvTournament={$TourId} and EvFinalTargetType=TarId and EvCode='$Event' and EvTeamEvent=$TeamEvent");
		if($r=safe_fetch($q)) $Regexp=$r->TarStars;
	}

	if(!$Regexp) return 0;

	if($TargetId) {
		if(empty($Targets[$TargetId])) {
			$Targets[$TargetId]=GetGoodLettersFromTgtId($TargetId, 1);
		}
		$Sum=0;
		$Matches=[];
		preg_match_all('/['.$Regexp.']/', $ArrowString, $Matches);
		foreach($Matches[0] as $ar) {
			$ArActual=strtoupper($ar);
			if(in_array($ArActual, $Targets[$TargetId])) {
				$Next=array_search($ArActual, $Targets[$TargetId])+1;
				if(isset($Targets[$TargetId][$Next])) {
					$ArNext=$Targets[$TargetId][$Next];
					$Sum+=($LetterPoint[$ArNext]['N']-$LetterPoint[$ArActual]['N']);
				}
			}
		}
		return $Sum;
	}

	return strlen($ArrowString) - strlen(preg_replace('/['.$Regexp.']/', '', $ArrowString));
}

function getLettersFromPrintList($printList, $FilterFromTarget=0) {
    global $LetterPoint;
    static $Targets=[];
    $retValue = '';
    if($FilterFromTarget and empty($Targets[$FilterFromTarget])) {
        $Targets[$FilterFromTarget]=GetGoodLettersFromTgtId($FilterFromTarget);
    }

    if(!is_array($printList)) {
        if(strpos(str_replace(' ','',$printList),',')!==false) {
            $printList = explode(',',str_replace(' ','',$printList));
        } else if(strpos(trim($printList),' ')!==false) {
            $printList = explode(',',trim($printList));
        } else {
            $printList = array($printList);
        }
    }
    foreach ($printList as $v) {
        foreach ($LetterPoint as $k=>$l) {
            if ($v==$l['P'] and (!$FilterFromTarget or in_array($k, $Targets[$FilterFromTarget]))) {
                $retValue .= $k;
            }
        }
    }
    return $retValue;
}
