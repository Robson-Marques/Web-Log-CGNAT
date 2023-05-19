<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Log - CGNAT</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
		<link rel="stylesheet" href="assets/icons/font/bootstrap-icons.min.css">
</head>
<?php

// Inicialize a sessão
session_start();
 
// Verifique se o usuário está logado, caso contrário, redirecione para a página de login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$Nivel = $_SESSION["Nivel"] ;
$Nome = $_SESSION["Nome"] ;
if($Nivel !== 'ADMIN'){

      // Redireciona o visitante de volta pro login
      header("Location: index.php"); exit;
  }
// Incluir arquivo de configuração
require_once "conf/config.php";

 // Defina variáveis e inicialize com valores vazios
$name = $sobrenome = $cdnivel = $username = $password = $confirm_password = "";
$name_err = $sobrenome_err = $username_err = $password_err = $confirm_password_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	// Validar nome
    if(empty(trim($_POST["name"]))){
        $name_err = "Por favor coloque um nome.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["name"]))){
        $name_err = "O nome pode conter apenas letras.";
    } else{
           $name = trim($_POST["name"]);
        }
    
	// Validar sobrenome
    if(empty(trim($_POST["sobrenome"]))){
        $sobrenome_err = "Por favor coloque um sobrenome.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["name"]))){
        $sobrenome_err = "O sobrenome pode conter apenas letras.";
    } else{
           $sobrenome = trim($_POST["sobrenome"]);
        }
       if(empty(trim($_POST["cdnivel"]))){
        $cdnivel_err = "Por favor coloque um nome.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["cdnivel"]))){
        $cdnivel_err = "O nome pode conter apenas letras.";
    } else{
           $cdnivel = trim($_POST["cdnivel"]);
        }
    // Validar nome de usuário
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor coloque um nome de usuário.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "O nome de usuário pode conter apenas letras, números e sublinhados.";
    } else{
        // Prepare uma declaração selecionada
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_username = trim($_POST["username"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Este nome de usuário já está em uso.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    // Validar senha
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor insira uma senha.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "A senha deve ter pelo menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validar e confirmar a senha
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Por favor, confirme a senha.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "A senha não confere.";
        }
    }
    
    // Verifique os erros de entrada antes de inserir no banco de dados
    if(empty($name_err) && empty($sobrenome_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare uma declaração de inserção
        $sql = "INSERT INTO users (nome, sobrenome, username, nivel, password) VALUES (:name, :sobrenome, :username, :nivel, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
			$stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":sobrenome", $param_sobrenome, PDO::PARAM_STR);
			$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":nivel", $param_nivel, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Definir parâmetros
			$param_name = $name;
			$param_sobrenome = $sobrenome;
			$param_username = $username;
            $param_nivel = $cdnivel;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Redirecionar para a página de login
                header("location: register.php");
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

<body id="page-top">

     <div id="wrapper">
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0" style="width: 80px;">
            <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>CGNAT</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                 <ul class="navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-fill-gear"></i><span>Perfil</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="log.php"><i class="fas fa-table"></i><span>Log</span></a></li>
					<?php
					if($Nivel === 'ADMIN'){
                   echo "<li class='nav-item'>";
					echo "<a class='nav-link' href='register.php'>";
					echo "<i class='bi bi-person-plus-fill'></i>";
					echo "<span>Usu&aacute;rios</span>";
					echo "</a></li>";
					}
					?>
					<?php
					if($Nivel === 'ADMIN'){
                   echo "<li class='nav-item'>";
					echo "<a class='nav-link' href='cad-route.php'>";
					echo "<i class='bi bi-hdd-rack-fill'></i>";
					echo "<span>Equipamentos</span>";
					echo "</a></li>";
					}
					?>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>

                </ul>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <div class="d-none d-sm-block topbar-divider"></div>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small"><?php echo $Nome; ?></span><img class="border rounded-circle img-profile" src="assets/img/avatars/avatar5.jpeg"></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil</a><a class="dropdown-item" href="table.php"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Log CGNAT</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Usuarios CGNAT</h3>
                    <div class="card shadow">
					<div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Cadastrar novo Usu&aacute;rio.</p>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
				              <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="row mb-3">
									<div class="col-sm-6">
									<input class="form-control d-inline-block <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" type="text" id="exampleInputEmail" placeholder="Usu&aacute;rio" name="username">
									<span class="invalid-feedback"><?php echo utf8_encode($username_err); ?></span>
									</div>
                                    <div class="col-sm-6">
									<input class="form-control d-inline-block <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" type="text" id="exampleFirstName" placeholder="Nome" name="name">
									<span class="invalid-feedback"><?php echo utf8_encode($name_err); ?></span>
									</div>
                                </div>
                                <div class="row mb-3">
									<div class="col-sm-6">
									<input class="form-control d-inline-block <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" type="password" id="examplePasswordInput" placeholder="Senha" name="password">
									<span class="invalid-feedback"><?php echo $password_err; ?></span>
									</div>
									<div class="col-sm-6">
									<input class="form-control d-inline-block  <?php echo (!empty($sobrenome_err)) ? 'is-invalid' : ''; ?>" type="text" id="exampleLastName" placeholder="Sobrenome" name="sobrenome">
									<span class="invalid-feedback"><?php echo utf8_encode($sobrenome_err); ?></span>
									</div>	
								</div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
									<input class="form-control d-inline-block <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" type="password" id="exampleRepeatPasswordInput" placeholder="Repetir Senha" name="confirm_password">
									<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
									</div>
									<div class="col-sm-3">
									<select class="form-control d-inline-block" name="cdnivel">
                                    <option value="ADMIN" selected="">Administrador</option>
                                    <option value="USER">Usuario</option>
                                    </select></div>
									<div class="col-sm-3 text-nowrap text-primary">
									<button class="btn btn-primary d-block w-100" type="submit">Salvar Usu&aacute;rio</button>
									</div>
                                </div>
                                <hr>
                               </form>
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Lista de Usu&aacute;rios Cadastrados.</p>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                            </div>
								<!-- Modal -->
 								<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h2 class="modal-title" id="myModalLabel">Exclus&atilde;o de Usu&aacute;rio</h2>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="Close" ></button>
											</div>
											<div class="modal-body">
												<h5> Voc&ecirc; gostaria de deletar este Registro?</h5>
											</div>
											<!--/modal-body-collapse -->
											<div class="modal-footer">
												<button type="button" class="btn btn-danger" id="btnDelteYes" href="#">Sim</button>
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">N&atilde;o</button>
											</div>
											<!--/modal-footer-collapse -->
										</div>
										<!-- /.modal-content -->
									</div>
									<!-- /.modal-dialog -->
								</div>
								<!-- /.modal -->
                            <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
							<div id="status" class="alert">
							</div>
                                <table class="table my-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
											<th>Nome</th>
											<th>Usuario</th>
											<th>Nivel</th>
											<th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody>
	<?php
	$sql = "SELECT * FROM users";
	if($stmt = $pdo->prepare($sql)){
    // Tente executar a declaração preparada
        if($stmt->execute()){
            $res = $stmt -> fetchAll(PDO::FETCH_OBJ);
            foreach($res as $usuario){ 
					?>
                        <tr Class="btnDelete" data-id="<?= $usuario -> id; ?>" >
                            <td><pre class='text-primary'><?= $usuario -> id; ?></td>
                            <td><pre class='text-primary'><?= $usuario -> nome; ?>&nbsp;<?= $usuario -> sobrenome; ?></td>
							<td><pre class='text-primary'><?= $usuario -> username; ?></td>
							<td><pre class='text-primary'><?= $usuario -> nivel; ?></td>
                            <td><button class="btn btn-danger btnDelete" style="margin-top: 3px;">EXCLUIR</button></td>
                        </tr>
 <?php
            }
		}
    // Fechar declaração
    unset($stmt);
	}
	// Fechar conexão
	unset($pdo);
 ?>
                                    </tbody>
                                    <tfoot>
                                        <tr></tr>
                                    </tfoot>
                                </table>
                       </div>
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
			</div>
			</div>
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright Â© Robson Marques 2023</span></div>
                </div>
            </footer>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/script.min.js"></script>
    <script src="assets/js/script.user.js"></script>
</body>

</html>