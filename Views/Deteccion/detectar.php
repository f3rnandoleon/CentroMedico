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

$predictionText = "";
$diagnostico = "";
$probabilityText = "";
$errorMessage = "";
$imagePath = "";
$imagePublicUrl = "";
$similarImages = [];
$similarLabels = [];
$diagnostic = ["La Imagen es MELANOMA con un porcentaje de ", "La Imagen es NO MELANOMA con un porcentaje de "];
$uploadDir = "uploads/";
// Variables para almacenar los porcentajes de las características
$colorScore = "N/A";
$bordeScore = "N/A";
$asimetriaScore = "N/A";
$texturaScore = "N/A";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mantener el valor del paciente seleccionado tras cada submit
    $paciente_id = $_POST['paciente'] ?? '';
    $paciente_name = $_POST['paciente_name'] ?? '';

    if (!empty($_POST['selected_image'])) {
        $publicUrl = $_POST['selected_image'];
        if (strpos($publicUrl, 'http') === 0) {
            $parsed = parse_url($publicUrl);
            $publicUrl = $parsed['path'];
        }
        $baseDir = realpath(__DIR__ . '/../../public');
        $relative = str_replace('/CentroMedico/public', '', $publicUrl);
        $candidate = $baseDir . $relative;
        if (file_exists($candidate)) {
            $imagePath = $candidate;
            $imagePublicUrl = $publicUrl;
        } else {
            $errorMessage = "No pude localizar la imagen seleccionada.";
            $imagePath = "";
            $imagePublicUrl = "";
        }
    }
    // Si sube imagen nueva
    elseif (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName    = uniqid() . "_" . basename($_FILES['image']['name']);
        $uploadDir    = __DIR__ . '/../../public/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destPath = $uploadDir . $imageName;
        if (move_uploaded_file($imageTmpPath, $destPath)) {
            $imagePath = $destPath;
            $imagePublicUrl = '/CentroMedico/public/uploads/' . $imageName;
        } else {
            $errorMessage = "Error al guardar la imagen subida.";
            $imagePath = "";
            $imagePublicUrl = "";
        }
    } else if (!empty($_POST['image_path']) && !empty($_POST['image_public_url'])) {
        // Mantener valores si ya existen (post submit intermedio)
        $imagePath = $_POST['image_path'];
        $imagePublicUrl = $_POST['image_public_url'];
    }

    // Si ya tenemos ruta válida y el usuario pidió detectar
    if (!empty($imagePath) && isset($_POST['action']) && $_POST['action'] === 'detect') {
        $result = predictMelanoma($imagePath);
        if (isset($result['prediction'])) {
            $predictionText = $result['prediction'];
            $diagnostico = ($predictionText === "Melanoma") ? $diagnostic[0] : $diagnostic[1];
            $probabilityText = $result['probability'] ?? '';
            $similarImages = $result['similar_images'] ?? [];
            $similarLabels = $result['similar_labels'] ?? [];
            $bestMatches = $result['best_matches'] ?? [];
            $colorScore = isset($bestMatches['color']) ? round($bestMatches['color']['score'], 2) . "%" : "N/A";
            $bordeScore = isset($bestMatches['borde']) ? round($bestMatches['borde']['score'], 2) . "%" : "N/A";
            $asimetriaScore = isset($bestMatches['asimetria']) ? round($bestMatches['asimetria']['score'], 2) . "%" : "N/A";
            $texturaScore = isset($bestMatches['textura']) ? round($bestMatches['textura']['score'], 2) . "%" : "N/A";
        } else {
            $errorMessage = "Error: " . ($result['error'] ?? 'Desconocido');
        }
    }

  
}
$bgColor = ($predictionText === "Melanoma") ? "var(--bs-red)" : "var(--bs-teal)";
$textColor = ($predictionText === "") ? "var(--bs-gray-dark)" : "var(--bs-white)";
?>
<?php if (isset($_SESSION['mensaje'])): ?>
  <div class="alert alert-success position-fixed top-20 start-50 translate-middle-x mt-3 shadow" style="z-index: 9999; max-width: 400px;">
    <strong><?php echo $_SESSION['mensaje']; ?></strong>
  </div>
