<footer>

<p class="navbar-text pull-left"><br><br><br><br><br><br><br> </p>

<nav class="navbar fixed-bottom navbar-light bg-light d-none d-sm-block col-sm-12">


<div class="row">
    <div class="col-sm-6 col-xs-12">
    <div class="py-1"><img src="<?php echo $pag_web; ?>/images/educacion_universitaria.jpg" style="max-height: 38px; width: auto;" alt="Logo Pertenencia"></div>
    </div>
    <div class="col-sm-6 col-xs-12">

<ul class="nav justify-content-end">
  <li class="nav-item">
    <a class="nav-link font-weight-bold text-primary" href="../manual_usuario.php"><i class="fas fa-book-reader mr-1"></i>Manual de Usuario</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#" data-toggle="modal" data-target="#modalTerminos">Términos y Condiciones</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#" data-toggle="modal" data-target="#modalComoUtilizar">Cómo utilizar el sitio</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="https://www.uptpc.edu.ve/" target="_blank">Contáctenos</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="soporte.php">Soporte</a>
  </li>
</ul>
    </div>
</div>
</nav>

<!-- MODALES DEL FOOTER -->
<div class="modal fade" id="modalTerminos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-file-contract"></i> Términos y Condiciones</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-content-body p-4">
        <div class="p-2">
          <div class="alert alert-primary mb-3">
            <i class="fas fa-university me-2"></i> <strong>Universidad Territorial Politécnica de Puerto Cabello (UPTPC)</strong><br>
            Reglamento General de Uso y Normativa Institucional del Sistema de Control de Estudios.
          </div>
          
          <div class="accordion" id="accordionTerminos">
            <!-- 1. Ámbito de Aplicación -->
            <div class="card border-0 shadow-sm mb-2">
              <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-shield-alt mr-2"></i>1. Ámbito de Aplicación y Acceso</h6>
              </div>
              <div class="card-body p-3">
                El presente sistema está destinado exclusivamente al uso de la comunidad universitaria de la UPTPC (Estudiantes, Docentes, Directores de Carrera y Personal Administrativo). Todo acceso no autorizado o intento de manipulación indebida será sancionado conforme a la legislación venezolana y reglamentos institucionales.
              </div>
            </div>

            <!-- 2. Custodia de Credenciales -->
            <div class="card border-0 shadow-sm mb-2">
              <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-key mr-2"></i>2. Custodia y Confidencialidad de Credenciales</h6>
              </div>
              <div class="card-body p-3">
                Las claves de acceso son de carácter personal e intransferible. El usuario es el único responsable por las operaciones realizadas desde su cuenta. Queda estrictamente prohibida la divulgación de credenciales a terceros.
              </div>
            </div>

            <!-- 3. Integridad de Información Académica -->
            <div class="card border-0 shadow-sm mb-2">
              <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-database mr-2"></i>3. Integridad de los Datos Académicos</h6>
              </div>
              <div class="card-body p-3">
                La información de calificaciones, actas de notas, inscripciones y expedientes almacenados en el sistema goza de fe pública institucional. La alteración o falsificación de expedientes constituye un delito grave de acuerdo con el Código Penal Venezolano y las leyes sobre Delitos Informáticos.
              </div>
            </div>

            <!-- 4. Proteccion de Datos -->
            <div class="card border-0 shadow-sm mb-2">
              <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-user-lock mr-2"></i>4. Protección de Datos Personales</h6>
              </div>
              <div class="card-body p-3">
                La UPTPC garantiza el resguardo confidencial de los datos personales e historial académico recopilados en la plataforma, utilizándolos únicamente para fines de gestión universitaria, estadísticas e inscripciones académicas.
              </div>
            </div>
          </div>
        </div>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalComoUtilizar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fas fa-question-circle"></i> Cómo Utilizar el Sitio</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-content-body p-4">
        <div class="p-2">
          <div class="row">
            <!-- Tarjeta 1 -->
            <div class="col-md-6 mb-3">
              <div class="card h-100 border-info">
                <div class="card-header bg-info text-white py-2">
                  <h6 class="mb-0"><i class="fas fa-compass mr-1"></i> Navegación y Perfiles</h6>
                </div>
                <div class="card-body p-3">
                  <p class="small text-muted mb-2">Avanza rápidamente por los módulos habilitados según tu perfil:</p>
                  <ul class="small mb-0 pl-3">
                    <li>Utiliza la barra superior para explorar tus opciones de usuario.</li>
                    <li>Desde la opción <strong>Ajustes ▾</strong> puedes cambiar de perfil o acceder a tu mensajería interna.</li>
                  </ul>
                </div>
              </div>
            </div>
            
            <!-- Tarjeta 2 -->
            <div class="col-md-6 mb-3">
              <div class="card h-100 border-success">
                <div class="card-header bg-success text-white py-2">
                  <h6 class="mb-0"><i class="fas fa-search mr-1"></i> Buscador y Filtros Avanzados</h6>
                </div>
                <div class="card-body p-3">
                  <p class="small text-muted mb-2">Optimiza tus búsquedas en las tablas del sistema:</p>
                  <ul class="small mb-0 pl-3">
                    <li>Utiliza el cuadro de búsqueda para filtrar en tiempo real por cédula, nombre, usuario o email.</li>
                    <li>Usa los paneles desplegables para filtrar por carrera, sede o género con un clic.</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Tarjeta 3 -->
            <div class="col-md-6 mb-3">
              <div class="card h-100 border-warning">
                <div class="card-header bg-warning text-dark py-2">
                  <h6 class="mb-0"><i class="fas fa-file-pdf mr-1"></i> Constancias y Reportes PDF</h6>
                </div>
                <div class="card-body p-3">
                  <p class="small text-muted mb-2">Generación de documentos impresos:</p>
                  <ul class="small mb-0 pl-3">
                    <li>Haz clic en <strong>Generar Reporte PDF</strong> para descargar constancias institucionales de los registros filtrados.</li>
                    <li>Puedes personalizar la inclusión de estadísticas e información académica.</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Tarjeta 4 -->
            <div class="col-md-6 mb-3">
              <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white py-2">
                  <h6 class="mb-0"><i class="fas fa-lock mr-1"></i> Seguridad de la Cuenta</h6>
                </div>
                <div class="card-body p-3">
                  <p class="small text-muted mb-2">Protección de tu sesión de trabajo:</p>
                  <ul class="small mb-0 pl-3">
                    <li>Al culminar tus actividades, cierra tu sesión desde la opción <strong>Ajustes ▾ ➔ Salir</strong>.</li>
                    <li>No dejes tu equipo desatendido mientras el sistema permanezca abierto.</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalContactenos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-envelope"></i> Contáctenos</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-content-body p-4">
        <div class="p-2">
          <div class="text-center mb-3">
            <h5 class="text-success font-weight-bold mb-1">Universidad Territorial Politécnica de Puerto Cabello</h5>
            <p class="text-muted small mb-0">Institución de Educación Universitaria de Venezuela</p>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="card h-100 border-light bg-light">
                <div class="card-body p-3">
                  <h6 class="font-weight-bold text-success"><i class="fas fa-map-marker-alt mr-2"></i>Ubicación Institucional</h6>
                  <p class="small mb-1"><strong>Sede Principal:</strong> Puerto Cabello, Estado Carabobo, Venezuela.</p>
                  <p class="small mb-0"><strong>COEF:</strong> Complejo Educativo COEF, Parroquia Salom.</p>
                </div>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <div class="card h-100 border-light bg-light">
                <div class="card-body p-3">
                  <h6 class="font-weight-bold text-success"><i class="fas fa-globe mr-2"></i>Canales Digitales</h6>
                  <p class="small mb-1"><i class="fas fa-external-link-alt text-success"></i> <a href="https://www.uptpc.edu.ve/" target="_blank" class="text-success font-weight-bold">www.uptpc.edu.ve</a></p>
                  <p class="small mb-0"><i class="fas fa-envelope text-success"></i> contacto@uptpc.edu.ve</p>
                </div>
              </div>
            </div>
          </div>

          <div class="alert alert-success py-2 text-center small mb-0">
            <i class="fas fa-clock mr-1"></i> <strong>Horarios de Atención:</strong> Lunes a Viernes de 8:00 AM a 4:00 PM
          </div>
        </div>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalSoporte" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="fas fa-headset"></i> Soporte Técnico</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-content-body p-4">
        <div class="p-2">
          <div class="alert alert-dark text-center mb-3 py-2">
            <h6 class="mb-0"><i class="fas fa-headset mr-2"></i> Asistencia Técnica y Atención al Usuario</h6>
            <small class="text-muted">Si presentas dudas o inconvenientes con tu cuenta o registros académicos, comunícate con nuestros encargados:</small>
          </div>

          <div class="row">
            <!-- Hector Marulanda -->
            <div class="col-md-6 mb-3">
              <div class="card border-primary h-100 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                  <h6 class="mb-0"><i class="fas fa-code mr-1"></i> Soporte Técnico y Sistemas</h6>
                </div>
                <div class="card-body p-3 text-center">
                  <i class="fas fa-user-tie fa-3x text-primary mb-2"></i>
                  <h6 class="font-weight-bold mb-1">Hector Marulanda</h6>
                  <span class="badge bg-primary text-white mb-2">Desarrollador del Sistema</span>
                  <hr class="my-2">
                  <p class="small mb-1"><i class="fas fa-phone-alt text-primary mr-1"></i> <strong>0412-412-2996</strong></p>
                  <p class="small mb-1"><i class="fas fa-envelope text-primary mr-1"></i> programador_control_estudios@uptpc.edu.ve</p>
                  <p class="small mb-0 text-muted"><i class="fas fa-clock text-primary mr-1"></i> Lunes a Viernes, 8:00 AM - 3:00 PM</p>
                </div>
              </div>
            </div>

            <!-- Blanca Crespo -->
            <div class="col-md-6 mb-3">
              <div class="card border-success h-100 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                  <h6 class="mb-0"><i class="fas fa-user-graduate mr-1"></i> Control de Estudios</h6>
                </div>
                <div class="card-body p-3 text-center">
                  <i class="fas fa-user-graduate fa-3x text-success mb-2"></i>
                  <h6 class="font-weight-bold mb-1">Blanca Crespo</h6>
                  <span class="badge bg-success text-white mb-2">Secretaria General</span>
                  <hr class="my-2">
                  <p class="small mb-1"><i class="fas fa-phone-alt text-success mr-1"></i> <strong>0412-838-8957</strong></p>
                  <p class="small mb-1"><i class="fas fa-envelope text-success mr-1"></i> blancacrespo@uptpc.edu.ve</p>
                  <p class="small mb-0 text-muted"><i class="fas fa-clock text-success mr-1"></i> Lunes a Viernes, 8:00 AM - 4:00 PM</p>
                </div>
              </div>
            </div>
          </div>
        </div>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</footer>



