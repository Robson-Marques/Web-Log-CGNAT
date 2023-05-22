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
$hostname = $modelo = $hostip = $tipo_log = "";
$hostname_err = $modelo_err = $hostip_err = $tipo_log_err = "";
 
// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){


	// Validar modelo
    if(empty(trim($_POST["hostip"]))){
        $hostip_err = "Por favor coloque um IP.";
    } elseif(!preg_match('/^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', trim($_POST["hostip"]))){
        $hostip_err = "O IP pode conter apenas números e ponto.";
    } else{
           $hostip = trim($_POST["hostip"]);
        }
	// Validar ip
    if(empty(trim($_POST["modelo"]))){
        $modelo_err = "Por favor coloque um modelo.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["modelo"]))){
        $modelo_err = "O modelo pode conter apenas letras.";
    } else{
           $modelo = trim($_POST["modelo"]);
        }
	// Validar tipo log	
       if(empty(trim($_POST["tipo_log"]))){
        $tipo_log_err = "Por favor coloque um tipo-log.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["tipo_log"]))){
        $tipo_log_err = "O tipo-log pode conter apenas letras.";
    } else{
           $tipo_log = trim($_POST["tipo_log"]);
        }
    // Validar hostname
    if(empty(trim($_POST["hostname"]))){
        $hostname_err = "Por favor coloque um nome de hostname.";
    } elseif(!preg_match('/^[[:ascii:]]+$/', trim($_POST["hostname"]))){
        $hostname_err = "O hostname pode conter apenas letras, números e sublinhados.";
    } else{
        // Prepare uma declaração selecionada
        $sql = "SELECT id FROM routers WHERE hostname = :hostname";
        
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":hostname", $param_hostname, PDO::PARAM_STR);
            
            // Definir parâmetros
            $param_hostname = trim($_POST["hostname"]);
            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $hostname_err = "Este hostname já está em uso.";
                } else{
                    $hostname = trim($_POST["hostname"]);
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }
    
    
    // Verifique os erros de entrada antes de inserir no banco de dados
    if(empty($hostname_err) && empty($modelo_err) && empty($hostip_err) && empty($tipo_log_err)){
        
        // Prepare uma declaração de inserção
        $sql = "INSERT INTO routers (hostname, modelo, hostip, tipo_log) VALUES (:hostname, :modelo, :hostip, :tipo_log)";
        if($stmt = $pdo->prepare($sql)){
            // Vincule as variáveis à instrução preparada como parâmetros
			$stmt->bindParam(":hostname", $param_hostname, PDO::PARAM_STR);
            $stmt->bindParam(":modelo", $param_modelo, PDO::PARAM_STR);
			$stmt->bindParam(":hostip", $param_hostip, PDO::PARAM_STR);
			$stmt->bindParam(":tipo_log", $param_tipo_log, PDO::PARAM_STR);


            
            // Definir parâmetros
			$param_hostname = $hostname;
			$param_modelo = $modelo;
			$param_hostip = $hostip;
			$param_tipo_log = $tipo_log;

            
            // Tente executar a declaração preparada
            if($stmt->execute()){
                // Redirecionar para a página de login
                header("location: cad-route.php");
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
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil</a><a class="dropdown-item" href="log.php"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Log CGNAT</a>
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
                            <p class="text-primary m-0 fw-bold">Cadastrar novo Equipamento.</p>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
				              <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="row mb-3">
									<div class="col-sm-6">
									<input class="form-control d-inline-block <?php echo (!empty($hostname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $hostname; ?>" type="text" id="exampleHostname" placeholder="Hostname" name="hostname">
									<span class="invalid-feedback"><?php echo utf8_encode($hostname_err); ?></span>
									</div>
									<div class="col-sm-6">
									<input class="form-control d-inline-block  <?php echo (!empty($modelo_err)) ? 'is-invalid' : ''; ?>" type="text" id="exampleModelo" placeholder="Modelo" name="modelo">
									<span class="invalid-feedback"><?php echo utf8_encode($modelo_err); ?></span>
									</div>	
								</div>
                                <div class="row mb-3">
                                    <div class="col-sm-6">
									<input class="form-control d-inline-block  <?php echo (!empty($hostip_err)) ? 'is-invalid' : ''; ?>" type="text" placeholder="IP address" name="hostip">
									<span class="invalid-feedback"><?php echo utf8_encode($hostip_err); ?></span>
									</div>
									<div class="col-sm-3">
									<select class="form-control d-inline-block" name="tipo_log">
                                    <option value="syslog" selected="">Syslog</option>
                                    <option value="flow">Flow</option>
                                    </select></div>
									<div class="col-sm-3 text-nowrap text-primary">
									<button class="btn btn-primary d-block w-100" type="submit">Salvar Equipamento</button>
									</div>
                                </div>
                                <hr>
                               </form>
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Lista de Equipamento Cadastrados.</p>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                            </div>
								<!-- Modal -->
 								<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h2 class="modal-title" id="myModalLabel">Exclus&atilde;o de Equipamento</h2>
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
											<th>Hostname</th>
											<th>IP address</th>
											<th>Modelo</th>
											<th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody>
	<?php
	$sql = "SELECT * FROM routers";
	if($stmt = $pdo->prepare($sql)){
    // Tente executar a declaração preparada
        if($stmt->execute()){
            $res = $stmt -> fetchAll(PDO::FETCH_OBJ);
            foreach($res as $router){ 
					?>
                        <tr Class="btnDelete" data-id="<?= $router -> id; ?>" >
                            <td><pre class='text-primary'><?= $router -> id; ?></td>
                            <td><pre class='text-primary'><?= $router -> hostname; ?></td>
							<td><pre class='text-primary'><?= $router -> hostip; ?></td>
							<td><pre class='text-primary'><?= $router -> modelo; ?></td>
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
                    <div class="text-center my-auto copyright"><span>Copyright Â© Flowspec Solutions 2023</span></div>
                </div>
            </footer>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/script.min.js"></script>
    <script src="assets/js/script.route.js"></script>
</body>

</html>