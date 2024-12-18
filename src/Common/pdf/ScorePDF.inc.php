<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once("Common/pdf/IanseoPdf.php");
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Sessions.inc.php');

class ScorePDF extends IanseoPdf {
	var $PrintLogo, $PrintHeader, $PrintDrawing, $PrintFlags, $PrintBarcode, $FillWithArrows=false, $PrintLineNo = true;
    var $IsRedding=false;

	/**
	 * If set to true adds a row with EnCode, Date of Birth and Email.
	 * @var bool [default: false]
	 */
	var $GetArcInfo=false;
	var $PrintTotalCols, $LastUpdate;
	var $BottomImage=true;
    var $NoTensOnlyX = false;
    var $ScoreQrPersonal = false;
    var $QRCode = [];

	//Constructor
	function __construct($Portrait=true) {
		parent::__construct('Scorecard', $Portrait);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->SetMargins(10,10,10);
		$this->SetAutoPageBreak(false, 10);
	    $this->PrintLogo = true;
	    $this->PrintHeader = true;
	    $this->PrintDrawing = true;
	    $this->PrintFlags = true;
		$this->PrintTotalCols = false;
		$this->PrintBarcode=false;
        $this->PrintLineNo = true;
		$this->SetSubject('Scorecard');
		$this->SetColors();
	}

	function Footer() {
		$this->SetDefaultColor();
		$this->SetFont($this->FontStd,'B',7);
		$this->SetXY(IanseoPdf::sideMargin,$this->h - 16);
		$this->multicell(0, 0, get_text('ScoreSingleWarning', 'Tournament'), 1, 'C', '', 1);
	}

	function SetColors($Datum=false, $Light=false) {

		if($this->PrintDrawing) {
			$this->SetTextColor(0x00, 0x00, 0x00);
			$this->SetDrawColor(0x33, 0x33, 0x33);
			if($Light)
				$this->SetFillColor(0xF8,0xF8,0xF8);
			else
				$this->SetFillColor(0xE8,0xE8,0xE8);
		} else {
			$this->SetDrawColor(0xFF, 0xFF, 0xFF);
			$this->SetFillColor(0xFF, 0xFF, 0xFF);
			if($Datum)
				$this->SetTextColor(0x00, 0x00, 0x00);
			else
				$this->SetTextColor(0xFF, 0xFF, 0xFF);
		}
	}


	function HideLogo() {
	    $this->PrintLogo = false;
	}

	function HideFlags() {
	    $this->PrintFlags = false;
	}

	function HideHeader() {
	    $this->PrintHeader = false;
	}

	function NoDrawing() {
	    $this->PrintDrawing = false;
	}

	function PrintTotalColumns() {
		$this->PrintTotalCols = true;
	}

	function NoLineNumbers() {
        $this->PrintLineNo = false;
    }

	function NoTensOnlyX() {
		$this->NoTensOnlyX = true;
		$this->setPrintFooter(true);
	}

// NEW DRAW SCORE
	function DrawScoreNew($TopX, $TopY, $Width, $Height, $Distance=0, $Data=array('Session'=>1)) {
		global $CFG;
		static $ArrowEnds=array(), $StdFont, $StdFontSmall, $StdFontMedium;

		// $ArrowEnds will contain the ends per arrows of each event and distance
		$Event=(empty($Data["Cat"]) || !trim($Data["Cat"]) ? '--' : $Data["Cat"]);
		$Session=(empty($Data["Session"]) ? '1' : $Data["Session"]);
		$CurDist=(empty($Distance) ? 0 : $Distance);
		$FirstDist=($Distance==1);

		if(empty($ArrowEnds[$Event][$CurDist])) {
			if(empty($Data['isField']) or empty($Data["ScoringEnds{$CurDist}"])) {
				if(empty($Data["NumEnds".$CurDist]) OR empty($Data["NumArrows".$CurDist])) {
					$ArrowEnds[$Event] = getArrowEnds($Session);
				} else {
					$ArrowEnds[$Event][$CurDist] = array('ends' => $Data["NumEnds".$CurDist], 'totEnds' => $Data["NumEnds".$CurDist], 'arrows' => $Data["NumArrows".$CurDist], 'offset'=>0);
				}
			} else {
				$ArrowEnds[$Event][$CurDist] = array('ends' => $Data["ScoringEnds{$CurDist}"], 'totEnds' => $Data["NumEnds".$CurDist], 'arrows' => $Data["NumArrows".$CurDist], 'offset'=>$Data["ScoringOffset".$CurDist]);
			}
			$ArrowEnds[$Event][$CurDist]['offset']=($ArrowEnds[$Event][$CurDist]['offset']??0);
			$ArrowEnds[$Event][$CurDist]['totEnds']=($ArrowEnds[$Event][$CurDist]['totEnds']??$Data["NumEnds".$CurDist]);
		}

		$prnGolds = (empty($Data["Golds"]) ? $this->prnGolds : $Data["Golds"] );
		$prnXNine = (empty($Data["XNine"]) ? $this->prnXNine : $Data["XNine"]);


		if(empty($ArrowEnds[$Event][$CurDist]['ends'])) {
			$ArrowEnds[$Event][$CurDist]['ends'] = 6;
		}
		if(empty($ArrowEnds[$Event][$CurDist]['arrows'])) {
			$ArrowEnds[$Event][$CurDist]['arrows'] = 6;
		}
		$NumEnd=$ArrowEnds[$Event][$CurDist]['ends'];
		$NumArrow=$ArrowEnds[$Event][$CurDist]['arrows'];

		if($NumArrow==6 and $NumEnd==6) {
			$NumArrow=3;
			$NumEnd=12;
		} elseif($NumArrow==6 and $NumEnd==5) {
			$NumArrow=3;
			$NumEnd=10;
		}
		if($NumArrow==1 AND ($NumEnd%3)==0) {
			$NumArrow=3;
			$NumEnd=$NumEnd/3;
		} else if($NumArrow==1 AND ($NumEnd%5)==0 AND isset($_SESSION["TourType"]) AND $_SESSION["TourType"]!=49) {
			$NumArrow=5;
			$NumEnd=$NumEnd/5;
		}

		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$this->ToPaths['ToLeft']) ) {
				$im=getimagesize($IM);
				$this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$this->ToPaths['ToRight']) ) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * 15 / $im[1]);
				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			// Sponsors disabled if QRCodes are to be printed!!!
			if($this->BottomImage and file_exists($IM=$this->ToPaths['ToBottom'])) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] / $im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] / $im[1] ;
				}
				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+(empty($Data['SecondScorer']) ? 0 : 25)+$Height-$imgH), $imgW, $imgH);
			}
		}

		$CellW = ($Width / ($NumArrow+5));
		$ExtraRows=3;
		if($this->NoTensOnlyX) {
			$ExtraRows=(2+($Distance ? $Distance : 1)) ;
		}

		$CellH = min(10,($Height-41-$BottomImage-4*intval($this->GetArcInfo))/($NumEnd + $ExtraRows));
		if(empty($StdFont)) {
			$StdFont=min(10, $CellH*2);
			$StdFontSmall=$StdFont*.8;
			$StdFontMedium=$StdFont*.9;
		}
        $NumDist = 0;

		//TESTATA GARA
		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(true);
	    	$this->SetFont($this->FontStd,'B',9);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Name, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight) {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where, 0, 'L', 0);
				$this->SetXY($TopX+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			} else {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
		}

		//DATI ATLETA
		$FlagOffset=0.2*$CellW;
		$this->SetXY($FlagOffset+$TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
		if($this->PrintFlags and !empty($Data['CoCode'])) {
			if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$Data['CoCode'].'.jpg')) {
				$H=$TopOffset*3/8;
				$W=$H*3/2;
				$OrgY=$this->gety();
				@$this->Image($file, $TopX, $this->gety(), $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
				$FlagOffset=$W+1;
			}
		}

		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*7/12));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$ArcherStringLength=$this->GetStringWidth((get_text('Archer') . ": "));
		$this->Cell($ArcherStringLength,$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
		$this->SetY($this->gety()-2, false);
		$this->SetFont($this->FontStd,'B',13);
		$this->SetColors(true);
		$this->Cell($Width-(($this->PrintTotalCols && empty($FirstDist)) ? 2.7*$CellW : 1.6*$CellW)-$ArcherStringLength - $FlagOffset,2+($TopOffset/6), ($Data["Ath"] ?? ' '),'B',0,'L',0);
		$this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*19/24));
		$this->SetFont($this->FontStd,'',8);

		// Country
		$this->SetColors(false);
		$CountryWidth=$this->GetStringWidth((get_text('Country') . ": "));
		$this->Cell($CountryWidth, $TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$CellTmpWidth=$Width-(($this->PrintTotalCols && empty($FirstDist)) ? 2.7*$CellW : 1.6*$CellW)-$CountryWidth - $FlagOffset;
		if(!empty($Data["Noc"])) {
			$str=empty($Data['CoCode']) ? '' : $Data['CoCode'].' -';
			$strW=$this->GetStringWidth($str);
			$this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
			$this->Cell($CellTmpWidth-$strW, $TopOffset/6, ($Data['CoName'] ?? ''),'B',0,'L',0);
		} else {
			$this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
		}

		//PAGLIONE
		$Target='';
		if(!empty($Data["tNo"])) {
            if(!empty($Data['is3dNFAA'])) {
                $Target=ReddingGrouping($Data['tNo'],$ArrowEnds[$Event][$CurDist]['totEnds']);
            } else if(!empty($Data['isField'])) {
				$Target=CheckBisTargets($Data['tNo'],$ArrowEnds[$Event][$CurDist]['totEnds']);
			} else {
				$Target=ltrim($Data["tNo"],'0');
			}
		}
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*13/24));
		$this->SetFont($this->FontStd,'B',20);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*7/24, $Target,0,0,'R',1);
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*10/12));
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*2/12,(!empty($Data["Cat"]) ? $Data["Cat"] : ' '),'T',0,'C',1);
		if($this->PrintTotalCols && empty($FirstDist)) {
			$this->SetFont($this->FontStd,'B',8);
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->SetXY($TopX+$Width-(2.5*$CellW), $TopY+($TopOffset*16/24));
			$this->Cell((1.1*$CellW),$TopOffset*4/24,get_text('Total'),1,0,'C',1);
			$this->SetXY($TopX+$Width-(2.5*$CellW), $TopY+($TopOffset*20/24));
			$this->Cell((1.1*$CellW),$TopOffset*4/24,($Data['Arr'.$Distance] == "" ? '' : $Data['Tot'.$Distance]),1,0,'C',1);
			$this->SetFont($this->FontStd,'B',10);
		}

		//HEADER DELLO SCORE
		$ArCellW=($this->PrintTotalCols ? 0.9 : 1)*$CellW;
		$EndNumCellW=0.8*$CellW;
		$TotalCellW=($this->PrintTotalCols ? 1 : 1.4)*$CellW;
		$XNineW=(($this->NoTensOnlyX or $prnGolds==$prnXNine) ? 1.4 : 0.7)*$CellW;

		if($this->NoTensOnlyX) {
			$TopOffset+=$CellH/2;
			if(!empty($Distance) and $Distance>1) {
				// prints previous distances
				for($i=1; $i<$Distance; $i++) {
					$this->SetXY($TopX + $ArCellW*$NumArrow, $TopY+$TopOffset);
					$this->Cell($TotalCellW + $EndNumCellW, $CellH, get_text('FlightsDistTotal', 'Tournament', $Data['D'.$i]??''),0,0,'R',0);
					$this->Cell($TotalCellW, $CellH,($Data['QuD'.$i] ?? ''),1,0,'C',0);
					$this->Cell($XNineW, $CellH,($Data['QuXD'.$i] ?? ''),1,1,'C',0);
					$TopOffset+=$CellH;
				}
			}
		}

		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',$StdFontSmall);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
        $this->Cell($EndNumCellW, $CellH, ($Data["D".$Distance] ?? ''), 0, 0, 'C', 1);
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd,'B',$StdFont);
		$this->SetColors(false);
		for($j=1; $j<=$NumArrow; $j++) {
			$this->Cell($ArCellW  + ($NumArrow==1 ? $TotalCellW:0), $CellH, $j, 1, 0, 'C', 1);
		}
		$this->SetFont($this->FontStd,'B',$StdFontSmall-($CellH*0.5 < 3? 2.5:0));
		$this->SetColors(true);
		$this->Cell(($TotalCellW*($NumArrow>5 ? 1.5 : 2))+($this->PrintTotalCols ? $CellW*1.1 : 0)+($XNineW*(($this->NoTensOnlyX or $prnGolds==$prnXNine) ? 1 : 2)) - ($NumArrow==1 ? $TotalCellW:0), $CellH*0.5, (isset($Data['SesName']) ? ($Data['SesName']!=='' ? $Data['SesName'] : get_text('Session') . ': ' . $Data["Session"]) : '' ),1,1,'R',1);
		$this->SetColors(false);
		$this->SetXY($TopX+$EndNumCellW+($ArCellW*$NumArrow) + ($NumArrow==1 ? $TotalCellW:0), $TopY+$TopOffset+$CellH * 0.5);
	   	$this->SetFont($this->FontStd,'B', $StdFontSmall-($CellH*0.5 < 3? 2.5:0));
        if($NumArrow>1) {
            $this->Cell($TotalCellW * ($NumArrow > 5 ? 3 / 4 : 1), $CellH * 0.5, (get_text('TotalProg', 'Tournament')), 1, 0, 'C', 1);
        }
        $this->Cell($TotalCellW * ($NumArrow > 5 ? 5 / 4 : 1), $CellH * 0.5, (get_text('TotalShort', 'Tournament')), 1, 0, 'C', 1);
        if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell($CellW*1.1,$CellH*0.5, (get_text('Total')),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		if(!($this->NoTensOnlyX or $prnGolds==$prnXNine)) {
			$this->Cell($XNineW,$CellH*0.5, ($prnGolds),1,0,'C',1);
		}
		$this->Cell($XNineW,$CellH*0.5, ($prnXNine),1,1,'C',1);

// 		DISTANZA => $Data["CurDist"];
		//RIGHE DELLO SCORE
        $StopFillingtotals=false;
		$ScoreMultiLineTotal = 0;
		$ScoreTotal = 0;
		$ScoreGold = 0;
		$ScoreXnine = 0;
		$StartCell=true;
		$End=(empty($Data['isField']) ? 1 : ((intval($Target)-1+($ArrowEnds[$Event][$CurDist]['offset']))%$ArrowEnds[$Event][$CurDist]['totEnds']) + 1);
		$HeighEndCell=$CellH*($NumEnd/$ArrowEnds[$Event][$CurDist]['ends']);
		for($i=1; $i<=$NumEnd; $i++) {
		   	$this->SetFont($this->FontStd,'B',$StdFont);
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			if($StartCell) {
				$this->Cell($EndNumCellW, $HeighEndCell, $End++, 1, 0, 'C', 1);
			} else {
				$this->SetX($TopX+$EndNumCellW);
			}
			$this->SetFont($this->FontStd,'',$StdFontMedium);
			if(!empty($Data['Arr'.$Distance])) {
				for($j=0; $j<$NumArrow; $j++) {
					$this->Cell($ArCellW+ ($NumArrow==1 ? $TotalCellW:0),$CellH,DecodeFromLetter(substr($Data['Arr'.$Distance],($i-1)*$NumArrow+$j,1)), 1, 0, 'C', 0);
				}
			} else {
				for($j=0; $j<$NumArrow; $j++) {
					$this->Cell($ArCellW+ ($NumArrow==1 ? $TotalCellW:0),$CellH,'', 1, 0, 'C', 0);
				}
			}
            $StopFillingtotals = (trim(substr(($Data['Arr'.$Distance]??''),($i-1)*$NumArrow,$NumArrow))=='');
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr(($Data['Arr'.$Distance]??''),($i-1)*$NumArrow,$NumArrow),$Data['GoldsChars'], $Data['XNineChars']);
			$ScoreMultiLineTotal += $ScoreEndTotal;
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
            if($NumArrow>1) {
                $this->Cell($TotalCellW * ($ArrowEnds[$Event][$CurDist]['arrows'] > 5 ? 3 / 4 : 1), $CellH, (($StopFillingtotals OR empty($Data['Arr' . $Distance])) ? '' : $ScoreEndTotal), 1, 0, 'C', 0);
            }
			$this->SetFont($this->FontStd,'',$StdFont);
			if(($NumArrow*$i)%$ArrowEnds[$Event][$CurDist]['arrows']) {
				$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $CellH,'',1,0,'C',0);
				$this->Line($x1=$this->getX(), $y1=$this->getY(), $x1-$TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $y1+$CellH);
				$this->Line($x1-$TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 5/4 : 1), $y1, $x1, $y1+$CellH);
				$StartCell=false;
			} else {
				if($ArrowEnds[$Event][$CurDist]['arrows']>5) {
					$this->Cell($TotalCellW*2/4, $CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance])) ? '' : $ScoreMultiLineTotal),1,0,'C',0);
					$this->Cell($TotalCellW*3/4, $CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance])) ? '' : $ScoreTotal),1,0,'C',0);
					$ScoreMultiLineTotal = 0;
				} else {
					$this->Cell($TotalCellW, $CellH,(empty($Data['Arr'.$Distance]) ? '' : $ScoreTotal),1,0,'C',0);
				}
				$StartCell=true;
			}

			if($this->PrintTotalCols) {
				$this->SetFillColor(0xFF,0xE8,0xE8);
				$this->SetFont($this->FontStd,'',$StdFontMedium);
				if(($NumArrow*$i)%$ArrowEnds[$Event][$CurDist]['arrows']) {
					$this->Cell(1.1*$CellW,$CellH,'',1,0,'C',1);
					$this->Line($x1=$this->getX(), $y1=$this->getY(), $x1-(1.1*$CellW), $y1+$CellH);
					$this->Line($x1-(1.1*$CellW), $y1, $x1, $y1+$CellH);
				} else {
					$this->Cell(1.1*$CellW,$CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance])) ? '' : $ScoreTotal + $Data['Tot'.$Distance]),1,0,'C',1);
					if(!empty($FirstDist) && $Data['Arr'.$Distance] == "") {
						$this->Line($this->GetX(),$this->GetY(),$this->GetX()-1.1*$CellW,$this->GetY()+$CellH);
						$this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-1.1*$CellW,$this->GetY());
					}
				}
				$this->SetFillColor(0xE8,0xE8,0xE8);
			}
			$this->SetFont($this->FontStd,'',$StdFontSmall);
			if(!($this->NoTensOnlyX or $prnGolds==$prnXNine)) {
				$this->Cell($XNineW, $CellH,(empty($Data['Arr'.$Distance]) || !$ScoreEndGold ? '' : $ScoreEndGold),1,0,'C',0);
			}
			$this->Cell($XNineW, $CellH,(empty($Data['Arr'.$Distance]) || !$ScoreEndXnine ? '' : $ScoreEndXnine),1,1,'C',0);

			if(!empty($Data['isField']) and $End>$ArrowEnds[$Event][$CurDist]['totEnds']) {
				$End=1;
			}
		}

		// CODICE A BARRE
		$BCode=0;
		if($this->PrintBarcode and !empty($Data['EnCode'])) {
			$this->SetColors(true);
			$this->SetXY($TopX-2, $TopY+$TopOffset+$CellH*($NumEnd+1)-1);
			$this->SetFont('barcode','',22);
			$BCode=($NumArrow+($this->PrintTotalCols ? 0.6 : 1.3))*$CellW;
			if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
			$this->Cell($BCode +3, $CellH, mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . ($Distance ? '-'.$Distance : ''), "UTF-8","cp1252") . "*",0,0,'C',0);
