
	

var player;
var players;

var hex_path;

var planet_class_cache;
var sector_class_cache;
var ship_class_cache;
var structure_class_cache;
var tech_tree;

var fleet_cache = {};

var current_tech;

var mouse_down_event;
var scroll_in_progress;
var map_scroll_offset;
var map_buffer = 800;
var map_chunk_size = {};
var map_canvas_buffer_offset = null;

var panel_view = "";

var fleet_orders = new Object();
var fleet_order_mode = false;
var fleet_order_start_hex = new Object();

var fleet_selected;

var system_orders = new Object();

var first_load = false;
var first_loading_complete = false;

var waiting_for_players = false;

$(document).ready(function (){
	Handlebars.registerHelper('compare', function (lvalue, operator, rvalue, options) {

    var operators, result;
    
    if (arguments.length < 3) {
        throw new Error("Handlerbars Helper 'compare' needs 2 parameters");
    }
    
    if (options === undefined) {
        options = rvalue;
        rvalue = operator;
        operator = "===";
    }
    
    operators = {
        '==': function (l, r) { return l == r; },
        '===': function (l, r) { return l === r; },
        '!=': function (l, r) { return l != r; },
        '!==': function (l, r) { return l !== r; },
        '<': function (l, r) { return l < r; },
        '>': function (l, r) { return l > r; },
        '<=': function (l, r) { return l <= r; },
        '>=': function (l, r) { return l >= r; },
        'typeof': function (l, r) { return typeof l == r; }
    };
    
    if (!operators[operator]) {
        throw new Error("Handlerbars Helper 'compare' doesn't know the operator " + operator);
    }
    
    result = operators[operator](lvalue, rvalue);
    
    if (result) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }

	});
	
	
	Handlebars.registerPartial("planet", $("#planet_template").html());
	Handlebars.registerPartial("ship_class", $("#ship_class_template").html());
	Handlebars.registerPartial("structure_class", $("#structure_class_template").html());		
	
	fetch_game_data(true);
	// fetch map data from the server
	
	// fetch all other game data they need and store it in a javascript object
	
	// this data is used so it hardly ever has to request data from the server other than between turns
	
	//$('#main_loading').hide();
});







function update_interface(){
	if (player){
		if (player.current_tech && player.research_per_turn){
			player.research_time = Math.ceil(player.current_tech.progress / player.research_per_turn);
		}
		append_template("player_resources_template", player, "player_resources");
	}
	if (panel_view == "system" && hex_selected && hex_selected.x){
		show_map_panel_system(hex_selected);	
	}
	render_research();
}



var minimap_drag = false;
function start_minimap_drag(x, y){
	minimap_drag = true;
}
function end_minimap_drag(x, y){
	// in case it was a single click and no drag, move the view point to the cursor
	minimap_move(x, y);
	minimap_drag = false;
}
function minimap_move(x, y){
	if (minimap_drag){
		//mouse_move_event({"pageX": , "});
		//jump_map(x * map_viewport.factorWidth  - $('#galactic_map_container').width()/2, y * map_viewport.factorHeight  - $('#galactic_map_container').height()/2);
		//$('#galactic_map_container').scrollLeft(x*map_viewport.factorWidth*map_scale - ($('#galactic_map_container').width() / 2));
		//$('#galactic_map_container').scrollTop(y*map_viewport.factorHeight*map_scale - ($('#galactic_map_container').height() / 2));
		draw_minimap_overlay();
	}
}


function update_map(){
	
}




function fetch_template(id, data){
	var source = $('#'+id).html();
	var template = Handlebars.compile(source);
	
	var html = template(data);
	return html;
}

function append_template(template_id, data, element_id){
	$("#"+element_id).hide();
	$("#"+element_id).empty();
	var html = fetch_template(template_id, data);	
	$("#"+element_id).append(html);
	
	$('.tooltip-template', $("#"+element_id)).each(function() {
		var selector = '#' + $(this).data('tooltip-id');		
		$(this).tooltipsy({
			content: $(selector).html(),
			delay : 700,
			alignTo: 'element',
			offset: [0, 1], 
			show: function (e, $el) {
        $el.css({
            'display': 'block'
        });
        if ($($el).position().left + $($el).width() > $(window).width()){
        	$el.css({
            'left': $($el).position().left - ($($el).position().left + $($el).width() - $(window).width() ) - 10
        });
        }
        if ($($el).position().top + $($el).height() > $(window).height()){
        	$el.css({
            'top': $($el).position().top - ($($el).position().top + $($el).height() - $(window).height() ) - 40
        });
        }
    	}
		});
	});
	$('.tooltip-title', $("#"+element_id)).each(function() {
		$(this).tooltipsy({
			content: $(this).attr('title'),
			delay : 700,
			alignTo: 'element'
		});
	});
	$("#"+element_id).show();
}

