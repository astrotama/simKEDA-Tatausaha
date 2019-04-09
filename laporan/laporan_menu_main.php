<?php

function laporan_menu_main($arg=NULL, $nama=NULL) {
    
	//drupal_set_message('Hai');

	$output_form = drupal_get_form('laporan_menu_main_form');
	return drupal_render($output_form);
	
}

function laporan_menu_main_form ($form, &$form_state) {
	
	$str_menu_all = '';
	$str_menu_rea_admin = '';
	
	if (isSuperuser()) {
		$str_menu_all = '<a href="cetakregister" class="list-group-item glyphicon glyphicon-th-large"> Register SP2D Keseluruhan (UP, GU, TU, Gaji, LS dan Nihil)</a>' . 
		'<a href="cetakregisterall" class="list-group-item glyphicon glyphicon-th-large"> Register SP2D Keseluruhan (Gaji, Rutin, Nihil dan PFK)</a>' .
		'<a href="cetakregisterkoreksi" class="list-group-item glyphicon glyphicon-th-large"> Register SP2D Koreksi</a>';
		
		$str_menu_rea_admin = '<a href="laporan/realisasisd" class="list-group-item glyphicon glyphicon-th-large"> Realisasi Belanja per Sumber Dana</a>' .
									'<a href="laporan/realisasiurusan" class="list-group-item glyphicon glyphicon-th-large"> Realisasi Belanja per Urusan</a>' .
									'<a href="laporan/realisasiuk" class="list-group-item glyphicon glyphicon-th-large"> Realisasi Belanja per SKPD</a>' . 
									'<a href="laporan/realisasisp2d" class="list-group-item glyphicon glyphicon-th-large"> Realisasi Belanja per Jenis Rekening per Bulan</a>';
	
	}
	
	
			$form['itemregister']= array(
				'#type'     => 'item', 
				'#title' => 'Laporan Register',
				'#markup'	=> '<div class="list-group">' .
									'<a href="cetakregisterspp" class="list-group-item glyphicon glyphicon-th-large"> Register SPP</a>' .
									'<a href="cetakregisterspm" class="list-group-item glyphicon glyphicon-th-large"> Register SPM</a>' .
									'<a href="cetakregisterskpd" class="list-group-item glyphicon glyphicon-th-large"> Register SP2D</a>' .
									  $str_menu_all .
									'<a href="rekapregisterspp" class="list-group-item glyphicon glyphicon-th-large"> Rekap Register SPP</a>' .
									'<a href="rekapregisterspm" class="list-group-item glyphicon glyphicon-th-large"> Rekap Register SPM</a>' .
									'<a href="rekapregister" class="list-group-item glyphicon glyphicon-th-large"> Rekap Register SP2D</a>'  .
									'<a href="cetakregisterpajak" class="list-group-item glyphicon glyphicon-th-large"> Rekap Register Pajak</a>'  .
							'</div>',
			);
			
			$form['itemrealisasi']= array(
				'#type'     => 'item', 
				'#title' => 'Laporan Realisasi Belanja',
				'#markup'	=> '<div class="list-group">' .
									'<a href="laporan/realisasirek" class="list-group-item glyphicon glyphicon-th-large"> Realisasi Belanja per Rekening</a>' . 
									'<div class="list-group">' . $str_menu_rea_admin .
									'<a href="laporan/realisasikeg" class="list-group-item glyphicon glyphicon-th-large"> Realisasi Kegiatan</a>'  .
									'<a href="laporan/laporan_daftar" class="list-group-item glyphicon glyphicon-th-large"> Laporan Daftar Tansaksi DTH</a>' . 
									'<a href="laporan/laporan_rekapitulasi" class="list-group-item glyphicon glyphicon-th-large"> Laporan Rekapitulasi Transaksi RTH </a>' . 
									'<a href="laporan/laporan2_main" class="list-group-item glyphicon glyphicon-th-large"> Laporan 2</a>' . 
							'</div>', 
			);
			


	return $form;
}



function _load_item($kodeuk) {

	$_SESSION["laporan_kas_kodeuk"] = $kodeuk;

	$reportitem	= '<div class="list-group">' .
						'<a href="/laporanbk0/' . $kodeuk . '" class="list-group-item glyphicon glyphicon-th-large"> BK-0 (Buku Kas Pembantu)</a>' .
						'<a href="/laporanbk1/' . $kodeuk . '" class="list-group-item glyphicon glyphicon-th-large"> BK-1 (Buku Kas Umum)</a>' .
						'<a href="/laporanbk2/' . $kodeuk . '" class="list-group-item glyphicon glyphicon-th-large"> BK-2 (Buku Panjar)</a>' .
						'<a href="/laporanbk3/' . $kodeuk . '" class="list-group-item glyphicon glyphicon-th-large"> BK-3 (Buku Pajak)</a>' .
						'<a href="/laporanbk5/' . $kodeuk . '" class="list-group-item glyphicon glyphicon-th-large"> BK-5 (Kartu Kendali Kegiatan)</a>' .
						'<a href="/laporanbk6/' . $kodeuk . '" class="list-group-item glyphicon glyphicon-th-large"> BK-6 (Rekap Pengeluaran per Kegiatan)</a>' .
						'<a href="/laporanbk8/' . $kodeuk . '/7" class="list-group-item glyphicon glyphicon-th-large"> BK-7 (SPJ Administratif)</a>' .
						'<a href="/laporanbk8/' . $kodeuk . '/8" class="list-group-item glyphicon glyphicon-th-large"> BK-8 (SPJ Fungsional)</a>' .
						'<a href="/laporanbk8p/' . $kodeuk . '/" class="list-group-item glyphicon glyphicon-th-large"> BK-8 (SPJ Bendahara Pembantu)</a>' .
				'</div>'; 

	return $reportitem;
}

?>