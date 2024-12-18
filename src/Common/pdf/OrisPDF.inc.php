<?php

if(!defined('PRINTLANG')) {
    define('PRINTLANG', 'EN');
}
require_once('Common/pdf/IanseoPdf.php');
require_once('Common/Lib/Fun_DateTime.inc.php');


class OrisPDF extends IanseoPdf {
	var $Number, $Title, $Event, $EvPhase, $EvComment;
	var $Name, $Where, $WhenF, $WhenT, $imgR, $imgL, $imgB, $imgB2, $prnGolds, $prnXNine;
	var $HeaderName = array();
	var $HeaderSize = array();
	var $DataSize = array();
	var $lastY=0;
	var $utsReportCreated=0;
	var $Records=array();
	var $RecCelHeight=4.5;
	var $StopHeader=false;
	var $FooterPrefix='AR_';
	var $Version='';

	const leftMargin=10;
	const topMargin=15;
    const bottomMargin=16;
	const topStart=45;
	var $extraBottomMargin=0;
	var $printPageNo=true;
    var $CompleteBookTitle='';


	//Constructor
	function __construct($DocNumber, $DocTitle, $headers='') {
		parent::__construct($DocTitle, true, $headers);
		if($this->ToPaths['ToBottom']) {
            $im=getimagesize($this->ToPaths['ToBottom']);
            if($im[0]/$im[1] < 17) {
                $this->extraBottomMargin = 8;
            }
        //} else {
        //    $this->extraBottomMargin = -5;
        }
		$this->Title=$DocTitle;
		$this->Number=$DocNumber;
		/*$this->Event='';
		$this->EvPhase='';*/
		if(isset($_REQUEST["ReportCreated"]) && preg_match("/^[0-9]{12}$/i", $_REQUEST["ReportCreated"])) {
            $this->utsReportCreated = mktime(substr($_REQUEST["ReportCreated"], 8, 2), substr($_REQUEST["ReportCreated"], 10, 2), 0, substr($_REQUEST["ReportCreated"], 4, 2), substr($_REQUEST["ReportCreated"], 6, 2), substr($_REQUEST["ReportCreated"], 0, 4));
        } else {
            $this->utsReportCreated = time();
        }

		$this->SetSubject($DocNumber . ' - ' . $DocTitle);
		$this->SetDefaultColor();

		$this->SetMargins(OrisPDF::leftMargin,OrisPDF::topMargin,OrisPDF::leftMargin);

		$this->SetAutoPageBreak(true,OrisPDF::bottomMargin+$this->extraBottomMargin);
	}

	public function setDocUpdate($newDate) {
        if(!$newDate) {
            return;
        }
        if(strlen($newDate)==10) {
            $newDate.=' 00:00:00';
        }
		$this->utsReportCreated = mktime(substr($newDate,11,2),substr($newDate,14,2),0,substr($newDate,5,2),substr($newDate,8,2),substr($newDate,0,4));
	}

	function SetDefaultColor() {
		$this->SetDrawColor(0x00, 0x00, 0x00);
		$this->SetFillColor(0xE0,0xE0,0xE0);
		$this->SetTextColor(0x00, 0x00, 0x00);
	}

	function SetTextRed() {
		$this->SetTextColor(0x8B, 0x00, 0x00);
	}

	function SetTextOrange() {
		$this->SetTextColor(0xEE, 0x76, 0x00);
	}

	function SetTextGreen() {
		$this->SetTextColor(0x4C, 0xC4, 0x17);
	}

    function SetSymbolColor() {
        $this->SetTextColor(0x80, 0x80, 0x80);
    }


