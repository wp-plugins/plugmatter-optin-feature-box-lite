jQuery(document).ready(function() {
	
	jQuery('#pm_button').click(function(event){
			jQuery("#pm_form_submit").submit();	
  		return false;
	});
	
	jQuery('#pm_input').keyup(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		  jQuery("#pm_form_submit").submit();	
			return false;
		}		 
	});	


	jQuery(".pm_form_track").submit(function(event){
		jQuery.post(pm_site_url,{"action":"pm_ab_track","track":"conv","ab_meta":jQuery("#pm_featurebox").attr("ab_meta")}).done(function(data) {});
	}); 
		
  	jQuery.post(pm_site_url,{"action":"pm_ab_track","track":"imp","ab_meta":jQuery("#pm_featurebox").attr("ab_meta")}).done(function(data) {
		//alert(data);
	});

	if(pm_getCookie("plugmatter_num_of_revisits") == "undefined") {
		pm_setCookie("plugmatter_num_of_revisits",1,365);
	} else {	
		var cvcnt = +pm_getCookie("plugmatter_num_of_revisits") + 1;
		pm_setCookie("plugmatter_num_of_revisits",cvcnt,365);
	}

	jQuery("#pm_featurebox").children('#loading_pmfb').remove();
	jQuery("#pm_featurebox").children().show();	
});
	
	jQuery("#pm_featurebox").children().hide().parent().append("<div id='loading_pmfb'><img src='"+pm_plugin_url+"/images/loading.gif' /></div>");
	

	jQuery("#pm_form_submit").submit(function (event){ 
				
		var email = jQuery('#pm_input').val();

		var e_patt = new RegExp(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/);
		if (e_patt.test(email)) { 
			pm_setCookie("plugmatter_conv_done",1,365);

			/* Analytics Tracking */
				var tid = jQuery("#pm_featurebox").attr("pm_meta_tid");
				
				if (typeof ga !== 'undefined') {
					if(tid) {
						var	temp_name = tid.split("_")[1];	
						ga('send','event', 'Plugmatter Feature Box', 'Subscription', temp_name);
					}	
				}

				if (typeof _gaq !== 'undefined') {
					if(tid) {
						var	temp_name = tid.split("_")[1];	
						_gaq.push(['_trackEvent', 'Plugmatter Feature Box', 'Subscription', temp_name]);
					}	
				}
			/*--------------------------*/

			if(jQuery("#pm_form_submit").attr("action") == "#pm_mailpoet" || jQuery("#pm_form_submit").attr("action") == "#pm_constantcontact" || jQuery("#pm_form_submit").attr("action") == "#pm_mailchimp_singloptin" || jQuery("#pm_form_submit").attr("action") == "#pm_jetpack") {  
        		event.preventDefault();      
      		}

			jQuery.post(pm_site_url,{"action":"pm_ab_track","track":"conv","ab_meta":jQuery("#pm_featurebox").attr("ab_meta")}).done(function(data) {
 
				/* MailPoet Subscription */ 
				if(jQuery("#pm_form_submit").attr("action") == "#pm_mailpoet") {
					var email = jQuery("#pm_featurebox").find('input[name="email"]').val();
					var fname = jQuery("#pm_featurebox").find('input[name="name"]').val();
					var list_id = jQuery("#pm_featurebox").find('input[name="list_id"]').val();
					var redirect_url = jQuery("#pm_featurebox").find('input[name="redirect_url"]').val();
					jQuery.get(pm_site_url,{"action":"wysija_ajax","controller":"subscribers","task":"save","data[0][name]":"wysija[user][firstname]", "data[0][value]":fname,"data[1][name]":"wysija[user][email]", "data[1][value]":email, "data[2][name]":"wysija[user_list][list_ids]", "data[2][value]": list_id}).done(function(data) {
						if(data.result === true) {
							location.href = redirect_url;
						} else {
							alert("Error Subscribing User");
						}
					}); 
				} else if(jQuery("#pm_form_submit").attr("action") == "#pm_constantcontact") {
					var pmcc_email = jQuery("#pm_featurebox").find('input[name="cc_email"]').val();
					var pmcc_fname = jQuery("#pm_featurebox").find('input[name="cc_firstname"]').val();
					var pmfb_tid = jQuery("#pm_featurebox").find('input[name="pmfb_tid"]').val();
					var pmcc_redirect_url = jQuery("#pm_featurebox").find('input[name="cc_redirect_url"]').val();

					jQuery.post(pm_site_url,{"action":"pmfb_cc","email":pmcc_email,"fname":pmcc_fname,"pmfb_tid":pmfb_tid}).done(function(data) {
						
						if(data === "0") {
							alert("Error Subscribing User");	
							return false;
						} else {
							location.href = pmcc_redirect_url;
						}
					});
				} else if(jQuery("#pm_form_submit").attr("action") == "#pm_mailchimp_singloptin") {
					var pm_mailchimp_email = jQuery("#pm_featurebox").find('input[name="MERGE0"]').val();
					var pm_mailchimp_fname = jQuery("#pm_featurebox").find('input[name="MERGE1"]').val();
					var pmfb_tid = jQuery("#pm_featurebox").find('input[name="pmfb_tid"]').val();
					var pm_mailchimp_redirect_url = jQuery("#pm_featurebox").find('input[name="redirect_url"]').val();

					jQuery.post(pm_site_url,{"action":"pmfb_mailchimp","MERGE0":pm_mailchimp_email,"MERGE1":pm_mailchimp_fname,"pmfb_tid":pmfb_tid}).done(function(data) {
						
						if(data === "0") {
							alert("Error Subscribing User");	
							return false;
						} else {
							location.href = pm_mailchimp_redirect_url;
						}
					});
				} else if(jQuery("#pm_form_submit").attr("action") == "#pm_jetpack") {
					var pm_jetpack_email = jQuery("#pm_featurebox").find('input[name="email"]').val();
					var pm_jetpack_fname = jQuery("#pm_featurebox").find('input[name="name"]').val();
					var pmfb_tid = jQuery("#pm_featurebox").find('input[name="pmfb_tid"]').val();
					var pm_jetpack_redirect_url = jQuery("#pm_featurebox").find('input[name="redirect_url"]').val();

					jQuery.post(pm_site_url,{"action":"pm_jetpack","email":pm_jetpack_email,"name":pm_jetpack_fname,"pmfb_tid":pmfb_tid}).done(function(data) {
						if(data === "0") {
							alert("Error Subscribing User");	
							return false;
						} else {
							location.href = pm_jetpack_redirect_url;
						}
					});
				}
			});
			return true;
		} else {
				alert("You have entered an invalid email address!");  
				return false;
		}    
});



function pm_setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
//	alert("done");
}

function pm_getCookie(c_name){
	var c_value = document.cookie;
	var c_start = c_value.indexOf(" " + c_name + "=");
	if (c_start == -1) {
		c_start = c_value.indexOf(c_name + "=");
	}
	if (c_start == -1) {
		c_value = null;
	} else {
		c_start = c_value.indexOf("=", c_start) + 1;
		var c_end = c_value.indexOf(";", c_start);
		if (c_end == -1) {
			c_end = c_value.length;
		}
		c_value = unescape(c_value.substring(c_start,c_end));
	}
	return c_value;
}