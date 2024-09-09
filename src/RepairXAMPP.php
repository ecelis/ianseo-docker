<?php
apache_setenv('no-gzip', '1');

if(php_uname('s')!='Windows NT') {
	die('<h1>THIS ONLY WORKS IN WINDOWS!</h1>');
}

error_reporting(E_ALL);
$MysqlDir=realpath(dirname(__DIR__).'/mysql/bin/').DIRECTORY_SEPARATOR;
$DataDir=realpath(dirname(__DIR__).'/mysql/data/').DIRECTORY_SEPARATOR;
echo "<h1>Will try to repair DB automatically... If it does not work, try shutting down mysql before refreshing this page!</h1>";

// echo '<div><b>Stopping server</b></div>';
// if(!exec("stop /B {$MysqlDir}mysqld.exe")) {
// 	echo 'could not stop the server';
// }

echo "<div><b>Launching Aria tables repair</b></div>";
flush();
unset($lines);
exec("{$MysqlDir}aria_chk -r --data_dir={$DataDir}", $lines);
foreach($lines as $line) {
	echo "<div style='font-size:small'>$line</div>";
}

echo '<div><b>Launching mysqld</b></div>';
unset($lines);

$process=proc_open("start /B {$MysqlDir}mysqld.exe --defaults-file={$MysqlDir}my.ini --console --skip-grant-tables --skip-external-locking",
	[['pipe','r'],['pipe','w'],['pipe','w']],
	$pipes);
if(is_resource($process)) {
	proc_close($process);
	echo "<div><b>Launching database repair</b></div>";
	flush();
	unset($lines);
	exec("{$MysqlDir}mysqlcheck -r --databases mysql,ianseo --use-frm", $lines);
	foreach($lines as $line) {
		echo "<div style='font-size:small'>$line</div>";
	}

	echo "<div><b>Checking privileges</b></div>";
	flush();
	unset($lines);
	$CFG=new stdClass();
	require_once(__DIR__.'/Common/config.inc.php');
	$q=mysqli_connect($CFG->R_HOST, 'root','', $CFG->DB_NAME) or die('could not reconnect to server');
	$q->query("flush privileges");
	$q->query("CREATE USER IF NOT EXISTS '$CFG->R_USER'@'$CFG->R_HOST' identified by '$CFG->R_PASS'") or die($q->error);
	$q->query("grant all privileges on {$CFG->DB_NAME}.* to '$CFG->R_USER'@'$CFG->R_HOST'") or die($q->error);

	echo '<h1><a href="./">Back to Ianseo</a></h1>';
	//	exec(realpath(dirname(__DIR__)).DIRECTORY_SEPARATOR.'mysql_stop.bat');
	die();
} else {
	echo "<div>unable to do anything</div>";
}
/*
.\mysqld.exe --defaults-file=.\my.ini --console --skip-grant-tables --skip-external-locking

.\mysqlcheck -r --databases mysql --use-frm

*/