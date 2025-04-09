<?php 
if(!isset($_SESSION)) { 
    session_start();   
}

// Recuperar el sort y dir actuales para mostrar iconos y conservar en paginación
$current_sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'fecha';
$current_dir  = isset($_SESSION['dir']) ? $_SESSION['dir'] : 'asc';

// Función para invertir la dirección
function invertDir($dir) {
  return ($dir === 'asc') ? 'desc' : 'asc';
}
?>

<div class="container text-center px-4 py-3" style="max-height: 87vh; overflow-y:auto;">
  <!-- Card principal -->
  <div class="card shadow">
    <!-- Cabecera con color verde -->
    <div class="card-header text-center text-white" style="background-color:#28a688;">
      <h3 class="mb-0">Actualizar Cita</h3>
    </div>
    
    <!-- Cuerpo del card -->
    <div class="card-body">
      <!-- La acción apunta al controlador de citas y a la acción update -->
      <form action='?controller=cita&action=update' method='post' id="eventForm">
        
        <!-- Campo oculto con el ID de la cita -->
        <input type="hidden" name="id" value="<?php echo $cita->getId(); ?>">
        <input type="hidden" name="paciente" value="<?php echo $cita->getPaciente(); ?>">
        <!-- Paciente (se asume que se ingresa el ID del paciente) -->
        <div class="mb-3 row">
          <label for="paciente" class="col-sm-2 col-form-label">Paciente:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" readonly
                   value="<?php 
                   $paciente=Paciente::getById($cita->getPaciente());
                   echo $paciente->getNombres()," ",$paciente->getApellidos(); ?>" required autocomplete="off">
          </div>
        </div>
        
        <!-- Fecha de la cita -->
        <div class="mb-3 row">
          <label for="fecha" class="col-sm-2 col-form-label">Fecha:</label>
          <div class="col-sm-10">
            <div class="input-group" id="datePicker" data-date="<?php echo $cita->getFecha(); ?>" data-date-format="yyyy-mm-dd">
              <input type="date" class="form-control" id="fecha" name="fecha"
                     required autocomplete="off"
                     value="<?php echo $cita->getFecha(); ?>" />
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
            <input type="time" class="form-control" id="hora" name="hora"
                   required autocomplete="off"
                   value="<?php echo $cita->getHora(); ?>">
          </div>
        </div>
        
        <!-- Motivo -->
        <div class="mb-3 row">
          <label for="motivo" class="col-sm-2 col-form-label">Motivo:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="motivo" name="motivo"
                   value="<?php echo $cita->getMotivo(); ?>"
                   required autocomplete="off">
          </div>
        </div>
        
        <!-- Estado -->
        <div class="mb-3 row">
          <label for="estado" class="col-sm-2 col-form-label">Estado:</label>
          <div class="col-sm-10">
            <select class="form-select" id="estado" name="estado">
              <!-- Opción actual de la cita -->
              <option value="<?php echo $cita->getEstado(); ?>">
                <?php echo ucfirst($cita->getEstado()); ?>
              </option>
              <!-- Resto de opciones -->
              <option value="pendiente">Pendiente</option>
              <option value="cancelada">Cancelada</option>
              <option value="realizada">Realizada</option>
            </select>
          </div>
        </div>
        
        <!-- Observaciones -->
        <div class="mb-3 row">
          <label for="observaciones" class="col-sm-2 col-form-label">Observaciones:</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="observaciones" name="observaciones" placeholder="Ingrese observaciones"><?php echo $cita->getObservaciones(); ?></textarea>
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
                    onclick="location.href='?controller=cita&action=show'">
              <span class="glyphicon glyphicon-hand-left"></span> Cancelar
            </button>
          </div>
        </div>
        
      </form>
    </div><!-- Fin card-body -->
  </div><!-- Fin card -->
</div><!-- Fin container -->

<!-- Incluye estilos y scripts de Bootstrap Datepicker -->
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
            // Revalida el campo date si usas alguna librería de validación
            $('#eventForm').formValidation('revalidateField', 'fecha');
        });

    $('#eventForm').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
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
            },
            hora: {
                validators: {
                    notEmpty: {
                        message: 'La hora es requerida'
                    }
                }
            },
            motivo: {
                validators: {
                    notEmpty: {
                        message: 'El motivo es requerido'
                    }
                }
            }
        }
    });
});
</script>
