<?php
// Inicialize a sess�o
session_start();
 
// Remova todas as vari�veis de sess�o
$_SESSION = array();
 
// Destrua a sess�o.
session_destroy();
 
// Redirecionar para a p�gina de login
header("location: login.php");
exit;
?>