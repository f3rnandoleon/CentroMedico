<style>
    body {
      background-color: #f5f9fa; /* Fondo claro */
    }
    .stat-card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .stat-card h5 {
      font-size: 1rem;
      margin-bottom: 0.5rem;
      color: #6c757d;
    }
    .stat-card h2 {
      font-weight: 700;
      margin: 0;
    }
    /* Para centrar ícono, título y número en la tarjeta */
    .stat-card .card-body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .chart-card {
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
<div class="container py-4" style="max-height: 85vh; overflow-y:auto;">
    
    <h2 class="mb-4">Reporte General</h2>

    <!-- Card con Gráfico de líneas -->
    <div class="card chart-card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="m-0">Melanomas Detectadas el mes de 
            <span id="mesSeleccionado">Octubre</span>
          </h5>
          
          <!-- Select para cambiar mes -->
          <select class="form-select w-auto" id="selectMes" onchange="cambiarMes()">
            <option value="Octubre" selected>Octubre</option>
            <option value="Noviembre">Noviembre</option>
            <option value="Diciembre">Diciembre</option>
          </select>
        </div>
        
        <div class="chart-container">
          <canvas id="lineChart"></canvas>
        </div>
      </div>
    </div>
    
    <!-- Card con Pie Chart -->
    <div class="card chart-card">
      <div class="card-body">
        <h5 class="mb-3">Pie Chart</h5>
        <div class="chart-container">
          <canvas id="pieChart"></canvas>
        </div>
      </div>
    </div>
    
  </div><!-- Fin container -->

  <!-- Si ya tienes bootstrap.bundle.min.js, no repitas -->
  <!-- <script src="js/bootstrap.bundle.min.js"></script> -->

  <!-- CDN de Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
    function cambiarMes() {
      const select = document.getElementById('selectMes');
      document.getElementById('mesSeleccionado').textContent = select.value;
      // Aquí podrías actualizar datos del gráfico en base al mes seleccionado
    }

    // Gráfico de líneas
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(ctxLine, {
      type: 'line',
      data: {
        labels: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'],
        datasets: [{
          label: 'Melanomas',
          data: [3,2,4,5,3,6,4,5,6,7,3,2,4,6,5,4,5,6,7,3,2,4,6,5,2,1,4,2,3,4,5],
          borderColor: '#28a745',
          backgroundColor: 'rgba(40,167,69,0.1)',
          tension: 0.3,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    // Gráfico de pastel
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: ['Melanomas', 'Lesiones X', 'Lesiones Y', 'Otras'],
        datasets: [{
          data: [55, 30, 10, 5],
          backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>