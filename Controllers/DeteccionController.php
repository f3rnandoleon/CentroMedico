<?php
class DeteccionController {
    public function detectar() {
        require_once('Views/Deteccion/detectar.php');
    }
    public function save() {
        $idHistoria = $this->generarNumero();
        
        // Directorio donde se guardarán las imágenes
        $uploadDir = "uploads/";
    
        // Verificar si se subió una imagen
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;
    
            // Crear el directorio si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            // Mover el archivo subido a la carpeta uploads
            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                $imageUrl = $imagePath;  // Guardamos la ruta para la base de datos
            } else {
                $_SESSION['mensaje'] = 'Error al guardar la imagen.';
                $this->show();
                return;
            }
        } else {
            $imageUrl = null; // No se subió imagen
        }
        $diagnostico=$_POST['resultado']+" "+$_POST['probabilidad'];
        // Crear el objeto HistoClinica con los datos del formulario
        $motivo="Deteccion de Melanoma";
        $historia = new HistoClinica(
            null, 
            $_POST['fecha'], 
            $idHistoria, 
            $motivo, 
            $diagnostico,
            $_POST['observaciones'], 
            $_POST['recomendacion'], 
            $imageUrl, // Se guarda la ruta de la imagen en la BD
            $_POST['paciente']
        );
    
        // Guardar en la base de datos
        HistoClinica::save($historia);
        
        $_SESSION['mensaje'] = 'Registro guardado satisfactoriamente';		
        $this->show();
    }
    
    public function generarNumero(){
		$numero=HistoClinica::getMaxId();
		$numero = (NULL) ? $numero : $numero+1 ;
		if ($numero<10) {
			$numero= "000".$numero;
		} elseif($numero>=10&&$numero<99) {
			$numero="00".$numero;
		}elseif ($numero>=100&&$numero<999) {
			$numero="0".$numero;
		}elseif ($numero>=1000&&$numero<9999) {
			$numero=$numero;
		}		
		return $numero;
	}
}
?>
