<?php 

if(!isset($_SESSION)) 
    { 
        session_start(); 
    }

class PacienteController
{	
	function __construct(){}

	public function register(){
		require_once('Views/Paciente/register.php');
	}

	public function save(){
		$paciente= new Paciente(null,$_POST['cedula'], $_POST['nombres'], $_POST['apellidos'], $_POST['ocupacion'], $_POST['estcivil'], $_POST['genero'],$_POST['date'],$_POST['email'],$_POST['direccion'], $_POST['telefono']);
		Paciente::save($paciente);
		$_SESSION['mensaje']='Registro guardado satisfactoriamente';		
		$this->show();
		//header('Location: index.php');
	}

	//muestra los pacientes por usuario
	public function show() {
		$pacientes = Paciente::all();
		
		// 1. Leer parámetros de ordenamiento
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'cedula'; // Por defecto, ordena por cédula
		$dir  = isset($_GET['dir']) ? $_GET['dir'] : 'asc';      // Ascendente por defecto
		
		// 2. Ordenar el arreglo $pacientes según la columna indicada
		usort($pacientes, function($a, $b) use ($sort, $dir) {
			// Determinar valores a comparar según la columna
			switch ($sort) {
				case 'cedula':
					$valA = $a->getCedula();
					$valB = $b->getCedula();
					break;
				case 'nombres':
					$valA = $a->getNombres();
					$valB = $b->getNombres();
					break;
				case 'apellidos':
					$valA = $a->getApellidos();
					$valB = $b->getApellidos();
					break;
				case 'ocupacion':
					$valA = $a->getOcupacion();
					$valB = $b->getOcupacion();
					break;
				case 'email':
					$valA = $a->getEmail();
					$valB = $b->getEmail();
					break;
				case 'telefono':
					$valA = $a->getTelefono();
					$valB = $b->getTelefono();
					break;
				default:
					// Si no coincide, usar cédula como fallback
					$valA = $a->getCedula();
					$valB = $b->getCedula();
					break;
			}
	
			// Comparar
			if ($valA == $valB) return 0;
	
			// Dependiendo de la dirección
			if ($dir === 'asc') {
				return ($valA < $valB) ? -1 : 1;
			} else {
				return ($valA > $valB) ? -1 : 1;
			}
		});
	
		// 3. Paginación
		$lista_pacientes = [];
		$registros = 6; // Debe ser siempre par
		if (count($pacientes) > $registros) { 
			$botones = (int) ceil(count($pacientes) / $registros);
			
			if (!isset($_GET['boton'])) { 
				$res = $registros * 1;
				for ($i = 0; $i < $res; $i++) { 
					$lista_pacientes[] = $pacientes[$i];
				}
			} else {
				$res = $registros * $_GET['boton'];
				for ($i = $res - $registros; $i < $res; $i++) { 
					if ($i < count($pacientes)) {
						$lista_pacientes[] = $pacientes[$i];
					}                
				}
			}
		} else { 
			$botones = 0;
			$lista_pacientes = $pacientes;
		}
	
		// 4. (Opcional) Guardar sort y dir en sesión para reutilizar en la vista
		$_SESSION['sort'] = $sort;
		$_SESSION['dir']  = $dir;
	
		require_once('Views/Paciente/show.php');
	}
	
	

	public function error(){
		require_once('Views/User/error.php');
	} 

	public function showupdate(){
		$id=$_GET['id'];
		$paciente=Paciente::getById($id);
		require_once('Views/Paciente/update.php');
		//Usuario::update($usuario);
		//header('Location: ../index.php');
	}

	public function update(){
		$paciente= new Paciente($_POST['id'],$_POST['cedula'], $_POST['nombres'], $_POST['apellidos'], $_POST['ocupacion'], $_POST['estcivil'], $_POST['genero'],$_POST['date'],$_POST['email'],$_POST['direccion'], $_POST['telefono']);

		//var_dump($paciente);
		//die();
		Paciente::update($paciente);
		$_SESSION['mensaje']='Registro actualizado satisfactoriamente';
		$this->show();
		//header('Location: index.php');
	}

	public function delete(){
		Paciente::delete($_GET['id']);
		$_SESSION['mensaje']='Registro eliminado satisfactoriamente';
		$this->show();
		//header('Location: index.php');
	}
	//muestra un paciente por cedula
		public function buscar() {
		// Si el campo de búsqueda no está vacío
		if (!empty($_POST['search'])) {
			$searchTerm = $_POST['search'];
			
			// Buscar pacientes por cédula, nombres, apellidos, teléfono (todos los campos relevantes)
			$lista_pacientes = Paciente::search($searchTerm);
			$botones=0;
			// Mostrar los resultados como una tabla HTML
			require_once('Views/Paciente/show.php');
		} else {
			// Si no se ha enviado nada, mostramos todos los pacientes
			$this->show();
		}
	}
	
}