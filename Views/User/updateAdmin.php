<?php if(!isset($_SESSION)) { session_start(); } ?>

<div class="container px-4 py-3" style="max-height: 87vh; overflow-y:auto;">
  <!-- Card principal -->
  <div class="card shadow">
    <!-- Cabecera con color verde -->
    <div class="card-header bg-success text-white text-center">
      <h3 class="mb-0">Actualizar Usuario</h3>
    </div>
    
    <!-- Cuerpo del card -->
    <div class="card-body">
      <!-- Ajusta la acción para tu controlador de usuarios -->
      <form action='?controller=usuario&action=updateAdmin' method='post' id="eventForm">
        
        <!-- Campo oculto con el ID del usuario -->
        <input type="hidden" name="id" value="<?php echo $usuario->getId(); ?>">

        <!-- Nombres -->
        <div class="mb-3 row">
          <label for="nombres" class="col-sm-2 col-form-label">Nombres:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="nombres" name="nombres"
                   value="<?php echo $usuario->getNombres(); ?>"
                   required autocomplete="off">
          </div>
        </div>

        <!-- Apellidos -->
        <div class="mb-3 row">
          <label for="apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellidos" name="apellidos"
                   value="<?php echo $usuario->getApellidos(); ?>"
                   required autocomplete="off">
          </div>
        </div>

        <!-- Email -->
        <div class="mb-3 row">
          <label for="email" class="col-sm-2 col-form-label">Email:</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email" name="email"
                   value="<?php echo $usuario->getEmail(); ?>"
                   required autocomplete="off">
          </div>
        </div>

        <!-- Rol -->
        <div class="mb-3 row">
          <label for="rol" class="col-sm-2 col-form-label">Rol:</label>
          <div class="col-sm-10">
            <select class="form-select" id="rol" name="rol">
              <!-- Opción actual -->
              <option value="<?php echo $usuario->getRol(); ?>">
                <?php echo ucfirst($usuario->getRol()); ?>
              </option>
                <?php if ($usuario->getRol()=="Admin") { ?>
                    <option value="Dermatologo">Dermatologo</option>
                <?php } ?>
                <?php if ($usuario->getRol()=="Dermatologo") { ?>
                    <option value="Admin">Admin</option>
                <?php } ?>

            </select>
          </div>
        </div>

        <!-- Fecha (puede ser fecha de creación, fecha de registro, etc.) -->
        <div class="mb-3 row">
          <label for="fecha" class="col-sm-2 col-form-label">Fecha:</label>
          <div class="col-sm-10">
            <div class="input-group" id="datePicker" 
                 data-date="<?php echo $usuario->getFecha(); ?>"  
                 data-date-format="yyyy-mm-dd">
              <input type="date" class="form-control" name="fecha"
                     required autocomplete="off"
                     value="<?php echo $usuario->getFecha(); ?>" />
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
                    onclick="location.href='?controller=usuario&action=showAdmin'">
              <span class="glyphicon glyphicon-hand-left"></span> Cancelar
            </button>
          </div>
        </div>

      </form>
    </div><!-- Fin card-body -->
  </div><!-- Fin card -->
</div><!-- Fin container -->

<!-- Estilos y scripts de Bootstrap Datepicker (ejemplo similar al original) -->
<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    $('#datePicker')
        .datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight: true
        })
        .on('changeDate', function(e) {
            // Si usas validación adicional, aquí iría la lógica
            $('#eventForm').formValidation('revalidateField', 'fecha');
        });

    // Ejemplo si usas formValidation plugin (opcional)
    $('#eventForm').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            nombres: {
                validators: {
                    notEmpty: {
                        message: 'El campo "Nombres" es requerido'
                    }
                }
            },
            fecha: {
                validators: {
                    notEmpty: {
                        message: 'La fecha es requerida'
                    },
                    date: {
                        format: 'yyyy-mm-dd',
                        message: 'La fecha no es válida'
                    }
                }
            }
        }
    });
});
</script>
