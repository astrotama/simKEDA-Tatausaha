<?php


/**
 * Implement verifikasisppgu_tab2()
 */
function verifikasisppgucoba_spp2_form() {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
	}

	//KEGIATAN
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		$form['formrekening_spp2' . $datakeg->kodekeg] = array (
			'#type' => 'fieldset',
			'#title'=> $nokeg . '. ' . $datakeg->kegiatan,
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,
		);


		$form['formrekening_spp2' . $datakeg->kodekeg]['spp2'] = array (
			'#type' => 'item',
			'#markup' => verifikasi_spp2($dokid, $datakeg->kodekeg),
		);



	}

	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisppgu_spp2_form_submit($form, &$form_state) {
	goback();
}

function verifikasisppgu_spp2_form($form, &$form_state) {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
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
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisppgucoba_spp2_form_submit($form, &$form_state) {
	goback();
}

function verifikasisppgu_bk5_form($form, &$form_state) {

	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
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

		$bk6  = '<a href="/verifikasisppgu/bk6keg/' . $dokid . '/' . $datakeg->kodekeg . '"> BK-6</a>';
		/*
		$form['formrekening_bk5' . $nokeg] = array (
			'#type' => 'fieldset',
			'#title'=> $nokeg . '. ' . $datakeg->kegiatan . '<em><small class="text-info pull-right">' . $bk6 .  '</small></em>',
			'#collapsible' => FALSE,
			'#collapsed' => FALSE,
		);
		*/
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
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	return $form;
}

function verifikasisppgucoba_bk5_form() {

	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
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

		$bk6  = '<a href="/verifikasisppgu/bk6keg/' . $dokid . '/' . $datakeg->kodekeg . '">BK-6</a>';
		$form['formrekening_bk5' . $datakeg->kodekeg] = array (
			'#type' => 'fieldset',
			'#title'=> $nokeg . '. ' . $datakeg->kegiatan . '<em><small class="text-info pull-right">' . $bk6 .  '</small></em>',
			'#collapsible' => FALSE,
			'#collapsed' => FALSE,
		);

		$form['formrekening_bk5' . $datakeg->kodekeg]['bk5'] = array (
			'#type' => 'item',
			'#markup' => '', 	//verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
		);


	}

	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	return $form;
}

function verifikasisppgu_bk5_form_submit($form, &$form_state) {
	goback();
}
function verifikasisppgucoba_bk5_form_submit($form, &$form_state) {
	drupal_goto('');
}

function verifikasisppgu_bk8_form() {
	//verifikasi_bk8($kodeuk, $tglawal, $tglakhir, &$output1, &$output2)
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
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
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisppgu_bk8_form_submit($form, &$form_state) {
	goback();
}

function verifikasisppgu_bk6_form($form, &$form_state) {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
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
				'#markup' => '', //verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);	
		}		
	}

	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	return $form;
}


function verifikasisppgu_bk6_form_submit($form, &$form_state) {
	goback();
}

function verifikasisppgu_verify_form() {
	$dokid = arg(2);
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
	}
	/*
	$form['tablekelengkapan']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th><th width="5px">Ada</th></th><th width="50px">Valid</th></tr>',
		 '#suffix' => '</table></div>',
	);
	$i = 0;
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'url', 'valid'));
	$query->fields('lt', array('uraian', 'tipe'));
	$query->condition('dk.dokid', $dokid, '=');
	$query->orderBy('lt.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++;

		if ($data->tipe=='0') {
			if ($data->url=='') {
				$uraian = $data->uraian;
				$simbol = apbd_icon_belumada();
				$disabled = true;

			} else {
				$uraian = l($data->uraian, $data->url, array('html'=>true));
				$simbol = apbd_icon_sudahada();
				$disabled = false;
			}


		} else {	//ESigner
			$disabled = false;
			$uraian = $data->uraian;
			$simbol = apbd_icon_belumada();
		}

		$form['tablekelengkapan']['kodekelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $data->kodekelengkapan,
		);
		$form['tablekelengkapan']['uraiankelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		);

		$form['tablekelengkapan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		);
		$form['tablekelengkapan']['uraian' . $i]= array(
			//'#type'         => 'textfield',
			'#prefix' => '<td>',
			'#markup'=> $uraian  . $disabled,
			'#suffix' => '</td>',
		);
		$form['tablekelengkapan']['simbol' . $i]= array(
			//'#type'         => 'textfield',
			'#prefix' => '<td>',
			'#markup'=> $simbol,
			'#suffix' => '</td>',
		);

		$form['tablekelengkapan']['valid' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'checkbox',
			'#default_value'=> $data->valid,
			'#disabled'=> $disabled,
			'#suffix' => '</td></tr>',
		);


	}
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	*/

	$form['description'] = array(
		'#type' => 'item',
		'#title' => 'Untuk verifikasi, klik Varifikasi</li></ul>',
	);	
	$form['dokid']= array(
		'#type' => 'value',
		'#value' => $dokid,
	);
	

	$sppok = 0;

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'sppok'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$sppok = $data->sppok;
	}
	if ($sppok=='0') {
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
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);

	return $form;
}

function verifikasisppgu_verify_form_validate($form, &$form_state) {
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

function verifikasisppgu_verify_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['submitback']) {
		goback();
	} elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitnotok']) {
		$query = db_update('dokumen')
					->fields(
						array(
							'sppok' => 0,

						)
					);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();

		goback();
	} else {
		/*
		for ($n=1; $n <= $jumlahrekkelengkapan; $n++) {
			$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
			$valid = $form_state['values']['valid' . $n];

			$query = db_update('dokumenkelengkapan')
			->fields(
					array(
						'valid' => $valid,
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodekelengkapan', $kodekelengkapan, '=');
			$res = $query->execute();

		}

		//KELENGKAPAN
		$num_deleted = db_delete('dokumenkelengkapanspm')
		  ->condition('dokid', $dokid)
		  ->execute();
		$res = db_query('select kodekelengkapan from ltkelengkapanspm where jenis=1 order by nomor');
		foreach ($res as $data) {
			db_insert('dokumenkelengkapanspm')
				->fields(array('dokid', 'kodekelengkapan'))
				->values(array(
						'dokid'=> $dokid,
						'kodekelengkapan' => $data->kodekelengkapan,
						))
				->execute();
		}
		*/

		$query = db_update('dokumen')
		->fields(
				array(
					'sppok' => '1',
				)
			);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();

		goback();
	}
}


function goback() {
	$dokid = $_SESSION["verifikasi_spp_gu"];
	drupal_goto('gubaruspp/edit/' . $dokid);
}
