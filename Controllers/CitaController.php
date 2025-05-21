<?php 
if (!isset($_SESSION)) { 
    session_start(); 
}


class CitaController
{
    function __construct(){}

    // Muestra el formulario de registro de cita
    public function register(){
        require_once('Views/Cita/register.php');
    }

    // Guarda una nueva cita
    public function save(){
        // Si no se envía estado lo asignamos por defecto a 'pendiente'
        $estado = isset($_POST['estado']) ? $_POST['estado'] : 'pendiente';
        // Creamos la cita. Los campos de fcreado_en se asignan automáticamente en la BD.
        $cita = new Cita(
            null,
            $_POST['paciente'],
            $_POST['fecha'],
            $_POST['hora'],
            $_POST['motivo'],
            $estado,
            $_POST['observaciones'],
            null, // fcreado_en: se asigna por la base de datos
            $_POST['usuario']

        );
        Cita::save($cita);
        $_SESSION['mensaje'] = 'Registro guardado satisfactoriamente';
        $this->show();
    }
    public function delete() {
        // Obtener el id de la cita desde la URL
        $id = $_GET['id'];
        
        // Llamar al método delete del modelo
        Cita::delete($id);
        
        // Mensaje de sesión indicando que se eliminó correctamente
        $_SESSION['mensaje'] = 'Registro eliminado satisfactoriamente';
        
        // Redirigir a la lista de citas
        $this->show();
    }
    public function showupdate(){
		$id=$_GET['id'];
		$cita=Cita::getBy($id);
		require_once('Views/Cita/update.php');
		//Usuario::update($usuario);
		//header('Location: ../index.php');
	}

    public function update() {
        // Se asume que el formulario envía los campos: id, paciente, fecha, hora, motivo, estado y observaciones.
        // El campo fcreado_en no se actualiza.
        $cita = new Cita(
            $_POST['id'],
            $_POST['paciente'],
            $_POST['fecha'],
            $_POST['hora'],
            $_POST['motivo'],
            $_POST['estado'],
            $_POST['observaciones'],
            null, // fcreado_en no se actualiza, se mantiene el valor original o lo asigna la BD
            $_POST['usuario']

        );
    
        // Llamada al método update del modelo para actualizar la cita
        Cita::update($cita);
    
        // Mensaje de sesión indicando que la actualización fue exitosa
        $_SESSION['mensaje'] = 'Registro actualizado satisfactoriamente';
    
        // Se muestra la lista actualizada de citas
        $this->show();
    }
    
    // Muestra la lista de todas las citas con ordenamiento y paginación
    public function show(){
        // Obtenemos todas las citas
        $citas = Cita::all();
        
        // Leer parámetros de ordenamiento
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'fecha'; // por defecto se ordena por fecha
        $dir  = isset($_GET['dir']) ? $_GET['dir'] : 'asc';      // dirección ascendente por defecto

        // Ordenamos el arreglo según el campo seleccionado
        usort($citas, function($a, $b) use ($sort, $dir) {
            switch($sort) {
                case 'fecha':
                    $valA = strtotime($a->getFecha());
                    $valB = strtotime($b->getFecha());
                    break;
                case 'hora':
                    $valA = strtotime($a->getHora());
                    $valB = strtotime($b->getHora());
                    break;
                case 'motivo':
                    $valA = $a->getMotivo();
                    $valB = $b->getMotivo();
                    break;
                default:
                    $valA = strtotime($a->getFecha());
                    $valB = strtotime($b->getFecha());
            }
            if ($valA == $valB) return 0;
            return ($dir === 'asc') ? (($valA < $valB) ? -1 : 1) : (($valA > $valB) ? -1 : 1);
        });

        // Paginación (por ejemplo 6 registros por página)
        $lista_citas = [];
        $registros = 6;
        if (count($citas) > $registros) {
            $botones = ceil(count($citas) / $registros);
            if (!isset($_GET['boton'])) {
                for ($i = 0; $i < $registros; $i++) { 
                    $lista_citas[] = $citas[$i];
                }
            } else {
                $pagina = (int)$_GET['boton'];
                $inicio = $registros * ($pagina - 1);
                for ($i = $inicio; $i < $inicio + $registros && $i < count($citas); $i++) { 
                    $lista_citas[] = $citas[$i];
                }
            }
        } else {
            $botones = 0;
            $lista_citas = $citas;
        }
        
        // Guardamos los parámetros de orden para la vista
        $_SESSION['sort'] = $sort;
        $_SESSION['dir']  = $dir;

        require_once('Views/Cita/show.php');
    }

    // Función de búsqueda de citas
    public function buscar(){
        if (!empty($_POST['searchTerm'])) {
            $searchTerm = $_POST['searchTerm'];
            // Se busca en motivo y a través del JOIN también en nombres y apellidos del paciente
            $lista_citas = Cita::search($searchTerm);
            $botones = 0;
            require_once('Views/Cita/show.php');
        } else {
            $this->show();
        }
    }

    // Ejemplo de reporte
    public function reporte(){
        if (ob_get_length()) {
            ob_end_clean();
        }
        require_once('generar_reporte.php');
        exit();
    }

    // Función para mostrar un error
    public function error(){
        require_once('Views/User/error.php');
    }
}
?>
