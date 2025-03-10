<?php 

class Usuario
{
	private $id;
	private $nombres;
	private $apellidos;
	private $email;
	private $clave;
	private $pregunta;
	private $respuesta;
	private $fecha;

	function __construct($id, $nombres, $apellidos, $email,$clave, $pregunta, $respuesta,$fecha)
	{
		$this->setId($id);
		$this->setNombres($nombres);
		$this->setApellidos($apellidos);
		$this->setEmail($email);
		$this->setClave($clave);
		$this->setPregunta($pregunta);
		$this->setRespuesta($respuesta);
		$this->setFecha($fecha);
	}


	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getFecha(){
		return $this->fecha;
	}

	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	public function getNombres(){
		return $this->nombres;
	}

	public function setNombres($nombres){
		$this->nombres = $nombres;
	}

	public function getApellidos(){
		return $this->apellidos;
	}

	public function setApellidos($apellidos){
		$this->apellidos = $apellidos;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getClave(){
		return $this->clave;
	}

	public function setClave($clave){
		$this->clave = $clave;
	}

	public function getPregunta(){
		return $this->pregunta;
	}

	public function setPregunta($pregunta){
		$this->pregunta = $pregunta;
	}

	public function getRespuesta(){
		return $this->respuesta;
	}

	public function setRespuesta($respuesta){
		$this->respuesta = $respuesta;
	}


	//opciones CRUD

	//función para obtener todos los usuarios
	public static function all(){
		$listaUsuarios =[];
		$db=Db::getConnect();
		$sql=$db->query('SELECT * FROM usuarios');

		// carga en la $listaUsuarios cada registro desde la base de datos
		foreach ($sql->fetchAll() as $usuario) {
			$listaUsuarios[]= new Usuario($usuario['id'], $usuario['nombres'],$usuario['apellidos'],$usuario['email'], $usuario['clave'], $usuario['respuesta'], $usuario['pregunta'],$usuario['fecha']);
		}
		return $listaUsuarios;
	}

	//la función para registrar un usuario
	public static function save($usuario){
		$db=Db::getConnect();
			
		$insert = $db->prepare('INSERT INTO USUARIOS (id, nombres, apellidos, email, clave, pregunta, respuesta, fecha)
        VALUES (NULL, :nombres, :apellidos, :email, :clave, :pregunta, :respuesta, :fecha)
    ');

		$insert->bindValue('nombres',$usuario->getNombres());
		$insert->bindValue('apellidos',$usuario->getApellidos());
		$insert->bindValue('email',$usuario->getEmail());
		//encripta la clave
		$pass=password_hash($usuario->getClave(),PASSWORD_DEFAULT);
		//var_dump($pass);
		//die();
		$insert->bindValue('clave',$pass);
		$insert->bindValue('pregunta',$usuario->getPregunta());
		$insert->bindValue('respuesta',$usuario->getRespuesta());
		$insert->bindValue('fecha',$usuario->getFecha());
		$insert->execute();
	}

	//la función para actualizar 
	public static function update($usuario){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE usuarios SET nombres=:nombres WHERE id=:id');
		$update->bindValue('id',$usuario->id);
		$update->bindValue('nombres',$usuario->nombres);
		$update->execute();
	}

	// la función para eliminar por el id
	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE FROM usuarios WHERE ID=:id');
		$delete->bindValue('id',$id);
		$delete->execute();
	}

	//la función para obtener un usuario por el id
	public static function getById($id){
		//buscar
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM usuarios WHERE ID=:id');
		$select->bindValue('id',$id);
		$select->execute();
		//asignarlo al objeto usuario
		$usuarioDb=$select->fetch();
		$usuario= new Usuario($usuarioDb['id'],$usuarioDb['fecha'],$usuarioDb['nombres'],$usuarioDb['apellidos'],$usuarioDb['email'], $usuarioDb['clave'],$usuarioDb['pregunta'],$usuarioDb['respuesta']);
		//var_dump($usuario);
		//die();
		return $usuario;
	}
}