function change_view(view_id){
	$('.view').hide();
	if (view_id == "research"){
		render_research();
	}
	
	$('#'+view_id+'_view').show();
}


function render_research(){
	var temp_obj = new Object();
	temp_obj['tech_tree'] = tech_tree;
	temp_obj['player'] = player;
	temp_obj['tiers'] = {};
	$.each(tech_tree.items, function(){
		this.player_has = null;
		if (player.tech[this.id]){
			this.player_has = player.tech[this.id];
		}
		this.player_available = null;
		if (player.available_tech[this.id]){
			this.player_available = player.available_tech[this.id];
		}
		if (player.current_tech && player.current_tech.class.id == this.id){
			this.player_current = true;
		}else {
			this.player_current = false;
		}
		if (!temp_obj['tiers'][this.tier]){
			temp_obj['tiers'][this.tier] = [];
		}
		temp_obj['tiers'][this.tier].push(this);
	});
	append_template("tech_tree_template",temp_obj,"tech_tree_items");
}

function show_map_panel_system(hex){
	panel_view = "system";
	fleet_selected = false;
	$("#galactic_map_panel").show();
	$("#galactic_map_panel_fleet").hide();
	$("#galactic_map_panel_content").empty();			
	var hex = hex_map['x'+hex.x+'z'+hex.z];
	if (hex.owner){
		hex.owner_object = players[hex.owner];
	}
	if (hex.system && hex.system.planets){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];
		});
	}
	if (hex.system && hex.system.build_queue && hex.system.build_queue.items){
		console.log(this);
		$.each(hex.system.build_queue.items, function (){
			console.log(this);
			if (this.type == "ship"){
				this.target = ship_class_cache[this.target_id];
			}else if (this.type == "structure"){
				this.target = structure_class_cache[this.target_id];
			}
			
		});
	}
	if (hex.class_id && hex.type){
		hex.class = sector_class_cache[hex.class_id];		
	}
	
	if (hex.your_fleets){
		$.each(hex.your_fleets, function(){
			if (this.ships && this.ships){
				$.each(this.ships, function (){
					this.class = ship_class_cache[this.class_id];					
				});
			}
			show_map_panel_fleet(this.id);
		});		
	}
	
	redraw_overlay();
	
	append_template('galactic_map_panel_system_template',hex, "galactic_map_panel_content");
	var html = fetch_template('galactic_map_panel_system_template',hex);
	if (hex.your_fleets && hex.your_fleets.length > 0){
		map_context_menu_type = "default";
	}else {
		map_context_menu_type = "default";
	}
}


function show_map_panel_fleet(id){
	$("#galactic_map_panel_fleet").show();
	//panel_view = "fleet";
	//$("#galactic_map_panel_content").empty();
	//start_fleet_order(id);
	var fleet = fleet_cache[id];
	var hex = get_hex(fleet.x, fleet.z);
	hex_selected = hex;
	fleet_selected = id;

	if (!fleet){
		return;
	}
	if (fleet.owner){
		fleet.owner_object = players[fleet.owner];
	}
	if (fleet.ships && fleet.ships){
		$.each(fleet.ships, function (){
			this.class = ship_class_cache[this.class_id];
		});
	}
	redraw_overlay();
	append_template('galactic_map_panel_fleet_template',fleet, "galactic_map_panel_fleet");
	map_context_menu_type = "default";
}

