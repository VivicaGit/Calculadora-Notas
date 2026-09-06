// notas.js — añadir y borrar notas sin recargar la página

document.addEventListener('DOMContentLoaded', function () {
    iniciarEventos();
});

function iniciarEventos() {
    // Interceptamos cada formulario de añadir nota
    document.querySelectorAll('.form-nota-crear').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // evitamos que el formulario recargue la página
            crearNota(form);
        });
    });

    // Interceptamos cada enlace de borrar nota (los que ya existen al cargar)
    document.querySelectorAll('.chip-nota-del').forEach(function (enlace) {
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
