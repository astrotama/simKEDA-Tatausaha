<?php
function spptu_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$dokid = arg(2);	
	$print = arg(3);	
	//drupal_set_message($dokid);
	if($print=='spp1') {			

		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/spp1', '', $url);
	
		$output = printspp_1($dokid);
		apbd_ExportSPP1($output, 'SPP_' . $dokid . '_SPP1.PDF', $url);
	 
	} else if ($print=='spp2') {			  
		$output = printspp_2($dokid);
		apbd_ExportSPP($output, 'SPP_' . $dokid . '_SPP2.PDF');
	
	} else if ($print=='spp3') {			  
		$output = printspp_3($dokid);
		apbd_ExportSPP($output, 'SPP_' . $dokid . '_SPP3.PDF');
	
	} else if ($print=='sppkelengkapan') {			  
		$output = printspp_kelengkapan($dokid);
		apbd_ExportSPP($output, 'SPP_' . $dokid . '_Kelengkapan.PDF');
		
	} else if ($print=='sp-lama') {			  
		$output = printspp_pernyataan($dokid);
		apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Pernyataan.PDF');
	
	} else if ($print=='sppk') {			  
		
		$edoc = 'E_SPPK_' . $dokid . '.PDF';
		if (is_eDocExistsBaru($edoc)) {
			drupal_goto('http://simkedajepara.web.id/edoc2019/spm/' . $edoc); 
			
		} else {	
			$output = printspp_pernyataan_ppk($dokid);
			//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Pernyataan.PDF');
			
			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url); 
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'SPPK_' . $dokid . '.PDF');
			
			
			
			apbd_ExportSPP_Logo_e_dok($output, $url, $fname);		
			drupal_goto('files/doc/' . $fname);
		}
	
	} else if ($print=='sp') {	
		//$edoc = 'E_SPPY_' . $dokid . '.PDF';
		$edoc = 'E_SPPY_' . $dokid . '.PDF';
		if (is_eDocExistsBaru($edoc)) {
			drupal_goto('http://simkedajepara.web.id/edoc2019/spm/' . $edoc); 
			
		} else {		
			$output = printspp_pernyataan($dokid);
			//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Keterangan.PDF');

			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url);
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'SPPY_' . $dokid . '.PDF');
			
			
			
			apbd_ExportSPP_Logo_e($output, $url, $fname);		
			drupal_goto('files/spm/' . $fname);
		}
		
	} else if ($print=='a21') {			  
		$output = printspp_a21($dokid);
		apbd_ExportSPP_No_Footer($output, 'SPP_' . $dokid . '_A2-1.PDF');

	} else if ($print=='ket-lama') {			  
		$output = printspp_keterangan($dokid);
		apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Keterangan.PDF');

	} else if ($print=='ket') {			
		
		$edoc = 'E_SPKT_' . $dokid . '.PDF';
		if (is_eDocExistsBaru($edoc)) {
			drupal_goto('http://simkedajepara.web.id/edoc2019/spm/' . $edoc);
			
		} else {		
			
			$output = printspp_keterangan($dokid);
			//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Keterangan.PDF');

			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url); 
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'SPKT_' . $dokid . '.PDF');
			
			 
			
			apbd_ExportSPP_Logo_e($output, $url, $fname);		
			drupal_goto('files/spm/' . $fname);
		}	
		
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('spptu_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
		
}

