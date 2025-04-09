<?php 
if(!isset($_SESSION)) { 
    session_start();   
}

// Recuperar el sort y dir actuales para mostrar iconos y conservar en paginación
// Por defecto, ordena por fecha (puedes cambiar según lo que necesites)
$current_sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'fecha';
$current_dir  = isset($_SESSION['dir']) ? $_SESSION['dir'] : 'asc';

// Función para invertir la dirección
function invertDir($dir) {
  return ($dir === 'asc') ? 'desc' : 'asc';
}
?>

<div class="container text-center px-4 py-3" style="max-height: 85vh; overflow-y:auto;">
  <h1 class="text-success mb-3">Lista de Citas</h1>

  <!-- Formulario de búsqueda -->
  <form class="row g-3 mb-3" action="?controller=cita&action=buscar" method="post">
    <div class="col-auto">
      <input class="form-control" id="search" name="searchTerm" type="text" placeholder="Buscar por fecha, motivo o paciente">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-success">
        <i class="bi bi-search"></i> Buscar
      </button>
    </div>
  </form>

  <!-- Mensaje de sesión, si existe -->
  <?php if (isset($_SESSION['mensaje'])) { ?>
    <div class="alert alert-success">
      <strong><?php echo $_SESSION['mensaje']; ?></strong>
    </div>
  <?php unset($_SESSION['mensaje']); } ?>

  <!-- Tabla responsiva -->
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-success">
        <tr>
          <!-- Fecha -->
          <th>
            <a href="?controller=cita&action=show&sort=fecha&dir=<?php echo ($current_sort === 'fecha') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              Fecha
              <?php if($current_sort === 'fecha'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Hora -->
          <th>
            <a href="?controller=cita&action=show&sort=hora&dir=<?php echo ($current_sort === 'hora') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              Hora
              <?php if($current_sort === 'hora'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Paciente -->
          <th>
            <a href="?controller=cita&action=show&sort=paciente&dir=<?php echo ($current_sort === 'paciente') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              Paciente
              <?php if($current_sort === 'paciente'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Motivo -->
          <th>
            <a href="?controller=cita&action=show&sort=motivo&dir=<?php echo ($current_sort === 'motivo') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              Motivo
              <?php if($current_sort === 'motivo'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Estado -->
          <th>
            <a href="?controller=cita&action=show&sort=estado&dir=<?php echo ($current_sort === 'estado') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              Estado
              <?php if($current_sort === 'estado'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Acciones -->
          <th colspan="2" class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          // Se asume que $lista_citas contiene un arreglo de objetos Cita.
          foreach ($lista_citas as $cita) { 
            // Recuperar datos del paciente asociado para mostrar nombre y apellido
            $paciente = Paciente::getById($cita->getPaciente());
            $nombrePaciente = $paciente ? $paciente->getNombres() . " " . $paciente->getApellidos() : "Desconocido";
        ?>
          <tr>
            <td><?php echo $cita->getFecha(); ?></td>
            <td><?php echo $cita->getHora(); ?></td>
            <td><?php echo $nombrePaciente; ?></td>
            <td><?php echo $cita->getMotivo(); ?></td>
            <td><?php echo ucfirst($cita->getEstado()); ?></td>
            
            <!-- Botón Actualizar -->
            <td>
              <button type="button" class="btn btn-warning"
                      onclick="location.href='?controller=cita&action=showupdate&id=<?php echo $cita->getId()?>'">
                <i class="bi bi-pencil"></i> Actualizar
              </button>
            </td>
            
            <!-- Botón Eliminar -->
            <td>
              <button type="button" class="btn btn-danger"
                      onclick="location.href='?controller=cita&action=delete&id=<?php echo $cita->getId()?>'">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- Paginación -->
  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $botones; $i++) { ?>
        <li class="page-item <?php echo (isset($_GET['boton']) && $_GET['boton'] == $i) ? 'active' : ''; ?>">
          <a class="page-link" href="?controller=cita&action=show&boton=<?php echo $i; ?>&sort=<?php echo $current_sort; ?>&dir=<?php echo $current_dir; ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php } ?>
    </ul>
  </nav>
</div>
