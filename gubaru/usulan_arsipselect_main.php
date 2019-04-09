<?php
function usulan_arsipselect_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
	
	$dokid = arg(2);
	
	$kodeuk = apbd_getuseruk();
	
	//drupal_set_message(arg(2));
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Tanggal','width' => '90px',  'field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kodekeg',  'valign'=>'top'),
		array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah', 'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	);

	$no=0;

	$query = db_select('spjgu', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');

	# get the desired fields from the database
	$query->fields('d', array('spjid', 'tanggal', 'kodekeg', 'jumlah', 'keperluan'));
	$query->fields('k', array('kegiatan'));
	
	$query->condition('k.kodeuk', $kodeuk, '=');
	$query->condition('d.posting', '0', '=');
	
	$query->orderByHeader($header);
	$query->orderBy('d.tanggal', 'ASC');
	$query->limit($limit);
		
	//dpq($query);
	
	# execute the query
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
		//$editlink = apbd_button_jurnal('gubaru/edit/' . $data->spjid);
		//gubaru/edit/16
		
		$editlink = '';
		if (issudahmasuk($dokid, $data->kodekeg)) {
			$kegiatan = $data->kegiatan . '<p style="color:red"><em><small>Kegiatan dari usulan ini sudah masuk dalam pengajuan SPP.</small></em></p>';			
		
		} else {
			$kegiatan = $data->kegiatan;
			$editlink = apbd_button_baru_lanjut('gubaru/selectspj/' . $data->spjid . '/' . $dokid , 'Pilih'); 
		}
		
		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fd($data->tanggal),'align' => 'right', 'valign'=>'top'),
			array('data' => $kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
			$editlink,
		);
	}
	
	
	//BUTTON
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return $output;
	
	
}


?>