// 			$this->write1DBarcode(mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . (array_key_exists("CurDist",$Data) ? '-'.$Data["CurDist"] : ''), "UTF-8","cp1252") . "*",
// 					'C39E', $TopX, $TopY+$TopOffset+$CellH*($NumEnd+1)+1, $BCode, $CellH-1);//, (float) $xres, (array) $style, (string) $align);

			$this->SetFont($this->FontStd,'',7);
			$this->ln();
			$this->SetXY($TopX-2, $this->GetY()-2);
			$this->Cell($BCode +3, $CellH, mb_convert_encoding($Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'] . ($Distance ? '-'.$Distance : ''), "UTF-8","cp1252"),0,0,'C',0, '', 1, false, 'T', 'T');
			$this->SetColors(false);
		}


		//TOTALE DELLO SCORE
		$ErScoreTotal = empty($Distance) ? '' : (($Data['Arr'.$Distance]??'') and ($Data["QuD{$Distance}"]??0)!=$ScoreTotal);
		$ErScoreGold  = empty($Distance) ? '' : (($Data["QuGD{$Distance}"]??0)!=$ScoreGold);
		$ErScoreXNine = empty($Distance) ? '' : (($Data["QuXD{$Distance}"]??0)!=$ScoreXnine);

		$this->SetXY($TopX + $BCode, $TopY+$TopOffset+$CellH*($NumEnd +1));
	   	$this->SetFont($this->FontStd,'B',11);
		$this->Cell(($NumArrow+($this->PrintTotalCols ? 1.5 : 2.2))*$CellW - $BCode + ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? $TotalCellW/4 : 0),$CellH, (get_text('Total') . " "),0,0,'R',0);
		$this->Cell($TotalCellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1),$CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance]))? '' : $ScoreTotal),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreTotal)
			$this->Line($x1 = $this->getx() - (($this->PrintTotalCols ? 1 : 1.4)*$CellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1)), $y1=$this->gety()+$CellH, $x1+($this->PrintTotalCols ? 1 : 1.4)*$CellW*($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1), $y1-$CellH);
		if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell(1.1*$CellW,$CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance])) ? '' : $ScoreTotal + $Data['Tot'.$Distance]),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		$this->SetFont($this->FontStd,'B',9);
		if(!($this->NoTensOnlyX or $prnGolds==$prnXNine)) {
			$this->Cell($XNineW,$CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance])) ? '' : $ScoreGold),1,0,'C',0);
			if($this->FillWithArrows && $ErScoreGold) {
				$this->Line($x1 = $this->getx() - 0.7*$CellW, $y1=$this->gety()+$CellH, $x1+0.7*$CellW, $y1-$CellH);
			}
		}
		$this->Cell($XNineW,$CellH,(($StopFillingtotals OR empty($Data['Arr'.$Distance])) ? '' : $ScoreXnine),1,0,'C',0);
		if($this->FillWithArrows && $ErScoreXNine) {
			$this->Line($x1 = $this->getx() - 0.7*$CellW, $y1=$this->gety()+$CellH, $x1+0.7*$CellW, $y1-$CellH);
		}

		if(($this->NoTensOnlyX) and !empty($Distance) and $Distance>1) {
			$this->ln();
			// prints Grand Total
			$Tot=0;
			$TotX=0;
			for($i=1; $i<=$Distance; $i++) {
				$Tot+=$Data['QuD'.$i]??0;
				$TotX+=$Data['QuXD'.$i]??0;
			}
			$this->SetX($TopX + $ArCellW*$NumArrow);
			$this->Cell($TotalCellW + $EndNumCellW, $CellH, get_text('RunningTotal', 'Tournament', ($Data['D'.$i] ?? '')),0,0,'R',0);
			$this->Cell($TotalCellW, $CellH, $this->FillWithArrows ? $Tot : '',1,0,'C',0);
			$this->Cell($XNineW, $CellH, $this->FillWithArrows ? $TotX : '',1,1,'C',0);
		}

		$this->ln($CellH/2);

		if($this->FillWithArrows && ($ErScoreTotal or $ErScoreGold or $ErScoreXNine)) {
            $this->ln($CellH/2);
			$this->SetX($TopX + $BCode);
		   	$this->SetFont($this->FontStd,'B',11);
			$this->Cell(($NumArrow+($this->PrintTotalCols ? 1.5 : 2.2))*$CellW - $BCode + ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? $TotalCellW/4 : 0),$CellH, (get_text('SignedTotal', 'Tournament') . " "),0,0,'R',0);
			$this->Cell($TotalCellW * ($ArrowEnds[$Event][$CurDist]['arrows']>5 ? 3/4 : 1),$CellH, $Data["QuD{$Distance}"] ,1,0,'C',0);
			if($this->PrintTotalCols)
			{
				$this->Cell(1.1*$CellW,$CellH,'',0,0,'C',0);
			}
			$this->SetFont($this->FontStd,'B',9);
			if(!($this->NoTensOnlyX or $prnGolds==$prnXNine)) {
				$this->Cell($XNineW,$CellH, ($Data["QuGD{$Distance}"] ?? ''),1,0,'C',0);
			}
			$this->Cell($XNineW,$CellH, ($Data["QuXD{$Distance}"] ?? ''),1,0,'C',0);
		}

		// Collect Dob and Email
		if($this->GetArcInfo) {
			$this->SetXY($TopX, $this->GetY()+10);
			$this->SetFont($this->FontFix,'BI',6);

			$this->Cell($CellW*0.75, 3, $Data['EnCode'], 0, 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW*1.5, 3, get_text('DOB', 'Tournament'), 0, 0, 'R', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW*1.5, 3, ($Data['DoB'] ?? ''), 'B', 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->Cell($CellW, 3, get_text('Email', 'Tournament'), 0, 0, 'R', 0, '', 1, false, 'T', 'B');
			$this->Cell($Width-4.75*$CellW, 3, ($Data['Email'] ?? ''), 'B', 0, 'L', 0, '', 1, false, 'T', 'B');
			$this->ln($CellH/2);
		}

		//FIRME
		if(!$this->FillWithArrows) {
			$SignY=$TopY+$Height-($BottomImage+3);
			//$this->Line($TopX+4, $SignY, $TopX+($Width/2)-3 , $SignY);
			//$this->Line($TopX+($Width/2)+3, $SignY, $TopX+$Width-4 , $SignY);
			$this->SetFont($this->FontFix,'BI',6);
			$this->SetXY($TopX+4, $SignY);
			$this->Cell(($Width/2)-7,3,(get_text('Archer')),'T',0,'C',0);
			$this->SetX($this->GetX()+6);
			$this->Cell(($Width/2)-7,3,empty($Data['SecondScorer']) ? get_text('Scorer') : get_text('ScorerNum', 'Api', 1),'T',0,'C',0);
			if(!empty($Data['SecondScorer'])) {
				$this->SetXY($TopX+3+($Width/2), $SignY+12);
				$this->Cell(($Width/2)-7,3,get_text('ScorerNum', 'Api', 2),'T',0,'C',0);
			}
		}
		//$this->Rect($TopX, $TopY, $Width, $Height);
	}

