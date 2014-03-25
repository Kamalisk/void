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
$ship_class = new VOID_SHIP_CLASS();
$ship_class->id = 1;
$ship_class->name = "Scout";
$ship_class->movement_capacity = 4;
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->id = 2;
$ship_class->name = "Colony";
$ship_class->add_special("colony");
$ship_class->work_required = 50;
$ship_class->damage = 0;
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->id = 3;
$ship_class->name = "Attack";
$ship_class->work_required = 30;
$ship_class->damage = 20;
$tech = $this->tech_tree->get_tech(2);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->id = 4;
$ship_class->name = "Speedy";
$ship_class->work_required = 30;
$ship_class->damage = 10;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(6);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->id = 5;
$ship_class->name = "Speedy Colony";
$ship_class->work_required = 40;
$ship_class->add_special("colony");
$ship_class->damage = 0;
$ship_class->movement_capacity = 4;
$tech = $this->tech_tree->get_tech(3);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;

$ship_class = new VOID_SHIP_CLASS();
$ship_class->id = 6;
$ship_class->name = "Constructor";
$ship_class->work_required = 40;
$ship_class->add_special("construct");
$ship_class->damage = 0;
$ship_class->movement_capacity = 6;
$tech = $this->tech_tree->get_tech(1);
$tech->add_ship_class($ship_class);
$this->ship_classes[$ship_class->id] = $ship_class;


$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 1;
$structure_class->name = "Captial";
$structure_class->set_unique("empire");
$structure_class->set_modifier("food", 1);
$structure_class->set_modifier("morale", 25);
$structure_class->set_modifier("production", 5);
$structure_class->set_modifier("credits", 5);
$structure_class->set_modifier("influence", 2);
$structure_class->set_modifier("research", 5);
$tech = $this->tech_tree->get_tech(1);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 2;
$structure_class->name = "Farm";
$structure_class->work_required = 40;
$structure_class->set_modifier("food", 5);
$structure_class->set_modifier("credits", -1);
$tech = $this->tech_tree->get_tech(3);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 3;
$structure_class->name = "Bank";		
$structure_class->work_required = 40;
$structure_class->set_modifier("credits", 5);
$tech = $this->tech_tree->get_tech(4);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 4;
$structure_class->name = "Happy Place";
$structure_class->work_required = 40;
$structure_class->set_modifier("morale", 3);
$structure_class->set_modifier("credits", -1);
$tech = $this->tech_tree->get_tech(4);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 5;
$structure_class->name = "Factory";
$structure_class->work_required = 50;
$structure_class->set_modifier("production", 5);
$structure_class->set_modifier("credits", -1);
$tech = $this->tech_tree->get_tech(2);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 6;
$structure_class->name = "Research Lab";
$structure_class->work_required = 40;
$structure_class->set_modifier("research", 5);
$structure_class->set_modifier("credits", -1);
$tech = $this->tech_tree->get_tech(6);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 6;
$structure_class->name = "Galactic Radio Station";
$structure_class->work_required = 40;
$structure_class->set_modifier("influence", 5);
$structure_class->set_modifier("credits", -2);
$tech = $this->tech_tree->get_tech(5);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;

$structure_class = new VOID_STRUCTURE_CLASS();
$structure_class->id = 7;
$structure_class->name = "80s Cartoon Museum";
$structure_class->set_unique("world");
$structure_class->work_required = 300;
$structure_class->set_modifier("influence", 15);		
$tech = $this->tech_tree->get_tech(5);
$tech->add_structure_class($structure_class);
$this->structure_classes[$structure_class->id] = $structure_class;


$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 1;
$upgrade_class->name = "Space Nebula Shop";
$upgrade_class->set_modifier("credits", 10);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(3);
$upgrade_class->add_requirement(4);
$upgrade_class->add_requirement(5);
$tech = $this->tech_tree->get_tech(1);
$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;

$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 2;
$upgrade_class->name = "Asteroid Amusement Park";
$upgrade_class->set_modifier("morale", 5);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(2);
$tech = $this->tech_tree->get_tech(10);
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
$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;

$upgrade_class = new VOID_UPGRADE_CLASS();
$upgrade_class->id = 4;
$upgrade_class->name = "Nebula Enrichment Facility";
$upgrade_class->set_modifier("influence", 20);
$upgrade_class->work_required = 40;
$upgrade_class->add_requirement(3);
$upgrade_class->add_requirement(4);
$upgrade_class->add_requirement(5);
$tech = $this->tech_tree->get_tech(3);
$tech->add_upgrade_class($upgrade_class);
$this->upgrade_classes[$upgrade_class->id] = $upgrade_class;


$power_class = new VOID_POWER_CLASS(1, "Light Accelerators");
$power_class->type = "vision";
$power_class->value = 1;
$power_class->description = "Increase vision radius on all fleets";
$tech = $this->tech_tree->get_tech(3);
$tech->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$tech = $this->tech_tree->get_tech(2);
$tech->add_power_class($power_class);
$this->power_classes[$power_class->id] = $power_class;


$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class X planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class C planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class J planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class Y planets.";
$this->power_classes[$power_class->id] = $power_class;

$power_class = new VOID_POWER_CLASS(2, "Morale Boost");
$power_class->type = "morale";
$power_class->value = 5;
$power_class->description = "Gives bonus morale";
$this->power_classes[$power_class->id] = $power_class;


// empires / races
// provides base line powers and special units

// Bakani

// Etrib

// Terran

// Penguina


// leaders 

// provides one special power 

// Each 


?>