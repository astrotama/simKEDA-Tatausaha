<?php


function opd_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	 


		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('opd_edit_form');
		return drupal_render($output_form);// . $output;
		 
	
}

    
function opd_edit_form($form, &$form_state){
    /*
	$form = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	*/
    //drupal_add_css('files/css/kegiatancam.css');	
	drupal_set_title('Organisasi Perangkat Daerah');

	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else 
		$kodeuk = arg(2);	
	
	
	//drupal_set_message($kodeuk);
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('kodeuk', 'kodeu', 'header1', 'header2', 'namauk', 'namasingkat', 'pimpinannama', 'pimpinanjabatan', 'pimpinanpangkat', 
				'pimpinannip', 'kodedinas', 'kodeu', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp', 'ppknama', 'ppknip', 'ppkjabatan', 'bendaharaatasnama', 'pimpinannowa', 'ppknowa', 'bendaharanowa'));
	
	$query->condition('u.kodeuk', $kodeuk, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;
		$namauk = $data->namauk;
		$kodeu = $data->kodeu;
		$namasingkat = $data->namasingkat;
		$pimpinannama = $data->pimpinannama;
		$pimpinanjabatan = $data->pimpinanjabatan;
		$pimpinanpangkat = $data->pimpinanpangkat;
		$pimpinannip = $data->pimpinannip;
		$kodedinas = $data->kodedinas;
		$pimpinannowa = $data->pimpinannowa;
		
		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;
		$bendahararekening = $data->bendahararekening;
		$bendaharabank = $data->bendaharabank;
		$bendaharanpwp = $data->bendaharanpwp;
		$bendaharanowa = $data->bendaharanowa;
		
		$header1 = $data->header1; 
		$header2 = $data->header2;		
		
		$ppknama = $data->ppknama;
		$ppkjabatan = $data->ppkjabatan;
		$ppknip = $data->ppknip;
		$bendaharaatasnama = $data->bendaharaatasnama;
		$ppknowa = $data->ppknowa;
	}
 
	$form['kodeu']= array(
		'#type'         => 'value', 
		'#title'        => 'kodeu',
		'#value'=> $kodeu, 
	); 
	$form['kodeuk']= array(
		'#type'         => 'value', 
		'#title'        => 'kodeuk',
		'#value'=> $kodeuk, 
	); 
	
	if (isAdministrator()) {
		$form['kodedinas']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Kode Dinas', 
			//'#description'  => 'pimpinannip', 
			'#maxlength'    => 5, 
			'#size'         => 10, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodedinas, 
			//'#weight' => 9,
		);
	} else {
	$form['kodedinas']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Kode Dinas', 
		//'#description'  => 'pimpinannip', 
		'#maxlength'    => 5, 
		'#size'         => 10, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodedinas, 
		//'#weight' => 9,
	);
		
	}
	$form['namauk']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'namauk', 
		'#maxlength'    => 100, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $namauk, 
		//'#weight' => 3,
	); 
	$form['namasingkat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama Singkat', 
		//'#description'  => 'namasingkat', 
		'#maxlength'    => 50, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $namasingkat, 
		//'#weight' => 4,
	); 
	
	//PIMPINAN
	$form['pimpinan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pimpinan',
		'#collapsible' => true,
		'#collapsed' => false,        
	);		
	$form['pimpinan']['pimpinannama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'pimpinannama', 
		'#maxlength'    => 50, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinannama, 
		//'#weight' => 5,
	); 
	$form['pimpinan']['pimpinanjabatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jabatan', 
		//'#description'  => 'pimpinanjabatan', 
		'#maxlength'    => 60, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinanjabatan, 
		//'#weight' => 6,
	); 
	
	$form['pimpinan']['pimpinanpangkat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Pangkat', 
		//'#description'  => 'pimpinanpangkat', 
		'#maxlength'    => 60, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinanpangkat, 
		//'#weight' => 7,
	); 
	$form['pimpinan']['pimpinannip']= array(
		'#type'         => 'textfield', 
		'#title'        => 'NIP', 
		//'#description'  => 'pimpinannip', 
		'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinannip, 
		//'#weight' => 8,
	);
	$form['pimpinan']['pimpinannowa']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor WA', 
		//'#description'  => 'pimpinannip', 
		'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinannowa, 
		//'#weight' => 8,
	);
	
	//PPK
	$form['ppk'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PPK',
		'#collapsible' => true,
		'#collapsed' => false,        
	);		
	$form['ppk']['ppknama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'ppknama', 
		'#maxlength'    => 50, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $ppknama, 
		//'#weight' => 5,
	); 
	$form['ppk']['ppkjabatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jabatan', 
		//'#description'  => 'ppkjabatan', 
		'#maxlength'    => 60, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $ppkjabatan, 
		//'#weight' => 6,
	); 
	$form['ppk']['ppknip']= array(
		'#type'         => 'textfield', 
		'#title'        => 'NIP', 
		//'#description'  => 'ppknip', 
		'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $ppknip, 
		//'#weight' => 8,
	);
	$form['ppk']['ppknowa']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor WA', 
		//'#description'  => 'ppknip', 
		'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $ppknowa, 
		//'#weight' => 8,
	);

	
	//BENDAHARA
	$form['bendahara'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Bendahara',
		'#collapsible' => true,
		'#collapsed' => false,        
	);		
	$form['bendahara']['bendaharanama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendaharanama, 
		//'#weight' => 8,
	);
	$form['bendahara']['bendaharaatasnama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama Sesuai Rekening Bank', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendaharaatasnama, 
		//'#weight' => 8,
	);
	$form['bendahara']['bendaharanip']= array(
		'#type'         => 'textfield', 
		'#title'        => 'NIP', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendaharanip, 
		//'#weight' => 8,
	);
	$form['bendahara']['bendahararekening']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Rekening', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendahararekening, 
		//'#weight' => 8,
	);
	$form['bendahara']['bendaharabank']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Bank', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendaharabank, 
		//'#weight' => 8,
	);	
	$form['bendahara']['bendaharanpwp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'NPWP', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendaharanpwp, 
		//'#weight' => 8,
	);
	$form['bendahara']['bendaharanowa']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor WA', 
		//'#description'  => 'pimpinannip', 
		//'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $bendaharanowa, 
		//'#weight' => 8,
	);	

	//ALAMAT
	$form['alamat'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Alamat',
		'#collapsible' => true,
		'#collapsed' => false,        
	);		
	$form['alamat']['header1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Alamat 1',
		'#maxlength'    => 100, 
		'#size'         => 60, 		
		'#default_value'=> $header1, 
		//'#weight' => 17,
	); 	
	$form['alamat']['header2']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Alamat 2',
		'#maxlength'    => 100, 
		'#size'         => 60, 		
		'#default_value'=> $header2, 
		//'#weight' => 17,
	); 	
	
	$form['submit'] = array (
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
		//'#weight' => 20,
	);
    return $form;
}

