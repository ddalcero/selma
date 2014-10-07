<?php

class Salario {

	// TO-DO: put into a database these values
	private $valores=array(
		'uf'=>22852.67,
		'utm'=>40085,
		'horas'=>1998,
		'tope_afp'=>70.3,
		'tope_afc'=>105.4,
		'salud'=>0.07,
		'cesantia'=>0.006,
		'minimo'=>193000,
		'indefinido'=>0.024,
		'plazofijo'=>0.03,
		'gratificacion'=>76396, // 4,75 * minimo / 12
		'mutual'=>0.0095,
		'sisempleador'=>0.0149,
		'seguro_comp'=>0.859,
	);

	private $plazos=array(
		'indefinido'=>'Indefinido',
		'plazofijo'=>'Plazo fijo'
	);

	private $valores_afp=array(
		'ING Capital'=>0.1144,
		'Cuprum'=>0.1148,
		'Habitat'=>0.1127,
		'PlanVital'=>0.1236,
		'Provida'=>0.1154,
		'Modelo'=>0.1077,
	);

	// franjas impuesto unico
	private $impuesto=array(
		array(0,13.5,0.000,0),
		array(13.5,30,0.040,0.54),
		array(30,50,0.080,1.74),
		array(50,70,0.135,4.49),
		array(70,90,0.230,11.14),
		array(90,120,0.304,17.8),
		array(120,150,0.355,23.92),
		array(150,99999,0.400,30.67)
	);

	public $data=array(
		'afp'=>'',
		'plazo'=>'',
		'liquido'=>0,
		'base'=>0,
		'imponible'=>0,
		'gratificacion'=>0,
		'afp_total'=>0,
		'salud'=>0,
		'cesantia'=>0,
		'desc_prev'=>0,
		'tributable'=>0,
		'impuesto_unico'=>0,
		'colacion'=>0,
		'movilizacion'=>32000,
		'no_imponibles'=>0,
		'total_descuentos'=>0,
		'total_haberes'=>0,
		'tickets'=>72450,
		'tickets_dia'=>3450,
		'mutual'=>0,
		'cesantia_emp'=>0,
		'sisempleador'=>0,
		'aguinaldo'=>8333,
		'seguro_comp'=>0,
		'coste_empresa'=>0,
		'coste_olga'=>0,
		'provisiones'=>0,
		'puesto'=>0,
	);

	public function recalculo() {
		$uf=$this->valores['uf'];
		$utm=$this->valores['utm'];
		// gratificación legal
		$base4=round($this->base/4);
		$this->gratificacion=min($this->valores['gratificacion'],$base4);
		// imponible
		$this->imponible=$this->base+$this->gratificacion;
		$imponible_uf=$this->imponible/$uf;
		// topes
		$tope_afp=min($imponible_uf,$this->valores['tope_afp']);
		$tope_afc=min($imponible_uf,$this->valores['tope_afc']);
		// afp
		$this->afp_total=round($tope_afp * $this->valores_afp[$this->afp] * $uf);
		// salud
		$this->salud=round($tope_afp * $this->valores['salud'] * $uf);
		// cesantia
		if ($this->plazo=='indefinido')
			$this->cesantia=round($tope_afc * $this->valores['cesantia'] * $uf);
		else $this->cesantia=0;
		// descuentos y tributable
		$this->desc_prev=$this->afp_total + $this->salud + $this->cesantia;
		$this->tributable=$this->imponible - $this->desc_prev;
		// mutual
		$this->mutual=round($tope_afp * $this->valores['mutual'] * $uf);
		// cesantia empresa
		$this->cesantia_emp=round($tope_afc * $this->valores[$this->plazo] * $uf);
		// sis_empleador
		$this->sisempleador=round($tope_afc * $this->valores['sisempleador'] * $uf);
		// no_imponibles
		$this->no_imponibles=$this->colacion + $this->movilizacion;
		// impuesto unico
		$tributable_utm=$this->tributable/$utm;
		foreach ($this->impuesto as $imp) {
			if ($tributable_utm >= $imp[0] && $tributable_utm < $imp[1]) {
				$this->impuesto_unico=round((($tributable_utm * $imp[2])-$imp[3])*$utm);
				break;
			}
		}
		// total_haberes
		$this->total_haberes=$this->imponible + $this->no_imponibles;
		// total_descuentos
		$this->total_descuentos=$this->desc_prev + $this->impuesto_unico;
		// liquido
		$this->liquido=$this->total_haberes - $this->total_descuentos;
		// coste_empresa
		$this->coste_empresa=$this->total_haberes + $this->sis_empleador + $this->mutual + $this->cesantia_emp;
		// seguro complementario
		$this->seguro_comp=$uf * $this->valores['seguro_comp'];
		// provisiones
		$this->provisiones=$this->total_haberes / 12;
		// tickets
		$this->tickets=$this->tickets_dia * 21;
		// coste_olga
		$this->coste_olga=$this->coste_empresa + $this->tickets + $this->seguro_comp + $this->aguinaldo;
	}

	public function afps() {
		foreach ($this->valores_afp as $key=>$value) $afps[$key]=$key;
		return $afps;
	}

	public function plazos() {
		return $this->plazos;
	}

	public function calcula_base($liquido) {
		$this->base=$liquido;
		$this->recalculo();
		//echo "Paso 1: Base = ".$this->base." - liquido = ".$liquido."\n";
		$iter=1;
		while ($this->liquido <> $liquido && $iter<30) {
			$delta=$liquido - $this->liquido;
			$iter++;
			$this->base += $delta;
			$this->recalculo();
			//echo "Paso ".$iter.": Base = ".$this->base." - liquido = ".$this->liquido." - delta = ".$delta."\n";
		}
	}

	public function __construct($attributes=array()) {
		$this->fill($attributes);
		if ($this->base<>0) {
			$this->recalculo();
		}
		else if ($this->liquido<>0) {
			$this->calcula_base($this->liquido);
		}
	}

	public function  __get($name) {
		// check if the named key exists in our ar
		if(array_key_exists($name, $this->data)) {
			// then return the value from the array
			return $this->data[$name];
		}
		return null;
	}

	public function  __set($name, $value) {
		// use the property name as the array key
		$this->data[$name] = $value;
	}
  
	public function  __isset($name) {
		// you could also use isset() here
		return array_key_exists($name, $this->data);
	}

	public function  __unset($name) {
		// forward the unset() to our array element
		unset($this->data[$name]);
	}

	/**
	 * Hydrate the model with an array of attributes.
	 *
	 * @param  array  $attributes
	 * @return Salario
	 */
	public function fill(array $attributes)
	{
		foreach ($attributes as $key => $value) {
			$this->$key = $value;
		}
		return $this;
	}

	public function to_array(){
		return $this->data;
	}

	public function dumpValues() {
		return array($this->data,$this->valores,$this->afp,$this->impuesto);
	}

}