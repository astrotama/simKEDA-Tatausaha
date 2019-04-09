<?php
function usulan_selectspj_main($arg=NULL, $nama=NULL) {

	$spjid = arg(2);
	$output_form = drupal_get_form('usulan_selectspj_main_form');
	return drupal_render($output_form);// . $output;
	
}

function usulan_selectspj_main_form($form, &$form_state) {
	$spjid = arg(2);
	$dokid = arg(3);

	//drupal_set_message($spjid);
	//drupal_set_message($dokid);	

	$result = db_query('SELECT s.spjid, s.kodeuk, s.kodekeg, s.tanggal, s.keperluan, s.jumlah, s.posting, d.spptgl, d.sppno, s.tglakhir, s.tglawal, s.spdno, s.bk5url, s.bk6url, s.spp2url FROM spjtu s left join dokumen d ON s.dokid=d.dokid WHERE s.spjid =:spjid', array(':spjid' => $spjid));
	foreach($result AS $data){
		$kodeuk = $data->kodeuk;
		$tanggal = dateapi_convert_timestamp_to_datetime($data->tanggal);
		
		$tanggalsql = $data->tanggal;
		
		$kodekeg = $data->kodekeg;
		$keperluan = $data->keperluan;
		$jumlah = $data->jumlah;

		$bk5url = $data->bk5url;
		$bk6url = $data->bk6url;
		$spp2url = $data->spp2url;
		
		if ($data->posting==0)
			$keterangan = '<p>Belum dibuatkan SPP</p>';
		else
			$keterangan = '<p>Sudah masuk SPP No. ' . $data->sppno . ', tanggal ' . apbd_fd($data->spptgl) . '</p>';
	}

	$form['spjid'] = array(
		'#type' => 'value',
		'#value' => $spjid,
	);			
		
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);			
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);			
	
	$form['spptgl_title'] = array(
		'#markup' => 'Tanggal',
		);
	$form['tanggal']= array(
		'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
		'#default_value' => $tanggal, 
			
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

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);		

	$form['keterangan'] = array(
		'#type' => 'item',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => $keterangan,
	);		

	//REKENING
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		'#title'=> 'REKENING<em class="text-info pull-right">' . apbd_fn($jumlah) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formrekening']['table']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">ANGGARAN</th><th width="50px">CAIR</th><th width="130px">BATAS</th><th width="130px">JUMLAH</th></tr>',
		 '#suffix' => '</table></div>',
	);	 
	
	$i = 0;
	$cair = 0;
	
	//drupal_set_message($dokid);
	$query = db_select('spjturekening', 'di');
	$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
	//$query->rightJoin('anggperkeg', 'a', 'di.kodero=a.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('di', array('jumlah'));
	//$query->fields('a', array('anggaran'));
	$query->condition('di.spjid', $spjid, '=');
	//$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	
	
	//dpq ($query);
	
	$results = $query->execute();

	foreach ($results as $data) {
		$i++; 
		$kodero = $data->kodero;
		$uraian = $data->uraian;
		$jumlah = $data->jumlah;
		
		$cair = apbd_readrealisasikegiatan_rekening($spjid, $kodekeg, $data->kodero, $tanggalsql);
		$batas = $data->anggaran - $cair;

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
			'#markup'=> '<p class="text-right">' . apbd_fn($batas) . '</p>', 
			'#suffix' => '</td>',
		); 		
		$form['formrekening']['table']['jumlahapbdhtml' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($jumlah) . '</p>', 
			'#suffix' => '</td></tr>',
		);			
		
	}	

	//SIMPAN
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Tambahkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}
	
function usulan_selectspj_main_form_submit($form, &$form_state) {
	$spjid = $form_state['values']['spjid'];
	
	$dokid = $form_state['values']['dokid'];
	$kodekeg = $form_state['values']['kodekeg'];

	//drupal_set_message($dokid);
	//drupal_set_message($kodekeg);
	
	$result = db_query('SELECT kodero, jumlah FROM spjturekening WHERE spjid =:spjid', array(':spjid' => $spjid));
	foreach($result AS $data){

		
		
		db_insert('dokumenrekening')
			->fields(array('dokid', 'kodero', 'jumlah', 'kodekeg', 'sumber', 'spjid'))
			->values(array(
					'dokid'=> $dokid,
					'kodero' => $data->kodero,
					'jumlah' => $data->jumlah,
					'kodekeg' => $kodekeg,
					'sumber' => 'usl',
					'spjid' => $spjid,
					))
			->execute();
		
		
	}
	
	
	//SPJGU
	$query = db_update('spjtu')
			->fields(
				array(
					'posting' => '1',
					'dokid' => $dokid,
					)
			);
	$query->condition('spjid', $spjid, '=');
	$res = $query->execute();			

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
	
	
	
	//gubaruspp/edit/0500073
	drupal_goto('tunihilbaruspp/edit/' . $dokid);
		

}


?>
