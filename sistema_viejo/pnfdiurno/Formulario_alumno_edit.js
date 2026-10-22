

$(document).ready(function(){
  

  var table_companies = $('#table_companies').dataTable({
    "ajax": "Formulario_alumno_edit.php?job=get_companies",
    "columns": [
      { "data": "codigo"},
      { "data": "cedula"},
      { "data": "nombre" },
      { "data": "carrera"},
      { "data": "mencion"},
      { "data": "plan"},
      { "data": "turno"},
      { "data": "actividad"},
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
      "sInfo":          "Total de _TOTAL_ Registros (mostrando _START_ hasta _END_)",
      "sInfoFiltered":  "(filtered from _MAX_ total records)"
    }
  });
  
  
  var form_alumno = $('#form_alumno');
  form_alumno.validate();

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

  // Add company button
  $(document).on('click', '#add_company', function(e){
    e.preventDefault();
    $('.lightbox_content h2').text('Agregar alumno');
    $('#form_alumno button').text('Agregar');
    $('#form_alumno').attr('class', 'form add');
    $('#form_alumno').attr('data-id', '');
    $('#form_alumno .field_container label.error').hide();
    $('#form_alumno .field_container').removeClass('valid').removeClass('error');
    $('#form_alumno #codigo').val('');
    $('#form_alumno #cedula').val('');
    $('#form_alumno #nombre').val('');
    $('#form_alumno #carrera').val('');
    $('#form_alumno #mencion').val('');
    $('#form_alumno #plan').val('');
    $('#form_alumno #turno').val('');
    $('#form_alumno #actividad').val('');
    show_lightbox();
  });

  // Add company submit form
  $(document).on('submit', '#form_alumno.add', function(e){
    e.preventDefault();
    // Validate form
    if (form_alumno.valid() == true){
      // Send company information to database
      hide_ipad_keyboard();
      hide_lightbox();
      show_loading_message();
      var form_data = $('#form_alumno').serialize();
      var request   = $.ajax({
        url:          'Formulario_alumno_edit.php?job=add_company',
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
            var cedula = $('#cedula').val();
            show_message("Company '" + cedula + "' added successfully.", 'success');
          }, true);
        } else {
          hide_loading_message();
          show_message('Add request failed', 'error');
        }
      });
      request.fail(function(jqXHR, textStatus){
        hide_loading_message();
        show_message('Add request failed: ' + textStatus, 'error');
      });
    }
  });

  // Edit company button
  $(document).on('click', '.function_edit a', function(e){
    e.preventDefault();
    // Get company information from database
    show_loading_message();
    var id      = $(this).data('id');
    var request = $.ajax({
      url:          'Formulario_alumno_edit.php?job=get_company',
      cache:        false,
      data:         'id=' + id,
      dataType:     'json',
      contentType:  'application/json; charset=utf-8',
      type:         'get'
    });
    request.done(function(output){
      if (output.result == 'success'){
        $('.lightbox_content h2').text('Editar alumno');
        $('#form_alumno button').text('Editar');
        $('#form_alumno').attr('class', 'form edit');
        $('#form_alumno').attr('data-id', id);
        $('#form_alumno .field_container label.error').hide();
        $('#form_alumno .field_container').removeClass('valid').removeClass('error');
        $('#form_alumno #codigo').val(output.data[0].codigo);
        $('#form_alumno #cedula').val(output.data[0].cedula);
        $('#form_alumno #nombre').val(output.data[0].nombre);
        $('#form_alumno #carrera').val(output.data[0].carrera);
        $('#form_alumno #mencion').val(output.data[0].mencion);
        $('#form_alumno #plan').val(output.data[0].plan);
        $('#form_alumno #turno').val(output.data[0].turno);
        $('#form_alumno #actividad').val(output.data[0].actividad);
        hide_loading_message();
        show_lightbox();
      } else {
        hide_loading_message();
        show_message('Information request failed', 'error');
      }
    });
    request.fail(function(jqXHR, textStatus){
      hide_loading_message();
      show_message('Information request failed: ' + textStatus, 'error');
    });
  });
  
  // Edit company submit form
  $(document).on('submit', '#form_alumno.edit', function(e){
    e.preventDefault();
    // Validate form
    if (form_alumno.valid() == true){
      // Send company information to database
      hide_ipad_keyboard();
      hide_lightbox();
      show_loading_message();
      var id        = $('#form_alumno').attr('data-id');
      var form_data = $('#form_alumno').serialize();
      var request   = $.ajax({
        url:          'Formulario_alumno_edit.php?job=edit_company&id=' + id,
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
            var cedula = $('#cedula').val();
            show_message("Company '" + cedula + "' edited successfully.", 'success');
          }, true);
        } else {
          hide_loading_message();
          show_message('Edit request failed', 'error');
        }
      });
      request.fail(function(jqXHR, textStatus){
        hide_loading_message();
        show_message('Edit request failed: ' + textStatus, 'error');
      });
    }
  });
  
  // Delete company
  $(document).on('click', '.function_delete a', function(e){
    e.preventDefault();
    var cedula = $(this).data('name');
    
    /*********************************************************/

   if (confirm("Desea borrar el alumno '" + cedula + "'?")){
      show_loading_message();
      var id      = $(this).data('id');
      var request = $.ajax({
        url:          'Formulario_alumno_edit.php?job=delete_company&id=' + id,
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
            show_message("Company '" + cedula + "' deleted successfully.", 'success');
          }, true);
        } else {
          hide_loading_message();
          show_message('Delete request failed', 'error');
        }
      });
      request.fail(function(jqXHR, textStatus){
        hide_loading_message();
        show_message('Delete request failed: ' + textStatus, 'error');
      });
    }



    /*********************************************************/





  });

});