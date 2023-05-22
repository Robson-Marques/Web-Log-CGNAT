<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - CGNAT</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
	<link rel="stylesheet" href="assets/icons/font/bootstrap-icons.min.css">
</head>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once "conf/config.php";
// Inicialize a sessão
session_start();
 
// Verifique se o usuário está logado, caso contrário, redirecione para a página de login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$Nivel = $_SESSION["Nivel"] ;
$Nome = $_SESSION["Nome"] ;

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
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">Dashboard</h3><a class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button" href="#"><i class="fas fa-download fa-sm text-white-50"></i>&nbsp;Generate Report</a>
                    </div>
                    <div class="row">
                        <div class="col-xl">
                            <div class="card shadow mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                   <ul class="nav nav-tabs" id="myTab" role="tablist">	
										<?php
											$sql = "SELECT * FROM routers";
											if($stmt = $pdo->prepare($sql)){
											// Tente executar a declaração preparada
												if($stmt->execute()){
													$res = $stmt -> fetchAll(PDO::FETCH_OBJ);
													foreach($res as $router){ 
										?>										
											<li class="nav-item" role="presentation">
											<button class="nav-link" id="<?= $router -> hostname; ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $router -> hostname; ?>-tab-pane" type="button" role="tab" aria-controls="<?= $router -> hostname; ?>-tab-pane" aria-selected="true">Top 20 do dia <?= $router -> hostname; ?></button>
											</li>
										<?php
													}
										?>
									</ul>
								</div>
								<div class="card shadow mb-4">
									<div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
									<table class="table my-0" id="dataTable">
                                    <tbody>
									<div class="tab-content" id="myTabContent">
									<?php
										foreach($res as $router){ 
									?>						
										<div class="tab-pane fade" id="<?= $router -> hostname; ?>-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
											<?php
												$tipo = $router -> tipo_log;
												$hostname = $router -> hostname;
												$agora = new DateTime();
												$agora->sub(new DateInterval('PT1H'));
												$Dia = $agora->format('d');
												$Mes = $agora->format('m');
												$Ano = $agora->format('Y');
												$Hora_C = $agora->format('H00');
												$Hora = $agora->format('H');
												if($tipo == "flow"){
													echo "<pre class='text-primary'>";
													$output = shell_exec("nfdump -r /var/log/cgnat/flow/$hostname/$Ano/$Mes/$Dia/nfcapd.$Ano$Mes$Dia$Hora_C -c 20 &");
													echo $output;
													echo "</pre>";
												}else{
													echo "<pre class='text-primary'>";
													$output = shell_exec("tail -n 20 /var/log/cgnat/syslog/$hostname/$Ano/$Mes/$Dia/server-$Hora.log | sed 's/<//g; s/>/->/g' &");
													echo $output;
													echo "</pre>";
												}
					                                                ?>
										</div>
										<?php
													}
										?>
									</div>
									<?php
																					
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
    <script src="assets/js/chart.min.js"></script>

</body>

</html>