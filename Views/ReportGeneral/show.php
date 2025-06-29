<?php
// Ya vienen de tu controlador:
    // $historiales = HistoClinica::all();
    // $pacientes   = Paciente::all();
    // $usuarios    = Usuario::all();

// Mapeo de pacientes y usuarios por ID para acceso rápido
$mapPacientes = [];
foreach ($pacientes as $p) {
    $mapPacientes[$p->getId()] = $p;
}
$mapUsuarios = [];
foreach ($usuarios as $u) {
    $mapUsuarios[$u->getId()] = $u;
}

// Meses y agrupación por mes
$meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$conteoPorMes   = array_fill(0, 12, 0);
$reportesPorMes = array_fill(0, 12, []);

foreach ($historiales as $hist) {
    $idx = date('n', strtotime($hist->getFregistro())) - 1;
    $conteoPorMes[$idx]++;
    $reportesPorMes[$idx][] = $hist;
}
$datosPorMesJson = json_encode($conteoPorMes);

// -- NUEVO: Agrupar por año y mes para el filtro de la gráfica --
$conteoPorAno = [];

// Recorremos todos los historiales
foreach ($historiales as $hist) {
    $year = date('Y', strtotime($hist->getFregistro()));
    $month = date('n', strtotime($hist->getFregistro())) - 1;
    if (!isset($conteoPorAno[$year])) {
        // Inicializa los 12 meses a cero
        $conteoPorAno[$year] = array_fill(0, 12, 0);
    }
    $conteoPorAno[$year][$month]++;
}
// Lista de años ordenada
$anos = array_keys($conteoPorAno);
sort($anos);

// JSON para JS
$datosPorAnoJson   = json_encode($conteoPorAno);
$mesesJson         = json_encode($meses);
?>

