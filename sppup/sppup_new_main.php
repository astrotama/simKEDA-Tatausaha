<?php
function sppup_new_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('sppup_new_main_form');
	return drupal_render($output_form);// . $output;
	
}

function sppup_new_main_form($form, &$form_state) {
	
	$nourut = 0;
	$dokid = '';
	$jenisdokumen = 0;
	$kodeuk = '';
	$kodekeg = '';
	$jenisbelanja = 2;
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$spptgl = date('Y-m-d');
	
	$bulan = date('m');
	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Permintaan Uang Persediaan';
	$jeniskegiatan = '1';
	$penerimanama = '';
	$penerimanip = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
	$pptknama = '';
	$pptknip = '';
	$jumlah = 0;
	$pajak = 0;
	$potongan = 0;
	$netto = 0;
	

	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	

	
	$title = 'SPP UP Tahun ' . apbd_tahun();
	drupal_set_title($title);
	
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);			
	} else {	
		//SKPD
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		if($results){
			foreach($results as $data) {
			  $option_skpd[$data->kodeuk] = $data->namasingkat; 
			}
		}		
		$form['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#prefix' => '<div id="skpd-replace">', 
			//'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodeuk,
		);	
	}
	$form['sppno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		////'#required' => TRUE,
		'#default_value' => $sppno,
	);
	$form['spptgl'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#date_year_range' => '-3:+3',
		'#default_value'=> array(
			'year' => format_date($spptgl, 'custom', 'Y'),
			'month' => format_date($spptgl, 'custom', 'n'), 
			'day' => format_date($spptgl, 'custom', 'j'), 
		  ), 
		
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
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


	//BENDAHARA
	//'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank','bendaharaatasnama', 'bendaharanpwp'));
	$query->condition('u.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$penerimanama = $data->bendaharaatasnama;
		$penerimanip = $data->bendaharanip;
		$penerimabankrekening = $data->bendahararekening;
		$penerimabanknama = $data->bendaharabank;
		$penerimanpwp = $data->bendaharanpwp;
	}
	/*
	$query=db_query("SELECT distinct kodeuk,penerimanama FROM `dokumen` where (penerimanama like 'bend pengel%' or penerimanama like 'bendpengel%' or penerimanama like 'upt dis%' ) and kodeuk=:kodeuk and jurnalsudah=1",array(':kodeuk'=>$kodeuk));
	foreach($query as $data){
		$penerimanama=$data->penerimanama;
	};
	*/
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'BENDAHARA',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
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
	$query->condition('dk.jenis', 0, '=');
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
		
		
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);


	return $form;
}

function sppup_new_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	$sppno = $form_state['values']['sppno'];
	$spptgl = $form_state['values']['spptgl'];
	$bulan = $spptgl['month'];
	$spptglsql = $spptgl['year'] . '-' . $spptgl['month'] . '-' . $spptgl['day'];

	$keperluan = $form_state['values']['keperluan'];
	$jumlah = $form_state['values']['jumlah'];

	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanip = $form_state['values']['penerimanip'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];
	
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];

	//BEGIN TRANSACTION
	//$transaction = db_transaction();
	
	//DOKUMEN
	//try {
		$dokid = apbd_getkodedokumen($kodeuk);

		//KELENGKAPAN
		$num_deleted = db_delete('dokumenkelengkapan')
		  ->condition('dokid', $dokid)
		  ->execute();		
		
		for ($n=1; $n <= $jumlahrekkelengkapan; $n++){
			$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
			$ada = $form_state['values']['adakelengkapan' . $n];
			$tidakada = $form_state['values']['tidakadakelengkapan' . $n];
			
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
				->fields(array('dokid', 'bulan', 'jenisdokumen', 'kodeuk', 'sppno', 'spptgl', 'keperluan', 
								'jumlah', 'netto', 'penerimanama', 'penerimanip', 'penerimabanknama', 
								'penerimabankrekening', 'penerimanpwp'))
				->values(
					array(
						'dokid'=> $dokid,
						'bulan'=> $bulan,
						'jenisdokumen' => 0,
						'kodeuk' => $kodeuk,
						'sppno' => $sppno,
						'spptgl' =>$spptglsql,
						'keperluan' => $keperluan, 
						'jumlah' => $jumlah,
						'netto' => $jumlah,
						'penerimanama' => $penerimanama,
						'penerimanip' => $penerimanip, 
						'penerimabanknama' => $penerimabanknama,
						'penerimabankrekening' => $penerimabankrekening,
						'penerimanpwp' => $penerimanpwp,
					)
				);
		//dpq $query;		
		//echo (string) $query;
		$res = $query->execute();
		
	
	//}
	//	catch (Exception $e) {
		//$transaction->rollback();
		//watchdog_exception('sppgaji-' . $nourut, $e);
	//}
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('sppuparsip');
}


?>
