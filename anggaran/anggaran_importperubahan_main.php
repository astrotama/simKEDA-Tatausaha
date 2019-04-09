<?php
function anggaran_importperubahan_main($arg=NULL, $nama=NULL) {
	$form = drupal_get_form('anggaran_importperubahan_form');
	$output = getlaporan();
	return drupal_render($form).$output;
	//return $output;
}
 
function anggaran_importperubahan_form($form, &$form_state) {

	db_set_active('anggaran');
	
	$res = db_query('SELECT revisi FROM {setupapp}');
	foreach ($res as $data) {
		$revisi = $data->revisi;
	}
	db_set_active();
	
	drupal_set_title('Transfer Anggaran Revisi #' . $revisi);

	$form['revisi']= array(
		'#type'         => 'value', 
		'#value' => $revisi,
	);	
	$form['notes'] = array(
		'#type' => 'markup',	
		'#markup' => '<div class="import-notes">Perhatian!<ul><li>Pastikan bahwa anggaran per kegitaan dan rekeningnya sudah benar dan sudah dissahkan.</li><li>Proses ini akan meng-update anggaran di Penatausahaan, Bendahara dan Akuntansi sekaligus.</li><li>Untuk meng-update anggaran, klik tombol <code>Update Anggaran</code> dibawah dan tunggu prosesnya sampai selesai.</li></ul></div>',
	);	

	$form['pendapatan']= array(
		'#type'  => 'checkbox', 
		'#title'  => 'Transfer Pendapatan',
		'#default_value' => '0',
	);	
	$form['pembiayaan']= array(
		'#type'  => 'checkbox', 
		'#title'  => 'Transfer Pembiayaan',
		'#default_value' => '0',
	);	
	//SIMPAN 
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => 'Update Anggaran',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
} 
function anggaran_importperubahan_form_validate($form, &$form_state) {

}
	 
function anggaran_importperubahan_form_submit($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	
	transfer_perubahan($revisi);
	
}

function transfer_perubahan($revisi) {
	
	$periode = $revisi + 1;
	
	db_set_active('anggaran');
	
	$res_keg = db_query('SELECT kodekeg,kegiatan FROM {kegiatanperubahan} WHERE inaktif=0 and periode=:periode order by kodeuk, kegiatan', array(':periode'=>$periode));
	$arr_kegiatan = $res_keg->fetchAll();
	db_set_active();
	
	$operations = array();
	
	foreach ($arr_kegiatan as $kegiatan) {
		//drupal_set_message($kegiatan->kegiatan);
		transfer_kegiatan($kegiatan->kodekeg);
 
		$operations[] = array(
							'anggaran_importperubahan_form_batch_processing',  // The function to run on each row
							array($kegiatan),  // The row in the csv
						);
		
	}
	
	transfer_pendapatan();
	$operations[] = array(
						'anggaran_importperubahan_form_batch_processing',  // The function to run on each row
						array('pendapatan'),  // The row in the csv
					);
	
	// Once everything is gathered and ready to be processed... well... process it!
	$batch = array( 
		'title' => t('Meng-update data anggaran...'),
		'operations' => $operations,  // Runs all of the queued processes from the while loop above.
		'finished' => 'anggaran_importperubahan_form_finished', // Function to run when the import is successful
		'error_message' => t('The installation has encountered an error.'),
		'progress_message' => t('Kegiatan ke @current dari @total kegiatan'),
	);
	batch_set($batch);
			
	//drupal_set_message('Selesai');	
}

