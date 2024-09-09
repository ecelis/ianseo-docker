<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once("Common/pdf/ResultPDF.inc.php");

	class SignPDF extends ResultPDF {
		const topMargin=35;

		protected $First;
		protected $Second;
		protected $Third;
		protected $QrCode;
		protected $QrCodeY;
		protected $QrCodeSize;

		protected $FirstSize;
		protected $SecondSize;
		protected $ThirdSize;
		protected $FirstCellSize=0;
		protected $SecondCellSize=0;
		protected $ThirdCellSize=0;

		public function init($First, $Second='', $Third='', $QrCode='') {
            $this->setAutoPageBreak(false);
			$this->First=$First;
			$this->Second=$Second;
			$this->Third=nl2br(trim($Third));
			$this->QrCode=$QrCode;

			$this->FirstSize=220;
			$this->SecondSize=95;
			$this->ThirdSize=14;
			$this->QrCodeSize=80;

			$PageHeight=intval($this->getPageHeight()-$this->getBreakMargin()-$this->getTopMargin()-10);

			switch(true) {
				case ($this->First and $this->Second and $this->QrCode):
					// all 3 so the 3 cell height are 3/6, 2/6, 1/6
					$this->FirstCellSize=$PageHeight/3;
					$this->SecondCellSize=$PageHeight/4;
					$this->ThirdCellSize=($PageHeight/4);
					$this->QrCodeSize=min(50, $this->ThirdCellSize);
					$this->FirstSize=150;
					break;
				case ($this->First and $this->Second):
					// 2 rows, so each has 1/2
					$this->FirstCellSize=intval($PageHeight/2)-5;
					$this->SecondCellSize=intval($PageHeight/2)-10;
					$this->FirstSize=200;
					break;
				case ($this->Second and $this->QrCode):
					// 2 rows, so each has 1/2
					$this->SecondCellSize=$PageHeight/2;
					$this->ThirdCellSize=$PageHeight/2-5;
					$this->QrCodeSize=min(60, $this->ThirdCellSize);
					break;
				case ($this->First and $this->QrCode):
					// 2 rows, so each has 1/2
					$this->FirstCellSize=$PageHeight/2;
					$this->ThirdCellSize=$PageHeight/2-5;
					$this->QrCodeSize=min(70, $this->ThirdCellSize);
					$this->FirstSize=200;
					break;
			}

			if($this->Third) {
				$this->FirstSize=30;
				$this->SecondSize=14;
				$this->ThirdSize=12;
			}
			$this->QrCodeY=$this->getPageHeight()-$this->bMargin-$this->QrCodeSize-5;
		}

		//Page Header
		function Header() {
			global $CFG;
			$LeftStart = 10;
			$RightStart = 10;
			$ImgSizeReq=30;

			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToLeft.jpg')) {
				$im=getimagesize($IM);
				$this->Image($IM, 10, 5, 0, $ImgSizeReq);
				$LeftStart += ($im[0] * $ImgSizeReq / $im[1]);
			}
			if(file_exists($IM=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-ToRight.jpg')) {
				$im=getimagesize($IM);
				$this->Image($IM, ($this->w-10) - ($im[0] * $ImgSizeReq / $im[1]), 5, 0, $ImgSizeReq);
				$RightStart += ($im[0] * $ImgSizeReq / $im[1]);
			}
	    	$this->SetFont($this->FontStd,'B',16);
			$this->SetXY($LeftStart,15);
			$this->Cell($this->w-$LeftStart-$RightStart, 4, ($this->Name), 0, 1, 'C', 0);
		}

		public function Make() {

			if($this->First) {
				$this->SetFont($this->FontStd,'B',$this->FirstSize);
				$this->cell(0,$this->FirstCellSize,$this->First,0,1,'C');
			}

			if ($this->Second!='') {
				$tmpWidth = $this->GetLineWidth();
				switch ($this->Second) {
					case '>':
					case '>>':
						$this->SetLineWidth(15);
						$this->Line($this->w-40,$this->y+25+7.5*sqrt(2)/2,$this->w-75,$this->y-10+7.5*sqrt(2)/2);
						$this->Line($this->w-40,$this->y+25-7.5*sqrt(2)/2,$this->w-75,$this->y+60-7.5*sqrt(2)/2);
						$this->Line(40,$this->y+25,$this->w-47.5,$this->y+25);
						break;
					case '<':
					case '<<':
						$this->SetLineWidth(15);
						$this->Line(40,$this->y+25+7.5*sqrt(2)/2,75,$this->y-10+7.5*sqrt(2)/2);
						$this->Line(40,$this->y+25-7.5*sqrt(2)/2,75,$this->y+60-7.5*sqrt(2)/2);
						$this->Line(47.5,$this->y+25,$this->w-40,$this->y+25);
						break;
					case '^':
					case '^^':
						$this->SetLineWidth(15);
						$this->Line(($this->w/3),$this->y+5,($this->w/3),$this->y+60);
						$this->Line(($this->w/3)-7.5*sqrt(2)/2,$this->y,($this->w/3)+35-7.5*sqrt(2)/2,$this->y+35);
						$this->Line(($this->w/3)+7.5*sqrt(2)/2,$this->y,($this->w/3)-35+7.5*sqrt(2)/2,$this->y+35);
						$this->Line(($this->w*2/3),$this->y+5,($this->w*2/3),$this->y+60);
						$this->Line(($this->w*2/3)-7.5*sqrt(2)/2,$this->y,($this->w*2/3)+35-7.5*sqrt(2)/2,$this->y+35);
						$this->Line(($this->w*2/3)+7.5*sqrt(2)/2,$this->y,($this->w*2/3)-35+7.5*sqrt(2)/2,$this->y+35);
						break;
					case 'v':
					case 'vv':
					case 'V':
					case 'VV':
						$this->SetLineWidth(15);
						$this->Line(($this->w/3),$this->y,($this->w/3),$this->y+55);
						$this->Line(($this->w/3)-7.5*sqrt(2)/2,$this->y+60,($this->w/3)+35-7.5*sqrt(2)/2,$this->y+25);
						$this->Line(($this->w/3)+7.5*sqrt(2)/2,$this->y+60,($this->w/3)-35+7.5*sqrt(2)/2,$this->y+25);
						$this->Line(($this->w*2/3),$this->y,($this->w*2/3),$this->y+55);
						$this->Line(($this->w*2/3)-7.5*sqrt(2)/2,$this->y+60,($this->w*2/3)+35-7.5*sqrt(2)/2,$this->y+25);
						$this->Line(($this->w*2/3)+7.5*sqrt(2)/2,$this->y+60,($this->w*2/3)-35+7.5*sqrt(2)/2,$this->y+25);
						break;
					default:
						$this->SetFont($this->FontStd,'B',$this->SecondSize);
						$this->cell(0,$this->SecondCellSize,$this->Second,0,1,'C');
				}
				$this->SetLineWidth($tmpWidth);
			}

			if($this->QrCode) {
                // set style for barcode
                $style = array(
                    'border' => 2,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
                $X=($this->getPageWidth()-$this->QrCodeSize)/2;
				$this->SetFont('', '', 10);
				$this->write2DBarcode($this->QrCode, 'QRCODE,L', $X, $this->QrCodeY, $this->QrCodeSize, $this->QrCodeSize, $style, 'N');
                $this->cell(0,0,$this->QrCode,'','','C');
			}
			$this->Output();
		}

		public function MakeDocument() {
			$this->SetFont($this->FontStd, 'B', $this->FirstSize);
			$this->cell(0,0,$this->First,0,1,'C');

			if($this->Second) {
				$this->dy(6);

				$this->SetFont($this->FontStd, 'B', $this->SecondSize);
				$this->cell(0,0,$this->Second,0,1,'L');
			}

			if($this->Third) {
				$this->dy(3);

				// get the QRcode tag...
				unset($Matches);
				preg_match_all('#<qrcode>(.*?)</qrcode>#sim', $this->Third, $Matches);
				if($Matches) {
					// set style for barcode
					$style = array(
						'border' => 2,
						'vpadding' => 'auto',
						'hpadding' => 'auto',
						'fgcolor' => array(0,0,0),
						'bgcolor' => false, //array(255,255,255)
						'module_width' => 1, // width of a single module in points
						'module_height' => 1 // height of a single module in points
					);
					$X=($this->getPageWidth()-60)/2;
					// require_once('Common/tcpdf/tcpdf_barcodes_2d.php');
					foreach($Matches[1] as $k => $v) {
						$params = $this->serializeTCPDFtagParameters(array($v, 'QRCODE,L', $X, '', 60, 60, $style, 'N'));
						$this->Third=str_replace($Matches[0][$k],'<tcpdf method="write2DBarcode" params="'.$params.'" />',$this->Third);
					}
				}



				$this->SetFont($this->FontStd, '', $this->ThirdSize);
				$this->MultiCell(0, 0, $this->Third, 0, 'J', false, 1, '', '', true, 0, true);
			}

			$this->Output();
		}
	}
?>