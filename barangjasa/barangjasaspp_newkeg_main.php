<?php
function barangjasaspp_newkeg_main($arg=NULL, $nama=NULL) {
	$limit = 10;
    
	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	

	$kodej = arg(2);
	if ($kodej=='') $kodej = 'ZZ';
	$kegiatandicari = arg(3);
	
	
	$output_form = drupal_get_form('barangjasaspp_newkeg_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px',  'field'=> 'anggaran', 'valign'=>'top'),
		array('data' => 'Cair', 'width' => '100px', 'valign'=>'top'),
		array('data' => 'Sisa', 'width' => '100px', 'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('kegiatanskpd', 'k')->distinct()->extend('PagerDefault')->extend('TableSort');
	if ($kodej!='ZZ') {
		$query->join('anggperkeg', 'a', 'k.kodekeg=a.kodekeg');
		$query->condition('a.kodero', db_like($kodej) . '%', 'LIKE');
	}
		$query->fields('k', array('kodekeg', 'kegiatan', 'anggaran'));
	
	//BL
	/*
	if ($kodeuk=='00') {
		//$query->condition('k.jenis', 1, '=');
		$query->condition('k.isppkd', 1, '=');
	} else
		$query->condition('k.jenis', 2, '=');
	*/
	$query->condition('k.inaktif', 0, '=');
	$query->condition('k.anggaran', 0, '>');
	
	$query->condition('k.kodeuk', $kodeuk, '=');
	if ($kegiatandicari!='') $query->condition('k.kegiatan', '%' . db_like($kegiatandicari) . '%', 'LIKE');
	
	$query->orderByHeader($header);
	$query->orderBy('k.kegiatan', 'ASC');
	$query->limit($limit);
	
	//drupal_set_message($query);
	
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		if (isjurnalsudahuk($data->kodekeg)) {
			$kegiatan = $data->kegiatan;
			$editlink = apbd_button_baru_custom_small('barangjasaspp/newrek/' . $data->kodekeg, 'SPP');
		} else {
			$kegiatan = $data->kegiatan . '<p style="color:red"><em><small>Ada SP2D yang belum divalidasi oleh Petugas Akuntansi (dijurnal) sehingga pengajuan SPP belum bisa dilakukan.</small></em></p>';				
			$editlink = 'SPP';
		}	
		
		$cair = apbd_readrealisasikegiatan($data->kodekeg, date('Y-m-d'));
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $kegiatan,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($cair), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran-$cair), 'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	//$output_form = drupal_get_form('barangjasaspm_arsip_main_form');
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $btn . $output . $btn;
	
}

function barangjasaspp_newkeg_main_form($form, &$form_state) {
	
	$kodeuk = apbd_getuseruk();
	
	$kodej = arg(2);
	if ($kodej=='') $kodej = 'ZZ';
	$kegiatandicari = arg(3);
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'CARI KEGIATAN' . '<em><small class="text-info pull-right">Klik untuk memilah kegiatan yang akan ditampilkan</small></em>',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	
	//JENIS
	$option_jenis['ZZ'] = 'SEMUA';
	$query = db_select('jenis', 'j');
	$query->fields('j', array('kodej', 'uraian'));
	
	if ($kodeuk=='00')
		$query->condition('j.kodek', '51', '=');
	else
		$query->condition('j.kodek', '52', '=');
	//$query->orderBy('j.kodej`', 'ASC');
	
	//dpq($query);
	
	$results = $query->execute();	
	foreach ($results as $data) {
		$option_jenis[$data->kodej] = $data->uraian;
	}
	$option_jenis['62'] = 'PEMBIAYAAN';
	$form['formdata']['kodej'] = array(
		'#type' => 'select',
		'#title' =>  t('Belanja'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_jenis,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kodej,
	);
	$form['formdata']['kegiatandicari'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kegiatandicari,
	);

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Cari',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}

function barangjasaspp_newkeg_main_form_submit($form, &$form_state) {
	$kodej = $form_state['values']['kodej'];
	$kegiatandicari = $form_state['values']['kegiatandicari'];
	

	
	$uri = 'barangjasaspp/newkeg/' . $kodej . '/' . $kegiatandicari;
	drupal_goto($uri);
	
}

?>
