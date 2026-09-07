// notas.js — añadir y borrar notas sin recargar la página

document.addEventListener('DOMContentLoaded', function () {
    iniciarEventos();
    iniciarModoEdicion();
    iniciarPanelAtalhos();
    iniciarAtalhos();
    iniciarGestionEstructura();
});

function iniciarEventos() {
    // Interceptamos cada formulario de añadir nota
    document.querySelectorAll('.form-nota-crear').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // evitamos que el formulario recargue la página
            crearNota(form);
        });
    });

    // Solo los chips de nota — el enlace de borrar categoría tiene la misma clase
    // pero no está dentro de .chip-nota, así que este selector no lo pilla
    document.querySelectorAll('.chip-nota .chip-nota-del').forEach(function (enlace) {
        enlace.addEventListener('click', function (e) {
            e.preventDefault();
            if (!confirm('¿Borrar esta nota?')) return;
            borrarNota(enlace);
        });
    });
}

// Añade un nuevo listener de borrado a un chip recién creado en el DOM
function activarBorradoChip(enlace) {
    enlace.addEventListener('click', function (e) {
        e.preventDefault();
        if (!confirm('¿Borrar esta nota?')) return;
        borrarNota(enlace);
    });
}

function crearNota(form) {
    const datos = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'fetch' },
        body: datos
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
        if (!json.ok) return;

        const article    = form.closest('.categoria-card');
        const inputValor = form.querySelector('input[name="valor"]');
        const valor      = parseFloat(json.valor);
        const tipo       = article.dataset.tipo;

        // Quitamos el mensaje "Sin notas" si estaba visible
        const sinNotas = article.querySelector('.sin-notas');
        if (sinNotas) sinNotas.remove();

        // Construimos el nuevo chip y lo insertamos antes del formulario
        const chip = construirChip(json.id, valor, json.asignaturaId);
        const contenedor = article.querySelector('.chips-notas');
        contenedor.insertBefore(chip, form);
        activarBorradoChip(chip.querySelector('.chip-nota-del'));

        // Para categorías de tipo único: ocultamos el formulario tras guardar
        if (tipo === 'unica') {
            form.classList.add('d-none');
        }

        inputValor.value = '';

        actualizarMediaCategoria(article);
        actualizarMediaAsignatura();
    });
}

function borrarNota(enlace) {
    const url = enlace.getAttribute('href');

    fetch(url, {
        headers: { 'X-Requested-With': 'fetch' }
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
        if (!json.ok) return;

        const chip       = enlace.closest('.chip-nota');
        const article    = enlace.closest('.categoria-card');
        const tipo       = article.dataset.tipo;
        const contenedor = article.querySelector('.chips-notas');

        chip.remove();

        // Si no quedan chips, mostramos el mensaje "Sin notas"
        const chipsRestantes = contenedor.querySelectorAll('.chip-nota');
        if (chipsRestantes.length === 0) {
            const mensaje = document.createElement('span');
            mensaje.className = 'sin-notas text-muted fst-italic small';
            mensaje.textContent = tipo === 'unica' ? 'Sin nota registrada' : 'Sin notas registradas';
            contenedor.insertBefore(mensaje, contenedor.querySelector('.form-nota-crear'));
        }

        // Para tipo único: volvemos a mostrar el formulario
        if (tipo === 'unica') {
            const formUnica = article.querySelector('.form-nota-crear');
            if (formUnica) formUnica.classList.remove('d-none');
        }

        actualizarMediaCategoria(article);
        actualizarMediaAsignatura();
    });
}

// Construye el HTML de un chip de nota nuevo
function construirChip(notaId, valor, asignaturaId) {
    const span = document.createElement('span');
    span.className = 'chip-nota';
    span.dataset.valor = valor;

    const valorSpan = document.createElement('span');
    valorSpan.textContent = valor.toFixed(2);

    const enlace = document.createElement('a');
    enlace.href = 'index.php?controlador=nota&accion=borrar&id=' + notaId + '&asignatura_id=' + asignaturaId;
    enlace.className = 'chip-nota-del';
    enlace.title = 'Borrar nota';
    enlace.innerHTML = '&times;';

    span.appendChild(valorSpan);
    span.appendChild(enlace);
    return span;
}

