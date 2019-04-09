<?php

function gubaruspp_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	//$aku= 'tampilkan123456789';
	//drupal_set_message (substr($aku,-9));
	$dokid = arg(2);	
	$print = arg(3);
	$_SESSION["kegiatan_dokid"] = $dokid;
	//drupal_set_message($dokid);
	if($print=='spp1') {			  

		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/spp1', '', $url);
	
		$output = printspp_1($dokid);
		apbd_ExportSPP1_LS($output, 'SPP_' . $dokid . '_SPP1.PDF', $url);
		//return $output;
		
	} else if ($print=='spp2') {	
		//$kodekeg = arg(4);	
		$output = printspp_2($dokid);
		apbd_ExportSPP($output, 'SPP_' . $dokid . $kodekeg . '_SPP2.PDF');
		//return $output;
	
	} else if ($print=='spp3') {	
		//$kodekeg = arg(4);
		$output = printspp_3($dokid);
		apbd_ExportSPP($output, 'SPP_' . $dokid . $kodekeg . '_SPP3.PDF');
	
	} else if ($print=='sppkelengkapan') {			  
		$output = printspp_kelengkapan($dokid);
		//print_pdf_p($output);
		apbd_ExportSPP($output, 'SPP_' . $dokid . '_Kelengkapan.PDF');
		
	} else if ($print=='sp') {			  
		//$output = printspp_pernyataan($dokid);
		//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Pernyataan.PDF');
	
		//$edoc = 'http://simkedajepara.web.id/edoc2019/spm/E_SPPY_' . $dokid . '.PDF';
		$edoc = 'E_SPPY_' . $dokid . '.PDF';
		if (is_eDocExistsBaru($edoc)) {
			drupal_goto('http://simkedajepara.web.id/edoc2019/spm/' . $edoc); 
			
		} else {	
			$output = printspp_pernyataan($dokid);
			//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Pernyataan.PDF');
			
			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url);
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'SPPY_' . $dokid . '.PDF');
			
			
			
			apbd_ExportSPP_Logo_e($output, $url, $fname);		
			drupal_goto('files/spm/' . $fname);
		}
		
	} else if ($print=='sppk') {			  
		
		$edoc = 'E_SPPK_' . $dokid . '.PDF';
		if (is_eDocExistsBaru($edoc)) {
			drupal_goto('http://simkedajepara.web.id/edoc2019/spm/' . $edoc); 
			
		} else {	
			//$output = printspp_pernyataan_ppk($dokid);
			$output = printspp_pernyataan_ppk_baru($dokid);
			//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_Pernyataan.PDF');
			
			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url);
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'SPPK_' . $dokid . '.PDF');
			
			
			
			apbd_ExportSPP_Logo_e_dok($output, $url, $fname);		
			drupal_goto('files/doc/' . $fname);
		}	
	} else if ($print=='sptjm') {			  
		$output = printspp_sptjm($dokid);
		//print_pdf_p2($output);
		apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_SPTJM.PDF');
	
	} else if ($print=='sptjb') {			  
		
		//print_pdf_p($output);
		
		$edoc = 'E_SPTJB_' . $dokid . '.PDF';
		if (is_eDocExistsBaru($edoc)) {
			drupal_goto('http://simkedajepara.web.id/edoc2019/spm/' . $edoc); 
			
		} else {	
	
			$output = printspp_pernyataan_tanggungjawab($dokid);
			//apbd_ExportSPP_Logo($output, 'SPP_' . $dokid . '_SPTJM.PDF');
			
			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url); 
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'SPTJB_' . $dokid . '.PDF');
			
			 
			
			apbd_ExportSPP_Logo_e($output, $url, $fname);		
			drupal_goto('files/spm/' . $fname);
			
		}	
	} else if ($print=='a21') {			  
		$output = printspp_a21($dokid);
		//print_pdf_p($output);
		apbd_ExportSPP_No_Footer($output, 'SPP_' . $dokid . '_A2-1.PDF');

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

	} else if ($print=='remove') {			  
		$kodekeg = arg(4);	
		$spjid = '0';
		
		//readsumber	
		$sumber = '';
		$results = db_query('select sumber,spjid from dokumenrekening where dokid=:dokid and kodekeg=:kodekeg limit 1', array(':dokid'=>$dokid, ':kodekeg'=>$kodekeg));	
		foreach ($results as $data) {
			$sumber = $data->sumber;
			$spjid = $data->spjid;
		}	
		
		//drupal_set_message('k . ' . $kodekeg);
		//drupal_set_message('s . ' . $sumber);
		
		//delete
		
		$num = db_delete('dokumenrekening')
			->condition('dokid', $dokid)
			->condition('kodekeg', $kodekeg)
			->execute();
		  
		//hitung total
		$total = 0; 
		$results = db_query('select sum(jumlah) jumlahnya from dokumenrekening where dokid=:dokid', array(':dokid'=>$dokid));	
		foreach ($results as $data) {
			$total = $data->jumlahnya;
		}	
		$query = db_update('dokumen')
				->fields( 
				array(
					'jumlah' => $total,	
					'netto'	 => $total,	
					
				)
			);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();
		
		
		//reset usulan
		if ($sumber=='usl') {
			//drupal_set_message('usulan');
			
			$query = db_update('spjgu')
					->fields( 
					array(
						'posting' => '0',	
						
					)
				);
			$query->condition('spjid', $spjid, '=');
			$res = $query->execute();
			
			
		} else {
			//drupal_set_message('kegiatan');
			
			
			$kodeuk = substr($dokid ,2,2 );
			
			db_set_active('bendahara');

			$query = db_update('bendahara' . $kodeuk) // Table name no longer needs {}
					->fields(array(
					  'sudahproses' => '0',
					  'dokid' => '',
					   )
					 );
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodekeg', $kodekeg, '=');
			$res = $query->execute();
			
			db_set_active();
			
		
		}		
		
		
		drupal_goto('gubaruspp/edit/' . $dokid);
		  
		
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('gubaruspp_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function kegiatan($dokid, $kodekeg, $kegiatan){
	//PENGEMBALIAN
	$header=array();
	
	$nokeg = 0;
	$rows[] =array(
		array('data' => '<b>NO</b>', 'width' => '5px','align'=>'left','style'=>''),
		array('data' => '<b>KODE</b>', 'width' => '20px','align'=>'left','style'=>''),
		array('data' => '<b>URAIAN</b>', 'width' => '300px','align'=>'left','style'=>''),
		array('data' => '<b>ANGGARAN</b>', 'width' => '70px', 'align'=>'left','style'=>''),
		array('data' => '<b>CAIR</b>', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '<b>BATAS</b>', 'width' => '60px','align'=>'left','style'=>''),
		array('data' => '<b>JUMLAH</b>', 'width' => '60px', 'align'=>'left','style'=>''),
		array('data' => '<b>A1</b>', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '<b>A2</b>', 'width' => '20px', 'align'=>'left','style'=>''),
	);
	$i = 0;
	$cair = 0;
	if(!isset($form_state['tampil'])){
	$form_state['tampil']= 0;
	}
	//drupal_set_message($dokid);
	$query = db_select('dokumenrekening', 'di');
	$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
	$query->join('dokumen', 'd', 'di.dokid=d.dokid');
	$query->rightJoin('anggperkeg', 'a', 'di.kodero=a.kodero and di.kodekeg=a.kodekeg');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('di', array('jumlah', 'kodekeg'));
	$query->fields('a', array('anggaran'));
	$query->fields('d', array('sppok','spmok'));
	$query->condition('di.dokid', $dokid, '=');
	$query->condition('di.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	
	$results = $query->execute();

	foreach ($results as $data) {
		$i++;
		$totalkeg += $data->jumlah;
		$sppok = $data->sppok;
		$spmok = $data->spmok;
		
		$cair = apbd_readrealisasikegiatan_rekening($dokid, $kodekeg, $data->kodero, $tanggalsql);
		$batas = $data->anggaran - $cair;

		$uraian =  l($data->uraian, 'laporan/realisasikegsp2d/filter/' . $kodekeg . '/' . $data->kodero . '/' . $tanggalsql, array('attributes' => array('class' => null)));
		
		$linkprint1 = apbd_link_print_small('/kuitansi/edit/' . $dokid . '/' . $kodekeg . $data->kodero, '');
		$linkprint2 = apbd_link_print_small('/kuitansi/edita2/' . $dokid . '/' . $data->kodero, '');

		
		$rows[]=array(
			array('data' => $i , 'width' => '5px','align'=>'left','style'=>''),
			array('data' => $data->kodero , 'width' => '20px','align'=>'left','style'=>''),
			array('data' => $uraian, 'width' => '300px','align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>', 'width' => '70px', 'align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($cair) . '</p>', 'width' => '20px', 'align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($batas) . '</p>', 'width' => '60px','align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($data->jumlah) . '</p>', 'width' => '60px', 'align'=>'left','style'=>''),
			array('data' => $linkprint1, 'width' => '20px', 'align'=>'left','style'=>''),
			array('data' => $linkprint2, 'width' => '20px', 'align'=>'left','style'=>''),
		);
	}
	$rows[]=array(
		array('data' => '' , 'width' => '5px','align'=>'left','style'=>''),
		array('data' => '' , 'width' => '20px','align'=>'left','style'=>''),
		array('data' => '<strong>TOTAL</strong>', 'width' => '300px','align'=>'left','style'=>''),
		array('data' => '', 'width' => '70px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '60px','align'=>'left','style'=>''),
		array('data' => '<p class="text-right"><strong>' . apbd_fn($totalkeg) . '</strong></p>', 'width' => '60px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '20px', 'align'=>'left','style'=>''),
	);
	$usulan =  'Usulan'; 
	$a21 =  'A2-1'; 
	$res_spj = db_query('select spjid from spjgu where dokid=:dokid and kodekeg=:kodekeg', array(':dokid'=>$dokid, ':kodekeg'=>$kodekeg));
	foreach ($res_spj as $dat_spj) {
		$usulan =  l('Usulan', 'gubaru/edit/' . $dat_spj->spjid, array('attributes' => array('class' => null)));
		$a21 =  l('A2-1', 'gukuitansi/edit/' . $dat_spj->spjid . '/ZZ', array('attributes' => array('class' => null)));
	}	
	//drupal_set_message();
	if ($sppok=='0' or $spmok=='3'){
		$delete_link = modal_delete($dokid . $kodekeg, 'Hapus', 'Apakah anda akan menghapus kegiatan <strong>"' . $kegiatan .'"</strong> dari usulan SPP?', array('data'=>'Ya','link'=>'gubaruspp/edit/' . $dokid. '/remove/' . $kodekeg));	
	}else{
		$delete_link = '<h5 class="glyphicon glyphicon-remove text-warning" aria-hidden="true"></h5>';	
	}
	$rows[]=array(
		array('data' => '' , 'width' => '5px','align'=>'left','style'=>''),
		array('data' => '' , 'width' => '20px','align'=>'left','style'=>''),
		array('data' => $usulan . "&nbsp;|&nbsp;" . $a21, 'width' => '300px','align'=>'left','style'=>''),
		array('data' => '', 'width' => '70px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '60px','align'=>'left','style'=>''),
		array('data' => '', 'width' => '60px', 'align'=>'left','style'=>''),
		array('data' => '', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => $delete_link, 'width' => '20px', 'align'=>'left','style'=>''),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}
function gubaruspp_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["guspplastpage"] = $referer;
	else
		$referer = $_SESSION["guspplastpage"];
	//drupal_set_message($referer);
	
	$kodeuk = '';
	$kodekeg = '';
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$tglawal = $spptgl; $tglakhir = $spptgl; 
	
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Ganti Uang ' . apbd_tahun();
	$jeniskegiatan = '1';
	$penerimanama = '';
	$penerimanip = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
	//$pptknama = '';
	//$pptknip = '';
	$jumlah = 0;
	$pajak = 0;
	$potongan = 0;
	$netto = 0;
	$adagambar = 0;
	
	$dokid = arg(2);
	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'kodeuk',  'sppno', 'spptgl','penerimanama', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 
			'sppok', 'spmok', 'sp2dok', 'spdno', 'tglawal', 'tglakhir', 'spjlink'));
	
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SPP GU ' . $data->keperluan ;
		
		$sppok = $data->sppok;
		$spmok = $data->spmok;
		$sp2dok = $data->sp2dok;
		
		$bulan = $data->bulan;
		
		$dokid = $data->dokid;
		$sppno = $data->sppno;
		//$spptgl = strtotime($data->spptgl);	
		$spptgl = dateapi_convert_timestamp_to_datetime($data->spptgl);
		
		$tglawal = dateapi_convert_timestamp_to_datetime($data->tglawal);
		$tglakhir = dateapi_convert_timestamp_to_datetime($data->tglakhir);
		
		$tanggalsql = $data->spptgl;
		
		$kodeuk = $data->kodeuk;

		$keperluan = $data->keperluan;
		
		
		$jumlah = $data->jumlah;
		$penerimanamadokumen = $data->penerimanama;
		
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
	if ($sppok=='0') {
		$form['formatas']['submitaddkeg']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Kegiatan',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
		$form['formatas']['submitaddusulan']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Usulan',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
	}	

	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_kegiatan',
			'wrapper' => 'kegiatan-wrapper',
		),	
	);	
	$form['penerimanamadokumen'] = array(
		'#type' => 'value',
		'#value' => $penerimanamadokumen,
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
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',		
		'#options' => $opt_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $bulan,
	);

	
	//SPD
	$query = db_select('spd', 's');
	# get the desired fields from the database
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'));
	if ($kodeuk != '00') $query->condition('s.jenis', 2, '=');
		$query->condition('s.kodeuk', $kodeuk, '=');
			$query->orderBy('spdkode', 'ASC');
	# execute the query
	$results = $query->execute();
	# build the table fields
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
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#prefix' => '<div id="skpd-replace">',
		//'#suffix' => '</div>',
		// When the form is rebuilt during ajax processing, the $selected variable
		// will now have the new value and so the options will change.
		'#options' => $option_spd,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $spdno,
	);
	
	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',		
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);

	$form['formperiode'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PERIODE',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',		
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
		$form['formperiode']['tgl1'] = array(
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',				
		);	
		$form['formperiode']['tgl1']['tglawal_title'] = array(
			'#markup' => '<small>Tanggal</small>',
		);
		$form['formperiode']['tgl1']['tglawal']= array(
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

		$form['formperiode']['tgl2'] = array(
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',				
		);		
		$form['formperiode']['tgl2']['tglakhir_title'] = array(
			'#markup' => '<small>Sampai Tanggal</small>',
		);
		$form['formperiode']['tgl2']['tglakhir']= array(
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
			

	//KEGIATAN
	$form['formkegiatan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KEGIATAN<em class="text-info pull-right">' . apbd_fn($jumlah) . '</em>',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',					
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
	}
	$nokeg = 0;
	//$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	
	$reskeg = db_query('select k.kodekeg,k.kegiatan,sum(dr.jumlah) jumlah from {kegiatanskpd} k inner join {dokumenrekening} dr on k.kodekeg=dr.kodekeg where dr.dokid=:dokid group by k.kodekeg,k.kegiatan order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		$form['formkegiatan']['kegiatan'. $nokeg]= array(
			'#type'         => 'checkbox', 
			'#title' =>  t($nokeg . '. ' . $datakeg->kegiatan) . ', sebesar <strong>' . apbd_fn($datakeg->jumlah) . '</strong>',
			'#default_value'=> 0 , 
			'#validated' => TRUE,
			'#ajax' => array(
				'event'=>'change',
				'callback' => '_ajax_kegiatan'. $nokeg,
				'wrapper' => 'kegiatan'.$nokeg.'-wrapper',
			),					
		);				
		
		// Wrapper for rekdetil dropdown list
		$form['formkegiatan']['wrapperkegiatan'. $nokeg] = array(
			'#prefix' => '<div id="kegiatan'.$nokeg.'-wrapper">',
			'#suffix' => '</div>',
		);

		if (isset($form_state['values']['kegiatan'.$nokeg])) {
			$kegiatan = $form_state['values']['kegiatan'.$nokeg];
		} else {
			$kegiatan = 0;
		}
		//drupal_set_message($nokeg);
		if ($kegiatan == '1') {
			
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => kegiatan($dokid, $datakeg->kodekeg, $datakeg->kegiatan),
			);		
		}	else {
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => '', //verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);	
		}		
	}

	
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
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',		
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
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
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table></div>',
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
			
			$edoc = 'E_SPTJB_' . $dokid . '.PDF';
			if (is_eDocExistsBaru($edoc)) {
				$form['formedoc']['submitsptjb']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPTJB',
					'#attributes' => array('class' => array('btn btn-success btn-sm')),
				);		
			} else {	
				$form['formedoc']['submitsptjb']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPTJB',
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
	
	//cetak bawah
	$form['formcetak_bawah']= array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
	);	
	$form['formcetak_bawah']['submitspp3']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 3',
		'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
	);
	$form['formcetak_bawah']['submitspp2']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 2',
		'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
	);
	$form['formcetak_bawah']['submitspp1']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 1',
		'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
	);
	$form['formcetak_bawah']['submitsppkelengkapan']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Kelengkapan',
		'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
	);

	//FORM SIMPAN ENABLE
	$disable_simpan = TRUE;
	//if ($sppok=='0' && $gambar>0) {
	if ($sppok=='0') {
		$form['formcetak_bawah']['submitspm']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
		);
		$disable_simpan = FALSE;
		
	} else {	
	
		
		if (($sppok=='1') and ($sp2dok=='0') and ($spmok == '0')) {
			$form['formcetak_bawah']['submitnotspm']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Batalkan',
				'#attributes' => array('class' => array('btn btn-danger btn-sm')),
			);
		}
		$form['formcetak_bawah']['submitinfo']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Info',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
		);
	}
	
	//$ref = "javascript:history.go(-1)";
	//FORM SUBMIT DECLARATION
	$form['formcetak_bawah']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);

	//drupal_set_message($sppok);
	
	return $form; 
}

