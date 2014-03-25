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