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


if ( ! class_exists( 'WP_My_Survey' ) ) {
	/**
	 * Main WP_My_Survey class
	 *
	 * @since       1.0.0
	 */
	class WP_My_Survey {
		/**
		 * [__construct description]
		 */
		function __construct() {
		}

		public static function get_instance() {
			if ( ! self::$instance ) {

				self::$instance = new WP_My_Survey();
			}
			return self::$instance;
		}

		function my_survey_new_client( $atts)
		 {		
			global $wpdb;
				
			$wpdb->query("INSERT INTO `{$wpdb->prefix}ms_clients`(`client`,`description`, `email`, `company`) VALUES ('".$atts['client']."','".$atts['description']."','".$atts['email']."','".$atts['company']."')");
			
			return $wpdb->insert_id;
		 }
		 
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
	}
} 
?>