//DRAW SCORE - ArrowCollector
	function DrawCollector($TopX, $TopY, $Width, $Height, $End, $NumArrow, $Archers, $Target='', $Distance='') {
		global $CFG;
		//PARAMETRI CALCOLATI
		$TopOffset=12;
		$TgtWidth = 12;
		$CellW = ($Width / (2*$NumArrow));
		$CellH = ($Height-$TopOffset)/(count($Archers)+1);
		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		if($this->PrintLogo && $this->PrintDrawing) {
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
				$im=getimagesize($IM);
				$imW=0; $imH=$TopOffset-2;
				if($im[0]/$im[1] > 1.5) { $imH=0; $imW=($TopOffset-2)*1.5; }
				$this->Image($IM, $TopX, $TopY, $imW, $imH);
				$TmpLeft = 1 + ($imW ? $imW : ($TopOffset-2)*$im[0]/$im[1]);
			}
		}

		//TESTATA GARA
//		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(false);
	    	$this->SetFont($this->FontStd,'B',8);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->Cell($Width-$TmpLeft-$TgtWidth, 5, mb_substr($this->Name, 0, 40, 'UTF-8'), 0, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $TopY+5);
			$this->Cell($Width-$TmpLeft-$TgtWidth, 5, mb_substr($this->Where, 0, 40, 'UTF-8'), 0, 0, 'L', 0);
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
//		}

		//PAGLIONE
		$this->SetXY($TopX+$Width-$TgtWidth, $TopY);
		$this->SetFont($this->FontStd,'B',14);
		$this->SetColors(true);
		$this->Cell($TgtWidth, 6, $Target, 0, 0, 'R', 1);
		$this->SetXY($TopX+$Width-$TgtWidth, $TopY+6);
		$this->SetFont($this->FontStd,'',9);
		$this->SetColors(true);
		$this->Cell($TgtWidth, 4, 'End: '.$End, 0, 0, 'L', 1);

		//HEADER DELLO SCORE
		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xE8,0xE8,0xE8);
//		$this->SetColors(true,true);
		$this->Cell($Width/2, $CellH, $Distance, 1, 0, 'L', 1);
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd, 'B', 8);
//		$this->SetColors(false);
		foreach(range(1, $NumArrow) as $j) $this->Cell($CellW, $CellH, $j, 1, 0, 'C', 1);

		//DATI ATLETI
		foreach($Archers as $k => $Archer) {
			$this->SetXY($TopX, $TopY+12+$CellH+($k*$CellH));
			$this->Cell(4, $CellH, chr(65+$k), 1, 0, 'L', 0);
			$this->Cell($Width/2 - 4, $CellH, $Archer, 1, 0, 'L', 0);
			foreach(range(1, $NumArrow) as $j) $this->Cell($CellW, $CellH, '', 1, 0, 'C', 0);
		}
	}

