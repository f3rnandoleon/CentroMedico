<?php

class ExaComplementario
{
	private $id;
	private $fecha;
	private $descripcion;
	private $paciente;
	
	function __construct($id,$fecha, $descripcion, $paciente)
	{
		$this->setId($id);
		$this->setFecha($fecha);
		$this->setDescripcion($descripcion);		
		$this->setPaciente($paciente);
	}

	//Getters y Setters
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

	public function getDescripcion(){
		return $this->descripcion;
	}

	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	public function getPaciente(){
		return $this->paciente;
	}

	public function setPaciente($paciente){
		$this->paciente = $paciente;
	}


	//opciones CRUD
}