function transfer_kegiatan($kodekeg) {

	db_set_active('anggaran');
	
	//baca data dari kegiatan dari anggaran
	$res_keg = db_query('SELECT * FROM {kegiatanperubahan} WHERE kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	$arr_kegiatan = $res_keg->fetchAll();

	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperkegperubahan} WHERE kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	$arr_rek = $res_rek->fetchAll();

	//baca data dari rekening kegiatan dari anggaran
	$res_dpa = db_query('SELECT * FROM {kegiatandpa} WHERE kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	$arr_dpa = $res_dpa->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active();
	foreach ($arr_kegiatan as $data_keg) {
		
		//drupal_set_message('Kegiatan : ' . $data_keg->kegiatan);
		
		//delete first
		$num = db_delete('kegiatanskpd')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	
		
		$num = db_delete('anggperkeg')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	

		//ReInput 
		$kodeuk = $data_keg->kodeuk;
		if ($data_keg->isppkd=='1') $kodeuk = '00';
		$num = db_insert('kegiatanskpd') // Table name no longer needs {}
			->fields(array(
				'kodekeg' => $data_keg->kodekeg, 
				'jenis' => $data_keg->jenis, 
				'tahun' => $data_keg->tahun, 
				'kodepro' => $data_keg->kodepro, 
				'kodeuk' => $kodeuk, 
				'kegiatan' => $data_keg->kegiatan, 
				'kodesuk' => $data_keg->kodesuk, 
				'isgaji' => $data_keg->isgaji,
				'isppkd' => $data_keg->isppkd,
				'inaktif' => $data_keg->inaktif,
				
				'sumberdana1' => $data_keg->sumberdana1, 
				
				'programsasaran' => $data_keg->programsasaran, 
				'programtarget' => $data_keg->programtarget, 
				'masukansasaran' => $data_keg->masukansasaran, 
				'masukantarget' => $data_keg->masukantarget, 
				'keluaransasaran' => $data_keg->keluaransasaran, 
				'keluarantarget' => $data_keg->keluarantarget, 
				'hasilsasaran' => $data_keg->hasilsasaran, 
				'hasiltarget' => $data_keg->hasiltarget,  

				//'tw1penetapan' => $data_keg->tw1, 
				//'tw2penetapan' => $data_keg->tw2, 
				//'tw3penetapan' => $data_keg->tw3, 
				//'tw4penetapan' => $data_keg->tw4, 

				'tw1' => $data_keg->tw1p, 
				'tw2' => $data_keg->tw2p, 
				'tw3' => $data_keg->tw3p, 
				'tw4' => $data_keg->tw4p, 

				//'totalpenetapan' => $data_keg->total, 
				'total' => $data_keg->totalp, 
				'anggaran'=> $data_keg->totalp, 
				
				'waktupelaksanaan' => $data_keg->waktupelaksanaan,
				'latarbelakang' => $data_keg->latarbelakang,
				'kelompoksasaran' => $data_keg->kelompoksasaran,
				'sumberdana2' => '-', 
				
			))
			->execute();		  
		  
		//Rekening	
		foreach ($arr_rek as $data_rek) {
			
			//drupal_set_message('Rekening : ' . $data_rek->kodero . ' - ' . $data_rek->jumlahp);
			$num = db_insert('anggperkeg') // Table name no longer needs {}
				->fields(array(
					'kodekeg' => $data_rek->kodekeg,
					'kodero' => $data_rek->kodero,
					'uraian' => $data_rek->uraian,
					//'jumlahpenetapan' => $data_rek->jumlah,
					'jumlah' => $data_rek->jumlahp,
					'anggaran' => $data_rek->jumlahp,				 
					//'jumlahsebelum' => $data_rek->jumlahsebelum,
				))
				->execute();	
			
			
		}
	}
 
	//DPA
	foreach ($arr_dpa as $data_dpa) {
		
		//drupal_set_message('DPA ' . $kodekeg);
		
		$num = db_delete('kegiatandpa')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	

		$num = db_insert('kegiatandpa') // Table name no longer needs {}
			->fields(array(
				'kodekeg' => $data_dpa->kodekeg,
				'dpano' => $data_dpa->dpano,
				'dpatgl' => $data_dpa->dpatgl,
			))
			->execute();	
		  
	}	
		
 
	//Transfer Bendahara
	db_set_active('bendahara');
	foreach ($arr_kegiatan as $data_keg) {
		
		//drupal_set_message('Kegiatan : ' . $data_keg->kegiatan);
		
		//delete first
		$num = db_delete('kegiatanskpd')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	
		
		$num = db_delete('anggperkeg')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	

		//ReInput 
		$kodeuk = $data_keg->kodeuk;
		if ($data_keg->isppkd=='1') $kodeuk = '00';		
		$num = db_insert('kegiatanskpd') // Table name no longer needs {}
			->fields(array(
				'kodekeg' => $data_keg->kodekeg, 
				'jenis' => $data_keg->jenis, 
				'tahun' => $data_keg->tahun, 
				'kodepro' => $data_keg->kodepro, 
				'kodeuk' => $kodeuk, 
				'kegiatan' => $data_keg->kegiatan, 
				'kodesuk' => $data_keg->kodesuk, 
				'isgaji' => $data_keg->isgaji,
				'inaktif' => $data_keg->inaktif,
				
				'sumberdana1' => $data_keg->sumberdana1, 
				
				'programsasaran' => $data_keg->programsasaran, 
				'programtarget' => $data_keg->programtarget, 
				'masukansasaran' => $data_keg->masukansasaran, 
				'masukantarget' => $data_keg->masukantarget, 
				'keluaransasaran' => $data_keg->keluaransasaran, 
				'keluarantarget' => $data_keg->keluarantarget, 
				'hasilsasaran' => $data_keg->hasilsasaran, 
				'hasiltarget' => $data_keg->hasiltarget,  	

				//'tw1penetapan' => $data_keg->tw1, 
				//'tw2penetapan' => $data_keg->tw2, 
				//'tw3penetapan' => $data_keg->tw3, 
				//'tw4penetapan' => $data_keg->tw4, 

				'tw1' => $data_keg->tw1p, 
				'tw2' => $data_keg->tw2p, 
				'tw3' => $data_keg->tw3p, 
				'tw4' => $data_keg->tw4p, 

				//'totalpenetapan' => $data_keg->total, 
				'total' => $data_keg->totalp, 
				'anggaran'=> $data_keg->totalp, 
				
				'waktupelaksanaan' => $data_keg->waktupelaksanaan,
				'latarbelakang' => $data_keg->latarbelakang,
				'kelompoksasaran' => $data_keg->kelompoksasaran,
				  
			))
			->execute();		  
		  
		//Rekening	
		foreach ($arr_rek as $data_rek) {
			
			//drupal_set_message('Rekening : ' . $data_rek->kodero . ' - ' . $data_rek->jumlahp);
			$num = db_insert('anggperkeg') // Table name no longer needs {}
				->fields(array(
					'kodekeg' => $data_rek->kodekeg,
					'kodero' => $data_rek->kodero,
					'uraian' => $data_rek->uraian,
					//'jumlahpenetapan' => $data_rek->jumlah,
					'jumlah' => $data_rek->jumlahp,
					'anggaran' => $data_rek->jumlahp,				 
					//'jumlahsebelum' => $data_rek->jumlahsebelum,
				))
				->execute();	
			
			
		}
	}
	
	db_set_active();
	
	//Transfer Akuntansi
	db_set_active('akuntansi');
	foreach ($arr_kegiatan as $data_keg) {
		
		//drupal_set_message('Akuntansi Kegiatan : ' . $data_keg->kegiatan);
		
		//delete first
		$num = db_delete('kegiatanskpd')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	
		
		$num = db_delete('anggperkeg')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	

		//ReInput 
		$kodeuk = $data_keg->kodeuk;
		if ($data_keg->isppkd=='1') $kodeuk = '00';		
		$num = db_insert('kegiatanskpd') // Table name no longer needs {}
			->fields(array(
				'kodekeg' => $data_keg->kodekeg, 
				'jenis' => $data_keg->jenis, 
				'tahun' => $data_keg->tahun, 
				'kodepro' => $data_keg->kodepro, 
				'kodeuk' => $kodeuk, 
				'kegiatan' => $data_keg->kegiatan, 
				'kodesuk' => $data_keg->kodesuk, 
				'isgaji' => $data_keg->isgaji,
				'inaktif' => $data_keg->inaktif,
				
				'sumberdana1' => $data_keg->sumberdana1, 
				
				'programsasaran' => $data_keg->programsasaran, 
				'programtarget' => $data_keg->programtarget, 
				'masukansasaran' => $data_keg->masukansasaran, 
				'masukantarget' => $data_keg->masukantarget, 
				'keluaransasaran' => $data_keg->keluaransasaran, 
				'keluarantarget' => $data_keg->keluarantarget, 
				'hasilsasaran' => $data_keg->hasilsasaran, 
				'hasiltarget' => $data_keg->hasiltarget,  	

				//'tw1penetapan' => $data_keg->tw1, 
				//'tw2penetapan' => $data_keg->tw2, 
				//'tw3penetapan' => $data_keg->tw3, 
				//'tw4penetapan' => $data_keg->tw4, 

				'tw1' => $data_keg->tw1p, 
				'tw2' => $data_keg->tw2p, 
				'tw3' => $data_keg->tw3p, 
				'tw4' => $data_keg->tw4p, 

				//'totalpenetapan' => $data_keg->total, 
				'total' => $data_keg->totalp, 
				'anggaran'=> $data_keg->totalp, 
				
				'waktupelaksanaan' => $data_keg->waktupelaksanaan,
				'latarbelakang' => $data_keg->latarbelakang,
				'kelompoksasaran' => $data_keg->kelompoksasaran,
				  
			))
			->execute();		  
		  
		//Rekening	
		foreach ($arr_rek as $data_rek) {
			
			//drupal_set_message('Akuntansi Rekening : ' . $data_rek->kodero . ' - ' . $data_rek->jumlahp);
			$num = db_insert('anggperkeg') // Table name no longer needs {}
				->fields(array(
					'kodekeg' => $data_rek->kodekeg,
					'kodero' => $data_rek->kodero,
					'uraian' => $data_rek->uraian,
					//'jumlahpenetapan' => $data_rek->jumlah,
					'jumlah' => $data_rek->jumlahp,
					'anggaran' => $data_rek->jumlahp,				 
					//'jumlahsebelum' => $data_rek->jumlahsebelum,
				))
				->execute();	
			
			
		}
	}
	
	db_set_active();	

}

function transfer_pendapatan() {

	db_set_active('anggaran');
	
	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperukperubahan} where jumlah<>jumlahp');
	$arr_rek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active(); db_set_active('akuntansi');

	
	//Rekening	
	foreach ($arr_rek as $data_rek) {
		$kodeuk = $data_rek->kodeuk;
		//drupal_set_message($data_rek->kodero . ' . ' . $data_rek->kodero);
		if ($kodeuk=='81') {
			if ((substr($data_rek->kodero, 0, 2)>=42) or (substr($data_rek->kodero, 0, 3)>=413)) {
				$kodeuk= '00';
			}
		}
		$num = db_delete('anggperuk')
			->condition('kodeuk', $kodeuk)
			->condition('kodero', $data_rek->kodero)
		  	->execute();	
		
		
		$num = db_insert('anggperuk') // Table name no longer needs {}
			->fields(array(
				'tahun' => $data_rek->tahun,
				'kodeuk' => $kodeuk,
				'kodero' => $data_rek->kodero,
				'uraian' => $data_rek->uraian,
				'jumlah' => $data_rek->jumlahp,
				'anggaran' => $data_rek->jumlahp,				 
			))
			->execute();	
		//if ($num) drupal_set_message('Input Item OK');	
		
	}

	
	//update ppkd
	/*
	$ret = db_query("UPDATE anggperuk SET kodeuk='00' WHERE kodeuk='81' AND LEFT(kodero,2)>='42'");
	$ret = db_query("UPDATE anggperuk SET kodeuk='00' WHERE kodeuk='81' AND LEFT(kodero,3)>='413'");
	*/

	db_set_active();
}

