<?php
/*
Run Archery events are made of a "qualification", then based on the settings of the events,
a semifinal (eventually) and a final.

All "rounds" are made of Laps, after each lap a series of arrows: missing the target means a penalty "loop".
Any error in arrow number or loops made adds a time penalty

Based on the number of participants, semifinals and finals are as is (Pools are always of 10):
- 1-10 => NO FINALS
- 11-15 => 1 FINAL with top 10
- 16-20 => 2 FINALS: A=1-10, B=11-20
- 21-30 => 2 semifinal pools (serpent mode) + 2 finals (top 3 of each pool + best 4 of the overall for final A)
- 31+ => 3 semifinal pools (serpent mode) + 2 finals (top 2 of each pool + best 4 overall for final A)

 *
 *
 *
 * */

global $CFG;
require_once(dirname(__DIR__) . '/config.php');
$JSON=array(
    'error'=>1,
    'msg'=>get_text('ErrGenericError', 'Errors'),
    );

	// require_once('Common/Lib/CommonLib.php');
	// require_once('Common/Fun_FormatText.inc.php');
	// require_once('Common/Fun_Various.inc.php');
	// require_once('Common/Fun_Sessions.inc.php');

if(!CheckTourSession() or !hasACL(AclQualification, AclReadWrite) or empty($_REQUEST['act'])) {
    JsonOut($JSON);
}