	function Header() {
		global $CFG;
		$LeftStart = 10;
		$RightStart = 10;
		$ImgSizeReq=20;

		//Immagini
		if($this->ToPaths['ToLeft']) {
			$im=getimagesize($this->ToPaths['ToLeft']);
			$this->Image($this->ToPaths['ToLeft'], 10, 5, 0, $ImgSizeReq);
			$LeftStart += ($im[0] * $ImgSizeReq / $im[1]);
		}
		if($this->ToPaths['ToRight']) {
			$im=getimagesize($this->ToPaths['ToRight']);
			$this->Image($this->ToPaths['ToRight'], (($this->w-10) - ($im[0] * $ImgSizeReq / $im[1])), 5, 0, $ImgSizeReq);
			$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
		}

		//Where & When
		$this->SetXY($LeftStart,5);
		$this->SetFont($this->FontStd,'',10);
		$this->MultiCell(40, 5, $this->Where,0,'L');
		$this->SetXY($LeftStart,20);
	// patch
		$this->MultiCell(40, 5, TournamentDate2StringShort($this->DtFrom,$this->DtTo),0,'L');

		//Competition Name
		$this->SetXY($LeftStart+40,5);
		$this->SetFont($this->FontStd,'B',11);
		$this->Cell($this->w-$LeftStart-$RightStart-40, 5, preg_replace("/[\r\n]+/sim", ' ', $this->Name),0,0,'L');

		//Event Name if available
		if($this->Event != '') {
			$this->SetXY($LeftStart+40,12.5);
			$this->SetFont($this->FontStd,'B',11);
			$this->Cell($this->w-$LeftStart-$RightStart-40, 5, $this->Event,0,0,'L');
		}

		//Event Phase if available
		if($this->EvPhase != '') {
			$this->SetXY($LeftStart+40,19.5);
			$this->SetFont($this->FontStd,'B',11);
			$this->Cell($this->w-$LeftStart-$RightStart-40, 5, $this->EvPhase,0,0,'L');
		}


		//Linea di divisione
		$this->SetLineWidth(0.3);
		$this->Line(5,30,$this->w-5,30);
		$this->SetLineWidth(0.1);

		//Report Title
		$this->SetXY(10,30);
		$this->SetFont($this->FontStd,'B',12);
		$this->Cell(190,7,mb_convert_case($this->Title, MB_CASE_UPPER, "UTF-8"),0,1,'C');

		//Comment if available
		if($this->EvComment != '') {
			$this->SetXY(145,30);
			$this->SetFont($this->FontStd,'B',8);
			$this->Cell(60,7,($this->EvComment ?? ''),0,1,'R');
		}

		$this->SetFont($this->FontStd,'',8);

		$this->lastY = OrisPDF::topStart-4;

		$this->SetXY(OrisPDF::leftMargin, $this->lastY);

		// Prints Records if available...
		// set defaults cell padding ;)
		$OldPadding=$this->cell_padding;
		$this->setCellPaddings(1,0,1,0);
		foreach($this->Records as $Record) {
			$Rows=0;
			foreach($Record->RtRecExtra as $Extra) {
				$Rows+=$this->RecCelHeight;
			}
			// what
			$this->SetFont('', 'B');
			$this->cell(45, $Rows, $Record->TrHeader.' '.$Record->RtRecDistance.':', 'LTB', 0);
			$this->SetFont('', '');
			// how much
            if($Record->RtRecTotal!=0) {
                $this->cell(10, $Rows, $Record->RtRecTotal . ($Record->RtRecXNine ? '/' . $Record->RtRecXNine : ''), 'TB', 0, 'R');
                $X = $this->getX();
                $Y = $this->getY();
                foreach ($Record->RtRecExtra as $k => $Extra) {
                    $this->SetXY($X, $Y + $k * $this->RecCelHeight);
                    $arc = array();
                    foreach ($Extra->Archers as $t => $Archer) {
                        $arc[] = $Archer['Archer'];
                    }
                    // who
                    $this->cell(80, $this->RecCelHeight, implode('/', $arc), 'TB', 0, 'R');
                    // NOC
                    $this->cell(10, $this->RecCelHeight, $Extra->NOC, 'TB', 0);
                    // where (NOC)
                    $this->cell(30, $this->RecCelHeight, $Extra->EventNOC, 'TB', 0, 'R');
                }
                // date
                $this->SetXY($X + 120, $Y);
                $this->cell(0, $Rows, $Record->RtRecDate, 'TBR', 1, 'R');
            } else {
                $this->cell(10, $Rows, '-', 'TB', 0, 'R');
                $this->cell(0, $Rows, '', 'TBR', 1, 'R');
            }
			$this->lastY+=$Rows;
		}
		$this->lastY+=3;


		//Report Table Header
		if(!$this->StopHeader and count($this->HeaderName)>0) {
			$this->printHeader(OrisPDF::leftMargin, $this->lastY);
		}
		$this->cell_padding=$OldPadding;
	}


