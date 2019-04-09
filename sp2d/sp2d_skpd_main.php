<?php
function sp2d_skpd_main($arg=NULL, $nama=NULL) {
	if (isUserPPKD())
		drupal_goto('gusp2darsip');
	else
		drupal_goto('sp2dgajiarsip');
	
}




?>
