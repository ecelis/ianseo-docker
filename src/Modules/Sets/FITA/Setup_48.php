<?php
/*
48 	RUN ARCHERY

Ogni gara consta di una "qualifica" ed eventualmente una fase "finale" (da che fase? sembrerebbe dalla semifinale)
Tabella RunArchery usabile anche nel biathlon?

Parametri di gara
- durata penalità Freccia integer che se mettono 5 minuti (300 secondi) siamo fregati con un tinyint => ToMaxFinIndScore
- durata penalità Loop (idem) => ToMaxFinTeamScore
- delay di startlist => ToMaxDistScore

Parametri di EVENTO:
- tipo start tinyint (partenze a gruppi o individuali, vale 0 se tutti gruppi) => EvElimType
- lunghezza lap (giro) int => EvTargetSize
- numero lap tinyint => EvFinEnds
- frecce da tirare tinyint => EvFinArrows
- frecce spare tinyint => EvFinSO
- bersagli da colpire: EvE1Arrows
- lunghezza loop penalità int => EvDistance
- Finalfirstphase: se 0 fanno solo qualifica
- EvElim1 => numero qualificati per le semifinali
- EvElim2 => numero qualificati per la finale
- EvShootOff => terminata la fase di qualifica => tutti i tempi RaDateTimeFinish vengono chiusi
- EvE1ShootOff => terminata la semifinale => tutti i tempi RaDateTimeFinish vengono chiusi
- EvE2ShootOff => terminata la finale => tutti i tempi RaDateTimeFinish vengono chiusi

Parametri di Entry RunArcRanking
- RarEntry
- RarTournament
- RarTeam
- RarSubTeam
- RarEvent
- RarPhase (0 solo qualifica, 1 finale, 2 semifinale, analogo a finalfirstphase...)
- RarStartlist
- RarDateTimeStart (millisecond from epoch?)
- RarDateTimeFinish (millisecond from epoch?)
- RarTimeTotal
- RarArrowTotalPenalty (dec 5,1 to go from 0,0 to 9999,9 seconds of penalty)
- RarLoopTotalPenalty
- RarTimeAdjustPlus
- RarTimeAdjustMinus
- RarTimeFinal
- RarRank
- RarLastUpdate

Parametri di Lap di gara
- RaEntry
- RaTournament
- RaTeam
- RaSubTeam
- RaEvent
- RaPhase (0 solo qualifica, 1 finale, 2 semifinale, analogo a finalfirstphase...)
- RaLap (1..n per i giri)
- RaLaptime (millisecond)
- RaArrowsShot (tinyint)
- RaHits (tinyint)
- RaLoopAssigned
- RaLoopDone
- RaArrowPenalty (dec 5,1 to go from 0,0 to 9999,9 seconds of penalty)
- RaLoopPenalty
- RaLastUpdate

*/

$TourType=48;

$tourDetTypeName		= 'Type_WA_RunArchery';
$tourDetNumDist			= '1';
$tourDetNumEnds			= '1';
$tourDetMaxDistScore	= '30'; // delay in seconds between starts
$tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= '10+X';
$tourDetXNine			= 'X';
$tourDetGoldsChars		= 'KL';
$tourDetXNineChars		= 'K';
$tourDetDouble			= '0';
$tourDetMaxFinIndScore  = 0;
$tourDetMaxFinTeamScore = 0;

$DistanceInfoArray=array(array(1, 1));

require_once('Setup_RunArchery.php');

