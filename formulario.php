<!DOCTYPE>
<html>
<head>
	<!-- Desenvolvido por Demócrito d'Anunciação democrito@olinda.pe.gov.br -->
	<title>Importar CSV</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/app.css" />
	<link rel="icon" type="image/x-icon" href="img/favicon.ico">
	<script type="text/javascript">
		var tipos = "";

		$(document).ready(function() {
			$("#frmCSVImport").on("submit", function () {

				$("#response").attr("class", "");
				$("#response").html("");
				var fileType = ".csv";
				var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
				if (!regex.test($("#file").val().toLowerCase())) {
						$("#response").addClass("error");
						$("#response").addClass("display-block");
					$("#response").html("Invalid File. Upload : <b>" + fileType + "</b> Files.");
					return false;
				}
				return true;
			});

			$("#primeiraLinha").change(function(){
				$(".colunas").prop("disabled", $(this).is(":checked"));
			});
			
		});

		function adicionarColuna(){
			$("#colunas").append("<div class=\"row\"><div class=\"col-sm-6\">"
									+"<input class=\"form-control m-1 colunas\" type=\"text\" name=\"colunas[]\">"
								+"</div>"
								+"<div class=\"col-sm-6\">"
									+"<select class='form-select' name='tipos[]'>"
								<?php 
									foreach(json_decode(file_get_contents("json/tipos.json"), true) as $indice => $tipos){
										echo "+\"<optgroup label='".$indice."'>\"";
										foreach($tipos as $tipo){
											echo "+\"<option value='".$tipo."'>".$tipo."</option>\"";
										}
										echo "+\"</optgroup>\"";
									}
								?>
									+"</select>"
								+"</div></div>");
		}
	</script>
</head>
<body>
	
	<div class="container">
	
		<div class="container-fuid mt-5 mb-5">
				
			<h2 class="h2 mt-5">Importe arquivo CSV em database Mysql usando PHP</h2>
			<br>
			
			<div id="response" 
				class="<?php echo isset($_GET['type']) ? "alert alert-".$_GET['type'] . " d-block" :  'd-none'; ?>">
				<?php echo (isset($_GET['message']) ? $_GET['message'] : "");  ?>
			</div>
			<div class="outer-scontainer">
				<div class="row card shadow-lg">
					<form class="form-horizontal p-5" action="importador.php" method="POST"
						id="frmCSVImport" enctype="multipart/form-data" accept-charset='UTF-8'>
						<div class="row form-group">
							<div class="col-sm-4">
								<label class="control-label h5">Host</label><br>
								<input class="form-control" type="text" name="host" id="host" value="localhost">
							</div>
							<div class="col-sm-4">
								<label class="control-label h5">Nome de usuário</label><br>
								<input class="form-control" type="text" name="username" id="username" value="phpmyadmin">
							</div>
							<div class="col-sm-4">
								<label class="control-label h5">Senha</label><br>
								<input class="form-control" type="password" name="password" id="password">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-6">
								<label class="control-label h5">Nome da base de dados</label><br>
								<input class="form-control" type="text" name="database" id="database" value="noticacoesteste">
							</div>
							<div class="col-sm-6">
								<label class="control-label h5">Nome da tabela</label><br>
								<input class="form-control" type="text" name="table" id="table" value="notificacoes">
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-6">
								<input class="form-check-input" type="checkbox" name="criarDatabase" id="criarDatabase">
								<label class="control-label h5" for="criarDatabase">Criar base de dados caso não exista</label>
							</div>
							<div class="col-sm-6">
								<input class="form-check-input" type="checkbox" name="criarTabela" id="criarTabela">
								<label class="control-label h5" for="criarTabela">Criar tabela na base de dados caso não exista</label>
							</div>
						</div>
						<br>
						<hr>
						<br>
						<div class="row form-group">
							<div class="col-sm-4">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="primeiraLinha" id="primeiraLinha">
									<label class="control-label" for="primeiraLinha">Usar a primeira linha do documento .csv para definir as colunas</label>
								</div>
							</div>
							<div class="col-sm-8">
								<label class="control-label h5">Colunas</label><br>
								<div class="border p-2" id="colunas">
									<div class="row">
										<div class="col-sm-6">
											<input class="form-control m-1 colunas" type="text" name="colunas[]">
										</div>
										<div class="col-sm-6">
											<select class="form-select" name="tipos[]">
												<?php 
												foreach(json_decode(file_get_contents("json/tipos.json"), true) as $indice => $tipos){
													echo "<optgroup label='".$indice."'>";
													foreach($tipos as $tipo){
														echo "<option value='".$tipo."'>".$tipo."</option>";
													}
													echo "</optgroup>";
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<a id="adicionarColuna" onclick="adicionarColuna();"><i class="bi bi-plus-square-fill"></i> Nova coluna...</a>
							</div>
						</div>
						<br>
						<hr>
						<br>
						<div class="row form-group">
							<div class="col-sm-8">
								<label class="col-md-4 control-label h5" for="file">Selecione um arquivo CSV</label>
								<input class="form-control" type="file" name="file" id="file" accept=".csv">
							</div>
							<div class="col-sm-4">
								<h5 class="h5">Caractere separador de valores</h5>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="separador[]" id="separador" value=";">
									<label class="control-label" for="separador">";"</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="separador[]" id="separador" value=",">
									<label class="control-label" for="separador">","</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="separador[]" id="separador" value=" ">
									<label class="control-label" for="separador">Espaço</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="separador[]" id="separador" value="	">
									<label class="control-label" for="separador">Tabulação</label>
								</div>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-sm-12">
								<button class="btn btn-success" type="submit" id="submit">
									Importar
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<footer class="footer border-top">
		<div class="row">
			<div id="message-callback" class="col-md-12 text-center">
				
			</div>
		</div>
	</footer>
</body>
</html>
