<?php 
if(!empty($_GET['src'])){ 
	$src = $_GET['src'];
 } else {
	$src = "http://achtsam-miteinander.de";
}
?>
<h2>Speedanalyse für <i><?php echo $src; ?></i></h2>
<input type="hidden" value="<?php echo $src; ?>" id="vie-src"/>
<div id="vie-test-results">
	<div id="vie-test-results--render"></div>
	<div id="vie-test-results--speed-index"></div>
</div>
