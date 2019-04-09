<?php
function padspp_newpost_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('padspp_newpost_main_form');
	return drupal_render($output_form);// . $output;
	
}

function padspp_newpost_main_form($form, &$form_state) {
	
	$kodero = arg(2);	
	$title = 'SPP Restitusi Pajak PAD';
	$kodekeg = '000000';
		
	$dokid = '';
	$jenisdokumen = 6;
	$kodeuk = '00';
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('m');
	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$jeniskegiatan = '1';
	$penerimanama = '';
	$penerimanip = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
	$jumlah = 0;
	
	$keperluan = 'Pembayaran restitusi pajak';
	//dpq($query);	
		
	drupal_set_title($title);
	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);

	$form['sppno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		////'#required' => TRUE,
		'#default_value' => $sppno,
	);
	/*
	$form['spptgl'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $spptgl,
		'#default_value'=> array(
			'year' => format_date($spptgl, 'custom', 'Y'),
			'month' => format_date($spptgl, 'custom', 'n'), 
			'day' => format_date($spptgl, 'custom', 'j'), 
		  ), 
		
	);*/
	
	$form['spptgl_title'] = array(
		'#markup' => 'Tanggal SPP',
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
	
	$form['bulan'] = array(
		'#type' => 'value',
		'#value' => $bulan,
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);


	//BENDAHARA
	//'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('namasingkat', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'));
	$query->condition('u.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$penerimanama = 'BENDAHARA PENGELUARAN ' . $data->namasingkat . ' (' . $data->bendaharanama . ')';
		$penerimanip = $data->bendaharanip;
		$penerimabankrekening = $data->bendahararekening;
		$penerimabanknama = $data->bendaharabank;
		$penerimanpwp = $data->bendaharanpwp;
	}
	
	$query=db_query("SELECT distinct kodeuk,penerimanama FROM `dokumen` where (penerimanama like 'bend pengel%' or penerimanama like 'bendpengel%' or penerimanama like 'upt dis%' ) and kodeuk=:kodeuk and jurnalsudah=1",array(':kodeuk'=>$kodeuk));
	foreach($query as $data){
		$penerimanama=$data->penerimanama;
	};
	
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
	$query->condition('dk.jenis', 6, '=');
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
		
	//ITEM REKENING
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		//'#title'=> 'REKENING',
		'#title'=> 'REKENING<em class="text-info pull-right">' . apbd_fn($jumlah) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formrekening']['table']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="140px">JUMLAH</th></tr>',
		 '#suffix' => '</table>',
	);	
 
	$i = 1; 
	$cair = 0;
	$jumlah = 0;
	$query = db_select('rincianobyek', 'ro');
	$query->fields('ro', array('uraian'));
	$query->condition('ro.kodero', $kodero, '=');	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$uraian = $data->uraian;
		$form['formrekening']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $kodero,
		); 		
		$form['formrekening']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kodero,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['jumlahapbd' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);				
	}
	
	$form['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);

	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	/*
	$form['formdata']['submitspp1']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 1',
		'#attributes' => array('class' => array('btn btn-success btn-sm disabled')),
	);
	$form['formdata']['submitspp2']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 2',
		'#attributes' => array('class' => array('btn btn-success btn-sm disabled')),
	);
	$form['formdata']['submitspp3']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 3',
		'#attributes' => array('class' => array('btn btn-success btn-sm disabled')),
	);
	*/

	return $form;
}

function padspp_newpost_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	$nourut = $form_state['values']['nourut'];
	
	$bulan = $form_state['values']['bulan'];
	
	$sppno = $form_state['values']['sppno'];
	//$spptgl = $form_state['values']['spptgl'];
	//$spptglsql = $spptgl['year'] . '-' . $spptgl['month'] . '-' . $spptgl['day'];
	$spptglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spptgl']);

	$keperluan = $form_state['values']['keperluan'];

	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanip = $form_state['values']['penerimanip'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
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
		
		//REKENING
		$num_deleted = db_delete('dokumenrekening')
		  ->condition('dokid', $dokid)
		  ->execute();			
		$totaljumlah = 0;
		for ($n=1; $n <= $jumlahrekrekening; $n++){
			$kodero = $form_state['values']['koderoapbd' . $n];
			$jumlah = $form_state['values']['jumlahapbd' . $n];
			
			db_insert('dokumenrekening')
				->fields(array('dokid', 'kodero', 'jumlah'))
				->values(array(
						'dokid'=> $dokid,
						'kodero' => $kodero,
						'jumlah' => $jumlah,
						))
				->execute();
			
			$totaljumlah = $totaljumlah + $jumlah;
		}

		//DOKUMEN
		$query = db_insert('dokumen')
				->fields(array('dokid', 'bulan', 'kodekeg', 'kodeuk', 'sppno', 'spptgl', 'keperluan', 
								'jumlah', 'netto', 'penerimanama', 'penerimanip', 'penerimabanknama', 
								'penerimabankrekening', 'penerimanpwp', 'jenisdokumen'))
				->values(
					array(
						'dokid'=> $dokid,
						'bulan'=> $bulan,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'sppno' => $sppno,
						'spptgl' =>$spptglsql,
						'keperluan' => $keperluan, 
						'jumlah' => $totaljumlah,
						'netto' => $totaljumlah,
						'penerimanama' => $penerimanama,
						'penerimanip' => $penerimanip, 
						'penerimabanknama' => $penerimabanknama,
						'penerimabankrekening' => $penerimabankrekening,
						'penerimanpwp' => $penerimanpwp,
						'jenisdokumen' => '6',
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
	drupal_goto('padspparsip');
}


?>
