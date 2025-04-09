<?php 

class Usuario
{
	private $id;
	private $nombres;
	private $apellidos;
	private $email;
	private $clave;
	private $rol;
	private $fecha;

	function __construct($id, $nombres, $apellidos, $email,$clave, $rol,$fecha)
	{
		$this->setId($id);
		$this->setNombres($nombres);
		$this->setApellidos($apellidos);
		$this->setEmail($email);
		$this->setClave($clave);
		$this->setRol($rol);
		$this->setFecha($fecha);
	}


	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
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

	public function getRol(){
		return $this->rol;
	}

	public function setRol($rol){
		$this->rol = $rol;
	}
	public function getFecha(){
		return $this->fecha;
	}

	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	//opciones CRUD

	//función para obtener todos los usuarios
	public static function all(){
		$listaUsuarios =[];
		$db=Db::getConnect();
		$sql=$db->query('SELECT * FROM usuarios ');

		// carga en la $listaUsuarios cada registro desde la base de datos
		foreach ($sql->fetchAll() as $usuario) {
			$listaUsuarios[]= new Usuario($usuario['id'], $usuario['nombres'],$usuario['apellidos'],$usuario['email'], $usuario['clave'], $usuario['rol'],$usuario['fecha']);
		}
		return $listaUsuarios;
	}

	//la función para registrar un usuario
	public static function save($usuario){
		$db=Db::getConnect();
			
		$insert = $db->prepare('INSERT INTO USUARIOS (id, nombres, apellidos, email, clave, rol, fecha)
        VALUES (NULL, :nombres, :apellidos, :email, :clave, :rol, :fecha)
    ');

		$insert->bindValue('nombres',$usuario->getNombres());
		$insert->bindValue('apellidos',$usuario->getApellidos());
		$insert->bindValue('email',$usuario->getEmail());
		//encripta la clave
		$pass=password_hash($usuario->getClave(),PASSWORD_DEFAULT);
		//var_dump($pass);
		//die();
		$insert->bindValue('clave',$pass);
		$insert->bindValue('rol',$usuario->getRol());
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
	public static function updatesinclave($usuario) {
		$db = Db::getConnect();
	
		// Preparamos la consulta SQL para actualizar los campos de la tabla usuarios
		$update = $db->prepare('
			UPDATE usuarios SET
				nombres = :nombres,
				apellidos = :apellidos,
				email = :email,
				rol = :rol,
				fecha = :fecha
			WHERE id = :id
		');
	
		// Asociamos los parámetros con los valores del objeto Usuario
		$update->bindValue('id',       $usuario->getId());
		$update->bindValue('nombres',  $usuario->getNombres());
		$update->bindValue('apellidos',$usuario->getApellidos());
		$update->bindValue('email',    $usuario->getEmail());
		$update->bindValue('rol',      $usuario->getRol());
		$update->bindValue('fecha',    $usuario->getFecha());
	
		// Ejecutamos la consulta
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
		$usuario= new Usuario($usuarioDb['id'],$usuarioDb['nombres'],$usuarioDb['apellidos'],$usuarioDb['email'], $usuarioDb['clave'],$usuarioDb['rol'],$usuarioDb['fecha']);
		//var_dump($usuario);
		//die();
		return $usuario;
	}
	public static function search($searchTerm) {
		$db = Db::getConnect();  // Suponiendo que tienes un método Db::getConnect() para obtener la conexión
	
		// Prepara la consulta con varios campos para filtrar
		$sql = "SELECT * FROM usuarios 
				WHERE nombres   LIKE :searchTerm 
				   OR apellidos LIKE :searchTerm 
				   OR email     LIKE :searchTerm
				   OR rol       LIKE :searchTerm";
	
		// Ejecutar la consulta
		$query = $db->prepare($sql);
		$query->bindValue(':searchTerm', '%' . $searchTerm . '%');
		$query->execute();
	
		// Arreglo para los resultados
		$usuarios = [];
		
		// Crear objetos Usuario por cada fila
		while ($row = $query->fetch()) {
			$usuarios[] = new Usuario(
				$row['id'],
				$row['nombres'],
				$row['apellidos'],
				$row['email'],
				$row['clave'],
				$row['rol'],
				$row['fecha']
			);
		}
	
		return $usuarios;
	}
	
	
}