<?php 
if(!isset($_SESSION)) { 
    session_start();   
}

// Recuperar parámetros de orden (si se guardaron en la sesión)
$current_sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'id';
$current_dir  = isset($_SESSION['dir']) ? $_SESSION['dir'] : 'asc';

// Función para invertir la dirección de orden
function invertDir($dir) {
  return ($dir === 'asc') ? 'desc' : 'asc';
}
?>

<div class="container text-center px-4 py-3" style="max-height: 85vh; overflow-y:auto;">
  <h1 class="text-primary mb-3">Lista de Usuarios</h1>

  <!-- Formulario de búsqueda (opcional) -->
  <form class="row g-3 mb-3" action="?controller=usuario&action=buscar" method="post">
    <div class="col-auto">
      <input class="form-control" id="search" name="search" type="text" placeholder="Busca por email o nombres">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-search"></i> Buscar
      </button>
    </div>
  </form>

  <!-- Mensajes de sesión -->
  <?php if (isset($_SESSION['mensaje'])) { ?>
    <div class="alert alert-success">
      <strong><?php echo $_SESSION['mensaje']; ?></strong>
    </div>
  <?php unset($_SESSION['mensaje']); } ?>

  <!-- Tabla de usuarios -->
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <!-- ID -->
          <th>
            <a href="?controller=usuario&action=show&sort=id&dir=<?php echo ($current_sort === 'id') ? invertDir($current_dir) : 'asc'; ?>" 
               class="text-dark text-decoration-none">
              ID
              <?php if($current_sort === 'id'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>

          <!-- Nombres -->
          <th>
            <a href="?controller=usuario&action=show&sort=nombres&dir=<?php echo ($current_sort === 'nombres') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Nombres
              <?php if($current_sort === 'nombres'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>

          <!-- Apellidos -->
          <th>
            <a href="?controller=usuario&action=show&sort=apellidos&dir=<?php echo ($current_sort === 'apellidos') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Apellidos
              <?php if($current_sort === 'apellidos'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>

          <!-- Email -->
          <th>
            <a href="?controller=usuario&action=show&sort=email&dir=<?php echo ($current_sort === 'email') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Email
              <?php if($current_sort === 'email'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>

          <!-- Rol -->
          <th>
            <a href="?controller=usuario&action=show&sort=rol&dir=<?php echo ($current_sort === 'rol') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Rol
              <?php if($current_sort === 'rol'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>

          <!-- Fecha -->
          <th>
            <a href="?controller=usuario&action=show&sort=fecha&dir=<?php echo ($current_sort === 'fecha') ? invertDir($current_dir) : 'asc'; ?>"
               class="text-dark text-decoration-none">
              Fecha
              <?php if($current_sort === 'fecha'): ?>
                <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
              <?php endif; ?>
            </a>
          </th>

          <!-- Acciones -->
          <th colspan="2" class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lista_usuarios as $usuario) { ?>
          <tr>
            <td><?php echo $usuario->getId(); ?></td>
            <td><?php echo $usuario->getNombres(); ?></td>
            <td><?php echo $usuario->getApellidos(); ?></td>
            <td><?php echo $usuario->getEmail(); ?></td>
            <td><?php echo $usuario->getRol(); ?></td>
            <td><?php echo $usuario->getFecha(); ?></td>
            
            <!-- Botón Actualizar -->
            <td>
              <button type="button" class="btn btn-warning"
                      onclick="location.href='?controller=usuario&action=showupdateAdmin&id=<?php echo $usuario->getId()?>'">
                <i class="bi bi-pencil"></i> Actualizar
              </button>
            </td>
            
            <!-- Botón Eliminar -->
            <td>
              <button type="button" class="btn btn-danger"
                      onclick="location.href='?controller=usuario&action=delete&id=<?php echo $usuario->getId()?>'">
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
          <a class="page-link" href="?controller=usuario&action=show&boton=<?php echo $i; ?>&sort=<?php echo $current_sort; ?>&dir=<?php echo $current_dir; ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php } ?>
    </ul>
  </nav>
</div>
