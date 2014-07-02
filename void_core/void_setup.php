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
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 10;
$ship_class->rush_cost = 50;
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Scout II";
$ship_class->work_required = 20;
$ship_class->rush_cost = 100;
$ship_class->vision_range = 2;
$ship_class->movement_capacity = 6;
$ship_class->hull = 200;
$ship_class->shields = 0;
$ship_class->damage = 20;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Scout III";
$ship_class->work_required = 80;
$ship_class->rush_cost = 400;
$ship_class->vision_range = 3;
$ship_class->movement_capacity = 8;
$ship_class->hull = 400;
$ship_class->shields = 0;
$ship_class->damage = 40;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS(2);
$ship_class->name = "Colony I";
$ship_class->add_special("colony");
$ship_class->work_required = 50;
$ship_class->rush_cost = 250;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 0;
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Colony II";
$ship_class->add_special("colony");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 2;
$ship_class->movement_capacity = 5;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 0;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Colony III";
$ship_class->add_special("colony");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 2;
$ship_class->movement_capacity = 5;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 0;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS(3);
$ship_class->name = "Basic Attack I";
$ship_class->work_required = 50;
$ship_class->rush_cost = 250;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 20;
$tech = $this->tech_tree->get_tech(2);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Siege I";
$ship_class->add_special("siege");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 0;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Siege I";
$ship_class->add_special("siege");
$ship_class->work_required = 100;
$ship_class->rush_cost = 500;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 4;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 0;
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
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS();
$ship_class->name = "Fighter Swarm I";
$ship_class->work_required = 100;
$ship_class->rush_cost = 100;
$ship_class->vision_range = 1;
$ship_class->movement_capacity = 6;
$ship_class->hull = 100;
$ship_class->shields = 0;
$ship_class->damage = 50;
//$this->tech_tree->get_tech(1)->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$ship_class = new VOID_SHIP_CLASS(4);
$ship_class->name = "Speedy";
$ship_class->work_required = 30;
$ship_class->rush_cost = 150;
$ship_class->damage = 10;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(6);
//$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS(5);
$ship_class->name = "Speedy Colony";
$ship_class->work_required = 40;
$ship_class->rush_cost = 200;
$ship_class->add_special("colony");
$ship_class->damage = 0;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(3);
//$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS(6);
$ship_class->name = "Constructor I";
$ship_class->work_required = 40;
$ship_class->rush_cost = 200;
$ship_class->add_special("construct");
$ship_class->damage = 0;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(1);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;





$structure_class = new VOID_STRUCTURE_CLASS(1);
$structure_class->name = "Captial";
$structure_class->set_unique("empire");
$structure_class->set_modifier("food", 1);
$structure_class->set_modifier("morale", 25);
$structure_class->set_modifier("morale_per_population", 1, 5);
$structure_class->set_modifier("production", 5);
$structure_class->set_modifier("credits", 5);
$structure_class->set_modifier("influence", 2);
$structure_class->set_modifier("research", 500);
$structure_class->set_modifier("credits_per_population", 2);
$tech = $this->tech_tree->get_tech(1);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(2);
$structure_class->name = "Farm I";
$structure_class->work_required = 40;
$structure_class->set_modifier("food", 5);
$structure_class->set_modifier("credits", -1);
$tech = $this->tech_tree->get_tech(3);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Farm II";
$structure_class->work_required = 80;
$structure_class->set_modifier("food", 15);
$structure_class->set_modifier("credits", -3);
$this->tech_tree->get_tech(21)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Orbital Shipyard";
$structure_class->work_required = 80;
$structure_class->set_modifier("ship_production", 15);
$structure_class->set_modifier("credits", -1);
$this->tech_tree->get_tech(10)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS(3);
$structure_class->name = "Bank I";		
$structure_class->work_required = 40;
$structure_class->set_modifier("credits", 5);
$tech = $this->tech_tree->get_tech(5);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Bank II";
$structure_class->work_required = 80;
$structure_class->set_modifier("credits", 15);
$this->tech_tree->get_tech(23)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(4);
$structure_class->name = "Happy Place I";
$structure_class->work_required = 40;
$structure_class->set_modifier("morale_per_population", 1, 3);
$structure_class->set_modifier("credits", -2);
$tech = $this->tech_tree->get_tech(7);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Happy Place II";
$structure_class->work_required = 80;
$structure_class->set_modifier("morale_per_population", 2, 6);
$structure_class->set_modifier("credits", -4);
$this->tech_tree->get_tech(25)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS(5);
$structure_class->name = "Factory I";
$structure_class->work_required = 50;
$structure_class->set_modifier("production", 5);
$structure_class->set_modifier("credits", -1);
$tech = $this->tech_tree->get_tech(6);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Factory II";
$structure_class->work_required = 80;
$structure_class->set_modifier("production", 15);
$structure_class->set_modifier("credits", -4);
$this->tech_tree->get_tech(24)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS(6);
$structure_class->name = "Research Lab I";
$structure_class->work_required = 40;
$structure_class->set_modifier("research", 5);
$structure_class->set_modifier("credits", -2);
$tech = $this->tech_tree->get_tech(4);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Research Lab II";
$structure_class->work_required = 80;
$structure_class->set_modifier("research", 15);
$structure_class->set_modifier("credits", -4);
$this->tech_tree->get_tech(22)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS(8);
$structure_class->name = "Galactic Radio I";
$structure_class->work_required = 40;
$structure_class->set_modifier("influence", 5);
$structure_class->set_modifier("credits", -2);
$tech = $this->tech_tree->get_tech(11);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Galactic Radio II";
$structure_class->work_required = 80;
$structure_class->set_modifier("influence", 15);
$structure_class->set_modifier("credits", -4);
//$this->tech_tree->get_tech(21)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Sonic Refinery";
$structure_class->work_required = 50;
$structure_class->set_modifier("production", 5);
$structure_class->set_modifier("production_percent", 10);
$structure_class->set_modifier("credits", -3);
$this->tech_tree->get_tech(14)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Money Percent";
$structure_class->work_required = 100;
$structure_class->set_modifier("credits", 3);
$structure_class->set_modifier("credits_percent", 10);
$this->tech_tree->get_tech(9)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Orbital Hydroponics";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("food", 10);
$structure_class->set_modifier("food_percent", 10);
$this->tech_tree->get_tech(13)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "Greendale Galactic College";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("influence", 5);
$structure_class->set_modifier("morale", 5);
$structure_class->set_modifier("research", 5);
$this->tech_tree->get_tech(11)->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->name = "80s Cartoon Museum";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("influence", 10);
$structure_class->set_modifier("influence_per_population", 2);
$this->tech_tree->get_tech(15)->add_structure_class($structure_class);
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


