<?php
function refresh_main($arg=NULL, $nama=NULL) {
	$output_form = drupal_get_form('refresh_main_form');
	return drupal_render($output_form);// . $output;
}

function refresh_main_form($form, &$form_state) {
	
	$form['formdata']['logout']= array(
		'#type' => 'submit',
		'#value' =>  'Force Logout',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	$form['formdata']['clear']= array(
		'#type' => 'submit',
		'#value' =>  'Clear Cache',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	$form['formdata']['unblock']= array(
		'#type' => 'submit',
		'#value' =>  'Unblock',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}

function refresh_main_form_submit($form, &$form_state) {
	if ($form_state['clicked_button']['#value'] == $form_state['values']['clear']) {
		$num = db_delete('cache')
		  ->execute();
		$num = db_delete('cache_block')
		  ->execute();
		$num = db_delete('cache_bootstrap')
		  ->execute();
		$num = db_delete('cache_rules')
		  ->execute();
		$num = db_delete('cache_field')
		  ->execute();
		$num = db_delete('cache_filter')
		  ->execute();
		$num = db_delete('cache_form')
		  ->execute();
		$num = db_delete('cache_image')
		  ->execute();
		$num = db_delete('cache_libraries')
		  ->execute();
		$num = db_delete('cache_menu')
		  ->execute();
		$num = db_delete('cache_page')
		  ->execute();
		$num = db_delete('cache_path')
		  ->execute();
		$num = db_delete('cache_update')
		  ->execute();
		 
		$num = db_delete('dokumenrekening')
		  ->condition('jumlah', '0')
		  ->execute();

		 drupal_set_message('berhasil hapus cache');
		
	} elseif($form_state['clicked_button']['#value'] == $form_state['values']['unblock']){
		$num = db_delete('flood')
		  ->execute();
		 
		$num = db_query('DELETE FROM dokumenrekening where dokid NOT IN (SELECT dokid FROM dokumen)');	
		drupal_set_message('berhasil hapus flood');
		  
	} elseif($form_state['clicked_button']['#value'] == $form_state['values']['logout']){
		$num = db_delete('sessions')
		  ->execute();
		  
		if ($num ) drupal_set_message('berhasil hapus force logout');
	}
}
	
?>