	function Footer() {
		global $CFG;

        $TopStart = ($this->h-(15 + $this->extraBottomMargin));

		$this->SetLineWidth(0.3);
		$this->Line(5, $TopStart, ($this->w-5), $TopStart);
		$this->SetLineWidth(0.1);

		$this->SetFont($this->FontStd,'',8);
		$this->SetXY(10,$TopStart);
		$this->Cell(60,3,mb_convert_case($this->FooterPrefix . $this->Number, MB_CASE_UPPER, "UTF-8"),0,0,'L');
        if($this->printPageNo) {
		    $this->SetXY($this->w-60,$TopStart);
            $this->Cell(60, 3, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
        }
		$this->SetXY($this->w/2-30,$TopStart);
		$this->Cell(60,3,'Report Created: ' . date('d M Y H:i',$this->utsReportCreated).' @ UTC'.$this->TzOffset.($this->Version ? ' (v. '.$this->Version.')' : ''),0,0,'C');

		$im=NULL;
		if($this->ToPaths['ToBottom']) {
			$im=getimagesize($this->ToPaths['ToBottom']);
            $imgwidth = $im[0] * (7 + $this->extraBottomMargin) / $im[1];
            $this->Image($this->ToPaths['ToBottom'], ($this->w-$imgwidth)/2, $this->h-(11+$this->extraBottomMargin), 0, (7+$this->extraBottomMargin));
		}
		$this->SetFont($this->FontStd,'',8);

	}

	function setDataHeader($FieldNames, $FieldSizes) {
		$this->HeaderName=array();
		$this->HeaderSize=array();
		$this->DataSize=array();
		if(end($FieldSizes)==0) {
			$FieldSizes[count($FieldSizes)-1]=$this->getPageWidth()-$this->lMargin-$this->rMargin-array_sum($FieldSizes);
		}

		if(!is_array($FieldNames)) {
            $this->HeaderName[] = $FieldNames;
        } else {
            $this->HeaderName = $FieldNames;
        }

		if(!is_array($FieldSizes)) {
			$this->HeaderSize[] = $FieldSizes;
			$this->DataSize[] = $FieldSizes;
		} else {
			foreach($FieldSizes as $fs) {
				if(!is_array($fs)) {
					$this->HeaderSize[] = $fs;
					$this->DataSize[] = $fs;
				} else {
					$this->HeaderSize[] = array_sum($fs);
					$this->DataSize = array_merge($this->DataSize,$fs);
				}
			}
		}
	}

	function printHeader($xPosition, $yPosition) {
		$maxCell= 0;
		$this->SetLineWidth(0.1);
		$this->SetFont($this->FontStd,'B',8);
		$this->SetXY($xPosition, $yPosition);
		$Rows=3.5;
		if(strstr(implode('', $this->HeaderName), "\n")) {
			$Rows=7;
		}
		foreach($this->HeaderName as $i => $Header) {
			if($Header and $Header[0]=='@') {
				$Header=substr($Header, 1);
			}

			$Align='L';
			if(strstr($Header, '#')) {
				$Align='R';
			} elseif(strstr($Header, '§')) {
				$Align='C';
			}

			if(strstr($Header, "\n")) {
				$Header=explode("\n", $Header);
				$cHeight=3.5;
			} else {
				$Header=array($Header);
				$cHeight=$Rows;
			}

			$OrgX=$this->getx();

			foreach($Header as $j => $Head) {
				if(strstr($Head, '#')) {
					$Align='R';
				} elseif(strstr($Head, '§')) {
					$Align='C';
				}
				$Head=str_replace(array("#",'§'),"", $Head);
				$this->SetXY($OrgX, $yPosition+$j*3.5);
				$this->Cell($this->HeaderSize[min($i,count($this->HeaderSize)-1)], $cHeight, $Head,0,0, $Align);
			}
		}
		$this->Rect($xPosition, $yPosition-1, $this->getPageWidth()-20,$Rows+2);
		$this->SetFont($this->FontStd,'',8);
		$this->lastY = $yPosition+$Rows+2;
	}

	function addSpacer($size=2) {
		$this->lastY += $size;
	}

	function printDataRow($data) {
		$maxCell= 1;
		$this->SetFont($this->FontStd,'',8);
        $this->samePage(2, 3.5, $this->lastY); // check if there is enough space from the future location
		$this->SetXY(OrisPDF::leftMargin, $this->lastY); // sets the correct location after eventually the reset of lastY made by the setheader in case of a new page
		for($i=0; $i<count($data); $i++) {
			$Align='L';
			if(strstr($data[$i]??'',"#")) {
				$Align='R';
			} elseif(strstr($data[$i]??'',"§")) {
				$Align='C';
			}
            if(strstr($data[$i]??'',"~")) {
                $this->SetFont('', 'B');
            }
			if(strstr($data[$i]??'', "\n")) {
				$CellData=explode("\n", $data[$i]);
				$OrgX=$this->GetX();
				$OrgY=$this->GetY();
				$maxCell=count($CellData);
				foreach($CellData as $k=>$v) {
					$this->SetXY($OrgX, $OrgY + ($k*3.5));
					if(!$k) {
						// first line bold
						$this->SetFont('', 'B');
					}
					$this->Cell($this->DataSize[min($i,count($this->DataSize)-1)],3.5, str_replace(array("#","§"),"", $v),0,0, $Align);
					$this->SetFont('', '');
				}
				$this->SetXY($this->GetX(), $OrgY);
				//$maxCell = max($maxCell, $this->MultiCell($this->DataSize[min($i,count($this->DataSize)-1)],3.5,str_replace(array("#","§"),"",$data[$i]),0, $Align, 0, 0));
			} else {
				$this->Cell($this->DataSize[min($i,count($this->DataSize)-1)],3.5,str_replace(array("#","§","~"),"",$data[$i]??''),0,0, $Align);
			}
            if(strstr($data[$i]??'',"~")) {
                $this->SetFont('', '');
            }
		}
		$this->lastY += 3.5*$maxCell;
	}

	function printSectionTitle($text, $y=null) {
		$this->SetFont($this->FontStd,'B',10);
		$this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
		$this->Cell(0.1,5,'',0,0,'L');
		$this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
		$this->Cell(array_sum($this->DataSize),5,str_replace(array("#",'§'),"",$text),0,0,(strpos($text,"#")===false ? (strpos($text,"§")===false ? 'L':'C'):'R'));
		$this->lastY += 5;
	}

    function printSectionTitleWContinue($text, $y=null) {
        $this->SetFont($this->FontStd,'B',10);
        $this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
        $this->Cell(0.1,5,'',0,0,'L');
        $this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
        $this->Cell(array_sum($this->DataSize),5,str_replace(array("#",'§'),"",$text),0,0,(strpos($text,"#")===false ? (strpos($text,"§")===false ? 'L':'C'):'R'));
        $this->SetXY(OrisPDF::leftMargin, is_null($y) ? $this->lastY : max(OrisPDF::topStart, $y));
        $this->SetFont($this->FontStd,'',8);
        $this->Cell(array_sum($this->DataSize),5,'continue...',0,0,'R');
        $this->lastY += 5;
    }

	function setEvent($name) {
		$this->Event = $name;
	}

	function setPhase($name) {
		$this->EvPhase = $name;
	}

	function setComment($comment) {
		$this->EvComment = $comment;
	}

	function samePage($rowNo, $rowHeight=3.5, $y='', $addPage=true) {
		return !$this->checkPageBreak($rowNo * $rowHeight, $y, $addPage);
	}

	function setOrisCode($newCode='', $newTitle='', $force=false) {
        // should print the correct Oris Code BEFORE opening the new page
        $this->endPage();

        if($newCode != '' or $force) {
			$this->Number=$newCode;
		}
		if($newTitle != '' or $force) {
			$this->Title=$newTitle;
		}
	}

	function setPrintPageNo($doPrint) {
	    $this->printPageNo = $doPrint;
    }

    function OrisScorecard($Data, $Bottom=0, $Phase=null, $Section=null, $Meta=null, $Team=0, $TeamComponents = array()) {
	    $nLines = 1;
        $symbols = array('l','n','s','t','u','v','w','z','H');
        if($Phase['FinElimChooser']) {
	    	$Ends = intval($Section['elimEnds']);
	    	$Arrows = intval($Section['elimArrows']);
	    	$SO = intval($Section['elimSO']);
	    } else {
	    	$Ends = intval($Section['finEnds']);
	    	$Arrows = intval($Section['finArrows']);
	    	$SO = intval($Section['finSO']);
	    }
        if($Team) {
            $nLines = ceil($Arrows / $Section["maxTeamPerson"]);
        }

		$CellHeight=5;
        $ShooterSpacingWidth = 10;
		$ScoreWidth=(($this->getPageWidth()-30)/2);
		$CellWidthShort=10+(($Arrows/$nLines)<=3 ? (10/($Arrows/$nLines)) : 0);
		$CellWidthLong=($ScoreWidth-$ShooterSpacingWidth-$CellWidthShort*(1 + ($Arrows/$nLines)))/2;
        $TableScoreWidth = $ScoreWidth-$CellWidthLong-$ShooterSpacingWidth;
        $InterspaceWidth = 2*($CellWidthLong+$ShooterSpacingWidth)+10;
        $JudgesLabel = $CellWidthShort * 2;


		$SoWidth=min($CellWidthShort, ($CellWidthShort*$Arrows)/$SO);
		$SoGap=$CellWidthShort*($Arrows/$nLines) - $SoWidth*$SO;
	    $HeadWidthTitle=20;
	    $HeadWidthData=$TableScoreWidth-$HeadWidthTitle;

		$Offset=35+($Bottom ? $this::topMargin+($this->getPageHeight()-50-$this->extraBottomMargin-$this::bottomMargin-$this::topMargin)/2 : 0);
		$this->SetY($Offset, true);
		$this->SetFont('','B',14);
		$this->Cell(0,0, $Phase['matchName'], '', '1','C');
		$this->SetFont('','',12);
		$this->Cell(0,0, (is_null($Data['scheduledKey']) ? '' : date('D j M Y', strtotime($Data['scheduledKey'])).' ').$Meta['fields']['scheduledTime'].': '.$Data['scheduledTime'], '', '1','C');
        if($Data['odfMatchName']!=0) {
            $this->Cell(0,0, 'Match Number: ' . $Data['odfMatchName'], '', '1','C');
        }
		//$this->ln();
        $this->SetFont('','',10);

        //line 0: target
        $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['target'].':');
        $this->Cell($HeadWidthData,$CellHeight, $Data['target']);
        $this->Cell($InterspaceWidth,$CellHeight, '');
        $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['oppTarget'].':');
        $this->Cell($HeadWidthData,$CellHeight, $Data['oppTarget']);
        $this->ln();

