<?php
function gubaruspp_pilihkeg_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	$dokid = arg(2);
	$kodesuk = arg(3);
	
	$kodeuk = apbd_getuseruk();
	if ($kodesuk=='') $kodesuk = '0000';
	
	$numbid = 0;
	if ($kodesuk=='0000')
		$str_suk = '| <strong>SEMUA</strong> |';
	else
		$str_suk = '| ' . '<a href="/gubaruspp/pilihkeg/' . $dokid . '/0000">SEMUA</a>' . ' |';
	
	$query = db_query('SELECT kodesuk,namasuk FROM subunitkerja WHERE kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($query as $data) {
		
		$numbid++;
		
		$namasuk_menu = str_replace('BIDANG ', '', strtoupper($data->namasuk));
		$namasuk_menu = str_replace('BAGIAN ', '', $namasuk_menu);
		
		if ($kodesuk==$data->kodesuk)
			$str_suk .= apbd_blank_space() . '<strong>' . $namasuk_menu . '</strong> |';
		else
			$str_suk .= apbd_blank_space() . '<a href="/gubaruspp/pilihkeg/' . $dokid . '/' . $data->kodesuk . '">' . $namasuk_menu . '</a>' . ' |';
		
	}
	
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'valign'=>'top', 'align'=>'right'),
		array('data' => 'Outstanding', 'width' => '100px', 'valign'=>'top', 'align'=>'right'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	);

	db_set_active('bendahara');
	
	if ($kodesuk=='0000')
		$res = db_query('select kodekeg, kodeuk, kegiatan, anggaran, total, jumlahspj from {q_kegiatan_outstanding' . $kodeuk . '} order by kegiatan');
	else
		$res = db_query('select kodekeg, kodeuk, kegiatan, anggaran, total, jumlahspj from {q_kegiatan_outstanding' . $kodeuk . '} where kodesuk=:kodesuk order by kegiatan', array(':kodesuk'=>$kodesuk));
	
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
				$editlink = apbd_button_baru_lanjut('gubaruspp/pilihkegspj/' . $dokid . '/' . $data->kodekeg, 'Pilih'); 
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
	
	if ($no==0) {
		$rows[] = array(
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
			array('data' => 'Data kosong, belum ada kegiatan yang sudah SPJ','align' => 'left', 'valign'=>'top'),
			array('data' => '','align' => 'right', 'valign'=>'top'),
			array('data' => '','align' => 'right', 'valign'=>'top'),
			'',
				
		);			 
	}	
	
	if ($numbid>1)
		$menu = '<p align="center">' . $str_suk . '</p>';
	else
		$menu = '';
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output .= theme('pager');
	
	//$output = 'x';
	return $menu . $output;
	
}


?>