<?php endif; unset($_SESSION['mensaje']); ?>

<!-- Incluir Cropper.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-..." crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" integrity="sha512-..." crossorigin="anonymous"></script>

<!-- Estilos personalizados -->
<style>
  /* Contenedor principal */
  .custom-card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15);
    background-color: #fff;
  }
  .custom-card-header {
    background-color: #28a688;
    color: #fff;
    padding: 1rem;
    text-align: center;
    border-top-left-radius: 0.35rem;
    border-top-right-radius: 0.35rem;
  }
  .custom-card-body {
    padding: 1rem 1.5rem;
  }
  .custom-search-form .form-control { border-radius: 0.35rem; }
  .custom-search-form .btn { border-radius: 20px; }
  .custom-table thead {
    background-color: #d4edda;
    color: #155724;
  }
  .custom-table th, .custom-table td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    vertical-align: middle;
  }
  .custom-table tbody tr:hover { background-color: #f1f3f5; }
  .custom-btn { border-radius: 20px; margin-right: 0.2rem; }
  .pagination { justify-content: center; }
  .pagination .page-item.active .page-link {
    background-color: #28a688;
    border-color: #28a688;
  }
  .pagination .page-link { border-radius: 0.35rem; }
  /* Estilos para el modal de edición de imagen */
  #editImageModal .modal-dialog { max-width: 800px; }
  #editImageModal .modal-content {
    border: 2px solid #28a688;
    border-radius: 0;
  }
  #editImageModal .modal-header {
    background-color: #28a688;
    color: #fff;
    padding: 1rem 1.5rem;
  }
  #editImageModal .modal-body {
    padding: 1rem;
    text-align: center;
  }
  #editImageModal .modal-footer {
    padding: 0.75rem 1.5rem;
    border-top: 2px solid #28a688;
  }
  /* Estilos para el modal de detalles (informe) */
  #detailsModal .modal-dialog { max-width: 900px; }
  #detailsModal .modal-content {
    border: 2px solid #000;
    border-radius: 0;
    font-family: Arial, sans-serif;
  }
  #detailsModal .modal-header {
    background-color: #fff;
    border-bottom: 2px solid #000;
    padding: 1rem 1.5rem;
  }
  #detailsModal .modal-title { font-weight: 700; text-transform: uppercase; }
  #detailsModal .modal-body {
    background-color: #f8f9fa;
    padding: 1.5rem;
    font-size: 0.95rem;
  }
  #detailsModal .modal-footer {
    background-color: #f8f9fa;
    padding: 0.75rem 1.5rem;
    border-top: 2px solid #000;
  }
  #detailsModal table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
  }
  #detailsModal th, #detailsModal td {
    border: 1px solid #000;
    padding: 0.5rem;
    text-align: left;
  }
  #detailsModal th {
    background-color: #d4edda;
    font-weight: 700;
  }
  .section-title {
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    display: block;
  }
</style>