	    //line 1: NOC / athlete
	    if($Team) {
		    $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['countryCode'] .':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,$CellHeight, $Data['countryCode']);
            $this->SetFont('','');
		    $this->Cell($InterspaceWidth,$CellHeight, '');
		    $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['countryCode'].':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,$CellHeight, $Data['oppCountryCode']);
		    $this->SetFont('','');
	    } else {
		    $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['fullName'].':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,$CellHeight, $Data['athlete'] .' ('.$Data['bib'].')');
            $this->SetFont('','');
            $this->Cell($InterspaceWidth,$CellHeight, '');
		    $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['fullName'].':');
		    $this->SetFont('','b');
		    $this->Cell($HeadWidthData,$CellHeight, $Data['oppAthlete'] .' ('.$Data['oppBib'].')');
		    $this->SetFont('','');
	    }
	    $this->ln();

        //line 2: Country
        $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['countryName'] . ':');
        $this->SetFont('', 'b');
        $this->Cell($HeadWidthData, $CellHeight, $Data['countryName']);
        $this->SetFont('', '');
        $this->Cell($InterspaceWidth, $CellHeight, '');
        $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['countryName'] . ':');
        $this->SetFont('', 'b');
        $this->Cell($HeadWidthData, $CellHeight, $Data['oppCountryName']);
        $this->SetFont('', '');
        $this->ln();

        //line 3: Seed
        $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['qualRank'] . ':');
        $this->SetFont('', 'b');
        $this->Cell($HeadWidthData, $CellHeight, $Data['qualRank']??'');
        $this->SetFont('', '');
        $this->Cell($InterspaceWidth, $CellHeight, '');
        $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['oppQualRank'] . ':');
        $this->SetFont('', 'b');
        $this->Cell($HeadWidthData, $CellHeight, $Data['oppQualRank']??'');
        $this->SetFont('', '');
        $this->ln();

        //Line 4: athletes in Team?
        $Athlist=array(array(), array());
        $AthAvg=array(array(), array());
        if($Team) {
            $posXY = array($this->GetX(), $this->GetY());
            foreach($TeamComponents[$Data['teamId']][$Data['subTeam']] as $k => $ath) {
                if($k==0) {
                    $this->Cell($HeadWidthTitle-($Data['shootingarchersAvailable'] ? 5:0), $CellHeight, $Meta['fields']['athlete'] . ':');
                } else {
                    $this->ln();
                    $this->Cell($HeadWidthTitle-($Data['shootingarchersAvailable'] ? 5:0), $CellHeight, '');
                }
                if($Data['shootingarchersAvailable']) {
                    $Athlist[0][$ath['id']] = array($k, 0, 0);
                    $AthAvg[0][$k] = 0;
                    $this->SetFont($this->FontSymbol);
                    $this->SetSymbolColor();
                    $this->Cell(5, $CellHeight, $symbols[$k % 9]);
                    $this->SetDefaultColor();
                    $this->SetFont($this->FontStd);
                }
                $this->Cell($HeadWidthData, $CellHeight, $ath['familyUpperName'] . ' '. $ath['givenName']);
            }
            $this->Cell($InterspaceWidth, $CellHeight, '');
            $posXY[0] = $this->GetX();
            $this->setXY($posXY[0],$posXY[1]);
            foreach($TeamComponents[$Data['oppTeamId']][$Data['oppSubTeam']] as $k => $ath) {
                if($k==0) {
                    $this->Cell($HeadWidthTitle-($Data['oppShootingarchersAvailable'] ? 5:0), $CellHeight, $Meta['fields']['athlete'] . ':');
                } else {
                    $this->ln();
                    $this->setX($posXY[0]);
                    $this->Cell($HeadWidthTitle-($Data['oppShootingarchersAvailable'] ? 5:0), $CellHeight, '');
                }
                if($Data['oppShootingarchersAvailable']) {
                    $Athlist[1][$ath['id']] = array($k, 0, 0);
                    $AthAvg[1][$k] = 0;
                    $this->SetFont($this->FontSymbol);
                    $this->SetSymbolColor();
                    $this->Cell(5, $CellHeight, $symbols[$k % 9]);
                    $this->SetDefaultColor();
                    $this->SetFont($this->FontStd);
                }
                $this->Cell($HeadWidthData, $CellHeight, $ath['familyUpperName'] . ' '. $ath['givenName']);
            }
            $this->ln();
        }

	    //After: Coach?
        if(!empty($Data['coach']) OR !empty($Data['oppCoach'])) {
            $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['coach'] . ':');
            $this->Cell($HeadWidthData, $CellHeight, $Data['coach'] . ($Data['countryCode']!=$Data['coachCountry'] ? ' ('.$Data['coachCountry'].')' : '' ));
            $this->Cell($InterspaceWidth, $CellHeight, '');
            $this->Cell($HeadWidthTitle, $CellHeight, $Meta['fields']['coach'] . ':');
            $this->Cell($HeadWidthData, $CellHeight, $Data['oppCoach'] . ($Data['oppCountryCode']!=$Data['oppCoachCountry'] ? ' ('.$Data['oppCoachCountry'].')' : '' ));
            $this->ln();
        }
	    // empty line
	    $this->ln(2);

	    // Winner / IRM status, BOXED
        $this->SetFont('', 'b', 14);
	    $Txt='';
	    if($Data['irm']) {
	    	$Txt=$Data['irmText'];
	    } elseif($Data['winner']) {
	    	$Txt=$Meta['fields']['winner'];
	    }
	    $this->Cell($TableScoreWidth,$CellHeight, $Txt, $Txt ? 1 : 0, 0, 'C');
	    $this->Cell($InterspaceWidth,$CellHeight, '');
	    $Txt='';
	    if($Data['oppIrm']) {
	    	$Txt=$Data['oppIrmText'];
	    } elseif($Data['oppWinner']) {
	    	$Txt=$Meta['fields']['winner'];
	    }
	    $this->Cell($TableScoreWidth,$CellHeight, $Txt, $Txt ? 1 : 0, 0, 'C');
	    $this->ln();

	    // empty line
	    $this->ln(2);

	    // score drawing
	    // head
        $this->SetFont('', 'b', 10);
	    $this->Cell($CellWidthShort, $CellHeight, $Section['endName'],1, '0', 'C');
        $this->Cell($CellWidthShort*($Arrows/$nLines), $CellHeight, $Meta['fields']['arrowstring'],1, '0', 'C');
        $this->Cell($CellWidthLong, $CellHeight, $Meta['fields']['scoreLong'],1, 0, 'C');

	    $this->Cell($ShooterSpacingWidth, $CellHeight, '');
        $this->Cell($InterspaceWidth-2*$ShooterSpacingWidth, $CellHeight, $Section['matchMode'] ? $Meta['fields']['setPoints'] : $Meta['fields']['scoreLong'],1, 0, 'C');
        $this->Cell($ShooterSpacingWidth, $CellHeight, '');

	    $this->Cell($CellWidthShort, $CellHeight, $Section['endName'],1, '0', 'C');
        $this->Cell($CellWidthShort*($Arrows/$nLines), $CellHeight, $Meta['fields']['arrowstring'],1, 0, 'C');
        $this->Cell($CellWidthLong, $CellHeight, $Meta['fields']['scoreLong'],1, 0, 'C');
	    $this->ln();
        $this->SetFont('', '');

        $endTot=explode('|', $Data['setPoints']);
        $endPts=explode('|', $Data['setPointsByEnd']);
        $oppEndTot=explode('|', $Data['oppSetPoints']);
        $oppEndPts=explode('|', $Data['oppSetPointsByEnd']);
        $Tot=0;
        $OppTot=0;
        for($i=0;$i<$Ends;$i++) {
	        $this->Cell($CellWidthShort, $CellHeight * $nLines,$i+1,1, 0, 'C');
	        $pts='';
            $posXY = array($this->GetX(),$this->GetY());
            for($n=0; $n<$nLines; $n++) {
                $loopArrows = ($Arrows/$nLines);
                for ($j = 0; $j < $loopArrows; $j++) {
                    $this->setXY($posXY[0]+($j*$CellWidthShort), $posXY[1]+($n*$CellHeight));
                    $pts = substr($Data['arrowstring'], ($i * $Arrows) + ($n * $loopArrows) + $j, 1);
                    if($Team and $Data['shootingarchersAvailable'] AND isset($Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]) AND isset($Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]])) {
                        $Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][1] += ValutaArrowString($pts);
                        $Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][2]++;
                        $AthAvg[0][$Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][0]] = $Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][1] / $Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][2];
                        $this->SetFont($this->FontSymbol);
                        $this->SetSymbolColor();
                        $this->Cell(5, $CellHeight, (($pts != ' ') ? $symbols[$Athlist[0][$Data['shootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][0]] : ''), 'LTB', 0, 'C');
                        $this->SetDefaultColor();
                        $this->SetFont($this->FontStd);
                        $this->Cell($CellWidthShort-5, $CellHeight, DecodeFromLetter($pts), 'RTB', 0, 'C');
                    } else {
                        $this->Cell($CellWidthShort, $CellHeight, DecodeFromLetter($pts), 1, 0, 'C');
                    }

                }
            }
            $this->setXY($posXY[0]+($Arrows/$nLines)*$CellWidthShort, $posXY[1]);
			if(trim($pts)) {
				$Tot+=$endTot[$i];
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, $endTot[$i],1, 0, 'C');
	            $this->Cell($ShooterSpacingWidth, $CellHeight*$nLines, ((intval($Data['shootFirst']) & (2**$i)) != 0 ? '<' : ''),0,0,'C');
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, ($Section['matchMode'] ? ($endPts[$i]??'') : $Tot),1, 0, 'C');
			} else {
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, '',1, 0, 'C');
	            $this->Cell($ShooterSpacingWidth, $CellHeight*$nLines, '');
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, '',1, 0, 'C');
			}

			$this->Cell(10, $CellHeight*$nLines, '');

            $pts=substr($Data['oppArrowstring'], $i*$Arrows,1);
			if(trim($pts)) {
				$OppTot+=$oppEndTot[$i];
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, $Section['matchMode'] ? ($oppEndPts[$i]??'') : $OppTot,1, 0, 'C');
                $this->Cell($ShooterSpacingWidth, $CellHeight*$nLines, ((intval($Data['oppShootFirst']) & (2**$i)) != 0 ? '>' : ''),0,0,'C');
			} else {
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, '',1, 0, 'C');
                $this->Cell($ShooterSpacingWidth, $CellHeight*$nLines, '');
			}
	        $this->Cell($CellWidthShort, $CellHeight*$nLines,$i+1,1, 0, 'C');

            $posXY = array($this->GetX(),$this->GetY());
            for($n=0; $n<$nLines; $n++) {
                $loopArrows = ($Arrows/$nLines);
                for ($j = 0; $j < $loopArrows; $j++) {
                    $this->setXY($posXY[0]+($j*$CellWidthShort), $posXY[1]+($n*$CellHeight));
                    $pts = substr($Data['oppArrowstring'], ($i * $Arrows) + ($n * $loopArrows) + $j, 1);
                    if($Team and $Data['oppShootingarchersAvailable'] AND isset($Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]) AND isset($Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]])) {
                        $Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][1] += ValutaArrowString($pts);
                        $Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][2]++;
                        $AthAvg[1][$Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][0]] = $Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][1] / $Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][2];
                        $this->SetFont($this->FontSymbol);
                        $this->SetSymbolColor();
                        $this->Cell(5, $CellHeight, (($pts != ' ') ? $symbols[$Athlist[1][$Data['oppShootingArchers'][($i * $Arrows) + ($n * $loopArrows) + $j]][0]] : ''), 'LTB', 0, 'C');
                        $this->SetDefaultColor();
                        $this->SetFont($this->FontStd);
                        $this->Cell($CellWidthShort-5, $CellHeight, DecodeFromLetter($pts), 'RTB', 0, 'C');
                    } else {
                        $this->Cell($CellWidthShort, $CellHeight, DecodeFromLetter($pts), 1, 0, 'C');
                    }
                }
            }
            $this->setXY($posXY[0]+($Arrows/$nLines)*$CellWidthShort, $posXY[1]);
			if(trim($pts)) {
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, $oppEndTot[$i],1, 0, 'C');
			} else {
		        $this->Cell($CellWidthLong, $CellHeight*$nLines, '',1, 0, 'C');
			}
	        $this->ln();
        }

		// SO
	    if($Data['tie'] or $Data['oppTie']) {
	    	$Rows=ceil(strlen(trim($Data['tiebreak']))/$SO);
	    	$Ties=explode(',', $Data['tiebreakDecoded']);
	    	$OppTies=explode(',', $Data['oppTiebreakDecoded']);
	        for($i=0; $i<$Rows; $i++) {
		        $this->Cell($CellWidthShort, $CellHeight,$Meta['fields']['tie'].' '.($i+1),1,0,'C');
		        for($j=0;$j<$SO;$j++) {
		            $pts=substr($Data['tiebreak'], $i*$SO + $j,1);
                    if($Team and $Data['shootingarchersAvailable'] AND isset($Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]) AND isset($Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]])) {
                        $Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][1] += ValutaArrowString($pts);
                        $Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][2]++;
                        $AthAvg[0][$Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][0]] = $Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][1] / $Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][2];
                        $this->SetFont($this->FontSymbol);
                        $this->SetSymbolColor();
                        $this->Cell(5, $CellHeight, $symbols[$Athlist[0][$Data['shootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][0]], 'LTB', 0, 'C');
                        $this->SetDefaultColor();
                        $this->SetFont($this->FontStd);
                        $this->Cell($SoWidth-5, $CellHeight, DecodeFromLetter($pts), 'RTB', 0, 'C');
                    } else {
                        $this->Cell($SoWidth, $CellHeight, DecodeFromLetter($pts), 1, 0, 'C');
                    }
		        }

		        if($SoGap) {
			        $this->Cell($SoGap, $CellHeight, '');
		        }

		        if($SO>1) { //Total Cell LEFT
			        $this->Cell($CellWidthLong, $CellHeight, $Ties[$i],1,0,'C');
		        } else {
			        $this->Cell($CellWidthLong, $CellHeight, '');
		        }

		        // closest to center goes only on last row
		        if($i==$Rows-1) {
                    $this->Cell($ShooterSpacingWidth,$CellHeight,((intval($Data['shootFirst']) & (2**$Ends)) != 0 ? '<' : ''),0, 0, 'C');
			        if($Section['matchMode']) {
				        $this->Cell($CellWidthLong, $CellHeight, $Data['tie'], 1,0,'C');
			        } else {
				        $this->Cell($CellWidthLong, $CellHeight, '', 0,0,'C');
			        }
		        } else {
                    $this->Cell($ShooterSpacingWidth,$CellHeight,((intval($Data['shootFirst']) & (2**$Ends)) != 0 ? '<' : ''),0, 0, 'C');
			        $this->Cell($CellWidthLong,$CellHeight,'');
		        }

				$this->Cell(10, $CellHeight, $Meta['fields']['tie'],1,0, 'C');

		        if($i==$Rows-1) {
			        if($Section['matchMode']) {
				        $this->Cell($CellWidthLong, $CellHeight, $Data['oppTie'], 1,0,'C');
			        } else {
				        $this->Cell($CellWidthLong, $CellHeight, '', 0,0,'C');
			        }
			        $this->Cell($ShooterSpacingWidth,$CellHeight,((intval($Data['oppShootFirst']) & (2**$Ends)) != 0 ? '>' : ''),0, 0, 'C');
		        } else {
                    $this->Cell($ShooterSpacingWidth,$CellHeight,((intval($Data['oppShootFirst']) & (2**$Ends)) != 0 ? '>' : ''),0, 0, 'C');
			        $this->Cell($CellWidthLong, $CellHeight,'');
		        }
		        $this->Cell($CellWidthShort, $CellHeight,$Meta['fields']['tie'].' '.($i+1),1,0,'C');
		        for($j=0;$j<$SO;$j++) {
		            $pts=substr($Data['oppTiebreak'], $i*$SO + $j,1);
                    if($Team and $Data['oppShootingarchersAvailable'] AND isset($Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]) AND isset($Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]])) {
                        $Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][1] += ValutaArrowString($pts);
                        $Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][2]++;
                        $AthAvg[1][$Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][0]] = $Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][1] / $Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][2];
                        $this->SetFont($this->FontSymbol);
                        $this->SetSymbolColor();
                        $this->Cell(5, $CellHeight, $symbols[$Athlist[1][$Data['oppShootingArchers'][($Ends * $Arrows) + ($i * $Rows) + $j]][0]], 'LTB', 0, 'C');
                        $this->SetDefaultColor();
                        $this->SetFont($this->FontStd);
                        $this->Cell($SoWidth-5, $CellHeight, DecodeFromLetter($pts), 'RTB', 0, 'C');
                    } else {
                        $this->Cell($SoWidth, $CellHeight, DecodeFromLetter($pts), 1, 0, 'C');
                    }
		        }

		        if($SoGap) {
			        $this->Cell($SoGap, $CellHeight, '');
		        }

		        if($SO>1) { //Total Cell RIGHT
			        $this->Cell($CellWidthLong, $CellHeight, $OppTies[$i],1,0,'C');
		        } else {
			        $this->Cell($CellWidthLong, $CellHeight, '',0,0,'C');
		        }

		        $this->ln();
	        }
	    }

	    // Closest+TOTALS
        $this->ln(1);
        $this->SetFont('', 'b');
        $this->Cell($CellWidthShort*(1+($Arrows/$nLines))+$CellWidthLong+$ShooterSpacingWidth, $CellHeight, $Data['closest'] ? $Meta['fields']['closest'] : '');
        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $Data['setScore'] : $Data['score'], 1, 0, 'C');
        $this->SetFont('', '');
		$this->Cell(10, $CellHeight, $Meta['fields']['scoreLong'],1,0, 'C');
        $this->SetFont('', 'b');
        $this->Cell($CellWidthLong, $CellHeight, $Section['matchMode'] ? $Data['oppSetScore'] : $Data['oppScore'], 1, 0, 'C');
        $this->Cell($ShooterSpacingWidth,$CellHeight, '');
        $this->Cell($CellWidthShort*(1+($Arrows/$nLines)), $CellHeight, $Data['oppClosest'] ? $Meta['fields']['closest'] : '');
        $this->SetFont('', '');

        //if Teams and components, put average
        if($Team and (count($Athlist[0]) OR count($Athlist[1]))) {
            $this->ln(10);
            $this->SetFont('', 'b');
            $this->Cell($CellWidthShort*(1+($Arrows/$nLines))+$CellWidthLong+$ShooterSpacingWidth, $CellHeight, '');
            $this->Cell($InterspaceWidth-2*$ShooterSpacingWidth, $CellHeight, $Meta['AverageArrowScore'],1,0,'C');
            $this->SetFont('', '');
            for ($i=0; $i<max(count($AthAvg[0]),count($AthAvg[1])); $i++) {
                $this->ln();
                $this->setX(($this->getPageWidth()-((2*$CellWidthLong+4*$JudgesLabel)+10))/2);
                $this->Cell(2*$JudgesLabel, $CellHeight, $TeamComponents[$Data['teamId']][$Data['subTeam']][$i]['familyUpperName'] . ' '. $TeamComponents[$Data['teamId']][$Data['subTeam']][$i]['givenName'], 1,0, 'L');
                $this->Cell($CellWidthLong, $CellHeight, number_format($AthAvg[0][$i],2,'.',''),1, 0, 'C');
                $this->SetFont($this->FontSymbol);
                $this->SetSymbolColor();
                $this->Cell(10, $CellHeight, $symbols[$i][0], 'LTB', 0, 'C');
                $this->SetDefaultColor();
                $this->SetFont($this->FontStd);
                $this->Cell($CellWidthLong, $CellHeight, number_format($AthAvg[1][$i],2,'.',''),1, 0, 'C');
                $this->Cell(2*$JudgesLabel, $CellHeight, $TeamComponents[$Data['oppTeamId']][$Data['oppSubTeam']][$i]['familyUpperName'] . ' '. $TeamComponents[$Data['oppTeamId']][$Data['oppSubTeam']][$i]['givenName'], 1,0, 'L');
            }
        }

	    //last line: Judges?
        if(!empty($Data['lineJudge']) OR !empty($Data['targetJudge'])) {
            $this->ln(10);
            $this->setX(($this->getPageWidth()-$ScoreWidth)/2);
            $this->Cell($JudgesLabel, $CellHeight, $Meta['fields']['lineJudge'] . ':', 'TL');
            $this->Cell($ScoreWidth-$JudgesLabel, $CellHeight, $Data['lineJudge'],'TR',1);
            $this->setX(($this->getPageWidth()-$ScoreWidth)/2);
            $this->Cell($JudgesLabel, $CellHeight, $Meta['fields']['targetJudge'] . ':','BL');
            $this->Cell($ScoreWidth-$JudgesLabel, $CellHeight, $Data['targetJudge'],'BR',1);
        }
	    return;
    }
}
