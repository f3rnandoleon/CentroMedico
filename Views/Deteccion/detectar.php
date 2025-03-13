

<?php 
if(!isset($_SESSION)) { session_start(); }

function predictMelanoma($imagePath) {
    $url = 'http://127.0.0.1:5000/predict';  // Ruta del servidor Flask

    $postFields = ['file' => new CURLFile($imagePath)];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Variables para la predicción y mensajes de error
$predictionText = "";
$probabilityText = "";
$errorMessage = "";
$imagePath = "";

// Directorio donde se guardarán las imágenes
$uploadDir = "uploads/";

// Manejo de la detección
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
    $imageTmpPath = $_FILES['image']['tmp_name'];
    $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
    $imagePath = $uploadDir . $imageName;

    // Guardar la imagen en el directorio 'uploads'
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($imageTmpPath, $imagePath)) {
        // Ahora que la imagen está guardada, hacer la predicción
        if ($_POST['action'] === 'detect') {
            $result = predictMelanoma($imagePath);
            if (isset($result['prediction'])) {
                $predictionText = $result['prediction'];
                $probabilityText = $result['probability'] ?? '';
            } else {
                $errorMessage = "Error: " . ($result['error'] ?? 'Desconocido');
            }
        }
    } else {
        $errorMessage = "Error al guardar la imagen.";
        $imagePath = ""; // Asegurar que no se guarde una ruta inválida
    }
} else {
    $errorMessage = "Por favor, seleccione una imagen antes de detectar.";
}
?>
<?php
    $bgColor = ($predictionText === "Melanoma") ? "var(--bs-red)" : "var(--bs-teal)";
    $textColor = "var(--bs-white)"; // Texto blanco para contraste
?>
<?php if (isset($_SESSION['mensaje'])): ?>
  <div 
    class="alert alert-success position-fixed top-20 start-50 translate-middle-x mt-3 shadow"
    style="z-index: 9999; max-width: 400px;"
  >
    <strong><?php echo $_SESSION['mensaje']; ?></strong>
  </div>
<?php endif; unset($_SESSION['mensaje']); ?>
<div class="container mt-3 px-4" style="max-height: 84vh; overflow-y:auto;">
    <div class="card shadow">
        <div class="card-header text-center text-white" style="background-color:#28a688;">
            <h5 class="mb-0">Datos previos del informe médico</h5>
        </div>
        <div class="card-body">
            <!-- FORMULARIO PARA DETECTAR -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Imagen de la lesión -->
                    <div class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0">
                        <?php if (!empty($imagePath)): ?>
                            <img src="<?= $imagePath ?>" 
                                 alt="Imagen cargada" class="img-fluid border rounded" style="max-height: 200px;">
                        <?php else: ?>
                            <div class="border rounded d-flex align-items-center justify-content-center"
                                 style="width: 200px; height: 200px;">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Información del paciente y resultado -->
                    <div class="col-md-8">
                        <div class="row g-3">
                            <!-- Subir imagen -->
                            <div class="mt-4 row g-3">
                                <div class="col-md-8">
                                    <label for="imageUpload" class="form-label">Subir imagen de la lesión</label>
                                    <input class="form-control" type="file" id="imageUpload" name="image" accept="image/*">
                                </div>

                                <!-- Botón "Detectar" -->
                                <div class="col-md-4 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" type="submit" name="action" value="detect">
                                        Detectar
                                    </button>
                                </div>

                                <!-- Mensajes de error -->
                                <?php if (!empty($errorMessage)): ?>
                                    <div class="alert alert-danger mt-3">
                                        <?= htmlspecialchars($errorMessage); ?>
                                    </div>
                                <?php endif; $errorMessage=""; ?>
                            </div>
                            <!-- Resultado de la predicción -->
                            <div class="col-sm-9">
                                <label class="form-label">Resultado</label>
                                <input type="text" class="form-control"
                                       name="resultado"
                                       value="<?= htmlspecialchars($predictionText) ?>"
                                       readonly
                                       style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                            </div>

                            <!-- Probabilidad -->
                            <div class="col-sm-3">
                                <label class="form-label">Probabilidad</label>
                                <input type="text" class="form-control"
                                       name="probabilidad"
                                       value="<?= htmlspecialchars($probabilityText) ?> %"
                                       readonly
                                       style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                            </div>

                            
                        </div>
                    </div>
                </div>
            </form>

            <!-- FORMULARIO PARA GUARDAR -->
            <form action="?controller=deteccion&action=save" method="POST">
                <input type="hidden" name="fecha" value="<?= date('Y-m-d'); ?>">
                <input type="hidden" name="resultado" value="<?= htmlspecialchars($predictionText) ?>">
                <input type="hidden" name="probabilidad" value="<?= htmlspecialchars($probabilityText) ?>">
                <input type="hidden" name="image" value="<?= $imagePath ?>">

                <!-- Seleccionar paciente -->
                <div class="mt-3">
                    <label class="form-label">Paciente</label>
                    <select name="paciente" class="form-select" required>
                        <option value="">Seleccione un paciente</option>
                        <?php $pacientes = Paciente::all();
                        foreach ($pacientes as $paciente) { ?>
                            <option value="<?= $paciente->getId(); ?>">
                                <?= $paciente->getNombres() . " " . $paciente->getApellidos(); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Observaciones -->
                <div class="mt-3">
                    <label class="form-label">Observaciones del Dermatólogo</label>
                    <textarea name="observaciones" class="form-control" rows="2" required></textarea>
                </div>

                <!-- Recomendaciones -->
                <div class="mt-2">
                    <label class="form-label">Recomendaciones</label>
                    <textarea name="recomendacion" class="form-control" rows="1" required></textarea>
                </div>

                <!-- Botón "Guardar Detección" -->
                <div class="card-footer text-center">
                    <button type="submit" class="btn text-white" style="background-color:#28a688;">
                        GUARDAR DETECCIÓN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Si deseas usar la funcionalidad de datepicker antigua, mantén estos enlaces,
     aunque para un input[type="date"] no es estrictamente necesario. -->

     <link rel="stylesheet" 
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" 
      href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script 
  src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js">
</script>

<script>
  // Solo si deseas usar datepicker adicionalmente
  $(document).ready(function() {
    // Ejemplo de inicialización
    $('#datePicker1, #datePicker2').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
  });
</script>