<div class="container mt-3 px-4" style="max-height: 84vh; overflow-y:auto;">
  <div class="card shadow custom-card">
    <div class="card-header text-center custom-card-header">
      <h5 class="mb-0">Datos previos del informe médico</h5>
    </div>
    <div class="card-body custom-card-body">
      <form action="" method="POST" enctype="multipart/form-data" class="custom-search-form">
        <!-- ---------- DATOS DEL PACIENTE (AUTOCOMPLETAR) --------- -->
        <div class="row mt-3">
          <div class="col-md-10">
            <label class="form-label">Paciente</label>
            <?php $pacientes = Paciente::all(); ?>
            <input 
              class="form-control" 
              id="patientInput" 
              name="paciente_name" 
              list="patientsList" 
              placeholder="Escribe el nombre del paciente…" 
              autocomplete="off"
              required
              value="<?= htmlspecialchars($paciente_name ?? '') ?>"
            >
            <datalist id="patientsList">
              <?php foreach ($pacientes as $paciente): 
                  $fullName = htmlspecialchars($paciente->getNombres() . ' ' . $paciente->getApellidos(), ENT_QUOTES);
              ?>
                <option value="<?= $fullName ?>"></option>
              <?php endforeach; ?>
            </datalist>
            <input type="hidden" name="paciente" id="patientIdInput" value="<?= htmlspecialchars($paciente_id ?? '') ?>">
          </div>
          <div class="col-md-2 text-center">
            <label class="form-label d-block">&nbsp;</label>
            <button 
              type="button" 
              class="btn btn-info w-100"
              id="showDetailsBtn">
              Visualizar seguimiento
            </button>
          </div>
        </div>

        <!-- ---------- IMAGEN Y DETECCIÓN ---------- -->
        <div class="row">
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-3" id="mainImageContainer">
            <?php if (!empty($imagePublicUrl)): ?>
              <img id="mainImagePreview" src="<?= $imagePublicUrl ?>" alt="Imagen cargada" class="img-fluid border" style="height:350px; width:400px">
            <?php else: ?>
              <div id="noImagePlaceholder" class="border rounded d-flex align-items-center justify-content-center" style="height:300px; width:350px">
                <span class="text-muted">Sin imagen</span>
              </div>
            <?php endif; ?>
          </div>
          <div class="col-md-7">
            <div class="row g-3">
              <div class="mt-2 row g-3">
                <div class="d-flex align-items-center gap-2">
                  <button 
                    type="button" 
                    class="btn btn-secondary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#selectImageModal">
                    Seleccionar imagen existente
                  </button>
                  <input type="hidden" name="selected_image" id="selectedImagePath" value="">
                </div>
                <div class="d-flex align-items-end">
                  <button class="btn btn-primary w-100" type="submit" name="action" value="detect">
                    Cargar y Detectar Lesión
                  </button>
                </div>
                <?php if (!empty($errorMessage)): ?>
                  <div class="alert alert-danger mt-3">
                    <?= htmlspecialchars($errorMessage); ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="row mt-1">
                <div class="mb-2">
                  <label class="form-label">Resultado</label>
                  <input type="text" class="form-control" name="resultado"
                         value="<?= $diagnostico . htmlspecialchars($probabilityText) . "%" ?>"
                         readonly style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                </div>
                <!-- Puedes incluir aquí las características (color, borde, etc.) si quieres -->
              </div>
            </div>
          </div>
        </div>

        <!-- ---------- GUARDADO ---------- -->
        <input type="hidden" name="fecha" value="<?= date('Y-m-d'); ?>">
        <input type="hidden" name="probabilidad" value="<?= htmlspecialchars($probabilityText) ?>">
        <input type="hidden" name="image_path" value="<?= htmlspecialchars($imagePath) ?>">
        <input type="hidden" name="image" value="<?= htmlspecialchars($imagePublicUrl) ?>">
        <input type="hidden" name="usuario_id" value="<?= $_SESSION['usuario_id'] ?>">

        <div class="mb-3 mt-3">
          <label class="form-label">Observaciones del Dermatólogo</label>
          <textarea name="observaciones" class="form-control" rows="2" ><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Recomendaciones</label>
          <textarea name="recomendacion" class="form-control" rows="1" ><?= htmlspecialchars($_POST['recomendacion'] ?? '') ?></textarea>
        </div>
        <div class="text-center card-footer">
          
            <button type="submit" class="btn text-white" style="background-color:#28a688;" name="action" value="save" formaction="?controller=deteccion&action=save">GUARDAR DETECCIÓN</button>
           
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para editar la imagen -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
  <!-- modal-xl: modal ancho; modal-dialog-centered: centra verticalmente -->
  <div class="modal-dialog modal-lg modal-dialog-centered" style="max-height: 90vh;">
    <div class="modal-content" style="height: 90vh; display: flex; flex-direction: column;">
      <div class="modal-header" style="background-color:#28a688; color:#fff;">
        <h5 class="modal-title" id="editImageModalLabel">Editar Imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Cuerpo del modal con flex:1 para ocupar el espacio sobrante -->
      <div class="modal-body p-0" style="flex:1; display: flex; align-items: center; justify-content: center;">
        <!-- Contenedor flexible para la imagen -->
        <div style="width:100%; height:100%; display: flex; align-items: center; justify-content: center; overflow:hidden;">
          <!-- La imagen se ajusta al contenedor -->
          <img id="cropperImage" 
               src="" 
               alt="Editar imagen" 
               style="max-width:100px; max-height:100px; object-fit:contain;">
        </div>
      </div>

      <div class="modal-footer" style="border-top: 1px solid #ccc;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="applyEditBtn">Aplicar Edición</button>
      </div>
    </div>
  </div>
