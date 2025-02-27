<?php 
if (!isset($_SESSION)) { 
  session_start(); 
} 
?>

<div class="container mt-3 px-4">
  <!-- Título en verde -->
  <h1 class="text-success text-center mb-4">Resumen Historias Clínicas</h1>
  
  <!-- Formulario de búsqueda -->
  <form class="row g-3 align-items-center mb-3" action="?controller=historia&action=buscar" method="post">
    <div class="col-auto">
      <input type="text" class="form-control" id="searchTerm" name="searchTerm" placeholder="0001">
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
  <?php } unset($_SESSION['mensaje']); ?>

  <!-- Tabla de historiales -->
  <div class="table-responsive mt-4">
    <table class="table table-hover align-middle">
      <!-- Encabezado con fondo verde claro -->
      <thead class="table-success">
        <tr>
          <th>Fecha Registro</th>
          <th>N. Historia Clínica</th>
          <th>Nombres Paciente</th>
          <th>Apellidos Paciente</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lista_historias as $historia) { ?>
          <tr>
            <td><?php echo $historia->getFregistro(); ?></td>
            <td><?php echo $historia->getNumero(); ?></td>
            <td>
              <?php 
                $paciente = Paciente::getById($historia->getPaciente());
                echo $paciente->getNombres();
              ?>
            </td>
            <td><?php echo $paciente->getApellidos(); ?></td>
            <td>
              <!-- Botón Ver Historia -->
              <button type="button" class="btn btn-info"
                      onclick="location.href='?controller=historia&action=reporteHistorico&id=<?php echo $paciente->getId(); ?>'">
                <i class="bi bi-eye"></i> Ver Historia
              </button>
              <!-- Botón Generar Reporte -->
              <button type="button" class="btn btn-success"
                      onclick="location.href='?controller=historia&action=register&id=<?php echo $paciente->getId(); ?>'">
                <i class="bi bi-file-earmark-plus"></i> Generar Reporte
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
          <li class="page-item">
            <a class="page-link" href="?controller=historia&action=show&boton=<?php echo $i; ?>">
              <?php echo $i; ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </nav>
  </div>
</div>
