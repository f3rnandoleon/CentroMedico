<?php 
if (!isset($_SESSION)) { 
  session_start(); 
} 
?>
  <style>
    /* Estilos para lograr un look "documento impreso" */
    body {
      background-color: #fff; /* Fondo blanco */
      color: #000; /* Texto negro */
      font-family: Arial, sans-serif;
    }
    .report-container {
      border: 2px solid #000; 
      padding: 20px;
      margin-top: 20px;
    }
    .report-title {
      text-align: center;
      text-transform: uppercase;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .section-title {
      text-transform: uppercase;
      font-weight: bold;
      margin-top: 25px;
      margin-bottom: 10px;
      border-bottom: 1px solid #000;
      padding-bottom: 5px;
    }
    .info-line {
      margin-bottom: 5px;
    }
    .info-line strong {
      display: inline-block;
      width: 150px; /* Ajusta el ancho para alinear */
    }

    /* Estilos para el diagnóstico con imagen */
    .diagnostico-container {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .diagnostico-image {
      max-width: 200px;
      border: 2px solid #000;
      border-radius: 8px;
    }
    .diagnostico-text {
      padding: 10px;
      border: 1px solid #000;
      background-color: #f8f9fa;
      border-radius: 5px;
    }
  </style>

<div class="container mt-3" style="max-height: 84vh; overflow-y:auto;">
  
  <?php if ($historia): ?>
    <div class="report-container">
      
      <!-- Título principal -->
      <h3 class="report-title">
        REPORTE HISTORIA CLÍNICA
      </h3>
      
      <!-- Sección: INFORMACIÓN PACIENTE -->
      <h5 class="section-title">INFORMACIÓN DEL PACIENTE</h5>
      <?php
        // Obtenemos datos del paciente
        $paciente = Paciente::getById($historia->getPaciente());
        $nombreCompleto = $paciente->getNombres() . " " . $paciente->getApellidos();
        $cedula = $paciente->getCedula() ?? "----";
        $sexo = $paciente->getGenero() ?? "----";
        $ocupacion = $paciente->getOcupacion() ?? "----";
        $estCivil = $paciente->getEstcivil() ?? "----";
        $direccion = $paciente->getDireccion() ?? "----";
        $telefono = $paciente->getTelefono() ?? "----";
        $email = $paciente->getEmail() ?? "----";
      ?>
      
      <div class="info-line">
        <strong>Nombre:</strong> <?php echo $nombreCompleto; ?>
      </div>
      <div class="info-line">
        <strong>Cédula:</strong> <?php echo $cedula; ?>
      </div>

      <div class="info-line">
        <strong>Sexo:</strong> <?php echo $sexo; ?>
      </div>
      <div class="info-line">
        <strong>Ocupación:</strong> <?php echo $ocupacion; ?>
      </div>
      <div class="info-line">
        <strong>Estado Civil:</strong> <?php echo $estCivil; ?>
      </div>
      <div class="info-line">
        <strong>Dirección:</strong> <?php echo $direccion; ?>
      </div>
      <div class="info-line">
        <strong>Teléfono:</strong> <?php echo $telefono; ?>
      </div>
      <div class="info-line">
        <strong>Email:</strong> <?php echo $email; ?>
      </div>
      <div class="info-line">
        <strong>Número de Historia Clínica:</strong> <?php echo $historia->getNumero(); ?>
      </div>
      
      <!-- Sección: Diagnostico -->
      <h5 class="section-title">Diagnóstico</h5>
      <?php if (!empty($historia->getImagen())): ?>
        <div class="diagnostico-container">
          <!-- Imagen de la lesión -->
          <img src="<?= $historia->getImagen(); ?>" alt="Imagen del diagnóstico" class="diagnostico-image">

          <!-- Diagnóstico -->
          <div class="diagnostico-text">
            <strong>Resultado:</strong>
            <p><?= $historia->getDiagnostico(); ?></p>
          </div>
        </div>
      <?php else: ?>
        <p><?= $historia->getDiagnostico() ?? "----"; ?></p>
      <?php endif; ?>

      <!-- Sección: OBSERVACIONES DEL DERMATÓLOGO -->
      <h5 class="section-title">Observaciones del Dermatólogo</h5>
      <p><?php echo $historia->getObservaciones() ?? "----"; ?></p>
      
      <!-- Sección: RECOMENDACIONES -->
      <h5 class="section-title">Recomendaciones</h5>
      <p><?php echo $historia->getRecomendacion() ?? "----"; ?></p>
      
      <!-- Botón Volver -->
      <div class="text-end mt-4">
        <a href="?controller=historia&action=show" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Volver
        </a>
      </div>
      
    </div><!-- Fin report-container -->
  <?php else: ?>
    <div class="alert alert-warning" role="alert">
      No se encontró ninguna historia clínica con el número proporcionado.
    </div>
  <?php endif; ?>
</div>
