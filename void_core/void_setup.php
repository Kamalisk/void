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

$power_class = new VOID_POWER_CLASS(5, "Sub-surface Purification");
$power_class->type = "colonise_planet";
$power_class->value = 6;
$power_class->description = "Allows you to colonise class J planets.";
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