<?php
/**
 * Plugin Name: WP My Survey
 * Plugin URI: esteio.com.br
 * Description: 
 *		(EN)	Plugin used to send satisfaction surveys to clients, 
 *				as well as show and manage the results of these sent surveys. 		
 * 		
 *		(PT-BR)	Plugin com a finalidade de enviar questionarios de satisfacao aos clientes,
 * 				bem como mostrar e gerenciar os resultadados dos questionarios enviados.
 * Version: 1.0
 * Author: Denilson Andre de Sousa
 * Author URI: deni.nilso@gmail.com
 */
 
 //defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

 //Inicialização
define( 'MYSURVEY_ROOT_PATH', dirname( __FILE__ ) );

function my_survey_new_client( $atts)
 {		
	global $wpdb;
    $newClient = shortcode_atts( array(
        'client' => 'client',
        'description' => 'description',
        'email' => 'email',
        'company' => 'company',
		), $atts );
		
	$wpdb->query("INSERT INTO `{$wpdb->prefix}ms_clients`(`client`,`description`, `email`, `company`) VALUES ('".$newClient['client']."','".$newClient['description']."','".$newClient['email']."','".$newClient['company']."')");
	$response =array(
	    'id' => $wpdb->insert_id
	);
	echo json_encode($response);
 }
 add_shortcode( 'new_client', 'my_survey_new_client' );
 
function my_survey_list_clients()
{
	global $wpdb;
	$clients = $wpdb->get_results( 
	"
	SELECT * FROM  {$wpdb->prefix}ms_clients
	"
	);
	echo <<<EOT
	<table id="tb-clients" class="highlight responsive-table white z-depth-1">
    	<thead>
      		<tr>	
      			<th>ID</th>
	  			<th>Nome</th>
				<th>Email</th>
				<th>Empresa</th>
	      	</tr>
	    </thead>
	    <tbody>
EOT;
foreach ( $clients as $client ) 
	{
		echo <<<EOT
			<tr>
				<td>{$client->ID}</td>
				<td>{$client->client}</td>
				<td>{$client->email}</td>
				<td>{$client->campany}</td>
			</tr>   
EOT;
	}
	echo <<<EOT
		</tbody>
	</table>      
EOT;
}
add_shortcode( 'list_clients', 'my_survey_list_clients' );

function my_survey_install()  
 {
	global $wpdb;
	$wpdb->query('CREATE TABLE '.$wpdb->prefix.'ms_survey (`ID` int(11) unsigned NOT NULL auto_increment, `name` varchar(255) NOT NULL, `description` text, PRIMARY KEY  (`ID`), UNIQUE KEY `ID` (`ID`))');
	$wpdb->query('CREATE TABLE '.$wpdb->prefix.'ms_questions (`ID` int(11) unsigned NOT NULL auto_increment, `number` int(11) NOT NULL, `type` enum(\'r\',\'c\',\'d\') NOT NULL, `question` text NOT NULL, `fk_survey` int(11) unsigned NOT NULL, PRIMARY KEY  (`ID`), UNIQUE KEY `ID` (`ID`), KEY `fk_survey` (`fk_survey`), CONSTRAINT `question_fk` FOREIGN KEY (`fk_survey`) REFERENCES `'.$wpdb->prefix.'ms_survey` (`ID`))');
	$wpdb->query('CREATE TABLE '.$wpdb->prefix.'ms_services ( `ID` INT unsigned NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL ,`description` TEXT, `start_date` DATE DEFAULT NULL, `date_last_survey` DATE DEFAULT NULL, PRIMARY KEY (`ID`), `fk_user` int(11) unsigned NOT NULL, KEY `fk_user` (`fk_user`), CONSTRAINT `user_fk` FOREIGN KEY (`fk_user`) REFERENCES `'.$wpdb->prefix.'users` (`ID`))');
	$wpdb->query('CREATE TABLE '.$wpdb->prefix.'ms_clients ( `ID` INT unsigned NOT NULL AUTO_INCREMENT , `client` VARCHAR(255) NOT NULL ,`description` TEXT, `email` VARCHAR(255), `company` VARCHAR(255), PRIMARY KEY (`ID`))');//, `fk_service` int(11) unsigned NOT NULL, KEY `fk_service` (`fk_service`), CONSTRAINT `service_fk` FOREIGN KEY (`fk_service`) REFERENCES `'.$wpdb->prefix.'ms_services` (`ID`))');
 }
 
 //Desistalação
 function my_survey_unistall() 
 {
	global $wpdb;
	$wpdb->query("DROP TABLE {$wpdb->prefix}ms_questions");
	$wpdb->query("DROP TABLE {$wpdb->prefix}ms_survey");
	$wpdb->query("DROP TABLE {$wpdb->prefix}ms_clients");
	$wpdb->query("DROP TABLE {$wpdb->prefix}ms_services");
 }
 
 register_activation_hook(__FILE__, 'my_survey_install');
 register_deactivation_hook( __FILE__, 'my_survey_unistall');
 
//add_shortcode( 'list_clients', 'my_survey_list_clients' );
 
 //**************Adicionando menu no Painel Administrativo***************
 
 /** Step 2 (from text above). */
add_action( 'admin_menu', 'my_survey_plugin_menu' );

/** Step 1. */
function my_survey_plugin_menu() {
	add_options_page( 'My Survey - Opções', 'My Survey', 'manage_options', 'My Survey', 'my_survey_plugin_options' );
}

/** Step 3. */
function my_survey_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	include_once(MYSURVEY_ROOT_PATH.'\views\admin\clients.php');
}
 
 //Recebendo os POSTS
if(!empty($_POST['type'])){
	 $type = $_POST['type'];
	 if ($type == 'new_client')
	 {
		//recebendo novo usuario 
		$parameters = "";
		foreach ($_POST as $parameter => $value) {
			$parameters .= " $parameter='$value'";
		}
		if(!empty($_POST['client']) && !empty($_POST['email']) && !empty($_POST['company'])){
			do_shortcode("[new_client $parameters]");
		}else{
			$response =array(
			    'id' => MYSURVEY_ROOT_PATH
			);

			header("Content-Type: application/json", true);

			echo json_encode($response);
		}
	 }
	 if($type == 'vincularUrl')
	 {
		$user = $_POST['userUrl'];
		$url = $_POST['url'];
	 }
	 if($type == 'receberLogin')
	 {
		$login = $_POST['login'];
		$senha = $_POST['senha'];
		do_shortcode("[validar_user login='$login' pass='$senha']");
	 }
 }
 ?>