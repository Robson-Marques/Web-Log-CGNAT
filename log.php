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
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil</a><a class="dropdown-item" href="table.php"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Log CGNAT</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Consultar Log CGNAT</h3>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 fw-bold">Insira os dados para consulta.</p>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
				<form action="" method="post">
                                <div class="row">
								    <div class="col-sm-3 col-xxl-3 text-nowrap text-primary" style="width: 280px;"><label class="col-form-label form-label form-label form-label">Hostname:&nbsp;<select class="form-select-sm d-inline-block form-select" style="width: 190px;" name="hostname">
                                        <?php
											$sql = "SELECT * FROM routers";
											if($stmt = $pdo->prepare($sql)){
											// Tente executar a declaração preparada
												if($stmt->execute()){
													$res = $stmt -> fetchAll(PDO::FETCH_OBJ);
													foreach($res as $router){ 
										?>
												<option value="<?= $router -> hostname; ?>"><?= $router -> hostname; ?></option>

										<?php
													}
												}
												// Fechar declaração
												unset($stmt);
											}
											// Fechar conexão
											unset($pdo);
										?>
                                            </select></label></div>
									<div class="col-sm-2 col-xxl-2 text-nowrap text-primary" style="width: 180px;"><label class="col-form-label form-label form-label form-label">Tipo Log:&nbsp;<select class="form-select-sm d-inline-block form-select" style="width: 100px;" name="log">
                                                <option value="flow" selected="">FLOW</option>
                                                <option value="syslog">SYSLOG</option>
                                            </select></label></div>
                                    <div class="col-sm-2 text-nowrap text-primary" style="width: 175px;"><label class="col-form-label form-label form-label form-label form-label">Data:&nbsp;<input class="form-control-sm d-inline-block form-control" type="date" style="width: 120px;height: 31px;color: rgb(133,135,150);border-radius: 4px;border-width: 1px;font-size: 14px;padding-bottom: 5px;padding-left: 10px;margin-bottom: -1px;" name="data"></label></div>
                                    <div class="col-sm-1 col-xxl-1 text-nowrap text-primary" style="width: 125px;"><label class="col-form-label form-label form-label form-label">Hora:&nbsp;<select class="form-select-sm d-inline-block form-select" style="width: 70px;" name="hora">
                                                <option value="00" selected="">00</option>
                                                <option value="01">01</option>
                                                <option value="02">02</option>
                                                <option value="03">03</option>
                                                <option value="04">04</option>
                                                <option value="05">05</option>
                                                <option value="06">06</option>
                                                <option value="07">07</option>
                                                <option value="08">08</option>
                                                <option value="09">09</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                            </select></label></div>
                                    <div class="col-sm-2 text-nowrap text-primary" style="width: 175px;"><label class="col-form-label form-label form-label">IP:&nbsp;<input class="form-control-sm d-inline-block form-control" type="text" minlength="7" maxlength="45" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$" placeholder="XXX.XXX.XXX.XXX" style="margin-right: -61px;padding-right: 6px;" name="ip"></label></div>
                                    <div class="col-sm-1 text-nowrap text-primary" style="width: 140px;"><label class="col-form-label form-label form-label">Porta:&nbsp;<input class="form-control-sm d-inline-block form-control" type="number" max="65535" min="0" placeholder="XXXXX" style="margin-right: -44px;width: 80px;" name="porta"></label></div>
                                    <div class="col-sm-2" style="width: 155px;">
                                        <div style="width: 135px;"><input class="btn btn-primary" type="submit" value="Consultar" style="margin-top: 3px;"></div>
                                    </div>
                                </div>
				</form>
                            </div>
                            <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info">
                                <table class="table my-0" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Filtro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
						<?php
						$mod_data=$_POST['data'];
						$mod_dia=date('d', strtotime($mod_data));
						$mod_mes=date('m', strtotime($mod_data));
						$mod_ano=date('Y', strtotime($mod_data));
						$command = "./script/nfdump.sh ".$mod_ano." ".$mod_mes." ".$mod_dia." ".$_POST['hora']." ".$_POST['ip']." ".$_POST['porta']." ".$_POST['hostname']." ".$_POST['log'];
                                                echo "<pre class='text-primary'>";
                                                $output = shell_exec($command);
                                                echo $output;
                                                ?>
						</td>
                                        </tr>
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
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright Â© Robson Marques 2023</span></div>
                </div>
            </footer>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>