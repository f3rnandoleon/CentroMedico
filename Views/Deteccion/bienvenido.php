
<?php
// Id de usuario actual (debe estar en sesión)
$idUser = $_SESSION['usuario_id'];

// Citas de hoy de este usuario
$hoy = date('Y-m-d');
$citasHoy = [];
foreach (Cita::getAllByUser($idUser) as $cita) {
    if (trim($cita->getFecha()) == $hoy) {
        $paciente = Paciente::getById($cita->getPaciente());
        $nombrePaciente = $paciente ? $paciente->getNombres() . " " . $paciente->getApellidos() : "Desconocido";
        $citasHoy[] = [
            'hora' => substr($cita->getHora(), 0, 5), // hh:mm
            'paciente' => $nombrePaciente,
            'motivo' => $cita->getMotivo(),
            'estado' => ucfirst($cita->getEstado())
        ];
    }
}
// Para el calendario (todas las citas de este usuario, igual que antes)
$eventos = [];
foreach (Cita::getAllByUser($idUser) as $cita) {
    $fechaInicio = $cita->getFecha() . 'T' . $cita->getHora();
    $horaFin = date('H:i:s', strtotime($cita->getHora() . ' +30 minutes'));
    $fechaFin = $cita->getFecha() . 'T' . $horaFin;
    $paciente = Paciente::getById($cita->getPaciente());
    $nombrePaciente = $paciente ? $paciente->getNombres() . " " . $paciente->getApellidos() : "Desconocido";
    $eventos[] = [
        'id' => $cita->getId(),
        'title' => $nombrePaciente . ' | ' . $cita->getMotivo(),
        'start' => $fechaInicio,
        'end'   => $fechaFin,
        'estado' => strtolower($cita->getEstado()),
        'extendedProps' => [
            'estado' => strtolower($cita->getEstado()),
            'observaciones' => $cita->getObservaciones()
        ]
    ];
}
?>
<script>
var eventos = <?php echo json_encode($eventos); ?>;
</script>
<style>
    body {
      background-color: #f5f9fa;
    }

    /* ================= Header Card ================= */
    .header-card {
      position: relative;
      overflow: hidden;
      border-radius: .75rem;
      height: 300px;
      background: url('assets/images/baner.png') center center / cover no-repeat;
      margin-bottom: 2rem;
    }
    .header-card::before {
      /* oscurecer ligeramente para mayor contraste */
      content: '';
      position: absolute;
      inset: 0;
     
    }
    .header-card .card-content {
      position: relative;
      z-index: 1;
      height: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      color: #fff;
    }
    .header-card .date-badge {
      display: inline-flex;
      align-items: center;
      gap: .25rem;
      background: rgba(255,255,255,0.25);
      padding: .35rem .75rem;
      border-radius: .5rem;
      font-size: .9rem;
    }
    .header-card .greeting {
      margin-top: 1rem;
    }
    .header-card .greeting h2 {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 600;
    }
    .header-card .greeting p {
      margin: .25rem 0 0;
      font-size: 1rem;
      opacity: .9;
    }

    /* ================ Stat Cards ================ */
    .stat-card {
      background: #fff;
      border-radius: 0.5rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      border-left-width: 0.5rem !important;
      overflow: hidden;
    }
    .stat-card .card-body {
      padding: 1.25rem;
    }
    .stat-card.border-primary { border-left-color: #0d6efd !important; }
    .stat-card.border-warning { border-left-color: #ffc107 !important; }
    .stat-card.border-success { border-left-color: #198754 !important; }
    .stat-card.border-danger  { border-left-color: #dc3545 !important; }

    .stat-title {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 0.25rem;
    }
    .stat-number {
      font-size: 2.25rem;
      font-weight: 700;
      margin: 0;
    }
    .stat-icon {
      font-size: 2.5rem;
    }
    .stat-card .card-footer {
      padding: 0.75rem 1.25rem;
      background: transparent;
      border-top: 1px solid #e9ecef;
    }
    .stat-card .card-footer a {
      text-decoration: none;
      font-size: 0.85rem;
      color: inherit;
    }
    .stat-card .card-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="container py-4">
    <!-- ======== Header Card ======== -->
    <div class="header-card">
      
      <div class="card-content">
        <!-- Fecha / hora -->
        <div>
          <span id="dateTime" class="date-badge">
            <i class="bi bi-calendar-event"></i>
            <!-- aquí irá la fecha desde JS -->
          </span>
          <div class="greeting">
            <h2>¡Buen día, <?php echo $_SESSION['usuario_nombre'] ; ?>!</h2>
            <p>Que tengas un excelente inicio de semana.</p>
          </div>
        </div>
        <!-- aquí la ilustración está en el fondo, puedes dejar un hueco vacío a la derecha -->
        <div class="d-none d-lg-block">
          <!-- opcional: podrías poner un img si no está todo en el background -->
        </div>
      </div>
    </div>

    <!-- ======== Estadísticas ======== -->
    <div class="row g-3 mb-4">

      <!-- Total Pacientes -->
      <div class="col-md-3">
        <div class="card stat-card border-start border-1 border-primary">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="stat-title">Total Pacientes</h5>
              <h2 class="stat-number text-primary">
                <?php echo count($pacientes); ?>
              </h2>
            </div>
            <i class="bi bi-people-fill stat-icon text-primary"></i>
          </div>
          <div class="card-footer">
            <a href="?controller=paciente&action=show" class="text-primary">Ver detalles →</a>
          </div>
        </div>
      </div>

      <!-- Total Historiales -->
      <div class="col-md-3">
        <div class="card stat-card border-start border-1 border-warning">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="stat-title">Total Historiales</h5>
              <h2 class="stat-number text-warning">
                <?php echo count($historias); ?>
              </h2>
            </div>
            <i class="bi bi-file-medical stat-icon text-warning"></i>
          </div>
          <div class="card-footer">
            <a href="?controller=historia&action=show" class="text-warning">Ver detalles →</a>
          </div>
        </div>
      </div>

      <!-- Melanomas Detectadas -->
      <div class="col-md-3">
        <div class="card stat-card border-start border-1 border-success">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="stat-title">Melanomas Detectadas</h5>
              <h2 class="stat-number text-success">
                <?php echo count($melanomas); ?>
              </h2>
            </div>
            <i class="bi bi-eyeglasses stat-icon text-success"></i>
          </div>
          <div class="card-footer">
            <a href="?controller=usuario&action=reportGeneral" class="text-success">Ver detalles →</a>
          </div>
        </div>
      </div>

      <!-- Citas de hoy -->
<div class="col-md-3">
  <div class="card stat-card border-start border-1 border-info">
    <div class="card-body d-flex flex-column align-items-start justify-content-between">
      <h5 class="stat-title mb-2">Citas de hoy</h5>
      <div style="min-height:70px; width: 100%;">
        <?php if (count($citasHoy) > 0): ?>
          <ul class="list-unstyled mb-1">
            <?php foreach ($citasHoy as $cita): ?>
              <li>
                <span class="fw-bold"><?= $cita['hora'] ?></span>
                - <?= $cita['paciente'] ?>
                <span class="text-muted">(<?= $cita['motivo'] ?>)</span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <span class="text-secondary">Sin citas hoy</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="card-footer">
      <a href="#" class="text-info" data-bs-toggle="modal" data-bs-target="#modalCalendario">
        Ver todas mis citas →
      </a>
    </div>
  </div>
</div>


    </div>
  </div>

<!-- Modal para el calendario -->
<div class="modal fade" id="modalCalendario" tabindex="-1" aria-labelledby="modalCalendarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalCalendarioLabel">Mi calendario de citas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- IMPORTANTE: el id DEBE ser ÚNICO -->
        <div id="calendario-citas-modal" style="min-height: 450px;"></div>
      </div>
    </div>
  </div>
</div>
<script>
// Usaremos una variable para el calendario
let calendarInstance = null;

// Cuando el modal se muestra
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalCalendario');
    modalEl.addEventListener('shown.bs.modal', function () {
        // SOLO inicializar si aún no existe
        if (!calendarInstance) {
            var calendarEl = document.getElementById('calendario-citas-modal');
            calendarInstance = new FullCalendar.Calendar(calendarEl, {
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
                events: eventos, // Tu variable PHP eventos debe estar arriba
                eventOverlap: false,
                slotDuration: '00:30:00',
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
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
            calendarInstance.render();
        }
    });

    // OPCIONAL: Si quieres "resetear" el calendario cuando se cierre el modal:
    modalEl.addEventListener('hidden.bs.modal', function () {
        if (calendarInstance) {
            calendarInstance.destroy();
            calendarInstance = null;
        }
    });
});
</script>


  <script>
  function updateDateTime() {
    const now = new Date();
    // Opciones de formato, ajústalas si quieres mostrar más/menos
    const opts = {
      weekday: 'short',    // "lun", "mar", ...
      year:    'numeric',  // "2025"
      month:   'short',    // "abr"
      day:     'numeric',  // "16"
      hour:    '2-digit',
      minute:  '2-digit'
    };
    // Formatear en español
    const formatted = new Intl.DateTimeFormat('es-ES', opts).format(now);
    document.getElementById('dateTime').textContent = formatted;
  }

  // Arranca ya y luego actualiza cada 30 segundos
  updateDateTime();
  setInterval(updateDateTime, 30 * 1000);
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<style>
/* Los mismos estilos que usabas antes para los boxes */
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
</style>
