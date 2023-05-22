<?php
/* Credenciais do banco de dados. LEMBRAR DE TROCAR SENHA NO DB_PASSWORD */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'logcgnat');
define('DB_PASSWORD', 'SUA SENHA');
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