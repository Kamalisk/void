
	

var player;
var players;

var hex_path;

var planet_class_cache;
var sector_class_cache;
var ship_class_cache;
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
	
	fetch_game_data();
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
		jump_map(x*map_viewport.factorWidth*map_scale, y*map_viewport.factorHeight*map_scale);
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
		$.each(hex.system.build_queue.items, function (){
			this.target = ship_class_cache[this.target_id];
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
	panel_view = "fleet";
	$("#galactic_map_panel_content").empty();
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
	append_template('galactic_map_panel_fleet_template',fleet, "galactic_map_panel_content");
	map_context_menu_type = "default";
}

function show_system_view(x, z){	
	$("#system_view_window .dialog_content").empty();		
	var hex = hex_map['x'+x+'z'+z];
	if (hex.system){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];			
		});
	}
	if (hex.system && hex.system.build_queue && hex.system.build_queue.items){
		$.each(hex.system.build_queue.items, function (){
			this.target = ship_class_cache[this.target_id];
		});
	}	
	var html = fetch_template('system_view_template',hex);		
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


function context_menu(event, params){
	show_context_menu('galactic_map_context_menu', event.pageX, event.pageY, 'system_planet_menu');
	event.preventDefault();
}

function start_fleet_order_mode(id){
	// get the current fleet details 
	var fleet = fleet_cache[id];
	
	// set the hex highlight path start points
	fleet_order_mode = true;
	
	map_context_menu_type = "default";
	map_right_click = "fleet_move";

	if (fleet_orders[id]){
		fleet_order_start_hex = fleet_orders[id][fleet_orders[id].length-1];
	}else {
		fleet_order_start_hex = get_hex(fleet.x,fleet.z);
	}
	
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
				var order = {'type':'move', 'x':path[i].x, 'z':path[i].z}
				if (fleet_orders[fleet_selected].length < 3){
					order.this_turn = true;
				}
				fleet_orders[fleet_selected][fleet_orders[fleet_selected].length] = order;
			}
		}
	}
	fleet_order_start_hex = end;
}

