<?php
class DeteccionController {

    public function detectar() {
        require_once('Views/Deteccion/detectar.php');
    }
    public function save() { 
        $idHistoria = $this->generarNumero();
        $uploadDir = "uploads/";
        $imageUrl = null;
    
        // Si la imagen ya fue detectada, usa la misma URL
        if (!empty($_POST['image'])) {
            $imageUrl = $_POST['image'];
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;
    
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                $imageUrl = $imagePath;
            }
        }
        if (!empty($_POST['image'])) {
            $imageUrl = $_POST['image'];
            // Convierte ruta absoluta a relativa si es necesario
            $pos = strpos($imageUrl, "public/");
            if ($pos !== false) {
                $imageUrl = substr($imageUrl, $pos);
            }
        }
        $historia = new HistoClinica(
            null, $_POST['fecha'], $idHistoria, "Detección de Melanoma", 
            $_POST['resultado'] , 
            $_POST['observaciones'], $_POST['recomendacion'], 
            $imageUrl, $_POST['paciente'] ?? null, $_POST['usuario_id'] ?? null
        );
    
        HistoClinica::save($historia);
        $_SESSION['mensaje'] = 'Registro guardado satisfactoriamente';  
        $this->detectar();
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
