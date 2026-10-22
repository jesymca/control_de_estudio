function LimpiarInput()
{
    id.value ='';
    codigo.value ='';
    cod_mat.value ='';
    nota.value ='';
    lapso.value ='';
    
    cod_doc.value ='';
    acu.value ='';
}
function LlenarDatos(text)
{
    var datos = text.split('|');
    id.value = datos[0];
    codigo.value = datos[1];
    cod_mat.value = datos[2];
    nota.value = datos[3];
    lapso.value = datos[4];
   
    cod_doc.value = datos[6];
    acu.value = datos[7];
}
function obten_datos(arrastre)
{
    id = document.getElementById('id');
    codigo = document.getElementById('codigo');
    cod_mat = document.getElementById('cod_mat');
    nota = document.getElementById('nota');
    lapso = document.getElementById('lapso');
   
    cod_doc = document.getElementById('cod_doc');
    acu = document.getElementById('acu');

    LimpiarInput('id');
    LimpiarInput('codigo');
    LimpiarInput('cod_mat');
    LimpiarInput('nota');
    LimpiarInput('lapso');
   
    LimpiarInput('cod_doc');
    LimpiarInput('acu');

    if (arrastre != 0)
    {
        arrastre.disabled = true;

        id.value = 'Cargando';
        codigo.value = 'Cargando';
        cod_mat.value = 'Cargando';
        nota.value = 'Cargando';
        lapso.value = 'Cargando';
        
        cod_doc.value = 'Cargando';
        acu.value = 'Cargando';

        $.ajax(
        {
            type: 'get',
            dataType: 'text',
            url: 'Formulario notas_buscar.php',
            data:
            {
                valor: arrastre
            },
            success: function (text)
            {
                LlenarDatos(text);
                arrastre.disabled = false;
            }
        }
        );
    }
}


function LimpiarInput_01()
{
    cod_mat.value ='';
}
function LlenarDatos_01(text)
{
    var datos = text.split('|');
    cod_mat.value = datos[3];
}
function llenar_combo_01(arrastre)
{
    cod_mat = document.getElementById('cod_mat');
    LimpiarInput_01('cod_mat');
    if (arrastre != 0)
    {
        arrastre.disabled = true;
        cod_mat.value = 'Cargando';
        $.ajax({type: 'POST',dataType: 'text',url: 'Formulario Lismat_cod_mat_combo_buscar.php',data:{valor_01: arrastre},success:function(text){LlenarDatos_01(text);arrastre.disabled = false;}});
    }
}
function LimpiarInput_02()
{
    lapso.value ='';
}
function LlenarDatos_02(text)
{
    var datos = text.split('|');
    lapso.value = datos[1];
}
function llenar_combo_02(arrastre)
{
    lapso = document.getElementById('lapso');
    LimpiarInput_02('lapso');
    if (arrastre != 0)
    {
        arrastre.disabled = true;
        lapso.value = 'Cargando';
        $.ajax({type: 'POST',dataType: 'text',url: 'Formulario Lapso_lapso_combo_buscar.php',data:{valor_02: arrastre},success:function(text){LlenarDatos_02(text);arrastre.disabled = false;}});
    }
}


function LimpiarInput_04()
{
    tiplap.value ='';
}
function LlenarDatos_04(text)
{
    var datos = text.split('|');
    tiplap.value = datos[1];
}
function llenar_combo_04(arrastre)
{
    tiplap = document.getElementById('tiplap');
    LimpiarInput_04('tiplap');
    if (arrastre != 0)
    {
        arrastre.disabled = true;
        tiplap.value = 'Cargando';
        $.ajax({type: 'POST',dataType: 'text',url: 'Formulario Tipos_lapso_tiplap_combo_buscar.php',data:{valor_04: arrastre},success:function(text){LlenarDatos_04(text);arrastre.disabled = false;}});
    }
}

function LimpiarInput_05()
{
    nota.value ='';
}
function LlenarDatos_05(text)
{
    var datos = text.split('|');
    nota.value = datos[1];

    if(parseInt(datos[1])>0){
        total= parseInt(datos[1])*5; 
        document.getElementById('acu').value=total; 
    }else{
     document.getElementById('acu').value=""; 	
 }

}
function llenar_combo_05(arrastre)
{
    nota = document.getElementById('nota');
    LimpiarInput_05('nota');
    if (arrastre != 0)
    {
        arrastre.disabled = true;
        nota.value = 'Cargando';
        $.ajax({type: 'POST',dataType: 'text',url: 'Formulario Nota_nota_combo_buscar.php',data:{valor_05: arrastre},success:function(text){LlenarDatos_05(text);arrastre.disabled = false;}});
    }
}

//////////////////////////////////////////////////////////////////////

function Borrar(valor)
{


    swal({
      title: "Desea borrar la nota?",
      text: "",
      type: "info",
      showCancelButton: true,
      confirmButtonColor: '#FF292B',
      confirmButtonText: 'Si',
      cancelButtonText: 'No',
      closeOnConfirm: false,
      closeOnCancel: false
  },
  function(isConfirm){
    if (isConfirm){


        $.post("Formulario notas_delete.php",{accion: "Borrar", id:valor},function(res){
            
           swal(res);
           if (res=="registro Borrado"){
              swal({   title: res,   text: "",   type: "success",   showCancelButton: false,   confirmButtonColor: "#4D759E",   confirmButtonText: "ok",   closeOnConfirm: false }, function(){
                window.location = 'Formulario_notas_tabla_index2.php';

            }); 
          }

      }); 
        
    } else {

     window.location = 'Formulario_notas_tabla_index2.php';


 }
});

}

        $("#Nuevo").click(function(){  

            document.getElementById('cod_mat').value = "";
            document.getElementById('seccion').value = "";
            document.getElementById('nota').value = "";
            document.getElementById('lapso').value = "";
            document.getElementById('tiplap').value = "";
            document.getElementById('cod_doc').value = "";
            document.getElementById('acu').value = "";
        });



