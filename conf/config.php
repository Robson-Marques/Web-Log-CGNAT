<?php
/* Credenciais do banco de dados. Supondo que voc� esteja executando o MySQL
servidor com configura��o padr�o (usu�rio 'root' sem senha) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'logcgnat');
define('DB_PASSWORD', '8564@cgnat#7485');
define('DB_NAME', 'LOGCGNAT');
 
/* Tentativa de conex�o com o banco de dados MySQL */
try{
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Defina o modo de erro PDO para exce��o
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: N�o foi poss�vel conectar." . $e->getMessage());
}
?>