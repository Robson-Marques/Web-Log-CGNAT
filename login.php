	<!DOCTYPE html>
	<html lang="pt-br">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<title>Login - CGNAT</title>
		<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
		<link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
	</head>

	<?php

	// Inicialize a sessão
	session_start();
	 
	// Verifique se o usuário já está logado, em caso afirmativo, redirecione-o para a página de boas-vindas
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		header("location: index.php");
		exit;
	}
	 
	// Incluir arquivo de configuração
	require_once "conf/config.php";
	 
	// Defina variáveis e inicialize com valores vazios
	$username = $password = "";
	$username_err = $password_err = $login_err = "";
	 
	// Processando dados do formulário quando o formulário é enviado
	if($_SERVER["REQUEST_METHOD"] == "POST"){
	 
		// Verifique se o nome de usuário está vazio
		if(empty(trim($_POST["username"]))){
			$username_err = "Por favor, insira o nome de usu&aacute;rio.";
		} else{
			$username = trim($_POST["username"]);
		}
		
		// Verifique se a senha está vazia
		if(empty(trim($_POST["password"]))){
			$password_err = "Por favor, insira sua senha.";
		} else{
			$password = trim($_POST["password"]);
		}
		
		// Validar credenciais
		if(empty($username_err) && empty($password_err)){
			// Prepare uma declaração selecionada
			$sql = "SELECT id, nome, username, nivel, password FROM users WHERE username = :username";
			
			if($stmt = $pdo->prepare($sql)){
				// Vincule as variáveis à instrução preparada como parâmetros
				$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
				
				// Definir parâmetros
				$param_username = trim($_POST["username"]);
				
				// Tente executar a declaração preparada
				if($stmt->execute()){
					// Verifique se o nome de usuário existe, se sim, verifique a senha
					if($stmt->rowCount() == 1){
						if($row = $stmt->fetch()){
							$id = $row["id"];
							$username = $row["username"];
							$nivel = $row["nivel"];
							$name = $row["nome"];
							$hashed_password = $row["password"];
								if(password_verify($password, $hashed_password)){
								// A senha está correta, então inicie uma nova sessão
								session_start();
								
								// Armazene dados em variáveis de sessão
								$_SESSION["loggedin"] = true;
								$_SESSION["id"] = $id;
								$_SESSION["username"] = $username;                            
								$_SESSION["Nivel"] = $nivel;
								$_SESSION["Nome"] = $name;
								// Redirecionar o usuário para a página de boas-vindas
								header("location: index.php");
							} else{
								// A senha não é válida, exibe uma mensagem de erro genérica
								$login_err = "Nome de usu&aacute;rio ou senha inv&aacute;lidos.";
							}
						}
					} else{
						// O nome de usuário não existe, exibe uma mensagem de erro genérica
						$login_err = "Nome de usu&aacute;rio ou senha inv&aacute;lidos.";
					}
				} else{
					echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
				}

				// Fechar declaração
				unset($stmt);
			}
		}
		
		// Fechar conexão
		unset($pdo);
	}
	?>
	 

	<body class="bg-gradient-primary">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-9 col-lg-12 col-xl-10">
					<div class="card shadow-lg o-hidden border-0 my-5">
						<div class="card-body p-0">
							<div class="row">
								<div class="col-lg-6 d-none d-lg-flex">
									<div class="flex-grow-1 bg-login-image" style="background-image: url(&quot;assets/img/dogs/image3.jpeg&quot;);"></div>
								</div>
								<div class="col-lg-6">
									<div class="p-5">
										<div class="text-center">
											<h4 class="text-dark mb-4">Bem Vindo!</h4>
										</div>
										<form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
											<div class="mb-3"><input class="form-control form-control-user <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" type="text" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Usuario" name="username">
						<span class="invalid-feedback"><?php echo utf8_encode($username_err); ?></span>
						</div>
											<div class="mb-3"><input class="form-control form-control-user <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" type="password" id="exampleInputPassword" placeholder="Senha" name="password">
						<span class="invalid-feedback"><?php echo $password_err; ?></span>
						</div>
											<div class="mb-3">
												<div class="custom-control custom-checkbox small">
													<div class="form-check"><input class="form-check-input custom-control-input" type="checkbox" id="formCheck-1"><label class="form-check-label custom-control-label" for="formCheck-1">Remember Me</label></div>
												</div>
											</div><button class="btn btn-primary d-block btn-user w-100" type="submit">Login</button>
								<?php 
								  if(!empty($login_err)){
									 echo '<hr><div class="alert alert-danger">' . utf8_encode($login_err) . '</div><hr>';
								 }        
								?>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
  		<script src="assets/js/jquery-3.7.0.min.js"></script>
		<script src="assets/bootstrap/js/bootstrap.min.js"></script>
	</body>

	</html>