// Recalcula y actualiza la media visible de una categoría
function actualizarMediaCategoria(article) {
    const chips = article.querySelectorAll('.chip-nota');
    const spanMedia = article.querySelector('.media-categoria');
    const tipo = article.dataset.tipo;

    if (chips.length === 0) {
        if (spanMedia) spanMedia.textContent = '';
        article.dataset.mediaActual = '';
        return;
    }

    let media;
    if (tipo === 'unica') {
        // Para nota única, el valor es directamente el del chip
        media = parseFloat(chips[0].dataset.valor);
    } else {
        // Media aritmética de todas las notas
        let suma = 0;
        chips.forEach(function (chip) {
            suma += parseFloat(chip.dataset.valor);
        });
        media = suma / chips.length;
    }

    // Guardamos la media calculada en el propio article para usarla en la media de asignatura
    article.dataset.mediaActual = media;

    if (spanMedia) {
        const etiqueta = tipo === 'unica' ? 'Nota: ' : 'Media: ';
        spanMedia.textContent = etiqueta + media.toFixed(2);
        spanMedia.className = 'media-categoria fw-bold ' + claseColor(media);
    }
}

// Recalcula y actualiza la media ponderada de la asignatura (la que aparece arriba del todo)
function actualizarMediaAsignatura() {
    const categorias = document.querySelectorAll('.categoria-card');
    let sumaPonderada = 0;
    let pesoTotal = 0;

    categorias.forEach(function (cat) {
        const media = parseFloat(cat.dataset.mediaActual);
        const peso  = parseFloat(cat.dataset.peso);
        if (!isNaN(media) && cat.dataset.mediaActual !== '') {
            sumaPonderada += media * peso;
            pesoTotal     += peso;
        }
    });

    const spanFinal = document.getElementById('media-asignatura');
    if (!spanFinal) return;

    if (pesoTotal === 0) {
        spanFinal.textContent = '--';
        spanFinal.className = 'nota-grande text-muted';
    } else {
        const mediaFinal = sumaPonderada / pesoTotal;
        spanFinal.textContent = mediaFinal.toFixed(2);
        spanFinal.className = 'nota-grande ' + claseColor(mediaFinal);
    }
}

// Devuelve la clase CSS de color según la nota (igual que claseColorNota en PHP)
function claseColor(nota) {
    if (nota >= 9) return 'nota-sobresaliente';
    if (nota >= 7) return 'nota-notable';
    if (nota >= 5) return 'nota-aprobado';
    if (nota >= 4) return 'nota-suficiente';
    return 'nota-suspenso';
}

