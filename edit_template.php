<?php 
global $wpdb;
$siteurl = get_option('siteurl');
$table = $wpdb->prefix.'plugmatter_templates';
// global params
$id="";
$temp_name="";
$base_temp_name="";
$pm_box_width="0";
$pm_box_tmargin="0";
$pm_box_bmargin="0";
$params = "";
$pm_custom_css = "\" \"";
//------------------
if($_GET['action']=="edit" && $_GET['template_id']!='') {
	 $temp_id= $_GET['template_id'];
	 $fivesdrafts = $wpdb->get_results("SELECT id,temp_name,base_temp_name,params FROM $table WHERE id= $temp_id");
	 foreach ( $fivesdrafts as $fivesdraft ) {
	 	$id=$fivesdraft->id;
	 	$temp_name=$fivesdraft->temp_name;
	 	$base_temp_name=$fivesdraft->base_temp_name;
		$params=$fivesdraft->params;
		$getalign = json_decode($params);
		foreach($getalign as $align) {
			if($align->type == "alignment") {
				$pm_box_width = $align->width;
				$pm_box_tmargin = $align->top_margin;
				$pm_box_bmargin = $align->bottom_margin;					
			
			}
			if($align->type == "pm_custom_css"){
			 	$pm_custom_css = json_encode($align->pm_custom_css); 
				break;
			}
		}
	 }
}

function get_pages_list() {
	$pages = get_pages();
	$list = array();
	foreach ($pages as $page_data) {
		//echo "|".$title = $page_data->ID;
		//echo "|".$title = $page_data->post_content;				
		$list[] = array("id"=>$page_data->ID,"title"=>escapeJsonString(addslashes($page_data->post_title)));
	}
	print json_encode($list);
}

function escapeJsonString($value) {  
    $escapers =     array("\'");
    $replacements = array("\\u0027");
    return str_replace($escapers, $replacements, $value);
}


?>
<div class='pmadmin_wrap'>
	<div class='pmadmin_headbar'>
		<div class='pmadmin_pagetitle'><h2>Template Editor</h2></div>
	    <div class='pmadmin_logodiv'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/logo.png";?>' height='35'></div>
	</div>
	<div class='pmadmin_body'>
<form name='form1' action="<?php echo admin_url("admin.php?page=template_submenu-page"); ?>" method="POST">

