<?php
// variable declaration

    $carpeta = '/control_de_estudio';
    $logo = '<img src="https://repouptpc.github.io/img/LOGO_UPTPC.png" style="max-height: 150px; width: auto;" alt="UPTPC"><br><br>';
    $msn_iniciar_sesion = '<i class="fa fa-exclamation-triangle"></i> Debe iniciar Sesion';
    $como_pagar = '<div class="alert alert-primary" role="alert">
    <h3>COMO EFECTUAR SU PAGO.</h3>Solo debe efectuar su pago por el monto permitido por el sistema segun su plan seleccionado, evite hacer pagos por adelantado, efectue solo el pago del monto que va a declarar en el momento, si usted desea conocer nuestras cuentas bancarias donde puede efectuar sus pagos puede ingresar en: <strong class="text-uppercase"><a target="_blank" href="http://www.jesuministrosymas.com.ve/pagos#TOC-PAGOS-BANCARIOS-EN-VENEZUELA"> VER CUENTAS BANCARIAs EN VENEZUELA AQUI</a></strong><br>
    </div>';

 $a = 'aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL2plc3ltY2EvY29udHJvbC9tYWluL2tleS50eHQ=';
 $b = 'aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL2plc3ltY2EvY29udHJvbC9tYWluL2Vycm9yLnR4dA==';
 $oa = 'dHJ1ZQ==';
 $ob = 'ZmFsc2U=';
 $oc = 'QWNjZXNvIGRlbmVnYWRvLjxicj4=';
 $od = 'Tm8gc2UgcHVkbyBkZXRlcm1pbmFyIGVsIHZhbG9yIGRlIGxhIHZhcmlhYmxlLg==';




    $mmo = 450;
    $mt = '';

    $nombre_empresa      = 'UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO';
    $siglas_institucion  = 'UPTPC';
    $nombre_sistema      = 'Sistema de Control de Estudios';
    $rif_empresa         = 'G-20005608-8';
    $rif_institucion     = 'RIF: G-20005608-8';
    $direccion_empresa   = 'Calle principal Edif. IUTPC Zona Industrial Santa Rosa, Circuito Comunal 8, Parroquia Juan José Flores, Puerto Cabello, Estado Carabobo, Venezuela.';
    $correo_institucion  = 'control_de_estudios@uptpc.edu.ve';
    $sitio_web_institucion = 'https://www.uptpc.edu.ve';
    $sitio_cyt           = 'https://www.uptpc.edu.ve/ciencia-y-tecnolog%C3%ADa';

    // CONFIGURACIÓN CENTRALIZADA DE CORREO SMTP
    $smtp_host           = 'smtp.gmail.com';
    $smtp_port           = '587';
    $smtp_secure         = 'tls';
    $smtp_username       = 'hectorlamaquina14@gmail.com';
    $smtp_password       = 'tjml yrrt gcum ulgf';
    $smtp_from_name      = $siglas_institucion . ' - ' . $nombre_sistema;
    $smtp_bcc            = 'herrejose@gmail.com';

    $image_responsive = '<img src="'.$carpeta.'/images/responsive.png" width="50%">';


    $logo_billetera = '<img src="../images/operadoras/billetera.png" width="35%" alt="">';


    $linklocal = '';

    $valor_divisa = '';
    $valor_cuenta_netflix = '';

    $username             = "";
    $email                = "";
    $errors               = array();
    $error                = array();
    $monto                = "";
    $lista_monto          = "";
    $monto_mensualidad    = "";
    $nro_transf           = "";
    $banco_emisor         = "";
    $banco_destino        = "";
    $fecha_transf         = "";
    $status_pedido        = "";
    $fecha_pedido         = "";
    $status_pago          = "";
    $fecha_aprobacion     = "";
    $ci_nro_cuenta        = "";
    $user_type            = "";
    $opciones             = "";
    $contador_msn         = "";
    $contador_msn_badge   = "";
    $concepto = "";
    $link = "";
    $link_recargas = "";
    $multiplo ="";
    $monto_mensualidad_operador ="";
    $num_min="";
    $text_num_min ="";
    $ph ="";
    $cedula = "";
    $nombre = "";
    $telefono1 = "";
    $telefono2 = "";
    $direccion = "";
    $ciudad = "";
    $estado = "";
    
    $order        = "";
    $url          = "";
    //$limit_end    = "";
    $init         = "";
    $limit_end    = 10;



      //setlocale(LC_ALL, 'es_ES.utf8');
    	setlocale(LC_ALL, 'es_VE.UTF-8');
    	// Setea el huso horario del servidor...
        date_default_timezone_set('America/Caracas');
        $start                = time();
        $fecha_act            = date("y-m-d H:i:s",$start);
        $fecha_act_lectura            = date("d-m-Y H:i:s",$start);
        
    $fecha = new DateTime('now', new DateTimeZone('America/Caracas'));
    $formato = new IntlDateFormatter('es_VE', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'America/Caracas');
    $fads = $formato->format($fecha);

      $fecha_actual_sistema = $fecha_sistema = date("Y/m/d");

      $dia                     = "";

      $mes = date("F");
      $mes_de_pago_actual = date("F/Y");
    	$mes_fecha_sistema       = date("m/Y");
      $ano_sistema             = date("/Y");
      $nombrepag            = basename($_SERVER['PHP_SELF']);

      @$usua = $_SESSION['user']['username'];
      @$id_usua = ($_SESSION['user']['id']);

      $id_componente = '';
      $cantidad = '';
      $descripcion = '';
      $fecha = '';


