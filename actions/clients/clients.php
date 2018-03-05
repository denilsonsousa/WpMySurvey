<?php
	 //Recebendo os POSTS
require( $_SERVER["DOCUMENT_ROOT"].'/site_esteio/wp-load.php' );
//require_once(MYSURVEY_ROOT_PATH.'\wp-my-survey-class.php');
//$WP_My_Survey = new WP_My_Survey();

if(!empty($_POST['type'])){

	 $type = $_POST['type'];
	 if ($type == 'new')
	 {
		//recebendo novo usuario 
		$parameters = "";
		foreach ($_POST as $parameter => $value) {
			$parameters .= " $parameter='$value'";
		}
		if(!empty($_POST['client']) && !empty($_POST['email']) && !empty($_POST['company'])){
			$id = do_shortcode("[new_client $parameters]");
			if($id){
				$response =array(
					'id' => "$id",
					'status' => "ok",
				    'msg' => "Cliente inserido com sucesso!"
				);
			}else{
				$response =array(
					'status' => "error",
				    'msg' => "Erro na base de dados"
				);
			}
			header("Content-Type: application/json", true);

			echo json_encode($response);
		}else{
			$response = array(
			    'status' => "error",
			    'msg' => "Preencha todos os campos obrigatorios!" 
			);

			header("Content-Type: application/json", true);

			echo json_encode($response);
		}
	 }elseif ($type == 'list_clients'){
	 	$parameters = "";
		foreach ($_POST as $parameter => $value) {
			$parameters .= " $parameter='$value'";
		}
		
		echo do_shortcode("[list_clients $parameters]");
	 }elseif ($type == 'fillform')
	 {
		$parameters = "";
		foreach ($_POST as $parameter => $value) {
			$parameters .= " $parameter='$value'";
		}
		if(!empty($_POST['id'])){
			$response = do_shortcode("[load_client $parameters]");
			
			header("Content-Type: application/json", true);

			echo $response;
		}else{
			$response = array(
			    'status' => "error",
			    'msg' => "Não foi possivel carregar os dados do Cliente!" 
			);

			header("Content-Type: application/json", true);

			echo json_encode($response);
		}
	 }
 }
?>