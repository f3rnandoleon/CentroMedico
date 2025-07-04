<?php
	if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
	//función que llama al controlador y su respectiva acción, que son pasados como parámetros
	function call($controller, $action){
		//importa el controlador desde la carpeta Controllers
		require_once('Controllers/' . $controller . 'Controller.php');
		//crea el controlador

		switch($controller){
			case 'usuario':
				require_once('Models/Usuario.php');
				require_once('Models/HistoClinica.php');
				require_once('Models/Paciente.php');
				require_once('Models/Cita.php');

				$controller= new UsuarioController();
				break; 
			case 'paciente':
				require_once('Models/Paciente.php');
				$controller=new PacienteController();
				break; 
			case 'historia':
				require_once('Models/AntPersonal.php');
				require_once('Models/AntFamiliar.php');
				require_once('Models/HistoClinica.php');
				require_once('Models/ExaVisual.php');
				require_once('Models/Paciente.php');
				require_once('Models/Usuario.php');
				
				$controller=new HistoriaController();
				break; 
			case 'consulta':
				require_once('Models/SigVitales.php');
				require_once('Models/HistoClinica.php');
				require_once('Models/Consulta.php');
				require_once('Models/Sistema.php');
				require_once('Models/ExaFisico.php');
				require_once('Models/ExaComplementario.php');
				require_once('Models/Receta.php');
				$controller= new ConsultaController();
				break;
			case 'deteccion':  // Nuevo controlador
				 // Carga el modelo si es necesario
				 require_once('Models/Usuario.php');
				require_once('Models/HistoClinica.php');
				require_once('Models/Paciente.php');
				require_once('Models/Cita.php');

				$controller = new DeteccionController();
				break;
			case 'cita':  // Nuevo controlador
				 // Carga el modelo si es necesario
				require_once('Models/HistoClinica.php');
				 require_once('Models/Usuario.php');
				 require_once('Models/Paciente.php'); // Para obtener datos de pacientes si se requieren en la vista
				 require_once('Models/Cita.php'); // Modelo de la cita
				$controller = new CitaController();
				break;				
		}
		//llama a la acción del controlador
		$controller->{$action }();
	}


	//array con los controladores y sus respectivas acciones
	$controllers= array(
						'usuario'=>['show','showAdmin','register','registerAdmin','save','saveAdmin','showregister', 'update','updateAdmin','showupdateAdmin', 'delete', 'showLogin','login','logout','error', 'welcome','reportGeneral','validarCedula','buscar'],
						'paciente'=>['register','save', 'show', 'showupdate','update', 'delete','buscar','uploadImage'],
						'historia'=>['register','save', 'show', 'showupdate','update', 'delete','reporteHistorico','reporte','buscar'],
						'consulta'=>['register','save','show', 'showupdate','update','recetaPdf','buscar'],
						'deteccion'=>['detectar','save'],
						'cita'=>['register','save','show','update','showupdate','error','buscar','delete','marcarRealizadaSubmit','welcome']
						);
						if ($controller == 'historia' && $action == 'reporte') {
							require_once('./Controllers/generar_reporte.php');
							exit; // Finaliza la ejecución para que no se cargue el layout
						}
	//verifica que el controlador enviado desde index.php esté dentro del arreglo controllers
	if (array_key_exists($controller, $controllers)) {

		//verifica que el arreglo controllers con la clave que es la variable controller del index exista la acción
		if (in_array($action, $controllers[$controller])) {
			//llama  la función call y le pasa el controlador a llamar y la acción (método) que está dentro del controlador
			if (isset($_SESSION['usuario'])){//ingresa sólo cuando el usuario tiene sesión abierta
				call($controller, $action);}
			elseif($controller=='usuario'&&($action=='showLogin'||$action=='login'||$action=='register'||$action=='save')){// ingresa a páginas que no necesitam sesión de usuario
				call($controller, $action);
			}else{//página que indica que no hay permisos
				call($controller, 'error');
			}
		}else{
			call('usuario', 'error');
		}
	}else{// le pasa el nombre del controlador y la pagina de error
		call('usuario', 'error');
	}
?>