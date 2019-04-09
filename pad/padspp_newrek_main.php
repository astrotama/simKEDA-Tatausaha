<?php
function padspp_newrek_main($arg=NULL, $nama=NULL) {
	$limit = 20;
    
	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '00';	

	
	//$output_form = drupal_get_form('padspp_newrek_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '90px',  'field'=> 'kodero', 'valign'=>'top'),
		array('data' => 'Uraian', 'field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('rincianobyek', 'k')->extend('PagerDefault')->extend('TableSort');
	$query->fields('k', array('kodero', 'uraian'));
	
	
	//$db_or = db_or();
	//$db_or->condition('k.kodero', db_like('411') . '%', 'LIKE');
	//$db_or->condition('k.kodero', db_like('412') . '%', 'LIKE');
	//$query->condition($db_or);
	
	
	$query->condition('k.kodero', db_like('41') . '%', 'LIKE');
	
	$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	$query->limit($limit);
	
	//dpq($query);
	
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
		
		$editlink = apbd_button_baru_custom_small('padspp/newpost/' . $data->kodero, 'SPP');
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->kodero,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $btn . $output . $btn;
	
}



?>
