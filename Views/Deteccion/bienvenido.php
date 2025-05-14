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

      <!-- Otras Lesiones -->
      <div class="col-md-3">
        <div class="card stat-card border-start border-1 border-danger">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="stat-title">Otras Lesiones</h5>
              <h2 class="stat-number text-danger">
                <?php echo count($nomelanomas); ?>
              </h2>
            </div>
            <i class="bi bi-heart-pulse stat-icon text-danger"></i>
          </div>
          <div class="card-footer">
            <a href="?controller=usuario&action=reportGeneral" class="text-danger">Ver detalles →</a>
          </div>
        </div>
      </div>

    </div>
  </div>
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
