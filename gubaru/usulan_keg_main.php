<?php
function usulan_keg_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	
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
	//db_set_active('penatausahaan');

	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan',  'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'field'=> 'anggaran',  'valign'=>'top', 'align'=>'right'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	);

	//DB PENATAUSAHAAN
	
	$query = db_select('kegiatanskpd', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('kodekeg',  'kodeuk', 'kegiatan', 'anggaran'));
	
	$query->condition('d.kodeuk', $kodeuk, '=');
	if ($kodeuk != '00')  $query->condition('d.jenis', 2, '=');
	
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

		//$editlink = createlink('SPJ','newspj/' . $data->kodekeg);
		if (isjurnalsudahuk($data->kodekeg)) {
			$kegiatan = $data->kegiatan;
			$editlink = apbd_button_baru_custom('gubaru/selectspj/' . $data->kodekeg, 'SPP'); 
		} else {
			$kegiatan = $data->kegiatan . '<p style="color:red"><em><small>Ada SP2D yang belum divalidasi oleh Petugas Akuntansi (dijurnal) sehingga pengajuan SPP belum bisa dilakukan.</small></em></p>';			
			$editlink = 'SPP'; 
		}
		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			$editlink,
				
		);			
	}
	
	
    //$table=createTable($header,$rows);
	//$output_form = drupal_get_form('usulan_keg_main_form');
	//return drupal_render($output_form) . $table;

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	//$output = 'x';
	return $output;
	
}

?>
