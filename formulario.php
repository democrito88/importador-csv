<!doctype>
<html>
<head>
	<title>Importar CSV</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="message-callback.js"></script>
  <link rel="stylesheet" href="css/app.css" />
</head>
<body>
	<script type="text/javascript">
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
	});
	
	function adicionarColuna(){
		$("#colunas").append("<input class=\"form-control m-1\" type=\"text\" name=\"colunas[]\">");
	}
	</script>
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
					<form class="form-horizontal p-5" action="importador.php" method="post"
						name="frmCSVImport" id="frmCSVImport"
						enctype="multipart/form-data">
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label h5">Host</label><br>
								<input class="form-control" type="text" name="host" id="host">
							</div>
							<div class="col-sm-4">
								<label class="control-label h5">Nome de usuário</label><br>
								<input class="form-control" type="text" name="username" id="username">
							</div>
							<div class="col-sm-4">
								<label class="control-label h5">Senha</label><br>
								<input class="form-control" type="password" name="password" id="password">
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<label class="control-label h5">Nome da base de dados</label><br>
								<input class="form-control" type="text" name="database" id="database">
							</div>
							<div class="col-sm-4">
								<label class="control-label h5">Nome da tabela</label><br>
								<input class="form-control" type="text" name="table" id="table">
							</div>
							<div class="col-sm-4">
								<label class="control-label h5">Colunas</label><br>
								<div class="border p-2" id="colunas">
									<input class="form-control m-1" type="text" name="colunas[]">
								</div>
								<a id="adicionarColuna" onclick="adicionarColuna();"><i class="fa fa-plus"></i></a>
							</div>
						</div>
						<br>
						<div class="row">
							<label class="col-md-4 control-label h5">Selecione um arquivo CSV</label><br>
							<input type="file" name="file" id="file" accept=".csv"><br><br>
						</div>
						<button class="btn btn-success" type="submit" id="submit" name="import"
								class="btn-submit">Importar</button>
					</form>

				</div>
			</div>
			
		</div>
		<footer class="footer border-top">
		<div class="row">
			<div id="message-callback" class="col-md-12 text-center">
				
			</div>
		</div>
	</footer>
	
	</div>
</body>
</html>