<script>
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://code.jquery.com/jquery-3.5.1.min.js"><\/script>');
    document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"><\/script>');
} else if (typeof $.fn.modal === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"><\/script>');
}
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#pedidosA').load('includes/notificacionpedidos.php #pedidosA');
    $('#pedidosB').load('includes/notificacionpedidos.php #pedidosB');
    $('#pedidosC').load('includes/notificacionpedidos.php #pedidosC');
});
</script>




<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover({
    trigger: 'hover',
    placement: 'auto'

        });
});

//MENSUALIDADES
$("#dropdown-mensualidades").on("click", function(e){
  e.stopPropagation();
});

$('#dropdown-mensualidades').hover(function() {
  $('#dropdown-mens').delay(200).show();
}, function() {
  $('#dropdown-mens').delay(100).hide(100);
});


// PEDIDOS
$("#dropdown-pedidos").on("click", function(e){
  e.stopPropagation();
});

$('#dropdown-pedidos').hover(function() {
  $('#dropdown-pedi').delay(200).show();
}, function() {
  $('#dropdown-pedi').delay(100).hide(100);
});



//Ajustes
$("#dropdown-ajustes").on("click", function(e){
  e.stopPropagation();
});

$('#dropdown-ajustes').hover(function() {
  $('#dropdown-ajus').delay(200).show();
}, function() {
  $('#dropdown-ajus').delay(100).hide(100);
});

</script>

</body>
</html>
