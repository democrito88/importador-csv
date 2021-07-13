<?php

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
		
        while (($column = fgetcsv($file, 1000, $separador)) !== FALSE) {
			
			$paramArray = array();
			
            foreach($column as $indice => $coluna){
				$paramArray[$indice] = mysqli_real_escape_string($conn, $coluna);
			}
            
			$stmt = $pdo->prepare($sqlInsert);
            
            if ($stmt->execute($paramArray)) {
                $type = "success";
                $message = "Dados CSV importados na base de dados.";
            } else {
                $type = "danger";
                $message = "Problema importando dados CSV.";
            }
        }
		
		header("Location: formulario.php?message=".$message."&type=".$type);
    }
}
?>