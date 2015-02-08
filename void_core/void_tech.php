<?


class VOID_POWER_CLASS {
	public $name;
	public $id;
	public $modifiers;
	public $description;
	public $value;
	public $type;
	
	function __construct($id, $name){
		$this->id = $id;
		$this->name = $name;
		$this->modifiers = [];
	}
	
	function set_modifier($type, $category, $value, $max=0, $scope="player"){
		$modifier = new VOID_MODIFIER($type, $category, $value, $max);
		$modifier->set_scope($scope);
		$this->modifiers[$modifier->get_modifier_id()] = $modifier;
	}

	public function apply($player){
		foreach($this->modifiers as $type => $modifier){
			$modifier->apply($player, "player");
		}
		VOID_DEBUG::write("power!");
		switch($this->type){
			case "research":{
				$player->research->add_percent($this->value, "power");
				VOID_DEBUG::write("research race");
				break;
			}
			case "credits":{
				$player->credits->add_percent($this->value, "power");					
				break;
			}
			case "production":{
				$player->production->add_percent($this->value, "power");	
				break;
			}
			case "food":{
				$player->food->add_percent($this->value, "power");	
				break;
			}
			case "colonise_planet":{
				
				break;
			}
		}
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
		foreach($this->items as $item){
			if ($item->tier == 0){
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
		
		// starting tech
		$tech = new VOID_TECH(1, "Space Flight", 10);
		$tech->set_tier(0);
		$this->add_tech($tech);
		
		
		$tech = new VOID_TECH(2, "Laser Amplification", 40);
		$tech->add_trait("military");
		$tech->set_tier(1);
		$this->add_tech($tech);				
		
		$tech = new VOID_TECH(3, "Xenobiology", 40);
		$tech->add_trait("agriculture");
		$tech->set_tier(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(4, "Lukas String Theory", 40);
		$tech->add_trait("science");
		$tech->set_tier(1);
		$this->add_tech($tech);	
				
		$tech = new VOID_TECH(5, "Galactic Currency", 40);
		$tech->add_trait("commerce");		
		$tech->set_tier(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(6, "Geothermal Frakking", 40);
		$tech->add_trait("industry");
		$tech->set_tier(1);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(7, "Interstellar Exchange Program", 40);
		$tech->add_trait("culture");
		$tech->set_tier(1);
		$this->add_tech($tech);
		
		
		
		$tech = new VOID_TECH(8, "Subspace Refraction", 800);
		$tech->require_trait("military");
		$tech->add_trait("science");
		$tech->set_tier(2);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(9, "Organic Replication", 800);		
		$tech->require_trait("agriculture");
		$tech->add_trait("commerce");		
		$tech->set_tier(2);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(10, "", 800);
		$tech->require_trait("science");
		$tech->add_trait("industry");
		$tech->set_tier(2);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(11, "Speculative Diplomacy", 800);
		$tech->require_trait("commerce");
		$tech->add_trait("culture");
		$tech->set_tier(2);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(12, "", 800);
		$tech->require_trait("industry");
		$tech->add_trait("military");
		$tech->set_tier(2);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(13, "", 800);
		$tech->require_trait("culture");
		$tech->add_trait("agriculture");
		$tech->set_tier(2);
		$this->add_tech($tech);
		
		
		
		$tech = new VOID_TECH(14, "", 1200);
		$tech->require_trait("military");
		$tech->require_trait("science");
		$tech->add_trait("industry");
		$tech->set_tier(3);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(15, "", 1200);		
		$tech->require_trait("agriculture");
		$tech->require_trait("commerce");
		$tech->add_trait("culture");		
		$tech->set_tier(3);
		$this->add_tech($tech);		
		
		$tech = new VOID_TECH(16, "", 1200);
		$tech->require_trait("science");
		$tech->require_trait("industry");
		$tech->add_trait("military");
		$tech->set_tier(3);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(17, "", 1200);
		$tech->require_trait("commerce");
		$tech->require_trait("culture");
		$tech->add_trait("agriculture");
		$tech->set_tier(3);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(18, "", 1200);
		$tech->require_trait("industry");
		$tech->require_trait("military");
		$tech->add_trait("science");
		$tech->set_tier(3);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(19, "", 1200);
		$tech->require_trait("culture");
		$tech->require_trait("agriculture");
		$tech->add_trait("commerce");
		$tech->set_tier(3);
		$this->add_tech($tech);
		
		
		
		$tech = new VOID_TECH(20, "", 1600);
		$tech->require_trait("military",2);
		$tech->require_trait("industry",2);
		$tech->add_trait("military");
		$tech->set_tier(4);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(21, "", 1600);		
		$tech->require_trait("agriculture",2);
		$tech->require_trait("science",2);
		$tech->add_trait("agriculture");		
		$tech->set_tier(4);
		$this->add_tech($tech);		
		
		$tech = new VOID_TECH(22, "", 1600);
		$tech->require_trait("science",2);
		$tech->require_trait("culture",2);
		$tech->add_trait("science");
		$tech->set_tier(4);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(23, "", 1600);
		$tech->require_trait("commerce",2);
		$tech->require_trait("military",2);
		$tech->add_trait("commerce");
		$tech->set_tier(4);
		$this->add_tech($tech);
				
		$tech = new VOID_TECH(24, "", 1600);
		$tech->require_trait("industry",2);
		$tech->require_trait("commerce",2);
		$tech->add_trait("industry");
		$tech->set_tier(4);
		$this->add_tech($tech);
		
		$tech = new VOID_TECH(25, "", 1600);
		$tech->require_trait("culture",2);
		$tech->require_trait("agriculture",2);
		$tech->add_trait("culture");
		$tech->set_tier(4);
		$this->add_tech($tech);

		
		//$this->calculate_tier();
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
	public $power_classes;
	public $upgrade_classes;
	
	public $traits_required;
	public $traits_given;
	public $tier_requirements;
	
	function __construct($id, $name, $cost){
		$this->id = $id;
		$this->name = $name;
		$this->requirements = array();
		$this->leads = array();
		$this->progress = $cost;
		$this->cost = $cost;
		$this->ship_classes = [];	
		$this->structure_classes = [];
		$this->power_classes = [];	
		$this->upgrade_classes = [];
		$this->tier_requirements = 0;	
		$this->traits_given = [];
		$this->traits_required = [];
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
	public function add_upgrade_class($upgrade_class){
		$this->upgrade_classes[] = $upgrade_class;
	}
	public function add_power_class($power_class){
		$this->power_classes[] = $power_class;
	}
	
	public function is_tech_available($tier){
		if ($tier >= $this->tier_requirements){
			return true;
		}
		return false;
	}
	
	public function add_trait($name, $amount=1){
		$this->traits_given[$name] = $amount;
	}
	public function require_trait($name, $amount=1){
		$this->traits_required[$name] = $amount;
	}
	public function set_tier_requirement($number){
		$this->tier_requirements = $number;
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