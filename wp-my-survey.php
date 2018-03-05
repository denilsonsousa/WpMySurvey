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
define( 'MYSURVEY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MYSURVEY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

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

function my_survey_pagination($table,$max_per_page,$page,$max_pages,$div_data,$url_page,$where = ""){
	global $wpdb;
	$totalPages = 0;
	$totalRows = 0;
	$totalDisplayPages = 0;
	$last_page = 0;
	$first_page = 1;
	$result = null;
	$return = array(
        'div_pagination' => '',
        'div_max_per_page' => '',
		);

	$pagination = '
	<div class="row">
		<div class="col s12 m12">
			<ul class="pagination">';

	$result = $wpdb->get_results("SELECT count(*) AS \"totalRows\" FROM {$wpdb->prefix}$table $where");
	//$totalRows = $totalRows->num_rows;

	foreach ( $result as $totalRow ) 
	{
		$totalRows = "{$totalRow->totalRows}";
	}

	if(is_numeric($totalRows) and is_numeric($max_per_page)){
		$totalPages = ceil($totalRows/$max_per_page);
		$totalDisplayPages = ceil($totalPages/$max_pages);

		//if(($page + $max_pages)<= $totalPages){
			for($j=1;$first_page <= $page;$j++){
				$first_page = $j * $max_pages;
			}
			$first_page -= $max_pages;
			//echo $first_page."(".$page.")";

			// Set the first page
			if(($first_page == 0) and ($page < $max_pages)){
				$first_page = 1;
			}elseif($first_page >= ($totalPages-ceil($max_pages/2))){
				$first_page = $totalPages - ($max_pages-1);
			}elseif(($first_page == $page) and ($page >= $max_pages)){
				$first_page = ($first_page-floor($max_pages/2)); 
			}elseif(($first_page+$max_pages)==($page+1)){
				$first_page = ($page-floor($max_pages/2));
			}

			$last_page = $first_page + ($max_pages - 1);
			if($last_page>$totalPages)
				$last_page = $totalPages;
		
		if($page == 1)
			$pagination .= '<li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>';
		else
			$pagination .= '<li class="waves-effect"><a onclick="$(this).click($.fn.pagination('.($page-1).',\''.$div_data.'\',\''.$url_page.'\'));"><i class="material-icons">chevron_left</i></a></li>';

		// Show the first page option
		if(($page >= $max_pages)){
			$pagination .= '<li class="waves-effect"><a class="pagination_button" href="#!" onclick="$(this).click($.fn.pagination(1,\''.$div_data.'\',\''.$url_page.'\'));">1 ...</a></li>';
		}

		for($i = $first_page; $i <= $last_page; $i++){
			if($i == $page){
				$class = "active";
			}else{
				$class = "waves-effect";
			}

			$pagination .= '<li class="'. $class .'"><a class="pagination_button" href="#!" onclick="$(this).click($.fn.pagination('.$i.',\''.$div_data.'\',\''.$url_page.'\'));">'.$i.'</a></li>';
		}

		// Show the last page option
		if(($first_page + $max_pages) <= $totalPages){
			$pagination .= '<li class="waves-effect"><a class="pagination_button" href="#!" onclick="$(this).click($.fn.pagination('.$totalPages.',\''.$div_data.'\',\''.$url_page.'\'));">... '.$totalPages.'</a></li>';
		}

		if($page<$totalPages)
			$pagination .= '<li class="waves-effect"><a href="#!" onclick="$(this).click($.fn.pagination('.($page+1).',\''.$div_data.'\',\''.$url_page.'\'));"><i class="material-icons">chevron_right</i></a></li>';
		else
			$pagination .= '<li class="disabled"><a href="#!"><i class="material-icons">chevron_right</i></a></li>';
		
		$pagination .= '</ul>';
	}
	$pagination .= '
		</div>
    </div>';
    $div_max_per_page = '
    <div class="col s12">
    	<div class="col m3 offset-l9">
			<p class="range-field">
		      <input type="range" onchange="$(this).click($.fn.pagination(1,\''.$div_data.'\',\''.$url_page.'\'));" id="'.$div_data.'_max_per_page" value="'.$max_per_page.'" min="5" step="5" max="50" class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Resultados por página: '.$max_per_page.'"/>
		    </p>
		</div>
	</div>';

    $return['div_pagination'] = $pagination;
    $return['div_max_per_page'] = $div_max_per_page;
	return $return;
}

function my_survey_list_clients( $atts )
{
	global $wpdb;
	$url_page = MYSURVEY_PLUGIN_URL."actions/clients/clients.php";

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

	$clients = $wpdb->get_results("SELECT ID,client,email,company FROM  {$wpdb->prefix}ms_clients ORDER BY client LIMIT ".$atts['max_per_page']." OFFSET $offset");

	$pagination =  my_survey_pagination("ms_clients",$atts['max_per_page'],$atts['page'],$atts['max_pages'],$atts['div'],$url_page);
	
	//echo $pagination;
	echo $pagination['div_max_per_page'];
	echo <<<EOT
	<div id="pagination_loader"></div>
	<div class="row">
		<table id="tb-clients" class="highlight centered bordered white z-depth-1">
	    	<thead>
	      		<tr>	
	      			<th width="25px">ID</th>
		  			<th>Nome</th>
					<th>Email</th>
					<th>Empresa</th>
					<th width="100px">Ação</th>
		      	</tr>
		    </thead>
		    <tbody>
EOT;
foreach ( $clients as $client ) 
	{
		echo '
		<tr id="tr-tb-clients-'.$client->ID.'">';
		// Show each col from the sql result
		foreach ($client as $label => $col) {
			echo '<td id="td-tb-clients-'.$label.'-'.$client->ID.'">'.$col.'</td>';
		}

		$edit = array(
			    'id' => "{$client->ID}",
			    'page' => "clients",
			    'form' => "form_client",
			    'MYSURVEY_PLUGIN_URL' => MYSURVEY_PLUGIN_URL,
			    'form_title' => "Editando Cliente ({$client->ID})"
			);
		$edit = json_encode($edit);

		echo <<<EOT
				<td>
					<a href="#!" onclick='$(this).click($.fn.edit($edit));'><i class="small material-icons green-text text-darken-4" >edit</i></a>
					<a href="#!" onclick='$(this).click($.fn.edit($edit));'><i class="small material-icons red-text text-darken-4">delete</i></a>
				</td>
			</tr>   
EOT;
	}
	echo <<<EOT
			</tbody>
		</table> 
	</div>	     
EOT;
	echo $pagination['div_pagination']; 
}
add_shortcode( 'list_clients', 'my_survey_list_clients' );

// Load client data
function my_survey_load_client( $atts )
{
	global $wpdb;
	$client = ["status" => "OK"];
	

	$atts = shortcode_atts( array(
        'id' => 'id',
		), $atts );

	$clients = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}ms_clients WHERE ID = ".$atts['id']);

	foreach ( $clients as $row ) {
		foreach ($row as $col => $value) {
			$client += [$col => $value];
		}
	}
	return json_encode($client);
}
add_shortcode( 'load_client', 'my_survey_load_client' );

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