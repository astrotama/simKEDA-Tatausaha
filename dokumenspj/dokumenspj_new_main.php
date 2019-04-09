<?php
function dokumenspj_new_main($arg=NULL, $nama=NULL) {
	
	$output_form = drupal_get_form('dokumenspj_new_main_form');
	return drupal_render($output_form);// . $output;
	
}

function dokumenspj_new_main_form($form, &$form_state) {

	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	

	$tanggal = mktime(0,0,0,date('m'),date('d'),apbd_tahun());	

	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			

	
	$form['tglawal_title'] = array(
		'#markup' => 'Mulai Tanggal',
		);
	$form['tglawal']= array(
		'#type' => 'date_select',
		'#default_value' => $tanggal, 
			
		'#date_format' => 'd-m-Y',
		'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		//'#description' => 'Tanggal',
	);
	$form['tglakhir_title'] = array(
		'#markup' => 'Sampai Tanggal',
		);
	$form['tglakhir']= array(
		'#type' => 'date_select',
		'#default_value' => $tanggal, 
			
		'#date_format' => 'd-m-Y',
		'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		//'#description' => 'Tanggal',
	);
	$form['uraian'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Uraian'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);


	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function dokumenspj_new_main_form_validate($form, &$form_state) {
}
	
function dokumenspj_new_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	$tglawal = dateapi_convert_timestamp_to_datetime($form_state['values']['tglawal']);
	$tglakhir = dateapi_convert_timestamp_to_datetime($form_state['values']['tglakhir']);

	$uraian = $form_state['values']['uraian'];

	//DOKUMEN
	$dokspjid = db_insert('dokumenspj')
			->fields(array('kodeuk', 'tglawal', 'tglakhir', 'uraian'))
			->values(
				array(
					'kodeuk' => $kodeuk,
					'tglawal' => $tglawal,
					'tglakhir' =>$tglakhir,
					'uraian' => $uraian, 
				)
			)->execute();
		

	
	//drupal_set_message($dokspjid);
	drupal_goto('dokumenspj/edit/' . $dokspjid);
	
}


?>
