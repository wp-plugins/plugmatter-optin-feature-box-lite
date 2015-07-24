<?php 
function  get_graph_data($ab_id){
	global $wpdb;
	$table = $wpdb->prefix.'plugmatter_ab_stats';	
	
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE ab_id = %d ORDER BY date ASC ",$ab_id));
	$box_data = array();
	$a_data[]="";
	$b_data[]="";
	foreach ($results as $fivesdraft)
	{
		$ab_id=$fivesdraft->ab_id;
		$date=$fivesdraft->date;
		$timestamp = strtotime($date)*1000;
		$a_conversion=$fivesdraft->a_conv;
		$a_data[] = array($timestamp,$a_conversion);			
		$b_conversion=$fivesdraft->b_conv;
		$b_data[] = array($timestamp, $b_conversion);		
	}	
	return array("a"=>json_encode($a_data), "b"=>json_encode($b_data));
}

	global $wpdb;	
	$ab_id = intval($_GET['ab_id']);	
	$tot_a_conversion = 0;
	$tot_b_conversion = 0;
	$tot_a_impression = 0;
	$tot_b_impression = 0;
	$avg_a_conversion = 0;
	$avg_b_conversion = 0;
	$tot_date = 0;
	
	$ab_stats_tbl = $wpdb->prefix.'plugmatter_ab_stats';	
	$ab_test_tbl = $wpdb->prefix.'plugmatter_ab_test';
	$temp_tbl = $wpdb->prefix.'plugmatter_templates';	
	$resultss = $wpdb->get_results($wpdb->prepare("SELECT *,COUNT(date) AS tot_date,SUM(a_imp) AS tot_a_impression,SUM(b_imp) AS tot_b_impression, SUM(a_conv) AS tot_a_conversion,SUM(b_conv) AS tot_b_conversion FROM $ab_stats_tbl WHERE ab_id =%d",$ab_id));	
	
	$results2 = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ab_test_tbl WHERE id =%d",$ab_id));
	$results3 = $wpdb->get_row("SELECT temp_name  FROM $temp_tbl WHERE id ='$results2->boxA' ");	
	$results4 = $wpdb->get_row("SELECT temp_name  FROM $temp_tbl WHERE id ='$results2->boxB' ");	
	
	$campaign_name = $results2->compaign_name;
	$boxA_name =  $results3->temp_name;
	$boxB_name =  $results4->temp_name;
	foreach ( $resultss as $fivesdraft ) {
		 $ab_id=$fivesdraft->ab_id;		
		 $tot_a_conversion=$fivesdraft->tot_a_conversion;
		 $tot_b_conversion=$fivesdraft->tot_b_conversion;
		 $tot_a_impression=$fivesdraft->tot_a_impression;
		 $tot_b_impression=$fivesdraft->tot_b_impression;
		 $tot_date=$fivesdraft->tot_date;
		 if($tot_a_conversion != 0) {
			 $avg_a_conversion = ($tot_a_conversion/$tot_a_impression)*100;
			 $avg_a_conversion = number_format((float)$avg_a_conversion, 2, '.', '');
         }
        if($tot_b_conversion != 0) {
			 $avg_b_conversion = ($tot_b_conversion/$tot_b_impression)*100;
			 $avg_b_conversion = number_format((float)$avg_b_conversion, 2, '.', '');
		 }
	}
	
	$ab_graph_data = get_graph_data($ab_id);
?>


<script>
	jQuery(document).ready(function(){ 
		var array_a = <?php echo $ab_graph_data["a"]; ?>;
		var array_b = <?php echo $ab_graph_data["b"]; ?>;
		
	jQuery.plot(jQuery("#ab_chart"), [array_a,array_b], 
	{
		xaxis: {
        	mode: "time",        	
        	timeformat: "%b %d",
        	tickLength: 1,
            tickSize: [1, "day"],            
              },
		lines: { 
           show: true, 
           fill: false, 
           fillColor: "rgba(250, 254, 251, 0.8)",
		},
		points: { show: true, fill: false },
		colors: ["#f97777", "#74c0fb"]
    }); 

});
</script>
<div class='pmadmin_wrap'>
	<div class='pmadmin_headbar'>
		<div class='pmadmin_pagetitle'><h2>A/B Split-Test Statistics - <?php echo $campaign_name; ?></h2></div>
	    <div class='pmadmin_logodiv'><img src='<?php echo plugins_url("/images/logo.png", __FILE__ );?>' height='35'></div>
	</div>
	<div class='pmadmin_body'>
	<div id="ab_chart"></div>
	<table class="widefat" style='width:700px;margin:auto;'>
	    <thead>
		    <tr style="">
				<th> </th>
				<th>Template Name</th>
				<th>Impressions</th>
				<th>Conversions</th>
				<th>Conversion Rate</th>
			</tr>
		</thead>
		<tbody style='text-align:center'>
			<tr>
				<td class="post-title column-title">
					<strong>&nbsp;&nbsp;<div class='boxa_color'>&nbsp;</div>Feature Box A</strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo  $boxA_name ;?></strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo $tot_a_impression; ?></strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo $tot_a_conversion; ?></strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo $avg_a_conversion; ?> %</strong>
				</td>				
			</tr>
			<tr>
				<td class="post-title column-title">
					<strong>&nbsp;&nbsp;<div class='boxb_color'>&nbsp;</div>Feature Box B</strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo  $boxB_name ;?></strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo $tot_b_impression; ?></strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo $tot_b_conversion; ?></strong>
				</td>
				<td class="post-title column-title">
					<strong><?php echo $avg_b_conversion; ?> %</strong>
				</td>				
			</tr>			
		</tbody>
	</table>
</div></div>