<?php if(!isset($_SESSION)) { session_start(); } ?>

<div class="container px-4 py-3" style="max-height: 87vh; overflow-y:auto;">
  <!-- Card principal -->
  <div class="card shadow">
    <!-- Cabecera con color verde -->
    <div class="card-header text-center text-white" style="background-color:#28a688;">
      <h3 class="mb-0">Registro de Usuario</h3>
    </div>
    
    <!-- Cuerpo del card -->
    <div class="card-body">
      <!-- La acción apunta al controlador de usuario y a la acción save -->
      <form action='?controller=usuario&action=saveAdmin' method='post' id="eventForm">
        
        <!-- Nombres -->
        <div class="mb-3 row">
          <label for="nombres" class="col-sm-2 col-form-label">Nombres:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="nombres" name="nombres"
                   placeholder="Ingrese sus nombres" required autocomplete="off">
          </div>
        </div>
        
        <!-- Apellidos -->
        <div class="mb-3 row">
          <label for="apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellidos" name="apellidos"
                   placeholder="Ingrese sus apellidos" required autocomplete="off">
          </div>
        </div>
        
        <!-- Email -->
        <div class="mb-3 row">
          <label for="email" class="col-sm-2 col-form-label">Email:</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="Ingrese su correo electrónico" required autocomplete="off">
          </div>
        </div>
        
        <!-- Rol -->
        <div class="mb-3 row">
          <label for="rol" class="col-sm-2 col-form-label">Rol:</label>
          <div class="col-sm-10">
            <select class="form-select" id="rol" name="rol">
              <!-- Puedes ajustar las opciones a lo que necesites -->
              <option value="Dermatologo">Dermatologo</option>
              <option value="Admin">Administrador</option>
            </select>
          </div>
        </div>
        
        <!-- Nota: La clave no se ingresa manualmente, se genera en el controlador de forma aleatoria
                 y luego se envía al email registrado -->
        
        <!-- Botones Guardar / Cancelar -->
        <div class="row mt-4">
          <div class="col-sm-2 offset-sm-2">
            <button type="submit" class="btn btn-success w-100">
              <span class="glyphicon glyphicon-save"></span> Guardar
            </button>
          </div>
          <div class="col-sm-2">
            <button type="button" class="btn btn-danger w-100"
                    onclick="location.href='?controller=usuario&action=showAdmin'">
              <span class="glyphicon glyphicon-hand-left"></span> Cancelar
            </button>
          </div>
        </div>
        
      </form>
    </div><!-- Fin card-body -->
  </div><!-- Fin card -->
</div><!-- Fin container -->

<!-- Incluye jQuery (si aún no lo tienes en tu proyecto) -->
<script src="assets/js/jquery-2.1.0.min.js"></script>
