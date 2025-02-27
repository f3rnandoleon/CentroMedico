<?php 

class HistoClinica
{
	private $id;
	private $fregistro;
	private $numero;
	private $motivo;
	private $diagnostico;
	private $recomendacion;
	private $paciente;
	
	function __construct($id, $fregistro, $numero, $motivo, $diagnostico, $recomendacion,  $paciente)
	{
		$this->setId($id);
		$this->setFregistro($fregistro);
		$this->setNumero($numero);
		$this->setMotivo($motivo);
		$this->setDiagnostico($diagnostico);
		$this->setRecomendacion($recomendacion);
		$this->setPaciente($paciente);
	}

	/***FUNCIONES Getters y Setters***/
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getFregistro(){
		return $this->fregistro;
	}

	public function setFregistro($fregistro){
		$this->fregistro = $fregistro;
	}

	public function getNumero(){
		return $this->numero;
	}

	public function setNumero($numero){
		$this->numero = $numero;
	}
	
	public function getMotivo(){
		return $this->motivo;
	}

	public function setMotivo($motivo){
		$this->motivo = $motivo;
	}
	public function getDiagnostico(){
		return $this->diagnostico;
	}

	public function setDiagnostico($diagnostico){
		$this->diagnostico = $diagnostico;
	}
	public function getRecomendacion(){
		return $this->recomendacion;
	}

	public function setRecomendacion($recomendacion){
		$this->recomendacion = $recomendacion;
	}

	public function getPaciente(){
		return $this->paciente;
	}

	public function setPaciente($paciente){
		$this->paciente = $paciente;
	}

	/***FUNCIONES CRUD***/

	public static function save($histoclinica){
		$db=Db::getConnect();
		//var_dump($paciente);
		//die();
			
		$insert=$db->prepare('INSERT INTO histoclinicas VALUES(NULL,:fecha, :numero, :motivo, :diagnostico, :recomendacion, :paciente)');
		$insert->bindValue('fecha', $histoclinica->getFregistro());
		$insert->bindValue('numero', $histoclinica->getNumero());
		$insert->bindValue('motivo', $histoclinica->getMotivo());
		$insert->bindValue('diagnostico', $histoclinica->getDiagnostico());
		$insert->bindValue('recomendacion', $histoclinica->getRecomendacion());
		$insert->bindValue('paciente', $histoclinica->getPaciente());
		$insert->execute();
	}

	//función para obtener todas la historias clínicas
	public static function all(){
		$listaHistorias = [];
		$db = Db::getConnect();
		$sql = $db->query('SELECT * FROM histoclinicas ORDER BY id');
	
		// Carga en la $listaHistorias cada registro desde la base de datos
		foreach ($sql->fetchAll() as $historia) {
			// Crear un objeto HistoClinica con todos los atributos de la base de datos
			$listaHistorias[] = new HistoClinica(
				$historia['id'],
				$historia['fregistro'],
				$historia['numero'],
				$historia['motivo'],
				$historia['diagnostico'],
				$historia['recomendacion'],
				$historia['paciente']
			);
		}
	
		return $listaHistorias;
	}

	//la función para obtener una HC por el id del paciente
	public static function getByPaciente($idPaciente){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM histoclinicas WHERE paciente = :id');
		$select->bindParam('id', $idPaciente);
		$select->execute();
	
		// Fetch el registro de la base de datos
		$historiaDb = $select->fetch();
	
		// Crear un objeto HistoClinica con todos los atributos de la base de datos
		if ($historiaDb) {
			$historia = new HistoClinica(
				$historiaDb['id'],
				$historiaDb['fregistro'],
				$historiaDb['numero'],
				$historiaDb['motivo'],
				$historiaDb['diagnostico'],
				$historiaDb['recomendacion'],
				$historiaDb['paciente']
			);
			return $historia;
		}
		return null; // Retorna null si no se encontró ningún registro
	}

