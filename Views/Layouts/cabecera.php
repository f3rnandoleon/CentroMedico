

<!-- Navbar clara con fondo blanco -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container-fluid">
    
    <!-- Marca / Título -->
    <a class="navbar-brand d-flex align-items-center text-success fw-bold" href="http://localhost/CentroMedico/index.php">
    <img src="assets\images\logo-piellog.png" alt="Logo"
           style="width: 8vh; heigth: 6vh; margin-right: 2vh;">
      Centro Medico de la Piel
    </a>

    <!-- Botón hamburguesa si tuvieras más cosas que colapsar, 
         pero en este caso, no es obligatorio. -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarSmall" aria-controls="navbarSmall" 
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Sección de la derecha -->
    <div class="collapse navbar-collapse" id="navbarSmall">
      <ul class="navbar-nav ms-auto px-2">
        
        <!-- Opciones del "navegador lateral" 
             visibles SOLO en pantallas pequeñas (d-lg-none) -->
        <li class="nav-item d-lg-none">
          <a class="nav-link text-dark" href="?controller=paciente&action=register">
            <i class="bi bi-people"></i> Registrar Paciente
          </a>
        </li>
        <li class="nav-item d-lg-none">
          <a class="nav-link text-dark" href="?controller=paciente&action=show">
            <i class="bi bi-people"></i> Ver Pacientes
          </a>
        </li>
        <li class="nav-item d-lg-none">
          <a class="nav-link text-dark" href="?controller=historia&action=show">
            <i class="bi bi-file-medical"></i> Nueva Consulta
          </a>
        </li>
        <li class="nav-item d-lg-none">
          <a class="nav-link text-dark" href="?controller=consulta&action=show">
            <i class="bi bi-journal-check"></i> Ver Consultas
          </a>
        </li>
        <li class="nav-item d-lg-none">
          <a class="nav-link text-dark" href="?controller=deteccion&action=detectar">
            <i class="bi bi-search"></i> Detectar
          </a>
        </li>

        <!-- Sección de usuario (igual para todos los tamaños) -->
        <?php if (isset($_SESSION['usuario'])) { ?>
          <li class="nav-item d-flex align-items-center">
            <span class="navbar-text me-3">
              Bienvenido: <?php echo $_SESSION['usuario_nombre']; ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="?controller=usuario&action=showregister&id=<?php echo $_SESSION['usuario_id'] ?>">
              <i class="bi bi-gear"></i> Mi cuenta
            </a>
          </li>
          <li class="nav-item" >
            <a class="nav-link" style="color: red;" href="?controller=usuario&action=logout">
              <i class="bi bi-box-arrow-right" ></i> Salir
            </a>
          </li>
        <?php }  ?>
          

      </ul>
    </div>
  </div>
</nav>