</div>



<!-- Modal para mostrar detalles del paciente y su historial clínico -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Detalles del Paciente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Información del Paciente -->
        <div id="modalPatientDetails">
          <span class="section-title">Información del Paciente</span>
          <table>
            <tbody>
              <tr>
                <th>Cédula</th>
                <td id="patientCedula">-</td>
                <th>Nombre</th>
                <td id="patientNombre">-</td>
              </tr>
              <tr>
                <th>Ocupación</th>
                <td id="patientOcupacion">-</td>
                <th>Estado Civil</th>
                <td id="patientEstCivil">-</td>
              </tr>
              <tr>
                <th>Género</th>
                <td id="patientGenero">-</td>
                <th>Fecha Nac.</th>
                <td id="patientFnacimiento">-</td>
              </tr>
              <tr>
                <th>Email</th>
                <td id="patientEmail">-</td>
                <th>Dirección</th>
                <td id="patientDireccion">-</td>
              </tr>
              <tr>
                <th>Teléfono</th>
                <td id="patientTelefono" colspan="3">-</td>
              </tr>
            </tbody>
          </table>
        </div>
        <hr>
        <!-- Historial Clínico -->
        <div id="modalHistoryDetails">
          <span class="section-title">Historial Clínico</span>
          <table>
            <thead>
              <tr>
                <th>Fecha Registro</th>
                <th>N° Historia</th>
                <th>Motivo</th>
                <th>Diagnóstico</th>
                <th>Observaciones</th>
                <th>Recomendación</th>
                <th>Atendido Por</th>
                <th>Imagen</th>
              </tr>
            </thead>
            <tbody id="historyTableBody">
              <tr>
                <td colspan="8" class="text-muted">No hay historial clínico registrado para este paciente.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Script para la edición de imagen con Cropper.js -->
<script>
  var cropper;
  document.getElementById('imageUpload').addEventListener('change', function(e) {
    var files = e.target.files;
    if (files && files.length > 0) {
      var file = files[0];
      var reader = new FileReader();
      reader.onload = function(e) {
        var image = document.getElementById('cropperImage');
        image.src = e.target.result;
        var editModal = new bootstrap.Modal(document.getElementById('editImageModal'), {
          backdrop: 'static',
          keyboard: false
        });
        editModal.show();
        if (cropper) { cropper.destroy(); }
        cropper = new Cropper(image, {
          aspectRatio: NaN,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
          
        });
      };
      reader.readAsDataURL(file);
    }
  });

  document.getElementById('applyEditBtn').addEventListener('click', function() {
    if (cropper) {
      cropper.getCroppedCanvas().toBlob(function(blob) {
        var fileInput = document.getElementById('imageUpload');
        var dt = new DataTransfer();
        var editedFile = new File([blob], fileInput.files[0].name, {type: blob.type});
        dt.items.add(editedFile);
        fileInput.files = dt.files;
        // Actualizar vista de imagen (si existe imagen en el preview)
        var previewImage = document.querySelector('.img-fluid.border');
        if (previewImage) { previewImage.src = URL.createObjectURL(blob); }
        var editModalEl = document.getElementById('editImageModal');
        var editModal = bootstrap.Modal.getInstance(editModalEl);
        editModal.hide();
        cropper.destroy();
        cropper = null;
      });
    }
  });
