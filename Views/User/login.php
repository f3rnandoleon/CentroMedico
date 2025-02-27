<?php if (isset($_SESSION['mensaje'])): ?>
  <div 
    class="alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
    style="z-index: 9999; max-width: 400px;"
  >
    <strong><?php echo $_SESSION['mensaje']; ?></strong>
  </div>
<?php endif; unset($_SESSION['mensaje']); ?>

<!-- Contenedor principal de altura completa -->
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-0" style="background-color: #28a688;">
  
  <!-- Card con dos columnas -->
  <div class="card shadow border-0" style="width: 90%; max-width: 1000px; border-radius: 20px;">
    <div class="row g-0">
      
      <!-- Columna Izquierda: Imagen a pantalla completa -->
      <div class="col-md-6 d-none d-md-block p-0" 
           style="
             background: url('assets/images/imagenPlasma.png') center/cover no-repeat;
             border-radius: 20px 0 0 20px;
             min-height: 100%;
           ">
        <!-- Si no deseas nada de texto aquí, déjalo vacío -->
      </div>
      
      <!-- Columna Derecha: Formulario de login -->
      <div class="col-md-6 d-flex align-items-center justify-content-center" 
           style="background-color: #fefefe; border-radius: 0 20px 20px 0;">
        
        <!-- Contenedor interno con padding -->
        <div class="p-4" style="width: 100%; max-width: 400px;">
          <h3 class="text-center text-success mb-3">Bienvenido</h3>
          <p>Ingrese sus Credenciales</p>
          
          <!-- Formulario -->
          <form action="?controller=usuario&action=login" method="post">
            
            <!-- Campo Correo -->
            <div class="mb-4">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email"
                     placeholder="Ingrese su email" required autocomplete="off">
            </div>
            
            <!-- Campo Contraseña -->
            <div class="mb-4">
              <label for="pwd" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="pwd" name="pwd"
                     placeholder="Ingrese su contraseña" required>
            </div>
            
            <!-- Sección Recordarme / Olvidaste contraseña -->
            <div class="row mb-4">
              <div class="col-6 d-flex align-items-center">
                <div class="form-check m-0">
                  <input class="form-check-input" type="checkbox" id="rememberMe">
                  <label class="form-check-label" for="rememberMe">
                    Recordarme
                  </label>
                </div>
              </div>
              <div class="col-6 text-end">
                <a href="#" class="text-success">¿Olvidaste tu contraseña?</a>
              </div>
            </div>
            
            <!-- Botón Ingresar -->
            <button type="submit" class="btn btn-success w-100">
              Ingresar
            </button>
            
            <!-- Enlace de registro -->
            <p class="text-center mt-4 mb-0">
              ¿No tienes una cuenta?
              <a href="?controller=usuario&action=register" class="text-success">Regístrate</a>
            </p>
          </form>
        </div><!-- Fin contenedor interno -->
        
      </div><!-- Fin col derecha -->
    </div><!-- Fin row -->
  </div><!-- Fin card -->
</div><!-- Fin container-fluid -->