function opd_edit_form_validate($form, &$form_state) {
//$kodeuk = arg(3);
//    if (!isset($kodeuk)) {
//        if (strlen($form_state['values']['kodeuk']) < 8 ) {
//            form_set_error('', 'kodeuk harus terdiri atas 8 karakter');
//        }            
//    }
}

function opd_edit_form_submit($form, &$form_state) {
    
    $kodeu = $form_state['values']['kodeu'];
	
	$kodeuk = $form_state['values']['kodeuk'];
	$namauk = $form_state['values']['namauk'];
	$namasingkat = $form_state['values']['namasingkat'];
	$pimpinannama = $form_state['values']['pimpinannama'];
	$pimpinanjabatan = $form_state['values']['pimpinanjabatan'];
	$pimpinanpangkat = $form_state['values']['pimpinanpangkat'];
	$pimpinannip = $form_state['values']['pimpinannip'];
	$kodedinas = $form_state['values']['kodedinas'];
	$pimpinannowa = $form_state['values']['pimpinannowa'];

	$bendaharanama = $form_state['values']['bendaharanama'];
	$bendaharaatasnama = $form_state['values']['bendaharaatasnama'];
	$bendaharanip = $form_state['values']['bendaharanip'];
	$bendahararekening = $form_state['values']['bendahararekening'];
	$bendaharabank = $form_state['values']['bendaharabank'];
	$bendaharanpwp = $form_state['values']['bendaharanpwp'];
	$bendaharanowa = $form_state['values']['bendaharanowa'];

	$ppknama = $form_state['values']['ppknama'];
	$ppkjabatan = $form_state['values']['ppkjabatan'];
	$ppknip = $form_state['values']['ppknip'];
	$ppknowa = $form_state['values']['ppknowa'];
	
	$header1 = $form_state['values']['header1'];
	$header2 = $form_state['values']['header2'];
	
	//drupal_set_message($header1);
	
	$query = db_update('unitkerja')
	->fields( 
			array(
				'namauk' => $namauk,
				'namasingkat' => $namasingkat,
				'pimpinannama' => $pimpinannama,
				'pimpinanjabatan' => $pimpinanjabatan,
				'pimpinanpangkat' => $pimpinanpangkat,
				'pimpinannip' => $pimpinannip,
				'pimpinannowa' => $pimpinannowa,

				'ppknama' => $ppknama,
				'ppkjabatan' => $ppkjabatan,
				'ppknip' => $ppknip,
				'ppknowa' => $ppknowa,
				
				'bendaharanama' => $bendaharanama,
				'bendaharaatasnama' => $bendaharaatasnama,
				'bendaharanip' => $bendaharanip,
				'bendahararekening' => $bendahararekening,
				'bendaharabank' => $bendaharabank,
				'bendaharanpwp' => $bendaharanpwp,
				'bendaharanowa' => $bendaharanowa,
				
				'header1' => $header1,
				'header2' => $header2,
			)
		);
	$query->condition('kodeuk', $kodeuk, '=');
	$res = $query->execute();
	
	drupal_goto('');
}
?>