<?php
/*
															- MakeTeams.php -
	Genera le squadre
*/
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$JSON=[
    'error'=>1,
    'msg'=>get_text('Error','Errors'),
];

if(!hasACL(AclQualification, AclReadWrite) or IsBlocked(BIT_BLOCK_QUAL)) {
    JsonOut($JSON);
}

$JSON['error'] = MakeTeams(NULL, NULL);
$JSON['msg'] = get_text('ResultSqClass','Tournament') . ($JSON['error'] ? get_text('MakeTeamsError','Tournament') : get_text('MakeTeamsOk','Tournament')) ;

JsonOut($JSON);