/**
 * Helper global para renderizar listas desplegables (<select>) de materias
 * agrupadas por Trayecto utilizando <optgroup>, evitando duplicación de código.
 * Universidad Territorial Politécnica de Puerto Cabello (UPTPC)
 */
(function(window) {
    'use strict';

    var nombresTrayectos = {
        0: 'Trayecto Inicial',
        1: 'Trayecto I (Primer Año - TSU)',
        2: 'Trayecto II (Segundo Año - TSU)',
        3: 'Trayecto III (Tercer Año - Ing./Lic.)',
        4: 'Trayecto IV (Cuarto Año - Ing./Lic.)',
        5: 'Trayecto V'
    };

    /**
     * Devuelve el HTML de opciones de materias agrupadas por Trayecto.
     * @param {Array} materias Lista de objetos materia
     * @param {string|number|null} idSeleccionado ID preseleccionado
     * @param {string|null} placeholder Texto del placeholder vacío
     * @param {Object} opciones Opciones adicionales (dataAttrs, incluirUc, incluirCodigo)
     * @returns {string}
     */
    window.renderizarOpcionesMateriasPorTrayectoJS = function(materias, idSeleccionado, placeholder, opciones) {
        opciones = opciones || {};
        var incluirUc = (opciones.incluirUc !== undefined) ? opciones.incluirUc : true;
        var incluirCodigo = (opciones.incluirCodigo !== undefined) ? opciones.incluirCodigo : true;
        var dataAttrs = !!opciones.dataAttrs;

        var html = '';
        if (placeholder !== null && placeholder !== undefined) {
            html += '<option value="">' + placeholder + '</option>';
        }

        if (!materias || !materias.length) {
            return html;
        }

        // Agrupar por número de trayecto
        var porTrayecto = {};
        materias.forEach(function(m) {
            var t = parseInt((m.trayecto !== undefined && m.trayecto !== null) ? m.trayecto : 0);
            if (isNaN(t)) t = 0;
            if (!porTrayecto[t]) porTrayecto[t] = [];
            porTrayecto[t].push(m);
        });

        var trayectos = Object.keys(porTrayecto).map(Number).sort(function(a, b) {
            return a - b;
        });

        trayectos.forEach(function(t) {
            var label = 'TRAYECTO ' + (t === 0 ? 'INICIAL' : t) + ' (' + (nombresTrayectos[t] || 'Trayecto ' + t) + ')';
            html += '<optgroup label="' + label + '">';

            porTrayecto[t].forEach(function(m) {
                var id = m.id_materia || m.id;
                var cod = m.cod_materia || m.codigo || '';
                var nom = m.nombre_materia || m.nombre || '';
                var uc = (m.creditos && m.creditos > 0) ? parseInt(m.creditos) : null;

                var texto = '';
                if (incluirCodigo && cod) {
                    texto += '[' + cod + '] ';
                }
                texto += nom;
                if (incluirUc && uc) {
                    texto += ' (' + uc + ' UC)';
                }

                var sel = (idSeleccionado !== null && idSeleccionado !== undefined && idSeleccionado !== '' && String(idSeleccionado) === String(id)) ? ' selected' : '';

                var extraData = '';
                if (dataAttrs) {
                    extraData += ' data-codigo="' + (cod || '') + '"';
                    extraData += ' data-nombre="' + nom + '"';
                    extraData += ' data-trayecto="' + t + '"';
                    if (uc) extraData += ' data-uc="' + uc + '"';
                    if (m.es_proyecto !== undefined) extraData += ' data-es-proyecto="' + (m.es_proyecto ? '1' : '0') + '"';
                }

                html += '<option value="' + id + '"' + sel + extraData + '>' + texto + '</option>';
            });

            html += '</optgroup>';
        });

        return html;
    };
})(window);