function add_fleet_colonise_order(){
	var previous_order = fleet_orders[fleet_selected][fleet_orders[fleet_selected].length-1];
	var order = {'type':'colonise', "x":previous_order.x, "z":previous_order.z};
	fleet_orders[fleet_selected][fleet_orders[fleet_selected].length] = order;
	//console.log(fleet_orders);
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
	item.type = "build";
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


function get_hex(x, z){
	return hex_map["x"+x+"z"+z];
}



/* galactic map drawing funtions */




// convert a mouseover or mouseclick event to hex coords and redraw the needed hexes
function click_to_hex(x, y, param, event){
	
	var coords = pixel_to_hex(x,y);
	//console.log(coords);
	if (param == "click"){		
		if (panel_view == "fleet" && fleet_order_mode == true){
			
			add_fleet_order(fleet_order_start_hex, {'x':coords[0], 'z':coords[2]});
			if (event && event.ctrlKey){
				// if ctrl key pressed then waypoint mode
			}else {
				end_fleet_order_mode(fleet_selected);
			}
			
		}else {
			fleet_order_mode = false;
			if (hex_selected.x != coords[0] || hex_selected.z != coords[2]){
				hex_selected.x = coords[0];
				hex_selected.z = coords[2];
				hex_hover_history.push(hex_selected);
				if (hex_hover_history.length > 5){
					hex_hover_history.pop();
				}
				redraw_overlay();
				show_map_panel_system(hex_selected);
				console.log(hex_map['x'+coords[0]+'z'+coords[2]]);
			}
		}
		
	}else if (param == "ctrl_click"){
		
	}else if (param == "double_click"){
		if (fleet_order_mode){
			end_fleet_order_mode(fleet_selected);
		}
	}else {
		if (hex_highlighted.x != coords[0] || hex_highlighted.z != coords[2]){
			hex_highlighted.x = coords[0];
			hex_highlighted.z = coords[2];
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

function fetch_game_data(){
	// call a function to the remote server to get all the game data.
	if (location.search){		
		$.get("main.php"+location.search,{},handle_fetch_game_data);
	}else {
		$.get("main.php",{'player_id':1},handle_fetch_game_data);
	}
	// for now fake the data
	//var data = create_fake_game_data();
	//handle_fetch_game_data(data);
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
		planet_class_cache = data.planet_classes;
		ship_class_cache = data.ship_classes;
		sector_class_cache = data.sector_classes;
		tech_tree = data.tech_tree;
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


//
// galactic map drawing functions
//

function draw_hex(canvas, x, y, size, color, fill, layer){
	var options = {
		strokeStyle: color,
	  strokeWidth: 2,
	  x: x, y: y,
	  radius: size,
	  sides: 6,
	  rotate : 30
	};
	if (fill != "none"){
		options['fillStyle'] = fill; 
	}
	if (layer){
		options['layer'] = true; 
		options['name'] = layer; 
	}
	$(canvas).drawPolygon(options);
}

function redraw_overlay(){
	
	var canvas = document.getElementById("galactic_map_overlay");
	var overlay_ctx = canvas.getContext('2d');
	overlay_ctx.save();
	overlay_ctx.translate(map_buffer, map_buffer);
	overlay_ctx.scale(map_scale,map_scale);
	$(canvas).clearCanvas();
	if (hex_highlighted && hex_map['x'+hex_highlighted.x+'z'+hex_highlighted.z]){
		var coords = hex_to_pixel(hex_highlighted, map_scroll_offset);
		draw_hex(canvas, coords.x,  coords.y, hex_size, "orange", "none");
	}
	if (hex_selected && hex_map['x'+hex_selected.x+'z'+hex_selected.z]){
		var coords = hex_to_pixel(hex_selected, map_scroll_offset);
		draw_hex(canvas, coords.x,  coords.y, hex_size-1, "red", "none");
	}
	
	var fleet_order_turn_counter = 0;
	var fleet_order_movement_counter = 0;
	var last_hex = null;
	// draw the current fleet orders
	if (panel_view == "fleet" && fleet_selected && fleet_orders && fleet_orders[fleet_selected]){
		
			$.each(fleet_orders[fleet_selected], function (key2, value){
				if (value.type == "move"){
					var hex = get_hex(value.x, value.z);
					var coords = hex_to_pixel(value, map_scroll_offset);
					fleet_order_movement_counter += hex.movement_cost;
										
					if(fleet_order_movement_counter > fleet_cache[fleet_selected].movement_capacity - fleet_order_movement_counter){
						fleet_order_movement_counter = 0;
						fleet_order_turn_counter++;						
					}
					if (fleet_order_turn_counter <= 0){
						draw_hex(canvas, coords.x,  coords.y, hex_size, "orange", "rgba(240, 150, 40, 0.2)");
					}else {
						draw_hex(canvas, coords.x,  coords.y, hex_size, "yellow", "rgba(240, 240, 40, 0.2)");
					}
					$(canvas).drawText({
					  fillStyle: "black",
					  strokeStyle: "black",
					  strokeWidth: 2,
					  x: coords.x, y: coords.y+20,
					  fontSize: "14pt",
					  fontFamily: "Verdana, sans-serif",
					  text: fleet_order_turn_counter+1
					});
					last_hex = coords;
				}else if (value.type == "colonise"){
					var coords = hex_to_pixel(value, map_scroll_offset);
					draw_hex(canvas, coords.x,  coords.y, hex_size-5, "pink", "rgba(240, 150, 40, 0.2)");
				}
			});
	
	}	
	
	if (panel_view == "fleet" && fleet_selected && fleet_order_mode && fleet_order_start_hex && hex_highlighted && get_hex(hex_highlighted.x,hex_highlighted.z) ){
		var path = get_path_between_hexes(fleet_order_start_hex, hex_highlighted, fleet_cache[fleet_selected].movement_capacity);
		//console.log(hex_highlighted);
		//var path = false;		
		if (path){
			for (var i = 0; i < path.length; i++){
				if (path[i]){
					var coords = hex_to_pixel(path[i], map_scroll_offset);
					
					fleet_order_movement_counter += path[i].movement_cost;
															
					if(fleet_order_movement_counter > fleet_cache[fleet_selected].movement_capacity - fleet_order_movement_counter){
						fleet_order_movement_counter = 0;
						fleet_order_turn_counter++;								
					}						
					last_hex = coords;
					if (fleet_order_turn_counter <= 0){
						draw_hex(canvas, coords.x,  coords.y, hex_size, "orange", "rgba(255, 150, 40, 0.5)");
					}else {
						draw_hex(canvas, coords.x,  coords.y, hex_size, "yellow", "rgba(255, 255, 40, 0.5)");
					}
					$(canvas).drawText({
					  fillStyle: "black",
					  strokeStyle: "black",
					  strokeWidth: 2,
					  x: coords.x, y: coords.y+20,
					  fontSize: "14pt",
					  fontFamily: "Verdana, sans-serif",
					  text: fleet_order_turn_counter+1
					});			
				}
			}
		}
	}

	overlay_ctx.restore();
	
}


function draw_minimap_overlay(){
	$("#galactic_map_mini_overlay").clearCanvas();
	$("#galactic_map_mini_overlay").drawRect({
	  'x': -map_scroll_offset.x + (-$("#galactic_map_hexes").position().left - map_buffer)/map_scale, 'y': -map_scroll_offset.y + (-$("#galactic_map_hexes").position().top)/map_scale - map_buffer,
	  width: $('#galactic_map_container').width() / map_scale,
	  height: $('#galactic_map_container').height() / map_scale,
	  strokeStyle : "white",
	  strokeWidth : 30,
	  fromCenter: false
	});
	
}



function redraw_map_section(x, y){
	
	var canvas_overlay = document.getElementById("galactic_map_overlay");
	//$(canvas_overlay).clearCanvas();
	var canvas = document.getElementById("galactic_map");
	var coords = pixel_to_hex(x, y);
	var hex = hex_map['x'+coords[0]+'z'+coords[2]];
	
	$(canvas).clearCanvas({
	    x: x, y: y,
	    width: hex_size*2,
	    height: hex_size*2
	});
	
	for (var direction = 0; direction < 6; direction++){
		var adj_hex = get_adjacent_hex(hex, direction);
		var i = adj_hex.x;
		var j = adj_hex.z;
		var x = hex_size * Math.sqrt(3) * (i + j/2);
		var y = hex_size * 3/2 * j;
		draw_hex(canvas, x+hex_size,  y+hex_size, hex_size, "white", "none");
	}
	var i = hex.x;
	var j = hex.z;
	var x = hex_size * Math.sqrt(3) * (i + j/2);
	var y = hex_size * 3/2 * j;
	draw_hex(canvas, x+hex_size,  y+hex_size, hex_size, "orange", "none");

}


function draw_map(map_id, first, custom_offset){
	var offset = map_scroll_offset;
	if (custom_offset){
		offset = custom_offset;
	}
	
	var bound = {
		"lower_x": -map_buffer * 2,
		"upper_x": map_chunk_size.x + map_buffer,
		"lower_y": -map_buffer * 2,
		"upper_y": map_chunk_size.y + map_buffer
	};
	
	console.log(offset.x);
	console.log(offset.y);
	
	var canvas = document.getElementById(map_id+"_hexes");
	var canvas_overlay = document.getElementById(map_id+"_overlay");
	var canvas_objects = document.getElementById(map_id+"_objects");

	//$(canvas_overlay).clearCanvas();
	$(canvas_objects).clearCanvas();
	$(canvas).clearCanvas();
	var ctx = canvas.getContext('2d');
	var objects_ctx = canvas_objects.getContext('2d');
	//var overlay_ctx = canvas_overlay.getContext('2d');
	ctx.save();
	objects_ctx.save();
	
	var mini_canvas = $('#galactic_map_mini');
	var mini_canvas_overlay = $('#galactic_map_mini_overlay');
	
	ctx.translate(map_buffer,map_buffer);
	objects_ctx.translate(map_buffer,map_buffer);
	
	ctx.scale(map_scale,map_scale);
	objects_ctx.scale(map_scale,map_scale);
	
	var hexes_to_draw = [];
	
	for(key in hex_map){
		var hex = hex_map[key];
		// first pre-compute the pixel coordinates of the hexes to save computing them lots later
		var coords = hex_to_pixel(hex, offset);
		hex.pixel_x = coords.x;
		hex.pixel_y = coords.y;
		if ( first || (hex.pixel_x > bound.lower_x && hex.pixel_y > bound.lower_y && hex.pixel_x < bound.upper_x && hex.pixel_y < bound.upper_y) ){
			hexes_to_draw.push(hex);
		}
	}
	
	for(var i = 0; i < hexes_to_draw.length; i++){
		draw_map_tile(canvas, hexes_to_draw[i]);
		if (first){
			draw_map_tile(mini_canvas, hexes_to_draw[i], true);
		}
	}
	
	for(var i = 0; i < hexes_to_draw.length; i++){
		var hex = hexes_to_draw[i];
		draw_tile_overlay(canvas, hex);
		if (first){
			draw_tile_overlay(mini_canvas, hex, true);
		}
	}
	
	// draw objects
	for(var i = 0; i < hexes_to_draw.length; i++){
		var hex = hexes_to_draw[i];
		if (hex.star){
			$(canvas_objects).drawImage({
			  source: "images/default.png",
			  x: hex.pixel_x, y: hex.pixel_y
			});
		}
		if (hex.your_fleets && hex.your_fleets.length > 0){
			
			// really needs to be temporary
			$.each(hex.your_fleets, function (){
				fleet_cache[this.id]  = this;
			});
			
			$(canvas_objects).drawImage({
			  source: "images/fleet_f.png",
			  x: hex.pixel_x-20, y: hex.pixel_y-25
			});
			
			if (hex.your_fleets[0].orders.length > 0){				
				fleet_orders[hex.your_fleets[0].id] = hex.your_fleets[0].orders;
			}
		}
		if (hex.enemy_fleets.length > 0){
			$(canvas_objects).drawImage({
			  source: "images/fleet_e.png",
			  x: hex.pixel_x+20, y: hex.pixel_y-25
			});
		}
		if (hex.system && hex.system.population){
			$(canvas_objects).drawText({
			  fillStyle: "black",
			  strokeStyle: "black",
			  strokeWidth: 2,
			  x: hex.pixel_x, y: hex.pixel_y,
			  fontSize: "14pt",
			  fontFamily: "Verdana, sans-serif",
			  text: hex.system.population
			});
			if (hex.system.owner){
				$(canvas_objects).drawEllipse({
				  strokeStyle : players[hex.system.owner].color.border, 
				  strokeWidth : 4,
				  x: hex.pixel_x, y: hex.pixel_y,
				  width: 32, height: 32
				});
			}
		}
	}
	
	ctx.restore();
	objects_ctx.restore();
}

function draw_tile_overlay(canvas, map_tile, small){
	if (map_tile.friendly){
		if (small){
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size-5, "none", "rgba(40,255,40,1)");
		}else {
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", "rgba(70,255,70,0.3)");
			//draw_hex_borders(canvas, map_tile.pixel_x, map_tile.pixel_y, map_tile, "friendly");
		}
	}else if (map_tile.enemy){
		if (small){
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size-5, "none", "rgba(165,40,40,1)");
		}else {
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", "rgba(205,70,70,0.3)");
			//draw_hex_borders(canvas, map_tile.pixel_x, map_tile.pixel_y, map_tile, "enemy");
		}
	}
	
	if (players[map_tile.owner]){
		if (small){
			draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size-5, "none", players[map_tile.owner].color.border);
		}else {
			draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", players[map_tile.owner].color.background);
			if (map_tile.fog){
				draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", "rgba(80,80,80,0.4)");
			}
			draw_hex_borders(canvas, map_tile.pixel_x, map_tile.pixel_y, map_tile, "friendly");
		}
	}
	
}


function draw_map_tile(canvas, map_tile, small){

	var x = map_tile.pixel_x;
	var y = map_tile.pixel_y;
	
	if (map_tile.unknown){
		if (small){
			//draw_hex(canvas, x,  y, hex_size+1, "none", "#3f3f3f");
		}else {
			draw_hex(canvas, x,  y, hex_size, "rgba(0,0,0,0.1)", "rgba(156,156,156,0.2)");
		}
	}else {
		if (!small){
			if (map_tile.type == "asteroid"){
				$(canvas).drawImage({
				  source: "images/asteroids.png",
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: sector_class_cache[map_tile.class_id].image,
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: sector_class_cache[map_tile.class_id].image,
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: "images/nebula_c.png",
				  x: x, y: y
				});
			}
			if (map_tile.fog){
				draw_hex(canvas, x,  y, hex_size, "rgba(150,150,150,0.0)", "rgba(180,180,180,0.3)");
			}else {
				draw_hex(canvas, x,  y, hex_size, "rgba(34,34,34,0.5)", "none");
			}
		}else {			
			draw_hex(canvas, x,  y, hex_size, "none", "black");
			if (map_tile.fog){
				draw_hex(canvas, x,  y, hex_size, "rgba(150,150,150,0.0)", "rgba(180,180,180,0.5)");
			}
		}
		
	}
}


function draw_hex_borders(canvas, x, y, map_tile, property){

	
	var stroke_color = players[map_tile.owner].color.border;
	//var stroke_color = "#fff";
	var adjacent_tile = get_adjacent_hex(map_tile, 0);
	if (!adjacent_tile || (adjacent_tile.owner != map_tile.owner)){
		// north west
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x, "y": y-hex_size,
		  a1: 240, l1: hex_size
		});
		
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 1);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// north east border 
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x, "y": y-hex_size,
		  a1: 120, l1: hex_size
		});
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 2);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// east border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x+(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 0, l1: hex_size
		});
	}

	adjacent_tile = get_adjacent_hex(map_tile, 3);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// south east border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x+(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 240, l1: hex_size
		});
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 4);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// south west border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x-(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 120, l1: hex_size
		});
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 5);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// west border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x-(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 0, l1: hex_size
		});
	}
}