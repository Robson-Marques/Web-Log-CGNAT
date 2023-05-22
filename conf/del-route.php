<?php 
// Incluir arquivo de configuração
require_once "config.php";

	if(!empty(trim($_POST["acao"]))){
		$data = array('success' => '0',
                  'msg' => 'Ocorreu um erro, nada foi excluido!');
				$id = (int) $_POST['id'];
			if ($_POST['acao'] == 'delete' && is_int($id)) {
				$sql = "DELETE FROM routers WHERE id=?";
				if($stmt = $pdo->prepare($sql)){
					// Tente executar a declaração preparada
					if($stmt->execute([$id])){
						$data = array('success' => '1',
								  'msg' => 'Registro excluido com sucesso!');
					} 
				}
			}
		echo json_encode($data);
	die();
	}
	?>