</script>
<script>
  var patientsData = [
    <?php foreach ($pacientes as $paciente): 
       $id   = $paciente->getId();
       $name = addslashes($paciente->getNombres() . ' ' . $paciente->getApellidos());
    ?>
      { id: '<?= $id ?>', name: '<?= $name ?>' },
    <?php endforeach; ?>
  ];
  inputName.addEventListener('input', function() {
    var val = this.value.trim();
    var match = patientsData.find(function(p) {
      return p.name.toLowerCase() === val.toLowerCase();
    });
    if (match) {
      inputId.value = match.id;
    } else {
      inputId.value = '';
    }
});

</script>

<!-- Script para cargar datos en el modal de detalles -->
<script>
  var clinicalHistories = {};
  <?php 
  foreach ($pacientes as $paciente) {
      $histories = HistoClinica::getAllByPaciente($paciente->getId());
      if (count($histories) > 0) {
          echo "clinicalHistories['" . $paciente->getId() . "'] = [];\n";
          foreach ($histories as $history) {
              $usuarioId = $history->getUsuario();
              if ($usuarioId) {
                  $usuarioObj = Usuario::getById($usuarioId);
                  $usuarioName = $usuarioObj ? addslashes($usuarioObj->getNombres() . " " . $usuarioObj->getApellidos()) : "Desconocido";
              } else {
                  $usuarioName = "Desconocido";
              }
              $fregistro     = addslashes($history->getFregistro());
              $numero        = addslashes($history->getNumero());
              $motivo        = addslashes($history->getMotivo());
              $diagnostico   = addslashes($history->getDiagnostico());
              $observaciones = addslashes($history->getObservaciones());
              $recomendacion = addslashes($history->getRecomendacion());
              $imagen        = addslashes($history->getImagen());
              echo "clinicalHistories['" . $paciente->getId() . "'].push({
                  fregistro: '$fregistro',
                  numero: '$numero',
                  motivo: '$motivo',
                  diagnostico: '$diagnostico',
                  observaciones: '$observaciones',
                  recomendacion: '$recomendacion',
                  imagen: '$imagen',
                  registrado_por: '$usuarioName'
              });\n";
          }
      } else {
          echo "clinicalHistories['" . $paciente->getId() . "'] = [];\n";
      }
  }
  ?>

  document.getElementById('detailsModal').addEventListener('show.bs.modal', function (event) {
  var inputId = document.getElementById('patientIdInput');
  var id = inputId.value;

  // Si no hay ID válido, limpia los campos y sal
  if (!id || !clinicalHistories[id]) {
    document.getElementById('patientCedula').textContent = "-";
    document.getElementById('patientNombre').textContent = "-";
    document.getElementById('patientOcupacion').textContent = "-";
    document.getElementById('patientEstCivil').textContent = "-";
    document.getElementById('patientGenero').textContent = "-";
    document.getElementById('patientFnacimiento').textContent = "-";
    document.getElementById('patientEmail').textContent = "-";
    document.getElementById('patientDireccion').textContent = "-";
    document.getElementById('patientTelefono').textContent = "-";
    document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="8" class="text-muted">No hay historial clínico registrado.</td></tr>';
    return;
  }

  // Encuentra el paciente (opcional, para info extra)
  var paciente = patientsData.find(function(p) { return p.id === id; });

  // Si quieres mostrar los datos (necesitas PHP/Javascript que pase más info si no tienes en patientsData)
  document.getElementById('patientNombre').textContent = paciente ? paciente.name : "-";
  // ... si tienes más datos en patientsData, puedes mostrarlos aquí ...

  // Historial clínico:
  var historyTableBody = document.getElementById('historyTableBody');
  historyTableBody.innerHTML = "";
  if (clinicalHistories[id] && clinicalHistories[id].length > 0) {
    clinicalHistories[id].forEach(function(history) {
      var tr = document.createElement('tr');
      tr.innerHTML = '<td>' + history.fregistro + '</td>' +
                     '<td>' + history.numero + '</td>' +
                     '<td>' + history.motivo + '</td>' +
                     '<td>' + history.diagnostico + '</td>' +
                     '<td>' + history.observaciones + '</td>' +
                     '<td>' + history.recomendacion + '</td>' +
                     '<td>' + history.registrado_por + '</td>' +
                     '<td>' + (history.imagen ? '<img src="'+ history.imagen +'" alt="Imagen Historial" style="max-width:100px;">' : 'Sin imagen') + '</td>';
      historyTableBody.appendChild(tr);
    });
  } else {
    historyTableBody.innerHTML = '<tr><td colspan="8" class="text-muted">No hay historial clínico registrado para este paciente.</td></tr>';
  }
});

