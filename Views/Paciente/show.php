<?php 
if(!isset($_SESSION)) { 
    session_start();   
}

// Recuperar el sort y dir actuales para mostrar iconos y conservar en paginación
$current_sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'cedula';
$current_dir  = isset($_SESSION['dir']) ? $_SESSION['dir'] : 'asc';

// Función para invertir la dirección
function invertDir($dir) {
  return ($dir === 'asc') ? 'desc' : 'asc';
}
?>

<div class="container text-center px-4 py-3" style="max-height: 85vh; overflow-y:auto;"><!-- Espaciado vertical superior e inferior -->

  <h1 class="text-success mb-3">Lista de Pacientes</h1>

  <!-- Formulario de búsqueda -->
  <form class="row g-3 mb-3" action="?controller=paciente&action=buscar" method="post">
    <div class="col-auto">
      <input class="form-control" id="search" name="search" type="text" placeholder="1717899322">
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
          <!-- Cédula -->
          <th>
            <a href="?controller=paciente&action=show&sort=cedula&dir=<?php echo ($current_sort === 'cedula') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              Cédula
              <?php if($current_sort === 'cedula'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Nombres -->
          <th>
            <a href="?controller=paciente&action=show&sort=nombres&dir=<?php echo ($current_sort === 'nombres') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Nombres
              <?php if($current_sort === 'nombres'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Apellidos -->
          <th>
            <a href="?controller=paciente&action=show&sort=apellidos&dir=<?php echo ($current_sort === 'apellidos') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Apellidos
              <?php if($current_sort === 'apellidos'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Ocupación -->
          <th>
            <a href="?controller=paciente&action=show&sort=ocupacion&dir=<?php echo ($current_sort === 'ocupacion') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Ocupación
              <?php if($current_sort === 'ocupacion'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Email -->
          <th>
            <a href="?controller=paciente&action=show&sort=email&dir=<?php echo ($current_sort === 'email') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Email
              <?php if($current_sort === 'email'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Teléfono -->
          <th>
            <a href="?controller=paciente&action=show&sort=telefono&dir=<?php echo ($current_sort === 'telefono') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Teléfono
              <?php if($current_sort === 'telefono'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>
          <!-- Acciones -->
          <th colspan="3" class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lista_pacientes as $paciente) { ?>
          <tr>
            <td><?php echo $paciente->getCedula(); ?></td>
            <td><?php echo $paciente->getNombres(); ?></td>
            <td><?php echo $paciente->getApellidos(); ?></td>
            <td><?php echo $paciente->getOcupacion(); ?></td>
            <td><?php echo $paciente->getEmail(); ?></td>
            <td><?php echo $paciente->getTelefono(); ?></td>
            
            <!-- Botón Actualizar -->
            <td>
              <button type="button" class="btn btn-warning"
                      onclick="location.href='?controller=paciente&action=showupdate&id=<?php echo $paciente->getId()?>'">
                <i class="bi bi-pencil"></i> Actualizar
              </button>
            </td>
            
            <!-- Botón Eliminar -->
            <td>
              <button type="button" class="btn btn-danger"
                      onclick="location.href='?controller=paciente&action=delete&id=<?php echo $paciente->getId()?>'">
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
          <!-- Conservar sort y dir en la paginación para no perder el orden actual -->
          <a class="page-link" href="?controller=paciente&action=show&boton=<?php echo $i; ?>&sort=<?php echo $current_sort; ?>&dir=<?php echo $current_dir; ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php } ?>
    </ul>
  </nav>
</div>
