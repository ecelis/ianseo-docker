<?php
if(!isset($req->device)) {
    $res = 'pong';
} elseif ($req->device == 'ngSocket') {
    $res['ianseo'] = array(
        'UUID' => GetParameter('UUID2'),
        'Version' => ProgramVersion,
        'Build' => ProgramBuild,
        'Release' => ProgramRelease
    );
}