function transfer_pembiayaan() {

	db_set_active('anggaran');
	
	//baca data dari kegiatan dari anggaran
	$res_keg = db_query('SELECT SUM(jumlahp) AS jumlahpembiayaan FROM {anggperdaperubahan} WHERE jumlah<>jumlahp AND left(kodero,2)=:kodekeluar', array(':kodekeluar' => '62'));
	foreach ($res_keg as $data) {
		$jumlahpembiayaan = $data->jumlahpembiayaan;
	}

	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperdaperubahan} WHERE jumlah<>jumlahp AND left(kodero,2)=:kodekeluar', array(':kodekeluar' => '62'));
	$arr_rek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active();

	//delete first
	$num = db_delete('kegiatanskpd')
	  ->condition('kodekeg', apbd_kodekeg_pembiayaan())
	  ->execute();	
	
	$num = db_delete('anggperkeg')
	  ->condition('kodekeg', apbd_kodekeg_pembiayaan())
	  ->execute();	
	
	//ReInput 
	$num = db_insert('kegiatanskpd') // Table name no longer needs {}
		->fields(array(
			'kodekeg' => apbd_kodekeg_pembiayaan(), 
			'jenis' => '2', 
			'tahun' => apbd_tahun(), 
			'kodepro' => '000', 
			'kodeuk' => '00', 
			'kegiatan' => 'Kegiatan Pembiayaan', 
			'kodesuk' => '0101', 
			'isgaji' => '0',

			'sumberdana1' => 'APBY', 
			
			'programsasaran' => '-', 
			'programtarget' => '-', 
			'masukansasaran' => '-', 
			'masukantarget' => '-', 
			'keluaransasaran' => '-', 
			'keluarantarget' => '-', 
			'hasilsasaran' => '-', 
			'hasiltarget' => '-',  	

			//'tw1penetapan' => $data_keg->tw1, 
			//'tw2penetapan' => $data_keg->tw2, 
			//'tw3penetapan' => $data_keg->tw3, 
			//'tw4penetapan' => $data_keg->tw4, 

			'tw1' => $jumlahpembiayaan, 
			'tw2' => 0, 
			'tw3' => 0, 
			'tw4' => 0, 

			//'totalpenetapan' => $data_keg->total, 
			'total' => $jumlahpembiayaan, 
			'anggaran'=> $jumlahpembiayaan, 
			
			'waktupelaksanaan' => '-',
			'latarbelakang' => '-',
			'kelompoksasaran' => '-',
			  
		))
		->execute();	
	
	//Rekening	
	foreach ($arr_rek as $data_rek) {
		
		//drupal_set_message('Rekening : ' . $data_rek->kodero);
		
		
		$num = db_insert('anggperkeg') // Table name no longer needs {}
			->fields(array(
				'kodekeg' => apbd_kodekeg_pembiayaan(),
				'kodero' => $data_rek->kodero,
				'uraian' => $data_rek->uraian,
				'jumlah' => $data_rek->jumlah,
				'anggaran' => $data_rek->jumlah,				 
				'jumlahsebelum' => $data_rek->jumlahsebelum,
			))
			->execute();	
		//if ($num) drupal_set_message('Input Item OK');	
		
	}
	db_set_active();
 
	//Transfer Bendahara
	db_set_active('bendahara');
	//delete first
	$num = db_delete('kegiatanskpd')
	  ->condition('kodekeg', apbd_kodekeg_pembiayaan())
	  ->execute();	
	
	$num = db_delete('anggperkeg')
	  ->condition('kodekeg', apbd_kodekeg_pembiayaan())
	  ->execute();	
	
	//ReInput 
	$num = db_insert('kegiatanskpd') // Table name no longer needs {}
		->fields(array(
			'kodekeg' => apbd_kodekeg_pembiayaan(), 
			'jenis' => '2', 
			'tahun' => apbd_tahun(), 
			'kodepro' => '000', 
			'kodeuk' => '00', 
			'kegiatan' => 'Kegiatan Pembiayaan', 
			'kodesuk' => '0101', 
			'isgaji' => '0',

			'sumberdana1' => 'APBY', 
			
			'programsasaran' => '-', 
			'programtarget' => '-', 
			'masukansasaran' => '-', 
			'masukantarget' => '-', 
			'keluaransasaran' => '-', 
			'keluarantarget' => '-', 
			'hasilsasaran' => '-', 
			'hasiltarget' => '-',  	

			//'tw1penetapan' => $data_keg->tw1, 
			//'tw2penetapan' => $data_keg->tw2, 
			//'tw3penetapan' => $data_keg->tw3, 
			//'tw4penetapan' => $data_keg->tw4, 

			'tw1' => $jumlahpembiayaan, 
			'tw2' => 0, 
			'tw3' => 0, 
			'tw4' => 0, 

			//'totalpenetapan' => $data_keg->total, 
			'total' => $jumlahpembiayaan, 
			'anggaran'=> $jumlahpembiayaan, 
			
			'waktupelaksanaan' => '-',
			'latarbelakang' => '-',
			'kelompoksasaran' => '-',
			  
		))
		->execute();	
	
	//Rekening	
	foreach ($arr_rek as $data_rek) {
		
		//drupal_set_message('Rekening : ' . $data_rek->kodero);
		
		
		$num = db_insert('anggperkeg') // Table name no longer needs {}
			->fields(array(
				'kodekeg' => apbd_kodekeg_pembiayaan(),
				'kodero' => $data_rek->kodero,
				'uraian' => $data_rek->uraian,
				'jumlah' => $data_rek->jumlah,
				'anggaran' => $data_rek->jumlah,				 
				'jumlahsebelum' => $data_rek->jumlahsebelum,
			))
			->execute();	
		//if ($num) drupal_set_message('Input Item OK');	
		
	}
	db_set_active();
 
}

