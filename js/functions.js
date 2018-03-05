$(function () {
	$.fn.smoothscroll = function (id) {
    	$('html, body').animate({
    		scrollTop: $('#'+id).offset().top
    	},800);
    };

    $.fn.pagination = function (page,div,url_page) {
    	$('#pagination_loader').html('<div class="progress"><div class="indeterminate"></div></div>');
    	$.fn.smoothscroll('pagination_loader');
        $.ajax({	
			type: "POST",
			url: url_page,
			data: 'type='+div+'&page='+page+'&div='+div+'&max_per_page=' + $('#'+div+'_max_per_page').val(),
			datatype: 'json',
			success: function(response){
				$('#div_'+div).html("<center>"+response+"</center>");
				$('.tooltipped').tooltip({delay: 50});	
			},
			error: function(e){
				$('#pagination_loader').html('');
				var toastContainer = '<a class="btn btn-floating pulse red"><i class="material-icons">error</i></a>&nbsp;<span>Erro ao carregar os dados!</span>';
				Materialize.toast(toastContainer, 4000, 'rounded')
			}
		});
    };
    $.fn.send = function (attr) {
		var data_values = "";
		var table_row = "";
		var form_error = false;

		$("#"+attr.form+" input"+",#"+attr.form+" textarea").each(function(){
			data_values += "&" + $(this).attr('id') + "=" +$(this).val();
			if(($(this).attr('required')=="required"))
				table_row += '<td>'+$(this).val()+'</td>';
		});
		//alert(table_row);
	
		$.ajax({
			
			type: "POST",
			//url: '../wp-admin/options-general.php?page=My+Survey',
			url: attr.MYSURVEY_PLUGIN_URL+'actions/'+attr.page+'/'+attr.page+'.php',
			data: 'type='+$("#btn_send").val()+data_values,
			datatype: 'json',
			success: function(response){
				if(response.status != "error"){
					if ($("#btn_send").val() != "edit")
						$('#tb-clients tbody').append('<tr>'+table_row+'</tr>');
					else
						

					//alert('#tr-tb-'+attr.page+'-'+$("#ID").val());
					var toastContainer = '<a class="btn btn-floating pulse"><i class="material-icons">done</i></a>&nbsp;<span>'+response.msg+'</span>';
					Materialize.toast(toastContainer, 4000, 'rounded');
					
					$.fn.smoothscroll('tr-tb-'+attr.page+'-'+$("#ID").val());

					$("#"+attr.form+" input"+",#"+attr.form+" textarea").each(function(){
						if ($("#btn_send").val() == "edit")
							$('#td-tb-'+attr.page+'-'+$(this).attr('id')+'-'+$("#ID").val()).html($(this).val());

						$(this).val('');
					});

					$('#form_title').html(attr.form_title);
					$('#btn_send').val("new");
        			$('#btn_send').html("Cadastrar");
        			$('.collapsible').collapsible('close', 0);
				}else{
					var toastContainer = '<a class="btn btn-floating pulse red"><i class="material-icons">error</i></a>&nbsp;<span>'+response.msg+'</span>';
					Materialize.toast(toastContainer, 4000, 'rounded');
				}
			},
			error: function(e){
				$('#md_fail').modal('open');
				document.getElementById("error_code").innerHTML = e.responseText;
			}
		});
    };
    $.fn.edit = function (attr) {
    	//$('#pagination_loader').html('<div class="progress"><div class="indeterminate"></div></div>');
    	$('#form_loader').html('<div class="progress"><div class="indeterminate"></div></div>');
    	$('#form_title').html(attr.form_title);
    	$('.collapsible').collapsible('close', 0);
        $('.collapsible').collapsible('open', 0);
        $('#btn_send').val("edit");
        $('#btn_send').html("Editar");
        $.fn.smoothscroll(attr.page);

        var data_values = "";
		var table_row = "";
		var form_error = false;
		var parameters = "";

		
        
        $.ajax({
			type: "POST",
			//url: '../wp-admin/options-general.php?page=My+Survey',
			url: attr.MYSURVEY_PLUGIN_URL+'actions/'+attr.page+'/'+attr.page+'.php',
			data: 'type=fillform&id='+attr.id+parameters,
			datatype: 'json',
			success: function(response){
				if(response.status != "error"){
					$("#"+attr.form+" input"+",#"+attr.form+" textarea").each(function(){
						$(this).val(response[$(this).attr('id')]);
						//alert($(this).attr('id')+"="+response[$(this).attr('id')]);
						Materialize.updateTextFields();
					});
					$('#form_loader').html("");

				}else{
					var toastContainer = '<a class="btn btn-floating pulse red"><i class="material-icons">error</i></a>&nbsp;<span>'+response+'</span>';
					Materialize.toast(toastContainer, 4000, 'rounded');
					$('#form_loader').html('');
				}
			},
			error: function(e){
				$('#md_fail').modal('open');
				document.getElementById("error_code").innerHTML = e.responseText;
				$('#form_loader').html('');
			}
		});
    };
});