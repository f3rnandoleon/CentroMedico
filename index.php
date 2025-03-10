<?php 
	if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
	 require_once('connection.php');
	// la variable controller guarda el nombre del controlador y action guarda la acción por ejemplo registrar 
	//si la variable controller y action son pasadas por la url desde layout.php entran en el if
	if (isset($_GET['controller'])&&isset($_GET['action'])) {
		$controller=$_GET['controller'];
		$action=$_GET['action'];		
	} else {
		$controller='usuario';
		if (isset($_SESSION['usuario'])) {
			$action='welcome';
		}else{
			$action='showLogin';
		}
		
	}	
	// Si la acción es la de generar PDF, llamamos directamente al controlador
    if ($controller == 'historia' && $action == 'reporte') {
        // Se llama al controlador y su método, sin cargar layout
        require_once('Controllers/generar_reporte.php');
        exit();
    }
    
    // Para el resto de acciones, se carga el layout
    require_once('Views/Layouts/layout.php');
?>