<?php
class VOID_ORDER_VIEW {
	public $type;
	public $delta_x;
	public $delta_y;
	
}

class VOID_ORDER {
	public $type;
	public $delta_x;
	public $delta_z;
	public $owner;
	public $x;
	public $z;
	public $planet_id;
	public $upgrade_id;
	public $fleet_id;
	public $ship_id;
	public $from_x;
	public $from_z;
	public $to_x;
	public $to_z;
	
	function __construct($type){
		$this->type = $type;
	}
	
	function add_params($params){
		if (isset($params['x'])){
			$this->x = $params['x'];
		}
		if (isset($params['z'])){
			$this->z = $params['z'];
		}
		if (isset($params['planet_id'])){
			$this->planet_id = $params['planet_id'];
		}
		if (isset($params['upgrade_id'])){
			$this->upgrade_id = $params['upgrade_id'];
		}
		if (isset($params['fleet_id'])){
			$this->fleet_id = $params['fleet_id'];
		}
		if (isset($params['ship_id'])){
			$this->ship_id = $params['ship_id'];
		}
		if (isset($params['from_x'])){
			$this->from_x = $params['from_x'];
		}
		if (isset($params['from_z'])){
			$this->from_z = $params['from_z'];
		}
		if (isset($params['to_x'])){
			$this->to_x = $params['to_x'];
		}
		if (isset($params['to_z'])){
			$this->to_z = $params['to_z'];
		}
	}
	
	public function dump_view($player_id){
		if ($this->owner == $player_id){
			return new VOID_ORDER_VIEW($this, $player_id);
		}else {
			return false;
		}
	}
	
	
	
}


?>