	//la función para obtener una HC por el numero
	public static function getByNumero($numero){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM histoclinicas WHERE numero = :numero');
		$select->bindParam('numero', $numero);
		$select->execute();
	
		// Fetch el registro de la base de datos
		$historiaDb = $select->fetch();
	
		// Crear un objeto HistoClinica con todos los atributos de la base de datos
		if ($historiaDb) {
			$historia = new HistoClinica(
				$historiaDb['id'],
				$historiaDb['fregistro'],
				$historiaDb['numero'],
				$historiaDb['motivo'],
				$historiaDb['diagnostico'],
				$historiaDb['recomendacion'],
				$historiaDb['paciente']
			);
			return $historia;
		}
		return null; // Retorna null si no se encontró ningún registro
	}
	public static function search($searchTerm) {
        $db = Db::getConnect();

        // Consulta para buscar en múltiples atributos
        $sql = "SELECT h.* FROM histoclinicas h 
                LEFT JOIN pacientes p ON h.paciente = p.id
                WHERE h.numero LIKE :searchTerm 
                OR p.nombres LIKE :searchTerm 
                OR p.apellidos LIKE :searchTerm";
        
        $query = $db->prepare($sql);
        $query->bindValue(':searchTerm', '%' . $searchTerm . '%');
        $query->execute();

        $historias = [];
        while ($row = $query->fetch()) {
            $historias[] = new HistoClinica(
                $row['id'], $row['fregistro'], $row['numero'], 
                $row['motivo'], $row['diagnostico'], $row['recomendacion'], $row['paciente']
            );
        }

        return $historias;
    }


	/***FUNCIONES CRUD ANTFAMILIAR***/
	public static function saveAntFamiliar($antFamiliar){
		$db=Db::getConnect();
		//ar_dump($paciente);
		//die();
			
		$insert=$db->prepare('INSERT INTO antfamiliares VALUES(NULL,:cardiopatia,:diabetes, :cancer, :enfcardiovasculares, :hipertension, :enfmentales, :tubercolosis, :enfinfecciosas,:malformacion, :otra, :descripcion, :paciente)');
		$insert->bindValue('cardiopatia',$antFamiliar->getCardiopatia());
		$insert->bindValue('diabetes',$antFamiliar->getDiabetes());
		$insert->bindValue('cancer',$antFamiliar->getCancer());
		$insert->bindValue('enfcardiovasculares',$antFamiliar->getEnfcardiovasculares());
		$insert->bindValue('hipertension',$antFamiliar->getHipertension());
		$insert->bindValue('enfmentales',$antFamiliar->getEnfmentales());
		$insert->bindValue('tubercolosis',$antFamiliar->getTubercolosis());
		$insert->bindValue('enfinfecciosas',$antFamiliar->getEnfinfecciosas());
		$insert->bindValue('malformacion',$antFamiliar->getMalformacion());
		$insert->bindValue('otra',$antFamiliar->getOtra());
		$insert->bindValue('descripcion',$antFamiliar->getDescripcion());
		$insert->bindValue('paciente',$antFamiliar->getPaciente());
		$insert->execute();
	}

	public static function getAntFamiliarByPaciente($idPaciente){
		$db=Db::getConnect();
		//ar_dump($paciente);
		//die();
		$select=$db->prepare('SELECT * FROM antfamiliares WHERE PACIENTE=:id');
		$select->bindParam('id',$idPaciente);
		$select->execute();

		$antFamiliarDb=$select->fetch();
		$antFamiliar= new AntFamiliar($antFamiliarDb['id'],$antFamiliarDb['cardiopatia'],$antFamiliarDb['diabetes'],$antFamiliarDb['cancer'], $antFamiliarDb['enfcardiovasculares'], $antFamiliarDb['hipertension'],$antFamiliarDb['enfmentales'],$antFamiliarDb['tubercolosis'],$antFamiliarDb['enfinfecciosas'], $antFamiliarDb['malformacion'],$antFamiliarDb['otra'], $antFamiliarDb['descripcion'],$antFamiliarDb['paciente']);
		return $antFamiliar;
	}


