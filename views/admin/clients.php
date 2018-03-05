<div class="wrap">
  	<script src="<?php echo(MYSURVEY_PLUGIN_URL) ?>/js/jquery.js"></script>
  	<link rel="stylesheet" href="<?php echo(MYSURVEY_PLUGIN_URL) ?>/css/materialize.css">
  	<script src="<?php echo(MYSURVEY_PLUGIN_URL) ?>/js/materialize.js"></script>
  	<link href="<?php echo(MYSURVEY_PLUGIN_URL) ?>/css/icons.css" rel="stylesheet">
	

	<div>
		<h3>Painel Administrativo do My Survey </h3>
		</br>
		<div class="container-fluid">
			<div>	
			  <div class="row">
			    <div class="col s12">
			      <ul class="tabs tabs-fixed-width z-depth-1">
			        <li class="tab col s3"><a class="active" href="#clients">Clientes</a></li>
			        <li class="tab col s3"><a href="#services">Serviços</a></li>
			        <li class="tab col s3"><a href="#surveys">Questionários</a></li>
			      </ul>
			    </div>
			    <div id="clients" class="col s12">
					<ul class="collapsible popout" data-collapsible="accordion">
						<li>
					      <div class="collapsible-header waves-effect  z-depth-1"><i class="material-icons">add</i><span id="form_title">Novo Cliente</span></div>
					      <div class="collapsible-body grey lighten-5">
					      	<div class="row">
					      		<form id="form_client" class="col s12" novalidate="novalidate">
									<div class="input-field col s6">
							          <input id="client" type="text" required="required" aria-required="true" class="validate">
							          <label for="client">Cliente</label>
							        </div>
							        <div class="input-field col s6">
							          <input id="email" type="email" required="required" aria-required="true" class="validate">
							          <label for="email" data-error="E-mail inválido." >E-mail</label>
							        </div>
							        <div class="input-field col s12">
							          <input id="company" type="text" required="required" aria-required="true" class="validate">
							          <label for="company">Empresa</label>
							        </div>
							        <div class="input-field col s12">
							          <textarea id="description"   class="materialize-textarea"></textarea>
							          <label for="description">Descrição</label>
							        </div>
							        <input id="ID" type="text" required="required" hidden="hidden">
						    	</form>
							</div>
							<div class="row">
								<?php
		$attr = array(
			    'MYSURVEY_PLUGIN_URL' => MYSURVEY_PLUGIN_URL,
			    'form' => "form_client",
			    'page' => "clients",
			    'form_title' => "Novo Cliente"
			);
		$attr = json_encode($attr);
								?>
								<button id="btn_send" value="new" onclick='$(this).click($.fn.send(<?php echo $attr; ?>));' class="btn btn-default">Cadastrar</button>
								<div id="form_loader"></div>
							</div>	
					      </div>
					    </li>
					</ul>
					<div id="div_list_clients" class="row ">
						<center>
							<?php echo do_shortcode("[list_clients]"); ?>
						</center>
					</div>
				</div>
			    <div id="services" class="col s12">Test 2</div>
			    <div id="surveys" class="col s12">Test 4</div>
			  </div>
			</div>
		</div>
	</div>         
	<!-- Modal -->
	<div id="md_fail" class="modal">
		<div class="modal-content">
			<h4>Erro!</h4>
			<p>Não foi possivel cadastrar o cliente</p>
			<span id="error_code"></span>
		</div>
		<div class="modal-footer">
			<a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Fechar</a>
		</div>
	</div>
          
	<script src="<?php echo(MYSURVEY_PLUGIN_URL) ?>/js/functions.js"></script>
</div>