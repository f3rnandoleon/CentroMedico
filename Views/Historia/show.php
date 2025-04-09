<?php 
if (!isset($_SESSION)) { 
  session_start(); 
}

// Recuperar los parámetros de ordenamiento actuales (si existen)
$current_sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'fregistro';
$current_dir  = isset($_SESSION['dir']) ? $_SESSION['dir'] : 'asc';

// Función auxiliar para invertir la dirección
function invertDir($dir) {
  return $dir === 'asc' ? 'desc' : 'asc';
}
?>
<style>
  .diagnostico-img-view {
      width: 100px;
      height: 100px;
      border: 2px solid #000;
      border-radius: 8px;
    }
    thead th {
  vertical-align: middle; /* Centra verticalmente el contenido */
  text-align: center; /* Asegura que el texto esté centrado horizontalmente */
}
</style>
<div class="container  mt-3 px-3" style="max-height: 84vh; overflow-y:auto;">
  <!-- Título en verde -->
  <div class="card shadow px-5 py-3">
    <h1 class="text-success text-center mb-4">Historias Clínicas</h1>

    <!-- Formulario de búsqueda -->
    <form class=" row g-3 align-items-center mb-3" action="?controller=historia&action=buscar" method="post">
      <div class="col-10">
        <input type="text" class="form-control" id="searchTerm" name="searchTerm" placeholder="0001">
      </div>
      <div class="col-2">
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
    <?php } unset($_SESSION['mensaje']); ?>

    <!-- Tabla de historiales -->
    <div class="table-responsive mt-4" >
      <table class="table table-hover align-middle">
        <!-- Encabezado con fondo verde claro -->
        <thead class="table-success">
          <tr class="py-4">
            <th>
              <a class="text-dark text-decoration-none" href="?controller=historia&action=show&sort=numero&dir=<?php echo ($current_sort === 'numero') ? invertDir($current_dir) : 'asc'; ?>">
                N. Historia 
                <?php if($current_sort === 'numero'): ?>
                  <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
                <?php endif; ?>
              </a>
            </th>
            <th>
              <a class="text-dark text-decoration-none" href="?controller=historia&action=show&sort=fregistro&dir=<?php echo ($current_sort === 'fregistro') ? invertDir($current_dir) : 'asc'; ?>">
                Fecha Registro
                <?php if($current_sort === 'fregistro'): ?>
                  <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
                <?php endif; ?>
              </a>
            </th>
            
            <th>
              <a class="text-dark text-decoration-none" href="?controller=historia&action=show&sort=nombres&dir=<?php echo ($current_sort === 'nombres') ? invertDir($current_dir) : 'asc'; ?>">
                Nombres Paciente
                <?php if($current_sort === 'nombres'): ?>
                  <i class="bi bi-arrow-<?php echo ($current_dir === 'asc') ? 'down' : 'up'; ?>"></i>
                <?php endif; ?>
              </a>
            </th>  
            <th class="text-center flex align-center">Atendido por </th>
            <th class="text-center" >Diagnostico </th>

            <th class="text-center pb-2">
                Imagen           
            </th>

            <th class="text-center pb-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lista_historias as $historia) { ?>
            <tr>
              
              <td><?php echo $historia->getNumero(); ?></td>
              <td><?php echo $historia->getFregistro(); ?></td>
              <td><?php $paciente = Paciente::getById($historia->getPaciente());
                  echo $paciente ? $paciente->getNombres() : 'N/A';
                  echo " ", $paciente ? $paciente->getApellidos() : 'N/A';
                ?><?php?>
              </td>
              <td>
              <?php if(!empty($historia->getUsuario())){
                 $usuario = Usuario::getById($historia->getUsuario());
                  echo $usuario ? $usuario->getNombres() : 'N/A';
                  echo " ", $usuario ? $usuario->getApellidos() : 'N/A';
              }else{ echo "---";}?>
              </td>
              <td><?php echo $historia->getDiagnostico() ?? "----"; ?></td>
              <td class="text-center">
                <?php if (!empty($historia->getImagen())): ?>
                  <div class="flex flex-lg-row">
                    <!-- Imagen de la lesión -->
                    <img src="<?= $historia->getImagen(); ?>" alt="Imagen del diagnóstico" class="diagnostico-img-view">
                  </div>
                <?php else: echo "---"?>

              <?php endif; ?>
              </td>
              <td class="text-center">
                <!-- Botón Ver Historia -->
                <button type="button" class="btn btn-info"
                        onclick="location.href='?controller=historia&action=reporteHistorico&numero=<?php echo $historia->getNumero(); ?>'">
                  <i class="bi bi-eye"></i> 
                </button>
                <!-- Botón Generar Reporte -->
                <button type="button" class="btn btn-success m-1 mx-2"
                        onclick="location.href='?controller=historia&action=reporte&id=<?php echo $historia->getId(); ?>'">
                  <i class="bi bi-file-earmark-plus"></i> 
                </button>
              </td>
              
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <!-- Paginación -->
      <nav>
        <ul class="pagination">
          <?php for ($i = 1; $i <= $botones; $i++) { ?>
            <li class="page-item <?php echo (isset($_GET['boton']) && $_GET['boton'] == $i) ? 'active' : ''; ?>">
              <a class="page-link" href="?controller=historia&action=show&boton=<?php echo $i; ?>&sort=<?php echo $current_sort; ?>&dir=<?php echo $current_dir; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </nav>
    </div>
    </div>
</div>
