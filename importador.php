<?php
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

		$colunasArray = array();
		$interrogArray = array();
		
		foreach($colunas as $chave => $coluna){
			$colunasArray = array_merge($colunasArray, [$chave => "`".$coluna."`"]);
			$interrogArray = array_merge($interrogArray, [$chave => "?"]);
		}
	}
	
	$conn = mysqli_connect($host, $username, $password, $database);
	
	$colunasString = implode(',', $colunasArray);
	$interrogString = implode(',', $interrogArray);
	
    if ($_FILES["file"]["size"] > 0) {
		$file = fopen($fileName, "r");
		$pdo = new PDO("mysql:host=".$host.";dbname=".$database, ''.$username, ''.$password);
		$sqlInsert = "INSERT INTO `".$table."` (".$colunasString.")
			VALUES (".$interrogString.");";
		
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
				$message = "Dados CSV importados na base de dados.";
			} else {
				$type = "danger";
				$message = "Problema importando dados CSV: ".$stmt->errorInfo()[2];
			}
		}

    }else{
		$type = "danger";
		$message = "O arquivo .csv está vazio.";
	}
	header("Location: formulario.php?message=".$message."&type=".$type);
}
?>