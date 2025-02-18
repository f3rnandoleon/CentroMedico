<?php if (isset($_SESSION['mensaje'])): ?>
  <div 
    class="alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
    style="z-index: 9999; max-width: 400px;"
  >
    <strong><?php echo $_SESSION['mensaje']; ?></strong>
  </div>
<?php endif; unset($_SESSION['mensaje']); ?>

<!-- Contenedor a pantalla completa con fondo verde y altura completa -->
<div class="container-fluid bg-success min-vh-100 d-flex p-0 m-0">
  <div class="row flex-nowrap w-100 g-0">
    
    <!-- Columna Izquierda -->
    <div class="col-md-6 col-sm-12 position-relative p-0">
      <!-- Logo posicionado absolutamente en la esquina sup-izq con algo de margen -->
      <img src="assets\images\logo-piel.png" alt="Logo"
           class="position-absolute top-5 start-5 m-3"
           style="display:absolute; max-width: 150px; left: 250px; top: 20px;">
      
      <!-- Contenido centrado vertical y horizontalmente, con texto en blanco -->
      <div class="d-flex flex-column justify-content-center align-items-center text-white h-100 p-4">
        <h2 class="text-center mb-3">
          Centro Médico de la Piel <br>
          “Dr. Johnny de la Riva Guzmán”
        </h2>
        <p class="text-start" style="max-width: 450px;">
          Una clínica dermatológica de confianza en La Paz, Bolivia, que ofrece cuidado
          integral para la salud y belleza de la piel. Cuenta con un equipo certificado
          de dermatólogos y esteticistas que brindan soluciones personalizadas para
          diversas necesidades cutáneas.
        </p>
      </div>
    </div>
    
    <!-- Columna Derecha (Formulario de Login) -->
    <div class="col-md-6 col-sm-12 d-flex align-items-center justify-content-center p-4">
      
      <!-- Card más ancha (max-width: 600px) y con padding adicional en .card-body -->
      <div class="card w-100" style="max-width: 500px;">
        <div class="card-body p-5"><!-- p-5 da más espacio interno -->
          <h3 class="text-center text-success mb-4">Bienvenido</h3>
          <p>Ingrese sus Credenciales </p>
          <form action="?controller=usuario&action=login" method="post">
            <div class="mb-4">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email"
                     placeholder="Ingrese su email" required autocomplete="off">
            </div>
            
            <div class="mb-4">
              <label for="pwd" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="pwd" name="pwd"
                     placeholder="Ingrese su contraseña" required>
            </div>
            
            <!-- Sección Recordarme y Olvidaste contraseña -->
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
              <a href="?controller=usuario&action=register" class="text-success">Regístrarse</a>
            </p>
          </form>
        </div>
      </div><!-- Fin card -->
      
    </div><!-- Fin col derecha -->
    
  </div><!-- Fin row -->
</div><!-- Fin container-fluid -->
