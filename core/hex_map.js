var hex_map = new Object();
var hex_size = 60; 
var hex_selected = {};
var hex_highlighted = {};
var hex_height = hex_size * 2;
var hex_width = Math.sqrt(3)/2 * hex_height;
var hex_vert = 3 / 4 * hex_height;
var hex_hover_history = new Array();

var map_context_menu_type;
var map_right_click;

var hex_neighbors = [
	[0,  -1],  [+1, -1],  [ +1, 0],
	[0,  +1],  [-1, +1],  [ -1, 0] 
];

var map_scale = 0.7;
var map_height = 1400;
var map_width = 1400;
var map_viewport = new Object();


function initialize_map(width, height){
	
	// figure out height and width of map based on the number of hexes
	map_height = (hex_height * 1.5 * Math.floor(height / 2)) + hex_height ;
	map_width = hex_width * width + (hex_width*0.7);
	
	var map_ratio = map_width / map_height;
	
	$('#galactic_map_mini').attr("width",map_width+400);
	$('#galactic_map_mini').attr("height",map_height+400);
	$('#galactic_map_mini').css("width",180*map_ratio);
	$('#galactic_map_mini').css("height","180");
	
	$('#galactic_map_mini_overlay').attr("width",map_width);
	$('#galactic_map_mini_overlay').attr("height",map_height);
	$('#galactic_map_mini_overlay').css("width",180*map_ratio);
	$('#galactic_map_mini_overlay').css("height","180");
		
	// calculate current view port
	//map_viewport.startX = $('#galactic_map_container').scrollLeft();
	//map_viewport.startY = $('#galactic_map_container').scrollTop();
	map_viewport.endX = map_viewport.startX + $('#galactic_map_container').width;
	map_viewport.endY = map_viewport.startY + $('#galactic_map_container').height;
	map_viewport.width = $('#galactic_map_container').width();
	map_viewport.height = $('#galactic_map_container').height();
	map_viewport.percentX = map_viewport.startX / map_viewport.width;
	map_viewport.percentY = map_viewport.startY / map_viewport.height;
	map_viewport.percentWidth = map_width / map_viewport.width;
	map_viewport.percentHeight = map_height / map_viewport.height;
	map_viewport.factorWidth = map_width / 200;
	map_viewport.factorHeight = map_height / 200;
	
	map_context_menu_type = "default";
	map_right_click = "";
	
	if (!first_load){
		map_scroll_offset = {"x":0, "y":0};
		$("#galactic_map_hexes").css({"top": -map_buffer , "left": -map_buffer });
		$("#galactic_map_overlay").css({"top": -map_buffer , "left": -map_buffer });
		$("#galactic_map_objects").css({"top": -map_buffer , "left": -map_buffer });
	}		
	
	map_chunk_size.x = Math.floor($('#galactic_map_container').width() + map_buffer*2);
	map_chunk_size.y = Math.floor($('#galactic_map_container').height() + map_buffer*2);
	
	$('#galactic_map_overlay').attr("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_overlay').attr("height",$('#galactic_map_container').height() + map_buffer*2);
	$('#galactic_map_overlay').css("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_overlay').css("height",$('#galactic_map_container').height() + map_buffer*2);
	
	$('#galactic_map_objects').attr("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_objects').attr("height",$('#galactic_map_container').height() + map_buffer*2);
	$('#galactic_map_objects').css("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_objects').css("height",$('#galactic_map_container').height() + map_buffer*2);
	
	$('#galactic_map_hexes').attr("width", $('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_hexes').attr("height", $('#galactic_map_container').height() + map_buffer*2);
	$('#galactic_map_hexes').css("width", $('#galactic_map_container').width() + map_buffer*2 );
	$('#galactic_map_hexes').css("height", $('#galactic_map_container').height() + map_buffer*2 );
	
	draw_map("galactic_map", true);
	draw_minimap_overlay();
	// after first load has been done don't bother with most of this function
	if (first_load){
		return;
	}	
	
	first_load = true;
	scroll_in_progress = false;	
	
	var view_port_width = $("#galactic_map_container").width();
	var map_surface_width = $("#galactic_map_hexes").width();
	var view_port_height = $("#galactic_map_container").height();
	var map_surface_height = $("#galactic_map_hexes").height();


	// dragging surface events
	$('#galactic_map_dragger').bind('mousedown',function(event){
		mouse_down_event = event;
		scroll_in_progress = {
			"x": event.pageX, "y": event.pageY,
			"target_x": $("#galactic_map_hexes").position().left, "target_y": $("#galactic_map_hexes").position().top
		};		
		event.preventDefault();
	});
	$('#galactic_map_dragger').bind('mouseup',function(event){
		scroll_in_progress = false;
		if (Math.abs(mouse_down_event.pageX - event.pageX) < 10 && Math.abs(mouse_down_event.pageY - event.pageY) < 10 && event.button == 0){
			var offset_x = $("#galactic_map_hexes").position().left + map_buffer;
			var offset_y = $("#galactic_map_hexes").position().top + map_buffer;
			var click_x = event.pageX - $(this).offset().left;// + offset_x/hex_size/2;
			var click_y = event.pageY - $(this).offset().top;// + offset_y/hex_size/2;
			//console.log(offset_x);
			click_to_hex( (click_x - offset_x) / map_scale - map_scroll_offset.x, (click_y - offset_y) / map_scale - map_scroll_offset.y, 'click', event);
			
		}
		mouse_down_event = null;
	});
	
	$('#galactic_map_dragger').bind('mousewheel',function(event){
		//console.log(event);
		var zoom_offset = {};
		if (event.deltaY > 0){
			map_scale = map_scale += 0.1;
		}else {
			map_scale = map_scale -= 0.1;
		}
		var click_x = event.pageX - $(this).offset().left;// + offset_x/hex_size/2;
		var click_y = event.pageY - $(this).offset().top;// + offset_y/hex_size/2;
		zoom_offset.x = 0.1 * click_x;
		zoom_offset.y = 0.1 * click_y;
		
		jump_map(-map_scroll_offset.x + click_x/map_scale, -map_scroll_offset.y + click_y/map_scale);
	});
	
	$('#galactic_map_dragger').bind('mousemove',function(event){
		
		//console.log(event);
		if (scroll_in_progress){
			// move the map!			
			var x = (event.pageX  - scroll_in_progress.x);
			var y = (event.pageY  - scroll_in_progress.y);
			
			var new_x = scroll_in_progress.target_x + x;
			var new_y = scroll_in_progress.target_y + y;

			var oldX = $("#galactic_map_hexes").css("left");
			var oldY = $("#galactic_map_hexes").css("top");
			
			map_scroll_offset.adjust_x = $("#galactic_map_hexes").position().left + map_buffer;
			map_scroll_offset.adjust_y = $("#galactic_map_hexes").position().top + map_buffer;

			$("#galactic_map_hexes").css({"top": new_y , "left": new_x });
			$("#galactic_map_overlay").css({"top": new_y , "left": new_x });
			$("#galactic_map_objects").css({"top": new_y , "left": new_x });
			
			draw_minimap_overlay();
			if (false && new_x >= -100 && !map_canvas_buffer_offset){					
				map_canvas_buffer_offset = {};
				map_canvas_buffer_offset.x = map_scroll_offset.x - map_buffer;
				map_canvas_buffer_offset.y = map_scroll_offset.y - map_buffer;
				console.log(map_canvas_buffer_offset);
				draw_map("galactic_map_buffer", false, map_canvas_buffer_offset);
			}
			
			if (new_x < (view_port_width - map_surface_width ) || new_x > 0 || new_y < (view_port_height - map_surface_height ) || new_y > 0){
				map_scroll_offset.x = map_scroll_offset.x + (new_x + map_buffer)/map_scale;
				map_scroll_offset.y = map_scroll_offset.y + (new_y + map_buffer)/map_scale;
				$("#galactic_map_overlay").css({"top": -map_buffer , "left": -map_buffer });
				$("#galactic_map_hexes").css({"top": -map_buffer , "left": -map_buffer });
				$("#galactic_map_objects").css({"top": -map_buffer , "left": -map_buffer });
				scroll_in_progress = {
					"x": event.pageX, "y": event.pageY,
					"target_x": $("#galactic_map_hexes").position().left, "target_y": $("#galactic_map_hexes").position().top
				};
				
				
				//if (map_canvas_buffer_offset && map_canvas_buffer_offset.x == map_scroll_offset.x && map_canvas_buffer_offset.y == map_scroll_offset.y){
				//	var offscreen_canvas = document.getElementById("galactic_map_buffer_hexes");
				//	var onscreen_canvas = document.getElementById("galactic_map_hexes");
				//	var onscreen_context = onscreen_canvas.getContext("2d");					
					// Don't care about transparency:
				//	onscreen_context.drawImage(offscreen_canvas, 0, 0, $("#galactic_map_hexes").width(), $("#galactic_map_hexes").height(), 0, 0, $("#galactic_map_hexes").width(), $("#galactic_map_hexes").height());
				//	map_canvas_buffer_offset = null;
				//}else {
					draw_map("galactic_map");
				//}								
				redraw_overlay();				
			}
			
			event.preventDefault();
		}
		var offset_x = $("#galactic_map_hexes").position().left + map_buffer;
		var offset_y = $("#galactic_map_hexes").position().top + map_buffer;
		var click_x = event.pageX - $(this).offset().left;// + offset_x/hex_size/2;
		var click_y = event.pageY - $(this).offset().top;// + offset_y/hex_size/2;
		//console.log(offset_x);
		click_to_hex( (click_x - offset_x) / map_scale - map_scroll_offset.x, (click_y - offset_y) / map_scale - map_scroll_offset.y, '', event);
	});
	$('#galactic_map_dragger').bind('mouseout',function(event){
		scroll_in_progress = false;
	});
	$('#galactic_map_dragger').bind('contextmenu',function(event){
			if (map_right_click && map_right_click != ""){
				click_to_hex(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top, 'click', event);
			}else if (show_context_menu('galactic_map_context_menu', event.pageX, event.pageY)){
				event.preventDefault();
			}
			return false;
	});
	
	// set up click and drag for mini map
	$('#galactic_map_mini_overlay').bind('mousedown',function(event){
			start_minimap_drag(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top);
	});
	$('#galactic_map_mini_overlay').bind('mouseup',function(event){
			end_minimap_drag(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top);
	});
	$('#galactic_map_mini_overlay').bind('mousemove',function(event){
			minimap_move((event.pageX - $(this).offset().left)  , (event.pageY - $(this).offset().top));
	});
	
	
	$('body').bind('click',function(event){
			$('#galactic_map_context_menu').hide();
	});
	
	$('body').bind('keypress',function(event){
			// handle shortcut keys
	});
	
	// center the map on the users home system
	if (player && player.home){
		var coords = hex_to_pixel(player.home);
		jump_map(coords.x, coords.y);
		draw_minimap_overlay();
		//$('#galactic_map_container').scrollLeft((coords.x * map_scale - ($('#galactic_map_container').width()/2)));
		//$('#galactic_map_container').scrollTop((coords.y  * map_scale - ($('#galactic_map_container').height()/2)));
	}
	
}


function jump_map(x, y){	
//	x = x;// - $("#galactic_map_container").width()/2/map_scale;
//	y = y;// - $("#galactic_map_container").height()/2/map_scale;
//	var new_x = Math.floor(x / map_buffer) * map_buffer;
//	var new_y = Math.floor(y / map_buffer) * map_buffer;
//	new_x = new_x - map_buffer;
//	new_y = new_y - map_buffer;
//	var diff_x = x % map_buffer;	
//	var diff_y = y % map_buffer;
//	
//	console.log(new_x);
//	console.log(diff_x);
//	
//	$("#galactic_map_overlay").css({"top": -diff_y , "left": -diff_x });
//	$("#galactic_map_hexes").css({"top": -diff_y , "left": -diff_x });
//	$("#galactic_map_objects").css({"top": -diff_y , "left": -diff_x });
//	map_scroll_offset.x = ( -new_x );
//	map_scroll_offset.y = ( -new_y );
	map_scroll_offset.x = (-x + $("#galactic_map_container").width()/2/map_scale);
	map_scroll_offset.y = (-y + $("#galactic_map_container").height()/2/map_scale);
	draw_map("galactic_map");
	redraw_overlay();
}

function map_zoom(out){
	if (out){
		map_scale = map_scale * 2;
	}else {
		map_scale = map_scale / 2;
	}
	$('#galactic_map').css("width",map_width*map_scale);
	$('#galactic_map').css("height",map_height*map_scale);
	$('#galactic_map_overlay').css("width",map_width*map_scale);
	$('#galactic_map_overlay').css("height",map_height*map_scale);
	$('#galactic_map_objects').css("width",map_width*map_scale);
	$('#galactic_map_objects').css("height",map_height*map_scale);
	
	draw_map("galactic_map");
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

var image_cache = {};

function preload_images(data, callback){
	
	var sources = [];
	
	// list of images to load
	sources.push({'name':'fleet_f', 'image':'images/fleet_f.png'});	
	sources.push({'name':'fleet_e', 'image':'images/fleet_e.png'});	
	sources.push({'name':'star', 'image':'images/default.png'});	
	sources.push({'name':'background', 'image':'images/back2.png'});	
	
	
	// add image data from server for preloading
	for(key in sector_class_cache){		
		var sector_class = sector_class_cache[key];
		if(sector_class.image){
			sources.push({'name':'sector_class_'+sector_class.id, 'image':sector_class.image});	
		}
	}
	for(key in planet_class_cache){		
		var planet_class = planet_class_cache[key];
		if(planet_class.image){
			sources.push({'name':'planet_class_'+planet_class.id, 'image':planet_class.image});	
		}
	}	
		
	var loadedImages = 0;
	var numImages = sources.length;

	for(var i = 0; i < sources.length; i++) {
	  var src = sources[i];
	  image_cache[src.name] = new Image();
	  image_cache[src.name].onload = function() {
	    $('#loading_status').html("Loaded "+src.name);
	    if(++loadedImages >= numImages) {	    		      
	      handle_preload_images(data, callback);
	    }
	  };
	  image_cache[src.name].src = src.image;
	}
}

function handle_preload_images(data, callback){
	//alert("yeah");
	
	callback(data);
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
			
			if (first){
				// really needs to be temporary
				$.each(hex.your_fleets, function (){
					fleet_cache[this.id]  = this;
				});
			}
			$(canvas_objects).drawImage({
			  source: image_cache['fleet_f'],
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
				  source: sector_class_cache[map_tile.class_id].image,
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: sector_class_cache[map_tile.class_id].image,
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