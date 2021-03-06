<?php


/**
 * Implement verifikasisppgu_tab2()
 */
function verifikasisp2dgu_spp2_form($form, &$form_state) {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_sp2d_gu"];
	} else {
		$_SESSION["verifikasi_sp2d_gu"] = $dokid;
	}

	//KEGIATAN
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		$form['formkegiatan']['kegiatan'. $nokeg]= array(
			'#type'         => 'checkbox', 
			'#title' =>  t($nokeg . '. ' . $datakeg->kegiatan),
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
				'#markup' => verifikasi_spp2($dokid, $datakeg->kodekeg),
			);		
		}	else {
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => '', //verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);	
		}	
	}

	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SP2D',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisp2dgu_spp2_form_submit($form, &$form_state) {
	goback($jenisdok);
}

function verifikasisp2dgu_bk5_form($form, &$form_state) {

	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_sp2d_gu"];
	} else {
		$_SESSION["verifikasi_sp2d_gu"] = $dokid;
	}

	
	$spptgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$tglawal = $spptgl; $tglakhir = $spptgl;
	$spdno = '';
	$spdtgl = '';

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'kodeuk', 'keperluan', 'tglawal', 'tglakhir'));

	$query->condition('d.dokid', $dokid, '=');

	//dpq($query);

	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;
		$keperluan = $data->keperluan ;

		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;

	}

	//KEGIATAN
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING

		$bk6  = '<a href="/verifikasisp2dgu/bk6keg/' . $dokid . '/' . $datakeg->kodekeg . '">BK-6</a>';
		$form['formkegiatan']['kegiatan'. $nokeg]= array(
			'#type'         => 'checkbox', 
			'#title' =>  t($nokeg . '. ' . $datakeg->kegiatan),
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
				'#markup' => verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);		
		}	else {
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => '', //verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);	
		}	
	}

	
	
	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SP2D',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	return $form;
}

function verifikasisp2dgu_bk5_form_submit($form, &$form_state) {
	goback($jenisdok);
}

function verifikasisp2dgu_bk8_form() {
	//verifikasi_bk8($kodeuk, $tglawal, $tglakhir, &$output1, &$output2)
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_sp2d_gu"];
	} else {
		$_SESSION["verifikasi_sp2d_gu"] = $dokid;
	}


	$spptgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$tglawal = $spptgl; $tglakhir = $spptgl;

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'kodeuk','tglawal', 'tglakhir'));

	$query->condition('d.dokid', $dokid, '=');

	//dpq($query);

	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;

		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;

	}


	$form['formrekening_bk8']['bk81'] = array (
		'#type' => 'item',
		'#markup' => verifikasi_bk8($kodeuk, $tglawal, $tglakhir),
	);


	$form['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SP2D',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisp2dgu_bk8_form_submit($form, &$form_state) {
	goback($jenisdok);
}

function verifikasisp2dgu_bk6_form($form, &$form_state) {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_sp2d_gu"];
	} else {
		$_SESSION["verifikasi_sp2d_gu"] = $dokid;
	}

	$spptgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$tglawal = $spptgl; $tglakhir = $spptgl;
	$spdno = '';
	$spdtgl = '';

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'kodeuk', 'keperluan', 'tglawal', 'tglakhir'));

	$query->condition('d.dokid', $dokid, '=');

	//dpq($query);

	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;
		$keperluan = $data->keperluan ;

		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;

	}

	//KEGIATAN
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		//'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $jenis, $sppok) . '</small></em>',/
		$form['formkegiatan']['kegiatan'. $nokeg]= array(
			'#type'         => 'checkbox', 
			'#title' =>  t($nokeg . '. ' . $datakeg->kegiatan),
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
				'#markup' => verifikasi_bk6($kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);		
		}	else {
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => '', //verifikasi_bk6($kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);	
		}	


	}

	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SP2D',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	return $form;
}


function verifikasisp2dgu_edoc_form($form, &$form_state) {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_sp2d_gu"];
	} else {
		$_SESSION["verifikasi_sp2d_gu"] = $dokid;
	}
 

	$form['e_sppk'] = array(
		'#type' => 'item',
		'#markup' => '1. Surat Pernyataan Pejabat Penatausahaan Keuangan',
	);
	$form['e_spk_l']= array(
		'#markup' => "<object data=\"http://simkedajepara.web.id/edoc2019/spm/E_SPPK_".$dokid.".PDF\" 
        type='application/pdf' 
        width='100%' 
        height='100%' style = \"height:300px\">
		</object>",
	);
		
	// drupal_set_message("http://simkedajepara.web.id/edoc2019/spm/E_SPPK_".$dokid.".PDF\"");
	$form['e_spkt'] = array(
		'#type' => 'item',
		'#markup' => '2. Surat Keterangan Pengguna Anggaran',
	);
	$form['e_spkt_l']= array(
		'#markup' => "<object data=\"http://simkedajepara.web.id/edoc2019/spm/E_SPKT_".$dokid.".PDF\" 
        type='application/pdf' 
        width='100%' 
        height='100%' style = \"height:300px\">
		</object>",
	);
	$form['e_sppy'] = array(
		'#type' => 'item',
		'#markup' => '3. Surat Pernyataan Pengguna Anggaran',
	);
	$form['e_sppy_l']= array(
		'#markup' => "<object data=\"http://simkedajepara.web.id/edoc2019/spm/E_SPPY_".$dokid.".PDF\" 
        type='application/pdf' 
        width='100%' 
        height='100%' style = \"height:300px\">
		</object>",
	);
	$form['e_sptjb'] = array(
		'#type' => 'item',
		'#markup' => '4. Surat Pernyataan Tanggung Jawab Belanja oleh Pengguna Anggaran',
	);
	$form['e_sptjb_l']= array(
		'#markup' => "<object data=\"http://simkedajepara.web.id/edoc2019/spm/E_SPTJB_".$dokid.".PDF\" 
        type='application/pdf' 
        width='100%' 
        height='100%' style = \"height:300px\">
		</object>",
	);
	$form['e_spm'] = array(
		'#type' => 'item',
		'#markup' => '5. Surat Pernyataan Tanggung Jawab Belanja oleh Pengguna Anggaran',
	);
	$form['e_spm_l']= array(
		'#markup' => "<object data=\"http://simkedajepara.web.id/edoc2019/spm/E_SPM_".$dokid.".PDF\" 
        type='application/pdf' 
        width='100%' 
        height='100%' style = \"height:300px\">
		</object>",
	);
	
	return $form;
}
function verifikasisp2dgu_bk6_form_submit($form, &$form_state) {
	goback($jenisdok);
}