function gubaruspp_edit_main_form_validate($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitspm']) {

		//CEK Penerima nama
		$penerimanama = $form_state['values']['penerimanamadokumen'];
		if ($penerimanama == '') {
			form_set_error('penerimanama', 'Maaf Nama Penerima Tidak Boleh Kosong');
		}	
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
		
	}	
} 
 
function gubaruspp_edit_main_form_submit($form, &$form_state) {

$dokid = $form_state['values']['dokid'];

if ($form_state['clicked_button']['#value'] == $form_state['values']['submitaddkeg']) {
	drupal_goto('gubaruspp/pilihkeg/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitaddusulan']) {
	drupal_goto('gubaru/select/' . $dokid);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp1']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/spp1');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsptjm']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sptjm');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp2']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/spp2');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp3']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/spp3');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppkelengkapan']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sppkelengkapan');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppsp']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sp');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppket']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/ket');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppspppk']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sppk');		
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsptjb']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sptjb');		
	
//submitsppa21
}  else if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambarspj']) {
	drupal_goto('upload/editspj/' . $dokid);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppa21']) {
	drupal_goto('kuitansi/edit/' . $dokid);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambar']) {
	drupal_goto('upload/edit/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambarspj']) {
	drupal_goto('upload/editspj/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan1']) {
	drupal_goto('laporan/surat_pernyataan1/' . $dokid); 

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan2']) {
	drupal_goto('laporan/surat_pernyataan2/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan3']) {
	drupal_goto('laporan/surat_pernyataan3/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan4']) {
	drupal_goto('laporan/surat_pernyataan4/' . $dokid);
  
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan5']) {
	drupal_goto('laporan/surat_pernyataan5/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitcetak']) {
	drupal_goto('gubaruspp/cetak/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambar']) {
	drupal_goto('upload/edit/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitinfo']) {
	drupal_goto('verifikasisppguinfo/bk5/' . $dokid);

} else {
	
	//submitsppsp
	
	$kodeuk = $form_state['values']['kodeuk'];
	
	$sppno = $form_state['values']['sppno'];
//	$spptgl = $form_state['values']['spptgl'];
	//$spptglsql = $spptgl['year'] . '-' . $spptgl['month'] . '-' . $spptgl['day'];
	$spptglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spptgl']);
	$bulan = substr($spptglsql, 5,2);

	$tglawal = dateapi_convert_timestamp_to_datetime($form_state['values']['tglawal']);
	$tglakhir = dateapi_convert_timestamp_to_datetime($form_state['values']['tglakhir']);
	
	$spdno = $form_state['values']['spdno'];

	$keperluan = $form_state['values']['keperluan'];
	
	
	//PENERIMA
	
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanip = $form_state['values']['penerimanip'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];
	
	
	//PPTK
	//$pptknama = '';	//$form_state['values']['pptknama'];
	//$pptknip = '';	//$form_state['values']['pptknip'];
	
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];

	
	//BEGIN TRANSACTION
	//$transaction = db_transaction();	
	 
	//try {
		/*
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
		*/
		
		//drupal_set_message('x1');

		//DOKUMEN
		if ($form_state['clicked_button']['#value'] == $form_state['values']['submitspm']) {
			//drupal_set_message('x');
			$query = db_update('dokumen')
						->fields( 
							array(
								'keperluan' => $keperluan,
								'bulan' => $bulan,
								'spdno' => $spdno,
								'sppno' => $sppno,
								'spptgl' =>$spptglsql,
								'tglawal' =>$tglawal,
								'tglakhir' =>$tglakhir,
								'penerimanama' => $penerimanama,
								'penerimabanknama' => $penerimabanknama,
								'penerimabankrekening' => $penerimabankrekening,
								'penerimanpwp' => $penerimanpwp,
								//'pptknama' => $pptknama,
								//'pptknip' => $pptknip,							
								//'sppok' => 1,
								
							)
						);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
			
			drupal_goto('verifikasisppgu/bk5/' . $dokid);

		} elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitnotspm']) {
			//drupal_set_message('x');
			
			$query = db_update('dokumen')
						->fields( 
							array(
								'sppok' => 0,
								
							)
						);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();			
			

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
								'bulan' => $bulan,
								'spdno' => $spdno,
								'sppno' => $sppno,
								'spptgl' =>$spptglsql,
								'tglawal' =>$tglawal,
								'tglakhir' =>$tglakhir,
								'penerimanama' => $penerimanama,
								'penerimabanknama' => $penerimabanknama,
								'penerimabankrekening' => $penerimabankrekening,
								'penerimanpwp' => $penerimanpwp,
								//'pptknama' => $pptknama,
								//'pptknip' => $pptknip,
									
							)
					);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		}	
	
	/*
	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('guspp-' . $nourut, $e);
	}
	*/
	
	//drupal_goto('');
}
}

