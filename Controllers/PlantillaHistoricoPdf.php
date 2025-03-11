<?php 
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php'); // Ajusta la ruta según la ubicación de TCPDF
class PlantillaHistoricoPdf 
{
    function calcularEdad($fechaNacimiento) {
        $nacimiento = new DateTime($fechaNacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($nacimiento);
        return $edad->y;
    }
    function generarHistoriaClinicaPDF1($reporte) {
      $pdf = new TCPDF();
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor('Hospital');
      $pdf->SetTitle('Historia Clínica');
      $pdf->SetHeaderData('', 0, 'HISTORIA CLÍNICA', 'CENTRO MEDICO DE LA MIEL');
      $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
      $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
      $pdf->SetMargins(15, 27, 15);
      $pdf->SetAutoPageBreak(TRUE, 25);
      $pdf->SetFont('helvetica', '', 12);
      $pdf->AddPage();
  
      // Datos de la historia clínica
      $cedula = $reporte[0]['cedula'];
      $nombres = $reporte[0]['nombres'];
      $apellidos = $reporte[0]['apellidos'];
      $ocupacion = $reporte[0]['ocupacion'];
      $estcivil = $reporte[0]['estcivil'];
      $genero = $reporte[0]['genero'];
      $fnacimiento = $reporte[0]['fnacimiento'];
      $email = $reporte[0]['email'];
      $direccion = $reporte[0]['direccion'];
      $telefono = $reporte[0]['telefono'];
      $numero_hc = $reporte[0]['numero'];
      $motivo = $reporte[0]['motivo'];
      $diagnostico = $reporte[0]['diagnostico'];
      $observaciones = $reporte[0]['observaciones'];
      $recomendacion = $reporte[0]['recomendacion'];
  
      $nom_ap = $nombres . ' ' . $apellidos;
  
      // Contenido del PDF
      $html = "
          <h1 style='text-align:center;'>HISTORIA CLÍNICA</h1>
          <h3>DATOS DEL PACIENTE:</h3>
          <p><strong>Nombre:</strong> $nom_ap</p>
          <p><strong>Cédula:</strong> $cedula</p>
          <p><strong>Edad:</strong> " . $this->calcularEdad($fnacimiento) . " años</p>
          <p><strong>Sexo:</strong> $genero</p>
          <p><strong>Ocupación:</strong> $ocupacion</p>
          <p><strong>Estado Civil:</strong> $estcivil</p>
          <p><strong>Dirección:</strong> $direccion</p>
          <p><strong>Teléfono:</strong> $telefono</p>
          <p><strong>Email:</strong> $email</p>
          <p><strong>Número de Historia Clínica:</strong> $numero_hc</p>
          
          <h3>MOTIVO DE CONSULTA:</h3>
          <p>$motivo</p>
          
          <h3>ENFERMEDAD ACTUAL:</h3>
      ";
  
      // Escribir el HTML hasta este punto
      $pdf->writeHTML($html, true, false, true, false, '');
      $rutaImagen="assets\images\melanoma_9600.jpg";
      // **Insertar imagen**
      if (file_exists($rutaImagen)) {
          $pdf->Image($rutaImagen, 15, $pdf->GetY(), 40, 40, '', '', '', true, 150, '', false, false, 0, false, false, false);
      }
  
      // **Cuadro con datos de resultado**
      $x = 90;  // Posición en X
      $y = $pdf->GetY(); // Posición en Y después de la imagen
      $pdf->SetXY($x, $y);
      $pdf->SetFont('helvetica', 'B', 10);
      $pdf->Cell(40, 10, 'Paciente', 1, 0, 'C');
      $pdf->Cell(40, 10, $nom_ap, 1, 1, 'C');
      $pdf->SetX($x);
      $pdf->Cell(40, 10, 'Resultado', 1, 0, 'C');
      $pdf->Cell(40, 10, $diagnostico, 1, 1, 'C');
      $pdf->SetX($x);
      $pdf->Cell(40, 10, 'Observaciones', 1, 0, 'C');
      $pdf->Cell(40, 10, $observaciones, 1, 1, 'C');
      // Espaciado antes de las recomendaciones
      $pdf->Ln(10);
  
      // **Recomendaciones**
      $html = "
          <h3>RECOMENDACIONES:</h3>
          <p>$recomendacion</p>
      ";
      $pdf->writeHTML($html, true, false, true, false, '');
  
      // Salida del PDF
      $pdf->Output('Historia_Clinica.pdf', 'I');
  }
  
  function generarHistoriaClinicaPDF2($reporte) {
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Configuraciones del documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Hospital');
    $pdf->SetTitle('Historia Clínica');
    $pdf->SetHeaderMargin(15);
    $pdf->SetHeaderData('', 0, 'CENTRO MEDICO DE LA PIEL', 'REPORTE HISTORIA CLINICA');
    $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
    $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->SetFont('helvetica', '', 14);
    $pdf->AddPage();

    // Logo en la parte superior derecha
    $logoPath = __DIR__ . '/../assets/images/logo-captura.jpg';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 135, 6, 60);
    }

