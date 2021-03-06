<?php

 
function anggaran_import_form($form, &$form_state) {


	
	$form['notes'] = array(
		'#type' => 'markup',	
		'#markup' => '<div class="import-notes">Perhatian!<ul><li>Pastikan bahwa anggaran per kegitaan dan rekeningnya sudah benar dan sudah dissahkan.</li><li>Proses ini akan meng-update anggaran di Penatausahaan, Bendahara dan Akuntansi sekaligus.</li><li>Untuk meng-update anggaran, klik tombol <code>Update Anggaran</code> dibawah dan tunggu prosesnya sampai selesai.</li></ul></div>',
	);

	$form['warning'] = array(
		'#type' => 'markup',	
		'#markup' => '<p style="color:red">PASTIKAN BAHWA SIMMANAGER SUDAH OFFLINE1</p>',
	);
	
	//SIMPAN 
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Update Anggaran',
		'#attributes' => array('class' => array('btn btn-success')),
	);
	
	return $form;
} 

function anggaran_import_form_validate($form, &$form_state) {

}
	 
function anggaran_import_form_submit($form, &$form_state) {
	db_set_active('anggaran');
	
	$res_keg = db_query('SELECT kodekeg,kegiatan FROM `kegiatanskpd` WHERE inaktif=0 and total>0 order by kodeuk, kegiatan');
	$arr_kegiatan = $res_keg->fetchAll();
	db_set_active();
	
	$operations = array();
	
	$server = 'akuntansi';
	//Reset
	delete_kegiatan_all($server);
	$operations[] = array(
						'anggaran_import_form_batch_processing',  // The function to run on each row
						array('Delete'),  // The row in the csv
					);
	
	foreach ($arr_kegiatan as $kegiatan) {
		//drupal_set_message($kegiatan->kegiatan);
		transfer_kegiatan($kegiatan->kodekeg, $server);
 
		$operations[] = array(
							'anggaran_import_form_batch_processing',  // The function to run on each row
							array($kegiatan),  // The row in the csv
						);
		
	}
	
	
	
	//Pembiayaan
	transfer_pembiayaan($server);	
	$operations[] = array(
						'anggaran_import_form_batch_processing',  // The function to run on each row
						array(apbd_kodekeg_pembiayaan()),  // The row in the csv
					);
	//Rekening	
	transfer_rekening($server);
	$operations[] = array(
						'anggaran_import_form_batch_processing',  // The function to run on each row
						array('Rekening'),  // The row in the csv
					);
	
	

	transfer_pendapatan($server);
	$operations[] = array(
						'anggaran_import_form_batch_processing',  // The function to run on each row
						array('Pendapatan'),  // The row in the csv
					);
	
	// Once everything is gathered and ready to be processed... well... process it!
	$batch = array( 
		'title' => t('Meng-update data anggaran...'),
		'operations' => $operations,  // Runs all of the queued processes from the while loop above.
		'finished' => 'anggaran_import_form_finished', // Function to run when the import is successful
		'error_message' => t('The installation has encountered an error.'),
		'progress_message' => t('Kegiatan ke @current dari @total kegiatan'),
	);
	batch_set($batch);
			
	//drupal_set_message('Selesai');
	
	
}

function transfer_kegiatan($kodekeg, $server) {

	db_set_active('anggaran');
	
	//baca data dari kegiatan dari anggaran
	$res_keg = db_query('SELECT * FROM {kegiatanskpd} WHERE kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	$arr_kegiatan = $res_keg->fetchAll();

	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	$arr_rek = $res_rek->fetchAll();
 
	//Transfer Penatausahaan
	db_set_active(); db_set_active($server);

	foreach ($arr_kegiatan as $data_keg) {

		
		//delete first
		$num = db_delete('kegiatanskpd')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	
		
		$num = db_delete('anggperkeg')
		  ->condition('kodekeg', $kodekeg)
		  ->execute();	
		
		
		//ReInput 
		if ($data_keg->isppkd=='1')
			$kodeuk = '00';
		else
			$kodeuk = $data_keg->kodeuk;
		
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

				'plafon1' => 0, 
				'plafon2' => 0, 

				'total' => $data_keg->total, 
				'sumberdana1' => $data_keg->sumberdana1, 
				'programsasaran' => $data_keg->programsasaran, 
				'programtarget' => $data_keg->programtarget, 
				'masukansasaran' => $data_keg->masukansasaran, 
				'masukantarget' => $data_keg->masukantarget, 
				'keluaransasaran' => $data_keg->keluaransasaran, 
				'keluarantarget' => $data_keg->keluarantarget, 
				'hasilsasaran' => $data_keg->hasilsasaran, 
				'hasiltarget' => $data_keg->hasiltarget,  
				'tw1' => $data_keg->tw1, 
				'tw2' => $data_keg->tw2, 
				'tw3' => $data_keg->tw3, 
				'tw4' => $data_keg->tw4, 
				'anggaran'=> $data_keg->total, 
				
				'sumberdana2' => $data_keg->sumberdana2,
				'waktupelaksanaan' => $data_keg->waktupelaksanaan,
				'latarbelakang' => $data_keg->latarbelakang,
				'kelompoksasaran' => $data_keg->kelompoksasaran,
				  
			))
			->execute();	
		
		//Rekening	
		foreach ($arr_rek as $data_rek) {
			
			//drupal_set_message('Rekening : ' . $data_rek->kodero);
			
			
			$num = db_insert('anggperkeg') // Table name no longer needs {}
				->fields(array(
					'kodekeg' => $data_rek->kodekeg,
					'kodero' => $data_rek->kodero,
					'uraian' => $data_rek->uraian,
					'jumlah' => $data_rek->jumlah,
					'anggaran' => $data_rek->jumlah,				 
				))
				->execute();	
			//if ($num) drupal_set_message('Input Item OK');	
			
		}
	}
	db_set_active();
}


