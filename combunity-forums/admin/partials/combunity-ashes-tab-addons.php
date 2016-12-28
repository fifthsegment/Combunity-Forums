<h3>Addons</h3>

<?php
	$response = wp_remote_get( 'http://updatesv2.combunity.com/fseg/combunity-addons/?ver=' . Combunity_Api()->get_version() );
	if( is_array($response) ) {
	  $header = $response['headers']; // array of http header lines
	  $body = $response['body']; // use the content
	  echo $body;
	}
?>
<footer style="margin-top:100px;">
	<hr>
	<div style="float:right; margin-top:5px; font-size:13px;">
		<i>Combunity v<?php echo Combunity_Api()->get_version(); ?></i>
	</div>
	
</footer>
