<?

$void_hex_adjacent = array(
	array(0,  -1),  array(+1, -1),  array( +1, 0),
	array(0,  +1),  array(-1, +1),  array( -1, 0) 
);

$void_ranges = array();

$range = 1;
$x = -1;
$z = 1;
$void_ranges[$range] = array();
for ($i = 0 ; $i < 6; $i++){
	for ($j = 0; $j < $range; $j++){
		$void_ranges[$range][] = array('x'=>$x, 'z'=>$z);
		$x = $x+$void_hex_adjacent[$i][0];
		$z = $z+$void_hex_adjacent[$i][1];
	}
}
$range = 2;
$x = -2;
$z = 2;
$void_ranges[$range] = array();
for ($i = 0 ; $i < 6; $i++){
	for ($j = 0; $j < $range; $j++){
		$void_ranges[$range][] = array('x'=>$x, 'z'=>$z);
		$x = $x+$void_hex_adjacent[$i][0];
		$z = $z+$void_hex_adjacent[$i][1];
	}
}

$range = 3;
$x = -3;
$z = 3;
$void_ranges[$range] = array();
for ($i = 0 ; $i < 6; $i++){
	for ($j = 0; $j < $range; $j++){
		$void_ranges[$range][] = array('x'=>$x, 'z'=>$z);
		$x = $x+$void_hex_adjacent[$i][0];
		$z = $z+$void_hex_adjacent[$i][1];
	}
}

$range = 4;
$x = -4;
$z = 4;
$void_ranges[$range] = array();
for ($i = 0 ; $i < 6; $i++){
	for ($j = 0; $j < $range; $j++){
		$void_ranges[$range][] = array('x'=>$x, 'z'=>$z);
		$x = $x+$void_hex_adjacent[$i][0];
		$z = $z+$void_hex_adjacent[$i][1];
	}
}

$range = 5;
$x = -5;
$z = 5;
$void_ranges[$range] = array();
for ($i = 0 ; $i < 6; $i++){
	for ($j = 0; $j < $range; $j++){
		$void_ranges[$range][] = array('x'=>$x, 'z'=>$z);
		$x = $x+$void_hex_adjacent[$i][0];
		$z = $z+$void_hex_adjacent[$i][1];
	}
}

$range = 6;
$x = -6;
$z = 6;
$void_ranges[$range] = array();
for ($i = 0 ; $i < 6; $i++){
	for ($j = 0; $j < $range; $j++){
		$void_ranges[$range][] = array('x'=>$x, 'z'=>$z);
		$x = $x+$void_hex_adjacent[$i][0];
		$z = $z+$void_hex_adjacent[$i][1];
	}
}

// system types
// space
// asteroid
// nebula
// blackhole

$void_system_types = array();
$void_system_types['asteroid'] = array(
	"movement_cost" => 2
);
$void_system_types['space'] = array(
	"movement_cost" => 1
);


$player_colors = [
				1 => [
					"background" => "rgba(0,155,0,0.2)", 
					"border"=>"rgba(0,155,0,1)", 
					"fleet"=> "images/fleets/fleet_f.png",
					"color_id" => 1
				],
				2 => [
					"background" => "rgba(155,0,0,0.2)", "border" => "rgba(155,0,0,1)", "fleet"=> "images/fleets/fleet_e.png",
					"color_id" => 2
				],
				3 => [
					"background" => "rgba(0,71,251,0.2)", "border" => "rgba(0,71,251,1)", "fleet"=> "images/fleets/fleet_blue.png",
					"color_id" => 3
				],
				4 => [
					"background" => "rgba(255,174,0,0.2)", "border" => "rgba(255,174,0,1)", "fleet"=> "images/fleets/fleet_orange.png",
					"color_id" => 4
				],
				5 => [
					"background" => "rgba(162,0,186,0.2)", "border" => "rgba(162,0,186,1)", "fleet"=> "images/fleets/fleet_purple.png",
					"color_id" => 5
				],
				6 => [
					"background" => "rgba(7,245,231,0.2)", "border" => "rgba(7,245,231,1)", "fleet"=> "images/fleets/fleet_cyan.png",
					"color_id" => 6
				],
				7 => [
					"background" => "rgba(255,246,0,0.2)", "border" => "rgba(255,246,0,1)", "fleet"=> "images/fleets/fleet_yellow.png",
					"color_id" => 7
				],
				8 => [
					"background" => "rgba(250,147,254,0.2)", "border" => "rgba(250,147,254,1)", "fleet"=> "images/fleets/fleet_pink.png",
					"color_id" => 8
				]
			];

// resources
// food
// production
// research
// credits

$void_resources = array();
$void_resources['food'] = "food";
$void_resources['production'] = "production";
$void_resources['research'] = "research";
$void_resources['credits'] = "credits";

$void_planet_classes = array();

$void_planet_classes[1] = [
	"name" => "Class M (Terran)",
	"id" => 1,
	"output" => [
		"food" => 1,
		"production" => 1, 
		"research" => 0,
		"credits" => 0
	],
	"morale" => 0, 
	"develop_per_turn" => 0.2, 
	"image" => "images/planets/class_m.png", 
	"image_populated" => "images/planets/class_m_populated.png", 
	"max_population" => 4
];

$void_planet_classes[2] = [
	"name" => "Class J (Jungle)",
	"id" => 2, 
	"output" => [
		"food" => 1,
		"production" => 0, 
		"research" => 1,
		"credits" => 0
	],
	"morale" => 0, 
	"develop_per_turn" => 0.2, 
	"image" => "images/planets/class_j.png", 
	"image_populated" => "images/planets/class_j_populated.png", 
	"max_population" => 4
];

