<?php

global $NewIanseo, $CFG, $version;

if(isset($NewIanseo) and isset($NewIanseo->UUID)) {
    // MUST be here!!!
    SetParameter('UUID2', $NewIanseo->UUID);
    DelParameter('UUID');
}

$newversion='2024-05-13 15:25:00';

// Check if the DB is up to date
if(in_array($CFG->DOCUMENT_PATH . 'Common'.DIRECTORY_SEPARATOR.'config.inc.php', get_included_files())) {
	$version = GetParameter('DBUpdate');
	if($version < $newversion) {
		require_once('Common/UpdateDb.inc.php');
	}
}
