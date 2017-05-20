<?php

$opinion = new VOID_OPINION("I don't like your military build up", "evil");
$this->opinions[$opinion->id] = $opinion;
$opinion = new VOID_OPINION("I approve of your war", "good");
$this->opinions[$opinion->id] = $opinion;
$opinion = new VOID_OPINION("I dislike your expansion new my borders", "evil");
$this->opinions[$opinion->id] = $opinion;

$tech = $this->tech_tree->get_tech(1);

// default movement speed is 4. 2 normal sectors.

// load ship classes from somewhere
$ship_class = new VOID_SHIP_CLASS(1);
$ship_class->name = "Scout I";
$ship_class->work_required = 10;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->attack = 10;
$ship_class->defense = 10;
$ship_class->rush_cost = 50;
$ship_class->description = "Basic scout vessel for exploration";
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Scout II";
$ship_class->work_required = 20;
$ship_class->rush_cost = 100;
$ship_class->vision_range = 2;
$ship_class->movement_capacity = 6;
$ship_class->attack = 20;
$ship_class->defense = 20;
$ship_class->description = "Basic scout vessel for exploration";
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Scout III";
$ship_class->work_required = 80;
$ship_class->rush_cost = 400;
$ship_class->vision_range = 3;
$ship_class->movement_capacity = 8;
$ship_class->attack = 40;
$ship_class->defense = 40;
$ship_class->description = "Basic scout vessel for exploration";
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS(2);
$ship_class->name = "Colony I";
$ship_class->add_special("colony");
$ship_class->work_required = 50;
$ship_class->rush_cost = 250;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->attack = 0;
$ship_class->defense = 20;
$ship_class->damage = 0;
$ship_class->description = "Can colonise other planets";
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Colony II";
$ship_class->add_special("colony");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 2;
$ship_class->movement_capacity = 5;
$ship_class->attack = 0;
$ship_class->defense = 40;
$ship_class->damage = 0;
$ship_class->description = "Can colonise other planets";
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Colony III";
$ship_class->add_special("colony");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 2;
$ship_class->movement_capacity = 5;
$ship_class->attack = 0;
$ship_class->defense = 80;
$ship_class->damage = 0;
$ship_class->description = "Can colonise other planets";
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS(3);
$ship_class->name = "Basic Attack I";
$ship_class->work_required = 50;
$ship_class->rush_cost = 250;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->attack = 20;
$ship_class->defense = 20;
$ship_class->description = "Basic attack ship";
$tech = $this->tech_tree->get_tech(2);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Defender I";
$ship_class->work_required = 50;
$ship_class->rush_cost = 250;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->attack = 10;
$ship_class->defense = 40;
$ship_class->description = "Ship designed to improve the defense of a fleet";
$tech = $this->tech_tree->get_tech(16);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Siege I";
$ship_class->add_special("siege");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->attack = 40;
$ship_class->defense = 40;
$ship_class->description = "Ship designed to take over weakend planetary systems";
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Siege I";
$ship_class->add_special("siege");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->attack = 80;
$ship_class->defense = 80;
$ship_class->description = "Ship designed to take over weakend planetary systems";
$this->tech_tree->get_tech(12)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Range I";
$ship_class->add_special("range");
$ship_class->work_required = 100;
$ship_class->rush_cost = 100;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 0;
$ship_class->description = "Ship designed to attack enemy fleets at a distance";
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Fighter Swarm I";
$ship_class->work_required = 100;
$ship_class->rush_cost = 100;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 6;
$ship_class->attack = 40;
$ship_class->defense = 5;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS(4);
$ship_class->name = "Speedy";
$ship_class->work_required = 30;
$ship_class->rush_cost = 150;
$ship_class->attack = 10;
$ship_class->defense = 20;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(6);
$ship_class->description = "Ship designed to move quickly around the galaxy";
//$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS(5);
$ship_class->name = "Speedy Colony";
$ship_class->work_required = 40;
$ship_class->rush_cost = 200;
$ship_class->add_special("colony");
$ship_class->attack = 0;
$ship_class->defense = 40;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(3);
//$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS(6);
$ship_class->name = "Constructor I";
$ship_class->work_required = 40;
$ship_class->rush_cost = 200;
$ship_class->add_special("construct");
$ship_class->attack = 0;
$ship_class->defense = 40;
$ship_class->movement_capacity = 4;
$ship_class->damage = 0;
$tech = $this->tech_tree->get_tech(1);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;





