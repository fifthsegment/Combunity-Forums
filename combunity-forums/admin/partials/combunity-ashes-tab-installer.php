<h3>Installer</h3>
<p>Thank you for choosing Combunity! There's only one more step left before Combunity is ready to use; install a Combunity theme.Choose one from the list below and install it as a WordPress theme.</p>
<?php
	$response = wp_remote_get( 'http://updatesv2.combunity.com/fseg/combunity-installer-themes/?ver=' . Combunity_Api()->get_version() );
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
