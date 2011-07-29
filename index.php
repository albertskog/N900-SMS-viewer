<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>Alberts N900 SMS viewer</title>
<link rel="shortcut icon" href="favicon.png" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div id="wrapper">

<?php 
echo exec('rsync -e ssh root@Albert-N900.local:/home/user/.rtcom-eventlogger/el-v1.db '.
	dirname(__FILE__).'/el_'.date('Y-m-d_H.i.s').'.sqlite');

try 
{
    //connect to SQLite database

    $database = new PDO("sqlite:el-v1.db");
    //echo "Handle has been created ...... <br><br>";

}
catch(PDOException $e)
{
    echo $e->getMessage();
    echo "<br><br>Database -- NOT -- loaded successfully .. ";
    die( "<br><br>Query Closed !!! $error");
}

//echo "Database loaded successfully ....";

$query = 'SELECT start_time, end_time, remote_uid, is_read, outgoing, free_text FROM Events WHERE event_type_id = 7 ORDER BY CASE WHEN end_time = 0 THEN start_time ELSE end_time END DESC LIMIT 100' ;

$sender = "";

foreach($database->query($query) as $message)
{
	if($message['end_time'] >> 0)
	{
		$message['start_time'] = $message['end_time'];
	}
	if($sender != str_replace('+46', '0', $message['remote_uid']))
	{
		echo '<hr>';
	}
	$sender = str_replace('+46', '0', $message['remote_uid']);

?>

	<div class="message <? echo ($message['outgoing'] ? 'outgoing':'');?>">
		<p>
			<span class="timestamp">
			<? echo date('Y.m.d | H:i', $message['start_time']);?>
			</span>
			<span class="sender <? echo ($message['outgoing'] ? 'outgoing':'');?>">
				<? echo ($message['outgoing'] ? "Du:" : $sender.":");?>
			</span>
			<span class="content">
				<? echo $message['free_text'];?>
			</span>
		</p>
	</div>
<?
}

?>
</div><!-- wrapper -->
</body>
</html>