function spptu_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["spptulastpage"] = $referer;
	else
		$referer = $_SESSION["spptulastpage"];
	//drupal_set_message($referer);
	
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
	$keperluan = 'Permintaan Tambahan Uang Persediaan';
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
	$adagambar = 0;
	$dokid = arg(2);
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'kodekeg', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimanip', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'pptknama', 'sppok', 'spmok', 'sp2dok', 'spdno', 'spjlink'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SPP TU - ' . $data->keperluan ;
		
		$sppok = $data->sppok;
		$spmok = $data->spmok;
		$sp2dok = $data->sp2dok;
		
		$dokid = $data->dokid;
		$sppno = $data->sppno;
		//$spptgl = strtotime($data->spptgl);		
		$spptgl = dateapi_convert_timestamp_to_datetime($data->spptgl);
		$kodeuk = $data->kodeuk;

		$keperluan = $data->keperluan;
		
		$penerimanama = $data->penerimanama;
		$penerimanip = $data->penerimanip;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;

		$jumlah = $data->jumlah;
		$spdno = $data->spdno;
		$adagambar = strlen($data->spjlink);
	}
	
	drupal_set_title($title);

	$form['formatas']= array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
	);
	$form['formatas']['id']= array(
		'#markup' => 'ID: <strong>' . $dokid . '</strong>',
	);	
	$form['formatas']['submitgambar']= array(
		'#type' => 'submit', 
		'#value' => '<span class="glyphicon glyphicon-picture" aria-hidden="true"></span> S P J',
		'#attributes' => apbd_button_image_spj($adagambar)  ,
	);
	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	
	//SKPD
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			


	$form['formsppno'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',				
	);
	$form['formsppno']['sppno_title'] = array(
		'#markup' => '<small>No. SPP</small>',
	);
	$form['formsppno']['sppno'] = array(
		'#type' => 'textfield',
		//'#title' =>  t('No. SPP'),
		//'#prefix' => '<div class="col-md-6">',
		//'#suffix' => '</div>',				
		'#default_value' => $sppno,
	);

	$form['formspptgl'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',				
	);	
	$form['formspptgl']['spptgl_title'] = array(
		'#markup' => '<small>Tanggal SPP</small>',
	);
	$form['formspptgl']['spptgl']= array(
		//'#title' =>  t('Tgl. SPP'),
		'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
		'#default_value' => $spptgl, 
		//'#prefix' => '<div class="col-md-6">',
		//'#suffix' => '</div>',				
			 
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

	//SPD
	if ($kodeuk=='00') {
		$query = db_select('spd', 's');
		# get the desired fields from the database
		$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'))
				->condition('s.kodeuk', $kodeuk, '=')
				->orderBy('spdkode', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		
	} else {
		$query = db_select('spd', 's');
		# get the desired fields from the database
		$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'))
				->condition('s.jenis', 2, '=')
				->condition('s.kodeuk', $kodeuk, '=')
				->orderBy('spdkode', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
	}
	if($results){
		foreach($results as $data) {
			if ($data->spdno=='')
				$spdnox = ', Nomor : ....., Tgl : .....';
			else
				$spdnox = ', Nomor : ' . $data->spdno . ', Tgl : ' . $data->spdtgl;
			$option_spd[$data->spdkode] = 'Tri Wulan #' . $data->tw . ', sejumlah ' . apbd_fn($data->jumlah) . $spdnox; 
		}
	}		
	$form['spdno'] = array(
		'#type' => 'select',
		'#title' =>  t('SPD'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',				
		'#options' => $option_spd,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $spdno,
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',				
		'#default_value' => $keperluan,
	);

	$form['jumlah'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Jumlah'),
		'#attributes'	=> array('style' => 'text-align: right'),
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',				
		'#default_value' => $jumlah,
	);

	//'pimpinannama', 'penerimanama', 'bendaharanip', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp'
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'BENDAHARA',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',						
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',						
			'#default_value'=> $penerimanama, 
		);				
		$form['formpenerima']['penerimanip']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NIP'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',						
			'#default_value'=> $penerimanip, 
		);				
		$form['formpenerima']['penerimabankrekening']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Rekening'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',						
			'#default_value'=> $penerimabankrekening, 
		);				
		$form['formpenerima']['penerimabanknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Bank'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',						
			'#default_value'=> $penerimabanknama, 
		);				
		$form['formpenerima']['penerimanpwp']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NPWP'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',						
			'#default_value'=> $penerimanpwp, 
		);				
	

	//KELENGKAPAN
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KELENGKAPAN DOKUMEN',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',						
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'ada', 'tidakada'));
	$query->fields('lt', array('uraian'));
	$query->condition('dk.dokid', $dokid, '=');
	$query->orderBy('lt.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++; 
		$kode = $data->kodekelengkapan;
		$uraian = $data->uraian;
		$ada = $data->ada;
		$tidakada = $data->tidakada;
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
			'#default_value'=> $ada, 
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		
		$form['formkelengkapan']['tablekelengkapan']['tidakadakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $tidakada, 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		

	}
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);	



	if ($sppok=='1') {
		//cetak esigner
		$form['formedoc']= array(
			'#prefix' => '<div align="center">',
			'#suffix' => '</div>',
		);	

			$edoc = 'E_SPPK_' . $dokid . '.PDF';
			if (is_eDocExistsBaru($edoc)) {
				$form['formedoc']['submitsppspppk']= array(
					'#type' => 'submit', 
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Pernyataan PPK',
					'#attributes' => array('class' => array('btn btn-success btn-sm')),
				);
				
			} else {	
				$form['formedoc']['submitsppspppk']= array(
					'#type' => 'submit', 
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Pernyataan PPK',
					'#attributes' => array('class' => array('btn btn-warning btn-sm')),
				);
			}
					
			$edoc = 'E_SPPY_' . $dokid . '.PDF';
			if (is_eDocExistsBaru($edoc)) {
				$form['formedoc']['submitsppsp']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Pernyataan',
					'#attributes' => array('class' => array('btn btn-success btn-sm')),
				);
				
			} else {	
				$form['formedoc']['submitsppsp']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Pernyataan',
					'#attributes' => array('class' => array('btn btn-warning btn-sm')),
				);
			}
			$edoc = 'E_SPKT_' . $dokid . '.PDF';
			if (is_eDocExistsBaru($edoc)) {		
				$form['formedoc']['submitsppket']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Keterangan',
					'#attributes' => array('class' => array('btn btn-success btn-sm')),
				);
				
			} else {	
				$form['formedoc']['submitsppket']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Keterangan',
					'#attributes' => array('class' => array('btn btn-warning btn-sm')),
				);
			}	
	}
	

	//CETAK BAWAH	
	$form['formdata']= array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
	);	
		$form['formdata']['submitsppa21']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> A2-1',
			'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
		);
		$form['formdata']['submitspp3']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 3',
			'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
		);
		$form['formdata']['submitspp2']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 2',
			'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
		);
		$form['formdata']['submitspp1']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 1',
			'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
		);
		$form['formdata']['submitsppkelengkapan']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Kelengkapan',
			'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
		);
	
	//FORM SIMPAN ENABLE
	$disable_simpan = TRUE;
	if ($sppok=='0') {
		if (isUserVerifikatorSKPD()) {
			$form['formdata']['submitspm']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
				'#attributes' => array('class' => array('btn btn-info btn-sm')),
			);
			$disable_simpan = TRUE;
		} else {
			$disable_simpan = FALSE;
		}
		
	} elseif (($sppok=='1') and ($sp2dok=='0') and ($spmok == '0')) {	
		$form['formdata']['submitnotspm']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Batalkan',
			'#attributes' => array('class' => array('btn btn-danger btn-sm')),
		);
	}
	
	//$ref = "javascript:history.go(-1)";
	//FORM SUBMIT DECLARATION
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function spptu_edit_main_form_validate($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitspm']) {

		//CEK NOMOR SPP
		$sppno = $form_state['values']['sppno'];
		if ($sppno == '') {
			form_set_error('sppno', 'Nomor SPP belum diisikan');
		}			

		//CEK KEPERLUAN
		$keperluan = $form_state['values']['keperluan'];
		if ($keperluan == '') {
			form_set_error('keperluan', 'Keperluan SPP belum diisikan');
		}		
		
		/*
		//CEK Jumlah
		$totaljumlah = 0;
		$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
		for ($n=1; $n <= $jumlahrekrekening; $n++) {
			$jumlah = $form_state['values']['jumlahapbd' . $n];			
			$totaljumlah = $totaljumlah + $jumlah;
			
		}		
		if ($totaljumlah == 0) {
			form_set_error('jumlahapbd1', 'Jumlah pengajuan belum diisikan');
		}
		*/
	}	
}

