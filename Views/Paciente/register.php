<?php if(!isset($_SESSION)) { session_start(); } ?>

<div class="container  px-4 py-3 " style="max-height: 87vh; overflow-y:auto;">
  <!-- Card principal -->
  <div class="card shadow">
    <!-- Cabecera con color verde -->
    <div class="card-header text-center text-white " style="background-color:#28a688;">
      <h3 class="mb-0">Registro de Paciente</h3>
    </div>
    
    <!-- Cuerpo del card -->
    <div class="card-body">
      <form action='?controller=paciente&action=save' method='post' id="eventForm">
        
        <!-- Cédula -->
        <div class="mb-3 row">
          <label for="cedula" class="col-sm-2 col-form-label">Cédula:</label>
          <div class="col-sm-10">
            <input type="number" class="form-control" id="cedula" name="cedula"
                   placeholder="Ingrese la cédula del paciente" required autocomplete="off">
            <div id="prueba"></div>
          </div>
        </div>
        
        <!-- Nombres -->
        <div class="mb-3 row">
          <label for="nombres" class="col-sm-2 col-form-label">Nombres:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="nombres" name="nombres"
                   placeholder="Ingrese los nombres del paciente" required autocomplete="off">
          </div>
        </div>
        
        <!-- Apellidos -->
        <div class="mb-3 row">
          <label for="apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellidos" name="apellidos"
                   placeholder="Ingrese los apellidos del paciente" required autocomplete="off">
          </div>
        </div>
        
        <!-- Ocupación -->
        <div class="mb-3 row">
          <label for="ocupacion" class="col-sm-2 col-form-label">Ocupación:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="ocupacion" name="ocupacion"
                   placeholder="Ingrese la ocupación del paciente" required autocomplete="off">
          </div>
        </div>
        <!-- Telefono -->
        <div class="mb-3 row">
          <label for="telefono" class="col-sm-2 col-form-label">Telefono:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="telefono" name="telefono"
                   placeholder="Ingrese el telefono del paciente" required autocomplete="off">
          </div>
        </div>
        <!-- Email -->
        <div class="mb-3 row">
          <label for="email" class="col-sm-2 col-form-label">Email:</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="Ingrese el email del paciente" required autocomplete="off">
          </div>
        </div>
        
        
        
        <!-- Dirección -->
        <div class="mb-3 row">
          <label for="direccion" class="col-sm-2 col-form-label">Dirección:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="direccion" name="direccion"
                   placeholder="Ingrese la dirección del paciente" required autocomplete="off">
          </div>
        </div>
        
        <!-- Estado Civil -->
        <div class="mb-3 row">
          <label for="estcivil" class="col-sm-2 col-form-label">Estado Civil:</label>
          <div class="col-sm-10">
            <select class="form-select" id="estcivil" name="estcivil">
              <option value="S">Soltero</option>
              <option value="C">Casado</option>
              <option value="V">Viudo</option>
              <option value="D">Divorciado</option>
              <option value="UL">Unión Libre</option>
              <option value="UH">Unión de Hecho</option>
            </select>
          </div>
        </div>
        
        <!-- Género -->
        <div class="mb-3 row">
          <label for="genero" class="col-sm-2 col-form-label">Género:</label>
          <div class="col-sm-10">
            <select class="form-select" id="genero" name="genero">
              <option value="M">Masculino</option>
              <option value="F">Femenino</option>
              <option value="O">Otro</option>
            </select>
          </div>
        </div>
        
        <!-- Fecha de nacimiento -->
        <div class="mb-3 row">
          <label for="fnacimiento" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
          <div class="col-sm-10">
            <div class="input-group" id="datePicker">
              <input type="date" class="form-control" name="date" required autocomplete="off">
              <span class="input-group-text">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
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
            <button type="button" class="btn btn-danger w-100"
                    onclick="location.href='?controller=paciente&action=show'">
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
