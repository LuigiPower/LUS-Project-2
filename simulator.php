<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'config.php';

	if(
		!isset($_GET['context']) 
		OR !my_autoloader($_GET['context']))
	{
		echo '
		<html><body><h1>'.WS_NAME.'</h1>
		<p>This is the default web page for this server.</p>
		</body></html>';
		die();
	}

$cnt = buildOptions($_GET['context']);
?>
<html>
<head>
<title><?php echo WS_NAME; ?> Simulator</title>
<style type="text/css">
	body{line-height:28px;}
	input{ width:300px; margin-left:20px;}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<body>
<h1><?php echo WS_NAME; ?> Simulator</h1>
<h2></h2>
<form enctype="multipart/form-data" action="index.php" method="POST">
	ACTIONS:

	<select class="selAction">
		<option value=''>select</option>
<?php 
	echo JOIN("\n",$cnt['options']);
	//foreach ($cnt['options'] as $o){
	//echo $o."\n"; //join("\n",$cnt['options']);		
//	}
?>
			
	</select>
	<textarea class = "request_data" placeholder ="place json data" name="REQUEST_DATA" style="width:100%; height:100px;" ></textarea> <br/>
	<input type="submit" value="Send" />
</form>	
<hr>
<h3>REQUEST</h3>
<div id="request"></div>
<hr>
<h3>RESULT</h3>
<div id="result"></div>

<h3>SESSION</h3>
<div id="SESSION"></div>


<script>

function getJsonSampleData(action){
	var jsonres= '';
	
	switch(action){
		
		case '':


<?php
	foreach ($cnt['calls'] as $c => $m){
		echo "case '$c':\njsonres = $m\nbreak\n\n";
	}
?>			
	}
	return  JSON.stringify(jsonres);
}




$(function() {
	
//	var url = '<?php echo BASE_URL.(isset($_GET['context']) ? $_GET['context'] : 'blackbox' ); ?>';


	//var url = '<?php echo BASE_URL.'index.php?context='.(isset($_GET['context']) ? $_GET['context'] : 'blackbox' ); ?>';
	var url = '<?php echo BASE_URL; ?>';

	
	
	$('.selAction').on('change', function (e) {
	    var optionSelected = $("option:selected", this);
	    var valueSelected = this.value;
		console.log(valueSelected);
		$('.request_data').val(getJsonSampleData(valueSelected));
	});
	
	
    $('form').submit(function(event) {
		
		event.preventDefault();
		
		//var REQUEST_DATA = JSON.parse($('.request_data').val());

		var REQUEST_DATA = $('.request_data').val();
		
		$( "#request" ).empty();
		$( "#result" ).empty();
		 /* Send the data using post */
		var posting = $.post( url, { REQUEST_DATA : REQUEST_DATA } );
		/* Put the results in a div */
		
		$( "#request" ).empty().append(JSON.stringify({ REQUEST_DATA : REQUEST_DATA }));
		
		posting.done(function( data ) {
			$( "#result" ).empty().append(JSON.stringify(data));
		});
		
    });
});
</script>
</body>
</html>


<?php
function buildOptions($class){
	$cnt = array();
	
	$class_methods = get_class_methods($class);
	//echo '<pre>';
	//print_r($class_methods);
	foreach($class_methods as $m){
	    if (substr($m, 0,1) != '_'){

			$cnt['options'][] = '<option value="'.$m.'">'.$m.'</option>';
			//echo $m;	
			$r = new ReflectionMethod($class, $m);
			$params = $r->getParameters();
			$prms = array('CONTEXT:"'.$class.'"','ACTION:"'.$m.'"');
			foreach ($params as $param) {
			    if (substr($param->getName(), 0,1) != '_'){
				    $prms[] = $param->getName().':"'.strtoupper($param->getName().'"');
			    }
			}
			$cnt['calls'][$m]='{'.join(',',$prms).'}';
		}
	}
	//echo '<pre>';
	//print_r($cnt);
	//die();
	return $cnt;
}
	
	
	
