<?php 

if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
require_once('Models/Paciente.php');

class HistoriaController
{	
	function __construct(){}

	 // Función para registrar o actualizar una historia clínica
	 public function register(){
        require_once('Views/Historia/register.php');

    }

	// Función para guardar una historia clínica
    public function save(){
        $idHistoria = $this->generarNumero();
        $historia = new HistoClinica(
            null, 
            $_POST['fecha'], 
            $idHistoria, 
            $_POST['motivo'], 
            $_POST['diagnostico'], 
			$_POST['observaciones'],
            $_POST['recomendacion'], 
			null,
            $_POST['paciente'],
			$_POST['usuario_id']
        );
		HistoClinica::save($historia);
		$_SESSION['mensaje']='Registro guardado satisfactoriamente';		
		$this->show();
		//header('Location: index.php');
    }

	// Función para mostrar todas las historias clínicas
    public function show(){
		// Obtener todos los registros
		$historias = HistoClinica::all();
	
		// Obtener parámetros de ordenamiento desde GET
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'fregistro'; // Por defecto ordena por fecha de registro
		$dir  = isset($_GET['dir']) ? $_GET['dir'] : 'asc';         // Dirección ascendente por defecto
	
		// Ordenar el arreglo de historias según la columna
		usort($historias, function($a, $b) use ($sort, $dir) {
			switch($sort) {
				case 'fregistro': 
					$valA = strtotime($a->getFregistro());
					$valB = strtotime($b->getFregistro());
					break;
				case 'numero':
					$valA = $a->getNumero();
					$valB = $b->getNumero();
					break;
				case 'nombres':
					// Se obtienen los datos del paciente para comparar
					$pacA = Paciente::getById($a->getPaciente());
					$pacB = Paciente::getById($b->getPaciente());
					$valA = $pacA ? $pacA->getNombres() : '';
					$valB = $pacB ? $pacB->getNombres() : '';
					break;
				case 'apellidos':
					$pacA = Paciente::getById($a->getPaciente());
					$pacB = Paciente::getById($b->getPaciente());
					$valA = $pacA ? $pacA->getApellidos() : '';
					$valB = $pacB ? $pacB->getApellidos() : '';
					break;
				default:
					// Ordena por fecha de registro si el parámetro no coincide
					$valA = strtotime($a->getFregistro());
					$valB = strtotime($b->getFregistro());
			}
			if ($valA == $valB) return 0;
			if ($dir === 'asc') {
				return ($valA < $valB) ? -1 : 1;
			} else {
				return ($valA > $valB) ? -1 : 1;
			}
		});
	
		// Paginación
		$lista_historias = [];
		$registros = 6; // Debe ser siempre par
		if (count($historias) > $registros) { // Solo si hay más historias que los registros mostrados
			$botones = ceil(count($historias) / $registros);
			
			if (!isset($_GET['boton'])) { // La primera vez carga los registros del botón 1
				$res = $registros * 1;
				for ($i = 0; $i < $res; $i++) { 
					$lista_historias[] = $historias[$i];
				}
			} else {
				// Multiplica el número del botón por el número de registros mostrados
				$res = $registros * $_GET['boton'];
				for ($i = $res - $registros; $i < $res; $i++) { 
					if ($i < count($historias)) {
						$lista_historias[] = $historias[$i];
					}
				}
			}
		} else { // Si no hay paginación
			$botones = 0;
			$lista_historias = $historias;
		}
	
		// Para poder reutilizar los parámetros de ordenamiento en la vista
		$_SESSION['sort'] = $sort;
		$_SESSION['dir']  = $dir;
	
		require_once('Views/Historia/show.php');
	}
	

	public function error(){
		require_once('Views/User/error.php');
	} 