<table class="pm_form_table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="name">
				Name Your Template:
				</label>
			</th>
			<td>
				<input id="title" class="regular-text" type="text" required="true" value="<?php if($temp_name){echo $temp_name;} ?>" name="temp_name">
				<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" >
				<input type="hidden" name="template_id" value="<?php echo $id; ?>" >
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="base_template">
				Select a Base Template:
				</label>
			</th>
			<td>
				<?php if($base_temp_name != ""){echo "<div id='base_temp_name' >".$base_temp_name."</div>";}else{ ?>
				<select name="base_temp_name" id="base_temp_name">
				<option selected=selected value='' ></option>
				<?php
				$dir = plugin_dir_path(__FILE__) . "templates/";
				$list = scandir($dir);
				foreach ($list as $v) {	
				if(($v != ".") && ($v != "..")){
				?>
				<option value="<?php echo $v; ?>"  <?php if($base_temp_name == $v){echo "selected=selected" ;} ?> ><?php echo $v; ?></option>
				<?php 
				} }
				?>
				<option value="user_designed_template">Your Custom Design</option>
				</select>
				<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="base_template">
				Alignment (In Pixels):
				</label>
			</th>
			<td>
				Width: <input type='text' size='4' maxlength='4' name='pm_box_width' id='pm_box_width' value='<?php echo $pm_box_width; ?>' >&nbsp;&nbsp;&nbsp;&nbsp;
			    Top Margin: <input type='text' size='3' maxlength='3' name='pm_box_tmargin' id='pm_box_tmargin' value='<?php echo $pm_box_tmargin; ?>' >&nbsp;&nbsp;&nbsp;&nbsp;
				Bottom Margin: <input type='text' size='3' maxlength='3' name='pm_box_bmargin' id='pm_box_bmargin' value='<?php echo $pm_box_bmargin; ?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
		</tr>		
		<tr>
			<td colspan="2">
				<div id="ajax_load_temp"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2"><a id="pm_add_custom_css" href="#">Custom CSS (Advance Users)</a><br/>
			<textarea name='pm_custom_css' id='pm_custom_css' rows="10" cols="45" style="display:none;"></textarea></td>
		</tr>

		<tr>
			<td colspan="2"><br>
				<input class="pm_primary_buttons" id="save_btn" type="button" value="     Save Template    "> &nbsp;&nbsp;
				<input class="pm_secondary_buttons" id="cancel_btn" type="button" value=" Cancel " onclick="location.href='<?php echo admin_url("admin.php?page=template_submenu-page"); ?>'">
			</td>
		</tr>
</tbody>
</table>
</form>
<div  id='pm_hover_icon' >&nbsp;</div>
        
<script type="text/javascript">
jQuery(document).ready(function(){ 

	pm_temp_style=document.createElement("link");
	pm_temp_style.setAttribute("rel", "stylesheet");
	pm_temp_style.setAttribute("type", "text/css");
	document.getElementsByTagName("head")[0].appendChild(pm_temp_style);


  pm_custom_style = document.createElement("STYLE");
  pm_custom_style.setAttribute("id", "pm_custom_style");
  pm_custom_style.type = 'text/css';
  document.getElementsByTagName('head')[0].appendChild(pm_custom_style);

	var temp_name = '<?php echo $temp_name; ?>';
	var pm_site_url = '<?php echo get_option('siteurl');?>';
	var admin_url = '<?php echo admin_url("admin-ajax.php?action=plug_load_template&data=$base_temp_name") ?>';
	
	var pm_codemirror = CodeMirror.fromTextArea(document.getElementById("pm_custom_css"), {
						            lineNumbers: true,
    						        mode: "css"
  						        });
	
	if(String(<?php echo $pm_custom_css; ?>) !== ""){
  		var pm_codemirror_value= <?php echo $pm_custom_css; ?>; 
 	} else{
  		var pm_codemirror_value = " "; 
 	}
	
	pm_codemirror.setValue(pm_codemirror_value); 
	
	jQuery("#pm_add_custom_css").click(function(event){
		event.preventDefault();
		jQuery(".CodeMirror").toggle();
	});

	jQuery(".CodeMirror").hide();	

   pm_codemirror.on("blur", function(pm_codemirror){
    var pm_icss = pm_codemirror.getValue();
    if (pm_custom_style.styleSheet){
        pm_custom_style.styleSheet.cssText = pm_icss;
    } else {
        pm_custom_style.appendChild(document.createTextNode(pm_icss));
    }
    document.getElementsByTagName('head')[0].appendChild(pm_custom_style);
  });

	if(temp_name != ""){
		var base_temp_name = '<?php echo $base_temp_name; ?>';
		
		var page_id = "";
		var params = JSON.parse('<?php echo trim(addslashes($params));?>');
		for(var i=0;i<params.length;i++) {
			if(params[i]["type"] == "user_designed_template") {
				base_template = params[i]["type"];
				page_id = params[i]["id"];
				jQuery.post("<?php echo admin_url("admin-ajax.php?action=plug_get_page_content") ?>",{"page_id":page_id},function(result){
					jQuery('#ajax_load_temp').html(result).show();
				});
                var user_designed_template = true;
			}	
		}	
    
   
    if(user_designed_template != true) {
      var filename = pm_plugin_url+'templates/'+base_temp_name+"/style.css";
      pm_temp_style.setAttribute("href", filename);			
      jQuery('#ajax_load_temp').html("<div class ='pm_loading' style='width:100%;height:300px; background:url("+pm_plugin_url+"images/loading.gif"+") no-repeat scroll center;'>&nbsp;</div>").show();
      setTimeout(function() {
      jQuery('#ajax_load_temp').load(admin_url,function(){			
          for(var i=0;i<params.length;i++) {
              if(params[i]["type"] == "text") {
                  var id = params[i]["id"];
                  var text = params[i]["params"]["text"];                        
                  var color = params[i]["params"]["color"];
                  jQuery("#"+id).css("color",color);
                  var font_family = params[i]["params"]["font_family"];
                  var font_weight =  params[i]["params"]["font_weight"];
                  curfont[id] = font_family.replace(/ /g,"+");
                  jQuery("#"+id).css("font-family", font_family);
                  jQuery("#"+id).text(text);
                  jQuery("#"+id).inlineEdit(params[i]["type"]);
              } else if(params[i]["type"] == "textarea") {
                  var html = params[i]["params"]["html"];
                  jQuery('#pm_description').html(html);
                  var color = params[i]["params"]["color"];
                  jQuery('#pm_description').css("color", color);										
                  var font_size = params[i]["params"]["font_size"];
                  jQuery('#pm_description').css("font-size", font_size);
                  var font_family = params[i]["params"]["font_family"];
                  update_font_family(font_family);	
                  //alert(font_family);
                  var id = params[i]["id"];	 	   	
                  jQuery("#"+id).inlineEdit(params[i]["type"]);				  	
              } else if(params[i]["type"] == "service") {
                  var html = params[i]["params"];
                  jQuery.each(html, function(name,value) {
                      email_service_option[name] = value;
                  });
                  var id = params[i]["id"]; 
                  jQuery("#"+id).inlineEdit(params[i]["type"]);
               jQuery("#pm_exclamation_icon").attr('src',pm_plugin_url+"/images/tick-icon.png").css("opacity","1");
              } else if(params[i]["type"] == "color") {							   		
                  var bgcolor = params[i]["params"]["bgcolor"];			           
                  var id = params[i]["id"]; 		                       
                  if(jQuery("#"+id).attr("gradient") != null) {				       		 		
                      var rules = jQuery("#"+id).css("background-image");				 		
                      var new_rules = rules.replace(/rgb\((\d{1,3}), (\d{1,3}), (\d{1,3})\)/,bgcolor);							 	
                      jQuery("#"+id).css("background-image",new_rules);
                      jQuery("#"+id).attr("gradient",bgcolor);		    			 	    				    
                   } else {			
                      jQuery("#"+id).css("background-color", bgcolor);					 		
                   }
                   jQuery("#"+id).inlineEdit(params[i]["type"]);					 
              } else if(params[i]["type"] == "image") {
                  var img_url = params[i]["params"]["img_url"];					
                  jQuery("#pm_image").css('background-image',img_url);
                  var id = params[i]["id"];	   		
                  jQuery("#"+id).inlineEdit(params[i]["type"]);	
              } else if(params[i]["type"] == "video") {
                  var id = params[i]["id"];	 
                  var video_src = params[i]["params"]["video_src"];		
                  var video_url = params[i]["params"]["video_url"];		
                  jQuery("#pm_video").attr("src", video_src);
                  jQuery("#pm_video").attr("video_url", video_url);
                  jQuery("#"+id).inlineEdit(params[i]["type"]);
              } else if(params[i]["type"] == "button") {
                  var txt = params[i]["params"]["text"];
                  var btn_class = params[i]["params"]["btn_class"];	
                  var email_input = params[i]["params"]["email_input"];
                  var name_input = params[i]["params"]["name_input"];
                  jQuery("#pm_input").val(email_input);
                  jQuery("#pm_name_field").val(name_input);                        
                  jQuery("#pm_button").val(txt);	
                  jQuery("#pm_button").removeClass();
                  jQuery("#pm_button").addClass(btn_class);			           
                  var id = params[i]["id"];	   		
                  jQuery("#"+id).inlineEdit(params[i]["type"]);
              }
          }	  
          update_fun();
      }).show();	
      }, 2000);	
    }
	  
  }

 

	jQuery("select#base_temp_name").change(function(){
		jQuery('#ajax_load_temp').html("<div class ='pm_loading' style='width:100%;height:300px;"+ 
				"background:url("+pm_plugin_url+"images/loading.gif"+") no-repeat scroll center;'>&nbsp;</div>").show();
		
		var template = jQuery("select#base_temp_name option:selected").val(); 
		if(document.getElementById("select_page") != null){
			jQuery('#select_page').remove();
			jQuery('#ajax_load_temp').css("border","solid 0px grey");
		}		
		if(template == null || template == "" ){
			alert("Select a base template");
			jQuery('#ajax_load_temp').html(" ").show();
			return false;
		} else if(template == "user_designed_template") {
			jQuery('#ajax_load_temp').html(" ").show();
			var page_list = jQuery.parseJSON('<?php  get_pages_list() ; ?>');			
			var select_page = document.createElement("select");
			select_page.setAttribute("id", "select_page");
			<?php if(get_option("Plugmatter_PACKAGE") != "plug_featurebox_pro" && get_option("Plugmatter_PACKAGE") != "plug_featurebox_dev")  {
				echo "jQuery('#base_temp_name').after(\"&nbsp;&nbsp;&nbsp;".Plugmatter_UPNOTE."\");";
			} else {
				echo "jQuery('#base_temp_name').after(select_page);";
			} ?>
			jQuery('#select_page').append(jQuery("<option></option>").attr("value","").text("Select Page"));
			for (var i = 0; i < page_list.length; i++) {
				var id = page_list[i]["id"];
				var title = page_list[i]["title"];
				jQuery('#select_page').append(jQuery("<option></option>").attr("value",id ).text(title));
			}
			
			jQuery("select#select_page").change(function(){				
				var page_id = jQuery("select#select_page").val();
				jQuery('#ajax_load_temp').html("<div class ='pm_loading' style='width:100%;height:300px; background:url("+pm_plugin_url+"images/loading.gif"+") no-repeat scroll center;'>&nbsp;</div>").show();
				jQuery.post("<?php echo admin_url("admin-ajax.php?action=plug_get_page_content") ?>",{"page_id":page_id},function(result){
					jQuery('#ajax_load_temp').html(result).show();
				  });						
			});
			
			//return false;

		} else {		
			var filename = pm_plugin_url+'templates/'+template+"/style.css";
			template_type = template.split("_")[0];
			pm_temp_style.setAttribute("href", filename);
			jQuery('#ajax_load_temp').load('<?php echo admin_url("admin-ajax.php?action=plug_load_template&data=") ?>'+template,function(){
			if(template_type != "mini") {
				jQuery("#pm_h1").text("Lorem ipsum dolor sit amet, consectetur adipisicing elit");
				jQuery("#pm_description").html("<ul><li>Fusce vel sapien vehicula, consequat massa eu, pellentesque mauris.</li><li>Ut fermentum dui nec neque blandit, a consequat tortor vestibulum.</li><li>Aenean et nibh rutrum, faucibus sapien non, placerat lectus.</li></ul>");
			} else {
				jQuery("#pm_h1").text("Lorem ipsum dolor sit amet");
				jQuery("#pm_description").html("Fusce vel sapien vehicula, consequat massa eu, pellentesque mauris.");			
			}
            jQuery("#pm_input").val("Enter Your Email Address");	
            jQuery("#pm_name_field").val("Enter Your First Name");	                
			jQuery("#pm_button").val("Subscribe");		
			jQuery("#pm_video").attr("src", "//player.vimeo.com/video/79277917?badge=0&byline=0&portrait=0&title=0");
			jQuery("#pm_video").attr("video_url", "http://vimeo.com/79277917");
			jQuery("#pm_button").removeClass();
 		    jQuery("#pm_button").addClass("pm_default_btn");				
			jQuery(".pmedit").each(function(){				
			    var edit_type = jQuery(this).attr("pm_meta");
		        if(edit_type == "text") {
			        var def_font = jQuery(this).attr("def_font");//alert(def_font);
			        get_font_h1(def_font);
		        	jQuery("#"+this.id).inlineEdit(edit_type);
		   		} else if(edit_type == "textarea") {
		   			var def_font = jQuery(this).attr("def_font");//alert(def_font);
		   			get_font_txtarea(def_font);
		   		  	jQuery("#"+this.id).inlineEdit(edit_type);
		   		} else if(edit_type == "service") {
		   		  	jQuery("#"+this.id).inlineEdit(edit_type);
		   		} else if(edit_type == "color") {	
		   		  	jQuery("#"+this.id).inlineEdit(edit_type);
		   		} else if(edit_type == "image") {
		   		  	jQuery("#"+this.id).inlineEdit(edit_type);
		   		} else if(edit_type == "video") {
		   		  	jQuery("#"+this.id).inlineEdit(edit_type);
		   		} else if(edit_type == "button") {			   		
		   		  	jQuery("#"+this.id).inlineEdit(edit_type);
		   		}				
		   		
			});   
			}).show();	
	}	
	}); 	

	
	jQuery("#save_btn").click(function() { 
		jQuery(document).click();
		var title = jQuery("input#title").val();
		if(title == ""){alert("Enter the Template name");return false;}
		var template = jQuery("select#base_temp_name option:selected").val();
		if(template == undefined) template = base_temp_name;
		if(template == "null"){alert("Select any Base Template");return false;}
		if(email_service_option["service"] == undefined && template != "user_designed_template") {		
			alert("Please select an email service");
			jQuery("#pm_form").click();
			return false;
		}
		hidden=document.createElement("input");
		hidden.setAttribute("type", "hidden");
		hidden.setAttribute("name", "params");
		jQuery("#title").append(hidden);
		var json_params = [];
		
		if(jQuery("#pm_box_width").val() != "") { var pm_box_width = jQuery("#pm_box_width").val(); } else { var pm_box_width = 0;}
		if(jQuery("#pm_box_tmargin").val() != "") { var pm_box_tmargin = jQuery("#pm_box_tmargin").val(); } else { var pm_box_tmargin = 0;}
		if(jQuery("#pm_box_bmargin").val() != "") { var pm_box_bmargin = jQuery("#pm_box_bmargin").val(); } else { var pm_box_bmargin = 0;}
		
		var pm_code = pm_codemirror.getValue();
		if(pm_code != ""){var pm_custom_css = pm_code;} else { var pm_custom_css = "";}
				
		json_params.push({"type":"alignment","width": pm_box_width, "top_margin":pm_box_tmargin, "bottom_margin":pm_box_bmargin});
		
		json_params.push({"type": "pm_custom_css","pm_custom_css": String(pm_custom_css) });
		
		if(template == "user_designed_template") {
			sel_page_id = jQuery("select#select_page").val();
			if(sel_page_id == undefined) sel_page_id = page_id;
    	    json_params.push({"type":"user_designed_template","id":sel_page_id});
		} else {
		
		jQuery(".pmedit").each(function(){
	        var edit_type = jQuery(this).attr("pm_meta");
	        if(edit_type == "text") {
		        var id = jQuery(this).attr('id');
		        var text = jQuery(this).text();
	        	var color = jQuery("#"+this.id).css("color");
	        	var font_family = jQuery("#"+this.id).css("font-family"); 
	        	font_family = font_family.replace(/'/g, "");	        	
				//alert(id+" "+font_family);				
	        	//var variant = jQuery("#"+this.id).css("font-weight");	        	
	        	json_params.push({"type":edit_type,"id":id,"params":{"text":text,"color":color,"font_family":font_family}});  //,"variant":variant
	   		} else if(edit_type == "textarea") {
	   		 	var id = jQuery(this).attr('id');
	   			var color = colorToHex(jQuery("#"+this.id).css("color"));
	        	var font_family = jQuery("#"+this.id).css("font-family");	//alert(id+" "+font_family);
				var font_size = jQuery("#"+this.id).css("font-size");
	        	font_family = font_family.replace(/'/g, "");	        	   
	        	var html = jQuery("#"+this.id).html();
	        	json_params.push({"type":edit_type,"id":id,"params":{"color":color,"font_family":font_family,"font_size":font_size,"html":html}});
	   		} else if(edit_type == "service") {	   
	   				var id = jQuery(this).attr('id');			
		   		    JSON.stringify(email_service_option);
			   		json_params.push({"type":edit_type,"id":id,"params":email_service_option});
			   		
			} else if(edit_type == "color") {
				var id = jQuery(this).attr('id');
	   			var bgcolor = "";	  
	   			var gradient = ""; 			
	   		 if(jQuery("#"+this.id).attr("gradient") != null) { 			   		 	     			 				
	   			//bgcolor = jQuery("#"+this.id).css("background-image");
	   			bgcolor = jQuery("#"+this.id).attr("gradient");
	   			gradient = "yes";
	   			//alert(bgcolor);	   			
		 	 }else{
		 		gradient = "no";
		 		bgcolor = jQuery("#"+this.id).css("background-color");
			 }
	   			json_params.push({"type":edit_type,"id":id,"params":{"bgcolor":bgcolor,"gradient":gradient}});
	   		} else if(edit_type == "image") {
	   			var id = jQuery(this).attr('id');
	   			//var img_url = jQuery("#pm_image").attr("src");
	   			var img_url = jQuery("#pm_image").css('background-image');
	   			json_params.push({"type":edit_type,"id":id,"params":{"img_url":img_url}});
	   		} else if(edit_type == "video") {
				var id = jQuery(this).attr('id');			
	   			var vid_src = jQuery("#pm_video").attr("src");
				var vid_url = jQuery("#pm_video").attr("video_url");
	   			json_params.push({"type":edit_type,"id":id,"params":{"video_src":vid_src, "video_url":vid_url}});
	   		} else if(edit_type == "button") {
	   			var id = jQuery(this).attr('id');
	   			var txt =  jQuery("#pm_button").val();
	   			var btn_class =  jQuery("#pm_button").attr("class") ;		   		
                var email_input =  jQuery("#pm_input").val();
                var name_input =  jQuery("#pm_name_field").val();
	   			json_params.push({"type":edit_type,"id":id,"params":{"text":txt,"btn_class":btn_class, "email_input":email_input, "name_input":name_input }});
		   	}			
		}); 

		}

		json_params = JSON.stringify(json_params);
		hidden.setAttribute("value", json_params);
        document.forms["form1"].submit();
	});
});

</script>
</div></div>
<?php if(get_option("Plugmatter_PACKAGE") != "plug_featurebox_pro") { ?>
    <div style='background:#fff;border:#ddd;padding:20px;margin:30px;'>
        <div class='plug_enable_lable' style='margin-top:10px;width:100%;margin-bottom:20px;'>Need more base templates? Check them out:</div>    
        <?php if(get_option("Plugmatter_PACKAGE") == "plug_featurebox_lite") { ?>
            <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_3.png"; ?>' width='225'></div>
            <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_4.png"; ?>' width='225'></div>
            <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_5.png"; ?>' width='225'></div>
            <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_6.png"; ?>' width='225'></div>
        <?php } ?>
        <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_7.png"; ?>' width='225'></div>
        <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_8.png"; ?>' width='225'></div>
        <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_9.png"; ?>' width='225'></div>
        <div style='float:left;margin:10px 20px;'><img src='<?php echo plugins_url()."/".Plugmatter_DIR_NAME."/images/up_preview_10.png"; ?>' width='225'></div>        
        <div style='clear:both'>&nbsp;</div>
        <div style='margin:10px;text-align:center;'><input id="submit" class="pm_primary_buttons" type="button" value="Upgrade To Get More Templates!" onclick="location.href='http://plugmatter.com/feature-box#plans&pricing'" name="submit"></div>    
    </div>
<?php } ?>