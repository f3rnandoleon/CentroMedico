<?php 
if(!isset($_SESSION)) { 
    session_start();   
}

// Recuperar el sort y dir actuales para mostrar iconos y conservar en paginación
$current_sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'fecha';
$current_dir  = isset($_SESSION['dir']) ? $_SESSION['dir'] : 'asc';

// Función para invertir la dirección
function invertDir($dir) {
  return ($dir === 'asc') ? 'desc' : 'asc';
}
?>
<?php
// Mapeo rápido por ID:
$mapPacientes = [];
foreach ($pacientes as $p) {
    $mapPacientes[$p->getId()] = $p;
}
$mapUsuarios = [];
foreach ($usuarios as $u) {
    $mapUsuarios[$u->getId()] = $u;
}

// Preparamos los eventos
$eventos = [];
foreach ($citas as $cita) {
    $fechaInicio = $cita->getFecha() . 'T' . $cita->getHora();
    $horaFin     = date('H:i:s', strtotime($cita->getHora() . ' +30 minutes'));
    $fechaFin    = $cita->getFecha() . 'T' . $horaFin;

    // Paciente
    $pac = $mapPacientes[$cita->getPaciente()] ?? null;
    $nombrePaciente = $pac
        ? $pac->getNombres() . ' ' . $pac->getApellidos()
        : 'Paciente #' . $cita->getPaciente();

    // Dermatólogo (usuario)
    $usr = $mapUsuarios[$cita->getUsuario()] ?? null;
    $nombreDerm = $usr
        ? $usr->getNombres() . ' ' . $usr->getApellidos()
        : 'Doctor #' . $cita->getUsuario();

    $eventos[] = [
        'id'            => $cita->getId(),
        'title'         => $nombrePaciente . ' | ' . $cita->getMotivo(),
        'start'         => $fechaInicio,
        'end'           => $fechaFin,
        'extendedProps'=> [
            'estado'        => strtolower($cita->getEstado()),
            'observaciones' => $cita->getObservaciones(),
            'dermatologo'   => $nombreDerm,
        ]
    ];
}
?>
<script>
  // Lo pasamos a JS
  var eventos = <?php echo json_encode($eventos, JSON_UNESCAPED_UNICODE); ?>;
</script>

<script>
var eventos = <?php echo json_encode($eventos); ?>;
</script>
<style>
/* Quita el subrayado de los títulos de los días en FullCalendar */
.fc-col-header-cell-cushion,
.fc-daygrid-day-number {
  text-decoration: none !important;
  color: #212529 !important;
}

/* Opcional: reduce el tamaño de fuente para que se vea más compacto */
#calendario-citas .fc {
  font-size: 0.92rem;
}
/* Box de evento colorido según estado */
.evento-box {
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 0.95em;
    font-weight: 500;
    display: inline-block;
    color: #212529;
    background: #e9ecef;
    border: 1px solid #dee2e6;
    box-shadow: 1px 2px 4px rgba(80,80,80,0.06);
    margin-bottom: 1px;
}

.box-pendiente  { background: #fff3cd; border-color: #ffecb5; }
.box-cancelada  { background: #f8d7da; border-color: #f5c2c7; }
.box-realizada  { background: #d1e7dd; border-color: #badbcc; }

/* Quitar puntito por defecto */
.fc-event-dot {
    display: none !important;
}

/* Compactar evento en vista semanal */
.fc .fc-timegrid-event .evento-box {
    width: 100%;
    white-space: normal;
}
</style>

<div class="container py-4">
    <div class="card shadow-sm mb-4 px-5 py-4">
        <div class="card-body p-2">
              <h4 class="mb-3 text-success">Calendario de Citas</h4>
            <div id="calendario-citas" style="min-height: 400px;"></div>
        </div>
    </div>
</div>

<!-- Modal para detalles de la cita -->
<div class="modal fade" id="modalCita" tabindex="-1" aria-labelledby="modalCitaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalCitaLabel">Detalle de la cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="modalBodyCita">
        <!-- Aquí se muestran los detalles de la cita -->
      </div>
    </div>
  </div>
</div>
<?php if (isset($_SESSION['mensaje'])): ?>
  <div class="alert alert-success position-fixed top-20 start-50 translate-middle-x mt-3 shadow" style="z-index: 9999; max-width: 400px;">
    <strong><?php echo $_SESSION['mensaje']; ?></strong>
  </div>
<?php endif; unset($_SESSION['mensaje']); ?>
<script>
  var eventos = <?php echo json_encode($eventos); ?>;
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendario-citas');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'es',
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        expandRows: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            day:      'Día'
        },
        events: eventos,
        eventOverlap: false,
        slotDuration: '00:30:00',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        events: eventos,
      eventClick: function(info) {
        let e   = info.event;
        let p   = e.extendedProps;
        let est = p.estado;
        let estadoTexto = {
          pendiente: 'Pendiente',
          cancelada: 'Cancelada',
          realizada: 'Realizada'
        }[est] || est.charAt(0).toUpperCase() + est.slice(1);

        // Construimos el HTML
        let html = `
          <h5>${e.title}</h5>
          <p><strong>Dermatólogo:</strong> ${p.dermatologo}</p>
          <p><strong>Fecha:</strong> ${e.start.toLocaleDateString('es-ES')}</p>
          <p><strong>Hora:</strong> ${e.start.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'})}</p>
          <p><strong>Estado:</strong> ${estadoTexto}</p>
          <p><strong>Observaciones:</strong> ${p.observaciones || 'Sin observaciones'}</p>
          <div class="mt-2">
            <a href="?controller=cita&action=showupdate&id=${e.id}" class="btn btn-warning btn-sm me-1">
              <i class="bi bi-pencil"></i> Actualizar
            </a>
            <a href="?controller=cita&action=delete&id=${e.id}" class="btn btn-danger btn-sm">
              <i class="bi bi-trash"></i> Eliminar
            </a>
          </div>
        `;
        document.getElementById('modalBodyCita').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalCita')).show();
      },

        height: 450,
        eventContent: function(arg) {
            let estado = arg.event.extendedProps.estado;
            let bg = '';
            if (estado === 'pendiente') bg = 'box-pendiente';
            else if (estado === 'cancelada') bg = 'box-cancelada';
            else if (estado === 'realizada') bg = 'box-realizada';
            let hora = arg.timeText ? `<span class="me-1 text-secondary">${arg.timeText}</span>` : '';
            return {
                html: `<div class="evento-box ${bg}">${hora}<span>${arg.event.title}</span></div>`
            };
        }
    });
    calendar.render();
});
</script>

<!-- Bootstrap (si ya lo usas, omite) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
