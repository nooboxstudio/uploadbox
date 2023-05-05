<?php require_once('Connections/conn.php'); ?>
<?php
// ID de exemplo
$id = $_GET["id"];

// Selecionando nome da foto do usuário
$sql = mysql_query("SELECT * FROM docs WHERE id = '".$id."'");
$usuario = mysql_fetch_object($sql);

// Removendo usuário do banco de dados
$sql = mysql_query("DELETE FROM docs WHERE id = '".$id."'");

// Removendo imagem da pasta fotos/
unlink("files/".$usuario->arquivo."");
header("Location: ./"); 
?>
