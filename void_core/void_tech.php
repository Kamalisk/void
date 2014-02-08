<?


class VOID_POWER {
	public $name;
	public $modifiers;
	
	function __construct(){
		
	}
}


class VOID_TECH_TREE {
	
	public $items;
	
	function __construct(){
		$this->items = array();
	}
	
	public function add_tech($tech){
		$this->items[$tech->id] = $tech;
		// maybe add indexes to the various techs it links to or something
	}
	
	public function get_starting_tech(){
		foreach($this->items as &$item){
			if ($item->get_req_count() <= 0){
				$array[] = $item;
			}
		}
		return $array;
	}
	
	private function calculate_tier(){
		// run through all tech and add back links
		foreach($this->items as $item){
			if ($reqs = $item->get_reqs()){
				foreach($reqs as $req){
					$this->items[$req]->add_lead($item->id);
				}
			}
		}
		
		// run through the tech tree and calculate the tier of techs
		$tech_pool = $this->get_starting_tech();
		
		$tier = 1;
		
		while (count($tech_pool) > 0){
			$new_tech = array();
			foreach($tech_pool as &$tech){
				$tech->set_tier($tier);
				
				if ($leads = $tech->get_leads()){
					foreach($leads as $lead){
						$new_tech[] = $this->items[$lead];
					}
				}
				
			}
			$tech_pool = $new_tech;
			$tier++;
		}
	}
	
	public function init(){
		$tech = new VOID_TECH(1, "Space Flight", 10);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(2, "Geothermal Frakking", 30);
		$tech->add_req(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(3, "Xenobiology", 30);
		$tech->add_req(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(4, "Interstellar Trade", 30);
		$tech->add_req(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(5, "Subspace Communications", 30);
		$tech->add_req(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(6, "Lukas String Theory", 30);
		$tech->add_req(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(7, "Galactic Currency", 200);
		$tech->add_req(4);
		$tech->add_req(5);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(8, "Organic Manufacturing", 200);
		$tech->add_req(3);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(9, "Laser Amplification", 200);
		$tech->add_req(2);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(10, "Gravitational Mapping", 200);
		$tech->add_req(5);
		$tech->add_req(6);
		$this->add_tech($tech);
		
		$this->calculate_tier();
	}
	
	public function get_tech($id){
		return $this->items[$id];
	}
	
	public function dump(){
		return $this;
	}
	
}

class VOID_TECH {
	public $id;
	public $name;
	public $cost;
	public $progress;
	public $requirements;
	public $leads;
	public $tier;
	
	public $ship_classes;
	public $structure_classes;
	
	function __construct($id, $name, $cost){
		$this->id = $id;
		$this->name = $name;
		$this->requirements = array();
		$this->leads = array();
		$this->progress = $cost;
		$this->cost = $cost;
		$this->ship_classes = [];	
		$this->structure_classes = [];	
	}
	public function add_req($id){
		$this->requirements[$id] = $id;
	}
	public function add_lead($id){
		$this->leads[$id] = $id;
	}
	public function get_req_count(){
		return count($this->requirements);
	}
	public function get_reqs(){
		if (count($this->requirements) <= 0){
			return false;
		}
		return $this->requirements;
	}
	public function get_leads(){
		if (count($this->leads) > 0){
			return $this->leads;
		}
		return false;
	}
	public function set_tier($tier){
		$this->tier = $tier;
	}
	public function add_ship_class($ship_class){
		$this->ship_classes[] = $ship_class;
	}
	public function add_structure_class($structure_class){
		$this->structure_classes[] = $structure_class;
	}
}

class VOID_TECH_ITEM {
	public $progress;
	public $cost;
	public $class;
	
	function __construct($tech){
		$this->progress = $tech->cost;		
		$this->class = $tech;
		$this->cost = $tech->cost;
	}
	
	
}


?>