</script>

<?php
  $baseDir = realpath(__DIR__ . '/../../public/directorio');
  $baseUrl = '/CentroMedico/public/directorio';
?>
<script>
  var patientImages = {};
  <?php foreach ($pacientes as $p):
      $id     = $p->getId();
      $folder = "$baseDir/$id";
      $files  = is_dir($folder)
              ? glob("$folder/*.{jpg,jpeg,png,gif}", GLOB_BRACE)
              : [];
      echo "patientImages['$id'] = [\n";
      foreach ($files as $f) {
          $url = $baseUrl . '/' . $id . '/' . basename($f);
          echo "  '" . addslashes($url) . "',\n";
      }
      echo "];\n";
  endforeach; ?>
  console.log('DEBUG patientImages:', patientImages);
</script>





<!-- Enlaces para datepicker si se requieren -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
  $(document).ready(function() {
    $('#datePicker1, #datePicker2').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });
  });
</script>

<div class="modal fade" id="selectImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Imágenes del paciente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="imageSelectionGallery" class="d-flex flex-wrap gap-2">
          <p class="text-muted">Seleccione un paciente arriba para ver sus imágenes.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
var selectModal = document.getElementById('selectImageModal');
selectModal.addEventListener('show.bs.modal', function () {
  var id = document.getElementById('patientIdInput').value;
  var gallery = document.getElementById('imageSelectionGallery');
  gallery.innerHTML = '';
  var imgs = patientImages[id] || [];
  if (!id || imgs.length === 0) {
    gallery.innerHTML = '<p class="text-muted">No hay imágenes disponibles para este paciente.</p>';
    return;
  }
  imgs.forEach(function(src) {
    var img = document.createElement('img');
    img.src = src;
    img.className = 'img-thumbnail';
    img.style.cursor = 'pointer';
    img.style.maxWidth = '120px';
    img.style.maxHeight = '120px';
    img.addEventListener('click', function() {
  var src = this.src;

  // 1) Pon la ruta en el hidden
  var url = new URL(src, window.location.origin);
document.getElementById('selectedImagePath').value = url.pathname;


  // 2) Actualiza SOLO el preview grande (mainImageContainer)
  var container = document.getElementById('mainImageContainer');
  var oldPlaceholder = document.getElementById('noImagePlaceholder');
  if (oldPlaceholder) oldPlaceholder.remove();

  var mainImg = document.getElementById('mainImagePreview');
  if (!mainImg) {
    mainImg = document.createElement('img');
    mainImg.id = 'mainImagePreview';
    mainImg.className = 'img-fluid border';
    mainImg.style.height = '350px';
    mainImg.style.width  = '400px';
    container.appendChild(mainImg);
  }
  mainImg.src = src;

  // 3) Limpia el file input para que no choque
  document.getElementById('imageUpload').value = '';

  // 4) Cierra modal
  bootstrap.Modal.getInstance(selectModal).hide();
});

    gallery.appendChild(img);
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var inputName = document.getElementById('patientInput');
  var inputId   = document.getElementById('patientIdInput');

  inputName.addEventListener('input', function() {
    var val = this.value.trim();
    var match = patientsData.find(function(p) {
      return p.name.toLowerCase() === val.toLowerCase();
    });
    if (match) {
      inputId.value = match.id;
    } else {
      inputId.value = '';
    }
  });

  inputName.addEventListener('blur', function() {
    if (!inputId.value) {
      this.value = '';
    }
  });
});
document.addEventListener('DOMContentLoaded', function() {
  var showDetailsBtn = document.getElementById('showDetailsBtn');
  showDetailsBtn.addEventListener('click', function() {
    var id = document.getElementById('patientIdInput').value;
    if (!id) {
      alert('Seleccione un paciente válido primero.');
      return;
    }
    var detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    detailsModal.show();
  });
});

</script>


<!-- Modal para mostrar detalles -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Usamos modal-xl para más espacio -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Detalles de la Detección</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Columna Izquierda: Imagen subida -->
                    <div class="col-md-4 text-center">
                        <h6>Imagen Subida</h6>
                        <?php if (!empty($imagePath)): ?>
                            <img src="<?= $imagePath ?>" alt="Imagen subida" class="img-fluid rounded" style="max-height: 215px;">
                        <?php else: ?>
                            <p class="text-muted">No hay imagen subida.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Columna Central: Características (color, borde, textura) -->
                    <div class="col-md-2">
                        <h6>Características</h6>
                        
                                <h6>Color</h6>
                                <p class="text-muted">Descripción del color de la lesión.</p>

                                <hr>
                                <h6>Borde</h6>
                                <p class="text-muted">Descripción del borde de la lesión.</p>

                                <hr>
                                <h6>Textura</h6>
                                <p class="text-muted">Descripción de la textura de la lesión.</p>
                    
                    </div>

                    <!-- Columna Derecha: Imágenes similares -->
                    <div class="col-md-6">
                        <h6>Imágenes Similares</h6>
                        <?php if (!empty($similarImages)): ?>
                            <div class="d-flex flex-wrap gap-2"> <!-- Contenedor flexible para las imágenes -->
                                <?php foreach ($similarImages as $index => $similarImage): 
                                    $similarImagePath = explode("DermMel", $similarImage);
                                    $path = "DermMel" . $similarImagePath[1]; ?>
                                    <div class="flex-grow-1" style="max-width: 48%;"> <!-- Ajusta el ancho máximo -->
                                        <img src="<?= $path ?>" 
                                            alt="Imagen similar" 
                                            class="img-fluid border rounded w-100 h-auto" 
                                            style="max-height: 120px;"> <!-- Ajusta la altura máxima -->
                                        <p class="text-center mt-2"><?= $diagnostic[$similarLabels[$index]] ?? 'Sin etiqueta' ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay imágenes similares.</p>
                        <?php endif; ?>
                    </div>
                </div>
                 <!-- Resultados de la predicción -->
                 <div class="mb-4 pt-3">
                    <h6>Resultados</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Predicción</label>
                            <input type="text" class="form-control"
                                   value="<?= htmlspecialchars($predictionText) ?>"
                                   readonly
                                   style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Probabilidad</label>
                            <input type="text" class="form-control"
                                   value="<?= htmlspecialchars($probabilityText) ?> %"
                                   readonly
                                   style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
