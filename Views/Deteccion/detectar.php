<?php
if(!isset($_SESSION)) { 
    session_start(); 
}

function predictMelanoma($imagePath) {
    $url = 'http://127.0.0.1:5000/predict';  // Ajusta la ruta a tu servidor Flask

    $postFields = [
        'file' => new CURLFile($imagePath)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Variables para mostrar resultado y probabilidad, si existen
$predictionText = "";
$probabilityText = "";
$errorMessage = "";  // Variable para mostrar el error

// Comprobamos si se ha hecho una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Identificamos qué botón se ha presionado
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Si se presionó "Detectar" (no es obligatorio el campo paciente)
    if ($action === 'detect' && isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
        $imagePath = $_FILES['image']['tmp_name']; 
        $result = predictMelanoma($imagePath);

        if (isset($result['prediction'])) {
            $predictionText = $result['prediction'];
            $probabilityText = $result['probability'] ?? '';
        } else {
            $predictionText = "Error: " . ($result['error'] ?? 'Desconocido');
            $probabilityText = "";
        }
    }else{
            $errorMessage = "Por favor, seleccione una imagen antes de detectar.";
        
    }

    // Si se presionó "Guardar Detección" (se valida el campo paciente)
    if ($action === 'save') {
        // Validamos que el campo "Paciente" esté lleno
        if (empty($_POST['paciente']) && !empty($imagePath)) {
            $errorMessage = "Por favor, seleccione un paciente antes de guardar la detección.";
        } else {
            // Aquí va la lógica para guardar la detección si es necesario
            // Esto puede ser guardar los datos en una base de datos, por ejemplo
            // En este caso, solo mostramos un mensaje de éxito
            $errorMessage = "¡Detección guardada exitosamente!";
        }
    }
}

 else {
    // ... Lógica de predicción

// Procesa el formulario (imagen subida)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $imagePath = $_FILES['image']['tmp_name']; 
    $result = predictMelanoma($imagePath);

    if (isset($result['prediction'])) {
        $predictionText   = $result['prediction'];
        $probabilityText  = $result['probability'] ?? '';
    } else {
        $predictionText   = "Error: " . ($result['error'] ?? 'Desconocido');
        $probabilityText  = "";
    }
}
}
?>

<!-- Encabezado principal -->
<div class="container my-4" >
  <div class="card shadow">
    <!-- Encabezado del card -->
    <div class="card-header text-center text-white" style="background-color:#28a688;">
      <h5 class="mb-0">Datos previos del informe médico</h5>
    </div>

    <!-- Cuerpo del card -->
    <div class="card-body">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
          
          <!-- Columna izquierda: Imagen o placeholder -->
          <div class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0">
            <?php if (!empty($imagePath)): ?>
              <!-- Si se subió imagen, la mostramos -->
              <img src="data:image/jpeg;base64,<?= base64_encode(file_get_contents($imagePath)) ?>"
                   alt="Imagen cargada"
                   class="img-fluid border rounded"
                   style="max-height: 200px;">
            <?php else: ?>
              <!-- Placeholder si no se ha subido imagen -->
              <div class="border rounded d-flex align-items-center justify-content-center"
                   style="width: 200px; height: 200px;">
                <span class="text-muted">Sin imagen</span>
              </div>
            <?php endif; ?>
          </div>
          
          <!-- Columna derecha: Paciente y Resultado -->
          <div class="col-md-8">
            <div class="row g-3">
              
              <!-- Seleccionar paciente -->
              <div class="col-sm-6">
                <label class="form-label">Paciente</label>
                <select class="form-select" name="paciente" >
                  <!-- Ajusta según tu lógica de pacientes -->
                  <option value="">Seleccione</option>
                  <option value="Paciente1">Paciente1</option>
                  <option value="Paciente2">Paciente2</option>
                  <option value="Paciente3">Paciente3</option>
                </select>
              </div>
              
              <!-- Resultado de la predicción -->
              <div class="col-sm-4">
                <label class="form-label">Resultado</label>
                <input type="text" class="form-control"
                       name="resultado"
                       placeholder="Melanoma / No Melanoma"
                       value="<?= htmlspecialchars($predictionText) ?>"
                       readonly>
              </div>
              <!-- Probabilidad -->
                <div class="col-sm-2">
                    <label class="form-label">Probabilidad</label>
                    <input type="text" class="form-control" name="probabilidad"
                        placeholder="Ej: 85%"
                        value="<?= htmlspecialchars($probabilityText) ?> %"
                        readonly>
                </div>
              <!-- Sección para subir imagen -->
                <div class="mt-4 row g-3">
                    <div class="col-md-8">
                        <label for="imageUpload" class="form-label">Subir imagen de la lesión</label>
                        <input class="form-control" type="file" id="imageUpload" name="image" accept="image/*"
                            accept="image/*">
                    </div>
                    
                    <!-- Botón Buscar (ejecutar predicción) -->
                    <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100" type="submit" name="action" value="detect">
                        Detectar
                    </button>
                    </div>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
          </div>
        </div><!-- Fin row superior -->
        
        <!-- Observaciones del Dermatólogo -->
        <div class="mt-4">
          <label class="form-label">Observaciones del Dermatólogo</label>
          <textarea class="form-control" rows="2" placeholder="Ingrese Texto..."></textarea>
        </div>
        
        

      </form>
    </div><!-- Fin card-body -->

    <!-- Footer del card con botón grande -->
    <div class="card-footer text-center">
        <button type="submit" class="btn text-white" style="background-color:#28a688;" form="detectionForm" name="action" value="save">
        GUARDAR DETECCIÓN
      </button>
    </div>
  </div><!-- Fin card -->

</div>
