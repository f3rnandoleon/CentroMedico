<?php
ob_start(); // Iniciar el búfer de salida
      require_once('./connection.php');
      include_once('PlantillaHistoricoPdf.php');
      $db=Db::getConnect();
      $sql=$db->prepare('SELECT *
      FROM pacientes p, histoclinicas hc
      WHERE hc.paciente = p.id
      AND p.id = :id;');
      $sql->bindParam(':id',$_GET['id']);
      $sql->execute();
      $reporte= $sql->fetchAll();
      
      //DATOS HC Y PACIENTE
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
      $recomendacion = $reporte[0]['recomendacion'];

      $nom_ap = $nombres . ' ' . $apellidos;



      //pdf
      ob_end_clean(); 
      
      $pdf = new PlantillaHistoricoPdf();


      //antecedentes personales
      $pdf->generarHistoriaClinicaPDF2($reporte);
      

      //formato de salida para el nombre del archivo
      $nombre='HISTORICO-HC-'.$numero_hc.'-'.date("Y").'-'.date("m").'-'.date("d");
      $pdf->Output('I',$nombre.'.pdf');
      ob_end_flush(); 
?>