$structure_class = new VOID_STRUCTURE_CLASS(1);
$structure_class->name = "Captial";
$structure_class->set_unique("empire");
$structure_class->set_modifier("food", "per_turn", 3);
$structure_class->set_modifier("morale", "per_turn", 20, 0, "player");
$structure_class->set_modifier("production", "per_turn", 3);
$structure_class->set_modifier("credits", "per_turn", 3);
$structure_class->set_modifier("influence","per_turn", 3);
$structure_class->set_modifier("research","per_turn", 3);
$structure_class->set_modifier("credits","per_turn", 3);
$structure_class->set_modifier("attack", "", 200);
$structure_class->set_modifier("defense", "", 200);
$structure_class->set_modifier("health", "", 200);
//$structure_class->set_modifier("credits","percent", 10, 0, "player");
$tech = $this->tech_tree->get_tech(1);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(2);
$structure_class->name = "Farm I";
$structure_class->work_required = 40;
$structure_class->set_modifier("food", "per_turn", 5);
$structure_class->set_modifier("credits", "per_turn", -1);
$tech = $this->tech_tree->get_tech(3);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS("colony_hub");
$structure_class->name = "Colony Hub";
$structure_class->work_required = 0;
$structure_class->set_modifier("food", "per_population", 1);
$structure_class->set_modifier("research", "per_population", 1);
$structure_class->set_modifier("credits", "per_population", 1);
$structure_class->set_modifier("production", "per_population", 1);
$structure_class->set_modifier("attack", "", 50);
$structure_class->set_modifier("defense", "", 50);
$structure_class->set_unique("planet");
$tech = $this->tech_tree->get_tech(1);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Farm II";
$structure_class->work_required = 80;
$structure_class->set_modifier("food", "per_turn", 15);
$structure_class->set_modifier("credits", "per_turn", -3);
$this->tech_tree->get_tech(21)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Orbital Shipyard";
$structure_class->work_required = 80;
$structure_class->set_modifier("ship_production", "percent", 15);
$structure_class->set_modifier("credits", "per_turn", -1);
$this->tech_tree->get_tech(10)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS(3);
$structure_class->name = "Bank I";		
$structure_class->work_required = 40;
$structure_class->set_modifier("credits", "per_turn", 5);
$tech = $this->tech_tree->get_tech(5);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Bank II";
$structure_class->work_required = 80;
$structure_class->set_modifier("credits", "per_turn", 15);
$this->tech_tree->get_tech(23)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(4);
$structure_class->name = "Happy Place I";
$structure_class->work_required = 40;
$structure_class->set_modifier("morale", "per_population", 1, 3, "player");
$structure_class->set_modifier("credits", "per_turn", -2);
$tech = $this->tech_tree->get_tech(7);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Happy Place II";
$structure_class->work_required = 80;
$structure_class->set_modifier("morale", "per_population", 2, 6, "player");
$structure_class->set_modifier("credits", "per_turn", -4);
$this->tech_tree->get_tech(25)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(5);
$structure_class->name = "Factory I";
$structure_class->work_required = 50;
$structure_class->set_modifier("production", "per_turn", 5);
$structure_class->set_modifier("credits", "per_turn", -1);
$tech = $this->tech_tree->get_tech(6);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Factory II";
$structure_class->work_required = 80;
$structure_class->set_modifier("production", "per_turn", 15);
$structure_class->set_modifier("credits", "per_turn", -4);
$this->tech_tree->get_tech(24)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS(6);
$structure_class->name = "Research Lab I";
$structure_class->work_required = 40;
$structure_class->set_modifier("research", "per_turn", 5);
$structure_class->set_modifier("credits", "per_turn", -2);
$tech = $this->tech_tree->get_tech(4);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Research Lab II";
$structure_class->work_required = 80;
$structure_class->set_modifier("research", "per_turn", 15);
$structure_class->set_modifier("credits", "per_turn", -4);
$this->tech_tree->get_tech(22)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Communication Hub";
$structure_class->work_required = 40;
$structure_class->set_modifier("influence", "per_turn", 5);
$structure_class->set_modifier("credits", "per_turn", -2);
$tech = $this->tech_tree->get_tech(1);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(8);
$structure_class->name = "Galactic Radio I";
$structure_class->work_required = 40;
$structure_class->set_modifier("influence", "per_turn", 5);
$structure_class->set_modifier("credits", "per_turn", -2);
$tech = $this->tech_tree->get_tech(11);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Galactic Radio II";
$structure_class->work_required = 80;
$structure_class->set_modifier("influence", "per_turn", 15);
$structure_class->set_modifier("credits", "per_turn", -4);
//$this->tech_tree->get_tech(21)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Sonic Refinery";
$structure_class->work_required = 50;
$structure_class->set_modifier("production", "per_turn", 5);
$structure_class->set_modifier("production", "percent", 10);
$structure_class->set_modifier("credits", "per_turn", -3);
$this->tech_tree->get_tech(14)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Money Percent";
$structure_class->work_required = 100;
$structure_class->set_modifier("credits", "per_turn", 3);
$structure_class->set_modifier("credits", "percent", 10);
$this->tech_tree->get_tech(9)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Orbital Hydroponics";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("food", "per_turn", 10);
$structure_class->set_modifier("food", "percent", 10);
$this->tech_tree->get_tech(13)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Greendale Galactic College";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("influence", "per_turn", 5);
$structure_class->set_modifier("morale", "per_turn", 5, 0, "player");
$structure_class->set_modifier("research", "per_turn", 5);
$this->tech_tree->get_tech(11)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "80s Cartoon Museum";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("influence", "per_turn", 10);
$structure_class->set_modifier("influence", "per_population", 2);
$this->tech_tree->get_tech(15)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Omega 359 Memorial";
$structure_class->set_unique("world");
$structure_class->work_required = 150;
$structure_class->set_modifier("influence", "per_turn", 5);
$structure_class->set_modifier("morale", "per_turn", 3, 0, "player");
$structure_class->set_modifier("attack", "", 50);
$this->tech_tree->get_tech(2)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Vault of the Ages";
$structure_class->set_unique("world");
$structure_class->work_required = 150;
$structure_class->set_modifier("influence", "per_turn", 5);
$structure_class->set_modifier("credits", "per_turn", 10);
$structure_class->set_modifier("defense", "", 50);
$this->tech_tree->get_tech(5)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Industrial Simposium";
$structure_class->set_unique("world");
$structure_class->work_required = 150;
$structure_class->set_modifier("influence", "per_turn", 5);
$structure_class->set_modifier("production", "per_turn", 10);
$structure_class->set_modifier("health", "", 50);
$this->tech_tree->get_tech(6)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 1;
$upgrade_class->name = "Space Nebula Shop";
$upgrade_class->set_modifier("credits", 10);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(3);
$upgrade_class->add_requirement(4);
$upgrade_class->add_requirement(5);
$tech = $this->tech_tree->get_tech(19);
$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;