function verifikasisp2dgu_verify_form() {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_sp2d_gu"];
	} else {
		$_SESSION["verifikasi_sp2d_gu"] = $dokid;
	}

		
	$form['description'] = array(
		'#type' => 'item',
		'#title' => 'Untuk verifikasi, periksa : <ul><li>BK 5</li><li>BK 6</li><li>BK 8</li><li>SPP 2</li><li>Bila semua sudah sesuai, klik Varifikasi</li></ul>',
	);
	
	$form['dokid']= array(
		'#type' => 'value',
		'#value' => $dokid,
	);
	
	$form['dokid']= array(
		'#type' => 'value',
		'#value' => $dokid,
	);

	$sp2dok = 0;

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'sp2dok'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dok = $data->sp2dok;
	}
	if ($sp2dok=='0') {
		$form['formdata']['submit']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span> Verifikasi',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			//'#disabled' => $disable_simpan,
		);

	}  else  {
		$form['formdata']['submitnotok']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Batalkan',
			'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
		);
	}


	$form['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SP2D',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisp2dgu_verify_form_validate($form, &$form_state) {
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];

	$x = 0;
	for ($n=1; $n <= $jumlahrekkelengkapan; $n++) {
		$x += $form_state['values']['valid' . $n];
	}

	//drupal_set_message('valid ' . $x);
	//drupal_set_message('j ' . $jumlahrekkelengkapan);

	if ($jumlahrekkelengkapan != $x) {
		form_set_error('', 'Kelengkapan belum valid semua');
	}
}

function verifikasisp2dgu_verify_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	$jumlahrekkelengkapansp2d = $form_state['values']['jumlahrekkelengkapansp2d'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['submitback']) {
		goback($jenisdok);
	} elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitnotok']) {
		$query = db_update('dokumen')
					->fields(
						array(
							'sp2dok' => 0,

						)
					);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();

		goback($jenisdok);

	} else {

		$query = db_update('dokumen')
		->fields(
				array(
					'sp2dok' => '1',
				)
			);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();

		goback($jenisdok);
	}
}


function goback($jenisdokk) {
	$dokid = $_SESSION["verifikasi_sp2d_gu"];
	
	$reskeg = db_query('select jenisdokumen from dokumen where dokid='. $dokid .'');
	foreach ($reskeg as $data) {
		$jenisdok = $data->jenisdokumen;
	}
	if ($jenisdok == '1'){
		drupal_goto('gubarusp2d/edit/' . $dokid);
	} else if ($jenisdok == '7' or $jenisdok == '5'){
		drupal_goto('tunihilbarusp2d/edit/' . $dokid);
	} else {
		drupal_goto('gubarusp2d/edit/' . $dokid);
	}
}