//DRAW SCORE - FIELD VERSION
	function DrawScoreField($TopX, $TopY, $Width, $Height, $CurDist=0, $Data=array(), $SesTar4Session=0, $SesFirstTarget=1) {
		global $CFG;
		static $ArrowEnds=array();

        if($FillWithArrows=$this->FillWithArrows and $this->IsRedding) {
            if($CurDist>1) {
                foreach(range(1, $CurDist-1) as $k) {
                    if(strlen(trim(str_replace(' ', '', $Data['Arr'.$k])))!=$Data['NumArrows'.$k]) {
                        $FillWithArrows=false;
                    }
                }
            }
        }

		$prnAppInfo=($SesFirstTarget!=1);

		// $ArrowEnds will contain the ends per arrows of each event and distance
        //$CurDist=(empty($CurDist) ? 1 : $CurDist);
        $ScoringEnds=$Data['ScoringEnds'.$CurDist]?:$Data['NumEnds'.$CurDist];
        $FlipEnd=$Data['NumEnds'.$CurDist];
        if($this->IsRedding) {
            $NumEnd=$ScoringEnds;
        } else {
            $NumEnd=$ScoringEnds/2;
        }
		if($SesTar4Session==0 || $SesTar4Session!=$Data['NumEnds'.$CurDist]) {
			$SesTar4Session = $Data['ScoringEnds'.$CurDist]?:$Data['NumEnds'.$CurDist];
		}

		$FirstDist=($CurDist==1);

		$NumArrow=$Data['NumArrows'.$CurDist];
		if($NumArrow==6) {
			$NumArrow=3;
			$NumEnd*=2;
		}
		$isNfaa = false;
		if($_SESSION['TourLocRule']=='NFAA' AND $ScoringEnds==51) {
			$isNfaa = true;
			$NumEnd=17;
		}

		$prnGolds = (empty($Data["Golds"]) ? $this->prnGolds : $Data["Golds"] );
		$prnXNine = (empty($Data["XNine"]) ? $this->prnXNine : $Data["XNine"]);


		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;
		$TargetNo=(!empty($Data["AtTarget"]) ? intval($Data["AtTarget"]) : 1)+$Data["ScoringOffset{$CurDist}"];
		$TargetNoApp=(!empty($Data["AtTarget"]) ? intval($Data["AtTarget"]) : 1)+$Data["ScoringOffset{$CurDist}"];

		if($TargetNo-($SesFirstTarget-1)>$FlipEnd) {
			$TargetNo = (($TargetNo-1) % $FlipEnd) + $SesFirstTarget;
		}
		if($TargetNoApp>$FlipEnd) {
			$TargetNoApp = (($TargetNoApp-1) % $FlipEnd) + 1;
		}
		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
        $imgH=0;
        $HeadWidth=empty($Data['PersonalScore']) ? $Width : $this->getPageWidth()-$this->getSideMargin()*2;
        if($this->PrintLogo) {
            if(empty($Data['PersonalScore']) or $CurDist==1) {
                if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
                    $im=getimagesize($IM);
                    $this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
                    $TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
                }
                if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
                    $im=getimagesize($IM);
                    $TmpRight = ($im[0] * 15 / $im[1]);
                    $this->Image($IM, ($TopX+$HeadWidth-$TmpRight), $TopY, 0, 15);
                    $TmpRight++;
                }
                //IMMAGINE DEGLI SPONSOR
                if($this->BottomImage and file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
                    $BottomImage=7.5;
                    $im=getimagesize($IM);
                    $imgW = $HeadWidth;
                    $imgH = $imgW * $im[1] /$im[0] ;
                    if($imgH > $BottomImage) {
                        $imgH = $BottomImage;
                        $imgW = $imgH * $im[0] /$im[1] ;
                    }
                    $this->Image($IM, ($TopX+($HeadWidth-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
                }
            } elseif($this->BottomImage and file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
                $BottomImage=7.5;
                $imgH = $BottomImage;
            }
		}

		$CellW = ((($Width-5)/2) / ($NumArrow+5));
        if(!empty($Data['PersonalScore']) or !empty($Data['MonoDistance'])) {
            $CellW = ($Width / ($NumArrow+5));
        }
		if($isNfaa) {
			$CellW = ((($Width-10)/3) / ($NumArrow+5)) ;
		}
		$CellH = ($Height-41-$BottomImage)/(ceil($NumEnd)+3+($this->IsRedding?2:0));
		$FontSize=min(10, $CellH/(1.3*0.352778));

		// CODICE A BARRE
		$BCode=0;
        if($this->PrintBarcode and !empty($Data['EnCode'])) {
            if($this->IsRedding) {
                if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
                $txtOrg=$Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'];
                if(!empty($Data['ElCode'])) {
                    $txtOrg=$Data['EnCode'].'-'.$Data['ElPhase'].'-'.$Data['ElCode'];
                }
                $txt=$txtOrg;
                if(!empty($CurDist)) {
                    $txt.='-'.$CurDist;
                }
                $this->SetXY($TopX, $TopY+$Height-$imgH-2.25*$CellH);
                $this->SetFont('barcode','',20);
                $this->Cell((empty($Data['PersonalScore']) and empty($Data['MonoDistance'])) ? ($Width-5)/2 : $Width, $CellH, mb_convert_encoding('*' . $txt, "UTF-8","cp1252") . "*",0,0,'C',0);
                $this->SetFont($this->FontStd,'',7);
                $this->SetXY($TopX, $TopY+$Height-$imgH-1*$CellH);
                $this->Cell((empty($Data['PersonalScore']) and empty($Data['MonoDistance'])) ? ($Width-5)/2 : $Width, $CellH, mb_convert_encoding($txt, "UTF-8","cp1252"),0,0,'C',0);

                if($CurDist==1 and empty($Data['PersonalScore']) and empty($Data['MonoDistance'])) {
                    $txt=$txtOrg.'-2';
                    $this->SetXY($TopX+5+($Width-5)/2, $TopY+$Height-$imgH-2.25*$CellH);
                    $this->SetFont('barcode','',20);
                    $this->Cell(($Width-5)/2, $CellH, mb_convert_encoding('*' . $txt, "UTF-8","cp1252") . "*",0,0,'C',0);
                    $this->SetFont($this->FontStd,'',7);
                    $this->SetXY($TopX+5+($Width-5)/2, $TopY+$Height-$imgH-1*$CellH);
                    $this->Cell(($Width-5)/2, $CellH, mb_convert_encoding($txt, "UTF-8","cp1252"),0,0,'C',0);

                }
            } else {
                if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
                $txt=$Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'];
                if(!empty($Data['ElCode'])) {
                    $txt=$Data['EnCode'].'-'.$Data['ElPhase'].'-'.$Data['ElCode'];
                }
                if(!empty($CurDist)) {
                    $txt.='-'.$CurDist;
                }
                $BCode=60;
                $this->SetXY(10+$Width-$TmpRight-$BCode, $TopY);
                $this->SetFont('barcode','',28);
                $this->Cell($BCode-5, $CellH, mb_convert_encoding('*' . $txt, "UTF-8","cp1252") . "*",0,0,'C',0);
                $this->SetFont($this->FontStd,'',7);
                $this->SetXY(10+$Width-$TmpRight-$BCode, $TopY+9);
                $this->Cell($BCode-5, $CellH, mb_convert_encoding($txt, "UTF-8","cp1252"),0,0,'C',0);
            }
        }

        //TESTATA GARA
        if(empty($Data['PersonalScore']) or $CurDist==1) {
            if($this->PrintHeader)
            {
                $this->SetColors(true);
                $this->SetFont($this->FontStd,'B',9);
                $this->SetXY($TopX+$TmpLeft,$TopY);
                $this->MultiCell($HeadWidth-$TmpLeft-$TmpRight-$BCode, 4, ($this->Name ?? ''), 0, 'L', 0);
                $this->SetFont($this->FontStd,'',7);
                $this->SetXY($TopX+$TmpLeft, $this->GetY());
                if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$HeadWidth-$TmpLeft-$TmpRight)
                {
                    $this->MultiCell($HeadWidth-$TmpLeft-$TmpRight-$BCode, 4, $this->Where, 0, 'L', 0);
                    $this->SetXY($TopX+$TmpLeft, $this->GetY());
                    $this->MultiCell($HeadWidth-$TmpLeft-$TmpRight-$BCode, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
                }
                else
                    $this->MultiCell($HeadWidth-$TmpLeft-$TmpRight-$BCode, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
            }

            //DATI ATLETA
            $FlagOffset=0;
            $this->SetXY($TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
            if($this->PrintFlags and !empty($Data['CoCode'])) {
                $FlagOffset=0.2*$CellW;
                if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$Data['CoCode'].'.jpg')) {
                    $H=$TopOffset*3/8;
                    $W=$H*3/2;
                    $OrgY=$this->gety();
                    $this->Image($file, $TopX, $this->gety(), $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
                    $FlagOffset=$W+1;
                }
            }

            $TargetTopBoxWidth=($prnAppInfo ? 3.4 : 1.9) * $CellW;
            $TargetBottomBoxWidth=1.4*$CellW;
            if($this->IsRedding) {
                $TargetTopBoxWidth=1.7 * $CellW;
                $TargetBottomBoxWidth=1.7 * $CellW;
            }

            $OldPad=$this->getCellPaddings();
            $this->setCellPaddings(0, $OldPad['T'], 0, $OldPad['B']);
            $this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*7/12));
            $this->SetFont($this->FontStd,'',8);
            $this->SetColors(false);
            $StrWidth=$this->GetStringWidth(get_text('Archer') . ": ");
            $this->Cell($StrWidth,$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
            $this->SetFont($this->FontStd,'B',$FontSize);
            $this->SetColors(true);
            $this->Cell($HeadWidth-$TargetTopBoxWidth-$StrWidth-$FlagOffset-1,$TopOffset/6, (array_key_exists("Ath",$Data) ? ($Data["Ath"] ?? '') : ' '),'B',0,'L',0);

            $this->SetXY($FlagOffset+$TopX, $TopY+($TopOffset*19/24));
            $this->SetFont($this->FontStd,'',8);
            $this->SetColors(false);
            $StrWidth=$this->GetStringWidth(get_text('Country') . ": ");
            $this->Cell($StrWidth,$TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
            $this->SetFont($this->FontStd,'B',$FontSize);
            $this->SetColors(true);
            $CellTmpWidth=$HeadWidth-($TargetTopBoxWidth)-$StrWidth-$FlagOffset-1;
            if(array_key_exists("Noc",$Data)) {
                $str=$Data['CoCode'].' -';
                $strW=$this->GetStringWidth($str);
                $this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
                $this->Cell($CellTmpWidth-$strW, $TopOffset/6, ($Data['CoName'] ?? ''),'B',0,'L',0);
            } else {
                $this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
            }
            $this->setCellPaddings($OldPad['L'], $OldPad['T'], $OldPad['R'], $OldPad['B']);

            //APP INFO
            if($prnAppInfo) {
                $this->SetXY($TopX+$HeadWidth-(3.4*$CellW), $TopY+($TopOffset*13/24));
                $this->SetFont($this->FontStd,'I',16);
                $this->SetColors(true);
                $HeaderTarget = ' ';
                if(array_key_exists("tNo",$Data)) {
                    $HeaderTarget = trim($Data["tNo"],'0');
                    if(!empty($Data["AtTarget"]) and $TargetNoApp!=intval($Data["AtTarget"])) {
                        $HeaderTarget = CheckBisTargets($Data["tNo"], $Data['NumEnds'.$CurDist]);
                    }
                }
                $this->Cell((1.9*$CellW),$TopOffset*7/24, $HeaderTarget,'TLR',0,'R',1);
                $this->SetXY($TopX+$HeadWidth-(3.4*$CellW), $TopY+($TopOffset*10/12));
                $this->SetFont($this->FontStd,'I',8);
                $this->SetColors(true);
                $this->Cell((1.9*$CellW),($TopOffset*2/12), 'ISK App','BLR',0,'C',1);

            }

            //PAGLIONE
            $this->SetXY($TopX+$HeadWidth-($TargetBottomBoxWidth), $TopY+($TopOffset*13/24));
            $this->SetFont($this->FontStd,'B',20);
            $this->SetColors(true);
            $HeaderTarget = ' ';
            if(array_key_exists("tNo",$Data)) {
                $HeaderTarget = trim($Data["tNo"],'0');
                if(!empty($Data["AtTarget"]) and $TargetNo!=intval($Data["AtTarget"])) {
                    $HeaderTarget = CheckBisTargets($Data["tNo"], $Data['NumEnds'.$CurDist]);
                }
            }

            $this->Cell(($TargetBottomBoxWidth),$TopOffset*7/24, $HeaderTarget,0,0,'R',1);
            $this->SetXY($TopX+$HeadWidth-($TargetBottomBoxWidth), $TopY+($TopOffset*10/12));
            $this->SetFont($this->FontStd,'B',$FontSize);
            $this->SetColors(true);
            $this->Cell(($TargetBottomBoxWidth),$TopOffset*2/12,(array_key_exists("Cat",$Data) ? $Data["Cat"] : ' '),'T',0,'C',1);
        }

		// TODO: investigate this
		$prnAppInfo=false;

		// recalculate the cell widths
		$TotalCells=$NumArrow + 3.3 + ($prnAppInfo?0.8:0) + ($NumArrow>1 ? 1.5 : 0) + ($prnXNine ? 1 : 0) + ($this->PrintTotalCols?1.5:0);
		$NewCellW = ((($Width-5-($isNfaa?5:0))/(2+($isNfaa?1:0))) / ($TotalCells));
        if(!empty($Data['PersonalScore']) or !empty($Data['MonoDistance'])) {
    		$NewCellW = ($Width / ($TotalCells));
        }

		$ArCellW=$NewCellW;
		$EndNumCellW=0.8*$NewCellW;
		$TotalCellW=$NewCellW*1.5;
		$XNineW=$NewCellW;

		//####SCORE 1 ####////
		//NFAA
		$TargetNo=$TargetNoApp;
		//HEADER DELLO SCORE 1
		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',($isNfaa ? 8 : $FontSize));
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell($EndNumCellW+($prnAppInfo?$EndNumCellW:0),$CellH, ($Data["D".$CurDist] ?? ' '),0,0,'C',(array_key_exists("D".$CurDist,$Data) ? 1 : 0));
		$this->SetFillColor(0xE8,0xE8,0xE8);
	   	$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : $FontSize));
		$this->SetColors(false);
		for($j=0; $j<$NumArrow; $j++) {
			$this->Cell($ArCellW,$CellH, ($j+1), 1, 0, 'C', 1);
		}
	   	$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : $FontSize));
		if($NumArrow>1) {
			$this->Cell($TotalCellW,$CellH, (get_text('TotalProg','Tournament')),1,0,'C',1);
		}
		$this->Cell($TotalCellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		if($this->PrintTotalCols) {
			$this->SetFillColor(0xFF,0xE8,0xE8);
			$this->Cell($TotalCellW,$CellH, (get_text('Total')),1,0,'C',1);
			$this->SetFillColor(0xE8,0xE8,0xE8);
		}
		$this->Cell($XNineW,$CellH, ($prnGolds),1,0,'C',1);
		if($prnXNine) {
			$this->Cell($XNineW, $CellH, ($prnXNine), 1, 0, 'C', 1);
		}
		$this->ln();
		$ScoreTotal = 0;
		$ScoreGold = 0;
		$ScoreXnine = 0;
        if($this->IsRedding and $CurDist==3) {
            $ScoreTotal=$Data['QuD1']+$Data['QuD2'];
        }
		//RIGHE DELLO SCORE 1
		for($i=1; $i<=ceil($NumEnd); $i++) {
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			if($prnAppInfo) {
				$this->SetFont($this->FontStd,'I',7);
				$this->Cell($EndNumCellW, $CellH, ($this->PrintLineNo ? '# '.$TargetNoApp : ''), 1, 0, 'C', 0);
			}
			$this->SetFont($this->FontStd,'B',($isNfaa ? 8 : $FontSize));
			$this->Cell($EndNumCellW,$CellH, ($this->PrintLineNo ? $TargetNo : ''),1,0,'C',1);
			$this->SetFont($this->FontStd,'',($isNfaa ? 8 : $FontSize));
			for($j=0; $j<$NumArrow; $j++) {
                $ArValue='';
                if($this->FillWithArrows) {
                    $ArValue=DecodeFromLetter(substr($Data["Arr".$CurDist], (($TargetNo-1)%($FlipEnd))*$NumArrow+$j, 1));
                    if(!trim($ArValue)) {
                        $FillWithArrows=false;
                    }
                }
				$this->Cell($ArCellW,$CellH, $ArValue, 1, 0, 'C', 0);
            }
			list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data["Arr".$CurDist],(($TargetNo-1)%($FlipEnd))*$NumArrow,$NumArrow), $Data['GoldsChars'], $Data['XNineChars']);
			$ScoreTotal += $ScoreEndTotal;
			$ScoreGold += $ScoreEndGold;
			$ScoreXnine += $ScoreEndXnine;
			if(!strlen(trim(substr($Data["Arr".$CurDist],(($TargetNo-1)%($FlipEnd))*$NumArrow,$NumArrow)))) {
				$ScoreEndTotal='';
				$ScoreEndGold='';
				$ScoreEndXnine='';
			}
			if($NumArrow>1) {
				$this->Cell($TotalCellW,$CellH,($FillWithArrows ? $ScoreEndTotal : ''),1,0,'C',0);
			}
			$this->Cell($TotalCellW,$CellH,(($FillWithArrows && $Data["Arr".$CurDist]) ? $ScoreTotal : ''),1,0,'C',0);
			if($this->PrintTotalCols) {
				$this->SetFillColor(0xFF,0xE8,0xE8);
				$this->Cell($TotalCellW,$CellH,($Data['Arr'.$CurDist] == "" ? '' : $ScoreTotal + $Data['Tot'.$CurDist]),1,0,'C',1);
				if(!empty($FirstDist) && $Data['Arr'.$CurDist] == "") {
					$this->Line($this->GetX(),$this->GetY(),$this->GetX()-$TotalCellW,$this->GetY()+$CellH);
					$this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-$TotalCellW,$this->GetY());
				}
				$this->SetFillColor(0xE8,0xE8,0xE8);
			}
			$this->Cell($XNineW,$CellH,($FillWithArrows ? $ScoreEndGold : ''),1,0,'C',0);
			if($prnXNine) {
				$this->Cell($XNineW, $CellH, ($FillWithArrows ? $ScoreEndXnine : ''), 1, 0, 'C', 0);
			}
			$this->ln();
			if(++$TargetNo-($SesFirstTarget-1)>$FlipEnd) {
				$TargetNo = $FlipEnd;
			}
			if(++$TargetNoApp>$FlipEnd) {
				$TargetNoApp= 1;
			}
			//NFAA
			$TargetNo=$TargetNoApp;
		}

        if($this->IsRedding) {
            // print score total
            $this->dy(1);
            $OldLine=$this->GetLineWidth();
            $this->SetLineWidth(0.5);
            $this->SetXY($TopX, $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
            // if($isNfaa){
            // 	$this->SetXY($TopX+($Width-5)*2/3+5+(empty($prnXNine) ? $XNineW:0), $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
            // }
            $this->SetFont($this->FontStd,'B',$FontSize);
            $this->Cell($EndNumCellW+$NumArrow*$ArCellW,$CellH, (get_text('Total') . " "),0,0,'R',0);
            $Total='';
            if($FillWithArrows) {
                $Total=$ScoreTotal;
                if($CurDist==3) {
                    $Total=$Data['QuD1']+$Data['QuD2']+$Data['QuD3'];
                }
            }
            $this->Cell($TotalCellW, $CellH, $Total,1,0,'C',0);
//            if($this->PrintTotalCols) {
//                $this->SetFillColor(0xFF,0xE8,0xE8);
//                $this->Cell($TotalCellW,$CellH, ($Data['Arr'.$CurDist] == "" ? '' : $ScoreTotal + $Data['Tot'.$CurDist]),1,0,'C',1);
//                if(!empty($FirstDist) && $Data['Arr'.$CurDist] == "") {
//                    $this->SetLineWidth($OldLine);
//                    $this->Line($this->GetX(),$this->GetY(),$this->GetX()-$TotalCellW,$this->GetY()+$CellH);
//                    $this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-$TotalCellW,$this->GetY());
//                    $this->SetLineWidth(0.5);
//                }
//                $this->SetFillColor(0xE8,0xE8,0xE8);
//            }

            $this->Cell($XNineW,$CellH,($FillWithArrows ? $ScoreGold : ''),1,0,'C',0);

            if($prnXNine) {
                $this->Cell($XNineW, $CellH, ($FillWithArrows ? $ScoreXnine : ''), 1, 0, 'C', 0);
            }
            $this->SetLineWidth($OldLine);
            if(!empty($Data['PersonalScore']) or !empty($Data['MonoDistance'])) {
                $this->setXY($TopX, $TopY+$TopOffset+$CellH*($NumEnd+3+($this->IsRedding?1:0))+1);
                $this->SetFont($this->FontFix,'BI',6);
                $this->Cell(4, 3, '', 0, 0, 'C', 0);
                $this->Cell($Width/2-7, 3, (get_text('Archer')), 'B', 0, 'L', 0);
                $this->Cell(6, 3, '', 0, 0, 'C', 0);
                $this->Cell($Width/2-7,3,(get_text('Scorer')),'B',1,'L',0);
                $this->SetLineWidth($OldLine);

                return;
            }
        }

        if($this->IsRedding and !empty($Data['MonoDistance'])) {
            return;
        }

//#### SCORE 2 ####////
		//HEADER DELLO SCORE 2
		$ScoreOffset=$TopX+($Width-5)/2+5;
		if($isNfaa) {
			$ScoreOffset=$TopX+($Width-10)/3+5;
		}
        if($this->IsRedding and $CurDist==1) {
            $CurDist=2;
        }
        if(!$this->IsRedding or $CurDist!=3) {
            $this->SetXY($ScoreOffset, $TopY+$TopOffset);
            $this->SetFont($this->FontStd,'I',($isNfaa ? 8 : $FontSize));
            $this->SetFillColor(0xF8,0xF8,0xF8);
            $this->SetColors(true,true);
            $this->Cell($EndNumCellW+($prnAppInfo?$EndNumCellW:0),$CellH,(!empty($Data["D".$CurDist]) && ($FillWithArrows or $this->IsRedding) ? $Data["D".$CurDist] : (array_key_exists("Dist",$Data) ? $Data["Dist"] : ' ')),0,0,'C',(array_key_exists("Dist",$Data) ? 1 : 0));
            $this->SetFillColor(0xE8,0xE8,0xE8);
            $this->SetFont($this->FontStd,'B',($isNfaa ? 8 : $FontSize));
            $this->SetColors(false);
            for($j=0; $j<$NumArrow; $j++) {
                $this->Cell($ArCellW,$CellH, ($j+1), 1, 0, 'C', 1);
            }
            $this->SetFont($this->FontStd,'B',($isNfaa ? 8 : $FontSize));
            if($NumArrow>1) {
                $this->Cell($TotalCellW,$CellH, (get_text('TotalProg','Tournament')),1,0,'C',1);
            }
            $this->Cell($TotalCellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
            if($this->PrintTotalCols) {
                $this->SetFillColor(0xFF,0xE8,0xE8);
                $this->Cell($TotalCellW,$CellH, (get_text('Total')),1,0,'C',1);
                $this->SetFillColor(0xE8,0xE8,0xE8);
            }
            $this->Cell($XNineW,$CellH, ($prnGolds),1,0,'C',1);
            if($prnXNine) {
                $this->Cell($XNineW, $CellH, ($prnXNine), 1, 0, 'C', 1);
            }
            $this->ln();

            //RIGHE DELLO SCORE 2
            for($i=1; $i<=$NumEnd; $i++) {
                $this->SetXY($ScoreOffset, $TopY+$TopOffset+$CellH*$i);
                if($prnAppInfo) {
                    $this->SetFont($this->FontStd,'I',7);
                    $this->Cell($EndNumCellW, $CellH, ($this->PrintLineNo ? '# '.$TargetNoApp : ''), 1, 0, 'C', 0);
                }
                $this->SetFont($this->FontStd,'B',($isNfaa ? 8 : $FontSize));
                $this->Cell($EndNumCellW,$CellH,($this->PrintLineNo ? $TargetNo : ''),1,0,'C',1);
                $this->SetFont($this->FontStd,'',($isNfaa ? 8 : $FontSize));
                for($j=0; $j<$NumArrow; $j++) {
                    $ArValue='';
                    if($this->FillWithArrows) {
                        $ArValue=DecodeFromLetter(substr($Data["Arr".$CurDist], (($TargetNo-1)%($FlipEnd))*$NumArrow+$j, 1));
                        if(!trim($ArValue)) {
                            $FillWithArrows=false;
                        }
                    }
                    $this->Cell($ArCellW,$CellH, $ArValue, 1, 0, 'C', 0);
                }
                list($ScoreEndTotal,$ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data["Arr".$CurDist],(($TargetNo-1)%($FlipEnd))*$NumArrow,$NumArrow),$Data['GoldsChars'], $Data['XNineChars']);
                $ScoreTotal += $ScoreEndTotal;
                $ScoreGold += $ScoreEndGold;
                $ScoreXnine += $ScoreEndXnine;
                if(!strlen(trim(substr($Data["Arr".$CurDist],(($TargetNo-1)%($FlipEnd))*$NumArrow,$NumArrow)))) {
                    $ScoreEndTotal='';
                    $ScoreEndGold='';
                    $ScoreEndXnine='';
                }
                if($NumArrow>1) {
                    $this->Cell($TotalCellW, $CellH, ($FillWithArrows ? $ScoreEndTotal : ''), 1, 0, 'C', 0);
                }
                $this->Cell($TotalCellW,$CellH,(($FillWithArrows && $Data["Arr".$CurDist]) ? ($this->IsRedding ? $Data['Tot'.$CurDist] : $ScoreTotal) : ''),1,0,'C',0);
                if($this->PrintTotalCols) {
                    $this->SetFillColor(0xFF,0xE8,0xE8);
                    $this->Cell($TotalCellW,$CellH,($Data['Arr'.$CurDist] == "" ? '' : $ScoreTotal + $Data['Tot'.$CurDist]),1,0,'C',1);
                    if(!empty($FirstDist) && $Data['Arr'.$CurDist] == "") {
                        $this->Line($this->GetX(),$this->GetY(),$this->GetX()-$TotalCellW,$this->GetY()+$CellH);
                        $this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-$TotalCellW,$this->GetY());
                    }
                    $this->SetFillColor(0xE8,0xE8,0xE8);
                }
                $this->Cell($XNineW,$CellH,($FillWithArrows ? $ScoreEndGold : ''),1,0,'C',0);
                if($prnXNine) {
                    $this->Cell($XNineW, $CellH, ($FillWithArrows ? $ScoreEndXnine : ''), 1, 0, 'C', 0);
                }
                $this->ln();
                if(++$TargetNo-($SesFirstTarget-1)>$FlipEnd) {
                    $TargetNo = $FlipEnd;
                }
                if(++$TargetNoApp>$FlipEnd) {
                    $TargetNoApp= 1;
                }
                //NFAA
                $TargetNo=$TargetNoApp;
            }
        }
