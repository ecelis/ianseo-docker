<?php

if(!defined('CRLF')) {
	define('CRLF', "\r\n" );
}

class IanseoCalendar {
	public $Author = "Ianseo";
	public $Description = "Ianseo Event Schedule";
	public $Reset=false;
	public $CancelPrevious=true;
	public $Events=[];
	public $Title = "";
	public $TourId = 0;
	public $Filename = "";
	public $UtcOffset = "";
	public $Location = "";
	public $Comment = "";
	private $EventStructure = [];

	public function __construct($TourId, $Title, $Location, $Filename, $UtcOffset, $Version='') {
		$this->Title  = $Title;
		$this->Filename = $Filename;
		$this->UtcOffset = $UtcOffset;
		$this->Location = $Location;
		$this->Comment = ($Version ? 'Version '.$Version : '');
		$this->$TourId = $TourId;
	}

	function Output($Download=false) {
		if($Download) {
			return $this->generateDownload();
		}
		return $this->generateString();
	}

	/**
	 *
	 * Call this function to download the invite.
	 */
	public function generateDownload() {
		$generated = $this->generateString();
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); //date in the past
		header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); //tell it we just updated
		header('Cache-Control: no-store, no-cache, must-revalidate' ); //force revalidation
		header('Cache-Control: post-check=0, pre-check=0', false );
		header('Pragma: no-cache' );
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename="'.$this->Filename.'.ics"');
		header("Content-Description: File Transfer");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . strlen($generated));
		print $generated;
	}

	/**
	 *
	 * The function generates the actual content of the ICS
	 * file and returns it.
	 *
	 * @return string|bool
	 */
	public function generateString() {
		$oldcontent='';
		$events=implode('', $this->Events);

		if($this->CancelPrevious) {
			$Previous=[];
			if(!$this->Reset and ($Previous=getModuleParameter('ICS', 'Previous', [], $this->TourId))) {
				// only creates the previous if there is actually something to delete!
				foreach($Previous as $UID=>$Vevent) {
					if(!isset($this->EventStructure[$UID])) {
						$oldcontent.='BEGIN:VEVENT'.CRLF
							. $Vevent . CRLF
							. 'STATUS:CANCELLED' . CRLF
							. 'END:VEVENT' . CRLF
							;
					}
				}
			}

			// adds the new "previous" items
			foreach($this->EventStructure as $UID=>$Vevent) {
				$Previous[$UID]=$Vevent;
			}
			setModuleParameter('ICS','Previous', $Previous, $this->TourId);
		}

		$content = "BEGIN:VCALENDAR".CRLF
			. "PRODID:-//" . $this->Author . "/" . $this->Filename . "//NONSGML//EN".CRLF
			. "VERSION:2.0".CRLF
			. "CALSCALE:GREGORIAN".CRLF
			. "METHOD:PUBLISH".CRLF
			. "LOCATION:".$this->formatValue($this->Location).CRLF
			. "X-WR-CALNAME:" . $this->formatValue($this->Title) . CRLF
			. "X-WR-CALDESC:" . $this->formatValue($this->Description) . CRLF
			. "DESCRIPTION:" . $this->formatValue($this->Description) . CRLF;
		if($this->Comment) {
			$content.="COMMENT:".$this->Comment.CRLF;
		}
		$content.=$oldcontent;
		$content .= implode('', $this->Events);
		$content .= "END:VCALENDAR";


		return $content;
	}

	public function addEvent($event) {
		$created = date('Y-m-d H:i:s');

		$id = md5(uniqid(mt_rand(), true));

		$this->EventStructure["UID:".($event['uid']??$id)] = "UID:".($event['uid']??$id).CRLF
			. "DTSTAMP:{$this->formatDate($event['start'])}".CRLF
			. "DTSTART:{$this->formatDate($event['start'])}";

		$content = "BEGIN:VEVENT".CRLF
			. "UID:".($event['uid']??$id).CRLF
			. "DTSTAMP:{$this->formatDate($event['start'])}".CRLF
			. "DTSTART:{$this->formatDate($event['start'])}".CRLF
			. "SEQUENCE:0".CRLF
			. "STATUS:CONFIRMED".CRLF
			. "TRANSP:TRANSPARENT".CRLF
			. $this->foldString("SUMMARY:".$this->formatValue($event['summary'])).CRLF
			. $this->foldString("DURATION:{$this->formatValue($event['duration'])}").CRLF
			// . "CREATED:{$this->formatDate($created)}".CRLF
			// . "LAST-MODIFIED:{$this->formatDate($event['start'])}".CRLF
			// . "URL:{$event['url']}".CRLF
			// . "SUMMARY:{$this->formatValue($event['summary'])}".CRLF
			;
		if($event['end']??'') {
			$content.="DTEND:{$this->formatDate($event['end'])}".CRLF;
		}
		if($event['description']??'') {
			$content.=$this->foldString("DESCRIPTION:".$this->formatValue(implode('\n', $event['description']))).CRLF;
		}
		if($event['comment']??'') {
			$content.=$this->foldString("COMMENT:".$this->formatValue(implode('\n', $event['comment']))).CRLF;
		}
		if($event['location']??'') {
			$content.=$this->foldString("LOCATION:".$this->formatValue($event['location'])).CRLF;
		}
		$content.='END:VEVENT'.CRLF;
		$this->Events[] = $content;
	}

	/**
	 * Get the start time set for the even
	 * @return string
	 */
	private function formatDate($date) {
		$return=new DateTime(str_replace(' ', 'T', $date).$this->UtcOffset);
		$return->setTimezone(new DateTimeZone('UTC'));
		return $return->format("Ymd\THis\Z");
	}

	/* Escape commas, semi-colons, backslashes.
	   http://stackoverflow.com/questions/1590368/should-a-colon-character-be-escaped-in-text-values-in-icalendar-rfc2445
	 */
	private function formatValue($str) {
		// return $str;
		return addcslashes($str, ",;");
	}

	private function foldString($str) {
		$pattern = '~.{1,74}~u'; // like "~.{1,76}~u"
		$str = preg_replace($pattern, '$0' . CRLF.' ', $str);
		return rtrim($str);
	}
}