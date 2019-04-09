<?php
function usulan_new_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$kodekeg = arg(2);	
	$output_form = drupal_get_form('usulan_new_main_form');
	return drupal_render($output_form);// . $output;
	
}

function usulan_new_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	/*$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjguitemlastpage"] = $referer;
	else
		$referer = $_SESSION["spjguitemlastpage"];*/
	
	$kodekeg = arg(2);
	//drupal_set_message($kodekeg);
	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	

	
	$keperluan = '';
	$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach ($results as $data) {
		$keperluan = $data->kegiatan;
	}
	
	$no = 0;
	$tglawal = mktime(0,0,0,date('m'),date('d'),apbd_tahun());	
	$tglakhir = mktime(0,0,0,date('m'),date('d'),apbd_tahun());	
	$spptgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());	

	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);			
	
	$form['spptgl_title'] = array(
		'#markup' => 'Tanggal',
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
	
	
	$bendid = $_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg];
	
	$arr_bendid = explode(',',$_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]);
	$max = sizeof($arr_bendid);
	
	$or = db_or();
	for($i = 0; $i < $max;$i++)
	{
		$or->condition('bendid', $arr_bendid[$i], '=');
	}	
	
	
	$form['bendid'] = array(
		'#type' => 'value',
		'#value' => $bendid,
	);
	
	db_set_active('bendahara');

	$query = db_select('bendahara' . $kodeuk, 'b');

	$query->fields('b', array('tanggal'));	
	$query->condition($or);
	$query->orderBy('b.tanggal', 'ASC');
	$results = $query->execute();
	
	$tglawal = '2019-01-01'; $tglakhir = '2019-01-01';
	$i = 0;
	foreach ($results as $data) {
		if ($i==0) {
			$tglawal = $data->tanggal;
			$tglakhir = $data->tanggal;
		} else {
			$tglakhir = $data->tanggal;
		}	
		$i++;	
	}
	
		
	$form['formperiode'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PERIODE',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formperiode']['tglawal_title'] = array(
			'#markup' => 'Tanggal',
		);
		$form['formperiode']['tglawal']= array(
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
		$form['formperiode']['tglakhir_title'] = array(
			'#markup' => 'Sampai Tanggal',
		);
		$form['formperiode']['tglakhir']= array(
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
		
	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);	
	
	//REKENING	
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		//'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
		'#title'=> 'REKENING',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	
	$form['formrekening']['tablerekening']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="150px">JUMLAH</th></tr>',
		 '#suffix' => '</table>',
	);	

	$i = 0;

	$query = db_query('select bi.kodero as kr ,ro.uraian, sum(bi.jumlah) as jumlahnya from bendaharaitem' . $kodeuk . ' as bi inner join rincianobyek as ro on bi.kodero=ro.kodero where bi.bendid in (:bendid ) group by bi.kodero, ro.uraian order by bi.kodero', array(':bendid'=>$arr_bendid));
	
	$total = 0;
	foreach ($query as $data) {

		$i++; 
		$kode = $data->kr;
		$uraian = $data->uraian;
		$jumlah = $data->jumlahnya;
		$total += $data->jumlahnya;
		//$tidakada = $data->tidakada;
		
		$form['formrekening']['tablerekening']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		
		$form['formrekening']['tablerekening']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['tablerekening']['kode' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kode,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['tablerekening']['uraian' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		/*
		$form['formrekening']['tablerekening']['jumlahapbd' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#default_value'=> $jumlah, 
			'#suffix' => '</td>',
		);
		*/	
		$form['formrekening']['tablerekening']['jumlahapbd' . $i]= array(
			'#type'	=> 'value', 
			'#value'=> $jumlah, 
		);					
		$form['formrekening']['tablerekening']['jumlahapbdhtml' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($jumlah) . '</p>', 
			'#suffix' => '</td></tr>',
		);					
		
	}

	///TOTAL
	$form['formrekening']['tablerekening']['nomor_t']= array(
			'#prefix' => '<tr><td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formrekening']['tablerekening']['kode_t']= array(
			'#prefix' => '<td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formrekening']['tablerekening']['uraian_t']= array(
		'#prefix' => '<td>',
		'#markup'=> 'TOTAL', 
		'#suffix' => '</td>',
	); 
	/*
	$form['formrekening']['tablerekening']['jumlahapbd_t']= array(
		'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
		'#default_value'=> $jumlah, 
		'#suffix' => '</td>',
	);
	*/	
	$form['formrekening']['tablerekening']['jumlahapbdhtml_t']= array(
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">' . apbd_fn($total) . '</p>', 
		'#suffix' => '</td></tr>',
	);					
	
	$form['formrekening']['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
	$form['formrekening']['total']= array(
		'#type' => 'value',
		'#value' => $total,
	);	
	
	db_set_active();

	//SIMPAN
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function usulan_new_main_form_validate($form, &$form_state) {
	//$spjno = $form_state['values']['spjno'];
	//if (($spjno=='') or ($spjno=='BARU')) form_set_error('spjno', 'Nomor SPJ harap diisi dengan benar');
	
	//$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
	//if ($jumlahrekrekening>15) form_set_error('', 'Jumlah rekening dalam satu SPP maksimal adalah 15 rekening');
		
}
	
function usulan_new_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	$spptglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spptgl']);
	
	$tglawalsql = dateapi_convert_timestamp_to_datetime($form_state['values']['tglawal']);
	$tglakhirsql = dateapi_convert_timestamp_to_datetime($form_state['values']['tglakhir']);
	
	$bendid = $form_state['values']['bendid'];
	

	$keperluan = $form_state['values']['keperluan'];	
	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
	$total = $form_state['values']['total'];


	$arr_bendid = explode(',',$_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]);
	$max = sizeof($arr_bendid);
	
	$or = db_or();
	for($i = 0; $i < $max;$i++)
	{
		$or->condition('bendid', $arr_bendid[$i], '=');
	}	
	
		//SPJGU
	$query = db_insert('spjgu')
			->fields(array('kodeuk', 'kodekeg', 'tanggal', 'tglawal', 'tglakhir', 'keperluan', 'jumlah'))
			->values(
				array(
					'kodeuk'=> $kodeuk,
					'kodekeg' => $kodekeg,
					'tanggal' =>$spptglsql,
					'tglawal' =>$tglawalsql,
					'tglakhir' =>$tglakhirsql,
					'keperluan' => $keperluan, 
					'jumlah' => $total,
				)
			);
	//dpq $query;		
	//echo (string) $query;
	$spjid = $query->execute();
	
	//REKENING
	$totaljumlah = 0;
	for ($n=1; $n <= $jumlahrekrekening; $n++){
		$kodero = $form_state['values']['koderoapbd' . $n];
		$jumlah = $form_state['values']['jumlahapbd' . $n];
		
		db_insert('spjgurekening')
			->fields(array('spjid', 'kodero', 'jumlah'))
			->values(array(
					'spjid' => $spjid,
					'kodero' => $kodero,
					'jumlah' => $jumlah,
					))
			->execute();
	}
	
	
	db_set_active('bendahara');

	
	$query = db_update('bendahara' . $kodeuk) 
			->fields(array(
			  'sudahproses' => '1',
			  'dokid' => $spjid,
			   )
			 );
	$query->condition($or);
	$res = $query->execute();
	
	db_set_active();	
	
	
	drupal_goto('gubaru/edit/' . $spjid);
	
}


?>
