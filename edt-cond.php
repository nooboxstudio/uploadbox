<?php require_once('Connections/conn.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update-form")) {
  $updateSQL = sprintf("UPDATE condominios SET name_cond=%s WHERE id_cond=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());

  $updateGoTo = "condominios";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_conn, $conn);
$query_user_loged = "SELECT * FROM `user`";
$user_loged = mysql_query($query_user_loged, $conn) or die(mysql_error());
$row_user_loged = mysql_fetch_assoc($user_loged);
$totalRows_user_loged = mysql_num_rows($user_loged);

mysql_select_db($database_conn, $conn);
$query_listar_condominios = "SELECT * FROM condominios ORDER BY id_cond ASC";
$listar_condominios = mysql_query($query_listar_condominios, $conn) or die(mysql_error());
$row_listar_condominios = mysql_fetch_assoc($listar_condominios);
$totalRows_listar_condominios = mysql_num_rows($listar_condominios);

$colname_edt_cond = "-1";
if (isset($_GET['id'])) {
  $colname_edt_cond = $_GET['id'];
}
mysql_select_db($database_conn, $conn);
$query_edt_cond = sprintf("SELECT * FROM condominios WHERE id_cond = %s", GetSQLValueString($colname_edt_cond, "int"));
$edt_cond = mysql_query($query_edt_cond, $conn) or die(mysql_error());
$row_edt_cond = mysql_fetch_assoc($edt_cond);
$totalRows_edt_cond = mysql_num_rows($edt_cond);
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $row_edt_cond['name_cond']; ?> | <?php print $sitename; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/jquery.min.js"></script>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link href="css/styles.css" rel="stylesheet">
    <meta charset="utf-8">
    <script type="text/javascript" src="js/bootstrap-filestyle.js">
    $('#arquivo').filestyle()
			$('#arquivo').filestyle({
				'placeholder' : 'Selecione um arquivo'
			});
    </script>
<link rel="stylesheet" href="css/bootstrap-select.css">
<script src="js/bootstrap-select.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  // Initialize the tooltip.
  $('#copy-button').tooltip();

  // When the copy button is clicked, select the value of the text box, attempt
  // to execute the copy command, and trigger event to update tooltip message
  // to indicate whether the text was successfully copied.
  $('#copy-button').bind('click', function() {
    var input = document.querySelector('#copy-input');
    input.setSelectionRange(0, input.value.length + 1);
    try {
      var success = document.execCommand('copy');
      if (success) {
        $('#copy-button').trigger('copied', ['Copiado!']);
      } else {
        $('#copy-button').trigger('copied', ['Copiar com Ctrl-c']);
      }
    } catch (err) {
      $('#copy-button').trigger('copied', ['Copiar com Ctrl-c']);
    }
  });

  // Handler for updating the tooltip message.
  $('#copy-button').bind('copied', function(event, message) {
    $(this).attr('title', message)
        .tooltip('fixTitle')
        .tooltip('show')
        .attr('title', "Copy to Clipboard")
        .tooltip('fixTitle');
  });
});
</script>
<script type="text/javascript">
$(document).ready(function(){
     /* #imagem é o id do input, ao alterar o conteudo do input execurará a função baixo */
     $('#imagem').live('change',function(){
         $('#visualizar').html('<img src="ajax-loader.gif" alt="Enviando..."/> Enviando...');
        /* Efetua o Upload sem dar refresh na pagina */           $('#formulario').ajaxForm({
            target:'#visualizar' // o callback será no elemento com o id #visualizar
         }).submit();
     });
 })
</script>

  </head>
  <body>
  	<div class="header">
	     <div class="container">
	        <div class="row">
	           <div class="col-md-5">
	              <!-- Logo -->
	              <div class="logo">
                  <img src="images/logo-cristiano.png" width="300" height="68"></div>
	           </div>
               <div class="col-md-5"></div>
	           <div class="col-md-2">
	              <div class="navbar navbar-inverse" role="banner">
	                  <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
	                    <ul class="nav navbar-nav">
	                      <li class="dropdown">
	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Minha Conta <b class="caret"></b></a>
	                        <ul class="dropdown-menu animated fadeInUp">
	                          <li><a href="perfil">Perfil</a></li>
	                          <li><a href="<?php echo $logoutAction ?>">Logout</a></li>
	                        </ul>
	                      </li>
	                    </ul>
	                  </nav>
	              </div>
	           </div>
	        </div>
            
	     </div>
         
  </div>


    <div class="page-content">

<!--começo menu-->
    	<div class="row">
		  <div class="col-md-2">
		  	<div class="sidebar content-box" style="display: block;">
                <ul class="nav">
                    <!-- Main menu -->
                    <li class="current"><a href="./"><i class="glyphicon glyphicon-cloud-upload"></i> Upload</a></li>
                    <li class="current"><a href="condominios"><i class="glyphicon glyphicon-th-large"></i> Condomínios</a></li>
                    <li class="current">&nbsp;</li>
                  <li class="current"><i class="glyphicon glyphicon-arrow-down"></i><strong>Arquivos por Condomínio</strong></li>
                    
                  <?php do { ?>
                  <li class="current"><a href="arquivos-cond?id=<?php echo $row_listar_condominios['id_cond']; ?>"> <?php echo $row_listar_condominios['name_cond']; ?></a></li>
                    <?php } while ($row_listar_condominios = mysql_fetch_assoc($listar_condominios)); ?>
                    
                </ul>
             </div>
		  </div>
<!--fim do menu-->
		  <div class="col-md-10">
		  	<div class="row"></div>

		  	<div class="row"></div>

		  	<div class="content-box-large">
            <h4>Alterar no do Condomínio: <strong><?php echo $row_edt_cond['name_cond']; ?></strong></h4>
				<form action="<?php echo $editFormAction; ?>" method="POST" name="update-form" id="update-form">
                <input name="id" type="hidden" id="id" value="<?php echo $row_edt_cond['id_cond']; ?>">
                <input name="name" class="form-control" type="text" id="name" value="<?php echo $row_edt_cond['name_cond']; ?>"><br>
                <input name="atualizar" type="submit" id="atualizar" value="Atualizar" class="btn btn-success" style="float:right"><br>
<br>
<input type="hidden" name="MM_update" value="update-form">

                </form>
		  	</div>
		  </div>
		</div>
    </div>

    <footer>
         <div class="container">
         
            <div class="copy text-center">
               Desenvolvido por <a href='http://noobox.org' target="_blank">Noobox</a>
            </div>
            
         </div>
      </footer>
  </body>
</html>
<?php
mysql_free_result($user_loged);

mysql_free_result($listar_condominios);

mysql_free_result($edt_cond);
?>
