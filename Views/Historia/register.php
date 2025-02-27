<?php 
if (!isset($_SESSION)) { 
  session_start(); 
} 
?>

<?php $pacientes = Paciente::all(); ?>

<div class="container px-4 py-3" style="max-height: 87vh; overflow-y:auto;">
  <!-- Card principal -->
  <div class="card shadow">
    
    <!-- Cabecera con color verde -->
    <div class="card-header text-center text-white" style="background-color:#28a688;">
      <h3 class="mb-0">Añadir Historial Clínico</h3>
    </div>
    
    <!-- Cuerpo del card -->
    <div class="card-body">
      <form action='?controller=historia&action=save' method='post'>
        
        <!-- Fecha de Registro (auto) -->
        <div class="mb-3 row">
          <label for="fecha" class="col-sm-2 col-form-label">Fecha Registro:</label>
          <div class="col-sm-10">
            <input 
              type="date" 
              class="form-control" 
              id="fecha" 
              name="fecha" 
              required 
              readonly 
              value="<?php echo date('Y-m-d'); ?>"
            >
          </div>
        </div>
        
        <!-- Paciente -->
        <div class="mb-3 row">
          <label for="paciente" class="col-sm-2 col-form-label">Paciente:</label>
          <div class="col-sm-10">
            <select class="form-select" id="paciente" name="paciente" required>
              <option value="">Seleccione</option>
              <?php foreach ($pacientes as $paciente) { ?>
                <option value="<?php echo $paciente->getId(); ?>">
                  <?php echo $paciente->getNombres(); ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        
        <!-- Motivo de Consulta -->
        <div class="mb-3 row">
          <label for="motivo" class="col-sm-2 col-form-label">Motivo de Consulta:</label>
          <div class="col-sm-10">
            <input 
              type="text" 
              class="form-control" 
              id="motivo" 
              name="motivo" 
              placeholder="Describa el motivo de la consulta" 
              required 
            >
          </div>
        </div>
        
        <!-- Diagnóstico -->
        <div class="mb-3 row">
          <label for="diagnostico" class="col-sm-2 col-form-label">Diagnóstico:</label>
          <div class="col-sm-10">
            <input 
              type="text" 
              class="form-control" 
              id="diagnostico" 
              name="diagnostico" 
              placeholder="Ingrese el diagnóstico" 
              required 
            >
          </div>
        </div>
        
        <!-- Recomendación -->
        <div class="mb-3 row">
          <label for="recomendacion" class="col-sm-2 col-form-label">Recomendación:</label>
          <div class="col-sm-10">
            <input 
              type="text" 
              class="form-control" 
              id="recomendacion" 
              name="recomendacion" 
              placeholder="Ingrese la recomendación" 
              required 
            >
          </div>
        </div>
        
        <!-- Botones Guardar / Cancelar -->
        <div class="row mt-4">
          <div class="col-sm-2 offset-sm-2">
            <button type="submit" class="btn btn-success w-100">
              <span class="glyphicon glyphicon-save"></span> Guardar
            </button>
          </div>
          <div class="col-sm-2">
            <button 
              type="button" 
              class="btn btn-danger w-100"
              onclick="location.href='?controller=paciente&action=show'"
            >
              <span class="glyphicon glyphicon-hand-left"></span> Cancelar
            </button>
          </div>
        </div>
      </form>
    </div><!-- Fin card-body -->
    
  </div><!-- Fin card -->
</div><!-- Fin container -->


<!-- Si deseas usar la funcionalidad de datepicker antigua, mantén estos enlaces,
     aunque para un input[type="date"] no es estrictamente necesario. -->

<link rel="stylesheet" 
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" 
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script 
  src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js">
</script>

<script>
  // Solo si deseas usar datepicker adicionalmente
  $(document).ready(function() {
    // Ejemplo de inicialización
    $('#datePicker1, #datePicker2').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
  });
</script>