	//la función para actualizar las enfermedades familiares
	public static function updateAntFamiliar($antFamiliar){
		//var_dump($historia);
		//die();
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE antfamiliares SET cardiopatia=:cardiopatia, diabetes=:diabetes,cancer=:cancer, enfcardiovasculares=:enfcardiovasculares, hipertension=:hipertension,enfmentales=:enfmentales, tubercolosis=:tubercolosis, enfinfecciosas=:enfinfecciosas, malformacion=:malformacion, otra=:otra, descripcion=:descripcion,paciente=:paciente  WHERE id=:id');
		$update->bindValue('id',$antFamiliar->getId());
		$update->bindValue('cardiopatia',$antFamiliar->getCardiopatia());
		$update->bindValue('diabetes',$antFamiliar->getDiabetes());
		$update->bindValue('cancer',$antFamiliar->getCancer());
		$update->bindValue('enfcardiovasculares',$antFamiliar->getEnfcardiovasculares());
		$update->bindValue('hipertension',$antFamiliar->getHipertension());
		$update->bindValue('enfmentales',$antFamiliar->getEnfmentales());
		$update->bindValue('tubercolosis',$antFamiliar->getTubercolosis());
		$update->bindValue('enfinfecciosas',$antFamiliar->getEnfinfecciosas());
		$update->bindValue('malformacion',$antFamiliar->getMalformacion());
		$update->bindValue('otra',$antFamiliar->getOtra());
		$update->bindValue('descripcion',$antFamiliar->getDescripcion());
		$update->bindValue('paciente',$antFamiliar->getPaciente());
		$update->execute();
	}




	/***FUNCIONES CRUD ANTPERSONAL***/

	public static function saveAntPersonal($antPersonal){
		$db=Db::getConnect();
		//var_dump($antPersonal);
		//die();
			
		$insert=$db->prepare('INSERT INTO antpersonales VALUES(NULL,:imenarquia,:imenopausia, :vsexualactiva, :ciclos, :gesta, :partos, :abortos, :cesareas,:fum, :fup, :hvivos,:mpf,:descripcion, :paciente)');
		$insert->bindValue('imenarquia',$antPersonal->getImenarquia());
		$insert->bindValue('imenopausia',$antPersonal->getImenopausia());
		$insert->bindValue('vsexualactiva',$antPersonal->getVsexualactiva());
		$insert->bindValue('ciclos',$antPersonal->getCiclos());
		$insert->bindValue('gesta',$antPersonal->getGesta());
		$insert->bindValue('partos',$antPersonal->getPartos());
		$insert->bindValue('abortos',$antPersonal->getAbortos());
		$insert->bindValue('cesareas',$antPersonal->getCesareas());
		$insert->bindValue('fum',$antPersonal->getFum());
		$insert->bindValue('fup',$antPersonal->getFup());
		$insert->bindValue('hvivos',$antPersonal->getHvivos());
		$insert->bindValue('mpf',$antPersonal->getMpf());
		$insert->bindValue('descripcion',$antPersonal->getDescripcion());
		$insert->bindValue('paciente',$antPersonal->getPaciente());
		$insert->execute();
	}

	//obtener los antecedentes personales por el paciente
	public static function getAntPersonalByPaciente($idPaciente){
		$db=Db::getConnect();
		//ar_dump($paciente);
		//die();
		$select=$db->prepare('SELECT * FROM antpersonales WHERE PACIENTE=:id');
		$select->bindParam('id',$idPaciente);
		$select->execute();

		$antPersonalDb=$select->fetch();
		$antPersonal= new AntPersonal($antPersonalDb['id'],$antPersonalDb['imenarquia'],$antPersonalDb['imenopausia'],$antPersonalDb['vsexualactiva'], $antPersonalDb['ciclos'], $antPersonalDb['gesta'],$antPersonalDb['partos'],$antPersonalDb['abortos'],$antPersonalDb['cesareas'],$antPersonalDb['fum'], $antPersonalDb['fup'],$antPersonalDb['hvivos'],$antPersonalDb['mpf'], $antPersonalDb['descripcion'],$antPersonalDb['paciente']);
		return $antPersonal;
	}

