<?php
function gunihilspp_newkeg_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
	$kodeuk = apbd_getuseruk();
	if ($arg) {
		switch($arg) {
			case 'filter':
			
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		
		
	}
	
	//drupal_set_message('2 . ' . arg(1));
	//drupal_set_message('3 . ' . arg(2));
	//drupal_set_message('4 . ' . arg(3));
	
	$kodesuk = arg(2);
	//drupal_set_message($kodesuk);
	
	if ($kodesuk=='') $kodesuk = 'ZZ';
	
	if ($kodesuk=='ZZ')
		$str_suk = '| <strong>SEMUA</strong> |';
	else
		$str_suk = '| ' . '<a href="/gunihilspp/newkeg/ZZ">SEMUA</a>' . ' |';
	
	$query = db_query('SELECT kodesuk,namasuk FROM `subunitkerja` WHERE kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($query as $data) {
		
		$namasuk_menu = str_replace('BIDANG ', '', strtoupper($data->namasuk));
		$namasuk_menu = str_replace('BAGIAN ', '', $namasuk_menu);
		
		if ($kodesuk==$data->kodesuk)
			$str_suk .= apbd_blank_space() . '<strong>' . $namasuk_menu . '</strong> |';
		else
			$str_suk .= apbd_blank_space() . '<a href="/gunihilspp/newkeg/' . $data->kodesuk . '">' . $namasuk_menu . '</a>' . ' |';
		
	}
	
	$menu  = '<p align="center">' . $str_suk . '</p>';
	
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan',  'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'field'=> 'anggaran',  'valign'=>'top', 'align'=>'right'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	);

	//DB PENATAUSAHAAN
	$limit = 15;
	
	$query = db_select('kegiatanskpd', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('kodekeg',  'kodeuk', 'kegiatan', 'anggaran'));
	
	$query->condition('d.kodeuk', $kodeuk, '=');
	if ($kodeuk!='00') $query->condition('d.jenis', 2, '=');
	if ($kodesuk!='ZZ') $query->condition('d.kodesuk', $kodesuk, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('d.kegiatan', 'ASC');
	$query->limit($limit);
	
	//$query->range(0,10);
	
	
	# execute the query
	$results = $query->execute();
		
	$rows = array();
	$no = 0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 	
	foreach ($results as $data) {
		$no++;  
		
		if ($data->anggaran=='0')
			$editlink = '';
		else
			$editlink = apbd_button_baru_custom('gunihilspp/newspj/' . $data->kodekeg, 'SPP'); 
		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $data->kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			$editlink,
				
		);			
	}
	
	
    //$table=createTable($header,$rows);
	//$output_form = drupal_get_form('gunihilspp_newkeg_main_form');
	//return drupal_render($output_form) . $table;

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	return $menu .  $output . $menu;
	
}


function gunihilspp_newkeg_main_form_submit($form, &$form_state) {
	/*
	$bulan = $form_state['values']['bulan'];
	$spjsudah = $form_state['values']['spjsudah'];
	$jenisgaji = $form_state['values']['jenisgaji'];
	
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	

	$_SESSION["gunihilspp_newkeg_bulan"] = $bulan;
	$_SESSION["gunihilspp_newkeg_spjsudah"] = $spjsudah;
	$_SESSION["gunihilspp_newkeg_jenisgaji"] = $jenisgaji;

	$uri = 'spjgu/baru/filter/' . $bulan . '/' . $spjsudah . '/' . $jenisgaji;
	drupal_goto($uri);
	*/
	
}


function gunihilspp_newkeg_main_form($form, &$form_state) {
	/*
	$kodeuk = apbd_getuseruk();
	if(arg(2)!=null){
		
		$bulan=arg(2);
		$spjsudah = arg(3);
		$jenisgaji = arg(4);

	} else {

		//$bulan = date('m');
		isset($_SESSION["gunihilspp_newkeg_bulan"])?$bulan = $_SESSION["gunihilspp_newkeg_bulan"]:$bulan = date('m');;
		isset($_SESSION["gunihilspp_newkeg_spjsudah"])?$spjsudah = $_SESSION["gunihilspp_newkeg_bulan"]:$spjsudah = 'ZZ';
		isset($_SESSION["gunihilspp_newkeg_jenisgaji"])?$jenisgaji = $_SESSION["gunihilspp_newkeg_jenisgaji"]:$jenisgaji = 'ZZ';
		
		
		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $jenisgaji, $spjsudah) . '</small></em>',		//'#attributes' => array('class' => array('container-inline')),
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	
	//SKPD
	$form['formdata']['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	
	//BULAN
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	//JENIS GAJI
	$opt_gaji['ZZ'] = 'SEMUA';
	$opt_gaji['0'] = 'REGULER';
	$opt_gaji['1'] = 'KEKURANGAN';	
	$opt_gaji['2'] = 'SUSULAN';	
	$opt_gaji['3'] = 'TERUSAN';	
	$form['formdata']['jenisgaji'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Gaji'),
		'#options' => $opt_gaji,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisgaji,
	);	 
	

	$opt_sp2d['ZZ'] ='SEMUA';
	$opt_sp2d['0'] = 'BELUM DICATAT';
	$opt_sp2d['1'] = 'SUDAH DICATAT';	
	//$opt_sp2d['2'] = 'SUDAH VALIDASI';	
	$form['formdata']['spjsudah'] = array(
		'#type' => 'select',
		'#title' =>  t('DIbukukan'),
		'#options' => $opt_sp2d,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $spjsudah,
	);	 

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
	*/
}


function get_label_data($bulan, $jenisgaji, $status) {
if ($bulan=='0')
	$label = 'Setahun';
else 
	$label = 'Bulan ' . apbd_get_namabulan($bulan);

if ($jenisgaji=='ZZ')
	$label .= '/Semua gaji';
else if ($jenisgaji=='0')	
	$label .= '/Gaji reguler';
else if ($jenisgaji=='1')	
	$label .= '/Kekurangan gaji';
else if ($jenisgaji=='2')
	$label .= '/Gaji susulan';
else if ($jenisgaji=='3')	
	$label .= '/Gaji terusan';

if ($status=='ZZ')
	$label .= '/Semua SP2D';
else if ($status=='0')	
	$label .= '/Belum verifikasi';
else if ($status=='1')	
	$label .= '/Sudah verifikasi';

$label .= ' (Klik disini untuk mengganti pilihan data)';
return $label;
}


?>