/*
function verifikasi_bk5($dokid, $kodeuk, $kodekeg, $tglawal, $tglakhir){
	$style='border-right:1px solid black;';

	$rows=null;

	db_set_active('bendahara');

	$rows[]=array(
		array('data' => 'No', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
		array('data' => 'Kode', 'width' => '60px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Uraian', 'width' => '225px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Anggaran', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Sebelumnya', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'UP/GU', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'TU', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'LS', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Total', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Sisa', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
	);


	$n = 0; $anggaran_total =0;
	$lalu_total = 0; $ls_total = 0; $gu_total = 0; $tu_total = 0; $rea_total = 0;

	//contents
	$query = db_select('anggperkeg', 'a');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('a', array('anggaran'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	//dpq($query);

	# execute the query
	$res_rek = $query->execute();
	foreach ($res_rek as $data_rek) {
		$n++;

		//lalu
		$lalu = verifikasi_bk5_read_sebelumnya($kodekeg, $data_rek->kodero, $tglawal);

		//transaksi
		$ls = 0; $gu = 0; $tu = 0;
		verifikasi_bk5_read_sekarang($kodekeg, $data_rek->kodero, $tglawal, $tglakhir, $ls, $gu, $tu);

		$anggaran_total += $data_rek->anggaran;

		$lalu_total += $lalu;

		$ls_total += $ls;
		$gu_total += $gu;
		$tu_total += $tu;

		$realisai = $ls + $gu + $tu + $lalu;
		$rea_total += $realisai;

		//LALU

		//$uraian = l($data_rek->uraian,  '', array ('html' => true));
		//$uraian = l(t('Edit'), '', array('query' => '')),

		$uraian = '<a href="/verifikasisp2dgu/bk6rek/' . $dokid . '/' . $kodekeg . '/' . $data_rek->kodero . '">' .  $data_rek->uraian . '</a>';

		//$uraian = $data_rek->uraian;
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_format_rek_rincianobyek($data_rek->kodero), 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => $uraian, 'width' => '225px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($lalu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($gu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($tu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($ls), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($realisai), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($data_rek->anggaran - $realisai), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),

		);
	}
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => '', 'width' => '60px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => 'TOTAL', 'width' => '225px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($anggaran_total),'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($lalu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($gu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($tu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($ls_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($rea_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($anggaran_total - $rea_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),

	);
	db_set_active();


	$table_element = array(
		'#theme' => 'table',
		'#header' => null,
		'#rows' => $rows,
		'#empty' =>t('Your table is empty'),
	);
	$output = drupal_render($table_element);


	//$output = createT($header, $rows);
	//$output = theme('table', array('header' => null, 'rows' => $rows ));
	return $output;
}


function verifikasi_bk5_read_sekarang($kodekeg, $kodero, $tglawal, $tglakhir, &$ls, &$gu, &$tu) {
	$ls = 0; $gu = 0; $tu = 0;

	//realisasi
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->fields('b', array('jenis'));
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$or->condition('b.jenis', 'tu-spj', '=');
	$or->condition('b.jenis', 'gu-spj', '=');
	$query->condition($or);
	$query->groupBy('b.jenis');

	$result = $query->execute();
	foreach ($result as $data_spj) {

		if ($data_spj->jenis == 'gu-spj')
			$gu += $data_spj->total;
		else if (($data_spj->jenis == 'ls') or ($data_spj->jenis == 'gaji'))
			$ls += $data_spj->total;
		else
			$tu += $data_spj->total;

	}

	//pindahbuku
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->fields('b', array('jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');
	$query->condition('b.jenis', 'pindahbuku', '=');
	//$query->condition('bi.jumlah', '0', '>=');

	$query->groupBy('b.jenispanjar');
	$result = $query->execute();
	foreach ($result as $data_spj) {

		if ($data_spj->jenispanjar == 'gu')
			$gu += $data_spj->total;
		else if ($data_spj->jenispanjar == 'ls')
			$ls += $data_spj->total;
		else
			$tu += $data_spj->total;

	}


	//ret
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->fields('b', array('jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');
	$query->condition('b.jenis', 'ret-spj', '=');

	$query->groupBy('b.jenispanjar');

	$result = $query->execute();
	foreach ($result as $data_spj) {

		if ($data_spj->jenispanjar == 'gu')
			$gu -= $data_spj->total;
		else if ($data_spj->jenispanjar == 'ls')
			$ls -= $data_spj->total;
		else
			$tu -= $data_spj->total;

	}
}


function verifikasi_bk5_read_sebelumnya($kodekeg, $kodero, $tglawal) {
	$val = 0;

	//rea
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '<');

	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$or->condition('b.jenis', 'tu-spj', '=');
	$or->condition('b.jenis', 'gu-spj', '=');
	$or->condition('b.jenis', 'pindahbuku', '=');
	$query->condition($or);

	//dpq($query);

	$result = $query->execute();
	foreach ($result as $data_spj) {

		$val = $data_spj->total;

	}

	//ret
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '<');
	$query->condition('b.jenis', 'ret-spj', '=');

	$result = $query->execute();
	foreach ($result as $data_spj) {

		$val -= $data_spj->total;

	}

	return $val;
}

function verifikasi_spp2($dokid, $kodekeg) {

	//READ UP DATA
	$periode = 1;

	$results = db_query('select kodeuk,spptgl,spdno,jumlah from {dokumen} where dokid=:dokid', array(':dokid'=>$dokid));
	foreach ($results as $data) {
		$kodeuk = $data->kodeuk;
		$spptglvalue = $data->spptgl;
		$spptgl = $data->spptgl;
		$jumlah = $data->jumlah;

		$spdkode = $data->spdno;

	}

	$results = db_query('select kodekeg,anggaran,tw1,tw2,tw3,tw4,periode from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach ($results as $data) {
		$anggaran = $data->anggaran;
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
		array('data' => 'NO.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'URAIAN', 'width' => '245px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'JUMLAH MATA UANG BERSANGKUTAN', 'width' => '240px', 'colspan' => '3', 'align'=>'center','style'=>'border:1px solid black;'),
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
		array('data' => 'Nomor : ' . $dpano, 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tanggal : ' . $dpatgl, 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
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
		array('data' => '1. Tgl./No. : ' . $spdtgl1 . '; ' . $spdno1 , 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah1), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '2. Tgl./No. : ' . $spdtgl2 . '; ' . $spdno2 , 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah2), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '3. Tgl./No. : ' . $spdtgl3 . '; ' . $spdno3 , 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah3), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '4. Tgl./No. : ' . $spdtgl4 . '; ' . $spdno4 , 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
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
		array('data' => 'a. SP2D total sebelumnya', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($sudahcair), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'b. Pengembalian', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($pengembalian), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'c. SP2D GU diminta', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($jumlah), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($totalkeluar), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($saldoakhir), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function verifikasi_bk8($kodeuk, $tglawal, $tglakhir){

	$rows=array();
	$header=array();

	$rows[]=array(
		array('data' => 'PENGELUARAN', 'colspan'=>13, 'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
	);

	$rows[]=array(
		array('data' => 'Uraian', 'rowspan'=>2, 'width' => '130px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'Jumlah Anggaran', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'SPJ-LS Gaji & Non Gaji', 'colspan'=>3, 'width' => '165px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'SPJ-UP/GU', 'colspan'=>3, 'width' => '165px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'SPJ-TU','colspan'=>3,  'width' => '165px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'Jumlah SPJ (LS+UP/GU/TU)', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'Sisa Pagu Anggaran', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
	);


	//Content

	$rows[]=array(
		array('data' => 'sd Bln Lalu', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Bulan Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 's.d Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-bottom:1px solid black;'),

		array('data' => 'sd Bln Lalu', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Bulan Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'sd Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-bottom:1px solid black;'),

		array('data' => 'sd Bln Lalu', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'sd Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-bottom:1px solid black;'),

	);


	db_set_active('bendahara');

	//TOTAL				PENGELUARAN
	//init total
	$total_anggaran = 0;
	$total_lslalu =0; $total_lsini = 0; $total_lstotal = 0;
	$total_gulalu =0; $total_guini = 0; $total_gutotal = 0;
	$total_tulalu =0; $total_tuini = 0; $total_tutotal = 0;

	$total_jumlahspj = 0;
	$total_sisa = 0;

	verifikasi_bk8_read_spj_skpd($kodeuk, $tglawal, $tglakhir, $total_anggaran, $total_lslalu, $total_lsini, $total_lstotal, $total_gulalu, $total_guini, $total_gutotal, $total_tulalu, $total_tuini, $total_tutotal);

	$total_sisa = $total_anggaran - $total_lstotal - $total_gutotal - $total_tutotal;
	$total_jumlahspj = $total_lstotal + $total_gutotal + $total_tutotal;

	$rows[]=array(
			array('data' => 'JUMLAH BELANJA', 'width' => '130px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%;border-bottom:1px solid black;border-top:1px solid black;'),

			array('data' => apbd_fn($total_anggaran), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_lslalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_lsini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_lstotal), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_gulalu), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_guini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_gutotal), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_tulalu), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_tuini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_tutotal), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_jumlahspj), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_sisa), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

	);

	//FOOTER PENGELUARAN
	//1. PAJAK					//Footer Pengeluaran Pajak
	//I. Read Data
	//1. LS GAJI

	$lslalu_pph21  = 0; $lsini_pph21 = 0;
	$lslalu_pph22  = 0; $lsini_pph22 = 0;
	$lslalu_pph23  = 0; $lsini_pph23 = 0;
	$lslalu_pph4  = 0; $lsini_pph4 = 0;
	$lslalu_ppn  = 0; $lsini_ppn = 0;
	$lslalu_pd  = 0; $lsini_pd = 0;

	verifikasi_bk8_read_pajak_ls($kodeuk, $tglawal, $tglakhir, $lslalu_pph21 ,$lsini_pph21,	$lslalu_pph22 ,$lsini_pph22,
		$lslalu_pph23, $lsini_pph23, $lslalu_pph4 ,$lsini_pph4, $lslalu_ppn, $lsini_ppn,
		$lslalu_pd, $lsini_pd);

	$lstotal_pph21 = $lslalu_pph21 + $lsini_pph21;
	$lstotal_pph22 = $lslalu_pph22 + $lsini_pph22;
	$lstotal_pph23 = $lslalu_pph23 + $lsini_pph23;
	$lstotal_pph4 = $lslalu_pph4 + $lsini_pph4;
	$lstotal_ppn = $lslalu_ppn + $lsini_ppn;
	$lstotal_pd = $lslalu_pd + $lsini_pd;

	//2. GU							//Footer Pengeluaran Pajak
	$gulalu_pph21 = 0; $guini_pph21 = 0;
	$gulalu_pph22 = 0; $guini_pph22 = 0;
	$gulalu_pph23 = 0; $guini_pph23 = 0;
	$gulalu_pph4 = 0; $guini_pph4 = 0;
	$gulalu_ppn = 0; $guini_ppn = 0;
	$gulalu_pd = 0; $guini_pd = 0;

	verifikasi_bk8_read_pajak_gu($kodeuk, $tglawal, $tglakhir, $gulalu_pph21 ,$guini_pph21,	$gulalu_pph22 ,$guini_pph22,
		$gulalu_pph23, $guini_pph23, $gulalu_pph4 ,$guini_pph4, $gulalu_ppn, $guini_ppn,
		$gulalu_pd, $guini_pd);

	$gutotal_pph21 = $gulalu_pph21 + $guini_pph21;
	$gutotal_pph22 = $gulalu_pph22 + $guini_pph22;
	$gutotal_pph23 = $gulalu_pph23 + $guini_pph23;
	$gutotal_pph4 = $gulalu_pph4 + $guini_pph4;
	$gutotal_ppn = $gulalu_ppn + $guini_ppn;
	$gutotal_pd = $gulalu_pd + $guini_pd;

	//3. TU							//Footer Pengeluaran Pajak
	$tulalu_pph21 = 0; $tuini_pph21 = 0;
	$tulalu_pph22 = 0; $tuini_pph22 = 0;
	$tulalu_pph23 = 0; $tuini_pph23 = 0;
	$tulalu_pph4 = 0; $tuini_pph4 = 0;
	$tulalu_ppn = 0; $tuini_ppn = 0;
	$tulalu_pd = 0; $tuini_pd = 0;

	verifikasi_bk8_read_pajak_tu($kodeuk, $tglawal, $tglakhir, $tulalu_pph21 ,$tuini_pph21,	$tulalu_pph22 ,$tuini_pph22,
		$tulalu_pph23, $tuini_pph23, $tulalu_pph4 ,$tuini_pph4, $tulalu_ppn, $tuini_ppn,
		$tulalu_pd, $tuini_pd);

	$tutotal_pph21 = $tulalu_pph21 + $tuini_pph21;
	$tutotal_pph22 = $tulalu_pph22 + $tuini_pph22;
	$tutotal_pph23 = $tulalu_pph23 + $tuini_pph23;
	$tutotal_pph4 = $tulalu_pph4 + $tuini_pph4;
	$tutotal_ppn = $tulalu_ppn + $tuini_ppn;
	$tutotal_pd = $tulalu_pd + $tuini_pd;

	//TOTAL PAJAK
	$pajak_ls_lalu = $lslalu_pph21+$lslalu_pph22+$lslalu_pph23+$lslalu_pph4+$lslalu_ppn+$lslalu_pd;
	$pajak_ls_ini = $lsini_pph21+$lsini_pph22+$lsini_pph23+$lsini_pph4+$lsini_ppn+$lsini_pd;
	$pajak_ls_total = $pajak_ls_ini+$pajak_ls_lalu;

	$pajak_gu_lalu = $gulalu_pph21+$gulalu_pph22+$gulalu_pph23+$gulalu_pph4+$gulalu_ppn+$gulalu_pd;
	$pajak_gu_ini = $guini_pph21+$guini_pph22+$guini_pph23+$guini_pph4+$guini_ppn+$guini_pd;
	$pajak_gu_total = $pajak_gu_ini+$pajak_gu_lalu;

	$pajak_tu_lalu = $tulalu_pph21+$tulalu_pph22+$tulalu_pph23+$tulalu_pph4+$tulalu_ppn+$tulalu_pd;
	$pajak_tu_ini = $tuini_pph21+$tuini_pph22+$tuini_pph23+$tuini_pph4+$tuini_ppn+$tuini_pd;
	$pajak_tu_total = $pajak_tu_ini+$pajak_tu_lalu;

	//Read Pengembalian
	$retur_gu_lalu = 0; $retur_tu_lalu = 0;  $retur_ls_lalu = 0;
	$retur_gu_ini = 0; $retur_tu_ini = 0; $retur_ls_ini = 0;

	verifikasi_bk8_read_pengembalian($kodeuk, $tglawal, $tglakhir, $retur_gu_lalu, $retur_gu_ini, $retur_tu_lalu, $retur_tu_ini, $retur_ls_lalu, $retur_ls_ini);
	$retur_gu_total = $retur_gu_lalu + $retur_gu_ini;
	$retur_tu_total = $retur_tu_lalu + $retur_tu_ini;
	$retur_ls_total = $retur_ls_lalu + $retur_ls_ini;

	//TOTAL PENGELUARAN
	$total_keluar_ls_lalu = $total_lslalu + $pajak_ls_lalu + $retur_ls_lalu;
	$total_keluar_ls_ini = $total_lsini + $pajak_ls_ini + $retur_ls_ini;
	$total_keluar_ls_total = $total_lstotal + $pajak_ls_total + $retur_ls_total;

	$total_keluar_gu_lalu = $total_gulalu + $pajak_gu_lalu + $retur_gu_lalu;
	$total_keluar_gu_ini = $total_guini + $pajak_gu_ini + $retur_gu_ini;
	$total_keluar_gu_total = $total_gutotal + $pajak_gu_total + $retur_gu_total;

	$total_keluar_tu_lalu = $total_tulalu + $pajak_tu_lalu + $retur_tu_lalu;
	$total_keluar_tu_ini = $total_tuini + $pajak_tu_ini + $retur_tu_ini;
	$total_keluar_tu_total = $total_tutotal + $pajak_tu_total + $retur_tu_total;

	//II. RENDER PAJAK
	//a. Total Pajak				//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => 'POTONGAN PAJAK', 'width' => '130px','align'=>'left','style'=>'border-bottom:1px solid black;font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_ls_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_ls_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_ls_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_gu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_gu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_gu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_tu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_tu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_tu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_ls_total+$pajak_gu_total+$pajak_tu_total), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

	);
	//b. PPN						//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPN', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_ppn),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_ppn+$gutotal_ppn+$tutotal_ppn), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//c. PPh 21			//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh 21', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph21),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph21+$gutotal_pph21+$tutotal_pph21), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//d. PPh 22				//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh 22', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph22),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph22+$gutotal_pph22+$tutotal_pph22), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//e. PPh 23			//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh 23', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph23),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph23+$gutotal_pph23+$tutotal_pph23), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//f. PPh ps 4		//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh Final', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph4),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph4+$gutotal_pph4+$tutotal_pph4), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//g. Pajak Daerah		//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- Pajak Daerah', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pd),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pd+$gutotal_pd+$tutotal_pd), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);

	//3. PENGEMBALIAN
	$rows[]=array(
			array('data' => 'PENGEMBALIAN', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($retur_ls_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($retur_ls_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($retur_ls_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($retur_gu_lalu), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($retur_gu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($retur_gu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($retur_tu_lalu), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($retur_tu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($retur_tu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' =>  apbd_fn($retur_ls_total + $retur_gu_total + $retur_tu_total), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);

	//4. TOTAL PENGELUARAN
	$rows[]=array(
			array('data' => 'JUMLAH PENGELUARAN', 'width' => '130px','align'=>'left','style'=>'font-size:60%;border-bottom:1px solid black;border-top:1px solid black;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_keluar_ls_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_keluar_ls_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_keluar_ls_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_keluar_gu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_keluar_gu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_keluar_gu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_keluar_tu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_keluar_tu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_keluar_tu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_keluar_ls_total+$total_keluar_gu_total+$total_keluar_tu_total), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

	);

	// PENERIMAAN
	$rows[]=array(
		array('data' => 'PENERIMAAN', 'colspan'=>13, 'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
	);

	$rows[]=array(
		array('data' => 'Uraian', 'rowspan'=>2, 'width' => '130px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-size:60%;'),
		array('data' => '', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'SPJ-LS Gaji & Non Gaji', 'colspan'=>3, 'width' => '165px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'SPJ-UP/GU',  'colspan'=>3, 'width' => '165px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'SPJ-TU',  'colspan'=>3, 'width' => '165px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'Jumlah SPJ s.d. Bulan ini', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
		array('data' => 'Keterangan', 'rowspan'=>2, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:60%;'),
	);


	//Content

	$rows[]=array(
		array('data' => 'sd Bln Lalu', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Bulan Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'sd Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-bottom:1px solid black;'),

		array('data' => 'sd Bln Lalu', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Bulan Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'sd Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-bottom:1px solid black;'),

		array('data' => 'sd Bln Lalu', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Bulan Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'sd Bln Ini', 'width' => '55px','align'=>'center','style'=>'font-size:60%;border-right:1px solid black;border-bottom:1px solid black;'),

	);

	//SP2D
	//a. READ DATA

	//init
	$lslalu = 0; $lsini = 0; $lstotal = 0;
	$gulalu = 0; $guini = 0; $gutotal = 0;
	$tulalu = 0; $tuini = 0; $tutotal = 0;

	verifikasi_bk8_read_sp2d_penerimaan($kodeuk, $tglawal, $tglakhir, $lslalu, $lsini, $lstotal, $gulalu, $guini, $gutotal,
		$tulalu, $tuini, $tutotal);

	//sub total
	$jumlahspj = $lstotal + $gutotal + $tutotal;

	//b. Render Data Penerimaan
	$rows[]=array(
			array('data' => 'SP2D', 'width' => '130px','align'=>'left','style'=>'border-bottom:1px solid black;font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($jumlahspj), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

	);


	//PAJAK		=> SAMA

	//TOTAL PENERIMAAN
	$total_masuk_ls_lalu = $lslalu + $pajak_ls_lalu;
	$total_masuk_ls_ini = $lsini + $pajak_ls_ini;
	$total_masuk_ls_total = $lstotal + $pajak_ls_total;

	$total_masuk_gu_lalu = $gulalu + $pajak_gu_lalu;
	$total_masuk_gu_ini = $guini + $pajak_gu_ini;
	$total_masuk_gu_total = $gutotal + $pajak_gu_total;

	$total_masuk_tu_lalu = $tulalu + $pajak_tu_lalu;
	$total_masuk_tu_ini = $tuini + $pajak_tu_ini;
	$total_masuk_tu_total = $tutotal + $pajak_tu_total;

	//II. RENDER PAJAK
	//a. Total Pajak				//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => 'POTONGAN PAJAK', 'width' => '130px','align'=>'left','style'=>'border-bottom:1px solid black;font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_ls_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_ls_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_ls_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_gu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_gu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_gu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_tu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_tu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($pajak_tu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($pajak_ls_total+$pajak_gu_total+$pajak_tu_total), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:60%;'),

	);
	//b. PPN						//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPN', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_ppn),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_ppn), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_ppn+$gutotal_ppn+$tutotal_ppn), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//c. PPh 21			//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh 21', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph21),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph21), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph21+$gutotal_pph21+$tutotal_pph21), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//d. PPh 22				//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh 22', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph22),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph22), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph22+$gutotal_pph22+$tutotal_pph22), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//e. PPh 23			//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh 23', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph23),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph23), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph23+$gutotal_pph23+$tutotal_pph23), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//f. PPh ps 4		//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- PPh Final', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pph4),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pph4), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pph4+$gutotal_pph4+$tutotal_pph4), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	//g. Pajak Daerah		//Footer Pengeluaran Pajak
	$rows[]=array(
			array('data' => '- Pajak Daerah', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lslalu_pd),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lsini_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($lstotal_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($gulalu_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($guini_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($gutotal_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($tulalu_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tuini_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($tutotal_pd), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($lstotal_pd+$gutotal_pd+$tutotal_pd), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);

	$rows[]=array(
			array('data' => 'PENGEMBALIAN', 'width' => '130px','align'=>'left','style'=>'font-size:60%;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => '0','width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),

			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:60%;'),
			array('data' => '0', 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

			array('data' => '0', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;'),

	);
	$rows[]=array(
			array('data' => 'JUMLAH PENERIMAAN', 'width' => '130px','align'=>'left','style'=>'font-size:60%;border-bottom:1px solid black;border-top:1px solid black;'),

			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_masuk_ls_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_ls_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_ls_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_masuk_gu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_gu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_gu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_masuk_tu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_tu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_tu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_masuk_ls_total+$total_masuk_gu_total+$total_masuk_tu_total), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

	);
	$rows[]=array(
			array('data' => 'SALDO KAS', 'width' => '130px','align'=>'left','style'=>'font-size:60%;border-bottom:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '70px','align'=>'left','style'=>'border-right:1px solid black;font-size:60%;border-bottom:1px solid black;border-top:1px solid black;'),

			array('data' => apbd_fn($total_masuk_ls_lalu-$total_keluar_ls_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_ls_ini-$total_keluar_ls_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_ls_total-$total_keluar_ls_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_masuk_gu_lalu-$total_keluar_gu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_gu_ini-$total_keluar_gu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_gu_total-$total_keluar_gu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn($total_masuk_tu_lalu-$total_keluar_tu_lalu),'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_tu_ini-$total_keluar_tu_ini), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => apbd_fn($total_masuk_tu_total-$total_keluar_tu_total), 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),

			array('data' => apbd_fn(($total_masuk_ls_total+$total_masuk_gu_total+$total_masuk_tu_total) - ($total_keluar_ls_total+$total_keluar_gu_total+$total_keluar_tu_total)), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;font-size:60%;'),
			array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:60%;border-bottom:1px solid black;border-top:1px solid black;'),

	);

	db_set_active();

	//$output1 = createT($header, $rows);
	//$output1 = theme('table', array('header' => $header, 'rows' => $rows ));


	$table_element = array(
		'#theme' => 'table',
		'#header' => null,
		'#rows' => $rows,
		'#empty' =>t('Your table is empty'),
	);
	$output = drupal_render($table_element);


	//$header1 = $header;
	//$row1 = $rows;
	//$output2 = 'B';

	return $output;
}




function verifikasi_bk8_read_spj_skpd($kodeuk, $tglawal, $tglakhir, &$anggaran, &$lslalu, &$lsini, &$lstotal, &$gulalu, &$guini, &$gutotal, &$tulalu, &$tuini, &$tutotal) {

	//init
	$anggaran = 0;
	$lslalu =0; $lsini = 0; $lstotal = 0;
	$gulalu =0; $guini = 0; $gutotal = 0;
	$tulalu =0; $tuini = 0; $tutotal = 0;

	//anggaran
	$res_keg = db_query('select sum(total) as anggaran from {kegiatanskpd} where inaktif=0 and kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($res_keg as $data_spj) {
		$anggaran = $data_spj->anggaran;
	}

	//LALU
	//1. LS
	$query = db_select('bendaharaitem', 'bi');
	$query->innerJoin('bendahara', 'b', 'bi.bendid=b.bendid');
	$query->fields('b', array('jenis', 'jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '<');

	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('b.jenis');
	$query->groupBy('b.jenispanjar');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if (($data_spj->jenis=='gaji') or ($data_spj->jenis=='ls')) {
			$lslalu += $data_spj->total;
		} else if ($data_spj->jenis=='gu-spj') {
			$gulalu += $data_spj->total;
		} else if ($data_spj->jenis=='tu-spj') {
			$tulalu += $data_spj->total;
		} else if ($data_spj->jenis=='pindahbuku') {
			if ($data_spj->jenispanjar=='ls')
				$lslalu += $data_spj->total;
			else if ($data_spj->jenispanjar=='gu')
				$gulalu += $data_spj->total;
			else if ($data_spj->jenispanjar=='tu')
				$tulalu += $data_spj->total;
		}
	}
	//ret
	$query = db_select('bendaharaitem', 'bi');
	$query->innerJoin('bendahara', 'b', 'bi.bendid=b.bendid');
	$query->fields('b', array('jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '<');
	$query->condition('b.jenis', 'ret-spj', '=');
	//$query->condition('b.jenispanjar', 'ls', '=');

	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('b.jenispanjar');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->jenispanjar=='ls') {
			$lslalu -= $data_spj->total;
		} else if ($data_spj->jenispanjar=='gu') {
			$gulalu -= $data_spj->total;
		} else if ($data_spj->jenispanjar=='tu') {
			$tulalu -= $data_spj->total;
		}
	}


	//INI
	//1. LS
	$query = db_select('bendaharaitem', 'bi');
	$query->innerJoin('bendahara', 'b', 'bi.bendid=b.bendid');
	$query->fields('b', array('jenis', 'jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('b.jenis');
	$query->groupBy('b.jenispanjar');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if (($data_spj->jenis=='gaji') or ($data_spj->jenis=='ls')) {
			$lsini += $data_spj->total;
		} else if ($data_spj->jenis=='gu-spj') {
			$guini += $data_spj->total;
		} else if ($data_spj->jenis=='tu-spj') {
			$tuini += $data_spj->total;
		} else if ($data_spj->jenis=='pindahbuku') {
			if ($data_spj->jenispanjar=='ls')
				$lsini += $data_spj->total;
			else if ($data_spj->jenispanjar=='gu')
				$guini += $data_spj->total;
			else if ($data_spj->jenispanjar=='tu')
				$tuini += $data_spj->total;
		}
	}
	//ret
	$query = db_select('bendaharaitem', 'bi');
	$query->innerJoin('bendahara', 'b', 'bi.bendid=b.bendid');
	$query->fields('b', array('jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$query->condition('b.jenis', 'ret-spj', '=');
	//$query->condition('b.jenispanjar', 'ls', '=');

	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('b.jenispanjar');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->jenispanjar=='ls') {
			$lsini -= $data_spj->total;
		} else if ($data_spj->jenispanjar=='gu') {
			$guini -= $data_spj->total;
		} else if ($data_spj->jenispanjar=='tu') {
			$tuini -= $data_spj->total;
		}
	}
	$lstotal = $lslalu + $lsini;
	$gutotal = $gulalu + $guini;
	$tutotal = $tulalu + $tuini;
}

function verifikasi_bk8_read_pajak_ls($kodeuk, $tglawal, $tglakhir, &$lslalu_pph21 ,&$lsini_pph21,	&$lslalu_pph22 ,&$lsini_pph22,
	&$lslalu_pph23, &$lsini_pph23, &$lslalu_pph4 ,&$lsini_pph4, &$lslalu_ppn, &$lsini_ppn,
	&$lslalu_pd, &$lsini_pd) {

	$lslalu_pph21  = 0; $lsini_pph21 = 0;
	$lslalu_pph22  = 0; $lsini_pph22 = 0;
	$lslalu_pph23  = 0; $lsini_pph23 = 0;
	$lslalu_pph4  = 0; $lsini_pph4 = 0;
	$lslalu_ppn  = 0; $lsini_ppn = 0;
	$lslalu_pd  = 0; $lsini_pd = 0;

	//LALU
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharapajak', 'bp', 'b.bendid=bp.bendid');
	$query->fields('bp', array('kodepajak'));

	$query->addExpression('SUM(bp.jumlah)', 'total');

 	$query->condition('b.tanggal', $tglawal, '<');

	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$query->condition($or);
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('bp.kodepajak');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->kodepajak=='01')
			$lslalu_pph21 = $data_spj->total;
		else if ($data_spj->kodepajak=='02')
			$lslalu_pph22 = $data_spj->total;
		else if ($data_spj->kodepajak=='03')
			$lslalu_pph23 = $data_spj->total;
		else if ($data_spj->kodepajak=='04')
			$lslalu_pph4 = $data_spj->total;
		else if ($data_spj->kodepajak=='09')
			$lslalu_ppn = $data_spj->total;
		else
			$lslalu_pd = $data_spj->total;

	}

	//INI
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharapajak', 'bp', 'b.bendid=bp.bendid');
	$query->fields('bp', array('kodepajak'));

	$query->addExpression('SUM(bp.jumlah)', 'total');

 	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$query->condition($or);
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('bp.kodepajak');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->kodepajak=='01')
			$lsini_pph21 = $data_spj->total;
		else if ($data_spj->kodepajak=='02')
			$lsini_pph22 = $data_spj->total;
		else if ($data_spj->kodepajak=='03')
			$lsini_pph23 = $data_spj->total;
		else if ($data_spj->kodepajak=='04')
			$lsini_pph4 = $data_spj->total;
		else if ($data_spj->kodepajak=='09')
			$lsini_ppn = $data_spj->total;
		else
			$lsini_pd = $data_spj->total;

	}
}

function verifikasi_bk8_read_pajak_gu($kodeuk, $tglawal, $tglakhir, &$gulalu_pph21 ,&$guini_pph21,	&$gulalu_pph22 ,&$guini_pph22,
	&$gulalu_pph23, &$guini_pph23, &$gulalu_pph4 ,&$guini_pph4, &$gulalu_ppn, &$guini_ppn,
	&$gulalu_pd, &$guini_pd) {

	//INIT
	$gulalu_pph21 = 0; $guini_pph21 = 0;
	$gulalu_pph22 = 0; $guini_pph22 = 0;
	$gulalu_pph23 = 0; $guini_pph23 = 0;
	$gulalu_pph4 = 0; $guini_pph4 = 0;
	$gulalu_ppn = 0; $guini_ppn = 0;
	$gulalu_pd = 0; $guini_pd = 0;

	//LALU
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharapajak', 'bp', 'b.bendid=bp.bendid');
	$query->fields('bp', array('kodepajak'));

	$query->addExpression('SUM(bp.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '<');

	$query->condition('b.jenis', 'gu-spj', '=');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('bp.kodepajak');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->kodepajak=='01')
			$gulalu_pph21 = $data_spj->total;
		else if ($data_spj->kodepajak=='02')
			$gulalu_pph22 = $data_spj->total;
		else if ($data_spj->kodepajak=='03')
			$gulalu_pph23 = $data_spj->total;
		else if ($data_spj->kodepajak=='04')
			$gulalu_pph4 = $data_spj->total;
		else if ($data_spj->kodepajak=='09')
			$gulalu_ppn = $data_spj->total;
		else
			$gulalu_pd = $data_spj->total;

	}

	//INI
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharapajak', 'bp', 'b.bendid=bp.bendid');
	$query->fields('bp', array('kodepajak'));

	$query->addExpression('SUM(bp.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$query->condition('b.jenis', 'gu-spj', '=');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('bp.kodepajak');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->kodepajak=='01')
			$guini_pph21 = $data_spj->total;
		else if ($data_spj->kodepajak=='02')
			$guini_pph22 = $data_spj->total;
		else if ($data_spj->kodepajak=='03')
			$guini_pph23 = $data_spj->total;
		else if ($data_spj->kodepajak=='04')
			$guini_pph4 = $data_spj->total;
		else if ($data_spj->kodepajak=='09')
			$guini_ppn = $data_spj->total;
		else
			$guini_pd = $data_spj->total;

	}
}


function verifikasi_bk8_read_pajak_tu($kodeuk, $tglawal, $tglakhir, &$tulalu_pph21 ,&$tuini_pph21,	&$tulalu_pph22 ,&$tuini_pph22,
	&$tulalu_pph23, &$tuini_pph23, &$tulalu_pph4 ,&$tuini_pph4, &$tulalu_ppn, &$tuini_ppn,
	&$tulalu_pd, &$tuini_pd) {

	//init
	$tulalu_pph21 = 0; $tuini_pph21 = 0;
	$tulalu_pph22 = 0; $tuini_pph22 = 0;
	$tulalu_pph23 = 0; $tuini_pph23 = 0;
	$tulalu_pph4 = 0; $tuini_pph4 = 0;
	$tulalu_ppn = 0; $tuini_ppn = 0;
	$tulalu_pd = 0; $tuini_pd = 0;

	//LALU
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharapajak', 'bp', 'b.bendid=bp.bendid');
	$query->fields('bp', array('kodepajak'));

	$query->addExpression('SUM(bp.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '<');

	$query->condition('b.jenis', 'tu-spj', '=');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('bp.kodepajak');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->kodepajak=='01')
			$tulalu_pph21 = $data_spj->total;
		else if ($data_spj->kodepajak=='02')
			$tulalu_pph22 = $data_spj->total;
		else if ($data_spj->kodepajak=='03')
			$tulalu_pph23 = $data_spj->total;
		else if ($data_spj->kodepajak=='04')
			$tulalu_pph4 = $data_spj->total;
		else if ($data_spj->kodepajak=='09')
			$tulalu_ppn = $data_spj->total;
		else
			$tulalu_pd = $data_spj->total;

	}

	//INI
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharapajak', 'bp', 'b.bendid=bp.bendid');
	$query->fields('bp', array('kodepajak'));

	$query->addExpression('SUM(bp.jumlah)', 'total');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$query->condition('b.jenis', 'tu-spj', '=');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('bp.kodepajak');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->kodepajak=='01')
			$tuini_pph21 = $data_spj->total;
		else if ($data_spj->kodepajak=='02')
			$tuini_pph22 = $data_spj->total;
		else if ($data_spj->kodepajak=='03')
			$tuini_pph23 = $data_spj->total;
		else if ($data_spj->kodepajak=='04')
			$tuini_pph4 = $data_spj->total;
		else if ($data_spj->kodepajak=='09')
			$tuini_ppn = $data_spj->total;
		else
			$tuini_pd = $data_spj->total;

	}
}


function verifikasi_bk8_read_pengembalian($kodeuk, $tglawal, $tglakhir, &$gulalu_ret, &$guini_ret, &$tulalu_ret, &$tuini_ret, &$lslalu_ret, &$lsini_ret ) {

	//INIT
	$gulalu_ret = 0; $guini_ret = 0;
	$tulalu_ret = 0; $tuini_ret = 0;
	$lslalu_ret = 0; $lsini_ret = 0;


	//LALU
	$query = db_select('bendahara', 'b');
	$query->fields('b', array('jenispanjar'));

	$query->addExpression('SUM(b.total)', 'total');

	$query->condition('b.tanggal', $tglawal, '<');

	//$query->condition('b.jenis', 'ret-kas', '=');
	$query->condition('b.jenis', db_like('ret-') . '%', 'LIKE');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('b.jenispanjar');

	//dpq($query);

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->jenispanjar=='ls')
			$lslalu_ret = $data_spj->total;

		else if ($data_spj->jenispanjar=='gu')
			$gulalu_ret = $data_spj->total;

		else
			$tulalu_ret = $data_spj->total;

	}

	//INI
	$query = db_select('bendahara', 'b');
	$query->fields('b', array('jenispanjar'));

	$query->addExpression('SUM(b.total)', 'total');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	//$query->condition('b.jenis', 'ret-kas', '=');
	$query->condition('b.jenis', db_like('ret-') . '%', 'LIKE');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$query->groupBy('b.jenispanjar');

	//dpq($query);

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		if ($data_spj->jenispanjar=='ls')
			$lsini_ret = $data_spj->total;

		else if ($data_spj->jenispanjar=='gu')
			$guini_ret = $data_spj->total;

		else
			$tuini_ret = $data_spj->total;

	}
}

function verifikasi_bk8_read_sp2d_penerimaan($kodeuk, $tglawal, $tglakhir, &$lslalu, &$lsini, &$lstotal, &$gulalu, &$guini, &$gutotal, &$tulalu, &$tuini, &$tutotal) {

	//init
	$lslalu =0; $lsini = 0; $lstotal = 0;
	$gulalu =0; $guini = 0; $gutotal = 0;
	$tulalu =0; $tuini = 0; $tutotal = 0;

	//LALU
	//1. LS
	$query = db_select('bendahara', 'b');
	$query->addExpression('SUM(b.total)', 'total');
	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$query->condition($or);
	$query->condition('b.kodeuk', $kodeuk, '=');

	$query->condition('b.tanggal', $tglawal, '<');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		$lslalu = $data_spj->total;
	}


	//2. GU							//Read Data Penerimaan Content
	$query = db_select('bendahara', 'b');
	$query->addExpression('SUM(b.total)', 'total');

	$or = db_or();
	$or->condition('b.jenis', 'gu-kas', '=');
	$or->condition('b.jenis', 'up', '=');
	$query->condition($or);

	$query->condition('b.tanggal', $tglawal, '<');

	$query->condition('b.kodeuk', $kodeuk, '=');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		$gulalu = $data_spj->total;
	}

	//3. TU							//Read Data Penerimaan Content
	$query = db_select('bendahara', 'b');
	$query->addExpression('SUM(b.total)', 'total');

	$query->condition('b.tanggal', $tglawal, '<');

	$query->condition('b.jenis', 'tu', '=');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		$tulalu = $data_spj->total;
	}

	//INI
	//1. LS
	$query = db_select('bendahara', 'b');
	$query->addExpression('SUM(b.total)', 'total');
	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$query->condition($or);
	$query->condition('b.kodeuk', $kodeuk, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		$lsini = $data_spj->total;
	}
	$lstotal = $lslalu + $lsini;


	//2. GU							//Read Data Penerimaan Content
	$query = db_select('bendahara', 'b');
	$query->addExpression('SUM(b.total)', 'total');

	$or = db_or();
	$or->condition('b.jenis', 'gu-kas', '=');
	$or->condition('b.jenis', 'up', '=');
	$query->condition($or);

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$query->condition('b.kodeuk', $kodeuk, '=');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		$guini = $data_spj->total;
	}
	$gutotal = $gulalu + $guini;

	//3. TU							//Read Data Penerimaan Content
	$query = db_select('bendahara', 'b');
	$query->addExpression('SUM(b.total)', 'total');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');

	$query->condition('b.jenis', 'tu', '=');
	$query->condition('b.kodeuk', $kodeuk, '=');
	$res_spj = $query->execute();
	foreach ($res_spj as $data_spj) {
		$tuini = $data_spj->total;
	}
	$tutotal = $tulalu + $tuini;
}

function verifikasi_bk6($kodeuk, $kodekeg, $tglawal, $tglakhir){

	db_set_active('bendahara');

	//Rekening
	$rows=null;

	$query = db_select('anggperkeg', 'a');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('a', array('anggaran'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	//dpq($query);

	# execute the query
	$res_rek = $query->execute();
	$i = 0;
	foreach ($res_rek as $data_rek) {
		if (verifikasi_bk6_is_ada_transaksi($kodekeg, $data_rek->kodero, $tglawal, $tglakhir)) {

			$i++;;
			$rows[]=array(
				array('data' => $i . '. ' . $data_rek->kodero . ' - ' . $data_rek->uraian, 'colspan'=>5,'width' => '430px','align'=>'left','style'=>'border:none;font-size:80%;font-weight: bold'),
				array('data' => 'Anggaran : ', 'width' => '70px','align'=>'right','style'=>'border:none;font-size:80%;font-weight: bold'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px','align'=>'right','style'=>'border:none;font-size:80%;font-weight: bold'),
			);

			$rows[]=array(
				array('data' => 'No.', 'width' => '10px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				array('data' => 'Tanggal', 'width' => '50px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				array('data' => 'Uraian', 'width' => '200px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				array('data' => 'Keterangan', 'width' => '100px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),

				array('data' => 'JUMLAH SPJ', 'width' => '210px','colspan'=>3,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'LS', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
				array('data' => 'UP/GU', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
				array('data' => 'TU', 'width' => '70px','align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
			);

			$query = db_select('bendahara', 'b');
			$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
			$query->fields('b', array('tanggal', 'keperluan', 'jenis', 'jenispanjar', 'penerimanama'));
			$query->fields('bi', array('jumlah', 'keterangan'));
			$query->condition('b.kodekeg', $kodekeg, '=');
			$query->condition('bi.kodero', $data_rek->kodero, '=');
			$query->condition('bi.jumlah', 0, '<>');

			$query->condition('b.tanggal', $tglawal, '>=');
			$query->condition('b.tanggal', $tglakhir, '<=');

			$or = db_or();
			$or->condition('b.jenis', 'gaji', '=');
			$or->condition('b.jenis', 'ls', '=');
			$or->condition('b.jenis', 'tu-spj', '=');
			$or->condition('b.jenis', 'gu-spj', '=');
			$or->condition('b.jenis', 'ret-spj', '=');
			$or->condition('b.jenis', 'pindahbuku', '=');
			$query->condition($or);

			$query->orderBy('b.tanggal', 'ASC');

			$total_ls = 0; $total_gu = 0; $total_tu = 0;

			# execute the query
			$no = 0;
			$res_spj = $query->execute();
			foreach ($res_spj as $data_spj) {
				$no++;

				$ls = 0; $gu = 0; $tu = 0;
				if ($data_spj->jenis == 'gu-spj')
					$gu = $data_spj->jumlah;

				else if (($data_spj->jenis == 'ls') or ($data_spj->jenis == 'gaji'))
					$ls += $data_spj->jumlah;

				else if ($data_spj->jenis == 'ret-spj') {

					if ($data_spj->jenispanjar == 'gu')
						$gu = -$data_spj->jumlah;
					else if ($data_spj->jenispanjar == 'ls')
						$ls = -$data_spj->jumlah;
					else
						$tu = -$data_spj->jumlah;

				} else
					$tu = $data_spj->jumlah;

				$total_ls += $ls; $total_gu += $gu; $total_tu += $tu;

				$ketdetil = $data_spj->penerimanama;
				if ($data_spj->keterangan<>'') $ketdetil .= ' (' . $data_spj->keterangan . ')';
				$rows[] = array(
					array('data' => $no, 'width' => '10px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
					array('data' => apbd_fd($data_spj->tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => $data_spj->keperluan, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => $ketdetil, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),

					array('data' => apbd_fn($ls), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => apbd_fn($gu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => apbd_fn($tu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				);

			}
			if ($no==0) {
				$rows[] = array(
					array('data' => '', 'width' => '10px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => 'Tidak ada', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => 'Tidak ada', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),

					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				);
			}

			//SEBELUMNYA
			$ls_lalu = 0; $gu_lalu = 0; $tu_lalu = 0;
			verifikasi_bk6_read_sebelumnya($kodekeg, $data_rek->kodero, $tglawal, $ls_lalu, $gu_lalu, $tu_lalu);

			$rows[]=array(
				array('data' => 'Jumlah periode ini', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;font-size:80%;'),

				array('data' => apbd_fn($total_ls), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_gu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_tu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah sampai dengan periode sebelumnya', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),

				array('data' => apbd_fn($ls_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($gu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah sampai dengan periode ini', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),

				array('data' => apbd_fn($total_ls + $ls_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_gu + $gu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_tu + $tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah Total Pengeluaran', 'colspan'=>6,'width' => '540px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-weight:bold;border-bottom:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_ls+$total_gu+$total_tu + $ls_lalu + $gu_lalu + $tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-right:1px solid black;font-size:80%;'),
			);

			//space
			$rows[]=array(
				array('data' => '', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			);


		}	//ada transaksi
	}	//end rekening

	db_set_active();
				//render
	//$output = createT($header, $rows);

	$table_element = array(
		'#theme' => 'table',
		'#header' => null,
		'#rows' => $rows,
		'#empty' =>t('Your table is empty'),
	);
	$output = drupal_render($table_element);

	return $output;
}

*/