function printspp_1($dokid){
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('urusan', 'u', 'uk.kodeu=u.kodeu');
	
	$query->fields('d', array('dokid', 'sppno', 'sppok', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimabanknama', 'penerimanpwp', 'penerimanip', 'tglawal', 'tglakhir'));
	$query->fields('uk', array('kodeuk', 'kodedinas', 'namauk', 'header1', 'bendaharanama','bendahararekening','bendaharabank', 'bendaharanip','bendaharaatasnama'));
	$query->fields('u', array('kodeu', 'urusan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$namauk = $data->namauk;
		$kodedinas = $data->kodedinas;		
		$kodeuk = $data->kodeuk; 
		
		$alamat = $data->header1;
		
		$bulan = apbd_fd_long($data->tglawal) . ' s/d. ' . apbd_fd_long($data->tglakhir);
		
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		
		$jumlah = apbd_fn($data->jumlah);
		$keperluan = $data->keperluan;
		//$penerimanama = $data->penerimanama;
		$penerimanama = $data->bendaharaatasnama;
		$rekening = $data->bendaharabank . ' No. Rek . ' . $data->bendahararekening;
		
		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;
		
	}	


	//DPA
	$results = db_query('select blno,bltgl from dpanomor where kodeuk=:kodeuk', array(':kodeuk'=>$kodeuk));
	foreach ($results as $data) {
		$dpa = $data->blno . '/BL/' . $kodedinas . '/' . apbd_tahun() . ', ' . $data->bltgl;
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
		array('data' => 'PEMBAYARAN GANTI UANG [SPP-GU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'OPD', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $skpd, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'No. DPA SKPD/Tanggal', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpa, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Tahun Anggaran', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Bulan', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $bulan, 'width' => '360px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
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
		array('data' => 'Penerima', 'width' => '170px','align'=>'left','style'=>'border:none;'),
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
					array('data' => '', 'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}


function printspp_2($dokid) {
	
	//READ UP DATA
	$periode = 1;
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'sppok', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'penerimanip', 'spdno', 'pptknama', 'pptknip', 'penerimaalamat', 'penerimapimpinan'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas','bendaharabank','bendahararekening','bendaharaatasnama', 'namauk', 'header1', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;
		$kodedinas  = $data->kodedinas;
		
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$spptglvalue = $data->spptgl;
		
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$namauk = $data->namauk;
		$pimpinannama = $data->pimpinannama;
		
		$alamat = $data->header1;
		
		$jumlah = $data->jumlah;		

		$keperluan = $data->keperluan;

		$bendaharanama = $data->bendaharanama;
		$rekening = $data->bendaharabank . ' No. Rek . ' . $data->bendahararekening;
		$bendaharanip = $data->bendaharanip;
		
		$penerimanama = $data->bendaharaatasnama;
		$penerimaalamat = $data->penerimaalamat;
		$penerimapimpinan = $data->penerimapimpinan;
		
		//$pptknama = $data->pptknama;
		//$pptknip = $data->pptknip;
		
		$spdkode = $data->spdno;
		
	}	
	
	$spd = 'SPD Nomor : ................, tanggal : ................';
	if ($spdkode=='') $spdkode = 'ZZZZZ';
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'));
	if ($kodeuk != '00')  $query->condition('s.jenis',2, '=');	
	$query->condition('s.spdkode', $spdkode, '=');	
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$spd = 'SPD Nomor : ' . $data->spdno . ', tanggal : ' . $data->spdtgl;
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
		array('data' => '', 'width' => '510','align'=>'left','style'=>'border:none;'),
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
		array('data' => 'SPP-2', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMBAYARAN GANTI UANG [SPP-GU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	
	$strbelanja = '';
	if ($kodeuk=='00') $strbelanja = 'TIDAK ';
	$rows[]=array(
		array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Jenis Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'BELANJA ' . $strbelanja . 'LANGSUNG', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Penerima', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Pimpinan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $pimpinannama, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nama dan No. Rekening Bank', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $rekening, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Untuk Pekerjaan / Keperluan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '7.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Dasar Pengeluaran', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $spd, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);	
	$rows[]=array(
		array('data' => '8.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Untuk Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);	
	

	$numkeg = 0;
	$res_keg = db_query('select k.kodekeg, k.kegiatan, sum(dr.jumlah) as subtotal from dokumenrekening dr inner join kegiatanskpd k on dr.kodekeg=k.kodekeg where dr.dokid=:dokid group by k.kodekeg, k.kegiatan order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($res_keg as $data_keg) {

		$rows[]=array(
			array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			
		);	
		$numkeg++;
		$rows[]=array(
			array('data' => $numkeg . '.', 'width' => '15px','align'=>'left','style'=>'border-left:1px solid black;'),
			array('data' => $data_keg->kegiatan, 'width' => '495px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
		
		$query = db_select('kegiatanskpd', 'k');
		$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4', 'periode'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('k.kodekeg', $data_keg->kodekeg, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$kegiatan = $kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg,-3) . ' - ' . $data->kegiatan;
			$anggaran = $data->anggaran;

			$kodekeg = $data->kodekeg;
			
			$spdjumlah1 = $data->tw1;
			$spdjumlah2 = $data->tw2;
			$spdjumlah3 = $data->tw3;
			$spdjumlah4 = $data->tw4;
			$periode = $data->periode;
		}		

		if ($kodeuk=='00') {
			$results = db_query('select sum(tw1) as tw1x,sum(tw2) as tw2x,sum(tw3) as tw3x,sum(tw4) as tw4x from {kegiatanskpd} where isppkd=1');
			foreach ($results as $data) {
				$spdjumlah1 = $data->tw1x;
				$spdjumlah2 = $data->tw2x;
				$spdjumlah3 = $data->tw3x;
				$spdjumlah4 = $data->tw4x;
			}

			$dpano = apbd_dpa_ppkd_belanja_nomor();
			$dpatgl = apbd_dpa_ppkd_tanggal();	
			
		} else {
			//DPA
			$periode_s = ($periode<=1? '': $periode-=1);
			$dpano = '................';
			$dpatgl = $dpano;
			$query = db_select('kegiatandpa' . $periode_s, 'd');
			$query->fields('d', array('dpano', 'dpatgl'));
			$query->condition('d.kodekeg', $kodekeg, '=');		
			# execute the query	
			$results = $query->execute();
			foreach ($results as $data) {
				$dpano = $data->dpano;
				$dpatgl = $data->dpatgl;
			}
		}	
		$arrspd = array($spdjumlah1, $spdjumlah2, $spdjumlah3, $spdjumlah4);
		
		
		//sebelumnuua
		$sudahcair = 0;
		$query = db_select('dokumen', 'd');
		//$query->fields('d', array('jumlah'));
		$query->addExpression('SUM(jumlah)', 'sudahcair');
		$query->condition('d.sp2dok', '1', '=');		
		//$query->condition('d.sp2dsudah', '1', '=');	
		$query->condition('d.kodeuk', $kodeuk, '=');	
		$query->condition('d.sp2dtgl', $spptglvalue, '<');		
		if ($kodeuk != '00') $query->condition('d.kodekeg', $kodekeg, '=');		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$sudahcair = $data->sudahcair;
		}	
		
		//SPD
		$twkumulatif = 0;
		$twaktif = 0;
		if ($spdkode=='') $spdkode = 'ZZZZZ';
		$query = db_select('spd', 's');
		$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'));
		if ($kodeuk != '00')  $query->condition('s.jenis',2, '=');	
		$query->condition('s.kodeuk', $kodeuk, '=');	
		# execute the query	
		$results = $query->execute();
		
		$i_spd = -1;
		
		foreach ($results as $data) {

			$i_spd++;
			if ($data->spdkode <= $spdkode) $twkumulatif +=  $arrspd[$i_spd];
			if ($data->spdkode== $spdkode) {
				$spdno = $data->spdno;
				$spdtgl = $data->spdtgl;
				$twaktif =  $arrspd[$i_spd];
			}
			

			switch ($data->tw) {
				case '1':
					$spdno1 = $data->spdno;
					$spdtgl1 = $data->spdtgl;
					//$spdjumlah1 = $data->jumlah;
					break;
				case '2':
					$spdno2 = $data->spdno;
					$spdtgl2 = $data->spdtgl;
					//$spdjumlah2 = $data->jumlah;		
					break;
				case '3':
					$spdno3 = $data->spdno;
					$spdtgl3 = $data->spdtgl;
					//$spdjumlah3 = $data->jumlah;
					break;
				case '4':
					$spdno4 = $data->spdno;
					$spdtgl4 = $data->spdtgl;
					//$spdjumlah4 = $data->jumlah;
					break;
			}
		}
		if ($kodeuk!='00') if ($twkumulatif>$anggaran) $twkumulatif = $anggaran;
		
	 
		//PENGEMBALIAN
		$pengembalian = apbd_readreturkegiatan($kodekeg, $spptgl);
		
		$totalkeluar = $sudahcair + $pengembalian + $data_keg->subtotal;
		$saldoakhir = $twkumulatif - $totalkeluar;


		
		$rows[]=array(
			array('data' => 'NO.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
			array('data' => 'URAIAN', 'width' => '245px','align'=>'center','style'=>'border:1px solid black;'),
			array('data' => 'JUMLAH MATA UANG BERSANGKUTAN', 'width' => '240px','align'=>'center','style'=>'border:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '1.', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'DPA-SKPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Nomor', 'width' => '40px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => $dpano, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Tanggal', 'width' => '40px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => $dpatgl, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => 'II', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'SPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'I', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => 'Tgl. ' . $spdtgl1, 'width' => '110px','align'=>'left','style'=>'border:none;'),
			array('data' => 'No. ' . $spdno1, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($spdjumlah1), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '2.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => 'Tgl. ' . $spdtgl2, 'width' => '110px','align'=>'left','style'=>'border:none;'),
			array('data' => 'No. ' . $spdno2, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($spdjumlah2), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '3.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => 'Tgl. ' . $spdtgl3, 'width' => '110px','align'=>'left','style'=>'border:none;'),
			array('data' => 'No. ' . $spdno3, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($spdjumlah3), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '4.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => 'Tgl. ' . $spdtgl4, 'width' => '110px','align'=>'left','style'=>'border:none;'),
			array('data' => 'No. ' . $spdno4, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($spdjumlah4), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($twaktif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($twkumulatif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => 'III', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'SP2D', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'a.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => 'SP2D total sebelumnya', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($sudahcair), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'b.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => 'Pengembalian', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($pengembalian), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'c.', 'width' => '15px','align'=>'left','style'=>'border-bottom:1px solid black;'),
			array('data' => 'SP2D GU diminta', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($data_keg->subtotal), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($totalkeluar), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($saldoakhir), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		);
	}	
	
	$rows[] = array(
		array('data' => 'Pada SPP ini ditetapkan lampiran-lampiran yang diperlukan sebagaimana tertera pada daftar kelengkapan dokumen SPP-1','width' => '510px', 'align'=>'center','style'=>'border:1px solid black;'),
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

function printspp_2_lama($dokid, $kodekeg) {
	
	//READ UP DATA
	$periode = 1;
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'sppok', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'penerimanip', 'spdno', 'pptknama', 'pptknip', 'penerimaalamat', 'penerimapimpinan'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'header1', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;
		$kodedinas  = $data->kodedinas;
		
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$spptglvalue = $data->spptgl;
		
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$namauk = $data->namauk;
		$pimpinannama = $data->pimpinannama;
		
		$alamat = $data->header1;
		
		$jumlah = $data->jumlah;		

		$keperluan = $data->keperluan;

		$bendaharanama = $data->bendaharanama;
		$rekening = $data->penerimabanknama . ' No. Rek . ' . $data->penerimabankrekening;
		$bendaharanip = $data->bendaharanip;
		
		$penerimanama = $data->penerimanama;
		$penerimaalamat = $data->penerimaalamat;
		$penerimapimpinan = $data->penerimapimpinan;
		
		//$pptknama = $data->pptknama;
		//$pptknip = $data->pptknip;
		
		$spdkode = $data->spdno;
		
	}	
	
	$query = db_select('kegiatanskpd', 'k');
	$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4', 'periode'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('k.kodekeg', $kodekeg, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$kegiatan = $kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg,-3) . ' - ' . $data->kegiatan;
		$anggaran = $data->anggaran;

		$kodekeg = $data->kodekeg;
		
		$spdjumlah1 = $data->tw1;
		$spdjumlah2 = $data->tw2;
		$spdjumlah3 = $data->tw3;
		$spdjumlah4 = $data->tw4;
		$periode = $data->periode;
	}		

	if ($kodeuk=='00') {
		$results = db_query('select sum(tw1) as tw1x,sum(tw2) as tw2x,sum(tw3) as tw3x,sum(tw4) as tw4x from {kegiatanskpd} where isppkd=1');
		foreach ($results as $data) {
			$spdjumlah1 = $data->tw1x;
			$spdjumlah2 = $data->tw2x;
			$spdjumlah3 = $data->tw3x;
			$spdjumlah4 = $data->tw4x;
		}

		$dpano = apbd_dpa_ppkd_belanja_nomor();
		$dpatgl = apbd_dpa_ppkd_tanggal();	
		
	} else {
		//DPA
		$periode_s = ($periode<=1? '': $periode-=1);
		$dpano = '................';
		$dpatgl = $dpano;
		$query = db_select('kegiatandpa' . $periode_s, 'd');
		$query->fields('d', array('dpano', 'dpatgl'));
		$query->condition('d.kodekeg', $kodekeg, '=');		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$dpano = $data->dpano;
			$dpatgl = $data->dpatgl;
		}
	}	
	$arrspd = array($spdjumlah1, $spdjumlah2, $spdjumlah3, $spdjumlah4);
	
	
	//sebelumnuua
	$sudahcair = 0;
	$query = db_select('dokumen', 'd');
	//$query->fields('d', array('jumlah'));
	$query->addExpression('SUM(jumlah)', 'sudahcair');
	$query->condition('d.sp2dok', '1', '=');		
	//$query->condition('d.sp2dsudah', '1', '=');	
	$query->condition('d.kodeuk', $kodeuk, '=');	
	$query->condition('d.sp2dtgl', $spptglvalue, '<');		
	if ($kodeuk != '00') $query->condition('d.kodekeg', $kodekeg, '=');		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$sudahcair = $data->sudahcair;
	}	
	
	//SPD
	$twkumulatif = 0;
	$twaktif = 0;
	if ($spdkode=='') $spdkode = 'ZZZZZ';
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'));
	if ($kodeuk != '00')  $query->condition('s.jenis',2, '=');	
	$query->condition('s.kodeuk', $kodeuk, '=');	
	# execute the query	
	$results = $query->execute();
	
	$i_spd = -1;
	
	foreach ($results as $data) {
		
		/*	
		if ($data->spdkode<= $spdkode) $twkumulatif +=  $data->jumlah;
		if ($data->spdkode== $spdkode) {
			$spdno = $data->spdno;
			$spdtgl = $data->spdtgl;
			$twaktif =  $data->jumlah;
		}
		*/
		
		//drupal_set_message($data->spdkode);
		
		$i_spd++;
		if ($data->spdkode <= $spdkode) $twkumulatif +=  $arrspd[$i_spd];
		if ($data->spdkode== $spdkode) {
			$spdno = $data->spdno;
			$spdtgl = $data->spdtgl;
			$twaktif =  $arrspd[$i_spd];
		}
		

		switch ($data->tw) {
			case '1':
				$spdno1 = $data->spdno;
				$spdtgl1 = $data->spdtgl;
				//$spdjumlah1 = $data->jumlah;
				break;
			case '2':
				$spdno2 = $data->spdno;
				$spdtgl2 = $data->spdtgl;
				//$spdjumlah2 = $data->jumlah;		
				break;
			case '3':
				$spdno3 = $data->spdno;
				$spdtgl3 = $data->spdtgl;
				//$spdjumlah3 = $data->jumlah;
				break;
			case '4':
				$spdno4 = $data->spdno;
				$spdtgl4 = $data->spdtgl;
				//$spdjumlah4 = $data->jumlah;
				break;
		}
	}
	if ($kodeuk!='00') if ($twkumulatif>$anggaran) $twkumulatif = $anggaran;

	if ($spdno=='') {
		$spdno = '................';
		$spdtgl = '................';		
	}
	$spd = 'SPD Nomor : ' . $spdno . ', tanggal : ' . $spdtgl;
	
 
	//PENGEMBALIAN
	$pengembalian = apbd_readreturkegiatan($kodekeg, $spptgl);
	
	$totalkeluar = $sudahcair + $pengembalian + $jumlah;
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
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'SPP-2', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMBAYARAN GANTI UANG [SPP-GU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	
	$strbelanja = '';
	if ($kodeuk=='00') $strbelanja = 'TIDAK ';
	$rows[]=array(
		array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Jenis Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'BELANJA ' . $strbelanja . 'LANGSUNG', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nomor dan Nama Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $kegiatan, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Penerima', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Pimpinan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
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
		array('data' => 'NO.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'URAIAN', 'width' => '245px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'JUMLAH MATA UANG BERSANGKUTAN', 'width' => '240px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'DPA-SKPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Nomor', 'width' => '40px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpano, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '40px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpatgl, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'II', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'SPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'I', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl1, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno1, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah1), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '2.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl2, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno2, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah2), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '3.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl3, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno3, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah3), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '4.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl4, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno4, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah4), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($twaktif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($twkumulatif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'III', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'SP2D', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'a.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'SP2D total sebelumnya', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($sudahcair), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'b.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Pengembalian', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($pengembalian), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'c.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'SP2D GU diminta', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($jumlah), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($totalkeluar), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($saldoakhir), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	
	$rows[] = array(
		array('data' => 'Pada SPP ini ditetapkan lampiran-lampiran yang diperlukan sebagaimana tertera pada daftar kelengkapan dokumen SPP-1','width' => '510px', 'align'=>'center','style'=>'border:1px solid black;'),
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
	
	$query->fields('d', array('dokid', 'sppok', 'sppno', 'spptgl', 'kodekeg', 'jumlah',  'penerimanama', 'penerimanip', 'pptknama', 'pptknip'));
	$query->fields('uk', array('kodeuk', 'kodedinas', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);

		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;

		$kodedinas = $data->kodedinas;	// . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);		
		
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
		array('data' => 'PEMBAYARAN GANTI UANG [SPP-GU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'RINCIAN RENCANA ANGGARAN', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:100%;font-weight:bold;'),
	);
	
	$numkeg = 0;
	$res_keg = db_query('select distinct k.kodekeg, k.kodepro, k.kegiatan from dokumenrekening dr inner join kegiatanskpd k on dr.kodekeg=k.kodekeg where dr.dokid=:dokid order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($res_keg as $data_keg) {
		$rows[]=array(
			array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		);

		$numkeg++;
		$rows[]=array(
			array('data' => $numkeg . '.', 'width' => '15px','align'=>'left','style'=>'border-left:1px solid black;'),
			array('data' => $data_keg->kegiatan, 'width' => '495px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
		
		$nomorkeg = $kodedinas . '.' . $data_keg->kodepro . '.' . substr($data_keg->kodekeg, -3);	

		$rows[] = array (
			array('data' => 'No','width' => '25px', 'align'=>'center','style'=>$styleheader),
			array('data' => 'Kode', 'width' => '125px','align'=>'center','style'=>$styleheader),
			array('data' => 'Uraian', 'width' => '270px', 'align'=>'center','style'=>$styleheader),
			array('data' => 'Jumlah', 'width' => '90px','align'=>'center','style'=>$styleheader),
			
			
		);
				
		# get the desired fields from the database
		$query = db_select('dokumenrekening', 'di');
		$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
		
		$query->fields('di', array('jumlah'));
		$query->fields('ro', array('kodero', 'uraian'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('di.dokid', $dokid, '=');
		$query->condition('di.kodekeg', $data_keg->kodekeg, '=');
		$query->condition('di.jumlah', 0, '>');
		$query->orderBy('ro.kodero', 'ASC');

		$results = $query->execute();
		$n = 0; $subtotal = 0;
		foreach ($results as $data) {
			$n++;	
			$subtotal += $data->jumlah;
			$rows[] = array(
				array('data' => $n,'width' => '25px', 'align'=>'center','style'=>'border-left:1px solid black;'.$style),
				array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '125px','align'=>'center','style'=>$style),
				array('data' => $data->uraian, 'width' => '270px', 'align'=>'left','style'=>$style),
				array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>$style),
			);
		}	
		$rows[]=array(
			array('data' => '', 'width' => '150px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'Sub Total', 'width' => '270px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($subtotal), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		);			
		
	}	
		

	$rows[] = array(
					array('data' => '','width' => '420px', 'align'=>'right','style'=>$styleheader),
					array('data' => '', 'width' => '90px','align'=>'right','style'=>$styleheader),
	);
	$rows[] = array(
					array('data' => 'TOTAL','width' => '420px', 'align'=>'right','style'=>$styleheader),
					array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>$styleheader),
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

function printspp_3_lama($dokid){
	
	//READ UP DATA
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'sppok', 'sppno', 'spptgl', 'kodekeg', 'jumlah',  'penerimanama', 'penerimanip', 'pptknama', 'pptknip'));
	$query->fields('uk', array('kodeuk', 'kodedinas', 'bendaharanama', 'bendaharanip'));
	$query->fields('k', array('kodekeg', 'kodepro'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);

		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;
		//$pptknama = $data->pptknama;
		//$pptknip = $data->pptknip;		
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);		
		
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
		array('data' => 'PEMBAYARAN GANTI UANG [SPP-GU]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
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
			
	# get the desired fields from the database
	$query = db_select('dokumenrekening', 'di');
	$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
	
	$query->fields('di', array('jumlah'));
	$query->fields('ro', array('kodero', 'uraian'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('di.dokid', $dokid, '=');
	$query->condition('di.jumlah', 0, '>');
	$query->orderBy('ro.kodero', 'ASC');

	$results = $query->execute();
	$n = 0;
	foreach ($results as $data) {
		$n++;	
		$rows[] = array(
			array('data' => $n,'width' => '25px', 'align'=>'center','style'=>'border-left:1px solid black;'.$style),
			array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '130px','align'=>'center','style'=>$style),
			array('data' => $data->uraian, 'width' => '255px', 'align'=>'left','style'=>$style),
			array('data' => apbd_fn($data->jumlah), 'width' => '100px','align'=>'right','style'=>$style),
		);
	}
	
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
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
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
		array('data' => 'PEMBAYARAN GANTI UANG [SPP-GU]', 'width' => '510px','align'=>'left','style'=>'border:none;'),
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

function printspp_pernyataan_tanggungjawab($dokid){

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl','penerimanama', 'jumlah', 'sppok'));
	$query->fields('uk', array('namauk', 'header1', 'header2','namasingkat', 'pimpinannama', 'pimpinannip', 'kodedinas'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		$bendaharanama = $data->penerimanama;
		
		$namauk = $data->namauk;
		$namasingkat = $data->namasingkat;
		$header1 = $data->header1;
		$header2 = $data->header2;
		
		$kodedinas = $data->kodedinas;
		
	}
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '460px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	
	if (strlen($namauk)<=30)
		$fsize = '175';
	else
		$fsize = '130';
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
		array('data' => 'SURAT PERNYATAAN TANGGUNG JAWAB BELANJA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'OPD', 'width' => '70px','align'=>'left','style'=>'border:none;'),
		array('data' => ' : '. $namasingkat, 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bend Pengel', 'width' => '70px','align'=>'left','style'=>'border:none;'),
		array('data' => ' : '. $bendaharanama, 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => ' ', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => 'Yang bertandatangan di bawah ini Pengguna Anggaran/Kuasa Pengguna Anggaran Pada '. $spptgl .' ('. $namauk .') Pemkab Jepara menyatakan bahwa saya bertanggung jawab penuh atas segala pengeluaran kepada yang berhak menerima pembayaran, dengan rincian sebagai berikut :', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	
	$numkeg = 0;
	$res_keg = db_query('select distinct k.kodekeg, k.kodepro, k.kegiatan from dokumenrekening dr inner join kegiatanskpd k on dr.kodekeg=k.kodekeg where dr.dokid=:dokid order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($res_keg as $data_keg) {
		$numkeg++;
		$rows[]=array(
			array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
		);
		$rows[]=array(
			array('data' => $numkeg . '.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => $data_keg->kegiatan, 'width' => '495px','align'=>'left','style'=>'border:none;'),
		);
		
		$nomorkeg = $kodedinas . '.' . $data_keg->kodepro . '.' . substr($data_keg->kodekeg, -3);	
		
		$rows[]=array(
		array('data' => 'No', 'width' => '40','align'=>'center','style'=>'border-top:0.5px solid black;border-left:0.5px solid black;border-right:0.5px solid black;border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
		array('data' => 'Kode', 'width' => '150px','align'=>'center','style'=>'border-top:0.5px solid black;border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
		array('data' => 'Uraian', 'width' => '200px','align'=>'center','style'=>'border-top:0.5px solid black;border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
		array('data' => 'Jumlah', 'width' => '130px','align'=>'center','style'=>'border-top:0.5px solid black;border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
	);
	
		$query = db_select('dokumenrekening', 'di');
		$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
		
		$query->fields('di', array('jumlah'));
		$query->fields('ro', array('kodero', 'uraian'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('di.dokid', $dokid, '=');
		$query->condition('di.jumlah', 0, '>');
		$query->condition('di.kodekeg', $data_keg->kodekeg, '=');
		$query->orderBy('ro.kodero', 'ASC');

		$results = $query->execute();
		$subtotal = 0;
		foreach ($results as $data) {
		$n++;	
		$subtotal += $data->jumlah;
		$rows[]=array(
			array('data' => $n , 'width' => '40','align'=>'center','style'=>'border-left:0.5px solid black;border-right:0.5px solid black;border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '140px','align'=>'left','style'=>'border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => $data->uraian, 'width' => '190px','align'=>'left','style'=>'border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => apbd_fn($data->jumlah) , 'width' => '120px','align'=>'right','style'=>'border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;;border-right:0.5px solid blackfont-size:28px;vertical-align: middle;'),
		);
		
	}
	$rows[]=array(
			array('data' => '' , 'width' => '40','align'=>'center','style'=>'font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'font-size:28px;vertical-align: middle;'),
			array('data' => '', 'width' => '140px','align'=>'left','style'=>'font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;font-size:28px;border-left:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => 'SUBTOTAL', 'width' => '190px','align'=>'left','style'=>'border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => apbd_fn($subtotal) , 'width' => '120px','align'=>'right','style'=>'border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;;border-right:0.5px solid blackfont-size:28px;vertical-align: middle;'),
		);
	}
	$rows[]=array(
			array('data' => '' , 'width' => '40','align'=>'center','style'=>'font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'font-size:28px;vertical-align: middle;'),
			array('data' => '', 'width' => '140px','align'=>'left','style'=>'font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;font-size:28px;border-left:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => 'TOTAL', 'width' => '190px','align'=>'left','style'=>'border-bottom:0.5px solid black;border-right:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => $jumlah , 'width' => '120px','align'=>'right','style'=>'border-bottom:0.5px solid black;font-size:28px;vertical-align: middle;'),
			array('data' => ' ', 'width' => '10px','align'=>'left','style'=>'border-bottom:0.5px solid black;;border-right:0.5px solid blackfont-size:28px;vertical-align: middle;'),
		);
	
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bukti-Bukti belanja diatas disimpan sesuai ketentuan yang berlaku pada satuan kerja perangkat daerah, untuk kelengkapan administrasi dan keperluan pemeriksaan aparat pengawasan fungsional.', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat pernyataan ini saya buat dengan sebenarnya.', 'width' => '510px','align'=>'left','style'=>'border:none;'),
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
					array('data' => 'Jepara, '. $spptgl ,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
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


function printspp_pernyataan_ppk_baru($dokid){

$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip', 'ppknama', 'ppknip'));
	
	//$query->fields('u', array('namasingkat')); 
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$ppknama = $data->ppknama;		//$data->pimpinannama;
		$ppknip = $data->ppknip;		//$data->pimpinannip;
		
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
		array('data' => 'SURAT PERNYATAAN PEJABAT PENATAUSAHAAN KEUANGAN', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);	
	$rows[]=array(
		array('data' => ' &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; Yang bertanda tangan di bawah ini pejabat Penatausahaan Keuangan (PPK) ' . $namauk . ' menyatakan bahwa telah memverifikasi pengajuan pembayaran [SPP - GU] sebesar Rp ' . 
			$jumlah . ',00 terbilang(' . $terbilang . ') Tahun Anggaran ' . apbd_tahun() . ', sesuai perundang-undangan yang ada.', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	/*$rows[]=array(
		array('data' => '1.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Ganti Uang (GU) tersebut diatas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami laksanakan sesuai dengan DPA-SKPD.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Ganti Uang (GU) tersebut tidak akan digunakan untuk membiayai pengeluaran- pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	*/
	$rows[]=array(
		array('data' => ' &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; Demikian surat pernyataan ini dibuat dengan sebenarnya dan dapat digunakan sebagai persyaratan pengajuan SPM.', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
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
					array('data' => 'Pejabat Penatausahaan Kuangan','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
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
/*
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah'));
	$query->fields('uk', array('namauk','kodeuk', 'header1', 'header2', 'pimpinannama', 'pimpinannip','ppknama','ppknip'));
	
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
		
		$kodeuk = $data->kodeuk;
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
		$ppknama = $data->ppknama;
		$ppknip = $data->ppknip;
	}
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:150%;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA'. $kodeuk, 'width' => '460px','align'=>'center','style'=>'border:none;font-size:150%;'),
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
		array('data' => 'SURAT PERNYATAAN PENGAJUAN SPP-LS GAJI TUNJANGAN', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '   &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; Yang bertanda tangan di bawah ini Pejabat Penatausahaan Keuangan (PPK) BPKAD Kabupaten Jepara menyatakan bahwa telah memverifikasi Kegiatan Pengelolaan Kas dan Perbendaharaan Jepara sebesar ' . 
			$jumlah . ',00 ( ' . $terbilang . ' ) Tahun Anggaran '. apbd_tahun() .' sesuai perundang-undangan yang ada.', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Demikian surat Pernyataan ini dibuat dengan sebenarnya dan dapat digunakan sebagai persyaratan Pengajuan SPM', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;font-size:90px;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Pejabat Penatausahaan Keuangan','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
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
*/

function printspp_pernyataan_ppk($dokid){

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip', 'ppknama', 'ppknip'));
	
	//$query->fields('u', array('namasingkat')); 
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$ppknama = $data->ppknama;		//$data->pimpinannama;
		$ppknip = $data->ppknip;		//$data->pimpinannip;
		
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
		array('data' => 'SURAT PERNYATAAN PEJABAT PENATAUSAHAAN KEUANGAN', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);	
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Pembayaran Ganti Uang(GU) yang kami ajukan sebesar Rp ' . 
			$jumlah . ',00 (terbilang ' . $terbilang . ') untuk keperluan ' . $namauk . ' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa :', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Ganti Uang (GU) tersebut diatas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami laksanakan sesuai dengan DPA-SKPD.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Ganti Uang (GU) tersebut tidak akan digunakan untuk membiayai pengeluaran- pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan Pembayaran Ganti Uang (GU) SKPD kami', 'width' => '510px','align'=>'left','style'=>'border:none;'),
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
					array('data' => 'Pejabat Penatausahaan Kuangan','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
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



function printspp_pernyataan($dokid){

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah', 'sppok'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
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
		array('data' => 'SURAT PERNYATAAN PENGAJUAN SPP-GANTI UANG PERSEDIAAN', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Ganti Uang (GU) yang kami ajukan sebesar Rp ' . 
			$jumlah . ',00 (terbilang ' . $terbilang . ') untuk ' . $namauk . ' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa :', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '1.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Ganti Uang (GU) tersebut diatas akan dipergunakan untuk keperluan guna membiayai kegiatan yang akan kami laksanakan sesuai dengan DPA-SKPD.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '50px','align'=>'right','style'=>'border:none;'),
		array('data' => ' Jumlah Pembayaran Ganti Uang (GU) tersebut tidak akan digunakan untuk membiayai pengeluaran- pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung.', 'width' => '460px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan Pembayaran Ganti Uang (GU) SKPD kami', 'width' => '510px','align'=>'left','style'=>'border:none;'),
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
function printspp_sptjm($dokid){

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah','dpano','dpatgl','kodekeg','keperluan'));
	$query->fields('uk', array('namauk','namasingkat', 'header1', 'header2', 'pimpinannama', 'pimpinannip','pimpinanjabatan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		$pimpinanjabatan=$data->pimpinanjabatan;
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		$namasingkat=$data->namasingkat;
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
		$keperluan=$data->keperluan;
		
		$kodekeg=$data->kodekeg;
	}
	//DPA
	$dpano = '................';
	$dpatgl = $dpano;
	$query = db_select('kegiatandpa', 'd');
	$query->fields('d', array('dpano', 'dpatgl'));
	$query->condition('d.kodekeg', $kodekeg, '=');		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$dpano = $data->dpano;
		$dpatgl = $data->dpatgl;
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
		array('data' => 'SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Yang bertanda tangan di bawah ini, Saya :', 'width' => '510px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Nama', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $pimpinannama, 'width' => '450px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Jabatan', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $pimpinanjabatan, 'width' => '450px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sebagai Pengguna Anggaran pada OPD '.ucwords(strtolower($namauk)).' dengan ini menyatakan bahwa saya bertanggung jawab Penuh terhadap kebenaran perhitungan dan penetapan besaran serta penggunaan dana, sebagaimana tertuang dalam DPA Nomor '.$dpano.' tanggal '.$dpatgl.' Kegiatan Penyusunan dan Pembuatan Laporan Keuangan Tahun '.apbd_tahun().' sebesar Rp. '.$jumlah.',- ('.$terbilang.') untuk '.$keperluan.'.'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Apabila di kemudian hari diketahui terjadi  penyimpangan terhadap penetapan dan perhitungan biaya serta penggunaan dana APBD tersebut di atas, sehingga kemudian menimbulkan kerugian Daerah, maka saya bersedia mengganti dan menyetorkan kerugian tersebut ke Kas Daerah dan bersedia dituntut sesuai dengan peraturan perundangan.'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Demikian surat Pernyataan ini dibuat untuk dapat digunakan sebagaimana mestinya.', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Kepala '.$namasingkat,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
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


function printspp_a21($dokid){
	
	//READ UP
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'jumlah', 'keperluan', 'penerimanama', 'penerimanip'));
	$query->fields('k', array('kegiatan'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$kegiatan = $data->kegiatan;
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		$keperluan = $data->keperluan;
		
		$spptgl = apbd_fd_long($data->spptgl);
		
		$bendaharanama = $data->penerimanama;
		$bendaharanip = $data->penerimanip;
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
	}
	
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
		array('data' => 'Belanja Barang/Jasa', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
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
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah', 'sppok'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip', 'kodedinas'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
		
		$kodedinas = $data->kodedinas;
		
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
		array('data' => 'SURAT KETERANGAN PENGAJUAN SPP-GU', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Pembayaran GU (Ganti Uang) yang kami ajukan sebesar Rp ' . 
				$jumlah . ',00 (terbilang ' . $terbilang . ') untuk ' . $namauk . 
				' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa jumlah tersebut digunakan untuk keperluan kegiatan sebagai berikut:', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	
	$numkeg = 0;
	$res_keg = db_query('select distinct k.kodekeg, k.kodepro, k.kegiatan from dokumenrekening dr inner join kegiatanskpd k on dr.kodekeg=k.kodekeg where dr.dokid=:dokid order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($res_keg as $data_keg) {
		$numkeg++;
		$rows[]=array(
			array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
		);
		$rows[]=array(
			array('data' => $numkeg . '.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
			array('data' => $data_keg->kegiatan, 'width' => '495px','align'=>'left','style'=>'border:none;'),
		);
		
		$nomorkeg = $kodedinas . '.' . $data_keg->kodepro . '.' . substr($data_keg->kodekeg, -3);	
		
		$rows[]=array(
			array('data' => 'No.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
			array('data' => 'Kode Rekening', 'width' => '125px','align'=>'left','style'=>'border:1px solid black;'),
			array('data' => 'Uraian', 'width' => '260px','align'=>'left','style'=>'border:1px solid black;'),
			array('data' => 'Jumlah', 'width' => '100px','align'=>'right','style'=>'border:1px solid black;'),
		);
		
		# get the desired fields from the database
		$query = db_select('dokumenrekening', 'di');
		$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
		
		$query->fields('di', array('jumlah'));
		$query->fields('ro', array('kodero', 'uraian'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('di.dokid', $dokid, '=');
		$query->condition('di.jumlah', 0, '>');
		$query->condition('di.kodekeg', $data_keg->kodekeg, '=');
		$query->orderBy('ro.kodero', 'ASC');

		$results = $query->execute();
		$n = 0; $subtotal = 0;
		foreach ($results as $data) {
			$n++;
			$subtotal += $data->jumlah;
			$rows[]=array(
				array('data' => $n, 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;'),
				array('data' => $data->uraian, 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($data->jumlah), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
			);
		}			
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'SUB TOTAL', 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($subtotal), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		);
	
	}
	
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TOTAL', 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => $jumlah, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan SPP-GU SKPD', 'width' => '510px','align'=>'left','style'=>'border:none;'),
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

function printspp_keterangan_lama($dokid){
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('sppno', 'spptgl', 'jumlah', 'sppok'));
	$query->fields('k', array('kodepro', 'kodekeg'));
	$query->fields('uk', array('namauk', 'header1', 'header2', 'pimpinannama', 'pimpinannip', 'kodedinas'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		if ($data->sppok=='0') $spptgl = '. . . . . . .';
		
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
		$namauk = $data->namauk;
		$header1 = $data->header1;
		$header2 = $data->header2;
		
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);		
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
		array('data' => 'SURAT KETERANGAN PENGAJUAN SPP-GU', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:125%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => 'Nomor : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;'),
	);
	
	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Sehubungan dengan Surat Permintaan Pembayaran GU (Ganti Uang) yang kami ajukan sebesar Rp ' . 
				$jumlah . ',00 (terbilang ' . $terbilang . ') untuk ' . $namauk . 
				' Tahun Anggaran ' . apbd_tahun() . ', dengan ini menyatakan dengan sebenarnya bahwa jumlah tersebut digunakan untuk keperluan sebagai berikut:', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Kode Rekening', 'width' => '125px','align'=>'left','style'=>'border:1px solid black;'),
		array('data' => 'Uraian', 'width' => '260px','align'=>'left','style'=>'border:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '100px','align'=>'right','style'=>'border:1px solid black;'),
	);
	
	# get the desired fields from the database
	$query = db_select('dokumenrekening', 'di');
	$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
	
	$query->fields('di', array('jumlah'));
	$query->fields('ro', array('kodero', 'uraian'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('di.dokid', $dokid, '=');
	$query->condition('di.jumlah', 0, '>');
	$query->orderBy('ro.kodero', 'ASC');

	$results = $query->execute();
	$n = 0;
	foreach ($results as $data) {
		$n++;
		$rows[]=array(
			array('data' => $n, 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '260px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}		
	$rows[]=array(
			array('data' => 'TOTAL', 'width' => '410px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
			array('data' => $jumlah, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	
	$rows[]=array(
		array('data' => 'Demikian surat keterangan ini dibuat untuk melengkapi persyaratan pengajuan SPP-GU SKPD', 'width' => '510px','align'=>'left','style'=>'border:none;'),
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