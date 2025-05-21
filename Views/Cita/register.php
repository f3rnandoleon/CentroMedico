<?php if(!isset($_SESSION)) { session_start(); } ?>

<div class="container px-4 py-3" style="max-height: 87vh; overflow-y:auto;">
  <!-- Card principal -->
  <div class="card shadow">
    <!-- Cabecera con color verde -->
    <div class="card-header text-center text-white" style="background-color:#28a688;">
      <h3 class="mb-0">Registro de Cita</h3>
    </div>
    
    <!-- Cuerpo del card -->
    <div class="card-body">
      <!-- La acción apunta al controlador de citas y a la acción save -->
      <form action='?controller=cita&action=save' method='post' id="eventForm">
        
        <!-- Paciente  -->
        <div class="mb-3 row">
          <label for="paciente" class="col-sm-2 col-form-label">Paciente:</label>
          <div class="col-sm-10">
          <select name="paciente" class="form-select" required>
              <option value="">Seleccione un paciente</option>
              <?php 
              $pacientes = Paciente::all();
              foreach ($pacientes as $paciente) { ?>
                <option value="<?= $paciente->getId(); ?>"
                >
                  <?= $paciente->getNombres() . " " . $paciente->getApellidos(); ?>
                </option>
              <?php } ?>
            </select>

          </div>
        </div>
        
        <!-- Fecha de la cita -->
        <div class="mb-3 row">
          <label for="fecha" class="col-sm-2 col-form-label">Fecha:</label>
          <div class="col-sm-10">
            <div class="input-group" id="datePicker">
              <input type="date" class="form-control" id="fecha" name="fecha" required autocomplete="off">
              <span class="input-group-text">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
        </div>
        
        <!-- Hora de la cita -->
        <div class="mb-3 row">
          <label for="hora" class="col-sm-2 col-form-label">Hora:</label>
          <div class="col-sm-10">
            <input type="time" class="form-control" id="hora" name="hora" required autocomplete="off">
          </div>
        </div>
        
        <!-- Motivo -->
        <div class="mb-3 row">
          <label for="motivo" class="col-sm-2 col-form-label">Motivo:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="motivo" name="motivo"
                   placeholder="Ingrese el motivo de la cita" required autocomplete="off">
          </div>
        </div>
        
        <!-- Observaciones -->
        <div class="mb-3 row">
          <label for="observaciones" class="col-sm-2 col-form-label">Observaciones:</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="observaciones" name="observaciones" placeholder="Ingrese observaciones (opcional)"></textarea>
          </div>
        </div>
        <!-- Dermatólogo -->
        <div class="mb-3 row">
          <label for="usuario" class="col-sm-2 col-form-label">Dermatólogo:</label>
          <div class="col-sm-10">
            <select name="usuario" id="usuario" class="form-select" required>
              <option value="">Seleccione un dermatólogo</option>
              <?php 
              $usuarios = Usuario::all();
              foreach ($usuarios as $usuario) { 
                if (strtolower($usuario->getRol()) == 'dermatologo') { ?>
                  <option value="<?= $usuario->getId(); ?>">
                    <?= $usuario->getNombres() . " " . $usuario->getApellidos(); ?>
                  </option>
              <?php }
              } ?>
            </select>
          </div>
        </div>

        <!-- Campo oculto para el estado de la cita, se asigna 'pendiente' por defecto -->
        <input type="hidden" name="estado" value="pendiente">
        
        <!-- Botones Guardar / Cancelar -->
        <div class="row mt-4">
          <div class="col-sm-2 offset-sm-2">
            <button type="submit" class="btn btn-success w-100">
              <span class="glyphicon glyphicon-save"></span> Guardar
            </button>
          </div>
          <div class="col-sm-2">
            <button type="button" class="btn btn-danger w-100"
                    onclick="location.href='?controller=cita&action=show'">
              <span class="glyphicon glyphicon-hand-left"></span> Cancelar
            </button>
          </div>
        </div>
        
      </form>
    </div><!-- Fin card-body -->
  </div><!-- Fin card -->
</div><!-- Fin container -->

<!-- Scripts de jQuery y Bootstrap Datepicker -->
<script src="assets/js/jquery-2.1.0.min.js"></script>
<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    $('#datePicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true
    });
});
</script>
