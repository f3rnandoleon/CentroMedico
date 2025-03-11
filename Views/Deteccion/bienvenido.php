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
<div class="container py-4" style="max-height: 84vh; overflow-y:auto;">
    
    <h2 class="mb-4">Datos Generales</h2>
    
    <!-- Row de tarjetas de estadísticas -->
    <div class="row g-3 mb-4">
      
      <!-- Card: Total Pacientes -->
      <!--<div class="col-md-3">
        <div class="card flex-row text-start" style="box-shadow: 0 0.5rem 1rem rgba(29, 145, 218, 0.81); background-color: #11B9F5; border-radius:17px">
          <div class="card-body text-white fw-bold" >           
            <h5>Total Pacientes</h5>
            <h2 ><?php echo count($pacientes)?></h2>
          </div>
		  <div class="card-body text-center py-4" >
			<div class="pb-2"  style="background-color:rgba(142, 218, 246, 0.51); border-radius:40%">
			<i class=" bi bi-people-fill fs-2 mb-2 text-white" ></i>
			</div>
		</div>

        </div>
      </div>-->
      <!-- Card: Total Pacientes -->
      <div class="col-md-3">
        <div class="card stat-card text-center">
          <div class="card-body">
            <!-- Ícono -->
            <i class="bi bi-people-fill fs-2 mb-2 text-primary"></i>
            <h5>Total Pacientes</h5>
            <h2 class="text-primary"><?php echo count($pacientes)?></h2>
          </div>
        </div>
      </div>
      <!-- Card: Total Consultas -->
      <div class="col-md-3">
        <div class="card stat-card text-center">
          <div class="card-body">
            <i class="bi bi-file-medical fs-2 mb-2 text-warning"></i>
            <h5>Total Historiales</h5>
            <h2 class="text-warning"><?php echo count($historias)?></h2>
          </div>
        </div>
      </div>
      
      <!-- Card: Melanomas Detectadas -->
      <div class="col-md-3">
        <div class="card stat-card text-center">
          <div class="card-body">
            <i class="bi bi-eyeglasses fs-2 mb-2 text-success"></i>
            <h5>Melanomas Detectadas</h5>
            <h2 class="text-success">55</h2>
          </div>
        </div>
      </div>
      
      <!-- Card: Otras Lesiones -->
      <div class="col-md-3">
        <div class="card stat-card text-center">
          <div class="card-body">
            <i class="bi bi-heart-pulse fs-2 mb-2 text-danger"></i>
            <h5>Otras Lesiones</h5>
            <h2 class="text-danger">53</h2>
          </div>
        </div>
      </div>
      
    </div><!-- Fin row de stats -->
    
    
  </div><!-- Fin container -->
