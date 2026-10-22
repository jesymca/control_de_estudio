

$(document).ready(function(){

  // Cargar datos:
  var table_companies = $('#table_companies').dataTable({
  	"ajax": "Formulario_lapso_auditoria_data.php?job=get_companies",
  	"columns": [
    { "data": "accion"},
    { "data": "cod"},
    { "data": "usuario"},
    { "data": "lapso"},
    { "data": "descrip"},
    { "data": "hora"},
    { "data": "fecha"},
  	{ "data": "functions",      "sClass": "functions" }
  	],
  	"aoColumnDefs": [
  	{ "bSortable": false, "aTargets": [-1] }
  	],
  	"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
  	"oLanguage": {
  		"oPaginate": {
  			"sFirst":       " ",
  			"sPrevious":    " ",
  			"sNext":        " ",
  			"sLast":        " ",
  		},
  		"sLengthMenu":    "Registro por pagina: _MENU_",
  		"sInfo":          "Total of _TOTAL_ registro (mostrando _START_ cada _END_)",
  		"sInfoFiltered":  "(filtered from _MAX_ total records)"
  	}
  });


  // Show message
  function show_message(message_text, message_type){
  	$('#message').html('<p>' + message_text + '</p>').attr('class', message_type);
  	$('#message_container').show();
  	if (typeof timeout_message !== 'undefined'){
  		window.clearTimeout(timeout_message);
  	}
  	timeout_message = setTimeout(function(){
  		hide_message();
  	}, 2);
  }
  // Hide message
  function hide_message(){
  	$('#message').html('').attr('class', '');
  	$('#message_container').hide();
  }

  // Show loading message
  function show_loading_message(){
  	$('#loading_container').show();
  }
  // Hide loading message
  function hide_loading_message(){
  	$('#loading_container').hide();
  }

  // Show lightbox
  function show_lightbox(){
  	$('.lightbox_bg').show();
  	$('.lightbox_container').show();
  }
  // Hide lightbox
  function hide_lightbox(){
  	$('.lightbox_bg').hide();
  	$('.lightbox_container').hide();
  }
  // Lightbox background
  $(document).on('click', '.lightbox_bg', function(){
  	hide_lightbox();
  });
  // Lightbox close button
  $(document).on('click', '.lightbox_close', function(){
  	hide_lightbox();
  });
  // Escape keyboard key
  $(document).keyup(function(e){
  	if (e.keyCode == 27){
  		hide_lightbox();
  	}
  });
  
  // Hide iPad keyboard
  function hide_ipad_keyboard(){
  	document.activeElement.blur();
  	$('input').blur();
  }

  // Add lapso_auditoria button
  $(document).on('click', '#add_company', function(e){
  	e.preventDefault();
  	$('.lightbox_content h2').text('Agregar Lapso Auditoria');
  	$('#form_lapso_auditoria button').text('Agregar');
  	$('#form_lapso_auditoria').attr('class', 'form add');
  	$('#form_lapso_auditoria').attr('data-id', '');
  	$('#form_lapso_auditoria .field_container label.error').hide();
  	$('#form_lapso_auditoria .field_container').removeClass('valid').removeClass('error');
    $('#form_lapso_auditoria #accion').val('');
    $('#form_lapso_auditoria #cod').val('');
    $('#form_lapso_auditoria #usuario').val('');
    $('#form_lapso_auditoria #lapso').val('');
    $('#form_lapso_auditoria #descrip').val('');
    $('#form_lapso_auditoria #hora').val('');
    $('#form_lapso_auditoria #fecha').val('');

  	show_lightbox();
  });

  // Add lapso_auditoria submit form
  $(document).on('submit', '#form_lapso_auditoria.add', function(e){
  	e.preventDefault();
    // Validate form
      // Send lapso_auditoria information to database
      hide_ipad_keyboard();
      hide_lightbox();
      show_loading_message();
      var form_data = $('#form_lapso_auditoria').serialize();
      var request   = $.ajax({
      	url:          'Formulario_lapso_auditoria_data.php?job=add_company',
      	cache:        false,
      	data:         form_data,
      	dataType:     'json',
      	contentType:  'application/json; charset=utf-8',
      	type:         'get'
      });
      request.done(function(output){
      	if (output.result == 'success'){
          // Reload datable
          table_companies.api().ajax.reload(function(){
          	hide_loading_message();
           var accion = $('#accion').val();
           show_message("lapso_auditoria '" + accion + "' adicion exitosa.", 'success');
          }, true);
      } else {
      	hide_loading_message();
      	show_message('Error al agregar el registro', 'error');
      }
  });
      request.fail(function(jqXHR, textStatus){
      	hide_loading_message();
      	show_message('Error al agregar el registro: ' + textStatus, 'error');
      });
});

  // Edit lapso_auditoria button
  $(document).on('click', '.function_edit a', function(e){
  	e.preventDefault();
    // Get lapso_auditoria information from database
    show_loading_message();
    var id      = $(this).data('id');
    var request = $.ajax({url:'Formulario_lapso_auditoria_data.php?job=get_company',cache:false,data:'id=' + id,dataType:'json',contentType:'application/json;charset=utf-8',type:'get'});
    request.done(function(output){
    	if (output.result == 'success'){
    		$('.lightbox_content h2').text('Editar Lapso Auditoria');
    		$('#form_lapso_auditoria button').text('Editar');
    		$('#form_lapso_auditoria').attr('class', 'form edit');
    		$('#form_lapso_auditoria').attr('data-id', id);
    		$('#form_lapso_auditoria .field_container label.error').hide();
    		$('#form_lapso_auditoria .field_container').removeClass('valid').removeClass('error');
        $('#form_lapso_auditoria #accion').val(output.data[0].accion);
        $('#form_lapso_auditoria #cod').val(output.data[0].cod);
        $('#form_lapso_auditoria #usuario').val(output.data[0].usuario);
        $('#form_lapso_auditoria #lapso').val(output.data[0].lapso);
        $('#form_lapso_auditoria #descrip').val(output.data[0].descrip);
        $('#form_lapso_auditoria #hora').val(output.data[0].hora);
        $('#form_lapso_auditoria #fecha').val(output.data[0].fecha);

    		hide_loading_message();
    		show_lightbox();
    	} else {
    		hide_loading_message();
    		show_message('Informacion requerida fallida', 'error');
    	}
    });
    request.fail(function(jqXHR, textStatus){
    	hide_loading_message();
    	show_message('Informacion requerida fallida: ' + textStatus, 'error');
    });
});

  // Edit lapso_auditoria submit form
  $(document).on('submit', '#form_lapso_auditoria.edit', function(e){
  	e.preventDefault();
    // Validate form
      // Send lapso_auditoria information to database
      hide_ipad_keyboard();
      hide_lightbox();
      show_loading_message();
      var id        = $('#form_lapso_auditoria').attr('data-id');
      var form_data = $('#form_lapso_auditoria').serialize();
      var request   = $.ajax({
      	url:          'Formulario_lapso_auditoria_data.php?job=edit_company&id=' + id,
      	cache:        false,
      	data:         form_data,
      	dataType:     'json',
      	contentType:  'application/json; charset=utf-8',
      	type:         'get'
      });
      request.done(function(output){
      	if (output.result == 'success'){
          // Reload datable
          table_companies.api().ajax.reload(function(){
          	hide_loading_message();
           var nombre = $('#accion').val();
          	show_message("lapso_auditoria '" + nombre + "' edicion terminada.", 'success');
          }, true);
      } else {
      	hide_loading_message();
      	show_message('Edicion fallida', 'error');
      }
  });
      request.fail(function(jqXHR, textStatus){
      	hide_loading_message();
      	show_message('Edicion fallida: ' + textStatus, 'error');
      });
});

  // Delete lapso_auditoria
  $(document).on('click', '.function_delete a', function(e){
  	e.preventDefault();
  	var nombre = $(this).data('name');
  	if (confirm("Esta seguro de borrar el registro '" + nombre + "'?")){
  		show_loading_message();
  		var id      = $(this).data('id');
  		var request = $.ajax({
  			url:          'Formulario_lapso_auditoria_data.php?job=delete_company&id=' + id,
  			cache:        false,
  			dataType:     'json',
  			contentType:  'application/json; charset=utf-8',
  			type:         'get'
  		});
  		request.done(function(output){
  			if (output.result == 'success'){
          // Reload datable
          table_companies.api().ajax.reload(function(){
          	hide_loading_message();
          	show_message("lapso_auditoria " + nombre + " Borrado exitoso.", 'success');
          }, true);
      } else {
      	hide_loading_message();
      	show_message('Borrado fallido', 'error');
      }
  });
  		request.fail(function(jqXHR, textStatus){
  			hide_loading_message();
  			show_message('Error al borrar el registro: ' + textStatus, 'error');
  		});
  	}
  });
});


