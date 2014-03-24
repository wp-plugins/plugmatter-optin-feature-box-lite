<?php
$fonts = json_decode(
		file_get_contents( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . 'AIzaSyBKo64RSV_kiZ8T7_J5LNv_npD0YERvr5g')
);
//echo "<pre>";
//print_r($fonts);

$json_fonts = array();

foreach($fonts->items as $itm) {
	$family= $itm->family;

	$var1='';
	foreach($itm->variants as $var){
		$var1=$var1.$var.',';

	}$variants= substr($var1,0,-1);

	$json_fonts[] = array("family"=>$family,"variants"=>$variants);
}
//print_r($json_fonts);
print  json_encode($json_fonts);
//space removed
?>
