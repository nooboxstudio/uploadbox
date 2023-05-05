<?php
require('conection.php');
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn = "localhost";
$database_conn = "uploaddocs";
$username_conn = "root";
$password_conn = "";
$conn = mysql_pconnect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
$site_url = "http://localhost/cristianocontabilidade/";
$sitename = "Cristiano Contabilidade";
$file_path = "files/";
?>