// Panel de atajos de teclado — se abre con ? y se cierra con Escape o clic fuera
function iniciarPanelAtalhos() {
    var overlay = document.createElement('div');
    overlay.id = 'overlay-atajos';
    overlay.className = 'overlay-atajos';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML =
        '<div class="overlay-atajos__panel" role="dialog" aria-label="Atajos de teclado">' +
            '<div class="overlay-atajos__cabecera">' +
                '<span class="overlay-atajos__titulo">Atajos de teclado</span>' +
                '<button class="overlay-atajos__cerrar" aria-label="Cerrar">&times;</button>' +
            '</div>' +
            '<table class="overlay-atajos__tabla"><tbody>' +
                '<tr><td><kbd>?</kbd></td><td>Mostrar / ocultar esta ayuda</td></tr>' +
                '<tr><td><kbd>F2</kbd></td><td>Activar / desactivar modo edición</td></tr>' +
                '<tr><td><kbd>Alt+N</kbd></td><td>Nueva asignatura o categoría</td></tr>' +
                '<tr><td><kbd>Alt+1</kbd> <kbd>2</kbd> <kbd>3</kbd></td><td>Cambiar de trimestre</td></tr>' +
            '</tbody></table>' +
        '</div>';
    document.body.appendChild(overlay);

    function abrir() {
        overlay.classList.add('overlay-atajos--visible');
        overlay.setAttribute('aria-hidden', 'false');
    }

    function cerrar() {
        overlay.classList.remove('overlay-atajos--visible');
        overlay.setAttribute('aria-hidden', 'true');
    }

    // Cerrar con el botón × del panel
    overlay.querySelector('.overlay-atajos__cerrar').addEventListener('click', cerrar);

    // Cerrar al hacer clic en el fondo oscuro (fuera del panel)
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) cerrar();
    });

    // Abrir/cerrar con ? y cerrar con Escape
    document.addEventListener('keydown', function (e) {
        if (e.target.matches('input, textarea, select')) return;
        if (e.key === '?') {
            overlay.classList.contains('overlay-atajos--visible') ? cerrar() : abrir();
        }
        if (e.key === 'Escape') cerrar();
    });
}

// Modo edición — muestra/oculta los controles de estructura de categorías
function iniciarModoEdicion() {
    var btn = document.getElementById('btn-modo-edicion');
    if (!btn) return;

    var main = document.querySelector('main');

    function aplicarEstado(activo) {
        main.classList.toggle('modo-edicion', activo);
        btn.classList.toggle('btn-modo-edicion--activo', activo);

        // En la vista de lista: muestra/oculta los controles del selector
        document.querySelectorAll('.btn-borrar-estructura, .btn-exportar-curso').forEach(function (el) {
            el.classList.toggle('d-none', !activo);
        });
        var filasExtra = document.querySelectorAll(
            '.fila-nueva-titulacion, .fila-nuevo-curso, .fila-importar'
        );
        filasExtra.forEach(function (fila) {
            fila.classList.toggle('d-none', !activo);
        });
    }

    // Restauro el estado de la sesión anterior
    var guardado = localStorage.getItem('modoEdicion') === '1';
    aplicarEstado(guardado);

    btn.addEventListener('click', function () {
        var activo = !main.classList.contains('modo-edicion');
        aplicarEstado(activo);
        localStorage.setItem('modoEdicion', activo ? '1' : '0');
    });
}

// Atajos de teclado
function iniciarAtalhos() {

    // Auto-focus al abrir cualquier formulario colapsable
    document.querySelectorAll('details').forEach(function (det) {
        det.addEventListener('toggle', function () {
            if (!det.open) return;
            // pequeño delay para que el navegador termine de mostrar el contenido
            setTimeout(function () {
                var input = det.querySelector('input[type="text"], input[type="number"]');
                if (input) input.focus();
            }, 50);
        });
    });

    document.addEventListener('keydown', function (e) {
        // No interferir si el foco está dentro de un campo editable
        if (e.target.matches('input, textarea, select')) return;

        // F2 — activar / desactivar modo edición
        if (e.key === 'F2') {
            var btnEdicion = document.getElementById('btn-modo-edicion');
            if (btnEdicion) { e.preventDefault(); btnEdicion.click(); }
        }

        // Alt+N — abrir el formulario de nueva asignatura o nueva categoría
        if (e.altKey && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            var toggle = document.querySelector('.new-subject-toggle');
            if (!toggle) return;
            toggle.open = true;
            setTimeout(function () {
                var input = toggle.querySelector('input[type="text"], input[type="number"]');
                if (input) input.focus();
            }, 50);
        }

        // Alt+1 / Alt+2 / Alt+3 — cambiar de trimestre
        // Usamos e.code (físico) además de e.key porque con teclado español
        // Alt+número puede producir un carácter distinto en e.key
        if (e.altKey && (e.code === 'Digit1' || e.code === 'Digit2' || e.code === 'Digit3')) {
            var lblTrimestre = document.getElementById('lbl-trimestre');
            if (!lblTrimestre) return;
            e.preventDefault();
            var fila = lblTrimestre.closest('.selector-fila');
            var botones = fila.querySelectorAll('.btn-selector');
            var indice = parseInt(e.code.replace('Digit', ''), 10) - 1;
            if (botones[indice]) botones[indice].click();
        }
    });
}

