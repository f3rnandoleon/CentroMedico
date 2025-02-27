<?php
if (!isset($_SESSION)) { 
    session_start(); 
}
?>

<aside class="d-flex flex-column flex-shrink-0 text-white"
       style="width: 250px; min-height: 100vh; background-color: #28a688;"
       id="sidebarMenu">

  <!-- Sección superior: ícono y nombre de usuario -->
  <div class="p-3 text-center border-bottom border-light">
    <div class="mb-2">
      <i class="bi bi-person-circle fs-1"></i>
    </div>
    <h5 class="m-0">
      <?php echo isset($_SESSION['usuario_nombre']) 
                 ? $_SESSION['usuario_nombre'] 
                 : 'Invitado'; ?>
    </h5>
  </div>

  <!-- Menú vertical -->
  <ul class="nav nav-pills flex-column mb-auto mt-3 px-3 fs-6">
    <?php if (isset($_SESSION['usuario'])) { ?>
      
      <!-- Pacientes -->
      <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse"
           href="#menuPacientes"
           role="button"
           aria-expanded="false"
           aria-controls="menuPacientes">
          <span><i class="bi bi-people"></i> Pacientes</span>
          <i class="bi bi-chevron-down rotate-on-collapse"></i>
        </a>
        <div class="collapse" id="menuPacientes" data-bs-parent="#sidebarMenu">
          <ul class="list-unstyled ps-4">
            <li>
              <a href="?controller=paciente&action=register" 
                 class="text-white text-decoration-none d-block py-1">
                 Registrar
              </a>
            </li>
            <li>
              <a href="?controller=paciente&action=show" 
                 class="text-white text-decoration-none d-block py-1">
                 Ver Pacientes
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Historias Clínico -->
      <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse"
           href="#menuConsultas"
           role="button"
           aria-expanded="false"
           aria-controls="menuConsultas">
          <span><i class="bi bi-file-medical"></i> Historias Clínico</span>
          <i class="bi bi-chevron-down rotate-on-collapse"></i>
        </a>
        <div class="collapse" id="menuConsultas" data-bs-parent="#sidebarMenu">
          <ul class="list-unstyled ps-4">
            <li>
              <a href="?controller=historia&action=register"
                 class="text-white text-decoration-none d-block py-1">
                 Nueva Historia
              </a>
            </li>
            <li>
              <a href="?controller=historia&action=show"
                 class="text-white text-decoration-none d-block py-1">
                 Ver Historias
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- Detección -->
      <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse"
           href="#menuDeteccion"
           role="button"
           aria-expanded="false"
           aria-controls="menuDeteccion">
          <span><i class="bi bi-search"></i> Detección</span>
          <i class="bi bi-chevron-down rotate-on-collapse"></i>
        </a>
        <div class="collapse" id="menuDeteccion" data-bs-parent="#sidebarMenu">
          <ul class="list-unstyled ps-4">
            <li>
              <a href="?controller=deteccion&action=detectar"
                 class="text-white text-decoration-none d-block py-1">
                 Detectar
              </a>
            </li>
          </ul>
        </div>
      </li>

    <?php } ?>
  </ul>
</aside>

<!-- CSS para la flecha y el estilo "activo" -->
<style>
  .rotate-on-collapse {
    transition: transform 0.2s ease;
  }
  /* Estado "activo": rectángulo blanco con texto oscuro y borde verde */
  .nav-link.active {
    background-color: #fff !important;
    color: #28a688 !important;
    border: 1px solid #28a688;
    border-radius: 0.25rem;
  }
</style>

<script>
  // Selecciona todos los elementos colapsables
  const collapses = document.querySelectorAll('.collapse');

  collapses.forEach((collapseEl) => {
    // Cuando se expande (muestra) el collapse
    collapseEl.addEventListener('show.bs.collapse', () => {
      // Enlace que lo controla
      const toggler = document.querySelector(`[href="#${collapseEl.id}"]`);
      if (!toggler) return;
      
      // Rotar flecha
      const icon = toggler.querySelector('.rotate-on-collapse');
      if (icon) {
        icon.style.transform = 'rotate(180deg)';
      }
      // Agregar clase .active para el rectángulo
      toggler.classList.add('active');
    });

    // Cuando se colapsa (oculta) el collapse
    collapseEl.addEventListener('hide.bs.collapse', () => {
      // Enlace que lo controla
      const toggler = document.querySelector(`[href="#${collapseEl.id}"]`);
      if (!toggler) return;
      
      // Rotar flecha a 0
      const icon = toggler.querySelector('.rotate-on-collapse');
      if (icon) {
        icon.style.transform = 'rotate(0deg)';
      }
      // Quitar la clase .active
      toggler.classList.remove('active');
    });
  });
</script>
