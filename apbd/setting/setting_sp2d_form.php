<?php

function setting_sp2d_form($form, &$form_state) {
	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["setting_sp2d"] = $referer;
	else
		$referer = $_SESSION["setting_sp2d"];
	
	$bud_jabatan = variable_get('bud_jabatan', 'BENDAHARA UMUM DAERAH');
	$bud_nama = variable_get('bud_nama', 'Drs. ANWAR HARYONO, MM');
	$bud_nip = variable_get('bud_nip', '19580318 198503 1 011');
	
	$ttd_by_kuasa_bud = variable_get('ttd_by_kuasa_bud', '0');
	$kuasa_bud_jabatan = variable_get('kuasa_bud_jabatan', 'KUASA BENDAHARA UMUM DAERAH');
	$kuasa_bud_nama = variable_get('kuasa_bud_nama', 'SITI NUR JANAH, SE');
	$kuasa_bud_nip = variable_get('kuasa_bud_nip', '19580318 198503 2 011');

	$form['referer'] = array (
		'#type' => 'value',
		'#value' => $referer,
	);
	
	$form['formbud'] = array (
		'#type' => 'fieldset',
		'#title'=> 'BENDAHARA UMUM DAERAH',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['formbud']['bud_jabatan'] = array (
			'#type' => 'textfield',
			'#default_value' => $bud_jabatan,
		);
		$form['formbud']['bud_nama'] = array (
			'#type' => 'textfield',
			'#default_value' => $bud_nama,
		);
		$form['formbud']['bud_nip'] = array (
			'#type' => 'textfield',
			'#default_value' => $bud_nip,
		);

	$form['formkuasabud'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KUASA BENDAHARA UMUM DAERAH',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['formkuasabud']['ttd_by_kuasa_bud'] = array(
		  '#type' =>'checkbox', 
		  '#title' => t('Tanda Tangan oleh Kuasa BUD'),
		  '#default_value' => $ttd_by_kuasa_bud,
		);
		$form['formkuasabud']['kuasa_bud_jabatan'] = array (
			'#type' => 'textfield',
			'#default_value' => $kuasa_bud_jabatan,
		);
		$form['formkuasabud']['kuasa_bud_nama'] = array (
			'#type' => 'textfield',
			'#default_value' => $kuasa_bud_nama,
		);
		$form['formkuasabud']['kuasa_bud_nip'] = array (
			'#type' => 'textfield',
			'#default_value' => $kuasa_bud_nip,
		);

	$form['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
				


	
	return $form;
}

function setting_sp2d_form_validate($form, &$form_state) {
}

function setting_sp2d_form_submit($form, &$form_state) {
	
	$referer = $form_state['values']['referer'];
	
	$bud_jabatan = $form_state['values']['bud_jabatan'];
	$bud_nama = $form_state['values']['bud_nama'];
	$bud_nip = $form_state['values']['bud_nip'];
	
	$kuasa_bud_jabatan = $form_state['values']['kuasa_bud_jabatan'];
	$kuasa_bud_nama = $form_state['values']['kuasa_bud_nama'];
	$kuasa_bud_nip = $form_state['values']['kuasa_bud_nip'];
	$ttd_by_kuasa_bud = $form_state['values']['ttd_by_kuasa_bud'];


	variable_set('bud_jabatan', $bud_jabatan);
	variable_set('bud_nama', $bud_nama);
	variable_set('bud_nip', $bud_nip);
	
	variable_set('ttd_by_kuasa_bud', $ttd_by_kuasa_bud);
	variable_set('kuasa_bud_jabatan', $kuasa_bud_jabatan);
	variable_set('kuasa_bud_nama', $kuasa_bud_nama);
	variable_set('kuasa_bud_nip', $kuasa_bud_nip);
	
	drupal_goto($referer);
	
	
	//drupal_set_message(drupal_realpath('public://'));
	/*
	$lok = variable_get('file_public_path', conf_path() . '/files');
	drupal_set_message($lok);
	$file = $lok . '/spm/XX/SPM.PDF';
	drupal_set_message($file);
	//drupal_goto($file);
	
	//sendftp($file);
	sendftptls();
	*/	
}

function sendftptls() {
	$ftp_server = "ftp://simkedajepara.link/";
	
	drupal_set_message('connect');
	$ftp_conn = ftp_ssl_connect( $ftp_server, '21');

	//Login
	drupal_set_message('login');
	$login_result = ftp_login($ftp_conn, 'doksimkeda', 'D0ks!mk3d4');

	if( $login_result ) {
		drupal_set_message('ok');
		
		/*
		//Set passive mode
		ftp_pasv( $ftp_conn, true );
		
		
		// Transfer file
		$transfer_result = ftp_put( $ftp_conn, $dest_file_path, $source_file_path, FTP_BINARY );

		//Verify if transfer was successfully made
		if( $transfer_result)
		{
			echo "Success";
		}
		else
		{
			echo "An error occured";
		}
		*/
		
	} else {
		drupal_set_message('not ok');
	}
}

function sendftp($file) {
$ftp_server = "ftp://simkedajepara.link";
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, 'doksimkeda', 'D0ks!mk3d4');

//$file = "localfile.txt";

// upload file
if (ftp_put($ftp_conn, "SPM.PDF", $file, FTP_BINARY))
  {
  echo "Successfully uploaded $file.";
  }
else
  {
  echo "Error uploading $file.";
  }

// close connection
ftp_close($ftp_conn);
}

?>