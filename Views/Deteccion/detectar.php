<?php if(!isset($_SESSION)) 
    { 
        session_start(); 
    } ?>
<?php
function predictMelanoma($imagePath) {
    $url = 'http://127.0.0.1:5000/predict';  // Asegúrate de que esté la ruta correcta

    $postFields = [
        'file' => new CURLFile($imagePath)  // Cargar la imagen
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Verificar la respuesta
    $response = curl_exec($ch);
    var_dump($response);  // Verificar la respuesta
    curl_close($ch);

    return json_decode($response, true);
}
?>
<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Predicción de Melanoma</h4>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="imageUpload" class="form-label">Sube una imagen de la lesión</label>
                    <input class="form-control" type="file" id="imageUpload" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-success">Realizar predicción</button>
            </form>

            <?php
            // Verifica si se ha enviado una imagen y ejecuta la predicción
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
                $imagePath = $_FILES['image']['tmp_name'];  // Ruta temporal de la imagen cargada
                echo ($imagePath);
                $result = predictMelanoma($imagePath);  // Llama a la función de predicción

                // Muestra el resultado de la predicción
                if (isset($result['prediction'])) {
                    echo "<div class='mt-4 alert alert-info'>Resultado de la predicción: <strong>" . $result['prediction'] . "</strong></div>";
                    echo "<div class='mt-4 alert alert-info'>Resultado de la predicción: <strong>" . $result['probability'] . "</strong></div>";
                } else {
                    echo "<div class='mt-4 alert alert-danger'>Error: " . $result['error'] . "</div>";
                }
            }
            ?>

            <!-- Mostrar imagen cargada -->
            <?php if (isset($_FILES['image'])): ?>
                <div class="mt-4">
                    <h5>Imagen cargada:</h5>
                    <img src="data:image/jpeg;base64,<?= base64_encode(file_get_contents($_FILES['image']['tmp_name'])) ?>" class="img-fluid border rounded" alt="Imagen cargada">
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>