	//la función para actualizar las enfermedades personales
	public static function updateAntPersonal($antPersonal){
		//var_dump($historia);
		//die();
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE antpersonales SET imenarquia=:imenarquia, imenopausia=:imenopausia,vsexualactiva=:vsexualactiva, ciclos=:ciclos, gesta=:gesta,partos=:partos, abortos=:abortos, cesareas=:cesareas, fum=:fum, fup=:fup, hvivos=:hvivos,mpf=:mpf,descripcion=:descripcion,paciente=:paciente  WHERE id=:id');
		$update->bindValue('id',$antPersonal->getId());
		$update->bindValue('imenarquia',$antPersonal->getImenarquia());
		$update->bindValue('imenopausia',$antPersonal->getImenopausia());
		$update->bindValue('vsexualactiva',$antPersonal->getVsexualactiva());
		$update->bindValue('ciclos',$antPersonal->getCiclos());
		$update->bindValue('gesta',$antPersonal->getGesta());
		$update->bindValue('partos',$antPersonal->getPartos());
		$update->bindValue('abortos',$antPersonal->getAbortos());
		$update->bindValue('cesareas',$antPersonal->getCesareas());
		$update->bindValue('fum',$antPersonal->getFum());
		$update->bindValue('fup',$antPersonal->getFup());
		$update->bindValue('hvivos',$antPersonal->getHvivos());
		$update->bindValue('mpf',$antPersonal->getMpf());
		$update->bindValue('descripcion',$antPersonal->getDescripcion());
		$update->bindValue('paciente',$antPersonal->getPaciente());
		$update->execute();
	}



	/***FUNCIONES CRUD EXAVISUAL***/
	public static function saveExaVisual($exaVisual){
		$db=Db::getConnect();
		//var_dump($antPersonal);
		//die();
			
		$insert=$db->prepare('INSERT INTO exavisuales VALUES(NULL,:descripcion, :paciente)');
		$insert->bindValue('descripcion',$exaVisual->getDescripcion());
		$insert->bindValue('paciente',$exaVisual->getPaciente());
		$insert->execute();
	}

	public static function getExaVisualByPaciente($idPaciente){
		$db=Db::getConnect();
		//ar_dump($paciente);
		//die();
		$select=$db->prepare('SELECT * FROM exavisuales WHERE PACIENTE=:id');
		$select->bindParam('id',$idPaciente);
		$select->execute();

		$exaVisualesDb=$select->fetch();
		$exaVisual= new exaVisual($exaVisualesDb['id'],$exaVisualesDb['descripcion'],$exaVisualesDb['paciente']);
		return $exaVisual;
	}

	//la función para actualizar los exámenes visuales
	public static function updateExaVisual($exaVisual){
		//var_dump($exaVisual);
		//die();
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE exavisuales SET descripcion=:descripcion,paciente=:paciente  WHERE id=:id');
		$update->bindValue('id',$exaVisual->getId());
		$update->bindValue('descripcion',$exaVisual->getDescripcion());
		$update->bindValue('paciente',$exaVisual->getPaciente());
		$update->execute();
	}






	/***FUNCIONES AUXILIARES HC***/
	//la función para obtener el valor max del id para el número de historia
	public static function getMaxId(){
		//buscar el max id de la tabla histoclinicas
		$db=Db::getConnect();
		$select=$db->prepare('SELECT MAX(id) AS id FROM histoclinicas');
		$select->execute();
		//asignarlo al objeto que obtiene el registro
		$histoDb=$select->fetch();
		$idMax= $histoDb['id'];
		return $idMax;
	}
}