$power_class = new VOID_POWER_CLASS(1, "Light Accelerators");
$power_class->type = "vision";
$power_class->value = 1;
$power_class->description = "Increase vision radius on all fleets";
$tech = $this->tech_tree->get_tech(3);
//$tech->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$tech = $this->tech_tree->get_tech(2);
//$tech->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;


$power_class = new VOID_POWER_CLASS(3, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class X planets.";
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

$power_class = new VOID_POWER_CLASS(10, "Researchious");
$power_class->type = "research";
$power_class->value = 0.1;
$power_class->description = "Gives 10% more research";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(11, "Cybernetically Enhanced");
$power_class->type = "production";
$power_class->value = 0.1;
$power_class->description = "Gives 10% more production";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(12, "Pyschic Ability");
$power_class->type = "influence";
$power_class->value = 0.1;
$power_class->description = "Gives 10% more influence";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(13, "Junk Food");
$power_class->type = "food";
$power_class->value = 0.05;
$power_class->description = "Gives 5% more food";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(14, "Entrepenurs");
$power_class->type = "credits";
$power_class->value = 0.05;
$power_class->description = "Gives 5% more credits";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(15, "Self Consuming");
$power_class->type = "food";
$power_class->value = 0.1;
$power_class->description = "Gives 10% more credits";
$this->power_classes[$power_class->id] = $power_class;


$power_class = new VOID_POWER_CLASS(16, "Class J Adaptation");
$power_class->type = "morale_planet";
$power_class->value = 2;
$power_class->description = "Reduce morale loss from colonized Class J planets by 2";
//$this->tech_tree->get_tech(13)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;


$power_class = new VOID_POWER_CLASS(17, "Monotomic Discovery");
$power_class->type = "research";
$power_class->value = 0.05;
$power_class->description = "Increase empire research by 5%";
$this->tech_tree->get_tech(8)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(18, "Quantum Coalescence");
$power_class->type = "ship_production";
$power_class->value = 0.10;
$power_class->description = "Increase ship production by 5%";
//$this->tech_tree->get_tech(8)->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;


// empires / races
// provides base line powers and special units

// Bakani
// 10% more research
// Class M

$bakani = new VOID_RACE("Bakani");
$bakani->add_power($this->power_classes[10]);
$this->races[$bakani->id] = $bakani;

// Etrib
// 10% more production
// Class M
$etrib = new VOID_RACE("Etrib");
$etrib->add_power($this->power_classes[11]);
$this->races[$etrib->id] = $etrib;

// Terran
// 5% more food
// 5% more credits
// Class M
$terran = new VOID_RACE("Terran");
$terran->add_power($this->power_classes[13]);
$terran->add_power($this->power_classes[14]);
$this->races[$terran->id] = $terran;

// Penguina
// 10% more influence
// Class C
$penguina = new VOID_RACE("Hruskan");
$penguina->add_power($this->power_classes[12]);
$this->races[$penguina->id] = $penguina;

// Nephalem (amoeba people)
// 10% more food
// Class K
$nephalem = new VOID_RACE("Nephalem");
$nephalem->add_power($this->power_classes[15]);
$this->races[$nephalem->id] = $nephalem;


$pirates = new VOID_RACE("Pirates");
$this->races[$pirates->id] = $pirates;


// empires 
// provides further specialisation and special powers

$empire = new VOID_EMPIRE("Bakani Empire");
$bakani->add_empire($empire);
$this->empires[$empire->id] = $empire;

$empire = new VOID_EMPIRE("Followers of Edam");
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
// 10% more production towards wonders
// Destroying ships returns production to home system
// golden ages last longer
// Free Techs

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