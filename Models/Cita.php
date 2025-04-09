<?php

class Cita
{
    private $id;
    private $paciente;
    private $fecha;
    private $hora;
    private $motivo;
    private $estado;
    private $observaciones;
    private $fcreado_en;

    function __construct($id, $paciente, $fecha, $hora, $motivo, $estado, $observaciones, $fcreado_en)
    {
        $this->setId($id);
        $this->setPaciente($paciente);
        $this->setFecha($fecha);
        $this->setHora($hora);
        $this->setMotivo($motivo);
        $this->setEstado($estado);
        $this->setObservaciones($observaciones);
        $this->setFcreadoEn($fcreado_en);
    }

    /*** GETTERS Y SETTERS ***/
    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id = $id;
    }

    public function getPaciente(){
        return $this->paciente;
    }
    public function setPaciente($paciente){
        $this->paciente = $paciente;
    }

    public function getFecha(){
        return $this->fecha;
    }
    public function setFecha($fecha){
        $this->fecha = $fecha;
    }

    public function getHora(){
        return $this->hora;
    }
    public function setHora($hora){
        $this->hora = $hora;
    }

    public function getMotivo(){
        return $this->motivo;
    }
    public function setMotivo($motivo){
        $this->motivo = $motivo;
    }

    public function getEstado(){
        return $this->estado;
    }
    public function setEstado($estado){
        $this->estado = $estado;
    }

    public function getObservaciones(){
        return $this->observaciones;
    }
    public function setObservaciones($observaciones){
        $this->observaciones = $observaciones;
    }

    public function getFcreadoEn(){
        return $this->fcreado_en;
    }
    public function setFcreadoEn($fcreado_en){
        $this->fcreado_en = $fcreado_en;
    }

    /*** FUNCIONES CRUD ***/

    // Inserta una nueva cita (el campo id se autogenera y fcreado_en se asigna por la base de datos)
    public static function save($cita) {
        $db = Db::getConnect();
        $insert = $db->prepare('
            INSERT INTO citas (paciente, fecha, hora, motivo, estado, observaciones) 
            VALUES (:paciente, :fecha, :hora, :motivo, :estado, :observaciones)
        ');
        $insert->bindValue('paciente', $cita->getPaciente());
        $insert->bindValue('fecha', $cita->getFecha());
        $insert->bindValue('hora', $cita->getHora());
        $insert->bindValue('motivo', $cita->getMotivo());
        $insert->bindValue('estado', $cita->getEstado());
        $insert->bindValue('observaciones', $cita->getObservaciones());
        $insert->execute();
    }

    // Actualiza una cita existente (no se modifica la fecha de creación)
    public static function update($cita) {
        $db = Db::getConnect();
        $update = $db->prepare('
            UPDATE citas 
            SET paciente = :paciente, fecha = :fecha, hora = :hora, motivo = :motivo, estado = :estado, observaciones = :observaciones
            WHERE id = :id
        ');
        $update->bindValue('id', $cita->getId());
        $update->bindValue('paciente', $cita->getPaciente());
        $update->bindValue('fecha', $cita->getFecha());
        $update->bindValue('hora', $cita->getHora());
        $update->bindValue('motivo', $cita->getMotivo());
        $update->bindValue('estado', $cita->getEstado());
        $update->bindValue('observaciones', $cita->getObservaciones());
        $update->execute();
    }

    // Retorna todas las citas
    public static function all() {
        $listaCitas = [];
        $db = Db::getConnect();
        $sql = $db->query('SELECT * FROM citas ORDER BY id');
        foreach ($sql->fetchAll() as $row) {
            $listaCitas[] = new Cita(
                $row['id'],
                $row['paciente'],
                $row['fecha'],
                $row['hora'],
                $row['motivo'],
                $row['estado'],
                $row['observaciones'],
                $row['fcreado_en']
            );
        }
        return $listaCitas;
    }

    // Retorna las citas de un paciente en específico
    public static function getAllByPaciente($idPaciente) {
        $listaCitas = [];
        $db = Db::getConnect();
        $select = $db->prepare('SELECT * FROM citas WHERE paciente = :id');
        $select->bindParam('id', $idPaciente);
        $select->execute();
        foreach ($select->fetchAll() as $row) {
            $listaCitas[] = new Cita(
                $row['id'],
                $row['paciente'],
                $row['fecha'],
                $row['hora'],
                $row['motivo'],
                $row['estado'],
                $row['observaciones'],
                $row['fcreado_en']
            );
        }
        return $listaCitas;
    }

    // Retorna una cita por su id
    public static function getBy($id) {
        $db = Db::getConnect();
        $select = $db->prepare('SELECT * FROM citas WHERE id = :id');
        $select->bindParam('id', $id);
        $select->execute();
        $row = $select->fetch();
        if ($row) {
            return new Cita(
                $row['id'],
                $row['paciente'],
                $row['fecha'],
                $row['hora'],
                $row['motivo'],
                $row['estado'],
                $row['observaciones'],
                $row['fcreado_en']
            );
        }
        return null;
    }

    // Busca citas según un término. Se buscan coincidencias en el motivo
    // y a través del join con la tabla de pacientes por nombres o apellidos.
    public static function search($searchTerm) {
        $db = Db::getConnect();
        $sql = "SELECT c.* FROM citas c 
                LEFT JOIN pacientes p ON c.paciente = p.id
                WHERE c.motivo LIKE :searchTerm 
                   OR p.nombres LIKE :searchTerm 
                   OR p.apellidos LIKE :searchTerm
                ORDER BY c.id";
        $query = $db->prepare($sql);
        $query->bindValue(':searchTerm', '%' . $searchTerm . '%');
        $query->execute();
        $listaCitas = [];
        while ($row = $query->fetch()) {
            $listaCitas[] = new Cita(
                $row['id'],
                $row['paciente'],
                $row['fecha'],
                $row['hora'],
                $row['motivo'],
                $row['estado'],
                $row['observaciones'],
                $row['fcreado_en']
            );
        }
        return $listaCitas;
    }

    // Función auxiliar para obtener el total de citas registradas
    public static function getCount(){
        $db = Db::getConnect();
        $sql = $db->query("SELECT COUNT(*) as total FROM citas");
        $row = $sql->fetch();
        return $row['total'];
    }
}
