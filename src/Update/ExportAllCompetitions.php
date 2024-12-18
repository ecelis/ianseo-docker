<?php

require_once(dirname(__FILE__, 2) . '/config.php');
checkACL(AclRoot, AclReadWrite);

if(!empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
    CD_redirect($CFG->ROOT_DIR.'noAccess.php');
}

require_once('Common/Fun_Export.php');
//$TourId=$_SESSION['TourId'];
//EraseTourSession();

// prepare the dir we will export to
$TempDir=dirname(__DIR__).'/Tournament/TmpDownload/ianseo_'.mt_rand(100000, mt_getrandmax());
if((is_dir($TempDir) and !rmdir($TempDir)) or (file_exists($TempDir) and !unlink($TempDir))) {
    die('Could not prepare temporary directory');
}

if(!mkdir($TempDir)) {
    die('Could not create temporary directory');
}
$TempDir.='/';

$zip = new ZipArchive;
$download = 'IanseoCompetitions.zip';
$zip->open($TempDir.$download, ZipArchive::CREATE);

$Sql = "SELECT ToId, ToCode from Tournament";
$q=safe_r_SQL($Sql);
while($r=safe_fetch($q)) {
    ini_set('max_execution_time','240');
    $Gara = export_tournament($r->ToId, true);
    $ToSave = gzcompress(serialize($Gara),9);
    file_put_contents($TempDir.$r->ToCode.".ianseo", $ToSave);
    $zip->addFile($TempDir.$r->ToCode.".ianseo", $r->ToCode.".ianseo");
}
$zip->close();

// send the zip file
header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename = $download");
header('Content-Length: ' . filesize($TempDir.$download));
readfile($TempDir.$download);

// Cleanup all the ianseo files
foreach(glob($TempDir.'*.ianseo') as $filename) {
    unlink($filename);
}

unlink($TempDir.$download);
rmdir($TempDir);