//#### SCORE 3 - SOLO NFAA ####////
		//HEADER DELLO SCORE 3
		if($isNfaa) {
			$ScoreOffset=$TopX+($Width-10)*2/3+10;
			$this->SetXY($ScoreOffset, $TopY+$TopOffset);

			$this->SetFont($this->FontStd, 'I', 8);
			$this->SetFillColor(0xF8, 0xF8, 0xF8);
			$this->SetColors(true, true);
			$this->Cell($EndNumCellW+($prnAppInfo?$EndNumCellW:0), $CellH, (!empty($Data["D" . $CurDist]) && $FillWithArrows ? $Data["D" . $CurDist] : (array_key_exists("Dist", $Data) ? $Data["Dist"] : ' ')), 0, 0, 'C', (array_key_exists("Dist", $Data) ? 1 : 0));
			$this->SetFillColor(0xE8, 0xE8, 0xE8);
			$this->SetFont($this->FontStd, 'B', 8);
			$this->SetColors(false);
			for ($j = 0; $j < $NumArrow; $j++) {
				$this->Cell($ArCellW, $CellH, ($j + 1), 1, 0, 'C', 1);
			}
			$this->SetFont($this->FontStd, 'B', 8);
			if ($NumArrow > 1) {
				$this->Cell($TotalCellW, $CellH, (get_text('TotalProg', 'Tournament')), 1, 0, 'C', 1);
			}
			$this->Cell($TotalCellW, $CellH, (get_text('TotalShort', 'Tournament')), 1, 0, 'C', 1);
			if($this->PrintTotalCols) {
				$this->SetFillColor(0xFF,0xE8,0xE8);
				$this->Cell($TotalCellW,$CellH, (get_text('Total')),1,0,'C',1);
				$this->SetFillColor(0xE8,0xE8,0xE8);
			}
			$this->Cell($XNineW, $CellH, ($prnGolds), 1, 0, 'C', 1);
			if($prnXNine) {
				$this->Cell($XNineW, $CellH, ($prnXNine), 1, 0, 'C', 1);
			}
			$this->ln();

			//RIGHE DELLO SCORE 3
			for ($i = 1; $i <= $NumEnd; $i++) {
				$this->SetXY($ScoreOffset, $TopY + $TopOffset + $CellH * $i);
				if($prnAppInfo) {
					$this->SetFont($this->FontStd,'I',7);
					$this->Cell($EndNumCellW, $CellH, ($this->PrintLineNo ? '# '.$TargetNoApp : ''), 1, 0, 'C', 0);
				}
				$this->SetFont($this->FontStd, 'B', 8);
				$this->Cell($EndNumCellW, $CellH, ($this->PrintLineNo ? $TargetNo : ''), 1, 0, 'C', 1);
				$this->SetFont($this->FontStd, '', 8);
				for ($j = 0; $j < $NumArrow; $j++) {
					$this->Cell($ArCellW, $CellH, ($FillWithArrows ? DecodeFromLetter(substr($Data["Arr" . $CurDist], (($TargetNo - 1) % (2 * $NumEnd)) * $NumArrow + $j, 1)) : ''), 1, 0, 'C', 0);
				}
				list($ScoreEndTotal, $ScoreEndGold, $ScoreEndXnine) = ValutaArrowStringGX(substr($Data["Arr" . $CurDist], (($TargetNo - 1) % (2 * $NumEnd)) * $NumArrow, $NumArrow), $this->goldsChars, $this->xNineChars);
				$ScoreTotal += $ScoreEndTotal;
				$ScoreGold += $ScoreEndGold;
				$ScoreXnine += $ScoreEndXnine;
				if (!strlen(trim(substr($Data["Arr" . $CurDist], (($TargetNo - 1) % (2 * $NumEnd)) * $NumArrow, $NumArrow)))) {
					$ScoreEndTotal = '';
					$ScoreEndGold = '';
					$ScoreEndXnine = '';
				}
				if ($NumArrow > 1) {
					$this->Cell($TotalCellW, $CellH, (($FillWithArrows && $Data["Arr".$CurDist]) ? $ScoreEndTotal : ''), 1, 0, 'C', 0);
				}
				$this->Cell($TotalCellW, $CellH, ($FillWithArrows ? $ScoreTotal : ''), 1, 0, 'C', 0);
				if($this->PrintTotalCols) {
					$this->SetFillColor(0xFF,0xE8,0xE8);
					$this->Cell($TotalCellW,$CellH,($Data['Arr'.$CurDist] == "" ? '' : $ScoreTotal + $Data['Tot'.$CurDist]),1,0,'C',1);
					if(!empty($FirstDist) && $Data['Arr'.$CurDist] == "") {
						$this->Line($this->GetX(),$this->GetY(),$this->GetX()-$TotalCellW,$this->GetY()+$CellH);
						$this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-$TotalCellW,$this->GetY());
					}
					$this->SetFillColor(0xE8,0xE8,0xE8);
				}
				$this->Cell($XNineW, $CellH, ($FillWithArrows ? $ScoreEndGold : ''), 1, 0, 'C', 0);
				if($prnXNine) {
					$this->Cell($XNineW, $CellH, ($FillWithArrows ? $ScoreEndXnine : ''), 1, 0, 'C', 0);
				}
				$this->ln();

				if(++$TargetNo-($SesFirstTarget-1)>$FlipEnd) {
					$TargetNo = $FlipEnd;
				}
				if(++$TargetNoApp>$FlipEnd) {
					$TargetNoApp= 1;
				}
				//NFAA
				$TargetNo=$TargetNoApp;
			}
		}

		//TOTALE DELLO SCORE
        // $ErScoreTotal = false;
        // $ErScoreGold = false;
        // $ErScoreXNine = false;
        // if(isset($Data["QuD"]) AND isset($Data["QuGD"]) AND isset($Data["QuXD"])) {
        //     $ErScoreTotal = ($Data["QuD"] != $ScoreTotal);
        //     $ErScoreGold = ($Data["QuGD"] != $ScoreGold);
        //     $ErScoreXNine = ($Data["QuXD"] != $ScoreXnine);
        // }

        if(!$this->IsRedding or $CurDist!=3) {

            $ErScoreTotal = (empty($CurDist) or $this->IsRedding) ? '' : (($Data['Arr'.$CurDist]??'') and ($Data["QuD{$CurDist}"]??0)!=$ScoreTotal);
            $ErScoreGold  = (empty($CurDist) or $this->IsRedding) ? '' : (($Data["QuGD{$CurDist}"]??0)!=$ScoreGold);
            $ErScoreXNine = (empty($CurDist) or $this->IsRedding) ? '' : (($Data["QuXD{$CurDist}"]??0)!=$ScoreXnine);

            //TOTALE GENERALE
            $OldLine=$this->GetLineWidth();
            $this->SetLineWidth(0.5);
            $this->SetXY($TopX + $Width - $TotalCellW*3 - $XNineW*($prnXNine ? 2 : 1) - ($this->PrintTotalCols ? $TotalCellW : 0), $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
            // if($isNfaa){
            // 	$this->SetXY($TopX+($Width-5)*2/3+5+(empty($prnXNine) ? $XNineW:0), $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
            // }
            $this->SetFont($this->FontStd,'B',$FontSize);
            $this->Cell($TotalCellW*2,$CellH, (get_text('Total') . " "),0,0,'R',0);
            $this->Cell($TotalCellW,$CellH,($FillWithArrows ? (($this->IsRedding && !$Data['Tot'.$CurDist]) ? '' : $ScoreTotal) : ''),1,0,'C',0);
            if($FillWithArrows && $ErScoreTotal and !$this->IsRedding) {
                $this->Line($x1 = $this->getx(), $y1=$this->gety()+$CellH, $x1-($TotalCellW), $y1-$CellH);
            }
            if($this->PrintTotalCols) {
                $this->SetFillColor(0xFF,0xE8,0xE8);
                $this->Cell($TotalCellW,$CellH, ($Data['Arr'.$CurDist] == "" ? '' : $ScoreTotal + $Data['Tot'.$CurDist]),1,0,'C',1);
                if(!empty($FirstDist) && $Data['Arr'.$CurDist] == "" and !$this->IsRedding) {
                    $this->SetLineWidth($OldLine);
                    $this->Line($this->GetX(),$this->GetY(),$this->GetX()-$TotalCellW,$this->GetY()+$CellH);
                    $this->Line($this->GetX(),$this->GetY()+$CellH,$this->GetX()-$TotalCellW,$this->GetY());
                    $this->SetLineWidth(0.5);
                }
                $this->SetFillColor(0xE8,0xE8,0xE8);
            }

            $this->Cell($XNineW,$CellH,($FillWithArrows ? $ScoreGold : ''),1,0,'C',0);
            if($FillWithArrows && $ErScoreGold) {
                $this->Line($x1 = $this->getx(), $y1=$this->gety()+$CellH, $x1-($XNineW), $y1-$CellH);
            }

            if($prnXNine) {
                $this->Cell($XNineW, $CellH, ($FillWithArrows ? $ScoreXnine : ''), 1, 0, 'C', 0);
                if($FillWithArrows && $ErScoreXNine) {
                    $this->Line($x1 = $this->getx(), $y1=$this->gety(), $x1+($XNineW), $y1+$CellH);
                }
            }
            $this->ln();

            if($FillWithArrows and ($ErScoreTotal or $ErScoreGold or $ErScoreXNine)) {
                $this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*($NumEnd+2)+1);
                $this->Cell(($NumArrow+2.2)*$CellW,$CellH, (get_text('SignedTotal', 'Tournament') . " "),0,0,'R',0);

                $this->Cell($TotalCellW,$CellH, ($Data["QuD".$CurDist] ?? ''),1,0,'C',0);
                $this->Cell($XNineW,$CellH, ($Data["QuGD".$CurDist] ?? ''),1,0,'C',0);
                if($prnXNine) {
                    $this->Cell($XNineW, $CellH, ($Data["QuXD".$CurDist] ?? ''), 1, 0, 'C', 0);
                }
                $this->ln();
            } else {
                $this->ln($CellH);
            }
        }

		$this->SetLineWidth(0.2);

		//FIRME
        $this->setXY($TopX, $TopY+$TopOffset+$CellH*($NumEnd+3+($this->IsRedding?1:0))+1);
		$this->SetFont($this->FontFix,'BI',6);
		$this->Cell(4, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7, 3, (get_text('Archer')), 'B', 0, 'L', 0);
		$this->Cell(6, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7,3,(get_text('Scorer')),'B',1,'L',0);
		$this->SetLineWidth($OldLine);

		$this->SetColors(true);
	}

