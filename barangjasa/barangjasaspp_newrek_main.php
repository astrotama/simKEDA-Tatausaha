<?php
function barangjasaspp_newrek_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('barangjasaspp_newrek_main_form');
	return drupal_render($output_form);// . $output;
	
}

function barangjasaspp_newrek_main_form($form, &$form_state) {
	
	$kodekeg = arg(2);	
	//drupal_set_message($kodekeg);	
	$title = 'SPP Barang Jasa';
	
	$dokid = '';
	$jenisdokumen = 4;
	$kodeuk = '';
	$jenisbelanja = 2;
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('m');
	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Pembayaran LS ';
	$jeniskegiatan = '2';
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
	
	//KEGIATAN
	$query = db_select('kegiatanskpd', 'k');
	$query->fields('k', array('kodekeg', 'kodeuk' , 'kegiatan'));
	$query->condition('k.jenis', 2, '=');
	$query->condition('k.kodekeg', $kodekeg, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		//$kodekeg = $data->kodekeg;
		$kodeuk = $data->kodeuk;
		$keperluan = $data->kegiatan;
	}
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	
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
	);/*
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
		
	);
	*/
	
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
	
	/*
	$opt_bulan['1'] = 'Januari';
	$opt_bulan['2'] = 'Februari';
	$opt_bulan['3'] = 'Maret';
	$opt_bulan['4'] = 'April';
	$opt_bulan['5'] = 'Mei';
	$opt_bulan['6'] = 'Juni';
	$opt_bulan['7'] = 'Juli';
	$opt_bulan['8'] = 'Agustus';
	$opt_bulan['9'] = 'September';
	$opt_bulan['10'] = 'Oktober';
	$opt_bulan['11'] = 'Nopember';
	$opt_bulan['12'] = 'Desember';
	$form['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $bulan,
	);
	*/

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
	/*
	$query=db_query("SELECT distinct kodeuk,penerimanama FROM `dokumen` where (penerimanama like 'bend pengel%' or penerimanama like 'bendpengel%' or penerimanama like 'upt dis%' ) and kodeuk=:kodeuk and jurnalsudah=1",array(':kodeuk'=>$kodeuk));
	foreach($query as $data){
		$penerimanama=$data->penerimanama;
	};
	*/

	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PENERIMA',
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
		
		$option_bank[''] = 'ISIKAN KODE BANK';
		$results = db_query('select kodebank, namabank from {bank} order by nourut, namabank');
		# build the table fields
		if($results){
			foreach($results as $data) {
				$option_bank[$data->kodebank] = $data->kodebank . ' - ' . $data->namabank; 
			}
		}	
		$form['formpenerima']['kodebank'] = array(
			'#type' => 'select',
			'#title' =>  t('Kode Bank'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#prefix' => '<div id="skpd-replace">',
			//'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_bank,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodebank,
		);		
		$form['formpenerima']['penerimabanknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama Bank'),
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
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0;
	$query = db_select('ltkelengkapandokumen', 'dk');
	$query->fields('dk', array('kodekelengkapan', 'uraian', 'nomor'));
	$query->condition('dk.jenis', 4, '=');
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
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">ANGGARAN</th><th width="50px">CAIR</th><th width="130px">BATAS</th><th width="130px">JUMLAH</th></tr>',
		 '#suffix' => '</table></div>',
	);	
 
		//'belanjagajipokok', 'belanjatunjangankeluarga', 'belanjatunjanganjabatan', 
		//'belanjatunjanganfungsional', 'belanjatunjanganumum', 'belanjatunjanganberas', 'belanjatunjanganpajak', 
		//'belanjapembulatan', 'belanjaaskes', 'belanjajkk', 'belanjajkm'
	$i = 0; 
	$cair = 0;
	$jumlah = 0;
	$query = db_select('anggperkeg', 'a');
	$query->join('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('a', array('anggaran'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	
	$query->orderBy('ro.kodero', 'ASC');
	$results = $query->execute();
	
	
	foreach ($results as $data) {
		$i++; 
		
		$kodero = $data->kodero;
		$uraian = $data->uraian;
		
		$cair = apbd_readrealisasikegiatan_rekening('new', $kodekeg, $kodero, date('Y-m-d'));
		
		$form['formrekening']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $kodero,
		); 
		$form['formrekening']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
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
		$form['formrekening']['table']['anggaran' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['cair' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($cair) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['batas' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data->anggaran - $cair) . '</p>', 
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

	//PAJAK	
	$form['formpajak'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formpajak']['tablepajak']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="20px">Lns</th><th width="260px">KETERANGAN</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0;
	$query = db_select('ltpajak', 'p');
	$query->fields('p', array('kodepajak', 'uraian'));
	$query->orderBy('p.kodepajak', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++; 
		$kode = $data->kodepajak;
		$uraian = $data->uraian;
		$jumlah = '0';
		$keterangan = '';;
		$form['formpajak']['tablepajak']['kodepajak' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formpajak']['tablepajak']['uraianpajak' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpajak']['tablepajak']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpajak']['tablepajak']['kode' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kode,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpajak']['tablepajak']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpajak']['tablepajak']['jumlahpajak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpajak']['tablepajak']['sudahbayar' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> '0', 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['formpajak']['tablepajak']['keteranganpajak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $keterangan, 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	
		
	}
	$form['jumlahrekpajak']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	//PPTK
	$form['formpptk'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PPTK',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formpptk']['pptknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> '', 
		);				
		$form['formpptk']['pptknip']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NIP'),
			//'#required' => TRUE,
			'#default_value'=> '', 
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

function barangjasaspp_newrek_main_form_validate($form, &$form_state) {
	$kodebank = $form_state['values']['kodebank'];
	$pptknama = $form_state['values']['pptknama'];
	$pptknip = $form_state['values']['pptknama'];

			 
	if ($kodebank=='') {
		form_set_error('kodebank', 'Isikan Kode Bank');
	}
	if ($pptknama=='') {
		form_set_error('pptknama', 'Isikan Nama PPTK');
	}	
	if ($pptknip=='') {
		form_set_error('pptknip', 'Isikan NIP PPTK');
	}		
} 


function barangjasaspp_newrek_main_form_submit($form, &$form_state) {
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
	$kodebank = $form_state['values']['kodebank'];

	//PPTK
	$pptknama = $form_state['values']['pptknama'];
	$pptknip = $form_state['values']['pptknip'];
	
	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	$jumlahrekpajak = $form_state['values']['jumlahrekpajak'];
	
	//BEGIN TRANSACTION
	//$transaction = db_transaction();
	 
	//DOKUMEN
	//try {
		$dokid = apbd_getkodedokumen($kodeuk);

		//drupal_set_message($dokid);
		
		
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
				->fields(array('dokid', 'kodekeg','kodero', 'jumlah'))
				->values(array(
						'dokid'=> $dokid,
						'kodekeg' => $kodekeg,
						'kodero' => $kodero,
						'jumlah' => $jumlah,
						))
				->execute();
			
			$totaljumlah = $totaljumlah + $jumlah;
		}

		//PAJAK
		$totalpajak = 0;
		$num_deleted = db_delete('dokumenpajak')
		  ->condition('dokid', $dokid)
		  ->execute();			
		for ($n=1; $n <= $jumlahrekpajak; $n++) {
			$kodepajak = $form_state['values']['kodepajak' . $n];
			$jumlah = $form_state['values']['jumlahpajak' . $n];
			$keterangan = $form_state['values']['keteranganpajak' . $n];
			$sudahbayar = $form_state['values']['sudahbayar' . $n];
			
			db_insert('dokumenpajak')
				->fields(array('dokid', 'nourut', 'kodepajak', 'jumlah', 'keterangan', 'sudahbayar'))
				->values(array(
						'dokid'=> $dokid,
						'nourut'=> $n,
						'kodepajak' => $kodepajak,
						'jumlah' => $jumlah,
						'keterangan' => $keterangan,
						'sudahbayar' => $sudahbayar, 
						))
				->execute();
			
			$totalpajak = $totalpajak + $jumlah;
			
		}	
		
		
		//DOKUMEN
		$query = db_insert('dokumen')
				->fields(array('dokid', 'bulan', 'kodekeg', 'kodeuk', 'sppno', 'spptgl', 'spmtgl', 'keperluan', 
								'jumlah', 'netto', 'penerimanama', 'penerimanip', 'penerimabanknama', 
								'penerimabankrekening', 'penerimanpwp', 'pptknama', 'pptknip', 'jenisdokumen', 'kodebank'))
				->values(
					array(
						'dokid'=> $dokid,
						'bulan'=> $bulan,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'sppno' => $sppno,
						'spptgl' =>$spptglsql,
						'spmtgl' =>$spptglsql,
						'keperluan' => $keperluan, 
						'jumlah' => $totaljumlah,
						'netto' => $totaljumlah,
						'penerimanama' => $penerimanama,
						'penerimanip' => $penerimanip, 
						'penerimabanknama' => $penerimabanknama,
						'penerimabankrekening' => $penerimabankrekening,
						'penerimanpwp' => $penerimanpwp,
						'pptknama' => $pptknama,
						'pptknip' => $pptknip,
						'jenisdokumen' => '4',
						'kodebank' => $kodebank,
					)
				);
		//dpq $query;		
		//echo (string) $query;
		$res = $query->execute();
		
		//GAJI
		/*
		$query = db_update('gaji')
		->fields(
				array(
					'proses' => 1,
					'dokid' => $dokid,
				)
			);
		$query->condition('nourut', $nourut, '=');
		$res = $query->execute();
		*/
		
	//}
	//	catch (Exception $e) {
		//$transaction->rollback();
		//watchdog_exception('sppgaji-' . $nourut, $e);
	//}
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('barangjasaspparsip');
}


?>
