<?php
if(!isset($_SESSION)) { 
    session_start();   
}
?>

<div class="container text-center px-4 py-3"><!-- Espaciado vertical superior e inferior -->

  <h1 class="text-success mb-3">Lista de Pacientes</h1>

  <!-- Formulario de búsqueda -->
  <form class="row g-3 mb-4" action="?controller=paciente&action=buscar" method="post">
    <div class="col-auto">
      <input class="form-control" id="cedula" name="cedula" type="text" placeholder="1717899322">
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
      <thead class="table-success"><!-- Encabezado con fondo verde claro -->
        <tr>
          <th>Cédula</th>
          <th>Nombres</th>
          <th>Apellidos</th>
          <th>Ocupación</th>
          <th>Email</th>
          <th>Tipo de Sangre</th>
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
            <td><?php echo $paciente->getTposangre(); ?></td>
            
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
            
            <!-- Botón Crear/Editar Historia Clínica -->
            <td>
              <button type="button" class="btn btn-success"
                      onclick="location.href='?controller=historia&action=register&id=<?php echo $paciente->getId()?>'">
                <i class="bi bi-journal-plus"></i> Crear/Editar H. Clínica
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
        <li class="page-item">
          <a class="page-link" href="?controller=paciente&action=show&boton=<?php echo $i; ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php } ?>
    </ul>
  </nav>
</div>
