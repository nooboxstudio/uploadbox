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
            
            if($tamanho < 10240){ //se imagem for até 1MB envia
                $nome_atual = $_FILES['arquivo']['name'] . md5(uniqid(time())).$ext; //nome que dará a imagem
                $tmp = $_FILES['arquivo']['tmp_name']; //caminho temporário da imagem
                
                if(move_uploaded_file($tmp,$pasta.$nome_atual)){
                    
					$query = mysql_query("INSERT INTO docs(arquivo, cond_id, data) VALUES ('$nome_atual', '$cond', '$data')");
					
					
                    echo "<script>alert('Upload efetuado com sucesso');window.location=\"./\"</script>";
                }else{
                    echo "<script>alert('Erro ao enviar');</script>";
                }
            }else{
                echo "<script>alert('O arquivo deve ser de no máximo 10MB');</script>";
            }
        }else{
            echo "<script>alert('Arquivo não permitido');window.location=\"./\"</script>";
        }
    }else{
        echo "<script>alert('Selecione um arquivo');</script>";
        exit;
    }
   
?>