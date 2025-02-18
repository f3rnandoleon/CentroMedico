
<div class="container-fluid bg-success min-vh-100 d-flex" >
  <!-- La clase "d-flex align-items-center" y "height:100vh" ayudan a centrar verticalmente el contenido -->
  
  <div class="row w-100 align-items-center ">
    <!-- Columna Izquierda -->
    <div class="col-md-6 col-sm-12 position-relative p-0">
      <!-- Logo posicionado absolutamente en la esquina sup-izq con algo de margen -->
      <img src="assets\images\logo-piel.png" alt="Logo"
           class="position-absolute top-5 start-5 m-3"
           style="display:absolute; max-width: 200px; left: 200px; top: -150px;">
      
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

    <!-- COLUMNA DERECHA: Formulario de Registro -->
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body px-5 py-4">
          <!-- Título -->
          <h4 class="text-center mb-3 text-success mt-3">Registrarse</h4>
          
          <!-- (Ejemplo de botón Google comentado)
          <button class="btn btn-light w-100 mb-3" style="border: 1px solid #ccc;">
            <strong style="color: #4285f4;">G</strong> Regístrate en Google
          </button>
          <p class="text-center text-muted mb-2">O utilice su dirección de correo</p>
          <hr>
          -->

          <!-- Formulario -->
          <form action="?controller=usuario&action=save" method="post" onsubmit="return validarPasswords();">
            
              <!-- Nombre y Apellido en la misma fila -->
              <div class="row">
                <div class="col-6 mb-3">
                  <label for="nombres" class="form-label">Nombre</label>
                  <input type="text" class="form-control" id="nombres" name="nombres"
                         required placeholder="Nombre" autocomplete="off">
                </div>
                <div class="col-6 mb-3">
                  <label for="apellidos" class="form-label">Apellido</label>
                  <input type="text" class="form-control" id="apellidos" name="apellidos"
                         required placeholder="Apellido" autocomplete="off">
                </div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label for="email" class="form-label">Dirección de correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email"
                       required placeholder="correo@ejemplo.com" autocomplete="off">
              </div>

              <!-- Contraseña -->
              <div class="mb-3">
                <label for="pwd" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="pwd" name="pwd"
                       required placeholder="Contraseña"
                       oninput="verificarCoincidencia();">
              </div>

              <!-- Confirmar Contraseña -->
              <div class="mb-3">
                <label for="pwd2" class="form-label">Confirmar contraseña</label>
                <input type="password" class="form-control" id="pwd2" name="pwd2"
                       required placeholder="Repita la contraseña"
                       oninput="verificarCoincidencia();">
              </div>

              <!-- Mensaje de error -->
              <div id="mensajeError" style="color: red;"></div>
			  			
              <!-- Botón de envío -->
              <button type="submit" class="btn btn-success w-100 my-3">
                Regístrese ahora
              </button>
			  <p class="">
                    ¿Ya tiene una cuenta?
                <a href="?controller=usuario&action=showLogin" class="text-success">Iniciar Sesion</a>
            </p>
          </form>
        </div>
      </div>
    </div>
    
  </div>
</div>

<!-- Scripts para validar contraseñas (sin librerías externas) -->
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
      return false; // Evita el envío del formulario
    }
    return true; // Permite el envío
  }
</script>
