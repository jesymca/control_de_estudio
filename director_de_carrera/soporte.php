<?php
$titulopag = "Soporte Técnico y Atención";
require_once('../funciones/functions.php');
require_once('includes/head.php');
?>

<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-headset me-2"></i> Centro de Soporte Técnico y Atención al Usuario</h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-primary mb-4">
                        <h6><i class="fas fa-info-circle me-2"></i> Asistencia Universitaria - UPTPC</h6>
                        <p class="small mb-0">Estimado usuario, en esta sección dispones de los canales directos de atención institucional para resolver inconvenientes técnicos, problemas con tu clave de acceso o dudas relacionadas con tu expediente de Control de Estudios.</p>
                    </div>

                    <div class="row">
                        <!-- UCyT -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-primary h-100 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0"><i class="fas fa-microchip me-2"></i> Soporte por la Unidad de Ciencia y Tecnología</h6>
                                </div>
                                <div class="card-body p-4 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-laptop-code fa-4x text-primary"></i>
                                    </div>
                                    <h5 class="font-weight-bold mb-1">Unidad de Ciencia y Tecnología</h5>
                                    <span class="badge bg-primary text-white mb-3">Soporte Técnico, Desarrollo y Mantenimiento</span>
                                    <hr>
                                    <p class="mb-2"><i class="fas fa-envelope text-primary me-2"></i> <a href="mailto:cienciaytecnologia@uptpc.edu.ve"><strong>cienciaytecnologia@uptpc.edu.ve</strong></a></p>
                                    <p class="mb-0 text-muted small"><i class="fas fa-clock text-primary me-1"></i> Horario: Lunes a Viernes, 8:00 AM - 4:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <!-- Control de Estudios -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-success h-100 shadow-sm">
                                <div class="card-header bg-success text-white py-3">
                                    <h6 class="mb-0"><i class="fas fa-user-graduate me-2"></i> Soporte por Control de Estudios</h6>
                                </div>
                                <div class="card-body p-4 text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-university fa-4x text-success"></i>
                                    </div>
                                    <h5 class="font-weight-bold mb-1">Control de Estudios</h5>
                                    <span class="badge bg-success text-white mb-3">Atención Académica y Expedientes</span>
                                    <hr>
                                    <p class="mb-2"><i class="fas fa-envelope text-success me-2"></i> <a href="mailto:control_de_estudios@uptpc.edu.ve"><strong>control_de_estudios@uptpc.edu.ve</strong></a></p>
                                    <p class="mb-0 text-muted small"><i class="fas fa-clock text-success me-1"></i> Horario: Lunes a Viernes, 8:00 AM - 4:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preguntas Frecuentes -->
                    <div class="card border-light bg-light mt-2">
                        <div class="card-body p-4">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-question-circle text-primary me-2"></i> Preguntas Frecuentes de Soporte</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="font-weight-bold small mb-1">¿Cómo recupero mi contraseña de acceso?</p>
                                    <p class="small text-muted mb-0">En la pantalla inicial de inicio de sesión haz clic en <em>Recuperar Contraseña</em> o solicita asistencia directamente a la Unidad de Ciencia y Tecnología.</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="font-weight-bold small mb-1">¿Qué hago si mis notas o carga no coinciden?</p>
                                    <p class="small text-muted mb-0">Ponte en contacto directo con la unidad de Control de Estudios enviando un correo a control_de_estudios@uptpc.edu.ve indicando tu número de cédula y nombre completo.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Licencia y Derechos -->
                    <div class="card border-dark bg-dark text-white mt-4 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <p class="mb-2 font-weight-bold">
                                Potenciado por la <a href="https://www.uptpc.edu.ve/ciencia-y-tecnolog%C3%ADa" target="_blank" rel="noopener noreferrer" class="text-info font-weight-bold">Unidad de Ciencia y Tecnología de la UPTPC</a>
                            </p>
                            <p class="mb-1 small text-light">
                                &copy; <?php echo date('Y'); ?> Universidad Politécnica Territorial de Puerto Cabello. Reservados Todos los Derechos.
                            </p>
                            <p class="mb-0 small text-muted">
                                Licencia de uso: <a href="https://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-underline">CC BY-NC-ND 4.0</a> | 
                                <a href="https://www.apache.org/licenses/LICENSE-2.0" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-underline">Licencia Pública General Apache 3.0</a>
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i> Regresar al Panel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>
