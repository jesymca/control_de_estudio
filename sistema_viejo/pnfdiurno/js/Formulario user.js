function LimpiarInput()
{
    id.value ='';
    nombre.value ='';
    login.value ='';
    clave.value ='';
}
function LlenarDatos(text)
{
    var datos = text.split('|');
    id.value = datos[0];
    nombre.value = datos[1];
    login.value = datos[2];
    clave.value = datos[3];
}
function obten_datos(arrastre)
{
    id = document.getElementById('id');
    nombre = document.getElementById('nombre');
    login = document.getElementById('login');
    clave = document.getElementById('clave');

    LimpiarInput('id');
    LimpiarInput('nombre');
    LimpiarInput('login');
    LimpiarInput('clave');

    if (arrastre != 0)
    {
        arrastre.disabled = true;

        id.value = 'Cargando';
        nombre.value = 'Cargando';
        login.value = 'Cargando';
        clave.value = 'Cargando';

        $.ajax(
        {
            type: 'get',
            dataType: 'text',
            url: 'Formulario user_buscar.php',
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

