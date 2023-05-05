<?php
$host = "localhost";
$username = "root";
$password = "";
$db = "uploaddocs";
$con_host = @mysql_connect($host,$username,$password);
$con_db = @mysql_select_db($db,$conexao_host );
mysql_connect($host,$username,$password) or
die ("Não pude conectar: " . mysql_error());
mysql_select_db($db);
?>