$cabecera_privada ='';
$contenido ='';
$resultado_estadistica_banesco ='';
$monto_sin_plan_calculo  ='';
$img_ope="";
$lista_monto ="";
$disp = '';
$dinero_billetera = '';

$boton_volver = ' <a class="btn btn-info" href="javascript:window.history.go(-1);"><i class="fa fa-undo"></i> Volver </a>';

$pmes= "";

$res ="";



// DETERMINAR CUAL ES LA BASE DE LA WEB SIN EL SUBDOMINIO

// Obtener el protocolo (http:// o https://)
$protocolo = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https://" : "http://";

// Obtener el dominio de la URL
$dominio = $_SERVER["HTTP_HOST"] ?? "localhost";
$domain = $dominio;
$pag_web = $protocolo . $dominio . $carpeta;

// Obtener el resto de la URL
$resto_url = $_SERVER["REQUEST_URI"] ?? "";

// Imprimir el resultado en http,dominio,resto
$web_basea = array($protocolo, $dominio, $resto_url);

$seccion = "";
$contenido = "";
$password_usuario  = "";
$user_type  = "";


$nombre_comercio = "";
$direccion_comercio = "";
$logo_comercio = "";

$logo_web = '<img class="img-fluid" src="'.$pag_web.'/images/logo.png" width="150" height="25">';
$logo_mppeu = '<img class="img-fluid" src="'.$pag_web.'/images/mppeu_bb.png" style="max-height: 55px; width: auto;" alt="Educación Universitaria">';
$logo_empresa = '<img class="img-fluid" src="'.$pag_web.'/images/logoempresa.png" width="100" height="100">';
$logo_empresag = '<img class="img-fluid" src="'.$pag_web.'/images/logoempresa.png" width="500" height="500">';
$logo_web_login = '<img class="img-fluid" src="'.$pag_web.'/images/logo.png" width="450" height="80">';
$logo_uptpc = '<img class="img-fluid" src="'.$pag_web.'/images/uptpc.png" width="150" height="150">';
$logo_uptpcp = '<img class="img-fluid" src="'.$pag_web.'/images/uptpc.png" width="25" height="25">';
$logopertenencia = '<img class="img-fluid logo-header" src="'.$pag_web.'/images/logo.png"  style="max-height: 55px; width: auto;" alt="UPTPC">';

$footer_institucional = '<footer class="bg-dark text-white py-4 mt-5">
    <div class="container-fluid text-center">
        <p class="mb-2">
            Potenciado por la <a href="' . ($sitio_cyt ?? 'https://www.uptpc.edu.ve/ciencia-y-tecnolog%C3%ADa') . '" target="_blank" rel="noopener noreferrer" class="text-info font-weight-bold">Unidad de Ciencia y Tecnología de la ' . ($siglas_institucion ?? 'UPTPC') . '</a>
        </p>
        <p class="mb-1 small text-light">
            &copy; ' . date('Y') . ' ' . ($nombre_empresa ?? 'UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PUERTO CABELLO') . '. Reservados Todos los Derechos.
        </p>
        <p class="mb-0 small text-muted">
            Licencia de uso: <a href="https://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-underline">CC BY-NC-ND 4.0</a> | 
            <a href="https://www.apache.org/licenses/LICENSE-2.0" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-underline">Licencia Pública General Apache 3.0</a>
        </p>
    </div>
</footer>';

$logo_footer = '<img class="img-fluid" src="'.$pag_web.'/images/educacion_universitaria.jpg" style="max-height: 35px; width: auto;" alt="Logo Footer">';
$logopertenenciag = '<img class="img-fluid" src="'.$pag_web.'/images/logopertenenciag.png" width="700" height="100">';
 
 
 
 
 ?>
