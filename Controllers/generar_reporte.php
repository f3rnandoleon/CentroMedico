<?php
// generar_reporte.php

// Inicia el búfer (opcional) y evita cualquier salida previa
ob_start();

// Incluye la conexión a la base de datos y la librería TCPDF
require_once('connection.php');
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('PlantillaHistoricoPdf.php');

// Recibe el ID
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID no proporcionado.");
}

// Obtiene los datos (modifica la consulta según corresponda)
$db = Db::getConnect();
$sql = $db->prepare('SELECT * FROM pacientes p, histoclinicas hc WHERE hc.paciente = p.id AND hc.id = :id');
$sql->bindParam(':id', $id);
$sql->execute();
$reporte = $sql->fetchAll();

if (!$reporte) {
    die("No se encontraron datos.");
}

// Limpia cualquier salida en búfer para evitar errores de cabecera
ob_end_clean();

// Crea y envía el PDF
$pdfGenerator = new PlantillaHistoricoPdf();
$pdfGenerator->generarHistoriaClinicaPDF2($reporte);
?>
