

$(document).ready(function(){

  // Cargar datos:
  var table_companies = $('#table_companies').dataTable({
  	"ajax": "Formulario_alumno_data.php?job=get_alumnos",
  	"columns": [
    { "data": "cedula"},
    { "data": "nombre"},
    { "data": "carrera"},
    { "data": "mencion"},
    { "data": "plan"},
    { "data": "actividad"},
    { "data": "sexo"},
    { "data": "edocivil"},
    { "data": "fechanac"},
    { "data": "edad"},
    { "data": "direccion"},
    { "data": "telefonoh"},
    { "data": "telefonoc"},
    { "data": "telefonot"},
    { "data": "email"},
    { "data": "ingreso"},
    { "data": "turno"},
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

  // Delete alumno
$(document).on('click', '.function_delete a', function(e){
  	e.preventDefault();
  	var nombre = $(this).data('name');
 	  var id = $(this).data('id');

   Borrar(id,nombre);
   function Borrar(valor,nombre)
   {
      swal({
      title: "Desea borrar la alumno?",
      text: nombre,
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#4D759E',
      confirmButtonText: 'No',
      cancelButtonText: 'Si',
      closeOnConfirm: false,
      closeOnCancel: false
   },
   function(isConfirm){
   if (isConfirm){

     window.location = 'Formulario_alumno_lista.php';

   } else {
     show_loading_message();
     $.post("Formulario_alumno_eliminar.php",{accion: "Borrar", id:valor},function(res){

     swal(res);
     if (res=="registro Borrado"){
       hide_loading_message();
       swal({   title: res,   text: "",   type: "success",   showCancelButton: false,   confirmButtonColor: "#4D759E",   confirmButtonText: "ok",   closeOnConfirm: false }, function(){
                window.location = 'Formulario_alumno_lista.php';
        });
       }
     });
    }
   });
  }
});
});


