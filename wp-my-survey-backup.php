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
	return $wpdb->insert_id;
 }
 add_shortcode( 'new_client', 'my_survey_new_client' );

function my_survey_pagination($table,$max_per_page,$page,$max_pages,$div_data,$where = ""){
	global $wpdb;
	$pagination = '<ul class="pagination ">';

	$result = $wpdb->get_results("SELECT count(*) AS \"totalRows\" FROM {$wpdb->prefix}$table $where");
	//$totalRows = $totalRows->num_rows;

	foreach ( $result as $totalRow ) 
	{
		$totalRows = "{$totalRow->totalRows}";
	}

	if(is_numeric($totalRows) and is_numeric($max_per_page)){
		$totalPages = ceil($totalRows/$max_per_page);

		if($page<$max_pages){
			if($page == 1)
				$pagination .= '<li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>';
			else
				$pagination .= '<li class="waves-effect"><a href=""><i class="material-icons">chevron_left</i></a></li>';

			for($i = 1; $i <= $totalPages; $i++){
				if($i == $page){
					$class = "active";
				}else{
					$class = "waves-effect";
				}

				$pagination .= '<li class="'. $class .'"><a class="pagination_button" href="#!" onclick="$(this).click($.fn.pagination('.$i.'));">'.$i.'</a></li>';
			}
			$pagination .= '<li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>';
		}
		$pagination .= '</ul>';
	}

	return $pagination;
}

function my_survey_list_clients( $atts )
{
	global $wpdb;
	$atts = shortcode_atts( array(
        'page' => 'page',
        'div' => 'div',
        'max_per_page' => 'max_per_page',
        'max_pages' => 'max_pages',
		), $atts );

	// Default values
	if(!is_numeric($atts['page']))
		$atts['page'] = 1;
	if(!is_numeric($atts['max_per_page']))
		$atts['max_per_page'] = 10;
	if(!is_numeric($atts['max_pages']))
		$atts['max_pages'] = 5;
	if($atts['div'] == 'div')
		$atts['div'] = 'list_clients';

	$offset = ($atts['page']-1) * $atts['max_per_page'];

	$clients = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}ms_clients ORDER BY client LIMIT 10 OFFSET $offset");

	$pagination =  my_survey_pagination("ms_clients",$atts['max_per_page'],$atts['page'],$atts['max_pages'],$atts['div']);
	echo $pagination;
	echo <<<EOT
	<div id="pagination_loader"></div>
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
	echo $pagination; }
add_shortcode( 'list_clients', 'my_survey_list_clients' );
 //Recebendo os POSTS
if(empty($_POST)){
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
}
 
 ?>