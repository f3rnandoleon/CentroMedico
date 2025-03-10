<?php if (isset($_SESSION['mensaje'])): ?>
  <div 
    class="alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
    style="z-index: 9999; max-width: 400px;"
  >
    <strong><?php echo $_SESSION['mensaje']; ?></strong>
  </div>
<?php endif; unset($_SESSION['mensaje']); ?>

<!-- Contenedor principal de altura completa, color verde de fondo -->
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-0" style="background-color: #28a688;">
  
  <!-- Card con dos columnas -->
  <div class="card shadow border-0" style="width: 90%; max-width: 1000px; border-radius: 20px;">
    <div class="row g-0">
      
      <!-- Columna Derecha: Formulario de registro -->
      <div class="col-md-6 d-flex align-items-center justify-content-center" 
           style="background-color: #fefefe; border-radius: 20px 0 0 20px ;">
        
        <!-- Contenedor interno con padding -->
        <div class="p-4" style="width: 100%; max-width: 400px;">
          
          <!-- Título principal -->
          <h3 class="text-center text-success mb-3">Regístrarse</h3>
          <p>Complete sus datos para crear la cuenta</p>
          
          <!-- Formulario -->
          <form action="?controller=usuario&action=save" method="post" onsubmit="return validarPasswords();">
            
            <!-- Nombre y Apellido en la misma fila -->
            <div class="row">
              <div class="col-6 mb-3">
                <label for="nombres" class="form-label">Nombre</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="nombres" 
                  name="nombres"
                  required 
                  placeholder="Nombre" 
                  autocomplete="off"
                >
              </div>
              <div class="col-6 mb-3">
                <label for="apellidos" class="form-label">Apellido</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="apellidos" 
                  name="apellidos"
                  required 
                  placeholder="Apellido" 
                  autocomplete="off"
                >
              </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email"
                required 
                placeholder="correo@ejemplo.com" 
                autocomplete="off"
              >
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
              <label for="pwd" class="form-label">Contraseña</label>
              <input 
                type="password" 
                class="form-control" 
                id="pwd" 
                name="pwd"
                required 
                placeholder="Contraseña"
                oninput="verificarCoincidencia();"
              >
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mb-3">
              <label for="pwd2" class="form-label">Confirmar contraseña</label>
              <input 
                type="password" 
                class="form-control" 
                id="pwd2" 
                name="pwd2"
                required 
                placeholder="Repita la contraseña"
                oninput="verificarCoincidencia();"
              >
            </div>

            <!-- Mensaje de error -->
            <div id="mensajeError" class="text-danger"></div>
            
            <!-- Botón de registro -->
            <button type="submit" class="btn btn-success w-100 my-3">
              Regístrese
            </button>
            
            <!-- Enlace "Ya tiene cuenta?" -->
            <p class="text-center mb-0">
              ¿Ya tiene una cuenta?
              <a href="?controller=usuario&action=showLogin" class="text-success">Iniciar Sesión</a>
            </p>
          </form>
        </div><!-- Fin contenedor interno -->
        
      </div><!-- Fin col derecha -->
      <!-- Columna Izquierda: Imagen a pantalla completa -->
      <div class="col-md-6 d-none d-md-block p-0" 
           style="
             background: url('assets/images/imagenPlasma.png') center/cover no-repeat;
             border-radius: 0 20px 20px 0;
             min-height: 100%;
           ">
        <!-- Deja vacío si no deseas texto aquí -->
      </div>
    </div><!-- Fin row -->
  </div><!-- Fin card -->
</div><!-- Fin container-fluid -->

<!-- Scripts para validar contraseñas -->
<script>
  function verificarCoincidencia() {
    const pwd  = document.getElementById('pwd').value;
    const pwd2 = document.getElementById('pwd2').value;
    const mensajeError = document.getElementById('mensajeError');

    if (pwd && pwd2 && pwd !== pwd2) {
      mensajeError.textContent = 'Las contraseñas no coinciden';
    } else {
      mensajeError.textContent = '';
    }
  }

  function validarPasswords() {
    const pwd  = document.getElementById('pwd').value;
    const pwd2 = document.getElementById('pwd2').value;

    if (pwd !== pwd2) {
      alert('Las contraseñas no coinciden');
      return false; 
    }
    return true; 
  }
</script>