$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 2;
$upgrade_class->name = "Asteroid Amusement Park";
$upgrade_class->set_modifier("morale", 5);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(2);
$tech = $this->tech_tree->get_tech(1);
$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;

$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 3;
$upgrade_class->name = "Gaseous Anomaly Station";
$upgrade_class->set_modifier("research", 5);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(3);
$upgrade_class->add_requirement(4);
$upgrade_class->add_requirement(5);
$tech = $this->tech_tree->get_tech(8);
//$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;

$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 4;
$upgrade_class->name = "Nebula Enrichment Facility";
$upgrade_class->set_modifier("influence", 20);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(3);
$upgrade_class->add_requirement(4);
$upgrade_class->add_requirement(5);
$tech = $this->tech_tree->get_tech(10);
//$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;

$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->name = "Asteroidal Lifeform Lab";
$upgrade_class->set_modifier("research", 5);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(2);
$upgrade_class->add_requirement(8);
$this->tech_tree->get_tech(4)->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;



$power_class = new VOID_POWER_CLASS(1, "Light Accelerators");
$power_class->type = "vision";
$power_class->value = 1;
$power_class->description = "Increase vision radius on all fleets";
$tech = $this->tech_tree->get_tech(3);
//$tech->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("terraform_class_y", "Colonise Class M");
$power_class->set_modifier("index", "terraformable", 1);
$power_class->description = "Allows you to colonise class M planets";
$this->tech_tree->get_tech(1)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("terraform_class_y", "Sub-surface Purification");
$power_class->description = "Allows you to colonise class Y planets.";
$power_class->set_modifier("index", "terraformable", 6);
$this->tech_tree->get_tech(12)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("terraform_class_c", "Atmospheric Cleansing");
$power_class->description = "Allows you to colonise class C planets.";
$power_class->set_modifier("index", "terraformable", 5);
$this->tech_tree->get_tech(18)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("terraform_class_j", "Jungle Reconstruction");
$power_class->description = "Allows you to colonise class J planets.";
$power_class->set_modifier("index", "terraformable", 2);
$this->tech_tree->get_tech(3)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("terraform_class_x", "Arid Climate Analysis");
$power_class->description = "Allows you to colonise class X planets.";
$power_class->set_modifier("index", "terraformable", 3);
$this->tech_tree->get_tech(7)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("terraform_class_p", "Terraform Aquatic Environments");
$power_class->description = "Allows you to colonise class P planets.";
$power_class->set_modifier("index", "terraformable", 4);
$this->tech_tree->get_tech(17)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;


