<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclAccreditation, AclReadWrite);
$badge2Print = urldecode($_REQUEST["toPrint"] ?? '');
$specificAutomator='';
$customPrinter = $_REQUEST["printer"] ?? '';;

runJack("AutoCheckinPrint", $_SESSION['TourId'], array("URL"=>$badge2Print , "Automator"=>$specificAutomator, "Printer"=>$customPrinter, "TourId"=>$_SESSION['TourId']));