function transfer_rekening() {

	db_set_active('anggaran');
	
	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {jenis}');
	$arr_jenis = $res_rek->fetchAll();

	$res_rek = db_query('SELECT * FROM {obyek}');
	$arr_obyek = $res_rek->fetchAll();

	$res_rek = db_query('SELECT * FROM {rincianobyek}');
	$arr_rincianobyek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active();

	//delete first
	$num = db_delete('jenis')->execute();	
	$num = db_delete('obyek')->execute();	
	$num = db_delete('rincianobyek')->execute();	
	
	
	//ReInput 
	foreach ($arr_jenis as $data) {
		$num = db_insert('jenis') // Table name no longer needs {}
			->fields(array(
				'kodej' =>  $data->kodej, 
				'kodek' => $data->kodek, 
				'uraian' => $data->uraian, 
			))
			->execute();	
	}
	foreach ($arr_obyek as $data) {
		$num = db_insert('obyek') // Table name no longer needs {}
			->fields(array(
				'kodej' =>  $data->kodej, 
				'kodeo' => $data->kodeo, 
				'uraian' => $data->uraian, 
			))
			->execute();	
	}	
	foreach ($arr_rincianobyek as $data) {
		$num = db_insert('rincianobyek') // Table name no longer needs {}
			->fields(array(
				'kodero' =>  $data->kodero, 
				'kodeo' => $data->kodeo, 
				'uraian' => $data->uraian, 
			))
			->execute();	
	}	

	//Transfer Bendahara
	db_set_active('bendahara');

	//delete first
	//delete first
	$num = db_delete('jenis')->execute();	
	$num = db_delete('obyek')->execute();	
	$num = db_delete('rincianobyek')->execute();	
	
	
	//ReInput 
	foreach ($arr_jenis as $data) {
		$num = db_insert('jenis') // Table name no longer needs {}
			->fields(array(
				'kodej' =>  $data->kodej, 
				'kodek' => $data->kodek, 
				'uraian' => $data->uraian, 
			))
			->execute();	
	}
	foreach ($arr_obyek as $data) {
		$num = db_insert('obyek') // Table name no longer needs {}
			->fields(array(
				'kodej' =>  $data->kodej, 
				'kodeo' => $data->kodeo, 
				'uraian' => $data->uraian, 
			))
			->execute();	
	}	
	foreach ($arr_rincianobyek as $data) {
		$num = db_insert('rincianobyek') // Table name no longer needs {}
			->fields(array(
				'kodero' =>  $data->kodero, 
				'kodeo' => $data->kodeo, 
				'uraian' => $data->uraian, 
			))
			->execute();	
	}	
	db_set_active();
 
}


