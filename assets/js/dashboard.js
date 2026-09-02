// Polling cada 5 segundos para simular tiempo real
const tabla = document.getElementById('tablaAsistencias');
const liveBadge = document.getElementById('liveBadge');

function construirURL() {
    const fecha = document.getElementById('f_fecha').value;
    const nombre = document.getElementById('f_nombre').value;
    const materia = document.getElementById('f_materia').value;

    const params = new URLSearchParams();
    if (fecha) params.append('fecha', fecha);
    if (nombre) params.append('nombre', nombre);
    if (materia) params.append('materia', materia);

    return 'api/get_asistencias.php?' + params.toString();
}

function pintarTabla(filas) {
    if (!filas.length) {
        tabla.innerHTML = '<tr><td colspan="5">Sin registros por ahora.</td></tr>';
        return;
    }

    tabla.innerHTML = filas.map(f => `
        <tr>
            <td>${f.estudiante}</td>
            <td>${f.codigo}</td>
            <td>${f.materia ?? '-'}</td>
            <td>${f.fecha}</td>
            <td>${f.hora}</td>
        </tr>
    `).join('');
}

function cargarAsistencias() {
    fetch(construirURL())
        .then(res => res.json())
        .then(json => {
            if (json.ok) {
                pintarTabla(json.data);
                liveBadge.style.opacity = 1;
                setTimeout(() => liveBadge.style.opacity = 0.5, 300);
            }
        })
        .catch(() => {
            tabla.innerHTML = '<tr><td colspan="5">Error al cargar datos.</td></tr>';
        });
}

document.getElementById('btnFiltrar').addEventListener('click', cargarAsistencias);
document.getElementById('btnLimpiar').addEventListener('click', () => {
    document.getElementById('f_fecha').value = '';
    document.getElementById('f_nombre').value = '';
    document.getElementById('f_materia').value = '';
    cargarAsistencias();
});

// Carga inicial y luego refresco automatico cada 5 segundos (tiempo real)
cargarAsistencias();
setInterval(cargarAsistencias, 5000);