	//muestra una historia clínicas por  numero
	public function buscar(){
		if (!empty($_POST['searchTerm'])) {
			$searchTerm = $_POST['searchTerm'];
			
			// Buscar historias clínicas basadas en número
			$lista_historias = HistoClinica::search($searchTerm);
			$botones = 0;
			require_once('Views/Historia/show.php');
		} else {
			// Si no hay término de búsqueda, mostrar todas las historias clínicas
			$this->show();
		}
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


	/*** Enfermedades familiares***/
	/*/guardar las enfermedades familiares
	public function saveAntFamiliares(){
		$antFamiliar= new AntFamiliar(null,$_POST['cardiopatia'], $_POST['diabetes'], $_POST['cancer'], $_POST['enfcardiovasculares'], $_POST['hipertension'], $_POST['enfmentales'], $_POST['tubercolosis'], $_POST['enfinfecciosas'], $_POST['malformacion'], $_POST['otra'], $_POST['descripcionfami'],$_POST['paciente']);
		//var_dump($antFamiliar);
		//die();
		HistoClinica::saveAntFamiliar($antFamiliar);
	}

	//actualizar las enfermedades familiares
	public function updateAntFamiliares(){		
		$antFamiliar= new AntFamiliar($_POST['idfamiliar'],$_POST['cardiopatia'], $_POST['diabetes'], $_POST['cancer'], $_POST['enfcardiovasculares'], $_POST['hipertension'], $_POST['enfmentales'], $_POST['tubercolosis'], $_POST['enfinfecciosas'], $_POST['malformacion'], $_POST['otra'], $_POST['descripcionfami'],$_POST['paciente']);
		
		HistoClinica::updateAntFamiliar($antFamiliar);
	}

	/*** Antecedentes personales***/
	/*/guardar antecedentes personales
	public function saveAntPersonales(){
		//var_dump($_POST['hipertension']);
		//die();
		$antPersonal= new AntPersonal(null,$_POST['imenarquia'], $_POST['imenopausia'],$_POST['vsexualactiva'],$_POST['ciclos'],$_POST['gesta'], $_POST['partos'], $_POST['abortos'], $_POST['cesareas'], $_POST['fum'],$_POST['fup'], $_POST['hvivos'],$_POST['mpf'],$_POST['descripcionper'],$_POST['paciente'] );
		HistoClinica::saveAntPersonal($antPersonal);
	}
	//actualiza antecedentes personales
	public function updateAntPersonales(){
		$antPersonal= new AntPersonal($_POST['idpersonal'],$_POST['imenarquia'], $_POST['imenopausia'],$_POST['vsexualactiva'],$_POST['ciclos'],$_POST['gesta'], $_POST['partos'], $_POST['abortos'], $_POST['cesareas'], $_POST['fum'],$_POST['fup'], $_POST['hvivos'],$_POST['mpf'],$_POST['descripcionper'],$_POST['paciente'] );
		HistoClinica::updateAntPersonal($antPersonal);
	}


	/*** Examenes visuales***/
	/*/guardar examenes visuales
	public function saveExaVisuales(){
		$exaVisual= new ExaVisual(null,$_POST['descripcionvisual'],$_POST['paciente'] );
		HistoClinica::saveExaVisual($exaVisual);
	}

	//actualiza examenes visuales
	public function updateExaVisuales(){
		$exaVisual= new ExaVisual($_POST['idvisual'],$_POST['descripcionvisual'],$_POST['paciente'] );
		HistoClinica::updateExaVisual($exaVisual);
	}*/


	//REPORTES
	public function reporteHistorico(){
		//validar que no se abra si no hay consultas
		$numero=$_GET['numero'];
		$historia = HistoClinica::getByNumero($numero);
		require_once('Views/Historia/details.php');
		
		//header('Location: Controllers/HistoricoPdf.php?id='.$_GET['id']);		
	}
	public function reporte() {
		// Limpiar cualquier salida previa
		if (ob_get_length()) {
			ob_end_clean();
		}
		
		// Incluir el archivo que genera el reporte PDF
		require_once('generar_reporte.php');
		
		// Finaliza la ejecución para evitar que se envíe contenido adicional
		exit();
	}
	


	
}