switch($_REQUEST['act']) {
    case 'getEvents':
	    $q=safe_w_SQL("select EvTeamEvent, EvCode, EvEventName 
			from Events where EvTournament={$_SESSION['TourId']} order by EvTeamEvent, EvProgr");
        $JSON['events']=[];
		$OldTeam=-1;
        while($r=safe_fetch($q)) {
			if($OldTeam!=$r->EvTeamEvent) {
		        $JSON['events'][]=['id'=>'','val'=>get_text($r->EvTeamEvent ? 'Team' : 'Individual'),'disabled'=>1];
				$OldTeam=$r->EvTeamEvent;
			}
	        $JSON['events'][]=['id'=>"{$r->EvTeamEvent}-{$r->EvCode}",'val'=>$r->EvEventName];
        }
        $JSON['error']=0;
        break;
    case 'getPhases':
		$Events=$_REQUEST['event']??'';
		if(!$Events) {
			JsonOut($JSON);
		}
		list($Team, $Event)=explode('-', $Events, 2);
		$Team=intval($Team);
	    $q=safe_w_SQL("select EvFinEnds as Laps, EvElim1 as HasSemi, EvElim2 as HasFinals 
			from Events 
			where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
        $JSON['phases']=[];
        $JSON['laps']=[];
        $JSON['phases'][]=['id'=>"0",'val'=>get_text('Q-Session', 'Tournament')];
        if($r=safe_fetch($q)) {
			if($r->HasSemi) {
				$JSON['phases'][]=['id'=>"2",'val'=>get_text('SF_Phase', 'Tournament')];
			}
			if($r->HasFinals) {
				$JSON['phases'][]=['id'=>"1",'val'=>get_text('Finals', 'Tournament')];
			}
			for($i=1;$i<=$r->Laps;$i++) {
				$JSON['laps'][]=['id'=>$i,'val'=>get_text('LapNum', 'RunArchery', $i)];
			}
        }
        $JSON['error']=0;
        break;
	case 'togleIsIn':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		// check Event exists
		$Phase=intval($_REQUEST['phase']??-1);
		$Entry=intval($_REQUEST['id']??0);
		$SubTeam=0;
		$IsIn=intval($_REQUEST['value']??0);
		if(!$Event or !$Entry or $Phase==-1) {
			JsonOut($JSON);
		}
		if($Team=intval($Team)) {
			list($Entry,$SubTeam)=explode('-', $_REQUEST['id']??'-');
			$Entry=intval($Entry);
			$SubTeam=intval($SubTeam);
		}


		$q=safe_r_sql("select EvFinEnds as EvNumLaps from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$EVENT=safe_fetch($q);
		if(!$EVENT) {
			JsonOut($JSON);
		}

		$JSON['StartList']='';
		$JSON['PrintStartlist']=1;
		$JSON['IsIn']=$IsIn;
		$JSON['IsTeam']=$Team;

		if($IsIn) {
			$Result=insertRunParticipant($Team, $Event, $Entry, $EVENT->EvNumLaps, $SubTeam);
			$JSON['StartList']=$Result->StartList;
			$JSON['BibNum']=$Result->RarBib;
			$JSON['msg']='';
		} else {
			safe_w_sql("delete from RunArcheryParticipants where RapTournament={$_SESSION['TourId']} and RapEntry=$Entry and RapSubTeam=$SubTeam and RapEvent='$Event' and RapTeamEvent=$Team");
			safe_w_sql("delete from RunArchery where RaTournament={$_SESSION['TourId']} and RaEntry=$Entry and RaSubTeam=$SubTeam and RaEvent='$Event' and RaTeam=$Team");
			safe_w_sql("delete from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarEvent='$Event' and RarTeam=$Team");
			$JSON['msg']='';
		}

		$JSON['error']=0;
		break;
	case 'setRandom':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		if(!$Event or $Phase==-1) {
			JsonOut($JSON);
		}
		$Team=intval($Team);
		$Start=str_replace('T',' ',$_REQUEST['start']??'');
		$Delay=intval($_REQUEST['delay']??0);
        $Type=intval($_REQUEST['type']??0);
        $Group=intval($_REQUEST['group']??0);

		$StartTime=strtotime($Start);
		// people is already in so just run the timing part
		$q=safe_r_sql("select RarEntry, RarSubTeam, RarGroup
			from RunArcheryRank
			where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase
			order by if(RarPhase=0, RarGroup,RarPool), rand()");
		$Bib=0;
		$Grp=0;
		// spread entries more "equaly" between groups
		$MaxGroupNums=ceil(safe_num_rows($q)/$Group);
		$Group=$Phase ? 10 : ceil(safe_num_rows($q)/$MaxGroupNums);
        $RarGroup=0;
		while($r=safe_fetch($q)) {
			$Bib++;
            if($Type) {
                // single delayed adjustments
                if($RarGroup!=$r->RarGroup) {
                    if($r->RarGroup%2==0) {
                        $StartTime+=(180-$Delay); // 3 minutes added delay for groups 2, 4, 5
                    } elseif ($r->RarGroup>1) {
                        $StartTime+=(1500-$Delay); // 25 minutes added delay for groups 3, 5, 7...
                    }
                }
            }
            $RarGroup=$r->RarGroup;
            $Start=date('Y-m-d H:i:s', $StartTime);
			safe_w_sql("update RunArcheryRank set RarStartlist='$Start', RarDateTimeStart=unix_timestamp('$Start')
                where RarEntry=$r->RarEntry 
                    and RarSubTeam=$r->RarSubTeam 
                    and RarTournament={$_SESSION['TourId']}
                    and RarTeam=$Team
                    and RarEvent=".StrSafe_DB($Event)."
                    and RarPhase=$Phase");
			if($Type) {
				// single delayed starts
                $StartTime+=$Delay;
			} elseif($Bib==$Group) {
				$Grp++;
				$Bib=0;
				$StartTime+=$Delay;
			}
		}

		// NO BREAK AS PROCEDURE CONTINUES SENDING THE NEW ORDER!
	case 'getDraw':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		if(!$Event or $Phase==-1) {
			JsonOut($JSON);
		}
		$Team=intval($Team);

		$q=safe_r_SQL("select EvFinEnds as NumLaps, EvMixedTeam, EvMaxTeamPerson, EvElimType, EvTeamCreationMode, EvMultiTeam, EvMultiTeamNo, EvElim1, ToMaxDistScore, SesAth4Target 
			from Events 
            inner join Tournament on ToId=EvTournament 
            inner join Session on SesTournament=EvTournament and SesType='Q' and SesOrder=1
			where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$EVENT=safe_fetch($q);
		if(!$EVENT) {
			JsonOut($JSON);
		}

		$JSON['Delay']=($EVENT->EvElimType ? $EVENT->ToMaxDistScore : 300);
		$JSON['Type']=(int) $EVENT->EvElimType;
		$JSON['Group']=$EVENT->SesAth4Target;
		$JSON['Team']=($Team==1);
		$JSON['TeamNum']=$EVENT->EvMaxTeamPerson;
		$JSON['Laps']=(int) $EVENT->NumLaps;
		$JSON['Phase']=(int) $Phase;

		// undecided...
		if($Phase>0) {
			$JSON['Type']=0; // only qualifications have delayed on single
			$JSON['Delay']=300;
		} elseif($EVENT->EvElimType==1) {
			// single archer
			$JSON['Group']=1;
		}

		$Rows=[];
		if($Team) {
			// ALL TEAMS ARE PRE-DECLARED BEFOREHAND
			// so first thing to do is create "whole" empty multiple teams in order to insert people in
			// calculate how many subteams are available
			if($EVENT->EvMixedTeam) {
				$NumPersonsPerGender=(int) round($EVENT->EvMaxTeamPerson/2);

				$q=safe_r_sql("select EnSex, CoId, floor(count(*)/$NumPersonsPerGender) as MaxTeams
					from Entries
				    inner join EventClass on EcTournament=EnTournament and EcTeamEvent>0 and EnDivision=EcDivision and EnClass=EcClass and if(EcSubClass!='', EnSubClass=EcSubClass, true) and EnTeamMixEvent=1
					inner join Events on EvCode=EcCode and EvTeamEvent=1 and EvTournament=EcTournament and EvCode=".StrSafe_DB($Event)."
				    inner join Countries on CoTournament=EnTournament and CoId=IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2)))
					where EnTournament={$_SESSION['TourId']}
					group by EnSex, CoId
					having MaxTeams>0");
				$Countries=[];
				while($r=safe_fetch($q)) {
					$Countries[$r->CoId][$r->EnSex]=(int) $r->MaxTeams;
				}
				$AllowedCountries=[];
				foreach($Countries as $CoId => $Genders) {
					if(count($Genders)==2) {
						// anyway, the number of maximum teams to be created depends on how the definition of the tema is!
						$AllowedCountries[$CoId]=min($Genders[0], $Genders[1], (int) ($EVENT->EvMultiTeam ? ($EVENT->EvMultiTeamNo ?: 9999) : 1));
					}
				}
			} else {
				$q=safe_r_sql("select CoId, floor(count(*)/{$EVENT->EvMaxTeamPerson}) as MaxTeams
					from Entries
				    inner join EventClass on EcTournament=EnTournament and EnDivision=EcDivision and EnClass=EcClass and if(EcSubClass!='', EnSubClass=EcSubClass, true) and EnTeamFEvent=1
					inner join Events on EvCode=EcCode and EvTeamEvent=EcTeamEvent and EvTournament=EcTournament and EvCode=".StrSafe_DB($Event)."
				    inner join Countries on CoTournament=EnTournament and CoId=IF(EvTeamCreationMode=3, EnCountry3, if(EvTeamCreationMode=2, EnCountry2, if((EvTeamCreationMode=0 and EnCountry2=0) or EvTeamCreationMode=1, EnCountry, EnCountry2)))
					where EnTournament={$_SESSION['TourId']}
					group by CoId
					having MaxTeams>0");
				$AllowedCountries=[];
				while($r=safe_fetch($q)) {
					$AllowedCountries[$r->CoId]=(int) min((int) $r->MaxTeams, (int) ($EVENT->EvMultiTeam ? ($EVENT->EvMultiTeamNo ?: 9999) : 1));
				}
			}
			// check we have the correct number of subteams for each country
			$q=safe_r_sql("select CoId, max(coalesce(TeSubTeam,-1)) as MaxSubTeam, coalesce(TeCoId,'') as HasTeam
				from Countries
			    left join Teams on TeCoId=CoId and TeTournament=CoTournament and TeFinEvent=1 and TeEvent=".StrSafe_DB($Event)."
				where CoTournament={$_SESSION['TourId']} and CoId in (".implode(',',array_keys($AllowedCountries)).")
				group by CoId");
			while($r=safe_fetch($q)) {
				if(empty($AllowedCountries[$r->CoId])) {
					// need to remove team and components
					safe_w_sql("delete from Teams 
       					where TeFinEvent=1 and TeTournament={$_SESSION['TourId']} and TeEvent=".StrSafe_DB($Event)." and TeCoId={$r->CoId}");
					safe_w_sql("delete from TeamFinComponent 
       					where TfcTournament={$_SESSION['TourId']} and TfcEvent=".StrSafe_DB($Event)." and TfcCoId={$r->CoId}");
					continue; // jump to next item
				}
				if($r->MaxSubTeam>=$AllowedCountries[$r->CoId]) {
					// too many subteams, remove the last ones!
					safe_w_sql("delete from Teams 
       					where TeFinEvent=1 and TeSubTeam>={$AllowedCountries[$r->CoId]} and TeTournament={$_SESSION['TourId']} and TeEvent=".StrSafe_DB($Event)." and TeCoId={$r->CoId}");
					safe_w_sql("delete from TeamFinComponent 
       					where TfcSubTeam>={$AllowedCountries[$r->CoId]} and TfcTournament={$_SESSION['TourId']} and TfcEvent=".StrSafe_DB($Event)." and TfcCoId={$r->CoId}");
					continue; // jump to next item
				}
				if($r->MaxSubTeam<($AllowedCountries[$r->CoId]-1)) {
					// creates the missing subteams
					for($i=$r->MaxSubTeam+1; $i<$AllowedCountries[$r->CoId]; $i++) {
						safe_w_sql("insert into Teams set 
                      		TeCoId={$r->CoId},
							TeSubTeam=$i,
							TeEvent=".StrSafe_DB($Event).",
							TeTournament={$_SESSION['TourId']},
							TeFinEvent=1");
						for($j=1;$j<=$EVENT->EvMaxTeamPerson;$j++) {
							safe_w_sql("insert into TeamFinComponent set
                                TfcCoId={$r->CoId},
								TfcSubTeam=$i,
								TfcTournament={$_SESSION['TourId']},
								TfcEvent=".StrSafe_DB($Event).",
								TfcId=".(4294967296-$j).",
								TfcOrder=$j");
						}
					}
				}
			}

			// CHECK IS FINISHED! Get the real thing!
			// select all eligible people from the countries
			switch($EVENT->EvTeamCreationMode) {
				case '1':
					$CountrySelected='EnCountry';
					break;
				case '2':
					$CountrySelected='EnCountry2';
					break;
				case '3':
					$CountrySelected='EnCountry3';
					break;
				default:
					$CountrySelected='if(EnCountry2=0, EnCountry, EnCountry2)';
			}
			$JSON['TeamMembers']=[];
			$JSON['Components']=[];
			$JSON['Selected']=[];
			$q=safe_r_SQL("Select EnId as value, concat(ucase(EnFirstName), ' ', EnName, ' (', if(EnSex=0, 'M', 'W'), ')') as name, $CountrySelected as Team, coalesce(TfcOrder, '') as IsIn, coalesce(TfcSubTeam,0) as SubTeam, coalesce(TfcOrder,0) as LapOrder
				from Entries 
			    inner join EventClass on EcTournament=EnTournament and EcDivision=EnDivision and EcClass=EnClass and if(EcSubClass!='', EnSubClass=EcSubClass, true) and EcTeamEvent>0
				inner join Events on EvCode=EcCode and EvTeamEvent=1 and EvTournament=EcTournament and EvCode=".StrSafe_DB($Event)."
				inner join Countries on CoId=$CountrySelected and CoTournament=EnTournament
				left join TeamFinComponent on TfcTournament=EnTournament and TfcCoId=$CountrySelected and TfcEvent=EcCode and TfcId=EnId
                where EnTournament={$_SESSION['TourId']}
                	and ".($EVENT->EvMixedTeam ? 'EnTeamMixEvent=1' : 'EnTeamFEvent=1')."
                	and $CountrySelected in (".implode(',', array_keys($AllowedCountries)).")
                order by CoCode, SubTeam, EnSex desc, LapOrder");
			while($r=safe_fetch($q)) {
				$tmp=['value'=>$r->value, 'name'=>$r->name];
				if(empty($JSON['Components'][$r->Team]) or !in_array($tmp, $JSON['Components'][$r->Team])) {
					$JSON['Components'][$r->Team][]=$tmp;
				}
				if($r->IsIn) {
					$JSON['TeamMembers'][$r->Team.'-'.$r->SubTeam]['order-'.$r->IsIn]=$r->value;
				}
			}

			// first select the teams that can participate
			$Rows=[];
			$Countries=[];
			$SQL="select concat(TeCoId,'-',TeSubTeam) as EnId, coalesce(RarBib, '') as RarBib, CoCode, concat(CoCode, if(TeSubTeam>0, TeSubTeam+1,'')) as CoCodeSubteam, CoName, CoId, TeSubTeam as SubTeam,
       			coalesce(RapEntry, 0)>0 as IsIn, group_concat(concat_ws('-',RaLap, RaArcher) separator '|') as LapMembers, RarGroup, RarTarget,
       			coalesce(date_format(RarStartlist, '%Y-%m-%dT%H:%i:%s'),'') as StartList, coalesce(concat(RarStartlist, RarBib), concat(CoCode,CoName,TeSubTeam)) as BasicOrder
			from Teams
			inner join Countries on CoId=TeCoId and CoTournament=TeTournament
         	left join RunArcheryParticipants on RapTournament=TeTournament and RapEntry=TeCoId and RapTeamEvent=1 and RapEvent=TeEvent and RapSubTeam=TeSubTeam
         	left join RunArcheryRank on RarTournament=TeTournament and RarEntry=TeCoId and RarSubTeam=TeSubTeam and RarTeam=1 and RarEvent=TeEvent and RarPhase=$Phase
			left join RunArchery on RaTournament=RarTournament and RaEntry=RarEntry and RaSubTeam=RarSubTeam and RaTeam=RarTeam and RaEvent=RarEvent and RaPhase=RarPhase
			where TeTournament={$_SESSION['TourId']} and TeFinEvent=1 and TeEvent=".StrSafe_DB($Event)."
			group by TeCoId, TeSubTeam, TeEvent, RarPhase
			order by RapEntry is null, RarStartList=0, RarStartList, RarBib+0, RarBib";
			$q=safe_r_sql($SQL);
			while($r=safe_fetch($q)) {
				if($r->LapMembers) {
					$tmp=[];
					foreach(explode('|', $r->LapMembers) as $a) {
						$a=explode('-', $a);
						$tmp['lap'.$a[0]]=$a[1];
					};
					$r->LapMembers=$tmp;
				} else {
					$r->LapMembers=[];
				}
				$Rows[]=$r;
			}

			$JSON['headers']=[
				'StartList'=>get_text('StartTime', 'RunArchery'),
				'IsIn'=>get_text('EntryIsIn', 'RunArchery'),
				'SelectAll'=>get_text('SelectAll'),
				'Bib'=>get_text('BibNumber', 'BackNumbers'),
				'TgtGrp'=>get_text('Group', 'Tournament'),
				'RarTarget'=>get_text('Target'),
				'Country'=>get_text('Country'),
				];
			for($i=1;$i<=$EVENT->NumLaps;$i++) {
				$JSON['headers']['Lap'.$i]=get_text('LapNum', 'RunArchery', $i);
			}

		} else {
			$JSON['headers']=[
				'EnBib'=>get_text('BibNumber', 'BackNumbers'),
				'TgtGrp'=>get_text('Group', 'Tournament'),
				'RarTarget'=>get_text('Target'),
				'EnCode'=>get_text('WaId', 'Tournament'),
				'EnFirstName'=>get_text('FamilyName','Tournament'),
				'EnName'=>get_text('GivenName','Tournament'),
				'EnSex'=>get_text('Sex','Tournament'),
				'CoCode'=>get_text('CountryCode'),
				'CoName'=>get_text('Nation'),
				'StartList'=>get_text('StartTime', 'RunArchery'),
				'IsIn'=>$Phase ? 'Selection' : get_text('EntryIsIn', 'RunArchery'),
				'SelectAll'=>get_text('SelectAll'),
				'Pool'=>get_text('MenuLM_Pools'),
			];
			if($Phase) {
				// check the number of participants for this event is compatible with the phase requested
				$SQL="select RarEntry as EnId, RarBib, coalesce(EnCode, '') as EnCode, coalesce(EnFirstName, '') as EnFirstName, coalesce(EnName, '') as EnName, coalesce(EnSex, '') as EnSex,
	                coalesce(CoCode, '') as CoCode, coalesce(CoName, '') as CoName, 1 as IsIn, RarGroup, RarTarget,
	                date_format(RarStartlist, '%Y-%m-%dT%H:%i:%s') as StartList, concat(RarStartlist, RarBib) as BasicOrder,
	                RarFromRank, RarFromType, RarPool
				from RunArcheryRank
			    inner join Events on EvCode=RarEvent and EvTournament=RarTournament and EvTeamEvent=0
				left join (
				    select EnId, EnTournament, EnCode, EnFirstName, EnName, EnSex, CoCode, CoName
				    from Entries
					inner join Countries on CoId=EnCountry and CoTournament=EnTournament
		            left join ExtraData on EdId=EnId and EdType='Z'
				    where EnTournament={$_SESSION['TourId']}
				) Entries on EnId=RarEntry and EnTournament=RarTournament
				where RarTournament={$_SESSION['TourId']} and RarTeam=0 and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase
				order by RarStartList=0, RarStartList, RarBib+0, RarBib, RarPool, RarFromType=0, RarFromType, RarFromRank";
			} else {
				$SQL="select EnId, coalesce(RarBib, EdExtra, EnCode) as RarBib, EnCode, EnFirstName, EnName, EnSex,
	                CoCode, CoName, coalesce(RapEntry, 0)>0 as IsIn, RarGroup, RarTarget,
	                coalesce(date_format(RarStartlist, '%Y-%m-%dT%H:%i:%s'),'') as StartList, coalesce(concat(RarStartlist, RarBib), concat(CoCOde,EnFirstName, EnName)) as BasicOrder,
	                RarFromRank, RarFromType, RarPool
				from Entries
				inner join Countries on CoId=EnCountry and CoTournament=EnTournament
	            inner join EventClass on EcTournament=EnTournament and EcDivision=EnDivision and EcClass=EnClass
	            inner join Events on EvTournament=EcTournament and EvCode=EcCode and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event)." and EvTeamEvent=(EcTeamEvent>0)
	            left join RunArcheryParticipants on RapTournament=EnTournament and RapEntry=EnId and RapTeamEvent=EvTeamEvent and RapEvent=EvCode
	            left join RunArcheryRank on RarTournament=EnTournament and RarEntry=EnId and RarSubTeam=0 and RarTeam=EvTeamEvent and RarEvent=EvCode and RarPhase=$Phase
	            left join ExtraData on EdId=EnId and EdType='Z'
				where EnTournament={$_SESSION['TourId']} and EnIndFEvent=1
				order by RapEntry is null, RarStartList=0, RarStartList, RarBib+0, RarBib";
			}
			$q=safe_r_sql($SQL);
			while($r=safe_fetch($q)) {
				// if no semifinals, always come straight from qualification
				$r->From="QUAL {$r->RarFromRank}";
				$r->Pool=get_text('SemiFinalName', 'RunArchery', $r->RarPool);
				if($Phase==1) {
					$r->Pool=get_text('Final'.$r->RarPool, 'RunArchery');
					// finals, check if we have semi, as "0" in fromtype means overall rank of semi
					if($EVENT->EvElim1) {
						if($r->RarFromType) {
							$r->From="Group {$r->RarFromType} $r->RarFromRank";
						} else {
							$r->From="SF Rank ".$r->RarFromRank;
						}
					}
				}
				$Rows[]=$r;
			}
		}
		$q=safe_r_sql($SQL);
		$JSON['start']='';
		$JSON['rows']=[];
		foreach($Rows as $r) {
			$r->RarGroup=($r->RarGroup?:'');
			$r->RarTarget=($r->RarTarget?:'');
			$JSON['rows'][]=$r;
			if($r->StartList and substr($r->StartList,0,4)!='0000') {
				if(!$JSON['start'] or $r->StartList<$JSON['start']) {
					$JSON['start']=$r->StartList;
				}
			}
		}
		$JSON['error']=0;
		break;
	case 'changeBib':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Entry=intval($_REQUEST['id']??0);
		if(!$Event or $Phase==-1 or empty($_REQUEST['value']) or !$Entry) {
			JsonOut($JSON);
		}
		$SubTeam=0;
		if($Team=intval($Team)) {
			list($Entry,$SubTeam)=explode('-', $_REQUEST['id']);
		}

		$q=safe_r_SQL("select EvMaxTeamPerson, EvElimType, ToMaxDistScore, SesAth4Target 
			from Events 
            inner join Tournament on ToId=EvTournament 
            inner join Session on SesTournament=EvTournament and SesType='Q' and SesOrder=1
			where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$EVENT=safe_fetch($q);
		if(!$EVENT) {
			JsonOut($JSON);
		}
		$BibNum=trim($_REQUEST['value']);
		safe_w_sql("update RunArcheryRank set RarBib=".StrSafe_DB($BibNum).", RarLastUpdate=now()
			where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
		$JSON['reds']=searchReds($Team, $Event, $BibNum);
		$JSON['error']=0;
		break;
	case 'BatchIsIn':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Entries=$_REQUEST['ids']??[];

		if(!$Event or !$Entries or $Phase==-1 or !is_array($Entries)) {
			JsonOut($JSON);
		}
		$Team=intval($Team);
		$SubTeam=0;

		$q=safe_r_sql("select EvFinEnds as EvNumLaps from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$EVENT=safe_fetch($q);
		if(!$EVENT) {
			JsonOut($JSON);
		}

		$JSON['PrintStartlist']=1;
		$JSON['IsIn']=1;
		$JSON['IsTeam']=$Team;
		$JSON['msg']='';
		$JSON['error']=0;
		$JSON['rows']=[];

		if($Team) {
			foreach($Entries as $Entry) {
				list($Entry,$SubTeam)=explode('-', $Entry);
				$Entry=intval($Entry);
				$SubTeam=intval($SubTeam);
				$Result=insertRunParticipant($Team, $Event, $Entry, $EVENT->EvNumLaps, $SubTeam);
				$JSON['rows'][]=['id'=>$Entry.'-'.$SubTeam, 'StartList'=>$Result->StartList, 'BibNum'=>$Result->RarBib];
			}
		} else {
			foreach($Entries as $Entry) {
				if($Entry=intval($Entry)) {
					$Result=insertRunParticipant($Team, $Event, $Entry, $EVENT->EvNumLaps, 0);
					$JSON['rows'][]=['id'=>$Entry, 'StartList'=>$Result->StartList, 'BibNum'=>$Result->RarBib];
				}
			}
		}
		break;
	case 'getDrawDetails':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		if(!$Event or $Phase==-1) {
			JsonOut($JSON);
		}
		$Team=intval($Team);
		$DrawType=intval($_REQUEST['drawType']??0);

		$JSON['Delay']='';
		$JSON['Group']='1';

		$q=safe_r_SQL("select EvELimType, ToMaxDistScore, SesAth4Target
                from Events 
                inner join Tournament on ToId=EvTournament 
                inner join Session on SesTournament=EvTournament and SesType='Q' and SesOrder=1
                where EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$r=safe_fetch($q);
		$JSON['Type']=$r->EvELimType;
		$JSON['Delay']=(($r->EvELimType and $Phase==0) ? $r->ToMaxDistScore : 300);
		$JSON['Group']=1;

		// undecided...
		if($Phase>0) {
			$JSON['Type']=0; // only qualifications have delayed on single
			$JSON['Delay']=300;
		} else {
			if($r->EvELimType==0) {
				$JSON['Group']=$r->SesAth4Target;
			}
		}

		break;
	case 'setStartList':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Entry=intval($_REQUEST['id']??0);
		$Value=($_REQUEST['value']??'');
		if(!$Event or !$Entry or $Phase==-1) {
			JsonOut($JSON);
		}
		$SubTeam=0;
		if($Team=intval($Team)) {
			list($Entry, $SubTeam)=explode('-', $_REQUEST['id']);
		}
		$Start=date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $Value)));

		safe_w_sql("update RunArcheryRank
			set RarStartlist='$Start', RarDateTimeStart=unix_timestamp('$Start'), RarDateTimeStart=unix_timestamp('$Start')
			where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarTeam=$Team and RarEvent='$Event' and RarPhase=$Phase and RarSubTeam=$SubTeam");

		$JSON['error']=0;
		$JSON['redTimes']=searchTimes($Start);
		break;
	case 'changeGrp':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Entry=intval($_REQUEST['id']??0);
		$TgtGrp=intval($_REQUEST['value']??0);
		if(!$Event or $Phase==-1 or !$Entry) {
			JsonOut($JSON);
		}
		$SubTeam=0;
		if($Team=intval($Team)) {
			list($Entry,$SubTeam)=explode('-', $_REQUEST['id']);
		}

		$q=safe_r_SQL("select EvMaxTeamPerson, EvElimType, ToMaxDistScore, SesAth4Target 
			from Events 
            inner join Tournament on ToId=EvTournament 
            inner join Session on SesTournament=EvTournament and SesType='Q' and SesOrder=1
			where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$EVENT=safe_fetch($q);
		if(!$EVENT) {
			JsonOut($JSON);
		}
		safe_w_sql("update RunArcheryRank set RarGroup=$TgtGrp
			where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
		$JSON['redTargets']=searchRedTargets($Team, $Event);
		$JSON['error']=0;
		break;
	case 'changeTarget':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		$Phase=intval($_REQUEST['phase']??-1);
		$Entry=intval($_REQUEST['id']??0);
		$TgtGrp=intval($_REQUEST['value']??0);
		if(!$Event or $Phase==-1 or !$Entry) {
			JsonOut($JSON);
		}
		$SubTeam=0;
		if($Team=intval($Team)) {
			list($Entry,$SubTeam)=explode('-', $_REQUEST['id']);
		}

		$q=safe_r_SQL("select EvMaxTeamPerson, EvElimType, ToMaxDistScore, SesAth4Target 
			from Events 
            inner join Tournament on ToId=EvTournament 
            inner join Session on SesTournament=EvTournament and SesType='Q' and SesOrder=1
			where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		$EVENT=safe_fetch($q);
		if(!$EVENT) {
			JsonOut($JSON);
		}
		safe_w_sql("update RunArcheryRank set RarTarget=$TgtGrp
			where RarTournament={$_SESSION['TourId']} and RarEntry=$Entry and RarSubTeam=$SubTeam and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarPhase=$Phase");
		$JSON['redTargets']=searchRedTargets($Team, $Event);
		$JSON['error']=0;
		break;
	case 'checkReds':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);

		$JSON['reds']=searchReds($Team, $Event);
		$JSON['redTargets']=searchRedTargets($Team, $Event);
		$JSON['redComponents']=searchRedComps($Event);
		$JSON['redTimes']=searchTimes();
		$JSON['error']=0;
		break;
	case 'changeTeam':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		list($Country, $SubTeam)= explode('-', $_REQUEST['id']??'-', 2);
		$Team=intval($Team);
		$Country=intval($Country);
		$SubTeam=intval($SubTeam);
		$Order=intval($_REQUEST['order']??1);
		$Entry=intval($_REQUEST['value']??0);
		$Phase=intval($_REQUEST['phase']??0);

		$q=safe_w_sql("update ignore TeamFinComponent set TfcId=".($Entry?:4294967296-$Order)."
			where TfcCoId=$Country and TfcSubTeam=$SubTeam and TfcTournament={$_SESSION['TourId']} and TfcEvent=".StrSafe_DB($Event)." and TfcOrder=$Order");
		if(safe_w_affected_rows()) {
			// also changes the team member in the corresponding laps!

			safe_w_sql("update RunArchery 
    			inner join Events on EvCode=RaEvent and EvTournament=RaTournament and EvTeamEvent=RaTeam 
				set RaArcher=$Entry
				where RaTournament={$_SESSION['TourId']}
				  	and RaEntry=$Country 
				  	and  RaSubTeam=$SubTeam
					and RaTeam=$Team
					and RaEvent=".StrSafe_DB($Event)."
					and RaPhase=$Phase
				    and if(EvMixedTeam=1, RaLap in ($Order, ".($Order+2)."), if(EvFinEnds>EvMaxTeamPerson, RaLap in (".(($Order*2)-1).",".($Order*2)."), RaLap=$Order))");
		}
		$JSON['error']=(safe_w_affected_rows()==0);
		$JSON['redComponents']=searchRedComps($Event);
		$JSON['lapComponents']=[];
		$q=safe_r_sql("select concat('.LapComponents.Lap', RaLap) as lapKey, RaArcher as lapValue from RunArchery where RaTournament={$_SESSION['TourId']}
		    and RaEntry=$Country 
		    and  RaSubTeam=$SubTeam
			and RaTeam=$Team
			and RaEvent=".StrSafe_DB($Event)."
			and RaPhase=$Phase");
		while($r=safe_fetch($q)) {
			$JSON['lapComponents'][$r->lapKey]=($r->lapValue?:'');
		}
		break;
	case 'changeLap':
		list($Team, $Event)=explode('-', $_REQUEST['event']??'-', 2);
		list($Country, $SubTeam)= explode('-', $_REQUEST['id']??'-', 2);
		$Team=intval($Team);
		$Country=intval($Country);
		$SubTeam=intval($SubTeam);
		$Lap=intval($_REQUEST['lap']??1);
		$Entry=intval($_REQUEST['value']??0);
		$Phase=intval($_REQUEST['phase']??-1);

		// check if the team is complete
		$q=safe_w_sql("update ignore RunArchery set RaArcher=".($Entry?:0)."
			where RaTournament={$_SESSION['TourId']} 
				and RaEntry=$Country 
				and RaSubTeam=$SubTeam 
				and RaTeam=$Team 
				and RaEvent=".StrSafe_DB($Event)." 
				and RaPhase=$Phase
				and RaLap=$Lap");
		$JSON['error']=(safe_w_affected_rows()==0);
		$JSON['redComponents']=searchRedComps($Event);
		break;
}

JsonOut($JSON);

function insertRunParticipant($Team, $Event, $Entry, $NumLaps, $SubTeam=0) {
	if($Team) {
		safe_w_sql("insert ignore into RunArcheryParticipants 
			set RapParticipates=1, RapTournament={$_SESSION['TourId']}, RapEntry=$Entry, RapSubTeam=$SubTeam, RapEvent='$Event', RapTeamEvent=$Team");
		// get maxbib for teams
		$q=safe_r_sql("select max(RarBib+0)+1 as MaxBib from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarTeam=1");
		$r=safe_fetch($q);

		safe_w_sql("insert ignore into RunArcheryRank 
            (RarTournament, RarEntry, RarSubTeam, RarTeam, RarEvent, RarPhase, RarBib, RarLastUpdate) 
			select {$_SESSION['TourId']}, $Entry, $SubTeam, 1, '$Event', 0, ".($r->MaxBib??1).", now()
			from Countries
			where CoId=$Entry and CoTournament={$_SESSION['TourId']}");
		for($i=1;$i<=$NumLaps; $i++) {
			safe_w_sql("insert ignore into RunArchery 
				set RaTournament={$_SESSION['TourId']}, 
					RaEntry=$Entry,
					RaSubTeam=$SubTeam,
					RaTeam=1,
					RaEvent='$Event',
					RaPhase=0,
					RaLap=$i,
					RaLastUpdate=now()");
		}

		// get the startlist of the TEAM
		$SQL="select coalesce(date_format(RarStartlist, '%Y-%m-%dT%H:%i:%s'),'') as StartList, RarBib
			from RunArcheryRank 
			where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarEvent=".StrSafe_DB($Event)." and RarEntry=$Entry and RarPhase=0 and RarSubTeam=$SubTeam ";
	} else {
		safe_w_sql("insert ignore into RunArcheryParticipants set RapParticipates=1, RapTournament={$_SESSION['TourId']}, RapEntry=$Entry, RapEvent='$Event', RapTeamEvent=$Team");
		safe_w_sql("insert ignore into RunArcheryRank 
	        (RarTournament, RarEntry, RarSubTeam, RarTeam, RarEvent, RarPhase, RarBib, RarLastUpdate) 
			select {$_SESSION['TourId']}, $Entry, 0, 0, '$Event', 0, coalesce(EdExtra,EnCode), now()
			from Entries
			left join ExtraData on EdId=EnId and EdType='Z'
			where EnId=$Entry and EnTournament={$_SESSION['TourId']}");
		for($i=1;$i<=$NumLaps; $i++) {
			safe_w_sql("insert ignore into RunArchery 
				set RaTournament={$_SESSION['TourId']}, 
					RaEntry=$Entry,
					RaSubTeam=0,
					RaTeam=0,
					RaEvent='$Event',
					RaPhase=0,
					RaLap=$i,
					RaArcher=$Entry,
					RaLastUpdate=now()");
		}
		$SQL="select coalesce(date_format(RarStartlist, '%Y-%m-%dT%H:%i:%s'),'') as StartList, RarBib
			from Entries
            inner join EventClass on EcTournament=EnTournament and EcDivision=EnDivision and EcClass=EnClass
            inner join Events on EvTournament=EcTournament and EvCode=EcCode and EvTeamEvent=(EcTeamEvent>0)
            inner join RunArcheryParticipants on RapTournament=EnTournament and RapEntry=EnId and RapTeamEvent=EvTeamEvent and RapEvent=EvCode
            left join RunArcheryRank on RarTournament=EnTournament and RarEntry=EnId and RarSubTeam=0 and RarTeam=EvTeamEvent and RarEvent=EvCode and RarPhase=0
			where EnTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event)." and EnId=$Entry
			";
	}
	$q=safe_r_sql($SQL);
	if($r=safe_fetch($q)) {
		return $r;
	}
	return '';
}

/**
 * @param $Event Event to search for duplicate teamcomponents
 * @return array of duplicate EnIds (part of more teams on the same event)
 */
function searchRedComps($Event) {
	$ret=[];
	$q=safe_r_sql("select count(*) as TotalNum, TfcId as id
		from TeamFinComponent
		where TfcTournament={$_SESSION['TourId']} and TfcEvent=".StrSafe_DB($Event)."
		group by TfcId
		having TotalNum>1");
	while($r=safe_fetch($q)) {
		$ret[]=$r->id;
	}
	return $ret;
}

function searchReds($Team, $Event, $BibNum='') {
	$ret=[];
	$q=safe_r_sql("select count(*) as TotalNum, group_concat(".($Team ? "concat_ws('-', RarEvent, RarEntry, RarSubTeam)" : 'RarEntry')." separator '|') as id
		from RunArcheryRank
		where RarTournament={$_SESSION['TourId']} and RarTeam=$Team and RarPhase=0 ".($BibNum ? "and RarBib=".StrSafe_DB($BibNum) : '')."
		group by RarBib
		having TotalNum>1");
	while($r=safe_fetch($q)) {
		$tmp=[];
		foreach(explode('|', $r->id) as $a) {
			$b=explode('-', $a, 2);
			if($b[0]==$Event) {
				$ret[]=$b[1];
			}
		}
	}
	return array_unique($ret);
}

function searchTimes($Time='') {
	$ret=[];
	$q=safe_r_sql("select SesAth4Target from Session where SesTournament={$_SESSION['TourId']} and SesOrder=1 and SesType='Q'");
	$r=safe_fetch($q);
	$MaxGroup=$r->SesAth4Target;
	$q=safe_r_sql("select date_format(RarStartlist, '%Y-%m-%dT%H:%i:%s') as StartList, count(*) as TotalNum
		from RunArcheryRank
		where RarTournament={$_SESSION['TourId']} and RarStartlist>0 
		group by RarStartlist
		having TotalNum>$MaxGroup");
	while($r=safe_fetch($q)) {
		$ret[]=$r->StartList;
		if(substr($r->StartList,-3)==':00') {
			$ret[]=substr($r->StartList,0,-3);
		}
	}
	return $ret;
}

/**
 * This function checks when group/target are set that there are less than 2 (group start) or SesAth4Target (target availability in single start)
 * @param $Team
 * @param $Event
 * @return array
 */
function searchRedTargets($Team, $Event) {
	$ret=[];
	$Concat=($Team ? "concat_ws('-', RarEvent, RarEntry, RarSubTeam)" : "concat_ws('-', RarEvent, RarEntry)");
	$SQL=[];
	// Group starting, each has a single target to shoot on!
	// group starting is for phase > 0 or EvElimType=0
	$SQL[]="select count(*) as TotalNum, SesAth4Target, group_concat($Concat separator '|') as id
		from RunArcheryRank
		inner join Events on EvTournament=RarTournament and EvCode=RarEvent and EvTeamEvent=RarTeam
		inner join Session on SesTournament=RarTournament and SesType='Q' and SesOrder=1
		where RarTournament={$_SESSION['TourId']} and RarTarget>0 and (EvElimType=0 or RarPhase>0)
		group by RarStartlist, RarTarget
		having TotalNum>1";
	// Individual start, the number of people in the same group must not be more than the number of targets available
	$SQL[]="select count(*) as TotalNum, SesAth4Target, group_concat($Concat separator '|') as id
		from RunArcheryRank
		inner join Events on EvTournament=RarTournament and EvCode=RarEvent and EvTeamEvent=RarTeam
		inner join Session on SesTournament=RarTournament and SesType='Q' and SesOrder=1
		where RarTournament={$_SESSION['TourId']} and RarGroup>0 and EvElimType=1 and RarPhase=0
		group by RarGroup
		having TotalNum>SesAth4Target";
	$q=safe_r_sql("(" . implode(') UNION (', $SQL) . ")");
	while($r=safe_fetch($q)) {
		$tmp=[];
		foreach(explode('|', $r->id) as $a) {
			$b=explode('-', $a, 2);
			if($b[0]==$Event) {
				$ret[]=$b[1];
			}
		}
	}
	return $ret;
}