function show_system_view(x, z){
	clean_up_tooltips();
	$("#system_view_window .dialog_content").empty();		
	var hex = hex_map['x'+x+'z'+z];
	if (hex.system){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];
		});
	}
	if (hex.system && hex.system.build_queue && hex.system.build_queue.items){
		$.each(hex.system.build_queue.items, function (){
			if (this.type == "ship"){
				this.target = ship_class_cache[this.target_id];
			}else if (this.type == "structure"){
				this.target = structure_class_cache[this.target_id];
			}
		});
		
		hex.system.available_structure_classes = [];
		$.each(hex.system.available_structures, function (){
			if (structure_class_cache[this]){
				var structure_id = this;
				var in_queue = false;
				// check the orders they have made this turn also
				if (system_orders[hex.system.id]){
					$.each(system_orders[hex.system.id], function (){
						if (this.target_id == structure_id){
				 			in_queue = true;
						}
					});
				}
				if (!in_queue){
					hex.system.available_structure_classes.push(structure_class_cache[this]);
				}
			}
		});
		
		if (hex.system.structures){
			$.each(hex.system.structures, function (){
				if (structure_class_cache[this.class_id]){				
					this.class = structure_class_cache[this.class_id];
				}
			});
		}
	}	
	//var html = fetch_template('system_view_template',hex);
	append_template('system_view_template',hex, "dialog_content");
	//$("#dialog_content").append(html);
	$("#dialog_title").html('System at '+x+','+z);
	
	$("#system_view_window").show();
	$(".selector_list").customScrollbar({
		hScroll: false,
		vScroll: true, 
		skin: "default-skin"	
	});		
}


function show_system_view_basic(x, z){
	clean_up_tooltips();
	$("#system_view_window .dialog_content").empty();		
	var hex = hex_map['x'+x+'z'+z];
	if (hex.system){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];
			this.selectable = true;
		});
	}
	var html = fetch_template('system_view_basic_template',hex);		
	$("#system_view_window .dialog_content").append(html);
	$("#dialog_title").html('System at '+x+','+z);
	$("#system_view_window").show();			
}

function show_context_menu(id, x, y, type){
	
	var map_x = x - $("#galactic_map_overlay").offset().left;
	var map_y = y - $("#galactic_map_overlay").offset().top
	
	$("#"+id).css({"left": x-4, "top" : y-4});
	$("#"+id).empty();
	if (type){
		if (type == "system_planet_menu"){
			var html = fetch_template('system_planet_context_menu');
		}
	}else {
		if (map_context_menu_type == "fleet_move"){
			var html = fetch_template('map_context_menu_fleet_move');		
		}else if (map_context_menu_type == "default"){
			var coords = pixel_to_hex(map_x,map_y);
			var html = fetch_template('map_context_menu_default', hex_map["x"+coords[0]+"z"+coords[2]]);	
		}else {
			return false;
		}
	}	
	$("#"+id).append(html);
	$("#"+id).show();
}


function clean_up_tooltips(){
	$('.tooltipsy').hide();
}

function context_menu(event, params){
	show_context_menu('galactic_map_context_menu', event.pageX, event.pageY, 'system_planet_menu');
	event.preventDefault();
}

function start_fleet_order_mode(id){
	// get the current fleet details 
	var fleet = fleet_cache[id];
	
	map_context_menu_type = "default";
	map_right_click = "fleet_move";

	if (fleet_orders[id]){
		fleet_order_start_hex = fleet_orders[id][fleet_orders[id].length-1];
	}else {
		fleet_order_start_hex = get_hex(fleet.x,fleet.z);
	}
	
	// set the hex highlight path start points
	fleet_order_mode = true;
	console.log(fleet_order_start_hex);
	console.log(fleet);
	console.log("banana");
	redraw_overlay();
}
function end_fleet_order_mode(id){
	fleet_order_mode = false;
	map_context_menu_type = "default";
	map_right_click = "";
	redraw_overlay();
}

function add_fleet_order(start, end){
	// add path of hexes to order list
	
	var path = get_path_between_hexes(start, end, fleet_cache[fleet_selected].movement_capacity);
	if (path){
		for (var i = 0; i < path.length ; i++){
			if (path[i]){
				if (!fleet_orders[fleet_selected]){
					fleet_orders[fleet_selected] = new Array();
				}
				var order = {'type':'move', 'x':path[i].x, 'z':path[i].z};
				fleet_orders[fleet_selected].push(order);
			}
		}
	}
	fleet_order_start_hex = end;
}

function add_fleet_colonise_order_dialog(){
	if (fleet_orders[fleet_selected] && fleet_orders[fleet_selected].length > 0){
		var previous_order = fleet_orders[fleet_selected][fleet_orders[fleet_selected].length-1];
	}else {
		var previous_order = {'x':fleet_cache[fleet_selected].x , 'z':fleet_cache[fleet_selected].z};		
	}
	show_system_view_basic(previous_order.x, previous_order.z);
}

