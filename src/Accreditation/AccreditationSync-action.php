<?php
require_once(dirname(__DIR__) . '/config.php');
$JSON=[
    'error'=>1,
    'msg'=>get_text('GenericError', 'Errors'),
];

if(!CheckTourSession() or !hasACL(AclAccreditation, AclReadWrite)) {
    JsonOut($JSON);
}

$CompCodes=getModuleParameter('AccSync', 'CompCodes', array());
asort($CompCodes);
$NumRows=0;
$Executed=false;

switch($_REQUEST['act']) {
    case 'remove':
        $code=($_REQUEST['code']??'');
        if(in_array($code, $CompCodes)) {
            unset($CompCodes[array_search($code, $CompCodes)]);
            setModuleParameter('AccSync', 'CompCodes', $CompCodes);
        }
        $JSON['rows']=getRows();
        $JSON['error']=0;
        break;
    case 'load':
        $JSON['rows']=getRows();
        $JSON['error']=0;
        break;
    case 'compList':
        $JSON['rows']=[];
        $JSON['error']=0;
        $CompCodes[]=$_SESSION['TourCode'];
        $q=safe_r_sql("select ToCode as `value`, concat_ws(' - ', ToCode, ToName) as `text` from Tournament where ToCode not in (".implode(',', StrSafe_DB($CompCodes)).") order by ToWhenFrom desc");
        while($r=safe_fetch($q)) {
            $JSON['rows'][]=$r;
        }
        break;
    case 'addCode':
        $CompCodes=array_merge($CompCodes, $_REQUEST['codes']??[]);
        asort($CompCodes);
        setModuleParameter('AccSync', 'CompCodes', $CompCodes);
        $JSON['rows']=getRows();
        $JSON['error']=0;
        break;
    case 'sync':
        safe_w_sql("update Entries
            inner join Tournament on ToId=EnTournament and ToCode=".StrSafe_DB($_SESSION['TourCode'])."
            inner join (
                select distinct EnCode OldCode, EnDivision OldDivision, max(EnBadgePrinted) as OldBadgePrinted
                from Entries 
                inner join Tournament on ToId=EnTournament
                where ToCode in (".implode(',', StrSafe_DB($CompCodes)).") and EnBadgePrinted>0
                group by EnCode, EnDivision
                ) Oldies on OldCode=EnCode and OldDivision=EnDivision
            set EnBadgePrinted=OldBadgePrinted
            where coalesce(EnBadgePrinted,0)=0");
        $Synced=safe_w_affected_rows();

        safe_w_sql("Update Entries
			inner join Photos on EnId=PhEnId
			set EnBadgePrinted=greatest(EnBadgePrinted, PhPhotoEntered)
			where EnTournament = {$_SESSION['TourId']} 
			and coalesce(EnBadgePrinted,0)>0");

        $JSON['msg']=get_text('PrintSyncResult', 'BackNumbers', $Synced);
        $JSON['error']=0;
        $JSON['rows']=getRows();
        break;
}

JsonOut($JSON);

function getRows() {
    $CompCodes=getModuleParameter('AccSync', 'CompCodes', array());
    asort($CompCodes);
    $CompDetails=[];
    $CompCodesToCheck=$CompCodes;
    $CompCodesToCheck[]= $_SESSION['TourCode'];
    foreach($CompCodesToCheck as $code) {
        $CompDetails[$code]=(object) [
            'ToCode'=>$code,
            'Total'=>0,
            'PrintedNo'=>0,
            'NewPhoto' => 0,
            'ToRetake' => 0,
            'canDelete' => true,
        ];
    }
    $SQL = "SELECT ToCode, COUNT(*) as Total, SUM(IF(EnBadgePrinted!='0000-00-00',1,0)) as PrintedNo, sum(if(coalesce(PhPhotoEntered,'0000-00-00')>date_sub(ToWhenFrom, interval 3 day), 1, 0)) as NewPhoto, sum(coalesce(PhToRetake,0)) as ToRetake
                FROM `Entries` 
                inner join Tournament on ToId=EnTournament
                left join Photos on PhEnId=EnId
                WHERE ToCode in (".implode(',', StrSafe_DB($CompCodesToCheck)).")
                group by ToId";
    $q=safe_r_sql($SQL);
    while($r=safe_fetch($q)) {
        $r->canDelete=($r->ToCode!=$_SESSION['TourCode']);
        $CompDetails[$r->ToCode]=$r;
    }

    return array_values($CompDetails);
}

