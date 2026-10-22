<?php
// .dios/footer_dios.php - Pie de página del Centro de Comando Soberano MODO DIOS
?>
    <footer class="mt-5 pt-4" style="border-top: 1px solid rgba(251, 191, 36, 0.25); color: #94a3b8; font-size: 0.85rem;">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
            <div>
                <strong style="color: #fbbf24;"><i class="fas fa-crown"></i> MODO DIOS • BÓVEDA SOBERANA DEL SISTEMA</strong>
                <span class="mx-2">|</span>
                <span>Universidad Politécnica Territorial de Puerto Cabello (UPTPC)</span>
            </div>
            <div>
                <span class="badge-dios" style="background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.35);">
                    <i class="fas fa-shield-alt"></i> Sesión Aislada: DIOS_SESSION
                </span>
                <span class="ml-2 text-muted">© <?php echo date('Y'); ?> Autoridad Suprema del Sistema</span>
            </div>
        </div>
    </footer>
</div> <!-- /.dios-container -->

<script>
// Reloj en tiempo real para la barra superior
(function() {
    const clockEl = document.getElementById('diosLiveClock');
    if (!clockEl) return;
    setInterval(function() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        clockEl.textContent =
            pad(now.getDate()) + '/' +
            pad(now.getMonth() + 1) + '/' +
            now.getFullYear() + ' ' +
            pad(now.getHours()) + ':' +
            pad(now.getMinutes()) + ':' +
            pad(now.getSeconds());
    }, 1000);
})();
</script>
</body>
</html>