function add_fleet_colonise_order(pid){
	if (fleet_orders[fleet_selected] && fleet_orders[fleet_selected].length > 0){
		var previous_order = fleet_orders[fleet_selected][fleet_orders[fleet_selected].length-1];
	}else {
		var previous_order = {'x':fleet_cache[fleet_selected].x , 'z':fleet_cache[fleet_selected].z};
		fleet_orders[fleet_selected] = [];
	}
	var order = {'type':'colonise', "x":previous_order.x, "z":previous_order.z, "planet_id": pid};
	fleet_orders[fleet_selected].push(order);
	
	redraw_overlay();
}

function cancel_fleet_orders(){
	fleet_orders[fleet_selected] = new Array();
}

function select_tech(id){
	if (!player.available_tech[id]){
		return false;
	}
	current_tech = id;
	player.current_tech = player.available_tech[id];
	update_interface();
	render_research();
}


function build_ship(ship_class_id){
	var system = hex_map['x'+hex_selected.x+'z'+hex_selected.z].system;
	
	var ship_class = ship_class_cache[ship_class_id];
	
	var item = new Object();
	item.type = "ship";
	item.target_id = ship_class_id;
	item.progress = ship_class.work_required;
	if (system.production_per_turn){
		item.turns = Math.ceil(item.progress / system.production_per_turn);
		
	}else {
		item.turns = "1000";
	}
	var d = new Date();
	item.id = Math.random().toString(36).substr(4)+hex_selected.x+hex_selected.z+system.build_queue.items.length;
	item.order = system.build_queue.items.length;
	//alert(system.build_queue.items.length);
	system.build_queue.items[system.build_queue.items.length] = item;
	
	if ($("#system_view").is(":visible")){
		show_system_view(hex_selected.x, hex_selected.z);
	}
	show_map_panel_system(hex_selected)
	if (!system_orders[system.id]){
		system_orders[system.id] = new Object();
	}
	system_orders[system.id][item.id] = item;
}

function build_structure(structure_class_id){
	var system = hex_map['x'+hex_selected.x+'z'+hex_selected.z].system;
	
	var structure_class = structure_class_cache[structure_class_id];
	
	var item = new Object();
	item.type = "structure";
	item.target_id = structure_class_id;
	item.progress = structure_class.work_required;
	if (system.production_per_turn){
		item.turns = Math.ceil(item.progress / system.production_per_turn);
		
	}else {
		item.turns = "1000";
	}
	var d = new Date();
	item.id = Math.random().toString(36).substr(4)+hex_selected.x+hex_selected.z+system.build_queue.items.length;
	item.order = system.build_queue.items.length;
	//alert(system.build_queue.items.length);
	system.build_queue.items[system.build_queue.items.length] = item;
			
	if (!system_orders[system.id]){
		system_orders[system.id] = new Object();
	}
	system_orders[system.id][item.id] = item;
	if ($("#system_view").is(":visible")){
		show_system_view(hex_selected.x, hex_selected.z);
	}
	show_map_panel_system(hex_selected);
}





// convert a mouseover or mouseclick event to hex coords and redraw the needed hexes
function click_to_hex(x, y, param, event){
	
	var coords = pixel_to_hex(x,y);
	//console.log(coords);
	if (param == "click"){		
		if (fleet_order_mode == true){
			
			add_fleet_order(fleet_order_start_hex, {'x':coords[0], 'z':coords[2]});
			if (event && event.ctrlKey){
				// if ctrl key pressed then waypoint mode
			}else {
				end_fleet_order_mode(fleet_selected);
			}
			
		}else {
			fleet_order_mode = false;
			
			hex_selected = get_hex(coords[0], coords[2]);

			hex_hover_history.push(hex_selected);
			if (hex_hover_history.length > 5){
				hex_hover_history.pop();
			}
			redraw_overlay();
			show_map_panel_system(hex_selected);
			console.log(hex_map['x'+coords[0]+'z'+coords[2]]);			
		}
		
	}else if (param == "ctrl_click"){
		
	}else if (param == "double_click"){
		console.log("double click");			
		hex_selected = get_hex(coords[0], coords[2]);
		redraw_overlay();
		show_system_view(hex_selected.x, hex_selected.z);
	}else {
		if (hex_highlighted.x != coords[0] || hex_highlighted.z != coords[2]){
			hex_highlighted = get_hex(coords[0], coords[2]);
			//draw_map("galactic_map");
			if (hex_map['x'+coords[0]+'z'+coords[2]]){
				redraw_overlay();
			}
		}
	}
}





function galactic_map_panel_system_data($scope){
	$scope.name = function (){
		return "hey";
	}
}

function reset_game(){
	$.get("main.php"+location.search,{"action":"reset"},handle_end_turn);
}