function anggaran_importperubahan_form_batch_processing($data) {
	
}

function anggaran_importperubahan_form_finished() {
  drupal_set_message(t('Update anggaran selesai...'));
}

function getlaporan(){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	// $kodekeg = '201933001002';

	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '55px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '55px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '375x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	//****BELANJA
	$kodeuk = 00;
	$totalb = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
		$fsql = sprintf($sql, db_escape_field($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}

	$rowsrek[] = array (
			 array('data' => '',  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
			 array('data' => '',  'width' => '375px', 'style' => 'border-right: 1px solid black; text-align:left;'),
			 array('data' => '',  'width' => '90px', 'style' => 'border-right: 1px solid black; text-align:right;'),
			 );

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		foreach ($resultakun as $dataakun) {
			$totalb += $dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;font-size:125%;'),
								 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_field($kodeuk), db_escape_field($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_field($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				foreach ($resultkel as $datakel) {
					if ($tingkat>3) {
						$rowsrek[] = array (
								 array('data' => '',  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:50%;'),
								 array('data' => '',  'width' => '375px', 'style' => 'border-right: 1px solid black; text-align:left;font-size:50%;'),
								 array('data' => '',  'width' => '90px', 'style' => 'border-right: 1px solid black; text-align:right;font-size:50%;'),
								 );
					}		
					
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\' and jumlah>0';
						$fsql = sprintf($sql, db_escape_field($kodeuk), db_escape_field($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\' and jumlah>0';
						$fsql = sprintf($sql, db_escape_field($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						foreach ($result as $data) {
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';

								$rowsrek[] = array (
										 array('data' => '',  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;font-size:25%;'),
										 array('data' => '',  'width' => '375px', 'style' => 'border-right: 1px solid black; text-align:left;font-size:25%;'),
										 array('data' => '',  'width' => '90px', 'style' => 'border-right: 1px solid black; text-align:right;font-size:25%;'),
										 );
								
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\' and jumlah>0';
									$fsql = sprintf($sql, db_escape_field($kodeuk), db_escape_field($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,3)=\'%s\' and jumlah>0';
									$fsql = sprintf($sql, db_escape_field($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								$resulto = db_query($fsql);
								if ($resulto) {
							
							foreach ($resulto as $datao) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\' and jumlah>0';
												$fsql = sprintf($sql, db_escape_field($kodeuk), db_escape_field($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanskpd} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\' and jumlah>0';
												$fsql = sprintf($sql, db_escape_field($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											//drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												foreach ($resultro as $dataro) {
													//font-size:small;
													if ($dataro->kodero=='52219003')
														$rowsrek[] = array (
																			 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																			 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;font-size:small;'),
																			 array('data' => apbd_fn($dataro->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																			 );	
													else 
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
												}
											}
										}
									}
								}
							}
							
						}
					}										 
										 
				////////
				}
			}			
		}

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '55px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'JUMLAH BELANJA',  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
	}

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output = createT($headersrek, $rowsrek,$opttbl);
	
	//$output .= $toutput;
	
	return $output;


	// $output = createT($header,$rows,null);
	// 	return $output;
}

?>
