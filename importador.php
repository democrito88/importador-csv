<?php
//Desenvolvido por Demócrito d'Anunciação democrito@olinda.pe.gov.br
function validateDate($date, $format = 'Y-m-d'){
	if(strlen($date) == 24){
		//formata a data
		$date = str_replace("T", " ", explode(".", $date)[0]);
		$d = DateTime::createFromFormat($format, $date);
    	return $d && $d->format($format) === $date;
	}else{
		return false;
	}
    
}

if (isset($_POST)) {
	$message = "";
	$sqlInsert = "";
    $fileName = $_FILES["file"]["tmp_name"];
    $host = $_POST['host'];
	$database = $_POST['database'];
	$table = $_POST['table'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$separador = $_POST['separador'][0];

	if(isset($_POST['primeiraLinha']) && $_POST['primeiraLinha'] == true){
		$file = fopen($fileName, "r");
		$colunasArray = fgetcsv($file, 10000, $separador);
		$interrogArray = array_fill(0, count($colunasArray), "?");
	}else{
		$colunas = $_POST['colunas'];
		$tiposColunas = $_POST['tipos'];

		$colunasArray = array();
		$tiposColunasArray = array();
		$interrogArray = array();
		
		foreach($colunas as $chave => $coluna){
			$colunasArray = array_merge($colunasArray, [$chave => "`".$coluna."`"]);
			$tiposColunasArray = array_merge($tiposColunasArray, [$chave => "`".$tiposColunas[$chave]."`"]);
			$interrogArray = array_merge($interrogArray, [$chave => "?"]);
		}
	}

	//cria a base dedados se for solicitado
	if($_POST['criarDatabase']){
		$conn = new mysqli($host, $username, $password);
		$sqlInsert .= "CREATE DATABASE IF NOT EXISTS `".$database."` "
		."DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; ";

		if ($conn->query($sqlInsert) === TRUE) {
			$message .= "Base de dados criada com sucesso! ";
			echo $message;
		} else {
			$message .= "Erro ao criar a base de dados: " . $conn->error." ";
			echo $message;
		}
	}

	//limpa o valor da string
	$sqlInsert = "";
	
	$conn = mysqli_connect($host, $username, $password, $database);
	
	$colunasString = implode(',', $colunasArray);
	$interrogString = implode(',', $interrogArray);
	
    if ($_FILES["file"]["size"] > 0) {
		$file = fopen($fileName, "r");
		$pdo = new PDO("mysql:host=".$host.";dbname=".$database, ''.$username, ''.$password);
		
		//cria a tabela caso não exista
		if($_POST['criarTabela']){
			$sqlInsert .= "CREATE TABLE IF NOT EXISTS `".$table."` (";
		
			foreach($colunasArray as $indice => $coluna){
				$sqlInsert .= " ".$coluna." ".$tiposColunas[$indice]." DEFAULT NULL".($indice + 1 == count($colunasArray) ? " " : ", ");
			}

			$sqlInsert .=   ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ";
			$sqlInsert .= "INSERT INTO `".$table."` (".$colunasString.")
				VALUES (".$interrogString."); ";
		}
		
		$i = 0;

		while (($column = fgetcsv($file, 10000, $separador)) !== FALSE) {
			if(isset($_POST['primeiraLinha']) && $_POST['primeiraLinha'] == true && $i == 0){
				$i++;
				continue;
			}
			$i++;

			$paramArray = array();
			
			foreach($column as $indice => $coluna){
				//Avalia se a informação é uma data
				if(validateDate($coluna, 'Y-m-d H:i:s') !== false) {
					$coluna = date_format(date_create($coluna), 'Y-m-d H:i:s');
				}else if($coluna == 'undefined' || $coluna == 'null' || $coluna == ""){
					$coluna = null;
					$paramArray[$indice] = $coluna;
					continue;
				}
				//var_dump("Linha ".$i.", coluna ".$indice.": ".(is_null($coluna)? "null" : $coluna)."<br>");
				$paramArray[$indice] = mysqli_real_escape_string($conn, utf8_encode($coluna));
			}
			
			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$stmt = $pdo->prepare($sqlInsert);
			
			if ($stmt->execute($paramArray)) {
				$type = "success";
				$message .= "Dados CSV importados na base de dados. ";
			} else {
				$type = "danger";
				$message .= "Problema importando dados CSV: ".$stmt->errorInfo()[2];
			}
		}

    }else{
		$type = "danger";
		$message .= "O arquivo .csv está vazio. ";
	}
	header("Location: formulario.php?message=".$message."&type=".$type);
}
?>