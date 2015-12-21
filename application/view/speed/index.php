<?php
if(!empty($_GET['src'])){
	$src = $_GET['src'];
 } else {
	$src = "http://achtsam-miteinander.de";
}



?>
<input type="hidden" value="<?php echo $src; ?>" id="vie-src"/>
<ul class="targets">
<?php
foreach ($testTargets as $target) {
	$displayText = str_replace("http://", "", $target->testUrl);
	$activeClass = "";
	if ($src == $target->testUrl){
		$activeClass = " targets__item--active";
	}
	echo "<li class='targets__item$activeClass'><a href='?src=".$target->testUrl."'>".$displayText."</a></li>";
}
?>
</ul>
<div class="content">
	<h2>Speedanalyse für <i><?php echo $src; ?></i></h2>

	<div id="vie-test-results">
		<div id="vie-test-results--render"></div>
		<div id="vie-test-results--speed-index"></div>
	</div>
</div>
