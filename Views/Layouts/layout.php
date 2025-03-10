<?php if(!isset($_SESSION)) 
    { ob_start(); // Inicia el almacenamiento en búfer de la salida
        session_start();        
    } ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>APP MEDICOS</title>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <!-- Bootstrap Icons (opcional) -->
  <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="m-0 p-0">
  
  <!-- Contenedor principal: d-flex para dividir en dos columnas -->
  <div class="d-flex" style="height: 100vh;">
    <?php if (isset($_SESSION['mensaje'])) { //mensaje, cuando realiza alguna acción crud ?>
			<div class="alert alert-success">
				<strong><?php echo $_SESSION['mensaje']; ?></strong>
			</div>
		<?php } 
			unset($_SESSION['mensaje']);
		?>
    <?php if (isset($_SESSION['usuario'])) { ?>
    <!-- Columna izquierda: sidebar (visible solo en pantallas grandes) -->
    <div class="d-none d-lg-flex flex-column bg-success text-white"
         style="width: 250px;">
         
      <?php require('navegador.php'); ?>
    </div>

    <!-- Columna derecha: cabecera + contenido -->
    <div class="d-flex flex-column flex-grow-1">
      
      <!-- Cabecera -->
      <div>
        <?php require('cabecera.php'); ?>
      </div>
      
      <!-- Contenido principal -->
      <div class="flex-grow-1" style="background-color: #f5f5f5;">
        <?php require_once('routing.php'); ?>
      </div>

    </div>
    <?php } else { ?>
        <!-- Contenido principal -->
         
      <div class="flex-grow-1" style="background-color: #f5f5f5;">
        
        <?php require_once('routing.php'); ?>
      </div>
      <?php } ?>
  </div>

  <!-- Scripts de Bootstrap (Popper incluido en bootstrap.bundle) -->
  <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
