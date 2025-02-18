<?php
if (!isset($_SESSION)) { 
    session_start(); 
}
?>

<!-- Barra lateral con fondo verde (bg-success) y texto blanco -->
<div class="d-flex flex-column flex-shrink-0 bg-success text-white"
     style="width: 250px; min-height: 100vh;">

  <!-- Sección superior: ícono y nombre de usuario -->
  <div class="p-3 text-center border-bottom border-light">
    <div class="mb-2">
      <!-- Ícono de usuario, puedes sustituirlo por una imagen si deseas -->
      <i class="bi bi-person-circle fs-1"></i>
    </div>
    <h5 class="m-0">
      <?php echo isset($_SESSION['usuario_nombre']) 
                 ? $_SESSION['usuario_nombre'] 
                 : 'Invitado'; ?>
    </h5>
  </div>

  <!-- Menú vertical -->
  <ul class="nav nav-pills flex-column mb-auto mt-3">
    <?php if (isset($_SESSION['usuario'])) { ?>
      <!-- Pacientes -->
      <li class="nav-item">
      <a class="nav-link text-white" data-bs-toggle="collapse" 
         href="#menuPacientes" role="button" aria-expanded="false" 
         aria-controls="menuPacientes">
        <i class="bi bi-people"></i> Pacientes
      </a>
      <div class="collapse" id="menuPacientes">
        <ul class="list-unstyled ps-4">
          <li><a href="?controller=paciente&action=register" 
                 class="text-white text-decoration-none d-block py-1">
                 Registrar
               </a></li>
          <li><a href="?controller=paciente&action=show" 
                 class="text-white text-decoration-none d-block py-1">
                 Ver Pacientes
               </a></li>
        </ul>
      </div>
    </li>

      <!-- Consultas -->
      <li class="nav-item">
        <a class="nav-link text-white" data-bs-toggle="collapse" 
           href="#menuConsultas" role="button" aria-expanded="false" 
           aria-controls="menuConsultas">
          <i class="bi bi-file-medical"></i> Consultas
        </a>
        <div class="collapse" id="menuConsultas">
          <ul class="list-unstyled ps-4">
            <li><a href="?controller=historia&action=show"
                   class="text-white text-decoration-none d-block py-1">
                   Nueva Consulta
                 </a></li>
          </ul>
        </div>
      </li>

      <!-- Revisiones -->
      <li class="nav-item">
        <a class="nav-link text-white" data-bs-toggle="collapse" 
           href="#menuRevisiones" role="button" aria-expanded="false" 
           aria-controls="menuRevisiones">
          <i class="bi bi-journal-check"></i> Revisiones
        </a>
        <div class="collapse" id="menuRevisiones">
          <ul class="list-unstyled ps-4">
            <li><a href="?controller=consulta&action=show"
                   class="text-white text-decoration-none d-block py-1">
                   Ver consultas
                 </a></li>
            <li><a href="?controller=deteccion&action=detectar"
                   class="text-white text-decoration-none d-block py-1">
                   Detectar
                 </a></li>
          </ul>
        </div>
      </li>

      <!-- Detección -->
      <li class="nav-item">
        <a class="nav-link text-white" data-bs-toggle="collapse" 
           href="#menuDeteccion" role="button" aria-expanded="false" 
           aria-controls="menuDeteccion">
          <i class="bi bi-search"></i> Detección
        </a>
        <div class="collapse" id="menuDeteccion">
          <ul class="list-unstyled ps-4">
            <li><a href="?controller=deteccion&action=detectar"
                   class="text-white text-decoration-none d-block py-1">
                   Detectar
                 </a></li>
          </ul>
        </div>
      </li>
    <?php } ?>
  </ul>
</div>
