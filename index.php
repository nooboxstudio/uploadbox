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

mysql_select_db($database_conn, $conn);
$query_lista_cond_select = "SELECT * FROM condominios ORDER BY id_cond ASC";
$lista_cond_select = mysql_query($query_lista_cond_select, $conn) or die(mysql_error());
$row_lista_cond_select = mysql_fetch_assoc($lista_cond_select);
$totalRows_lista_cond_select = mysql_num_rows($lista_cond_select);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Upload | <?php print $sitename; ?></title>
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
				<form action="./" method="POST" enctype="multipart/form-data" name="formulario" id="formulario">
            <div class="form-group">
              <label for="arquivo">Selecione o arquivo <span style="font-size:10px; color:#FF6464">MÁX:50MB</span></label>
              <input name="arquivo" type="file" class="filestyle" id="arquivo" required data-buttonName="btn-primary" data-buttonText="Selecionar arquivo" data-size="sm"><br>
              
              <select name="condominio" size="1" class="selectpicker form-control" id="condominio" title="Escolha um Condominio" data-live-search="true">
              <?php do { ?>
                <option value="<?php echo $row_lista_cond_select['id_cond']; ?>"><?php echo $row_lista_cond_select['name_cond']; ?></option>
                <?php } while ($row_lista_cond_select = mysql_fetch_assoc($lista_cond_select)); ?>
                </select>
                
<input name="data" type="hidden" id="data" value="<?php echo date('d/m/Y'); ?>">
            </div>
            <input name="upload" type="submit" value="upload"  class="btn btn-primary" style="float:right">
    	</form>     <br>
<br>
   
        <div id="mostra-resultado">
        <?php
    $pasta = "files/";
    
    /* formatos de imagem permitidos */
    $permitidos = array(".jpg",".jpeg",".gif",".png", ".bmp", ".pdf", ".doc", ".xls", ".xml",".mp4");
    
    if(isset($_POST)){
        $nome_imagem    = $_FILES['arquivo']['name'];
        $tamanho_imagem = $_FILES['arquivo']['size'];
		$cond = $_POST['condominio'];
		$data = $_POST['data'];
        /* pega a extensão do arquivo */
        $ext = strtolower(strrchr($nome_imagem,"."));
        
        /*  verifica se a extensão está entre as extensões permitidas */
        if(in_array($ext,$permitidos)){
            
            /* converte o tamanho para KB */
            $tamanho = round($tamanho_imagem / 1024);
            
            if($tamanho < 50240){ //se imagem for até 50MB envia
                $nome_atual = str_replace(' ', '-', $_FILES['arquivo']['name'] . md5(uniqid(time())).$ext); //nome que dará a imagem
                $tmp = $_FILES['arquivo']['tmp_name']; //caminho temporário da imagem
                
                if(move_uploaded_file($tmp,$pasta.$nome_atual)){
                    
					$query = mysql_query("INSERT INTO docs(arquivo, cond_id, data) VALUES ('$nome_atual', '$cond', '$data')");
					
					
                    echo "<br><br><input type=\"text\" class=\"form-control\"
        value=\"".$site_url.$file_path.$nome_atual."\" placeholder=\"Some path\" id=\"copy-input\"><br>
      <button class=\"btn btn-default right\" type=\"button\" id=\"copy-button\"
          data-toggle=\"tooltip\" data-placement=\"button\"
          title=\"Copiar para àrea de transferência\">
        Copiar
      </button>";
                }else{
                    echo "Erro ao enviar";
                }
            }else{
                echo "O arquivo deve ser de no máximo 50MB";
            }
        }else{
            echo "";
        }
    }else{
        echo "Selecione um arquivo";
        exit;
    }
   
?><br>
<br>
</div>
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

mysql_free_result($lista_cond_select);
?>