$power_class = new VOID_POWER_CLASS("command1", "Fleet Logistics");
$power_class->description = "Increases command limit by 1";
$power_class->set_modifier("command", "", 1);
$this->tech_tree->get_tech(20)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("command2", "Autonomous Operational Diodes");
$power_class->description = "Increases command limit by 1";
$power_class->set_modifier("command", "", 1);
$this->tech_tree->get_tech(30)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("command3", "Catonian Management Nodes");
$power_class->description = "Increases command limit by 1";
$power_class->set_modifier("command", "", 1);
$this->tech_tree->get_tech(19)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;





$power_class = new VOID_POWER_CLASS(4, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class C planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(5, "Class J Terraforming");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class J planets.";
$this->tech_tree->get_tech(13)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(6, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(7, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(8, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(9, "Morale Boost");
$power_class->type = "morale";
$power_class->value = 5;
$power_class->description = "Gives bonus morale";
$this->power_classes[$power_class->id] = $power_class;



$power_class = new VOID_POWER_CLASS("research_10", "Researchious");
$power_class->set_modifier("research", "percent", 10);
$power_class->description = "Increase empire research by 10%";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("production_10", "Cybernetically Enhanced");
$power_class->set_modifier("production", "percent", 10);
$power_class->description = "Increase production of systems by 10%";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("influence_10", "Pyschic Ability");
$power_class->set_modifier("influence", "percent", 10);
$power_class->description = "Increase influence of systems by 10%";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("food_credits_5", "Junk Food");
$power_class->set_modifier("food", "percent", 5);
$power_class->set_modifier("credits", "percent", 5);
$power_class->description = "Increases food and credit income by 5%";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("food_10", "Self Consuming");
$power_class->set_modifier("food", "percent", 10);
$power_class->description = "Increase food of systems by 10%";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("research_bakani_empire", "Dedicated");
$power_class->set_modifier("research", "per_population", 1, 0, "planet");
$power_class->description = "Increase research by 1 per population";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("research_edam_empire", "Dedicated");
$power_class->set_modifier("windfall", "research", 100);
$power_class->description = "Gain 100 research whenever you colonise a new planet";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("production_conglomerate_empire", "Dedicated");
$power_class->set_modifier("windfall", "production", 100);
$power_class->description = "Gain 100 production in your home system whenever you colonise a new planet";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("producsdsdsdate_empire", "Dedicated");
$power_class->set_modifier("salvage", "credits", 100);
$power_class->description = "Gain 100 credits whenever you destroy an enemy fleet";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("production_constructors_empire", "Dedicated");
$power_class->set_modifier("salvage", "credits", 100);
$power_class->description = "Gain 100 credits whenever you destroy an enemy fleet";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("salvage_sanctuary_empire", "Dedicated");
$power_class->set_modifier("salvage", "credits", 100);
$power_class->description = "Gain 100 credits whenever you destroy an enemy fleet";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS("salvage_sanctuary_empire", "Dedicated");
$power_class->set_modifier("salvage", "credits", 100);
$power_class->description = "Gain 100 credits whenever you destroy an enemy fleet";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(17, "Monotomic Discovery");
$power_class->set_modifier("research", "percent", 10);
$power_class->description = "Increase empire research by 5%";
$this->tech_tree->get_tech(8)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;



// empires / races
// provides base line powers and special units

// Bakani
// 10% more research
// Class M

$bakani = new VOID_RACE("Bakani");
$bakani->add_power($this->power_classes["research_10"]);
$this->races[$bakani->id] = $bakani;

// Etrib
// 10% more production
// Class M
$etrib = new VOID_RACE("Etrib");
$etrib->add_power($this->power_classes["production_10"]);
$this->races[$etrib->id] = $etrib;

// Terran
// 5% more food
// 5% more credits
// Class M
$terran = new VOID_RACE("Terran");
$terran->add_power($this->power_classes["food_credits_5"]);
$this->races[$terran->id] = $terran;

// Penguina
// 10% more influence
// Class C
$penguina = new VOID_RACE("Hruskan");
$penguina->add_power($this->power_classes["influence_10"]);
$this->races[$penguina->id] = $penguina;

// Nephalem (amoeba people)
// 10% more food
// Class K
$nephalem = new VOID_RACE("Nephalem");
$nephalem->add_power($this->power_classes["food_10"]);
$this->races[$nephalem->id] = $nephalem;

$pirates = new VOID_RACE("Pirates");
$this->races[$pirates->id] = $pirates;


// empires 
// provides further specialisation and special powers

$empire = new VOID_EMPIRE("Bakani Empire");
//$empire->add_power($this->power_classes["research_bakani_empire"]);
$bakani->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("Followers of Edam");
$empire->add_power($this->power_classes["research_edam_empire"]);
$bakani->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Etrib Conglomerate");
$etrib->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("Galactic Constructors");
$etrib->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Terran Republic");
$terran->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Aztec Empire");
$terran->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("Shadow Proclimation");
$terran->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Emirates of Hruska");
$penguina->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Kalten");
$penguina->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Sanctuary Coalition");
$nephalem->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("The Boradic Cube");
$nephalem->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("Bavarian Pirates");
$pirates->add_empire($empire);
$this->empires[$empire->id] = $empire;


// leaders 

// +1 vision. 
// +1 movement point on all ships
// +1 fleet size
// wonders require 10% less production to build
// Destroying ships returns production to home system
// golden ages last longer
// Free Techs
// happiness per X techs
// Gain money for each planet you colonise
// new systems start at 2 population
// +1 happiness per population, -1 happiness per system

// Start with one tech?

// provides one special power 

// Bakani Leaders

$leader = new VOID_LEADER("Lina III");
$this->leaders[$leader->id] = $leader;
$bakani->add_leader($leader);

$leader = new VOID_LEADER("Najica Blitzer");
$this->leaders[$leader->id] = $leader;
$bakani->add_leader($leader);

$leader = new VOID_LEADER("Steve");
$this->leaders[$leader->id] = $leader;
$terran->add_leader($leader);

// Etrib Leaders
$leader = new VOID_LEADER("Odin Falsehand");
$this->leaders[$leader->id] = $leader;
$etrib->add_leader($leader);
// Thor Grim
$leader = new VOID_LEADER("Thor Grim");
$this->leaders[$leader->id] = $leader;
$etrib->add_leader($leader);

// Penguina
$leader = new VOID_LEADER("Teef Yapph");
$this->leaders[$leader->id] = $leader;
$penguina->add_leader($leader);


$leader = new VOID_LEADER("Ytar");
$this->leaders[$leader->id] = $leader;
$nephalem->add_leader($leader);

// 
// 10% more production when building wonders

$leader = new VOID_LEADER("Bavarian Space Pirate Guy");
$this->leaders[$leader->id] = $leader;
$pirates->add_leader($leader);


?>