// Gestión de titulaciones y cursos (modo edición en Listar)
function iniciarGestionEstructura() {
    var modal = document.getElementById('modal-borrar');
    if (!modal) return; // solo existe en la vista de lista

    var btnConfirmar   = document.getElementById('modal-borrar-confirmar');
    var inputNombre    = document.getElementById('modal-borrar-input');
    var inputId        = document.getElementById('modal-borrar-id');
    var inputNombreOk  = document.getElementById('modal-borrar-nombre-confirmacion');
    var form           = document.getElementById('modal-borrar-form');
    var enlaceExportar = document.getElementById('modal-borrar-exportar');
    var btnCancelar    = document.getElementById('modal-borrar-cancelar');

    var nombreEsperado = '';

    function abrirModal(tipo, id, nombre) {
        nombreEsperado    = nombre;
        inputId.value     = id;
        inputNombre.value = '';
        btnConfirmar.disabled = true;
        inputNombreOk.value   = '';

        document.getElementById('modal-borrar-titulo').textContent =
            'Borrar ' + (tipo === 'curso' ? 'curso' : 'titulación') + ' «' + nombre + '»';

        var aviso = document.getElementById('modal-borrar-aviso');
        if (tipo === 'curso') {
            aviso.innerHTML =
                '<strong>Atención:</strong> se borrarán todas las asignaturas, ' +
                'categorías y notas de este curso. Esta acción no se puede deshacer.';
        } else {
            aviso.innerHTML =
                '<strong>Atención:</strong> se borrarán todos los cursos, ' +
                'asignaturas y notas de esta titulación. Esta acción no se puede deshacer.';
        }

        // El enlace de exportar solo aplica a cursos
        if (tipo === 'curso') {
            enlaceExportar.href = 'index.php?controlador=estructura&accion=exportar-curso&curso_id=' + id;
            enlaceExportar.style.display = 'inline-flex';
        } else {
            enlaceExportar.style.display = 'none';
        }

        document.getElementById('modal-borrar-label').textContent =
            'Escribe «' + nombre + '» para confirmar:';
        document.getElementById('modal-borrar-hint').textContent =
            'El nombre debe coincidir exactamente para poder borrar.';

        form.action = tipo === 'curso'
            ? 'index.php?controlador=estructura&accion=borrar-curso'
            : 'index.php?controlador=estructura&accion=borrar-titulacion';

        modal.classList.add('modal-borrar--visible');
        setTimeout(function () { inputNombre.focus(); }, 80);
    }

    function cerrarModal() {
        modal.classList.remove('modal-borrar--visible');
        inputNombre.value = '';
        btnConfirmar.disabled = true;
    }

    // Habilito el botón solo cuando el nombre coincide exactamente
    inputNombre.addEventListener('input', function () {
        var coincide = inputNombre.value === nombreEsperado;
        btnConfirmar.disabled = !coincide;
        inputNombreOk.value   = coincide ? nombreEsperado : '';
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('modal-borrar--visible')) {
            cerrarModal();
        }
    });
    modal.addEventListener('click', function (e) {
        if (e.target === modal) cerrarModal();
    });
    btnCancelar.addEventListener('click', cerrarModal);

    document.querySelectorAll('.btn-borrar-estructura').forEach(function (btn) {
        btn.addEventListener('click', function () {
            abrirModal(btn.dataset.tipo, btn.dataset.id, btn.dataset.nombre);
        });
    });
}