    // Datos del paciente
    $nom_ap = $reporte[0]['nombres'] . ' ' . $reporte[0]['apellidos'];
    $edad   = $this->calcularEdad($reporte[0]['fnacimiento']);
    $diagnostico = $reporte[0]['diagnostico'] ?? "----";
    $recomendacion = $reporte[0]['recomendacion'] ?? "----";
    $observaciones = $reporte[0]['observaciones'] ?? "----";
    $imagen = $reporte[0]['imagen'] ?? ""; // Ruta de la imagen

    // === ESTRUCTURA HTML ===
    $html = '
    <style>
          /* Estilos para tablas, secciones, etc. */
          h2, h3 {
            text-align: center;
          }
          .titulo-seccion {
            background-color: #f2f2f2;
            font-weight: bold;
            
          }
          .seccion {
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 14pt;
            font-weight: bold;
            margin: 2px;
            text-decoration: underline;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
          }
          th, td {
            border: 1px solid #000;
            padding: 25px;
            font-size: 12pt;
          }
        </style>

    <h5></h5>
    <h3>REPORTE HISTORIA CLINICA Nro.'. $reporte[0]['numero'] . '</h3>

    <div cellmargin="5" class="seccion">INFORMACIÓN PACIENTE:</div>
    <table border="1" cellpadding="3" cellspacing="5">
      <tr><td><strong>Nombre:</strong> ' . $nom_ap . '</td><td><strong>Cédula:</strong> ' . $reporte[0]['cedula'] . '</td></tr>
      <tr><td><strong>Edad:</strong> ' . $edad . ' años</td><td><strong>Sexo:</strong> ' . $reporte[0]['genero'] . '</td></tr>
      <tr><td><strong>Ocupación:</strong> ' . $reporte[0]['ocupacion'] . '</td><td><strong>Estado Civil:</strong> ' . $reporte[0]['estcivil'] . '</td></tr>
      <tr><td><strong>Dirección:</strong> ' . $reporte[0]['direccion'] . '</td><td><strong>Teléfono:</strong> ' . $reporte[0]['telefono'] . '</td></tr>
      <tr><td colspan="2"><strong>Email:</strong> ' . $reporte[0]['email'] . '</td></tr>
      <tr><td colspan="2"><strong>Número de Historia Clínica:</strong> ' . $reporte[0]['numero'] . '</td></tr>
    </table>';

    // === Diagnóstico con imagen si existe ===
    if (!empty($imagen) && file_exists($imagen)) {
        // Diagnóstico con imagen (dos columnas)
        $html .= '
        <div class="seccion" >DIAGNOSTICO:</div>
        <table border="1" cellspacing="5">
          <tr>
            <td style="width: 40%; text-align: center; border: 1px solid #000;">
              <img src="' . $imagen . '" width="165" height="150" style="border: 1px solid #000;">
            </td>
            <td style="width: 60%; text-align: center; border: 1px solid #000; font-size: 14pt; font-weight: bold; padding: 20px;">
              <strong>Resultado:</strong> <br>' . $diagnostico . '
            </td>
          </tr>
        </table>';
    } else {
        // Diagnóstico sin imagen (una sola celda centrada)
        $html .= '
        <div class="seccion">DIAGNOSTICO:</div>
        <table cellpadding="3" >
          <tr>
            <td class="diagnostico-container">' . $diagnostico . '</td>
          </tr>
        </table>';
    }

    // === Otras secciones ===
    $html .= '
    <!-- Observaciones del Dermatólogo (similar a la imagen que mostraste) -->
        <div class="seccion">OBSERVACIONES DEL DERMATÓLOGO</div>
        <table>
          <tr>
            <td style="height: 80px;">' . $reporte[0]['observaciones'] . '</td>
          </tr>
        </table>
    
        <!-- Recomendaciones -->
        <div class="seccion">RECOMENDACIONES:</div>
        <table>
          <tr>
            <td style="height: 80px;">' . $reporte[0]['recomendacion'] . '</td>
          </tr>
        </table>';

    // Escribir el HTML en el PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Generar el PDF en pantalla
    $pdf->Output('Historia_Clinica.pdf', 'I');
    exit();
}

}