$void_planet_classes[3] = [
	"name" => "Class X (Arid)",
	"id" => 3, 
	"output" => [
		"food" => 0,
		"production" => 2, 
		"research" => 0,
		"credits" => 0
	],
	"morale" => -2, 
	"develop_per_turn" => 0.2, 
	"image" => "images/planets/class_x.png", 
	"image_populated" => "images/planets/class_x_populated.png", 
	"max_population" => 5
];

$void_planet_classes[4] = [
	"name" => "Class P (Ocean)",
	"id" => 4, 
	"output" => [
		"food" => 2,
		"production" => 0, 
		"research" => 0,
		"credits" => 0
	],
	"morale" => -1, 
	"develop_per_turn" => 0.2, 
	"image" => "images/planets/class_p.png", 
	"image_populated" => "images/planets/class_p_populated.png", 
	"max_population" => 5
];

$void_planet_classes[5] = [
	"name" => "Class C",
	"id" => 5, 
	"output" => [
		"food" => 0,
		"production" => 0, 
		"research" => 0,
		"credits" => 2
	],
	"morale" => -1, 
	"develop_per_turn" => 0.2, 
	"image" => "images/planets/class_c.png", 
	"image_populated" => "images/planets/class_c_populated.png", 
	"max_population" => 4
];

$void_planet_classes[6] = [
	"name" => "Class Y",
	"id" => 6, 
	"output" => [
		"food" => 0,
		"production" => 1, 
		"research" => 2,
		"credits" => 0
	],
	"morale" => -2, 
	"develop_per_turn" => 0.2, 
	"image" => "images/planets/class_y.png", 
	"image_populated" => "images/planets/class_y_populated.png", 
	"max_population" => 2
];


$void_sector_classes = array();

$void_sector_classes[1] = [
	"id" => 1,
	"name" => "space",
	"type" => "empty",
	"movement_cost" => 2
];

$void_sector_classes[2] = [
	"id" => 2,
	"name" => "Asteroid Field",
	"image" => "images/asteroid.png",
	"type" => "asteroid",
	"movement_cost" => 3
];


$modifier = new VOID_MODIFIER("attack", "percent", 10);
$modifier->set_scope("fleet");

$void_sector_classes[3] = [
	"id" => 3,
	"name" => "Emmission Nebula",
	"image" => "images/nebula.png",
	"type" => "nebula",
	"movement_cost" => 4,
	"modifiers" => [ $modifier ]
];

$void_sector_classes[4] = [
	"id" => 4,
	"name" => "Proto Nebula",
	"image" => "images/nebula_b.png",
	"type" => "nebula",
	"movement_cost" => 4
];

$void_sector_classes[5] = [
	"id" => 5,
	"name" => "Thermo Nebula",
	"image" => "images/nebula_c.png",
	"type" => "nebula", 
	"movement_cost" => 4
];

$void_sector_classes[6] = [
	"id" => 6,
	"name" => "Black Hole",
	"image" => "images/blackhole.png",
	"type" => "anomaly", 
	"movement_cost" => 10
];

$void_sector_classes[7] = [
	"id" => 7,
	"name" => "Void",
	"image" => "images/void.png",
	"type" => "void", 
	"movement_cost" => 20
];

$void_sector_classes[8] = [
	"id" => 8,
	"name" => "Debris Field",
	"image" => "images/asteroid.png",
	"type" => "asteroid",
	"movement_cost" => 3
];


$void_system_names = [
"Tau", "Sol", "Notts", "Trygve", "Strongem", "Allan", "Camp", 
"Einar", "Cylon", "Caprica", "Trunk", "Cato", "Pike", "Cerberus", "Wotan",
"Odin", "Crichton", "Omega", "Dominus", "Rygel", "Metacol Rex", "Zeus", "Hera", 
"Xena", "Gabrielle", "Lawless", "Nene", "Priss", "Lena", "Sylia", "Yotsuba", 
"Manilla", "Oddish", "Pikachu", "Ash", "Pinkie", "Fluttershy", "Rarity", 
"Cheese", "Carmex", "Oscilon", "Drewan", "Omicron", "Triscolon", "Frodo", 
"Andromeda", "Tyr", "Eli", "Heimdall", "Thor", "Apophis", "Edea", 
"Risa", "Crima", "Muan", "Nerv", "Lansbury", "Gosling", "Damon", "Pine",
"Sharoo", "Lambtron", "Lister", "Esseb", "Quinton", "Quade", "Riker", 
"Crusher", "Worf", "Yar", "Torres", "Neelix", "Nazgul", "Shepard",
"Abydos", "Aegis", "Alderaan", "Athena", "Antar", "Avalon", "Belzagor", 
"Chiron", "Darwin", "Demeter", "Dosadi", "Dothraki", "Eternia", 
"Gaia", "Gallifrey", "Hesikos", "Hydros", "Ishtar", "Ireta", 
"Krull", "Lithia", "Lumen", "Minerva", "Nidor", "Yggdrasil", 
"Pandora", "Pern", "Skaro", "Takis", "Thundera", "Tiamat", "Tirol", "Titan",
"Vekta", "Zahir", "Zeist", "Blostrupmoen", "Clements", "Leder", "Rist", 
"Sondre", "Dyson", "Niobium", "Parker", "Hardison", "Chaos", "Drakon",
"Baratheon", "Armitage", "Tyrell", "Lannister", "Stark", "Targaryn", 
"Hyrule", "Brimstar", "Zebes", "Tyreal", "Litago", "Etterstad", "Luna", 
"Celestia"
];









?>