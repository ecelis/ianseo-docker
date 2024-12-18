<?php
/*
															- MakeTeamsAbs.php - 
	Genera le squadre Assolute
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

$JSON['error'] = MakeTeamsAbs(NULL, NULL, NULL);
$JSON['msg'] = get_text('ResultSqAbs','Tournament') . ($JSON['error'] ? get_text('MakeTeamsError','Tournament') : get_text('MakeTeamsOk','Tournament')) ;

JsonOut($JSON);