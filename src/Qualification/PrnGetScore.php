<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/LabelPDF.inc.php');
require_once('Common/Fun_Sessions.inc.php');

if(CheckTourSession()) {
    checkACL(AclQualification, AclReadOnly);

	$pdf = new LabelPDF();

	error_reporting(E_ALL);

	$ath4target=0;
	$RowsPerPage=4;
	$ses=GetSessions('Q');
	foreach ($ses as $s)
	{
		$ath4target=max($ath4target, $s->SesAth4Target);
		if($ath4target>8) {
			$RowsPerPage=2;
		} elseif($ath4target>4) {
			$RowsPerPage=3;
		}
	}

	$Session=max(0, intval($_REQUEST['x_Session'] ?? $_REQUEST["Session"] ?? 0));
	$From=intval($_REQUEST['x_From'] ?? 0);
	$To=intval($_REQUEST['x_To'] ?? 0);

	$MyQuery = "SELECT ToCategory&12 as IsField3D, ToNumEnds, EnName AS Name, EnFirstName AS FirstName, AtSession AS Session, AtTarget AS TargetNo,  AtLetter AS BackNo,  IF(TfGolds='',ToGolds,TfGolds) as Golds, IF(TfXNine='',ToXNine,TfXNine) as XNine
		FROM AvailableTarget
		INNER JOIN Tournament AS t ON t.ToId=AtTournament";
	if(!empty($_REQUEST["noEmpty"])) {
		$MyQuery .= " INNER JOIN
				(SELECT DISTINCT EnTournament, QuTarget, QuSession
				FROM Qualifications
				INNER JOIN Entries On QuId=EnId
				WHERE EnTournament = {$_SESSION['TourId']} ".($Session ? " AND QuSession=$Session" : "").($To>0 ? " and QuTarget between $From and $To" : "")."
				) as Tgt ON AtTournament=Tgt.EnTournament AND AtTarget=QuTarget and AtSession=QuSession	";
	}
	$MyQuery.= "LEFT JOIN (
					SELECT EnName, EnFirstName, EnTournament, QuTargetNo, TfGolds, TfXNine
					FROM Entries AS e 
					INNER JOIN Qualifications AS q ON e.EnId=q.QuId AND e.EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EnAthlete=1
					left join TargetFaces ON TfTournament=EnTournament and EnTargetFace=TfId
					) sq ON AtTournament=sq.EnTournament AND AtTargetNo=sq.QuTargetNo 
				WHERE AtTournament = {$_SESSION['TourId']} ".($Session ? " AND AtSession=$Session" : "").($To>0 ? " and AtTarget between $From and $To" : "");

	$MyQuery.= " ORDER BY if(ToCategory&12, (substr(AtTargetNo,2 , 3)-1)%ToNumEnds, 1), AtTargetNo, Name, FirstName ";

	$Rs=safe_r_sql($MyQuery);
    $Etichetta=-1;
    $CurrentTarget=0;
    $NewTarget=true;
    $CurHeight=0;

    $BlockHeight=($pdf->getPageHeight()-30)/$RowsPerPage;
    $CellHeight=$BlockHeight/($ath4target+1);
    $BlocksPerPage=$RowsPerPage*3;

    while($MyRow=safe_fetch($Rs)) {
        if($CurrentTarget != $MyRow->Session . $MyRow->TargetNo) {
            $Etichetta = ++$Etichetta % $BlocksPerPage;
            $NewTarget=true;
        }
        $CurrentTarget = $MyRow->Session . $MyRow->TargetNo;

        if($Etichetta==0 && $NewTarget)
            $pdf->AddPage();

        if($NewTarget) {
            $aths=$ath4target;

            $CurHeight = $CellHeight;

            $pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5) +5));
            $pdf->SetFont($pdf->FontStd,'B',25);
            $tgt=(int)$MyRow->TargetNo;
            if($MyRow->IsField3D) {
                if($MyRow->TargetNo > $MyRow->ToNumEnds) {
                    $tgt = CheckBisTargets($MyRow->TargetNo, $MyRow->ToNumEnds);
                }
            }
            $pdf->Cell(60,$CurHeight, $tgt,1,0,'C',1);


            $pdf->SetFont($pdf->FontStd,'B',20);
            //for($i=1; $i<=$MyRow->{"ToAth4Target" . $MyRow->Session}; $i++)
            for($i=1; $i<=$aths; $i++) {
                $pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+($i*$CurHeight));
                $pdf->Cell(18, $CurHeight, chr(ord("A")-1+$i), '1', 0, 'C', 0, '', 1, '', '', 'T' );
                $pdf->Cell(28, $CurHeight, '', 1, 0, 'C');
                $pdf->Cell(14, $CurHeight, '', 1, 0, 'C');
            }
            $NewTarget=false;
        }
        $pdf->SetXY((($Etichetta % 3) * 70)+5,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+((ord($MyRow->BackNo)-ord("A")+1)*$CurHeight)+0.6*$CurHeight);
        $pdf->SetFont($pdf->FontStd,'',10);
        $pdf->Cell(18,0.4*$CurHeight,$MyRow->FirstName ?? '',0,0,'C',0);
        $pdf->SetXY((($Etichetta % 3) * 70)+23,(intval($Etichetta / 3) * ($BlockHeight+5)+5)+((ord($MyRow->BackNo)-ord("A")+1)*$CurHeight));
        $pdf->SetFont($pdf->FontStd,'',8);
        $pdf->Cell(28,0.1*$CurHeight,get_text('Score','Tournament'),0,0,'C',0);
        $pdf->Cell(8,0.1*$CurHeight,$MyRow->Golds  ?? '',0,0,'L',0);
        $pdf->Cell(6,0.1*$CurHeight,$MyRow->XNine  ?? '',0,0,'R',0);
    }
    safe_free_result($Rs);
	$pdf->Output();
}
