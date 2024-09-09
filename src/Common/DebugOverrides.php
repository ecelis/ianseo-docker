<?php

ini_set('display_errors', true);
$CFG->TRACE_QUERRIES = false;

//$CFG->IanseoServer='http://ianseonet/';
/*
$CFG->WaWrapper='http://wa-api.hippo/';
$CFG->ExtranetWrapper='http://wa.hippo/';
$CFG->InfoSystem='http://info.hippo/';
*/
 //$CFG->DB_NAME = 'ianseo_IS';

// $CFG->R_HOST = '24.111.81.72';
// $CFG->W_HOST = '24.111.81.72';

$CFG->ODF=true;
//$CFG->USERAUTH = true;
$CFG->ACLExcluded=array(
    '192.168.42.40',
    '192.168.42.46',
    '192.168.42.47'
);
