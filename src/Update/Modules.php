<?php

require_once(dirname(__FILE__, 2) . '/config.php');
checkACL(AclRoot, AclReadWrite);

$JSON=array('error'=>1);

$user = base64_decode(GetParameter('TestingUser',false,base64_encode(''),false));
if(isset($_REQUEST['req']) and ($user !== false and $user !== '')) {
    $Query=array( 'user' => $user, 'license' => $_REQUEST['req'] );
    $postdata = http_build_query( $Query, '', '&' );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    $context = stream_context_create($opts);
    $stream = fopen($CFG->IanseoServer.'DownloadLicense.php', 'r', false, $context);
    if($stream !==false) {
        $tmp=stream_get_contents($stream);
        try {
            $data = json_decode($tmp);
            if($data->error == 0 ) {
                file_put_contents('modules.pgp', $data->license);
                $JSON['modules'] = $data->license;
                $JSON['error'] = 0;
            }
        }  finally {
            fclose($stream);
        }
    }
} else if(is_file('modules.pgp') and $license = file_get_contents('modules.pgp')) {
    $JSON['error'] = 0;
    $JSON['modules'] = $license;
}

JsonOut($JSON);