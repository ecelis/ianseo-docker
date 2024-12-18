<?php
$JSON=[
    'error'=>1,
];
require_once(dirname(__FILE__, 5) . '/config.php');

if(!CheckTourSession() or !hasACL(AclIndividuals, AclReadWrite)) {
    JsonOut($JSON);
}

switch($_REQUEST['act'] ?? '') {
    case 'setValue':
        require_once('Common/Lib/ArrTargets.inc.php');
        require_once('Common/Lib/CommonLib.php');
        $EvCode=($_REQUEST['event'] ?? '');
        $EnId=intval($_REQUEST['entry'] ?? 0);
        $Field=($_REQUEST['fld'] ?? '');
        $Value=($_REQUEST['val'] ?? '');
        $Target=intval($_REQUEST['tgt'] ?? 0);
        if(!$EvCode or !$EnId or !$Field or !$Target) {
            JsonOut($JSON);
        }

        $JSON['error']=0;
        switch($Field) {
            case 'IndTbClosest':
                $Value=intval($Value);
                $q=safe_r_sql("select IndTieBreak, IndTbClosest from Individuals where IndTournament={$_SESSION['TourId']} and IndId={$EnId} and IndEvent=".StrSafe_DB($EvCode));
                if($r=safe_fetch($q)) {
                    safe_w_sql("update Individuals set IndTbClosest='$Value', IndTbDecoded=".StrSafe_DB(decodeTie($r->IndTieBreak, 1, $Value))." where IndTournament={$_SESSION['TourId']} and IndId={$EnId} and IndEvent=".StrSafe_DB($EvCode));
                } else {
                    $JSON['error']=1;
                }
                break;
            case 'IndTieBreak_0':
            case 'IndTieBreak_1':
            case 'IndTieBreak_2':
                $idx=substr($Field,-1);
                $q=safe_r_sql("select IndTieBreak, IndTbClosest from Individuals where IndTournament={$_SESSION['TourId']} and IndId={$EnId} and IndEvent=".StrSafe_DB($EvCode));
                if($r=safe_fetch($q)) {
                    $TargetLetters=[];
                    foreach(GetTargetNgInfo($Target) as $L) {
                        $TargetLetters[$L['letter']]=$L['point'];
                    }
                    $r->IndTieBreak=str_pad($r->IndTieBreak, 3, ' ', STR_PAD_RIGHT);
                    $r->IndTieBreak[$idx]=GetLetterFromPrint($Value, $TargetLetters);
                    $r->IndTieBreak=rtrim($r->IndTieBreak);
                    safe_w_sql("update Individuals set IndSO=1, IndTieBreak='{$r->IndTieBreak}', IndTbDecoded=".StrSafe_DB(decodeTie($r->IndTieBreak, 1, $r->IndTbClosest))." where IndTournament={$_SESSION['TourId']} and IndId={$EnId} and IndEvent=".StrSafe_DB($EvCode));
                    $JSON['value']=strtoupper($Value);
                } else {
                    $JSON['error']=1;
                }
            break;
            case 'IndRank':
                safe_w_sql("update Individuals set IndRank=$Value, IndRankFinal=$Value where IndTournament={$_SESSION['TourId']} and IndId={$EnId} and IndEvent=".StrSafe_DB($EvCode));
                break;
            default:
                $JSON['error']=1;
        }
        break;
}

JsonOut($JSON);

