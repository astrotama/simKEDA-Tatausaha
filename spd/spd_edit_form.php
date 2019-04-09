<?php

function spd_edit_form($form, &$form_state) {
    
	$jenis = arg(1);
    $spdkode = arg(2);
	$auto = arg(3);
	//drupal_set_message($spdkode);
	
	if ($auto=='auto') {
		
		$jenisbelanja = ($jenis=='1'? 'btl':'bl');
		$tw = substr($spdkode, -1);
		
		$uri = 'http://spd.sipkdjepara.net/service/spdget.php?tahun=' . apbd_tahun() . '&kodesatker=' . substr($spdkode, 0, 2) . '&jenisbelanja=' . $jenisbelanja . '&triwulan=' . $tw . '&token=5pd51mk3d4'; 
		//drupal_set_message($uri);
		
		//retreive and parse the json from URL.
		$request = drupal_http_request($uri);
		//drupal_set_message($request->data);

		$json_response = drupal_json_decode($request->data);

		//drupal_set_message($json_response['nomor']);
		//drupal_set_message($json_response['tanggal']);
		//drupal_set_message($json_response['jumlah']);

		$spdno = $json_response['nomor'];
		$spdtgl = $json_response['tanggal'];
		$jumlah = $json_response['jumlah'];		
	
	} else {
		$query = db_select('spd', 's');
		$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw', 'jenis'));
		
		$query->condition('s.spdkode', $spdkode, '=');
		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$spdkode = $data->spdkode;
			$spdno = $data->spdno;
			$spdtgl = $data->spdtgl;
			$tw = $data->tw;	
			$jenis = $data->jenis;
			$jumlah = $data->jumlah;
		}	
	}
	
	if ($jenis=='1')
		$title = 'SPD Belanja Tidak Langsung';
	else
		$title = 'SPD Belanja Langsung';
	$title .= 'Triwulan #' . $tw;
	drupal_set_title($title);
	
	$form['spdkode'] = array(
		'#type' => 'value', 
		'#value' => $spdkode
	);
	$form['jenis'] = array(
		'#type' => 'value', 
		'#value' => $jenis
	);
	$form['spdno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => $spdno,
	);
	$form['spdtgl'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Tgl. SPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => $spdtgl,
	);
	$form['jumlah'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Jumlah'),
		'#attributes'	=> array('style' => 'text-align: right'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $jumlah,
	);	
	$form['otomatis']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-play" aria-hidden="true"></span> Otomatis',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		//'#disabled' => TRUE,
		//'#suffix' => "&nbsp;<a href='' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	$form['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='/#' class='btn btn-info btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'>Tutup</a>",
		
	);
					


	
	return $form;
}

function spd_edit_form_validate($form, &$form_state) {
}

function spd_edit_form_submit($form, &$form_state) {
	$spdkode = $form_state['values']['spdkode'];
	$jenis = $form_state['values']['jenis'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['otomatis']) {

		
		drupal_goto('spd/' . $jenis . '/' . $spdkode . '/auto');
		
	} else {	
		$spdkode = $form_state['values']['spdkode'];
		$spdno = $form_state['values']['spdno'];
		$spdtgl = $form_state['values']['spdtgl'];
		$jumlah = $form_state['values']['jumlah'];

		$query = db_update('spd')
				->fields( 
				array(
					'spdno' => $spdno,
					'spdtgl' => $spdtgl,
					'jumlah' => $jumlah,
				)
			);
		$query->condition('spdkode', $spdkode, '=');
		$num = $query->execute();
			
		if ($num) {
			
			//drupal_set_message($spdkode);
			drupal_set_message('SPD berhasil disimpan');

		}
	}
	
}


function getspd(){

$uri = 'http://spd.sipkdjepara.net/service/spdget.php?tahun=2018&kodesatker=03&jenisbelanja=bl&triwulan=3&token=5pd51mk3d4'; 
//retreive and parse the json from URL.
$request = drupal_http_request($uri);
//drupal_set_message($request->data);

$json_response = drupal_json_decode($request->data);

drupal_set_message($json_response['nomor']);
drupal_set_message($json_response['tanggal']);
drupal_set_message($json_response['jumlah']);
 
}

?>