function fetch_game_data(first){
	// call a function to the remote server to get all the game data.
	var url = "main.php";
	var params = {};
	if (location.search){
		url += location.search;
	}else {
		params.player_id = 1;
	}
	if (first){
		params.first = true;
	}
	$.get(url,params,handle_fetch_game_data);

}
function create_fake_game_data(){
	var data = new Object();
	var width = 18;
	var height = 18;
	var hex_map2 = new Object();
	for (var z = 0; z < height; z++){
		for (var x = -Math.floor(z/2); x < width - Math.floor(z/2); x++){
			
			hex_map2['x'+x+'z'+z] = {'x':x,'z':z};
			if (x < 3 || x > 10 || z < 5 || z > 16){
				hex_map2['x'+x+'z'+z].unknown = 1;
			}else {
				if (Math.random() < 0.2){
					hex_map2['x'+x+'z'+z].star = 1;
					hex_map2['x'+x+'z'+z].planets = new Array();
					var system_size = 0;
					var max_system_size = 0;
					for (var i = 0; i < Math.random()*6+1; i++){
						hex_map2['x'+x+'z'+z].planets[i] = {
							'name' : 'Planet '+i,
							'class' : 'M',
							'population' : 3
						}
						system_size += 3;
						max_system_size += 5;
					}
					hex_map2['x'+x+'z'+z].size = system_size;
					hex_map2['x'+x+'z'+z].max_size = max_system_size;
				}
				if (Math.random() < 0.2){
					hex_map2['x'+x+'z'+z].fleet = 1;
					hex_map2['x'+x+'z'+z].fleets = new Array();
					for (var i = 1; i < Math.random()*2+1; i++){
						hex_map2['x'+x+'z'+z].fleets[i] = new Object();
					}
				}
				if (x > 5 && x < 10 && z > 5 && z < 10){
					hex_map2['x'+x+'z'+z].friendly = 1;					
				}else if (Math.random() < 0.2){
					hex_map2['x'+x+'z'+z].enemy = 1;
				}
			} 
		}
	}
	data.map = hex_map2;
	data.map_width = width;
	data.map_height = height;
	
	// the player object for the logged in players
	data.player = new Object();
	data.player.name = 'Kam';
	data.player.home = {'x':7, 'z': 8};
	data.player.resources = 'No Resources!';
	data.players = new Object();
	
	data.tech = new Object();
	data.ships = new Object();
	data.planets = new Object();
	
	
	return data;
}

function handle_fetch_game_data(data){
	if (data){

		fleet_orders = new Object();
		hex_map = data.map.sectors;
		player = data.player;
		players = data.players;
		if (data.planet_classes){
			planet_class_cache = data.planet_classes;
		}
		if (data.ship_classes){
			ship_class_cache = data.ship_classes;
		}
		if (data.structure_classes){
			structure_class_cache = data.structure_classes;
		}
		if (data.sector_classes){
			sector_class_cache = data.sector_classes;
		}
		if (data.tech_tree){
			tech_tree = data.tech_tree;
		}
		if (!first_loading_complete){
			preload_images(data, game_update);
			first_loading_complete = true;
		}else {
			game_update(data);
		}
	}
}

function game_update(data){
	initialize_map(data.map.map_width, data.map.map_height);
	update_interface();
	redraw_overlay();
	if (!player.done){
		$("#end_turn_button").html('<img src="images/ajax-loader.png" style="visibility: hidden;"> End Turn ');
		$("#end_turn_button").attr("disabled", false);
	}else {
		status_check();
		$("#end_turn_button").html('<img src="images/ajax-loader.png"> End Turn ');
		$("#end_turn_button").attr("disabled", true);
	}
	$('#main_loading').hide();
}


function status_check(){
	$.get("main.php"+location.search,{'action':'status'},handle_status_check);	
}
function handle_status_check(data){
	if (data && data.player){
		if (data.player.done == false){
			fetch_game_data();
			return;
		}
	}
	setTimeout(status_check, 4000);
}

function end_turn(){
	// send all committed orders to the server
	$("#end_turn_button").attr("disabled", true);
	$("#end_turn_button").html('<img src="images/ajax-loader.png"> End Turn ');
	$.post("main.php"+location.search,{'action':'end_turn', 'fleet_orders':fleet_orders, 'system_orders':system_orders, 'current_tech':current_tech},handle_end_turn);
	system_orders = new Object();
	current_tech = null;
}

function handle_end_turn(data){
	console.log(data);
	handle_status_check(data);
}

