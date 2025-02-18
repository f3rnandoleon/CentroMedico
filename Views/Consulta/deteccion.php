<?php if(!isset($_SESSION)) 
    { 
        session_start(); 
    } ?>
<?php
function predictMelanoma($imagePath) {
    $url = 'http://127.0.0.1:5000';  // Cambia a la IP de tu servidor si es remoto

    $postFields = [
        'file' => new CURLFile($imagePath)  // Cargar la imagen
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

// Ejemplo de uso:
$imagePath = 'ruta/a/tu/imagen.jpg';  // Ruta de la imagen en tu servidor PHP
$result = predictMelanoma($imagePath);

if (isset($result['prediction'])) {
    echo "Resultado de la predicción: " . $result['prediction'];
} else {
    echo "Error: " . $result['error'];
}
?>
