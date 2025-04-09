<?php 

if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class UsuarioController
{	
	public function __construct(){}

	public function show(){
		//echo 'index desde UsuarioController';
			
		$usuario=Usuario::getById($_GET['id']);
		require_once('Views/User/show.php');
	}
	public function showAdmin(){
		$usuarios = Usuario::all();

		// 1. Leer parámetros de ordenamiento
		// Podrías ordenar por id, nombres, apellidos, email, rol o fecha.
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id'; // Ordena por id por defecto
		$dir  = isset($_GET['dir']) ? $_GET['dir'] : 'asc';

		// 2. Ordenar el arreglo $usuarios según la columna indicada
		usort($usuarios, function($a, $b) use ($sort, $dir) {
			switch ($sort) {
				case 'id':
					$valA = $a->getId();
					$valB = $b->getId();
					break;
				case 'nombres':
					$valA = $a->getNombres();
					$valB = $b->getNombres();
					break;
				case 'apellidos':
					$valA = $a->getApellidos();
					$valB = $b->getApellidos();
					break;
				case 'email':
					$valA = $a->getEmail();
					$valB = $b->getEmail();
					break;
				case 'rol':
					$valA = $a->getRol();
					$valB = $b->getRol();
					break;
				case 'fecha':
					$valA = $a->getFecha();
					$valB = $b->getFecha();
					break;
				default:
					// Si el campo no coincide, se ordena por id
					$valA = $a->getId();
					$valB = $b->getId();
					break;
			}

			if ($valA == $valB) {
				return 0;
			}
			return ($dir === 'asc') ? (($valA < $valB) ? -1 : 1) : (($valA > $valB) ? -1 : 1);
		});

		// 3. Paginación similar a la de pacientes
		$lista_usuarios = [];
		$registros = 6; // Número de registros por página
		if (count($usuarios) > $registros) {
			$botones = (int) ceil(count($usuarios) / $registros);
			if (!isset($_GET['boton'])) { 
				for ($i = 0; $i < $registros; $i++) {
					$lista_usuarios[] = $usuarios[$i];
				}
			} else {
				$pagina = (int) $_GET['boton'];
				$inicio = $registros * ($pagina - 1);
				for ($i = $inicio; $i < $inicio + $registros && $i < count($usuarios); $i++) {
					$lista_usuarios[] = $usuarios[$i];
				}
			}
		} else { 
			$botones = 0;
			$lista_usuarios = $usuarios;
		}

		// 4. Guardar sort y dir en sesión para la vista
		$_SESSION['sort'] = $sort;
		$_SESSION['dir']  = $dir;

		require_once('Views/User/showAdmin.php');
	}
	public function register(){
		//echo getcwd ();
		require_once('Views/User/register.php');
	}
	public function registerAdmin(){
		//echo getcwd ();
		require_once('Views/User/registerAdmin.php');
	}

	//guardar
	public function save(){
		//Usuario::save($usuario);
		$usuarios=[];
		$usuarios=Usuario::all();
		$existe=False;
		//var_dump($existe);
		//	die();
		foreach ($usuarios as $usuario) {
			//echo $usuario->alias."<br>".$_POST['alias']."<br>".$usuario->email;
			if ( strcmp($usuario->getEmail(),$_POST['email'])==0) {
				$existe=True;
			}
		}			

		if (!$existe) {
			$usuario= new Usuario(null, $_POST['nombres'],$_POST['apellidos'],$_POST['email'], $_POST['pwd'], NULL,date('Y-m-d'));
			Usuario::save($usuario);
			$_SESSION['mensaje']='Registro guardado satisfactoriamente';
			$this->showLogin();
			//header('Location: index.php');
			//require_once('Views/Layouts/layout.php');*/
		}else{
			$_SESSION['mensaje']='El  correo para tu usuario ya existen';
			$this->showLogin();
		}	
	}
	public function saveAdmin() {
		// Verificar que el email no exista
		$usuarios = Usuario::all();
		$existe = false;
		foreach ($usuarios as $usuario) {
			if (strcmp($usuario->getEmail(), $_POST['email']) == 0) {
				$existe = true;
				break;
			}
		}
		
		if (!$existe) {
			// Generar clave aleatoria de 8 caracteres hexadecimales
			$clave = bin2hex(random_bytes(4));
			
			// Crear el objeto Usuario. Ajusta los índices de $_POST según tu formulario.
			$usuario = new Usuario(
				null,
				$_POST['nombres'],
				$_POST['apellidos'],
				$_POST['email'],
				$clave,         // Clave generada
				$_POST['rol'],
				date('Y-m-d')
			);
			
			// Guardar en la BD
			Usuario::save($usuario);
			
			// Enviar el email con la clave usando PHPMailer
			$mail = new PHPMailer(true);
			try {
				// Configurar PHPMailer para usar SMTP
				$mail->isSMTP();
				$mail->Host       = 'smtp.gmail.com'; // Servidor SMTP (ajusta si usas otro proveedor)
				$mail->SMTPAuth   = true;
				$mail->Username   = 'hospitalarcoiris1405@gmail.com'; // Tu cuenta SMTP
				$mail->Password   = 'ivpy kipe gwne pszv';         // Tu clave SMTP (o contraseña de aplicación)
				$mail->SMTPSecure = 'tls'; // o 'ssl' según el proveedor
				$mail->Port       = 587;   // Puerto SMTP (587 para TLS, 465 para SSL)
				
				// Datos del remitente y destinatario
				$mail->setFrom('hospitalarcoiris1405@gmail.com', 'Centro Medico de la Piel');
				$mail->addReplyTo('hospitalarcoiris1405@gmail.com', 'Centro Medico de la Piel');

				$mail->addAddress($_POST['email'], $_POST['nombres'].' '.$_POST['apellidos']);
				
				// Contenido del mensaje
				$mail->isHTML(false);
				$mail->Subject = 'Registro de Usuario - Clave de Acceso';
				$mail->Body    = "Hola {$_POST['nombres']} {$_POST['apellidos']},\n\n" .
								 "Su cuenta ha sido registrada satisfactoriamente.\n" .
								 "Su clave de acceso es: $clave\n\n" .
								 "Cambie su clave una vez que inicie sesión.";
				
				$mail->send();
				
				$_SESSION['mensaje'] = 'Registro guardado satisfactoriamente. Se ha enviado su clave por email.';
			} catch (Exception $e) {
				$_SESSION['mensaje'] = "Registro guardado, pero el email no pudo ser enviado. Error: {$mail->ErrorInfo}";
			}
			
			// Redirige a la vista de login u otra según tu aplicación
			$this->showAdmin();
		} else {
			$_SESSION['mensaje'] = 'El correo para el usuario ya existe';
			$this->showAdmin();
		}
	}
	
	public function buscar() {
		// Si el campo de búsqueda no está vacío
		if (!empty($_POST['search'])) {
			$searchTerm = $_POST['search'];
			
			// Llama a un método en tu modelo Usuario para buscar por nombres, apellidos o email
			$lista_usuarios = Usuario::search($searchTerm);
			
			// Forzamos $botones = 0 (suponiendo no hacemos paginación aquí)
			$botones = 0;
	
			// Mostramos resultados en la vista de usuarios
			require_once('Views/User/showAdmin.php');
		} else {
			// Si no se envió nada, simplemente mostramos la lista completa
			$this->showAdmin();
		}
	}
	
	public function showregister(){
		$id=$_GET['id'];
		$usuario=Usuario::getById($id);
		require_once('Views/User/update.php');
		//Usuario::update($usuario);
		//header('Location: ../index.php');
	}
	public function showupdateAdmin(){
		$id=$_GET['id'];
		$usuario=Usuario::getById($id);
		require_once('Views/User/updateAdmin.php');
		//Usuario::update($usuario);
		//header('Location: ../index.php');
	}
	public function update(){
		$usuario= new Usuario($_POST['id'],$_POST['nombres'],NULL,NULL,NULL,NULL, NULL);

		//var_dump($usuario);
		//die();
		Usuario::update($usuario);
		$_SESSION['mensaje']='Registro actualizado satisfactoriamente';
		header('Location: index.php');
	}
	public function updateAdmin(){
	   // Instanciamos un nuevo objeto Usuario con los datos enviados por POST
		// Ajusta los $_POST según cómo tengas estructurado tu formulario (name="...")
		$usuario = new Usuario(
			$_POST['id'],
			$_POST['nombres'],
			$_POST['apellidos'],
			$_POST['email'],
			null,
			$_POST['rol'],
			$_POST['fecha']
		);

		// Actualizamos en la base de datos
		Usuario::updatesinclave($usuario);

		// Mensaje de sesión
		$_SESSION['mensaje'] = 'Usuario actualizado satisfactoriamente';

		// Redirige de nuevo a la lista de usuarios
		$this->showAdmin();
	}

	public function delete(){
		Usuario::delete($_GET['id']);
		$_SESSION['mensaje']='Registro eliminado satisfactoriamente';
		$this->showAdmin();
	}
		
	public function error(){
		require_once('Views/User/error.php');
	} 
	public function welcome(){
		$historias=HistoClinica::all();
		$pacientes=Paciente::all();
		$melanomas=HistoClinica::findByMelanoma();
		$nomelanomas=HistoClinica::findByNoMelanoma();
		require_once('Views/Deteccion/bienvenido.php');
	} 
	public function reportGeneral(){
		$melanomas=HistoClinica::findByMelanoma();
		$nomelanomas=HistoClinica::findByNoMelanoma();
		require_once('Views/ReportGeneral/show.php');
	} 

	public function showLogin(){
		require_once('Views/User/login.php');
	}

	//función que valida el usuario esté registrado
	public function login(){
		$usuarios=[];
		$usuarios=Usuario::all();
		$existe=False;
		//var_dump($existe);
		//	die();
		foreach ($usuarios as $usuario) {
			if (password_verify($_POST['pwd'],$usuario->getClave()) && strcmp($usuario->getEmail(),$_POST['email'])==0) {
				$existe=True;
				$_SESSION['usuario_id']=$usuario->getId();
				$_SESSION['usuario_nombre']=$usuario->getNombres();
				$_SESSION['usuario_rol']=$usuario->getRol();

			}
		}
		if ($existe) {
			$_SESSION['usuario']=True;//inicio de sesion de usuario				
			//require_once('Views/Layouts/layout.php');
			header('Location: index.php');
		}else{
			$_SESSION['mensaje']='Email o contraseña invalidos';
			$this->showLogin();
		}
	}

	public function logout() {
		unset($_SESSION['usuario']);
		unset($_SESSION['usuario_id']);
		unset($_SESSION['usuario_name']);
		unset($_SESSION['usuario_rol']);
		echo "<script type='text/javascript'>window.location.href = 'index.php';</script>"; 
	}

	public function validarCedula(){
		// fuerzo parametro de entrada a string
		$retorno="";
        $numero = $_POST['cedula'];
        //var_dump($numero);
        //die();
        // borro por si acaso errores de llamadas anteriores.
        //$this->setError('');
        // validaciones
        
            //$this->validarInicial($numero, '10');
           // $this->validarCodigoProvincia(substr($numero, 0, 2));
            //$this->validarTercerDigito($numero[2], 'cedula');
            $this->algoritmoModulo10(substr($numero, 0, 9), $numero[9]);
            $retorno='SI';
        
        $datos = array('estado' => 'ok','nombre' => $nombre, 'apellido' => $apellido, 'edad' => $edad);
        echo  json_encode($datos, true);
	}

	public function algoritmoModulo10($digitosIniciales, $digitoVerificador)
    {
        $arrayCoeficientes = array(2,1,2,1,2,1,2,1,2);
        $digitoVerificador = (int)$digitoVerificador;
        $digitosIniciales = str_split($digitosIniciales);
        $total = 0;
        foreach ($digitosIniciales as $key => $value) {
            $valorPosicion = ( (int)$value * $arrayCoeficientes[$key] );
            if ($valorPosicion >= 10) {
                $valorPosicion = str_split($valorPosicion);
                $valorPosicion = array_sum($valorPosicion);
                $valorPosicion = (int)$valorPosicion;
            }
            $total = $total + $valorPosicion;
        }
        $residuo =  $total % 10;
        if ($residuo == 0) {
            $resultado = 0;
        } else {
            $resultado = 10 - $residuo;
        }
        if ($resultado != $digitoVerificador) {
            //return false;
            //throw new Exception('Dígitos iniciales no validan contra Dígito Idenficador');
        }
        return true;
    }

    public function setError($newError)
    {
        $this->error = $newError;
        return $this;
    }
}