function spptu_edit_main_form_submit($form, &$form_state) {

$dokid = $form_state['values']['dokid'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp1']) {
	drupal_goto('spptu/edit/' . $dokid . '/spp1');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp2']) {
	drupal_goto('spptu/edit/' . $dokid . '/spp2');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp3']) {
	drupal_goto('spptu/edit/' . $dokid . '/spp3');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppspppk']) {
	drupal_goto('spptu/edit/' . $dokid . '/sppk');
 
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppkelengkapan']) {
	drupal_goto('spptu/edit/' . $dokid . '/sppkelengkapan');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppsp']) {
	drupal_goto('spptu/edit/' . $dokid . '/sp');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppket']) {
	drupal_goto('spptu/edit/' . $dokid . '/ket');
	
//submitsppa21
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppa21']) {
	//drupal_goto('spptu/edit/' . $dokid . '/a21');
	drupal_goto('kuitansi/edit/' . $dokid);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambar']) {
	drupal_goto('upload/edit/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambarspj']) {
	drupal_goto('upload/editspj/' . $dokid);

} else {
	
	//submitsppsp
	
	$kodeuk = $form_state['values']['kodeuk'];
	
	$sppno = $form_state['values']['sppno'];
	//$spptgl = $form_state['values']['spptgl'];
	//$spptglsql = $spptgl['year'] . '-' . $spptgl['month'] . '-' . $spptgl['day'];
	$spptglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spptgl']);

	$keperluan = $form_state['values']['keperluan'];
	$jumlah = $form_state['values']['jumlah'];
	$spdno = $form_state['values']['spdno'];
	
	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanip = $form_state['values']['penerimanip'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];

	
	//BEGIN TRANSACTION
	//$transaction = db_transaction();	
	 
	//try {
		//KELENGKAPAN
		for ($n=1; $n <= $jumlahrekkelengkapan; $n++) {
			$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
			$ada = $form_state['values']['adakelengkapan' . $n];
			$tidakada = $form_state['values']['tidakadakelengkapan' . $n];
			
			$query = db_update('dokumenkelengkapan')
			->fields( 
					array(
						'ada' => $ada,
						'tidakada' => $tidakada,
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodekelengkapan', $kodekelengkapan, '=');
			$res = $query->execute();
			
		}
		

		//DOKUMEN
		//if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp3']) {
		if ($form_state['clicked_button']['#value'] == $form_state['values']['submitspm']) {
			//drupal_set_message('x');
			$query = db_update('dokumen')
						->fields( 
							array(
								'keperluan' => $keperluan,
								'spdno' => $spdno,
								'sppno' => $sppno,
								'spptgl' =>$spptglsql,
								'jumlah' => $jumlah,
								'netto' => $jumlah,
								'penerimanama' => $penerimanama,
								'penerimanip' => $penerimanip,
								'penerimabanknama' => $penerimabanknama,
								'penerimabankrekening' => $penerimabankrekening,
								'penerimanpwp' => $penerimanpwp,
								'sppok' => 1,
								
							)
						);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();

		} elseif (isset($form_state['values']['submitnotspm'])){
			if($form_state['clicked_button']['#value'] == $form_state['values']['submitnotspm']) {
				//drupal_set_message('x');
				$query = db_update('dokumen')
							->fields( 
								array(
									'sppok' => 0,
									
								)
							);
				$query->condition('dokid', $dokid, '=');
				$res = $query->execute();			
			}
		} else {	
			/*
			if (isAdministrator()) {
				drupal_set_message($totaljumlah);
				drupal_set_message($totalpotongan);
				//drupal_set_message();
			}
			*/
			$query = db_update('dokumen')
					->fields( 
							array(
								'keperluan' => $keperluan,
								'spdno' => $spdno,
								'sppno' => $sppno,
								'spptgl' =>$spptglsql,
								'jumlah' => $jumlah,
								'netto' => $jumlah,
								'penerimanama' => $penerimanama,
								'penerimanip' => $penerimanip,
								'penerimabanknama' => $penerimabanknama,
								'penerimabankrekening' => $penerimabankrekening,
								'penerimanpwp' => $penerimanpwp,
									
							)
					);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		}	
	
	/*
	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('spptu-' . $nourut, $e);
	}
	*/
	
	//drupal_goto('');
}
}


function printspp_1($dokid){
	//$periose=1;
	//READ UP DATA
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('urusan', 'u', 'uk.kodeu=u.kodeu');
	
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'penerimanip'));
	$query->fields('uk', array('bendaharanama','bendaharanip','kodeuk', 'kodedinas', 'namauk', 'header1'));
	$query->fields('u', array('kodeu', 'urusan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		$kodeuk = $data->kodeuk;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$namauk = $data->namauk;
		$unitkerja = $data->kodedinas . '01 - SEKRETARIAT / TATA USAHA'; 
		$alamat = $data->header1;
		$bulan = apbd_getbulan($data->bulan);
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		//$program = $data->kodeu . '.' . $data->kodepro . ' - ' . $data->program;
		//$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg,-3) . ' - ' . $data->kegiatan;
		
		$jumlah = apbd_fn($data->jumlah);
		$keperluan = $data->keperluan;
		$bendaharanama = $data->bendaharanama;
		$penerimanama=$data->penerimanama;
		$rekening = $data->penerimabanknama . ' No. Rek . ' . $data->penerimabankrekening;
		$bendaharanip = $data->bendaharanip;
	}	
	
	//dpa
	$dpa = '...................., .................';
	$query = db_select("dpanomor", "d");
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->fields('d', array('blno', 'bltgl'));
	$query->fields('uk', array('kodedinas'));
	$query->condition('d.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$dpa = $data->blno . '/BL/' . $data->kodedinas . '/' . apbd_tahun() . ', ' . $data->bltgl;
	}
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Asli', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 2', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 3', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERMINTAAN PEMBAYARAN (SPP)', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'SPP-1', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TAMBAHAN UANG PERSEDIAAN [SPP-TU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'SKPD', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $skpd, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Unit Kerja', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $unitkerja, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'No. DPA SKPD Tanggal', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpa, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Tahun Anggaran', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Bulan', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $bulan, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '7.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Urusan Pemerintah', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $urusan, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-top:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'Kepada Yth.','width' => '260px', 'align'=>'left','style'=>'border:none;'),
							
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'Pengguna Anggaran','width' => '260px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => $namauk, 'width' => '260px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'di - ','width' => '260px', 'align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
		array('data' => '','width' => '280px', 'align'=>'center','style'=>'border:none;'),
		array('data' => 'Jepara','width' => '230px', 'align'=>'left','style'=>'border:none;'),
	);

	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'Dengan memperhatikan Peraturan Bupati Jepara ' . apbd_perda() . ', bersama ini kami mengajukan Surat Permintaan Pembayaran sebagai berikut :', 'width' => '510px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	
	$rows[]=array(
		array('data' => 'a.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Jumlah Pembayaran yang diminta', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Rp ' . $jumlah . ',00', 'width' => '310px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'b.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Untuk Keperluan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '310px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'c.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Nama Bendahara', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '310px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'd.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Alamat', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '310px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'e.', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'No. Rekening Bank', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $rekening, 'width' => '310px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;font-size:100%;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Bendahara Pengeluaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '510px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function printspp_2($dokid) {
	
	//READ UP DATA
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'penerimanip', 'spdno'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'header1','bendaharanama'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$spptglvalue = $data->spptgl;
		
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$kodeuk = $data->kodeuk;
		$namauk = $data->namauk;
		$pimpinannama = $data->pimpinannama;
		
		$alamat = $data->header1;
		
		$jumlah = $data->jumlah;
		$keperluan = $data->keperluan;

		$bendaharanama = $data->bendaharanama;
		$rekening = $data->penerimabanknama . ' No. Rek . ' . $data->penerimabankrekening;
		$bendaharanip = $data->penerimanip;
		
		$spdkode = $data->spdno;
		
	}	

	//DPAx
	if ($kodeuk == '00') {
		$query = db_select("dpanomor", "d");
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->fields('d', array('btlno', 'btltgl'));
		$query->fields('uk', array('kodedinas'));
		$query->condition('d.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$dpano = $data->btlno . '/BTL/' . $data->kodedinas . '/' . apbd_tahun();
			$dpatgl = $data->btltgl;
		}
		
	} else {
		$query = db_select("dpanomor", "d");
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->fields('d', array('blno', 'bltgl'));
		$query->fields('uk', array('kodedinas'));
		$query->condition('d.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$dpano = $data->blno . '/BL/' . $data->kodedinas . '/' . apbd_tahun();
			$dpatgl = $data->bltgl;
		}
	}
	
	//Anggaran
	$query = db_select("kegiatanskpd","k");
	$query->addExpression('SUM(k.anggaran)', 'totalanggaran');
	$query->condition('k.kodeuk', $kodeuk, '=');		
	if ($kodeuk != '00') $query->condition('k.jenis', '2', '=');
	$query->condition('k.inaktif', '0', '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$anggaran = $data->totalanggaran;
	}
	
	
	//SPD, ANGGARAN DAN TRIWULAN
	$twkumulatif = 0;
	$twaktif = 0;
	if ($spdkode=='') $spdkode = 'ZZZZZ';
	
	//drupal_set_message('x: '. $spdkode);
	
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'));
	if ($kodeuk != '00') $query->condition('s.jenis', 2, '=');	
	$query->condition('s.kodeuk', $kodeuk, '=');	
	$query->orderBy('s.tw');
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		if ($data->spdkode<= $spdkode) $twkumulatif +=  $data->jumlah;
		if ($data->spdkode== $spdkode) {
			$spdno = $data->spdno;
			$spdtgl = $data->spdtgl;
			$twaktif =  $data->jumlah;
		}		
		
		//drupal_set_message($data->spdkode);
		switch ($data->tw) {
			case '1':
				$spdno1 = $data->spdno;
				$spdtgl1 = $data->spdtgl;
				$spdjumlah1 = $data->jumlah;
				break;
			case '2':
				$spdno2 = $data->spdno;
				$spdtgl2 = $data->spdtgl;
				$spdjumlah2 = $data->jumlah;		
				break;
			case '3':
				$spdno3 = $data->spdno;
				$spdtgl3 = $data->spdtgl;
				$spdjumlah3 = $data->jumlah;
				break;
			case '4':
				$spdno4 = $data->spdno;
				$spdtgl4 = $data->spdtgl;
				$spdjumlah4 = $data->jumlah;
				break;
		}
	}
	if ($twkumulatif>$anggaran) $twkumulatif = $anggaran;
	if ($spdno=='') {
		$spdno = '................';
		$spdtgl = '................';		
	}
	$spd = 'SPD Nomor : ' . $spdno . ', tanggal : ' . $spdtgl;
	
	//sudah cair
	$sudahcair = 0;
	$query = db_select('dokumen', 'd');
	//$query->fields('d', array('jumlah'));
	$query->addExpression('SUM(jumlah)', 'sudahcair');
	$query->condition('d.spmok', '1', '=');	
	$query->condition('d.spmtgl', $spptglvalue, '<=');
	$query->condition('d.dokid', $dokid, '!=');		
	//$query->condition('d.sp2dok', '1', '=');		
	$query->condition('d.sp2dsudah', '1', '=');	
	$query->condition('d.kodeuk', $kodeuk, '=');	
	//$query->condition('d.sp2dtgl', $spptglvalue, '<');		
	
	/*
	$or = db_or();
	$or->condition('d.jenisdokumen', '0', '=');		//UP
	$or->condition('d.jenisdokumen', '1', '=');		//GU
	$or->condition('d.jenisdokumen', '2', '=');		//TU
	$or->condition('d.jenisdokumen', '4', '=');		//LS
	$query->condition($or);
	*/
	
	$query->condition('d.jenisdokumen', '3', '<>');	

	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$sudahcair = $data->sudahcair;
	}		
	
	//pengembalian
	$pengembalian = 0;
	
	$totalkeluar = $sudahcair - $pengembalian + $jumlah;
	$saldoakhir = $twkumulatif - $totalkeluar;
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Asli', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 2', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 3', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERMINTAAN PEMBAYARAN (SPP)', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'SPP-2', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TAMBAHAN UANG PERSEDIAAN [SPP-TU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	
	if ($kodeuk=='00') {
		$rows[]=array(
			array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'Jenis Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
			array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
			
		);
		
	} else {
		$rows[]=array(
			array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'Jenis Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
			array('data' => 'BELANJA LANGSUNG', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
			
		);
	}
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nomor dan Nama Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tambahan Uang Persediaan', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'SKPD', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $skpd, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat SKPD', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Pimpinan SKPD', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $pimpinannama, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nama dan No. Rekening Bank', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $rekening, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '7.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Untuk Pekerjaan / Keperluan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '8.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Dasar Pengeluaran', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $spd, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		
	);	
	
	$rows[]=array(
		array('data' => 'NO.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;font-size:95%;'),
		array('data' => 'URAIAN', 'width' => '245px','align'=>'center','style'=>'border:1px solid black;font-size:95%;'),
		array('data' => 'JUMLAH MATA UANG BERSANGKUTAN', 'width' => '240px','align'=>'center','style'=>'border:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => 'I', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'DPA-SKPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'Nomor', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => $dpano, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'Tanggal', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => $dpatgl, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => 'II', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'SPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => '1.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'Tgl. ' . $spdtgl1, 'width' => '110px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'No. ' . $spdno1, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($spdjumlah1), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => '2.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'Tgl. ' . $spdtgl2, 'width' => '110px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'No. ' . $spdno2, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($spdjumlah2), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => '3.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'Tgl. ' . $spdtgl3, 'width' => '110px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'No. ' . $spdno3, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($spdjumlah3), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => '4.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'Tgl. ' . $spdtgl4, 'width' => '110px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'No. ' . $spdno4, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($spdjumlah4), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($twaktif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($twkumulatif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => 'III', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'SP2D', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'a.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'SP2D total sebelumnya', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($sudahcair), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'b.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'Pengembalian', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($pengembalian), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:95%;'),
		array('data' => 'c.', 'width' => '15px','align'=>'left','style'=>'border:none;font-size:95%;'),
		array('data' => 'SP2D TU diminta', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($jumlah), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($totalkeluar), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
		array('data' => apbd_fn($saldoakhir), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:95%;'),
	);
	  
	$rows[] = array(
		array('data' => 'Pada SPP ini ditetapkan lampiran-lampiran yang diperlukan sebagaimana tertera pada daftar kelengkapan dokumen SPP-1','width' => '510px', 'align'=>'center','style'=>'border:1px solid black;font-size:95%;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);

	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
			);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Bendahara Pengeluaran','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function printspp_3($dokid){
	
	//READ UP DATA
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'kodekeg', 'jumlah',  'penerimanama', 'penerimanip'));
	$query->fields('uk', array('kodeuk', 'kodedinas','bendaharanama','bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);

		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;
		
		$koderekening = $data->kodedinas . '.000.000.52000000';		
		
	}		
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();

	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Asli', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 2', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 3', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERMINTAAN PEMBAYARAN (SPP)', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'SPP-3', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TAMBAHAN UANG PERSEDIAAN [SPP-TU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'RINCIAN RENCANA ANGGARAN', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:100%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
		
	$rows[] = array (
		array('data' => 'No','width' => '25px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '130px','align'=>'center','style'=>$styleheader),
		array('data' => 'Uraian', 'width' => '255px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '100px','align'=>'center','style'=>$styleheader),
		
		
	);
			

	$rows[] = array(
		array('data' => '1','width' => '25px', 'align'=>'center','style'=>'border-left:1px solid black;'.$style),
		array('data' => $koderekening, 'width' => '130px','align'=>'center','style'=>$style),
		array('data' => 'Tambahan Uang Persediaan', 'width' => '255px', 'align'=>'left','style'=>$style),
		array('data' => apbd_fn($data->jumlah), 'width' => '100px','align'=>'right','style'=>$style),
	);
	
	$rows[] = array(
					array('data' => 'Jumlah','width' => '410px', 'align'=>'right','style'=>$styleheader),
					array('data' => $jumlah, 'width' => '100px','align'=>'right','style'=>$styleheader),
	);
	$rows[] = array(
					array('data' => 'Terbilang: ' . $terbilang,'width' => '510px', 'align'=>'left','style'=>'border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
					
	);

	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Bendahara Pengeluaran','width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $bendaharanama,'width' => '210px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '210px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
					
	);
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function printspp_kelengkapan($dokid){
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
	}
		
	$header=array();

	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Asli', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 2', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 3', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	
	$rows = null;
	$rows[]=array(
		array('data' => 'PENELITIAN KELENGKAPAN DOKUMEN SPP', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TAMBAHAN UANG PERSEDIAAN [SPP-TU]', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);

	//item kelengkapan
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'ada', 'tidakada'));
	$query->fields('lt', array('uraian'));
	$query->condition('dk.dokid', $dokid, '=');
	$query->orderBy('lt.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {
		if ($data->ada )
			$x = 'x';
		else
			$x = '';
		$rows[]=array(
			array('data' => '<div style="width:5px;height:5px;background:red">' . $x . '</div>', 'width' => '25px','align'=>'center','style'=>'border:0.1px solid black;'),
			array('data' => '', 'width' => '5px','align'=>'left','style'=>'border:none'),
			array('data' => $data->uraian, 'width' => '480px','align'=>'left','style'=>'border:none;'),
		);
	}	

	$rows[]=array(
		array('data' => 'PENELITI KELENGKAPAN DOKUMEN SPP', 'width' => '510px','align'=>'left','style'=>'border:none;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:0.5px dashed black;'),
	);
	$rows[]=array(
		array('data' => 'Nama', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:0.5px dashed black;'),
	);
	$rows[]=array(
		array('data' => 'NIP', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:0.5px dashed black;'),
	);
	$rows[]=array(
		array('data' => 'Tanda Tangan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'left','style'=>'border-bottom:0.5px dashed black;'),
	);
	$attributes=array('style'=>'cellspacing="10";');
	$output .= theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => $attributes));
	   
	return $output;
}

function printspp_pernyataan($dokid){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
	}
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '460px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	
	if (strlen($namauk)<=40)
		$fsize = '175';
	else
		$fsize = '150';
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
		array('data' => $namauk, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:' . $fsize . '%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:120%;'),
		array('data' => $header1, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-bottom-style:double;font-size:120%;'),
		array('data' => $header2, 'width' => '460px','align'=>'center','style'=>'border-bottom-style:double;font-size:120%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERNYATAAN PENGAJUAN SPP-TU', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Tambahan Uang Persediaan (SPP-TU) yang kami ajukan sebesar Rp ' . 
			$jumlah . ',00 (terbilang ' . $terbilang . ') untuk keperluan ' . $namauk . ' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa :', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Tambahan Uang Persediaan (TU) tersebut diatas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami laksanakan sesuai dengan DPA-SKPD.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Tambahan Uang Persediaan (TU) tersebut tidak akan digunakan untuk membiayai pengeluaran- pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan Tambahan Uang Persediaan (TU) SKPD kami', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Pengguna Anggaran/Kuasa Pengguna Anggaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	); 
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $pimpinannip, 'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

function printspp_pernyataan_ppk($dokid){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip','ppknama','ppknip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		$ppknama = $data->ppknama;
		$ppknip = $data->ppknip;
		
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
	}
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '460px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	
	if (strlen($namauk)<=40)
		$fsize = '175';
	else
		$fsize = '150';
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
		array('data' => $namauk, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:' . $fsize . '%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:120%;'),
		array('data' => $header1, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-bottom-style:double;font-size:120%;'),
		array('data' => $header2, 'width' => '460px','align'=>'center','style'=>'border-bottom-style:double;font-size:120%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERNYATAAN PENGAJUAN SPP-TU', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Tambahan Uang Persediaan (SPP-TU) yang kami ajukan sebesar Rp ' . 
			$jumlah . ',00 (terbilang ' . $terbilang . ') untuk keperluan ' . $namauk . ' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa :', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Tambahan Uang Persediaan (TU) tersebut diatas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami laksanakan sesuai dengan DPA-SKPD.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Tambahan Uang Persediaan (TU) tersebut tidak akan digunakan untuk membiayai pengeluaran- pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan Tambahan Uang Persediaan (TU) SKPD kami', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Pengguna Anggaran/Kuasa Pengguna Anggaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	); 
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;'),
					array('data' => $ppknama,'width' => '255px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $ppknip, 'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

function printspp_a21($dokid){
	
	//READ UP
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'jumlah', 'keperluan', 'penerimanama', 'penerimanip'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		$keperluan = $data->keperluan;
		
		$spptgl = apbd_fd_long($data->spptgl);
		
		$bendaharanama = $data->penerimanama;
		$bendaharanip = $data->penerimanip;
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
	}
	$kegiatan = 'Tambahan Uang Persediaan';
	
	$styleheader='border:0.5px dashed grey;';
	$style='border-right:0.5px dashed grey;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '300px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'A2-1', 'width' => '110px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'Nama Kegiatan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $kegiatan, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Rekening', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Tambahan Uang Persediaan', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Tahun anggaran', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TANDA BUKTI PENGELUARAN', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Sudah terima dari', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Uang sejumlah', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $terbilang, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Untuk', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => '<div style="vertical-align:middle;">TERBILANG Rp </div>', 'width' => '115px','rowspan'=>'3','align'=>'left','style'=>'border-top:2px solid black;border-bottom:2px solid black;font-size:150%;vertical-align: middle;'),
		array('data' => '#' . $jumlah, 'width' => '135px','rowspan'=>'3','align'=>'right','style'=>'border-top:2px solid black;border-bottom:2px solid black;font-size:175%;vertical-align: middle;'),
		array('data' => '', 'width' => '25px','rowspan'=>'3','align'=>'left','style'=>'border:none'),
		array('data' => 'Jepara, ' . $spptgl, 'width' => '225px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => 'Yang berhak menerima', 'width' => '260px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanda tangan', 'width' => '90px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '135px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '275px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nama', 'width' => '90px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '135px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '275px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Alamat', 'width' => '90px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '135px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[] = array(
					array('data' => 'Setuju dibayarkan,','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Pengguna Anggaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Bendahara Pengeluaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	/*
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	*/
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}

function printspp_keterangan($dokid){
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip', 'kodedinas'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
		
		$nomorkeg = $data->kodedinas . '.000.000';		
	}
	
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '460px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	
	if (strlen($namauk)<=40)
		$fsize = '175';
	else
		$fsize = '150';
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
		array('data' => $namauk, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:' . $fsize . '%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:120%;'),
		array('data' => $header1, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border-bottom-style:double;font-size:120%;'),
		array('data' => $header2, 'width' => '460px','align'=>'center','style'=>'border-bottom-style:double;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);

	$rows[]=array(
		array('data' => 'SURAT KETERANGAN PENGAJUAN SPP-TU', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Tambahan Uang Persediaan (SPP-TU) yang kami ajukan sebesar Rp ' . 
				$jumlah . ',00 (terbilang ' . $terbilang . ') untuk keperluan ' . $namauk . 
				' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa jumlah tersebut digunakan untuk keperluan sebagai berikut:', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Kode Rekening', 'width' => '125px','align'=>'left','style'=>'border:1px solid black;'),
		array('data' => 'Uraian', 'width' => '260px','align'=>'left','style'=>'border:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '100px','align'=>'right','style'=>'border:1px solid black;'),
	);
	

	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $nomorkeg . '.52000000', 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => 'Tambahan Uang Persediaan', 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($data->jumlah), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
	);

	$rows[]=array(
			array('data' => 'TOTAL', 'width' => '410px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
			array('data' => $jumlah, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan SPP-TU SKPD', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Pengguna Anggaran/Kuasa Pengguna Anggaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border:none;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}



?>
