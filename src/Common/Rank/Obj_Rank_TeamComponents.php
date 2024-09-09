<?php
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');

/**
 * Obj_Rank_TeamComponents
 *
 * Per questa classe $opts ha la seguente forma:
 * array(
 * 		events => array(<ev_1>,...,<ev_n>)																[read,non influisce su calculate]
 * 		tournament => #																					[calculate/read]
 * )
 */

class Obj_Rank_TeamComponents extends Obj_Rank {
/**
 * safeFilter()
 * Protegge con gli apici gli elementi di $this->opts['events']
 *
 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
 */
    protected function safeFilter()	{
        $filter='';
        if (array_key_exists('events', $this->opts) and $this->opts['events']) {
            if (is_array($this->opts['events'])) {
                $filter.=" AND EvCode IN(" . implode(',', StrSafe_DB($this->opts['events'])) . ")";
            } elseif (gettype($this->opts['events'])=='string') {
                $filter.=" AND EvCode LIKE " . StrSafe_DB($this->opts['events']);
            }
        }
        if (array_key_exists('noc', $this->opts)) {
            $filter=" AND CoCode = " . StrSafe_DB($this->opts['noc']);
        }
        return $filter;
    }

    public function __construct($opts) {
        parent::__construct($opts);
    }

/**
 * calculate()
 */
    public function calculate() {
        return true;
    }

/**
 * read()
 * @Override
 */
    public function read() {
        $filter = $this->safeFilter();
        if ($filter === false) {
            $filter = "";
        }

        $filter .= (empty($this->opts['coid']) ? '' : " AND CoId=" . intval($this->opts['coid'])) ;

        $q="SELECT TfclCoId, TfclSubTeam, CoCode, CoName, if(CoNameComplete>'', CoNameComplete, CoName) as CoNameComplete,
                EvProgr, TfclEvent, TfclTimeStamp, TfclOrder, TfclEvent, EvEventName, EvMixedTeam, EvOdfCode, EvOdfGender, EvFinalPrintHead as PrintHeader,
                pr.EnId as prId, pr.EnCode as prCode, coalesce(pred.EdExtra, pr.EnCode) as prLocalbib, pr.EnSex as prSex, pr.EnNameOrder as prNameOrder, pr.EnFirstName as prFirstName, upper(pr.EnFirstName) as prFirstNameUpper, pr.EnName as prName, 
                nx.EnId as nxId, nx.EnCode as nxCode, coalesce(nxed.EdExtra, nx.EnCode) as nxLocalbib, nx.EnSex as nxSex, nx.EnNameOrder as nxNameOrder, nx.EnFirstName as nxFirstName, upper(nx.EnFirstName) as nxFirstNameUpper, nx.EnName as nxName,
                ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
                date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
                ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes, ToTimeZone
            FROM Tournament
            INNER JOIN `TeamFinComponentLog` on ToId=TfclTournament
            INNER JOIN Countries on CoId=TfclCoId AND CoTournament=TfclTournament
            INNER JOIN Entries as pr ON TfclIdPrev=pr.EnId
            INNER JOIN Entries as nx ON TfclIdNext=nx.EnId
            INNER JOIN `Events` ON TfclEvent=EvCode AND TfclTournament=EvTournament AND EvTeamEvent=1
            LEFT JOIN DocumentVersions DV1 on TfclTournament=DV1.DvTournament AND DV1.DvFile = 'C-TEAM' and DV1.DvEvent=''
            LEFT JOIN DocumentVersions DV2 on TfclTournament=DV2.DvTournament AND DV2.DvFile = 'C-TEAM' and DV2.DvEvent=TfclEvent
            left join ExtraData pred on pred.EdId=pr.EnId and pred.EdType='Z'
            left join ExtraData nxed on nxed.EdId=nx.EnId and nxed.EdType='Z'
            WHERE ToId={$this->tournament} " . $filter . "
            ORDER BY EvProgr, TfclEvent, CoCode, TfclTimeStamp ASC, TfclOrder";

        $r=safe_r_sql($q);

        $this->data['meta']['title']=get_text('TeamComponentsLog','Tournament');
        $this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
        $this->data['sections']=array();

        $tz = "+00:00";
        $myEv='';
        $myTeam='';

        if(safe_num_rows($r)>0)	{
            $section=null;
            while ($myRow=safe_fetch($r)) {
                $tz = $myRow->ToTimeZone;
                if ($myEv!=$myRow->TfclEvent) {
                    if ($myEv!='') {
                        $this->data['sections'][$myEv]=$section;
                        $section=null;
                    }

                    $myEv=$myRow->TfclEvent;
                    $fields = array(
                        'id' 			=> 'Id',
                        'countryCode' 	=> '',
                        'countryName' 	=> get_text('Country'),
                        'subteam' 		=> get_text('PartialTeam'),
                        'previousAthletes' => get_text('TeamComponentsPrev','Tournament'),
                        'nextAthletes' => get_text('TeamComponentsNext','Tournament'),
                        'athletes' 		=> array(
                            'name' => get_text('Name','Tournament'),
                            'fields'=>array(
                                'id'  => 'Id',
                                'bib' => get_text('Code','Tournament'),
                                'athlete' => get_text('Athlete'),
                                'familyname' => get_text('FamilyName', 'Tournament'),
                                'givenname' => get_text('Name', 'Tournament'),
                                'gender' => get_text('Sex', 'Tournament')
                            )
                        ),
                        'timestamp' => get_text('TeamComponentsTimestamp', 'Tournament')
                    );

                    $section=array(
                        'meta' => array(
                            'event' => $myEv,
                            'odfEvent' => $myRow->EvOdfCode,
                            'odfGender' => $myRow->EvOdfGender,
                            'descr' => get_text($myRow->EvEventName, '', '', true),
                            'printHeader'=>get_text($myRow->PrintHeader, '', '', true),
                            'mixedTeam' => $myRow->EvMixedTeam,
                            'order'=>$myRow->EvProgr,
                            'lastUpdate'=>'0000-00-00 00:00:00',
                            'fields' => $fields,
                            'version' => $myRow->DocVersion,
                            'versionDate' => $myRow->DocVersionDate,
                            'versionNotes' => $myRow->DocNotes,
                            'OrisCode' => 'C56',
                        ),
                        'items'=>array()
                    );
                }



                if ($myTeam!=$myRow->TfclCoId . $myRow->TfclSubTeam . $myRow->TfclEvent . $myRow->TfclTimeStamp) {
                    $item=array(
                        'id' 			=> $myRow->TfclCoId,
                        'countryCode' 	=> $myRow->CoCode,
                        'countryName' 	=> $myRow->CoName,
                        'countryNameLong' 	=> $myRow->CoNameComplete,
                        'subteam' 		=> $myRow->TfclSubTeam,
                        'previousAthletes' => array(),
                        'nextAthletes'  => array(),
                        'timestamp'     => $myRow->TfclTimeStamp
                    );

                    $section['items'][$myRow->TfclCoId.'_'.$myRow->TfclSubTeam.'_'.$myRow->TfclTimeStamp]=$item;

                    if ($myRow->TfclTimeStamp>$this->data['meta']['lastUpdate']) {
                        $this->data['meta']['lastUpdate'] = $myRow->TfclTimeStamp;
                    }
                    $myTeam=$myRow->TfclCoId . $myRow->TfclSubTeam . $myRow->TfclEvent . $myRow->TfclTimeStamp;
                }

                $athlete=array(
                    'id' => $myRow->prId,
                    'bib' => $myRow->prCode,
                    'localbib' => $myRow->prLocalbib,
                    'athlete'=>$myRow->prFirstNameUpper . ' ' . $myRow->prName,
                    'familyname' => $myRow->prFirstName,
                    'familynameUpper' => $myRow->prFirstNameUpper,
                    'givenname' => $myRow->prName,
                    'nameOrder' => $myRow->prNameOrder,
                    'gender' => $myRow->prSex,
                );
                $section['items'][$myRow->TfclCoId.'_'.$myRow->TfclSubTeam.'_'.$myRow->TfclTimeStamp]['previousAthletes'][]=$athlete;
                $athlete=array(
                    'id' => $myRow->nxId,
                    'bib' => $myRow->nxCode,
	                'localbib' => $myRow->nxLocalbib,
                    'athlete'=>$myRow->nxFirstNameUpper . ' ' . $myRow->nxName,
                    'familyname' => $myRow->nxFirstName,
                    'familynameUpper' => $myRow->nxFirstNameUpper,
                    'givenname' => $myRow->nxName,
                    'nameOrder' => $myRow->nxNameOrder,
                    'gender' => $myRow->nxSex,
                );
                $section['items'][$myRow->TfclCoId.'_'.$myRow->TfclSubTeam.'_'.$myRow->TfclTimeStamp]['nextAthletes'][]=$athlete;
            }

        // ultimo giro
            $this->data['sections'][$myEv]=$section;
            $this->data['meta']['lastUpdate'] = (new DateTime($this->data['meta']['lastUpdate']))
                ->modify(0-intval(substr($tz,0, strpos($tz,':')))*60 + intval(substr($tz,strpos($tz,':')+1,2)).' minutes')
                ->format('Y-m-d H:i:s');
        }
    }
}