<style>
    body { background-color: #f5f9fa; }
    .chart-card, .report-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .chart-container { position: relative; height: 300px; width: 100%; }
    .month-header {
        background: #343a40;
        color: #fff;
        padding: 8px 12px;
        font-weight: bold;
        border-radius: 4px;
        margin-top: 16px;
    }
    .report-item {
        display: flex;
        flex-wrap: nowrap;
        background: #fff;
        margin-bottom: 20px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .report-item .report-img {
        flex: 0 0 200px;
        max-width: 200px;
        height: 200px;
        object-fit: cover;
    }
    .report-item .report-body {
        flex: 1;
        padding: 16px;
    }
    .report-item .report-body .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .report-item .report-body .header .left {
        font-size: 1rem;
    }
    .report-item .report-body .header .left strong {
        font-size: 1.1rem;
    }
    .report-item .report-body .header .right .badge {
        font-size: 0.8rem;
        margin-left: 4px;
    }
    .report-item .report-body .field {
        margin-bottom: 8px;
    }
    .report-item .report-body .field strong {
        width: 100px;
        display: inline-block;
    }
    .report-item .report-body .observaciones {
        margin-top: 12px;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background: #f8f9fa;
        min-height: 80px;
    }
    .report-item .info-row {
        display: flex;
        gap: 24px;
        margin-bottom: 8px;
    }
    .report-item .info-row .field {
        font-size: 0.95rem;
    }
    .report-item .info-row .field strong {
        display: inline-block;
        width: 80px;
    }
</style>

    
<div class="container py-4" style="max-height: 87vh; overflow-y:auto;">
    <h2 class="mb-4">Reporte General</h2>

    <!-- FILTRO Año para la gráfica -->
    <div class="d-flex justify-content-end align-items-center mb-3">
        <label for="selectAnioGraf" class="me-2">Año:</label>
        <select id="selectAnioGraf" class="form-select filter-select w-auto" onchange="actualizarGrafico()">
            <?php foreach ($anos as $y): ?>
                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Gráfico de Líneas: Total de reportes por mes -->
    <div class="card chart-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Reportes por Mes</h5>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>
    

    <!-- Lista de reportes agrupados por mes -->
    <div class="card report-card mb-4">
        <div class="  p-3">
            <div class="d-flex align-items-center">
                <label for="filtroMes" class="me-2 mb-0">Filtrar Mes:</label>
                <select id="filtroMes" class="form-select w-auto me-4">
                    <option value="">Todos</option>
                    <?php foreach ($meses as $i => $nombre): ?>
                        <option value="<?php echo $i ?>"><?php echo $nombre ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="filtroAno" class="me-2 mb-0">Filtrar Año:</label>
                <select id="filtroAno" class="form-select w-auto">
                    <option value="">Todos</option>
                    <option value="<?php echo date('Y') ?>"><?php echo date('Y') ?></option>
                    <!-- Agrega más años si quieres -->
                </select>
            </div>
        </div>

        <!-- Detalle de reportes -->
    <div class="card report-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Detalle de Reportes</h5>

            <?php if (count($historiales) === 0): ?>
                <p class="text-muted">No hay reportes para mostrar.</p>
            <?php else: ?>
                <?php foreach ($reportesPorMes as $i => $lista): ?>
                    <?php if (count($lista) > 0): ?>
                        <div class="month-header" data-mes="<?php echo $i ?>">
                            <?php echo $meses[$i]; ?> — <?php echo count($lista); ?> reporte(s)
                        </div>
                        <?php foreach ($lista as $hist): 
                            // Objeto paciente y usuario completo
                            $pac = $mapPacientes[ $hist->getPaciente() ] ?? null;
                            $usr = $mapUsuarios[  $hist->getUsuario()   ] ?? null;
                        ?>
                            <div class="report-item" 
                                 data-mes="<?php echo date('n', strtotime($hist->getFregistro())) - 1 ?>"
                                 data-ano="<?php echo date('Y', strtotime($hist->getFregistro())) ?>">
                                <!-- Imagen -->
                                <?php if ($hist->getImagen()): ?>
                                    <img src="<?php echo htmlspecialchars($hist->getImagen()); ?>"
                                         class="report-img"
                                         alt="Imagen reporte">
                                <?php else: ?>
                                    <div class="report-img bg-secondary"></div>
                                <?php endif; ?>

                                <!-- Cuerpo -->
                                <div class="report-body">
                                    <div class="header">
                                        <div class="left">
                                            <strong>#<?php echo htmlspecialchars($hist->getNumero()); ?></strong>
                                            <span class="badge bg-secondary">
                                                <?php echo date('d/m/Y', strtotime($hist->getFregistro())); ?>
                                            </span>
                                        </div>
                                        <div class="right">
                                            <span class="badge bg-info text-dark">
                                                <?php echo $usr 
                                                    ? htmlspecialchars($usr->getNombres() . ' ' . $usr->getApellidos()) 
                                                    : 'ID ' . htmlspecialchars($hist->getUsuario()); ?>
                                            </span>
                                        </div>
                                    </div>

                                    
                                    <div class="field mb-1">
                                            <strong>Paciente:</strong>
                                            <?php echo $pac
                                                ? htmlspecialchars($pac->getNombres() . ' ' . $pac->getApellidos()) 
                                                : 'ID ' . htmlspecialchars($hist->getPaciente()); ?>
                                    </div>
                                    <div class="field mb-1">
                                        <strong>Diagnóstico:</strong>
                                        <?php echo htmlspecialchars($hist->getDiagnostico()); ?>
                                    </div>
                                    <div class="field mb-1">
                                        <strong>Motivo:</strong>
                                        <?php echo htmlspecialchars($hist->getMotivo()); ?>
                                    </div>

                                    <div class="observaciones">
                                        <?php echo nl2br(htmlspecialchars($hist->getObservaciones())); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>





<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos pasados desde PHP
    const datosPorAno   = <?php echo $datosPorAnoJson; ?>;
    const mesesLabels   = <?php echo $mesesJson; ?>;

    // Inicializamos el gráfico con el primer año disponible
    const primerAno = Object.keys(datosPorAno)[0];
    const ctxLine   = document.getElementById('lineChart').getContext('2d');
    let lineChart   = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: mesesLabels,
            datasets: [{
                label: 'Total Reportes',
                data: datosPorAno[primerAno],
                borderColor: 'rgba(33, 209, 83, 0.97)',
                backgroundColor: 'rgba(31, 232, 88, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Función que se dispara al cambiar el año
    function actualizarGrafico() {
        const select = document.getElementById('selectAnioGraf');
        const ano    = select.value;
        if (datosPorAno[ano]) {
            lineChart.data.datasets[0].data = datosPorAno[ano];
            lineChart.data.datasets[0].label = 'Total Reportes ' + ano;
            lineChart.update();
        }
    }
    actualizarGrafico();
    const selectMes = document.getElementById('filtroMes');
    const selectAno = document.getElementById('filtroAno');
    const reportItems = document.querySelectorAll('.report-item');
    const monthHeaders = document.querySelectorAll('.month-header');

    function filtrar() {
        const mes = selectMes.value;
        const ano = selectAno.value;

        reportItems.forEach(item => {
            const itemMes = item.getAttribute('data-mes');
            const itemAno = item.getAttribute('data-ano');
            const visibleMes = (!mes || mes === itemMes);
            const visibleAno = (!ano || ano === itemAno);
            item.style.display = (visibleMes && visibleAno) ? 'flex' : 'none';
        });
        // Ocultar headers de mes que no tengan items visibles
        monthHeaders.forEach(header => {
            const hMes = header.getAttribute('data-mes');
            const anyVisible = Array.from(reportItems).some(it =>
                it.getAttribute('data-mes') === hMes && it.style.display === 'flex'
            );
            header.style.display = anyVisible ? 'block' : 'none';
        });
    }

    selectMes.addEventListener('change', filtrar);
    selectAno.addEventListener('change', filtrar);
</script>