//DRAW SCORE - 3D VERSION
	function DrawScore3D($TopX, $TopY, $Width, $Height, $NumEndTotal, $Data=array(), $OnlyLeftScore=false, $Target='')
	{
		global $CFG;
		if(!$Target) {
			$Target=array(11, 10, 8, 5, 'M');
		}

        $NumEnd = $NumEndTotal/2;
		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;
		$TargetNo=(!empty($Data["AtTarget"]) ? intval($Data["AtTarget"]) : 1);
		if($TargetNo>2*$NumEnd) {
			$TargetNo = (($TargetNo-1) % (2*$NumEnd)) + 1;
		}

		$prnGolds = (empty($Data["Golds"]) ? $this->prnGolds : $Data["Golds"] );
		$prnXNine = (empty($Data["XNine"]) ? $this->prnXNine : $Data["XNine"]);

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo && $this->PrintDrawing) {
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
				$im=getimagesize($IM);
				$this->Image($IM, $TopX, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * 15 / $im[1]);
				$this->Image($IM, ($TopX+$Width-$TmpRight), $TopY, 0, 15);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			if($this->BottomImage and file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToBottom.jpg')) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] /$im[0] ;
				if($imgH > $BottomImage)
				{
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] /$im[1] ;
				}
				$this->Image($IM, ($TopX+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}
		$CellW = ((($Width-5)/2) / (count($Target)+3));
		$CellH = ($Height-41-$BottomImage)/($NumEnd+2);

		// CODICE A BARRE
		$BCode=0;
		if($this->PrintBarcode and !empty($Data['EnCode'])) {
			$BCode=60;
			$this->SetXY(10+$Width-$TmpRight-$BCode, $TopY);
			$this->SetFont('barcode','',28);
			if($Data['EnCode'][0]=='_') $Data['EnCode']='UU'.substr($Data['EnCode'], 1);
			$this->Cell($BCode-5, $CellH, mb_convert_encoding('*' . $Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'], "UTF-8","cp1252") . "*",0,0,'C',0);
			$this->SetFont($this->FontStd,'',7);
			$this->SetXY(10+$Width-$TmpRight-$BCode, $TopY+9);
			$this->Cell($BCode-5, $CellH, mb_convert_encoding($Data['EnCode'].'-'.$Data['Div'].'-'.$Data['Cls'], "UTF-8","cp1252"),0,0,'C',0);
		}

		//TESTATA GARA
		if($this->PrintHeader)
		{
			$this->SetColors(true);
	    	$this->SetFont($this->FontStd,'B',9);
			$this->SetXY($TopX+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Name, 0, 'L', 0);
    		$this->SetFont($this->FontStd,'',7);
			$this->SetXY($TopX+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight)
			{
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Where, 0, 'L', 0);
				$this->SetXY($TopX+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			else
				$this->MultiCell($Width-$TmpLeft-$TmpRight-$BCode, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
		}


		//DATI ATLETA
		$this->SetXY($TopX+0.2*$CellW, $TopY+($TopOffset*7/12));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$this->Cell($this->GetStringWidth((get_text('Archer') . ": ")),$TopOffset/6, (get_text('Archer') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$this->Cell($Width-(1.9*$CellW)-$this->GetStringWidth((get_text('Archer') . ": ")),$TopOffset/6, (array_key_exists("Ath",$Data) ? ($Data["Ath"] ?? '') : ' '),'B',0,'L',0);
		$this->SetXY($TopX+0.2*$CellW, $TopY+($TopOffset*19/24));
		$this->SetFont($this->FontStd,'',8);
		$this->SetColors(false);
		$this->Cell($this->GetStringWidth((get_text('Country') . ": ")),$TopOffset/6, (get_text('Country') . ": "),'B',0,'L',0);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetColors(true);
		$CellTmpWidth=$Width-(1.9*$CellW)-$this->GetStringWidth((get_text('Country') . ": "));
		if(array_key_exists("Noc",$Data)) {
			$str=$Data['CoCode'].' -';
			$strW=$this->GetStringWidth($str);
			$this->Cell($strW, $TopOffset/6, $str,'B',0,'L',0);
			$this->Cell($CellTmpWidth-$strW, $TopOffset/6, $Data['CoName'],'B',0,'L',0);
		} else {
			$this->Cell($CellTmpWidth,$TopOffset/6, ' ','B',0,'L',0);
		}
		//PAGLIONE
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*13/24));
		$this->SetFont($this->FontStd,'B',20);
		$this->SetColors(true);
		$HeaderTarget = ' ';
		if(array_key_exists("tNo",$Data)) {
			$HeaderTarget = trim($Data["tNo"],'0');
			if(!empty($Data["AtTarget"]) and $TargetNo!=intval($Data["AtTarget"])) {
				$HeaderTarget = CheckBisTargets($Data["tNo"], $NumEndTotal);
			}
		}

		$this->Cell((1.4*$CellW),$TopOffset*7/24,$HeaderTarget,0,0,'R',1);
		$this->SetXY($TopX+$Width-(1.4*$CellW), $TopY+($TopOffset*10/12));
		$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(true);
		$this->Cell((1.4*$CellW),$TopOffset*2/12,(array_key_exists("Cat",$Data) ? $Data["Cat"] : ' '),'T',0,'C',1);

		// recalculate the cell widths
		$TotalCells=$NumArrow + 3.3 + ($NumArrow>1 ? 1.5 : 0) + ($prnXNine ? 1 : 0) + ($this->PrintTotalCols?1.5:0);
		$NewCellW = ((($Width-5-($isNfaa?5:0))/(2+($isNfaa?1:0))) / ($TotalCells));

		$ArCellW=$NewCellW;
		$EndNumCellW=0.8*$NewCellW;
		$TotalCellW=$NewCellW*1.5;
		$XNineW=$NewCellW;

//####SCORE 1 ####////
		//HEADER DELLO SCORE 1
		$this->SetXY($TopX, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell($EndNumCellW,$CellH,(array_key_exists("Dist",$Data) ?
			$Data["Dist"] : ' '),0,0,'C',(array_key_exists("Dist",$Data) ?
			1 : 0));
		$this->SetFillColor(0xFF,0xFF,0xFF);
		if ($this->PrintDrawing)
			$this->SetFillColor(0xE8,0xE8,0xE8);

//	   	$this->SetFont($this->FontStd,'',6);
//		$this->Cell(0.9*$CellW,$CellH,($this->PrintDrawing ? get_text('Target') : ''),1,0,'C',1);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(false);
		$this->Cell(0.9*(count($Target))*$CellW,$CellH, get_text('Arrow'), 1, 0, 'C', 1);
	   	$this->SetFont($this->FontStd,'B',8);
		$this->Cell(1.4*$CellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($prnGolds),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($prnXNine),1,1,'C',1);
		//RIGHE DELLO SCORE 1
		for($i=1; $i<=ceil($NumEnd); $i++)
		{
		   	$this->SetFont($this->FontStd,'B',10);
			$this->SetXY($TopX, $TopY+$TopOffset+$CellH*$i);
			$this->Cell($EndNumCellW,$CellH,$TargetNo,1,0,'C',1);
		   	$this->SetFont($this->FontStd,'',10);
//			$this->Cell(0.9*$CellW,$CellH,'',1,0,'C',0);
			foreach($Target as $point) {
				$this->Cell(0.9*$CellW,$CellH, $point, 1, 0, 'C', 0);
			}
// 			$this->Cell(0.9*$CellW,$CellH, 'M', 1, 0, 'C', 0);
			$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			if(++$TargetNo>($OnlyLeftScore ? $NumEnd : 2*$NumEnd))
				$TargetNo=1;
		}
/*
		//TOTALE DELLO SCORE 1
		$this->SetXY($TopX, $TopY+$TopOffset+$CellH*($NumEnd+1));
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(6.2*$CellW,$CellH, (get_text('Total1','Tournament') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
*/
//#### SCORE 2 ####////
		//HEADER DELLO SCORE 2
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset);
	   	$this->SetFont($this->FontStd,'I',8);
		$this->SetFillColor(0xF8,0xF8,0xF8);
		$this->SetColors(true,true);
		$this->Cell($EndNumCellW,$CellH,(array_key_exists("Dist",$Data) ?
$Data["Dist"] : ' '),0,0,'C',(array_key_exists("Dist",$Data) ?
1 : 0));
		$this->SetFillColor(0xFF,0xFF,0xFF);
		if ($this->PrintDrawing)
			$this->SetFillColor(0xE8,0xE8,0xE8);
//	   	$this->SetFont($this->FontStd,'',6);
//		$this->Cell(0.9*$CellW,$CellH,($this->PrintDrawing ? get_text('Target') : ''),1,0,'C',1);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->SetColors(false);
		$this->Cell(0.9*(count($Target))*$CellW,$CellH, get_text('Arrow'), 1, 0, 'C', 1);
	   	$this->SetFont($this->FontStd,'B',8);
		$this->Cell(1.4*$CellW,$CellH, (get_text('TotalShort','Tournament')),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($prnGolds),1,0,'C',1);
		$this->Cell(0.7*$CellW,$CellH, ($prnXNine),1,1,'C',1);
		//RIGHE DELLO SCORE 2
		for($i=1; $i<=floor($NumEnd); $i++)
		{
		   	$this->SetFont($this->FontStd,'B',10);
			$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*$i);
			$this->Cell($EndNumCellW,$CellH,$TargetNo,1,0,'C',1);
		   	$this->SetFont($this->FontStd,'',10);
//			$this->Cell(0.9*$CellW,$CellH,'',1,0,'C',0);
			foreach($Target as $point) {
				$this->Cell(0.9*$CellW,$CellH, $point, 1, 0, 'C', 0);
			}
// 			$this->Cell(0.9*$CellW,$CellH, 'M', 1, 0, 'C', 0);
			$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
			if(++$TargetNo>($OnlyLeftScore ? $NumEnd : 2*$NumEnd))
				$TargetNo=1;
		}
/*
		//TOTALE DELLO SCORE 2
		$this->SetXY($TopX+($Width-5)/2+5, $TopY+$TopOffset+$CellH*($NumEnd+1));
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(6.2*$CellW,$CellH, (get_text('Total2','Tournament') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
*/
		//TOTALE GENERALE
		$OldLine=$this->GetLineWidth();
		$this->SetLineWidth(0.5);
		$this->SetXY($TopX+$Width-$CellW*5, $TopY+$TopOffset+$CellH*($NumEnd+1)+1);
	   	$this->SetFont($this->FontStd,'B',10);
		$this->Cell(((0.9*(count($Target)-1)) + 1.7)*$CellW,$CellH, (get_text('Total') . " "),0,0,'R',0);
		$this->Cell(1.4*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,0,'C',0);
		$this->Cell(0.7*$CellW,$CellH,'',1,1,'C',0);
		$this->SetLineWidth(0.2);
		//Se solo score di SINISTRA
		if($OnlyLeftScore)
		{
			$this->SetLineWidth(0.5);
			$this->Line($TopX+($Width-5)/2+5, $TopY+$TopOffset, $TopX+$Width, $TopY+$TopOffset+$CellH*($NumEnd+2));
			$this->SetLineWidth(0.2);
		}
		//FIRME
		$this->SetFont($this->FontFix,'BI',6);
		$this->Cell(4, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7, 3, (get_text('Archer')), 'B', 0, 'L', 0);
		$this->Cell(6, 3, '', 0, 0, 'C', 0);
		$this->Cell($Width/2-7,3,(get_text('Scorer')),'B',1,'L',0);
		$this->Cell(4,3,'',0,1,'C',0);
		$this->Cell(4,3,'',0,0,'C',0);
		$this->Cell($Width-8,3,(get_text('JudgeNotes')),'B',0,'L',0);

		$this->SetLineWidth($OldLine);
		//$this->Rect($TopX, $TopY, $Width, $Height);

		$this->SetColors(true);
	}

	function DrawScoreRunArcherySpotter($Data=array()) {
		$TopY=$Data['top'];
		$Width=$this->getPageWidth()-20;
		$Height=$Data['height'];
		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;

		// draw a separation line...
		if($TopY-10>0) {
			$this->Line(0,$TopY-10, $this->getPageWidth(), $TopY-10);
		}

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$this->ToPaths['ToLeft']) ) {
				$im=getimagesize($IM);
				$this->Image($IM, 10, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$this->ToPaths['ToRight']) ) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * ($TopOffset/2) / $im[1]);
				$this->Image($IM, (10+$Width-$TmpRight), $TopY, 0, $TopOffset/2);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			// Sponsors disabled if QRCodes are to be printed!!!
			if($this->BottomImage and file_exists($IM=$this->ToPaths['ToBottom'])) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] / $im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] / $im[1] ;
				}
				$this->Image($IM, (10+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}


		//TESTATA GARA
		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(true);
			$this->SetFont($this->FontStd,'B',9);
			$this->SetXY(10+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Name, 0, 'L', 0);
			$this->SetFont($this->FontStd,'',7);
			$this->SetXY(10+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight) {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where, 0, 'L', 0);
				$this->SetXY(10+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			} else {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
		}

		$this->dy(2);
		$Y=$this->GetY();
		$this->SetFont('','b', 14);
		$this->cell(0,0, get_text('ShootingReport', 'RunArchery'), '', 1, 'C');
		$this->dy(2);

		$this->SetFont('','', 8);
		$this->cell(12,0, get_text('RaceNum', 'RunArchery'), '',0,'R');
		$this->cell(10,0, '', 'B');
        $this->cell(22,0, get_text('SpotterName', 'RunArchery'), '', 0,'R');
        $this->cell(60,0, '', 'B', 0);
		$this->cell(20,0, get_text('RaceName', 'RunArchery'), '', 0, 'R');
		$this->cell(40,0, '', 'B');
		$this->cell(12,0, get_text('Target'), '', 0,'R');
		$this->cell(0,0, '', 'B', 1);
		$this->dy(2);

		$this->SetFont('','', 9);

		$CellH=($Height+$TopY-14-$this->getY()-$BottomImage)/$Data['rows'];
		$Circle=min(7,$CellH)/2;
		$CircleYOffset=$CellH/2;
		$CellHits=$Circle*$Data['targets']*2+2;

		// actual score
		$this->cell(20,13, get_text('Bib', 'RunArchery'), '1',0,'C');
		$this->cell(16,5, get_text('Shooting', 'RunArchery'), 'LTR',0,'C');
		$this->cell($CellHits+8,8, get_text('TargetsHit','RunArchery'), 'LTR',0,'C');
		$this->writeHTMLCell(15,13, null, null, get_text('ArrowsShot', 'RunArchery'), 1,0,false, true, 'C');
		$this->writeHTMLCell(15,13, null, null, get_text('PenaltyLoops', 'RunArchery'), 1,0,false, true,'C');
		$this->writeHTMLCell(0,13, null, null, get_text('Notes', 'Tournament').'<div style="text-align: left"><b>A</b>: '.get_text('Accepted', 'RunArchery').'&nbsp;&nbsp;&nbsp;<b>R</b>: '.get_text('Rejected', 'RunArchery').'<br/><b>W</b>: '.get_text('Withdrawn', 'RunArchery').'</div>', 1,0,false, true, 'C');
		$this->ImageSVG(dirname(__DIR__).'/Images/standing.svg', 31.5, $this->GetY()+4, 5);
		$this->ImageSVG(dirname(__DIR__).'/Images/kneed.svg', 39.5, $this->GetY()+5.5, 5);
		$this->SetXY(46,$this->getY()+5);
        $L=$CellHits/$Data['targets'];
        for($j=0; $j<$Data['targets']; $j++ ) {
            $this->cell($L, 8, chr(65+$j), $j ? '' : 'L', 0, 'C');
        }
		$this->cell(8,8, "#", 'BR',0,'C');
		$this->SetY($this->getY()+8);

		$L=47+$Circle;
        $Rect=($CellH-3)/2;
        $this->SetFont('','', 7);
		for($i=0;$i<$Data['rows'];$i++) {
			// creates the circles for the targets
			for($j=0; $j<$Data['targets']; $j++ ) {
				$this->Circle($L+$j*$Circle*2, $this->getY()+$CircleYOffset, $Circle);
			}

            // sets the 3 letters
            $this->Rect(75+$CellHits+$this->getSideMargin(), $this->GetY()+1, $Rect, $Rect);
            $this->Rect(75+$CellHits+$this->getSideMargin(), $this->GetY()+2+$Rect, $Rect, $Rect);
            $this->Rect(75+$CellHits+$this->getSideMargin()+$Rect*2+2, $this->GetY()+2+$Rect, $Rect, $Rect);
            $OrgX=$this->GetX();
            $OrgY=$this->GetY();
            $this->setXY($OrgX+$CellHits+74.5+$Rect, $OrgY+0.35);
            $this->cell(5, $CellH/2, 'A');
            $this->setXY($OrgX+$CellHits+74.5+$Rect, $OrgY+$CellH/2-0.2);
            $this->cell(5, $CellH/2, 'R');
            $this->setXY($OrgX+$CellHits+74.5+$Rect*2+5, $OrgY+$CellH/2-0.2);
            $this->cell(5, $CellH/2, 'W');

            $this->setXY($OrgX, $OrgY);
			$this->cell(20,$CellH, '', '1',0,'C');
			$this->cell(8,$CellH, '', '1',0,'C');
			$this->cell(8,$CellH, '', '1',0,'C');
			$this->cell($CellHits,$CellH, '', '1',0,'C');
			$this->cell(8,$CellH, '', '1',0,'C');
			$this->cell(15,$CellH, '', '1',0,'C');
			$this->cell(15,$CellH, '', '1',0,'C');
			$this->cell(0,$CellH, '', '1',1,'C');
		}
	}

    function DrawScoreRunArcheryDelays($Data=array()) {
		$TopY=$Data['top'];
		$Width=$this->getPageWidth()-$this->getSideMargin()*2;
		$Height=$Data['height'];
		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;

		// draw a separation line...
		if($TopY-10>0) {
			$this->Line(0,$TopY-10, $this->getPageWidth(), $TopY-10);
		}

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$this->ToPaths['ToLeft']) ) {
				$im=getimagesize($IM);
				$this->Image($IM, 10, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$this->ToPaths['ToRight']) ) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * ($TopOffset/2) / $im[1]);
				$this->Image($IM, (10+$Width-$TmpRight), $TopY, 0, $TopOffset/2);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			// Sponsors disabled if QRCodes are to be printed!!!
			if($this->BottomImage and file_exists($IM=$this->ToPaths['ToBottom'])) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] / $im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] / $im[1] ;
				}
				$this->Image($IM, (10+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}


		//TESTATA GARA
		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(true);
			$this->SetFont($this->FontStd,'B',9);
			$this->SetXY(10+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Name, 0, 'L', 0);
			$this->SetFont($this->FontStd,'',7);
			$this->SetXY(10+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight) {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where, 0, 'L', 0);
				$this->SetXY(10+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			} else {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
		}

		$this->dy(2);
		$Y=$this->GetY();
		$this->SetFont('','b', 14);
		$this->cell(0,0, get_text('DelaysReport', 'RunArchery'), '', 1, 'C');
		$this->dy(2);

		$this->SetFont('','', 8);
		$this->cell(12,0, get_text('RaceNum', 'RunArchery'), '',0,'R');
		$this->cell(10,0, '', 'B');
        $this->cell(22,0, get_text('OfficialName', 'RunArchery'), '', 0,'R');
        $this->cell(70,0, '', 'B', 0);
		$this->cell(20,0, get_text('RaceName', 'RunArchery'), '', 0, 'R');
		$this->cell(0,0, '', 'B', 1);
		$this->dy(2);

		$this->SetFont('','', 9);

		$CellH=($Height+$TopY-14-$this->getY()-$BottomImage)/$Data['rows'];

		// actual score
		$this->cell(20,13, get_text('Bib', 'RunArchery'), '1',0,'C');
		$this->cell(50,7, get_text('StopwatchTime', 'RunArchery'), 'LTR',0,'C', 0, '', 1, false, 'T', 'B');
		$this->writeHTMLCell(25,13, null, null, get_text('TimeToDeduct', 'RunArchery'), 1,0,false, true, 'C');
		$this->cell(0,13, get_text('Notes', 'Tournament'), 1,0,'C');
		$this->SetXY($this->getSideMargin()+20,$this->getY()+7);
		$this->cell(25,6, get_text('Start', 'RunArchery'), 'BR',0,'C');
		$this->cell(25,6, get_text('End', 'RunArchery'), 'LBR',0,'C');
		$this->SetY($this->getY()+6);

        $Rect=($CellH-3)/2;
        $this->SetFont('','', 7);
		for($i=0;$i<$Data['rows'];$i++) {
			$this->cell(20, $CellH, '', '1',0,'C');
			$this->cell(25, $CellH, '', '1',0,'C');
			$this->cell(25, $CellH, '', '1',0,'C');
			$this->cell(25, $CellH, '', '1',0,'C');
			$this->cell(0, $CellH, '', '1',1,'C');
		}
	}
	function DrawScoreRunArcheryLoop($Data=array()) {
		$TopY=10;
		$Width=$this->getPageWidth()-20;
		$Height=$this->getPageHeight()-20;
		//PARAMETRI CALCOLATI
		$TopOffset=30;
		$BottomImage=0;

		//HEADER LOGO SX & Dx
		$TmpLeft = 0;
		$TmpRight = 0;
		if($this->PrintLogo) {
			if(file_exists($IM=$this->ToPaths['ToLeft']) ) {
				$im=getimagesize($IM);
				$this->Image($IM, 10, $TopY, 0, ($TopOffset/2));
				$TmpLeft = (1 + ($im[0] * ($TopOffset/2) / $im[1]));
			}
			if(file_exists($IM=$this->ToPaths['ToRight']) ) {
				$im=getimagesize($IM);
				$TmpRight = ($im[0] * ($TopOffset/2) / $im[1]);
				$this->Image($IM, (10+$Width-$TmpRight), $TopY, 0, $TopOffset/2);
				$TmpRight++;
			}
			//IMMAGINE DEGLI SPONSOR
			// Sponsors disabled if QRCodes are to be printed!!!
			if($this->BottomImage and file_exists($IM=$this->ToPaths['ToBottom'])) {
				$BottomImage=7.5;
				$im=getimagesize($IM);
				$imgW = $Width;
				$imgH = $imgW * $im[1] / $im[0] ;
				if($imgH > $BottomImage) {
					$imgH = $BottomImage;
					$imgW = $imgH * $im[0] / $im[1] ;
				}
				$this->Image($IM, (10+($Width-$imgW)/2), ($TopY+$Height-$imgH), $imgW, $imgH);
			}
		}


		//TESTATA GARA
		if($this->PrintHeader) {
			$tmpPad=$this->getCellPaddings();
			$this->SetCellPadding(0);
			$this->SetColors(true);
			$this->SetFont($this->FontStd,'B',9);
			$this->SetXY(10+$TmpLeft,$TopY);
			$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Name, 0, 'L', 0);
			$this->SetFont($this->FontStd,'',7);
			$this->SetXY(10+$TmpLeft, $this->GetY());
			if($this->GetStringWidth($this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT))>=$Width-$TmpLeft-$TmpRight) {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where, 0, 'L', 0);
				$this->SetXY(10+$TmpLeft, $this->GetY());
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			} else {
				$this->MultiCell($Width-$TmpLeft-$TmpRight, 4, $this->Where . ", " . TournamentDate2String($this->WhenF,$this->WhenT), 0, 'L', 0);
			}
			$this->SetCellPaddings($tmpPad['L'], $tmpPad['T'], $tmpPad['R'], $tmpPad['B']);
		}

		$this->dy(3);
		$Y=$this->GetY();
		$this->SetFont('','b', 14);
		$this->cell(0,0, get_text('PenaltyLoopsReport', 'RunArchery'), '', 1, 'C');
        $this->dy(2);

        $this->SetFont('','', 8);
        $this->cell(12,0, get_text('RaceNum', 'RunArchery'), '',0,'R');
        $this->cell(10,0, '', 'B');
        $this->cell(22,0, get_text('OfficialName', 'RunArchery'), '', 0,'R');
        $this->cell(70,0, '', 'B', 0);
        $this->cell(20,0, get_text('RaceName', 'RunArchery'), '', 0, 'R');
        $this->cell(0,0, '', 'B', 1);
        $this->dy(2);

        $this->SetFont('','', 9);

		$CellH=($Height+$TopY-13-$this->getY()-$BottomImage)/($Data['rows']??20);
        $Circle=($CellH)/2;
        $Loops=$Circle*4+5;
        $L=26+$this->getSideMargin();

		// actual sheet
		// header row 1
		$this->cell(25,12, get_text('Bib', 'RunArchery'), '1',0,'C');
		$this->cell($Loops+10,12, get_text('CountingLoops', 'RunArchery'), 'LTR',0,'C');
		$this->cell(25,12, get_text('Notes', 'Tournament'), 1,0,'C');
		$this->SetFont('','B', 9);
		$this->cell(0,4, get_text('PostCheckByJudges', 'RunArchery'), '1',0,'C');
		$this->SetFont('','', 9);

		// header row 2
		$this->setXY($this->getSideMargin()+$Loops+60,$this->GetY()+4);
		$this->cell(10,4, get_text('DueLoops-Due', 'RunArchery'), 'LTR',0,'C','','',1,'','','B');
		$this->cell(0,8, get_text('Notes', 'Tournament'), '1',0,'C');

		$this->setXY($this->getSideMargin()+$Loops+60, $this->GetY()+4);
		$this->cell(10,4, get_text('DueLoops-Loops', 'RunArchery'), 'LBR',0,'C','','',1,'','','T');

		$this->setY($this->GetY()+4);

		for($i=0;$i<($Data['rows']??20);$i++) {
            // circles
            for($j=0; $j<4; $j++ ) {
//                $this->Circle($L+$j*$Circle*2, $this->getY()+1+$Circle, $Circle);
                $this->RoundedRect($L+$j*($Circle+1), $this->getY()+1, $Circle, $CellH-2, 2);
            }

            $this->cell(25,$CellH, '', '1');
			$this->cell($Loops,$CellH, '', '1');
			$this->cell(10,$CellH, '', '1');
			$this->cell(25,$CellH, '', '1');
			$this->cell(10,$CellH, '', '1');
			$this->cell(0,$CellH, '', '1',1);
		}
	}
}
