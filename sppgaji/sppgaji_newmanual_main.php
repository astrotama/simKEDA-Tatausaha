<?php
function sppgaji_newmanual_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('sppgaji_newmanual_main_form');
	return drupal_render($output_form);// . $output;
	
}

function sppgaji_newmanual_main_form($form, &$form_state) {
	
	$tamsil = arg(2);	
	
	if ($tamsil=='tamsil') {
		$jenisgaji = '4';
		$title = 'SPP Tamsil/Insentif Baru';

	} else {
		$jenisgaji = '0';
		$title = 'SPP Gaji Baru';
	}
	
	$nourut = 0;
	$dokid = '';
	$jenisdokumen = 3;
	$kodeuk = '';
	$kodekeg = '';
	$jenisbelanja = 1;
	$sppno = '';
	
	$bulan = date('n');
	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$bulan++;
	if ($bulan=='13') $bulan = '1';
	
	
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Gaji bulan ' . apbd_get_namabulan($bulan);
	$jeniskegiatan = '1';
	$penerimanama = '';
	$penerimanip = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
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

	//drupal_set_message($kodeuk);	
	
	//KEGIATAN
	$query = db_select('kegiatanskpd', 'k');
	$query->fields('k', array('kodekeg','kegiatan'));
	$query->condition('k.jenis', 1, '=');
	$query->condition('k.isppkd', 0, '=');
	$query->condition('k.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$kodekeg = $data->kodekeg;
		$keperluan = $data->kegiatan . ' bulan ' . apbd_get_namabulan($bulan);
	}
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	
	drupal_set_title($title);
	
	$form['nourut'] = array(
		'#type' => 'value',
		'#value' => $nourut,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);
	



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
	
	$kegsudahok = isjurnalsudahuk($kodekeg);
	
	if ($kegsudahok==false) {
		$form['kegbelumok'] = array(
			'#type' => 'markup',
			'#markup' => '<p style="color:red"><em>Ada SP2D yang belum divalidasi oleh Petugas Akuntansi (dijurnal) sehingga pengajuan SPP belum bisa dilakukan.</em></p>',
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

	//Jenis
	if ($tamsil=='tamsil') {
		$form['jenisgaji'] = array(
			'#type' => 'value',
			'#value' => $jenisgaji,
		);	 

	} else {
		$opt_gaji['0'] = 'Reguler';
		$opt_gaji['1'] = 'Kekurangan';	
		$opt_gaji['2'] = 'Susulan';	
		$opt_gaji['3'] = 'Terusan';	
		$form['jenisgaji'] = array(
			'#type' => 'select',
			'#title' =>  t('Jenis Gaji'),
			'#options' => $opt_gaji,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $jenisgaji,
		);	 
	}
	
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
	$query->fields('u', array('bendaharanama','bendaharaatasnama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'));
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
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0;
	$query = db_select('ltkelengkapandokumen', 'dk');
	$query->fields('dk', array('kodekelengkapan', 'uraian', 'nomor'));
	$query->condition('dk.jenis', 3, '=');
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
	//drupal_set_message(date('Y-m-d'));
	//$re=db_query("")
	$jumlah = 0;
	/*$query = db_select('anggperkeg', 'a');
	$query->join('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('a', array('anggaran'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	
	//if ($tamsil=='tamsil') $query->condition('a.kodero', db_like('51101') . '%', 'LIKE');
	if ($tamsil=='tamsil') 
		$query->condition('a.kodero', '51101099', '>');
	else
		$query->condition('a.kodero', db_like('51101') . '%', 'LIKE');
	
	$query->orderBy('ro.kodero', 'ASC');
	$results = $query->execute();*/
	if ($tamsil=='tamsil') 
		$results=db_query("SELECT ro.kodero AS kodero, ro.uraian AS uraian, a.anggaran AS anggaran
			FROM 
			anggperkeg a
			INNER JOIN rincianobyek ro ON a.kodero=ro.kodero
			WHERE  (a.kodekeg = :kodekeg ) AND (a.kodero > '51101099') 
			ORDER BY ro.kodero ASC",array(':kodekeg'=>$kodekeg));
	else
		//if ($kodeuk=='01') 
			$results=db_query("SELECT ro.kodero AS kodero, ro.uraian AS uraian, a.anggaran AS anggaran
					FROM 
					anggperkeg a
					INNER JOIN rincianobyek ro ON a.kodero=ro.kodero
					WHERE  a.kodekeg = :kodekeg AND (a.kodero LIKE '51101%'  or a.kodero LIKE '51103%') 
					ORDER BY ro.kodero ASC",array(':kodekeg'=>$kodekeg));
		//else 
		//$results=db_query("SELECT ro.kodero AS kodero, ro.uraian AS uraian, a.anggaran AS anggaran
		//		FROM 
		//		anggperkeg a
		//		INNER JOIN rincianobyek ro ON a.kodero=ro.kodero
		//		WHERE  a.kodekeg = :kodekeg AND (a.kodero LIKE '51101%'  or a.kodero='51103001') 
		//		ORDER BY ro.kodero ASC",array(':kodekeg'=>$kodekeg));
	//drupal_set_message($kodekeg);
	foreach ($results as $data) {
		$i++; 
		
		if ($data->kodero=='51101002') {
			$kodero = $data->kodero;
			$uraian = $data->uraian;
			$cair=0;
			$now=date('Y-m-d');
			//drupal_set_message($now);
			$re=db_query("SELECT dokid FROM `dokumen` where kodeuk=:kodeuk and sp2dtgl < :tgl",array(':kodeuk'=>$kodeuk,':tgl'=>$now));
			foreach($re as $dat){
				$res=db_query("select sum(jumlah) as jumlah from dokumenrekening where dokid=:dokid and kodero=:kodero",array(':dokid'=>$dat->dokid,':kodero'=>$kodero));
				
				foreach($res as $datk){
					$cair+=$datk->jumlah;
					//drupal_set_message($cair);
				}
			}
			$form['formrekening']['table']['koderoapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $kodero,
			); 
			$form['formrekening']['table']['uraianapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			$form['formrekening']['table']['detil' . $i]= array(
					'#type' => 'value',
					'#value' => '0',
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
				'#prefix' => '<td>',
				'#markup'=> '<p class="glyphicon glyphicon-menu-down" style="color:red"> Isi detilnya</p>', 
				'#suffix' => '</td></tr>',
			);	
			

			//ISTRI/SUAMI
			$i++;
			$kodero = $data->kodero . '01';
			
			$uraian = 'Belanja Tunjangan Istri/Suami';
			$form['formrekening']['table']['koderoapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $kodero,
			); 
			$form['formrekening']['table']['uraianapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			$form['formrekening']['table']['detil' . $i]= array(
					'#type' => 'value',
					'#value' => '1',
			); 
			
			$form['formrekening']['table']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => '',
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
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
				'#markup'=> '', 
				'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['cair' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> '', 
				'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['batas' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> '', 
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

			//ANAK
			$i++;
			$kodero = $data->kodero . '02';
			$uraian = 'Belanja Tunjangan Anak';
			$form['formrekening']['table']['koderoapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $kodero,
			); 
			$form['formrekening']['table']['uraianapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			$form['formrekening']['table']['detil' . $i]= array(
					'#type' => 'value',
					'#value' => '1',
			); 
			
			$form['formrekening']['table']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => '',
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
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
				'#markup'=> '', 
				'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['cair' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> '', 
				'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['batas' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> '', 
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
						
		} else {	//SELAIN TUNJANGAN
		
			
			
			$kodero = $data->kodero;
			$uraian = $data->uraian;
			$cair=0;
			$now=date('Y-m-d');
			//drupal_set_message($now);
			$re=db_query("SELECT dokid FROM `dokumen` where kodeuk=:kodeuk and sp2dtgl < :tgl",array(':kodeuk'=>$kodeuk,':tgl'=>$now));
			foreach($re as $dat){
				$res=db_query("select sum(jumlah) as jumlah from dokumenrekening where dokid=:dokid and kodero=:kodero",array(':dokid'=>$dat->dokid,':kodero'=>$kodero));
				
				foreach($res as $datk){
					$cair+=$datk->jumlah;
					//drupal_set_message($cair);
				}
			}
			$form['formrekening']['table']['koderoapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $kodero,
			); 
			$form['formrekening']['table']['uraianapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			$form['formrekening']['table']['detil' . $i]= array(
					'#type' => 'value',
					'#value' => '0',
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
	}
	
	$form['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);

		
	//POTONGAN	
	if ($tamsil!='tamsil') {
		$keterangan = '';
		$form['formpotongan'] = array (
			'#type' => 'fieldset',
			'#title'=> 'POTONGAN<em class="text-info pull-right">' . apbd_fn($potongan) . '</em>',
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,        
		);	
		$form['formpotongan']['tablepotongan']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="260px">KETERANGAN</th></tr>',
			 '#suffix' => '</table>',
		);	
			$i = 0;
	 
			//potonganiwp', 'potongantaperum', 'potonganaspph', 'potonganaskes', 'potonganjkk', 'potonganjkm

			$i++; 
			$kode = '01';
			$uraian = 'Iuran Wajib Pegawai 8%';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	

		$i++; 
		$kode = '02';
		$uraian = 'Iuran Wajib Pegawai 2%';
		$jumlah = 0;
		$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpotongan']['tablepotongan']['kode' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kode,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $keterangan, 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);				
			$i++; 
			$kode = '03';
			$uraian = 'Tabungan Perumahan Pegawai';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	

			$i++; 
			$kode = '04';
			$uraian = 'A s k e s';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	


			$i++; 
			$kode = '05';
			$uraian = 'P P h';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	

			$i++; 
			$kode = '06';
			$uraian = 'Jaminan Kecelakaan Kerja';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	


			$i++; 
			$kode = '07';
			$uraian = 'Jaminan Kematian';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	

			
			$i++; 
			$kode = '08';
			$uraian = 'Pembulatan';
			$jumlah = 0;
			$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	
		
			
		$form['jumlahrekpotongan']= array(
			'#type' => 'value',
			'#value' => $i,
		);
		
	} else {
		$form['jumlahrekpotongan']= array(
			'#type' => 'value',
			'#value' => 0,
		);
		
	}

	//PAJAK	
	if (($tamsil=='tamsil') or ($kodeuk=='01')) {
		
		$form['formpajak'] = array (
			'#type' => 'fieldset',
			'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);	
		$form['formpajak']['tablepajak']= array(
			'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="260px">KETERANGAN</th></tr>',
			 '#suffix' => '</table></div>',
		);	
		$i = 0;
		$query = db_select('ltpajak', 'p');
		$query->fields('p', array('kodepajak', 'uraian'));
		if ($kodeuk!='02') $query->condition('p.kodepajak', '01', '=');
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
		
	} else {
		$form['jumlahrekpajak']= array(
			'#type' => 'value',
			'#value' => 0,
		);
	}	
	
	//PNS	
	$jumlahpns = 0;
	$form['formpns'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JUMLAH PNS<em class="text-info pull-right">' .  $jumlahpns . ' orang</em>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formpns']['tablepns']= array(
		'#prefix' => '<table class="table table-hover"><tr><th></th><th width="130px">PNS</th><th width="130px">ISTRI/SUAMI</th><th width="130px">ANAK</th></tr>',
		 '#suffix' => '</table>',
	);	
		$i = 5;
 
		//'g4pns', 'g4istri', 'g4anak', 'g3pns', 'g3istri', 'g3anak', 
		//'g2pns', 'g2istri', 'g2anak', 'g1pns', 'g1istri', 'g1anak'

		$i--; 
		$uraian = 'GOLONGAN IV';
		$pns = 0;   //$g4pns;
		$istri = 0;   //$g4istri;
		$anak = 0;   //$g4anak;
		$form['formpns']['tablepns']['kodepns' . $i]= array(
				'#type' => 'value',
				'#value' => $i,
		); 
		$form['formpns']['tablepns']['uraianpns' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpns']['tablepns']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpns']['tablepns']['jumlahpnspns' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $pns, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsistri' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $istri, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsanak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $anak, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	

		$i--; 
		$uraian = 'GOLONGAN III';
		$pns = 0;   //$g3pns;
		$istri = 0;   //$g3istri;
		$anak = 0;   //$g3anak;
		$form['formpns']['tablepns']['kodepns' . $i]= array(
				'#type' => 'value',
				'#value' => $i,
		); 
		$form['formpns']['tablepns']['uraianpns' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpns']['tablepns']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpns']['tablepns']['jumlahpnspns' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $pns, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsistri' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $istri, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsanak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $anak, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	

		$i--; 
		$uraian = 'GOLONGAN II';
		$pns = 0;   //$g2pns;
		$istri = 0;   //$g2istri;
		$anak = 0;   //$g2anak;
		$form['formpns']['tablepns']['kodepns' . $i]= array(
				'#type' => 'value',
				'#value' => $i,
		); 
		$form['formpns']['tablepns']['uraianpns' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpns']['tablepns']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpns']['tablepns']['jumlahpnspns' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $pns, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsistri' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $istri, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsanak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $anak, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	

		$i--; 
		$uraian = 'GOLONGAN I';
		$pns = 0;   //$g1pns;
		$istri = 0;   //$g1istri;
		$anak = 0;   //$g1anak;
		$form['formpns']['tablepns']['kodepns' . $i]= array(
				'#type' => 'value',
				'#value' => $i,
		); 
		$form['formpns']['tablepns']['uraianpns' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpns']['tablepns']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpns']['tablepns']['jumlahpnspns' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $pns, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsistri' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $istri, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsanak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $anak, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	


	
	if ($kegsudahok) {	
		$form['formdata']['submit']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
			'#attributes' => array('class' => array('btn btn-success btn-sm')),
		);
	}

	return $form;
}

function sppgaji_newmanual_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
		
	$kodekeg = $form_state['values']['kodekeg'];
	$nourut = $form_state['values']['nourut'];
	
	$bulan = $form_state['values']['bulan'];
	
	$sppno = $form_state['values']['sppno'];
	//$spptgl = $form_state['values']['spptgl'];
	//$spptglsql = $spptgl['year'] . '-' . $spptgl['month'] . '-' . $spptgl['day'];
	$spptglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spptgl']);

	$keperluan = $form_state['values']['keperluan'];
	$jenisgaji = $form_state['values']['jenisgaji'];

	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanip = $form_state['values']['penerimanip'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	//PPTK
	$pptknama = $form_state['values']['pptknama'];
	$pptknip = $form_state['values']['pptknip'];
	
	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
	$jumlahrekpotongan = $form_state['values']['jumlahrekpotongan'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	$jumlahrekpajak = $form_state['values']['jumlahrekpajak'];
		
	//BEGIN TRANSACTION
	//$transaction = db_transaction();
	
	//DOKUMEN
	//try {
		$dokid = apbd_getkodedokumen($kodeuk);
		drupal_set_message($dokid);
		
		
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
			$jumlahtunjangan = 0;
			$num_deleted = db_delete('dokumenrekening')
			  ->condition('dokid', $dokid)
			  ->execute();			
			$totaljumlah = 0;
			for ($n=1; $n <= $jumlahrekrekening; $n++){
				$kodero = $form_state['values']['koderoapbd' . $n];
				
				
				if (substr($kodero, 0, 8) == '51101002') {			//TUNJANGAN
					if ($form_state['values']['detil' . $n] == '1') {
						$jumlah = $form_state['values']['jumlahapbd' . $n];
						$jumlahtunjangan += $jumlah;

						db_insert('dokumenrekening')
							->fields(array('dokid','kodekeg', 'kodero', 'jumlah'))
							->values(array(
									'dokid'=> $dokid,
									'kodekeg' => $kodekeg,
									'kodero' => $kodero,
									'jumlah' => $jumlah,
									))
							->execute();
						
					}
					
				} else {
					$jumlah = $form_state['values']['jumlahapbd' . $n];
					
					db_insert('dokumenrekening')
						->fields(array('dokid','kodekeg', 'kodero', 'jumlah'))
						->values(array(
								'dokid'=> $dokid,
								'kodekeg' => $kodekeg,
								'kodero' => $kodero,
								'jumlah' => $jumlah,
								))
						->execute();
					
					$totaljumlah = $totaljumlah + $jumlah;
				}
			}
			//Tunjangan
			if ($jumlahtunjangan > 0) {
				$kodero = '51101002';
				db_insert('dokumenrekening')
					->fields(array('dokid', 'kodekeg','kodero', 'jumlah'))
					->values(array(
							'dokid'=> $dokid,
							'kodekeg' => $kodekeg,
							'kodero' => $kodero,
							'jumlah' => $jumlahtunjangan,
							))
					->execute();
				
				$totaljumlah = $totaljumlah + $jumlahtunjangan;
			}

			//POTONGAN
			$totalpotongan = 0;
			$num_deleted = db_delete('dokumenpotongan')
			  ->condition('dokid', $dokid)
			  ->execute();			
			for ($n=1; $n <= $jumlahrekpotongan; $n++){
				$kodepotongan = $form_state['values']['kodepotongan' . $n];
				$jumlah = $form_state['values']['jumlahpotongan' . $n];
				$keterangan = $form_state['values']['keteranganpotongan' . $n];
				
				db_insert('dokumenpotongan')
					->fields(array('dokid', 'kodepotongan', 'jumlah', 'keterangan'))
					->values(array(
							'dokid'=> $dokid,
							'kodepotongan' => $kodepotongan,
							'jumlah' => $jumlah,
							'keterangan' => $keterangan,
							))
					->execute();
				
				$totalpotongan = $totalpotongan + $jumlah;
			}

			//PAKAL
			$totalpajak = 0;
			//KELENGKAPAN
			$num_deleted = db_delete('dokumenpajak')
			  ->condition('dokid', $dokid)
			  ->execute();		
			for ($n=1; $n <= $jumlahrekpajak; $n++) {
				$kodepajak = $form_state['values']['kodepajak' . $n];
				$jumlah = $form_state['values']['jumlahpajak' . $n];
				$keterangan = $form_state['values']['keteranganpajak' . $n];
				
				db_insert('dokumenpajak')
					->fields(array('dokid', 'nourut', 'kodepajak', 'jumlah', 'keterangan'))
					->values(array(
							'dokid'=> $dokid,
							'nourut'=> $n,
							'kodepajak' => $kodepajak,
							'jumlah' => $jumlah,
							'keterangan' => $keterangan,
							))
					->execute();
				
				$totalpajak = $totalpajak + $jumlah;
				
			}	
			
			//PNS
			$num_deleted = db_delete('dokumenpns')
			  ->condition('dokid', $dokid)
			  ->execute();			
			for ($n=1; $n <= 4; $n++){
				$jumlahpnspns = $form_state['values']['jumlahpnspns' . $n];
				$jumlahpnsistri = $form_state['values']['jumlahpnsistri' . $n];
				$jumlahpnsanak = $form_state['values']['jumlahpnsanak' . $n];
				
				db_insert('dokumenpns')
					->fields(array('dokid', 'golongan', 'pns', 'istri', 'anak'))
					->values(array(
							'dokid'=> $dokid,
							'golongan' => $n,
							'pns' => $jumlahpnspns,
							'istri' => $jumlahpnsistri,
							'anak' => $jumlahpnsanak,
							))
					->execute();
			}

			//DOKUMEN
			$query = db_insert('dokumen')
					->fields(array('dokid', 'bulan', 'kodekeg', 'kodeuk', 'sppno', 'spptgl', 'keperluan', 
									'jumlah', 'potongan', 'netto', 'penerimanama', 'penerimanip', 'penerimabanknama', 
									'kodebank', 'penerimabankrekening', 'penerimanpwp',  'jenisgaji'))
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
							'potongan' => $totalpotongan,
							'netto' => $totaljumlah - $totalpotongan,
							'penerimanama' => $penerimanama,
							'penerimanip' => $penerimanip, 
							'penerimabanknama' => $penerimabanknama,
							'kodebank' => '113',
							'penerimabankrekening' => $penerimabankrekening,
							'penerimanpwp' => $penerimanpwp,
							'jenisgaji' => $jenisgaji,
						)
					);
			//dpq $query;		
			//echo (string) $query;
			$res = $query->execute();
			
			//GAJI
			$query = db_update('gaji')
			->fields(
					array(
						'proses' => 1,
						'dokid' => $dokid,
					)
				);
			$query->condition('nourut', $nourut, '=');
			$res = $query->execute();
		
	//}
	//	catch (Exception $e) {
		//$transaction->rollback();
		//watchdog_exception('sppgaji-' . $nourut, $e);
	//}
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('sppgajiarsip');
}
/*
function isjurnalsudahuk($kodekeg) {
	
	$x = 0;
	
	$res = db_query('select count(dokid) as jumlah from {dokumen} where jenisdokumen=3 and kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$x = $data->jumlah;
	}
	
	
	return ($x==0);
	
	return true;
}
*/
?>
