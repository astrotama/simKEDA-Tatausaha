<?php
function tunihilbaruspp_new_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$kodekeg = arg(2);	
	if(arg(3)=='pdf'){		
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		//$output = printspm($kodekeg);
		//apbd_ExportSPM($output, 'SPM', $url);
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('tunihilbaruspp_new_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function tunihilbaruspp_new_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	/*$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjguitemlastpage"] = $referer;
	else
		$referer = $_SESSION["spjguitemlastpage"];*/
	
	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	

	$no = 0;
	$spptgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());	
	$tglawal = $spptgl; $tglakhir = $spptgl; 

	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	
	$form['sppno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => '',
	);	
	$form['spptgl_title'] = array(
		'#markup' => 'Tanggal',
		);
	$form['spptgl']= array(
		'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
		'#default_value' => $spptgl, 
			
		//'#default_value'=> array(
		//	'year' => format_date($TANGGAL, 'custom', 'Y'),
		//	'month' => format_date($TANGGAL, 'custom', 'n'), 
		//	'day' => format_date($TANGGAL, 'custom', 'j'), 
		// ), 
	 
		'#date_format' => 'd-m-Y',
		'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		//'#description' => 'Tanggal',
		);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);

	$form['formperiode'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PERIODE',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formperiode']['tglawal_title'] = array(
			'#markup' => 'Tanggal',
		);
		$form['formperiode']['tglawal']= array(
			'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
			'#default_value' => $tglawal, 
				 
			//'#default_value'=> array(
			//	'year' => format_date($TANGGAL, 'custom', 'Y'),
			//	'month' => format_date($TANGGAL, 'custom', 'n'), 
			//	'day' => format_date($TANGGAL, 'custom', 'j'), 
			// ), 
		 
			'#date_format' => 'd-m-Y',
			'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
			'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
			//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
			'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
			//'#description' => 'Tanggal',
		);
		$form['formperiode']['tglakhir_title'] = array(
			'#markup' => 'Sampai Tanggal',
		);
		$form['formperiode']['tglakhir']= array(
			'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
			'#default_value' => $tglakhir, 
				 
			//'#default_value'=> array(
			//	'year' => format_date($TANGGAL, 'custom', 'Y'),
			//	'month' => format_date($TANGGAL, 'custom', 'n'), 
			//	'day' => format_date($TANGGAL, 'custom', 'j'), 
			// ), 
		 
			'#date_format' => 'd-m-Y',
			'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
			'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
			//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
			'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
			//'#description' => 'Tanggal',
		);	
	
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('namasingkat', 'bendaharaatasnama', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'));
	$query->condition('u.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$penerimanama = $data->bendaharaatasnama;	// 'BENDAHARA PENGELUARAN ' . $data->namasingkat . ' (' . $data->bendaharanama . ')';
		$penerimanip = $data->bendaharanip;
		$penerimabankrekening = $data->bendahararekening;
		$penerimabanknama = $data->bendaharabank;
		$penerimanpwp = $data->bendaharanpwp;
	}
		
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PENERIMA',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanama, 
		);				
		$form['formpenerima']['penerimanip']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NIP'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanip, 
		);				
		$form['formpenerima']['penerimabankrekening']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Rekening'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabankrekening, 
		);				
		$form['formpenerima']['penerimabanknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Bank'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabanknama, 
		);				
		$form['formpenerima']['penerimanpwp']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NPWP'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanpwp, 
		);	
	
	//KELENGKAPAN
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KELENGKAPAN DOKUMEN',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('ltkelengkapandokumen', 'dk');
	$query->fields('dk', array('kodekelengkapan', 'uraian', 'nomor'));
	$query->condition('dk.jenis', 1, '=');
	$query->orderBy('dk.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++; 
		$kode = $data->kodekelengkapan;
		$uraian = $data->uraian;
		$form['formkelengkapan']['tablekelengkapan']['kodekelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraiankelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formkelengkapan']['tablekelengkapan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['adakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> '0', 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		
		$form['formkelengkapan']['tablekelengkapan']['tidakadakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> '0', 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		

	}
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);		
	
	
	//SIMPAN
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function tunihilbaruspp_new_main_form_validate($form, &$form_state) {

		
}
	
function tunihilbaruspp_new_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	$sppno = $form_state['values']['sppno'];
	$spptglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spptgl']);
	$tglawal = dateapi_convert_timestamp_to_datetime($form_state['values']['tglawal']);
	$tglakhir = dateapi_convert_timestamp_to_datetime($form_state['values']['tglakhir']);
	
	$keperluan = $form_state['values']['keperluan'];
	$bulan = substr($spptglsql, 5,2);

	//BENDAHARA
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('namasingkat', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp', 'bendaharaatasnama'));
	$query->condition('u.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$penerimanama = $data->bendaharaatasnama;
		$penerimanip = $data->bendaharanip;
		$penerimabankrekening = $data->bendahararekening;
		$penerimabanknama = $data->bendaharabank;
		$penerimanpwp = $data->bendaharanpwp;
	}
		
	$dokid = apbd_getkodedokumen($kodeuk);
	
	//KELENGKAPAN
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	$num_deleted = db_delete('dokumenkelengkapan')
	  ->condition('dokid', $dokid)
	  ->execute();		
	
	for ($n=1; $n <= $jumlahrekkelengkapan; $n++){
		$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
		$ada = '0';	//$form_state['values']['adakelengkapan' . $n];
		$tidakada = '0';	//$form_state['values']['tidakadakelengkapan' . $n];
		
		db_insert('dokumenkelengkapan')
			->fields(array('dokid', 'kodekelengkapan', 'ada', 'tidakada'))
			->values(array(
					'dokid'=> $dokid,
					'kodekelengkapan' => $kodekelengkapan,
					'ada' => $ada,
					'tidakada' => $tidakada,
					))
			->execute();
	}		
		
	//DOKUMEN
	$query = db_insert('dokumen')
			->fields(array('dokid', 'bulan', 'jenisdokumen', 'kodeuk', 'sppno', 'spptgl', 'spmtgl', 'keperluan', 'jumlah', 'netto', 'penerimanama', 'penerimanip', 'penerimabanknama', 'penerimabankrekening', 'penerimanpwp', 'tglawal', 'tglakhir'))
			->values(
				array(
					'dokid'=> $dokid,
					'bulan'=> $bulan,
					'jenisdokumen' => 7,
					'kodeuk' => $kodeuk,
					'sppno' => $sppno,
					'spptgl' =>$spptglsql,
					'spmtgl' =>$spptglsql,
					'keperluan' => $keperluan, 
					'jumlah' => 0,
					'netto' => 0,
					'penerimanama' => $penerimanama,
					'penerimanip' => $penerimanip, 
					'penerimabanknama' => $penerimabanknama,
					'penerimabankrekening' => $penerimabankrekening,
					'penerimanpwp' => $penerimanpwp,
					'tglawal' =>$tglawal,
					'tglakhir' =>$tglakhir,
				)
			);
	//dpq $query;		
	//echo (string) $query;
	$res = $query->execute();
		
	drupal_goto('sppnihilarsip');
	
}


?>