function transfer_pembiayaan($server) {

	db_set_active('anggaran');
	
	//baca data dari kegiatan dari anggaran
	$res_keg = db_query('SELECT SUM(jumlah) AS jumlahpembiayaan FROM {anggperda} WHERE left(kodero,2)=:kodekeluar', array(':kodekeluar' => '62'));
	foreach ($res_keg as $data) {
		$jumlahpembiayaan = $data->jumlahpembiayaan;
	}

	//drupal_set_message($jumlahpembiayaan);
	
	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperda} WHERE left(kodero,2)=:kodekeluar', array(':kodekeluar' => '62'));
	$arr_rek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active(); db_set_active($server);

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
			'jenis' => '1', 
			'tahun' => apbd_tahun(), 
			'kodepro' => '000', 
			'kodeuk' => '00', 
			'kegiatan' => 'Kegiatan Pembiayaan', 
			'kodesuk' => '0001', 
			'isgaji' => '0',
			'total' => $jumlahpembiayaan, 
			'sumberdana1' => 'APBY', 
			'programsasaran' => '-', 
			'programtarget' => '-', 
			'masukansasaran' => '-', 
			'masukantarget' => '-', 
			'keluaransasaran' => '-', 
			'keluarantarget' => '-', 
			'hasilsasaran' => '-', 
			'hasiltarget' => '-',  
			'tw1' => 0, 
			'tw2' => 0, 
			'tw3' => 0, 
			'tw4' => 0, 
			'anggaran'=> $jumlahpembiayaan, 
			
			'sumberdana2' => '-',
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
			))
			->execute();	
		//if ($num) drupal_set_message('Input Item OK');	
		
	}
	db_set_active();
 
}

function transfer_pendapatan($server) {

	db_set_active('anggaran');
	
	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperuk}');
	$arr_rek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active(); db_set_active($server);

	
	//Rekening	
	foreach ($arr_rek as $data_rek) {

		$kodeuk = $data_rek->kodeuk;
		drupal_set_message($data_rek->kodero);
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
				'jumlah' => $data_rek->jumlah,
				'anggaran' => $data_rek->jumlah,				 
			))
			->execute();	
		//if ($num) drupal_set_message('Input Item OK');	
		
	}
 
 	db_set_active('anggaran');
	
	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {anggperda} WHERE left(kodero,2)=:kodekeluar', array(':kodekeluar' => '61'));
	$arr_rek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active(); db_set_active($server);

	//Rekening	
	foreach ($arr_rek as $data_rek) {
		
		//drupal_set_message('Rekening : ' . $data_rek->kodero);
		
		
		$num = db_insert('anggperda') // Table name no longer needs {}
			->fields(array(
				'tahun' => $data_rek->tahun,
				'kodeuk' => '00',
				'kodero' => $data_rek->kodero,
				'uraian' => $data_rek->uraian,
				'jumlah' => $data_rek->jumlah,
				'anggaran' => $data_rek->jumlah,				 
				'jumlahsebelum' => $data_rek->jumlahsebelum,
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


function transfer_rekening($server) {

	db_set_active('anggaran');
	
	//baca data dari rekening kegiatan dari anggaran
	$res_rek = db_query('SELECT * FROM {jenis}');
	$arr_jenis = $res_rek->fetchAll();

	$res_rek = db_query('SELECT * FROM {obyek}');
	$arr_obyek = $res_rek->fetchAll();

	$res_rek = db_query('SELECT * FROM {rincianobyek}');
	$arr_rincianobyek = $res_rek->fetchAll();
	
	//Transfer Penatausahaan
	db_set_active(); db_set_active($server);

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



function anggaran_import_form_batch_processing($data) {
	
}

function anggaran_import_form_finished() {
  drupal_set_message(t('Update anggaran selesai...'));
}

function delete_kegiatan_all($server) {
	db_set_active(); db_set_active($server);
	$num = db_delete('kegiatanskpd')->execute();	
	$num = db_delete('anggperkeg')->execute();	
	db_set_active();
	  
}
?>
