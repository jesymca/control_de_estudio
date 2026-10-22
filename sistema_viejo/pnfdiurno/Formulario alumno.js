function LimpiarInput()
{
    id.value ='';
    //codigo.value ='';
    cedula.value ='';
    nombre.value ='';
    carrera.value ='';      
    sexo.value ='';
    edocivil.value ='';
    lugar.value ='';
    municipio.value ='';
    estado.value ='';
    procedenci.value ='';
    fechanac.value ='';
    edad.value ='';
    direccion.value ='';
    telefonoh.value ='';
    telefonoc.value ='';
    telefonot.value ='';
    email.value ='';
    tipingreso.value ='';
    //ingreso.value ='';
    // semestre.value ='';
    egreso.value ='';
    pasantia.value ='';
    turno.value ='';
    trabajo.value ='';
    beca.value ='';
    ireceptor.value ='';
    folio.value ='';
    tomo.value ='';
    rusnies.value ='';
    discapacid.value ='';
    pnf.value ='';
    // trayecto.value ='';
    
    fcedula.value ='';
    inscripmilt.value ='';
    ftitulo.value ='';
    fcerfidicado.value ='';
    fnotas.value ='';
    fdosfotos.value ='';
    fpinscrip.value ='';
    
    fnacimie.value ='';



}
function LlenarDatos(text)
{
    var datos = text.split('|');
    id.value = datos[0];
    //codigo.value = datos[1];
    cedula.value = datos[2];
    nombre.value = datos[3];
    carrera.value = datos[4];
    sexo.value = datos[8];
    edocivil.value = datos[9];
    lugar.value = datos[10];
    municipio.value = datos[11];
    estado.value = datos[12];
    procedenci.value = datos[13];
    fechanac.value = datos[14];
    edad.value = datos[15];
    direccion.value = datos[16];
    telefonoh.value = datos[17];
    telefonoc.value = datos[18];
    telefonot.value = datos[19];
    email.value = datos[20];
    tipingreso.value = datos[21];
    //ingreso.value = datos[22];
    // semestre.value = datos[23];
    egreso.value = datos[24];
    pasantia.value = datos[25];
    turno.value = datos[26];
    trabajo.value = datos[27];
    beca.value = datos[28];
    ireceptor.value = datos[29];
    folio.value = datos[30];
    tomo.value = datos[31];
    rusnies.value = datos[32];
    discapacid.value = datos[33];
    pnf.value = datos[34];
    // trayecto.value = datos[35];

    fcedula.value = datos[36];
    inscripmilt.value = datos[37];
    ftitulo.value = datos[38];
    fcerfidicado.value = datos[39];
    fnotas.value = datos[40];
    fdosfotos.value = datos[41];
    fpinscrip.value = datos[42];    
    fnacimie.value = datos[44];
    
}
function obten_datos(arrastre)
{
    id = document.getElementById('id');
    //codigo = document.getElementById('codigo');
    cedula = document.getElementById('cedula');
    nombre = document.getElementById('nombre');
    carrera = document.getElementById('carrera');    
    sexo = document.getElementById('sexo');
    edocivil = document.getElementById('edocivil');
    lugar = document.getElementById('lugar');
    municipio = document.getElementById('municipio');
    estado = document.getElementById('estado');
    procedenci = document.getElementById('procedenci');
    fechanac = document.getElementById('fechanac');
    edad = document.getElementById('edad');
    direccion = document.getElementById('direccion');
    telefonoh = document.getElementById('telefonoh');
    telefonoc = document.getElementById('telefonoc');
    telefonot = document.getElementById('telefonot');
    email = document.getElementById('email');
    tipingreso = document.getElementById('tipingreso');
    //ingreso = document.getElementById('ingreso');
    // semestre = document.getElementById('semestre');
    egreso = document.getElementById('egreso');
    pasantia = document.getElementById('pasantia');
    turno = document.getElementById('turno');
    trabajo = document.getElementById('trabajo');
    beca = document.getElementById('beca');
    ireceptor = document.getElementById('ireceptor');
    folio = document.getElementById('folio');
    tomo = document.getElementById('tomo');
    rusnies = document.getElementById('rusnies');
    discapacid = document.getElementById('discapacid');
    pnf = document.getElementById('pnf');
    fcedula = document.getElementById('fcedula');
    inscripmilt = document.getElementById('inscripmilt');
    ftitulo = document.getElementById('ftitulo');
    fcerfidicado = document.getElementById('fcerfidicado');
    fnotas = document.getElementById('fnotas');
    fdosfotos = document.getElementById('fdosfotos');
    fpinscrip = document.getElementById('fpinscrip'); 
    fnacimie = document.getElementById('fnacimie');






    buscar = document.getElementById('buscar');
    
    
    LimpiarInput('id');
    //LimpiarInput('codigo');
    LimpiarInput('cedula');
    LimpiarInput('nombre');
    LimpiarInput('carrera');   
    LimpiarInput('sexo');
    LimpiarInput('edocivil');
    LimpiarInput('lugar');
    LimpiarInput('municipio');
    LimpiarInput('estado');
    LimpiarInput('procedenci');
    LimpiarInput('fechanac');
    LimpiarInput('edad');
    LimpiarInput('direccion');
    LimpiarInput('telefonoh');
    LimpiarInput('telefonoc');
    LimpiarInput('telefonot');
    LimpiarInput('email');
    LimpiarInput('tipingreso');
    //LimpiarInput('ingreso');
    // LimpiarInput('semestre');
    LimpiarInput('egreso');
    LimpiarInput('pasantia');
    LimpiarInput('turno');
    LimpiarInput('trabajo');
    LimpiarInput('beca');
    LimpiarInput('ireceptor');
    LimpiarInput('folio');
    LimpiarInput('tomo');
    LimpiarInput('rusnies');
    LimpiarInput('discapacid');
    LimpiarInput('pnf');
    // LimpiarInput('trayecto');

    LimpiarInput('fcedula');
    LimpiarInput('inscripmilt');
    LimpiarInput('ftitulo');
    LimpiarInput('fcerfidicado');
    LimpiarInput('fnotas');
    LimpiarInput('fdosfotos');
    LimpiarInput('fpinscrip');
    
    LimpiarInput('fnacimie');

    LimpiarInput('buscar');


    if (arrastre != 0)
    {
        arrastre.disabled = true;

        id.value = 'Cargando';
        //codigo.value = 'Cargando';
        cedula.value = 'Cargando';
        nombre.value = 'Cargando';
        carrera.value = 'Cargando';
        sexo.value = 'Cargando';
        edocivil.value = 'Cargando';
        lugar.value = 'Cargando';
        municipio.value = 'Cargando';
        estado.value = 'Cargando';
        procedenci.value = 'Cargando';
        fechanac.value = 'Cargando';
        edad.value = 'Cargando';
        direccion.value = 'Cargando';
        telefonoh.value = 'Cargando';
        telefonoc.value = 'Cargando';
        telefonot.value = 'Cargando';
        email.value = 'Cargando';
        tipingreso.value = 'Cargando';
        //ingreso.value = 'Cargando';
        // semestre.value = 'Cargando';
        egreso.value = 'Cargando';
        pasantia.value = 'Cargando';
        turno.value = 'Cargando';
        trabajo.value = 'Cargando';
        beca.value = 'Cargando';
        ireceptor.value = 'Cargando';
        folio.value = 'Cargando';
        tomo.value = 'Cargando';
        rusnies.value = 'Cargando';
        discapacid.value = 'Cargando';
        pnf.value = 'Cargando';
        // trayecto.value = 'Cargando';

        fcedula.value = 'Cargando';
        inscripmilt.value = 'Cargando';
        ftitulo.value = 'Cargando';
        fcerfidicado.value = 'Cargando';
        fnotas.value = 'Cargando';
        fdosfotos.value = 'Cargando';
        fpinscrip.value = 'Cargando';
       
        fnacimie.value = 'Cargando';
    


        buscar.value = '';
        $.ajax(
        {
            type: 'get',
            dataType: 'text',
            url: 'Formulario alumno_buscar.php',
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

