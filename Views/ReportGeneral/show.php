<?php
// Agrupar los melanomas por mes
$melanomasPorMes = array_fill(0, 12, 0);
$otrasLesiones = count($nomelanomas);

foreach ($melanomas as $historia) {
    $mes = date('n', strtotime($historia->getFregistro())) - 1;
    $melanomasPorMes[$mes]++;
}

// Convertir los datos a JSON para usarlos en JavaScript
$datosMelanomasJson = json_encode($melanomasPorMes);
$datosPieJson = json_encode([count($melanomas), $otrasLesiones]);
?>

<style>
    body {
        background-color: #f5f9fa;
    }
    .stat-card, .chart-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>

<div class="container py-4"  style="max-height: 87vh; overflow-y:auto;">
    <h2 class="mb-4">Reporte General</h2>

    <div class="d-flex justify-content-end mb-3">
        <label for="selectAnio" class="me-2">Seleccionar Año:</label>
        <select class="form-select w-auto" id="selectAnio">
            <option value="2023" selected>2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
        </select>
    </div>

    <!-- Gráfico de Líneas -->
    <div class="card chart-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Melanomas Detectados</h5>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Pastel -->
    <div class="card chart-card">
        <div class="card-body">
            <h5 class="mb-3">Distribución de Casos</h5>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Cargar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Datos pasados desde PHP
    const datosMelanomas = <?php echo $datosMelanomasJson; ?>;
    const datosPie = <?php echo $datosPieJson; ?>;

    // Inicializar gráfico de líneas
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Melanomas',
                data: datosMelanomas,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.1)',
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

    // Inicializar gráfico de pastel
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Melanomas', 'Otras Lesiones'],
            datasets: [{
                data: datosPie,
                backgroundColor: ['#dc3545', '#198754']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
