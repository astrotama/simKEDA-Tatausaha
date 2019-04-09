<?php
function tunihilbaruspp_pilihkeg_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	$dokid = arg(2);
	$kodeuk = apbd_getuseruk();
	
	//drupal_set_message($kodeuk);
	//drupal_set_message($dokid);
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'valign'=>'top', 'align'=>'right'),
		array('data' => 'Outstanding', 'width' => '100px', 'valign'=>'top', 'align'=>'right'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	);

	db_set_active('bendahara');
	 
	$res = db_query('select kodekeg, kodeuk, kegiatan, anggaran, total, jumlahspj from {q_kegiatan_outstandingtu' . $kodeuk . '} where kodeuk=:kodeuk order by kegiatan', array(':kodeuk'=>$kodeuk));
	
	$results = $res->fetchAllAssoc('kodekeg');
	
	db_set_active();
	
	$rows = array();
	$no = 0;

	foreach ($results as $data) {
		$no++;  
		
		//drupal_set_message($dokid . '|' . $data->kodekeg);
		
		$editlink = ''; 
		
		if (issudahmasuk($dokid, $data->kodekeg)) {

			$kegiatan = $data->kegiatan . '<p style="color:red"><em><small>Sudah masuk dalam pengajuan SPP ini.</small></em></p>';			
			
		} else {	
			if (isjurnalsudahuk($data->kodekeg)) {
				$kegiatan = $data->kegiatan;
				$editlink = apbd_button_baru_lanjut('tunihilbaruspp/pilihkegspj/' . $dokid . '/' . $data->kodekeg, 'Pilih'); 
			} else {
				$kegiatan = $data->kegiatan . '<p style="color:red"><em><small>Ada SP2D yang belum divalidasi oleh Petugas Akuntansi (dijurnal) sehingga pengajuan SPP belum bisa dilakukan.</small></em></p>';			
			}
		}
		
		
		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data->jumlahspj),'align' => 'right', 'valign'=>'top'),
			$editlink,
				
		);			
	}
	
	
    //$output = createT($header,$rows);
	//$output_form = drupal_get_form('gubaruspp_pilihkeg_main_form');
	//return drupal_render($output_form) . $table;

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output .= theme('pager');
	
	//$output = 'x';
	return $output;
	
}


?>
