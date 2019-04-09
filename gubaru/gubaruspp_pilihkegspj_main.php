<?php
function gubaruspp_pilihkegspj_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('gubaruspp_pilihkegspj_main_form');
	return drupal_render($output_form);// . $output;
	
}

function gubaruspp_pilihkegspj_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	/*$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjgajilastpage"] = $referer;
	else
		$referer = $_SESSION["spjgajilastpage"];*/

	$dokid = arg(2);
	$kodekeg = arg(3);
	
	$kodeuk = apbd_getuseruk();
	
	//drupal_set_message($dokid);
	//drupal_set_message($kodekeg);

	$title = 'Pilih SPJ-';
	$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach ($results as $data) {
		$title .= $data->kegiatan;
	}
	drupal_set_title($title);
	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);	

	
	db_set_active('bendahara');
	
	
	$form['formdokumen']['tablespj']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">No.</th><th width="100px">No. SPJ</th><th width="100px">Tanggal</th><th>Keperluan</th><th>Penerima</th><th width="100px">Jumlah</th><th width="80px">Diajukan</th><th width="40px">Pilih</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	//$query = db_query('SELECT bendid, spjno, tanggal, keperluan, total FROM `bendahara`  WHERE sudahproses=0 and kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));

	$query = db_select('bendahara' . $kodeuk, 'b');

	# get the desired fields from the database
	$query->fields('b', array('bendid',  'spjno', 'jenis', 'tanggal', 'keperluan', 'penerimanama', 'penerimanama', 'total', 'sudahproses'));
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('b.sudahproses', 0, '=');

	
	//$db_or = db_or();
	//	$db_or->condition('b.jenis', 'gu-spj' , '=');
	//	$db_or->condition('b.jenis', 'pindahbuku' , '=');
	//$query->condition($db_or);
	
	
	$query->condition('b.jenis', 'gu-spj' , '=');
	$query->orderBy('b.tanggal', 'ASC');
	
	//$query->range(0,10);
	
	//dpq ($query);

	# execute the query
	$results = $query->execute();
	
	$total = 0;
	foreach ($results as $data) {

		$i++; 
		$spjno = $data->spjno;
		$tanggal = $data->tanggal;
		$keperluan = $data->keperluan;
		$penerimanama = $data->penerimanama;
		$jumlah = $data->total;
		
		$total += $data->total;
		
		
		//if ($data->jenis=='pindahbuku') {
		//	$resx = db_query('select sum(jumlah) as jumlahnya from {bendaharaitem } where bendid=:bendid and jumlah>0', array(':bendid'=>$data->bendid));
		//	foreach ($resx as $datax) {
		//		$jumlah = $datax->jumlahnya;
		//	}
		//}
		
		
		if ($data->sudahproses)
			$sudahproses = 'Sudah';
		else
			$sudahproses = 'Belum';
		
		$form['formdokumen']['tablespj']['bendid' . $i]= array(
				'#type' => 'value',
				'#value' => $data->bendid,
		); 
		$form['formdokumen']['tablespj']['jumlah' . $i]= array(
				'#type' => 'value',
				'#value' => $jumlah,
		); 
		 
		$form['formdokumen']['tablespj']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['spjno' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $spjno,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['tanggal' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_fd($tanggal),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['uraian' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $keperluan,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['penerimanama' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $penerimanama,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['jumlah_view' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p align="right">' . apbd_fn($jumlah) . '</p>',
				//'#size' => 10,
				'#suffix' => '</td>',
		);
		$form['formdokumen']['tablespj']['sudah' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p align="center">' . $sudahproses . '</p>',
				//'#size' => 10,
				'#suffix' => '</td>',
		);
		$form['formdokumen']['tablespj']['pilih' . $i]= array(
			'#type'         => 'checkbox', 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	

	}
	db_set_active();
	

	
	$form['formdokumen']['tablespj']['nomor_t']= array(
			'#prefix' => '<tr><td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablespj']['spjno_t']= array(
			'#prefix' => '<td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablespj']['tanggal_t']= array(
			'#prefix' => '<td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablespj']['uraian_t']= array(
			'#prefix' => '<td>',
			'#markup' => 'TOTAL',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablespj']['penerimanama_t']= array(
			'#prefix' => '<td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formdokumen']['tablespj']['jumlah_t']= array(
			'#prefix' => '<td>',
			'#markup' => '<p align="right">' . apbd_fn($total) . '</p>',
			//'#size' => 10,
			'#suffix' => '</td>',
	);
	$form['formdokumen']['tablespj']['sudah_t']= array(
			'#prefix' => '<td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	);
	$form['formdokumen']['tablespj']['pilih_t']= array(
		'#prefix' => '<td>',
		'#markup' => '',
		'#suffix' => '</td></tr>',
	);	

	$form['formdokumen']['jumlahdok']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
    
	//CETAK BAWAH
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span> Lanjut',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}

function gubaruspp_pilihkegspj_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
	$jumlahdok = $form_state['values']['jumlahdok'];
	$total = 0;
	
	for ($n=1;$n<=$jumlahdok;$n++){
		if($form_state['values']['pilih' . $n]!=0){
			$total += $form_state['values']['jumlah' . $n];
		}
	}
		
	if ($total<=0) {
		form_set_error('', 'Belum ada SPJ yang dipilih.');
	}	
}
	
function gubaruspp_pilihkegspj_main_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];	
	$kodekeg = $form_state['values']['kodekeg'];	
	$jumlahdok = $form_state['values']['jumlahdok'];
	
	


	if ($dokid=='usulan') {
		$str='';
		for($n=1;$n<=$jumlahdok;$n++){
			if($form_state['values']['pilih' . $n]!=0){
				$str.=$form_state['values']['bendid' . $n];
				if($n!=$jumlahdok)$str.=',';
			}
			
			
		}
		$_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]=$str;
		

		drupal_goto('gubaru/new/' . $kodekeg);

	
	} else {
		$kodeuk = apbd_getuseruk();
		
		$or = db_or();
		$or_update = db_or();
		for ($n=1;$n<=$jumlahdok;$n++){
			if($form_state['values']['pilih' . $n]){
				$or->condition('b.bendid', $form_state['values']['bendid' . $n], '=');
				$or_update->condition('bendid', $form_state['values']['bendid' . $n], '=');
			}
		}

		db_set_active('bendahara');

		$query = db_select('bendaharaitem' . $kodeuk, 'bi');
		$query->join('bendahara' . $kodeuk, 'b', 'b.bendid=bi.bendid');
		$query->fields('bi', array('kodero'));
		$query->addExpression('SUM(bi.jumlah)', 'jumlahnya');
		$query->condition($or);

		$query->groupBy('bi.kodero');
		
		$results = $query->execute();
		$arr_result = $results->fetchAllAssoc('kodero');
		

		$query = db_update('bendahara' . $kodeuk) // Table name no longer needs {}
			->fields(array(
			  'sudahproses' => '1',
			  'dokid' => $dokid,
			   )
			 );
		$query->condition($or_update);
		$res = $query->execute();
		
		db_set_active();

		foreach ($arr_result as $data) {
			
			//drupal_set_message($data->kodero);
			//drupal_set_message($data->jumlahnya);
			
			
			db_insert('dokumenrekening')
				->fields(array('dokid', 'kodero', 'jumlah', 'kodekeg'))
				->values(array(
						'dokid'=> $dokid,
						'kodero' => $data->kodero,
						'jumlah' => $data->jumlahnya,
						'kodekeg' => $kodekeg,
						))
				->execute();
			
			
		}
		
		
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
		
	
		
		drupal_goto('gubaruspp/edit/' . $dokid);
	}
}



?>
