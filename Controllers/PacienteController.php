<?php 
/**
* Controlador PacienteController, para administrar los pacientes y datos relacionados
* Autor: Elivar Largo
* Sitio Web: wwww.ecodeup.com
* Fecha: 22-03-2017
*/
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
	public function show(){
		$pacientes = Paciente::all();
	
		// Paginator
		$lista_pacientes = [];
		$registros = 4; // debe ser siempre par
		if (count($pacientes) > $registros) { // Solo paginar si hay más registros que el límite
			$botones = (int) ceil(count($pacientes) / $registros);
			
			if (!isset($_GET['boton'])) { // Primera carga
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
		} else { // No necesita paginador
			$botones = 0;
			$lista_pacientes = $pacientes;
		}
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