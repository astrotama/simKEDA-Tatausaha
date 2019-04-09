<?php
function laporan_rearek_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(3);
				$kodeuk = arg(4);
				$tingkat = arg(5);
				$sumberdana = arg(6);
				$cetakpdf = arg(7);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$tingkat = '3';
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = 'ZZ';
		}
		$sumberdana = 'ZZ';
		
	}
	$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $sumberdana);
	if ($cetakpdf=='pdf') {
		apbd_ExportPDF_P($output, 10, "Realisasi per Rekening.pdf");
		//apbd_ExportPDF_P($htmlContent, $marginatas, $pdfFiel)
		//return $output;
		
	} else if($cetakpdf == 'excel'){

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Realisasi_per_Rekening.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $kodeuk, $tingkat, $sumberdana);
		$output_form = drupal_get_form('laporan_rearek_main_form');	
		
		$btn = l('Cetak', 'laporan/realisasirek/filter/' . $bulan . '/'. $kodeuk .'/'.$tingkat . '/' . $sumberdana . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp' .  l('Excel', 'laporan/realisasirek/filter/' . $bulan . '/'. $kodeuk .'/'.$tingkat . '/' . $sumberdana . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_rearek_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$sumberdana = $form_state['values']['sumberdana'];

	$uri = 'laporan/realisasirek/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat . '/' . $sumberdana;
	drupal_goto($uri);
	
}


function laporan_rearek_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = 'ZZ';
	}
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('n');
	$tingkat = '3';
	$sumberdana = 'ZZ';
	
	if(arg(3)!=null){
		
		$bulan = arg(3);
		$kodeuk = arg(4);
		$tingkat = arg(5);
		$sumberdana = arg(6);
		
	} 
	
	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= '|' . $data->namasingkat;
			}
		}	
	}
	
	$bulanstr = ($bulan==0? 'SETAHUN':$bulan);
		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulanstr . $namasingkat . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '0' => t('SETAHUN'), 	
			 '1' => t('JANUARI'), 	
			 '2' => t('FEBRUARI'),
			 '3' => t('MARET'),	
			 '4' => t('APRIL'),	
			 '5' => t('MEI'),	
			 '6' => t('JUNI'),	
			 '7' => t('JULI'),	
			 '8' => t('AGUSTUS'),	
			 '9' => t('SEPTEMBER'),	
			 '10' => t('OKTOBER'),	
			 '11' => t('NOVEMBER'),	
			 '12' => t('DESEMBER'),	
		   ),
	);
	
	//SKPD
	if (isUserSKPD()) {
		$form['formdata']['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);
		
	} else {
		
		$option_skpd['ZZ'] = 'SELURUH SKPD'; 
		$result = db_query('SELECT kodeuk, namasingkat FROM unitkerja ORDER BY kodedinas');	
		while($row = $result->fetchObject()){
			$option_skpd[$row->kodeuk] = $row->namasingkat; 
		}
		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
		);
	}
	
	$opttingkat = array();
	$opttingkat['3'] = 'Jenis';
	$opttingkat['4'] = 'Obyek';
	$opttingkat['5'] = 'Rincian';
	$form['formdata']['tingkat'] = array(
		'#type' => 'select',
		'#title' =>  t('Tingkat'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $opttingkat,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $tingkat,
	);

	$opt_sumberdana['ZZ'] ='SEMUA';
	$opt_sumberdana['BANPROV'] = 'BANPROV';
	$opt_sumberdana['BANTUAN KHUSUS'] = 'BANTUAN KHUSUS';	
	$opt_sumberdana['BOS'] = 'BOS';	
	$opt_sumberdana['DAK'] = 'DAK';	
	$opt_sumberdana['DAU'] = 'DAU';	
	$opt_sumberdana['DBH'] = 'DBH';	
	$opt_sumberdana['DBH CHT'] = 'DBH CHT';	
	$opt_sumberdana['DID'] = 'DID';	
	$opt_sumberdana['LAIN-LAIN PENDAPATAN'] = 'LAIN-LAIN PENDAPATAN';	
	$opt_sumberdana['PAD'] = 'PAD';	
	$form['formdata']['sumberdana'] = array(
		'#type' => 'select',
		'#title' =>  t('SUMBER DANA'),
		'#options' => $opt_sumberdana,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sumberdana,
	);	 
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan, $kodeuk, $tingkat, $sumberdana) {

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '50px', 'valign'=>'top'),
);
$rows = array();

//BELANJA
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	
	$realisasi = 0;
	$sql = db_select('dokumen', 'j');
	$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
	if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
	$sql->condition('j.sp2dok', '1', '='); 
	$sql->condition('j.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
	if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$sql = db_select('dokumen', 'j');
		$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
		$sql->condition('j.sp2dok', '1', '='); 
		$sql->condition('j.sp2dno', '', '<>'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}	
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasi = 0;
			$sql = db_select('dokumen', 'j');
			$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
			$sql->condition('j.sp2dok', '1', '='); 
			$sql->condition('j.sp2dno', '', '<>'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
				if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 				
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasi = 0; 
					$sql = db_select('dokumen', 'j');
					$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
					$sql->condition('j.sp2dok', '1', '='); 
					$sql->condition('j.sp2dno', '', '<>'); 
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = (is_null($data->realisasi)?0: $data->realisasi);
					}
					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi = 0;
							$sql = db_select('dokumen', 'j');
							$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
							$sql->condition('j.sp2dok', '1', '='); 
							$sql->condition('j.sp2dno', '', '<>'); 
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = (is_null($data->realisasi)?0: $data->realisasi);
							}
							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. $data_rek->uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

if ($kodeuk=='ZZ') {
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('k.kodek', '62', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$sql = db_select('dokumen', 'j');
		$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
		$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 		  
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
		$sql->condition('j.sp2dok', '1', '='); 
		$sql->condition('j.sp2dno', '', '<>'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}		

		$rows[] = array(
			array('data' => '<strong>6</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');		
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');		
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasi = 0;
			$sql = db_select('dokumen', 'j');
			$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');			
			$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 			 			
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
			$sql->condition('j.sp2dok', '1', '='); 
			$sql->condition('j.sp2dno', '', '<>'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');				
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');								
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasi = 0;
					$sql = db_select('dokumen', 'j');
					$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
					
					$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');					 					 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
					$sql->condition('j.sp2dok', '1', '='); 
					$sql->condition('j.sp2dno', '', '<>'); 
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = (is_null($data->realisasi)?0: $data->realisasi);
					}

					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						
						
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi = 0; 
							$sql = db_select('dokumen', 'j');
							$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
							$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
							$sql->condition('j.sp2dok', '1', '='); 
							$sql->condition('j.sp2dno', '', '<>'); 
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}

							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. $data_rek->uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	
	}	//foreach ($results as $datas)	
}

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $sumberdana) {

if ($tingkat>4) {
	set_time_limit(0);
	ini_set('memory_limit','940M');
}

if ($kodeuk == 'ZZ')
	$skpd = 'KABUPATEN JEPARA';
else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$rows[] = array(
	array('data' => 'LAPORAN REALISASI ANGGARAN (SP2D)', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($sumberdana!='ZZ') {
	$rows[] = array(
		array('data' => 'SUMBER DANA : ' . $sumberdana, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));



//$header = array();
$header[] = array (
	array('data' => 'Kode','width' => '45px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Uraian','width' => '285px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Anggaran', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Realisasi', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();


//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasi = 0;
	$sql = db_select('dokumen', 'j');
	$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
	if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
	$sql->condition('j.sp2dok', '1', '='); 
	$sql->condition('j.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
	if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$sql = db_select('dokumen', 'j');
		$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
		$sql->condition('j.sp2dok', '1', '='); 
		$sql->condition('j.sp2dno', '', '<>'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}	
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasi = 0;
			$sql = db_select('dokumen', 'j');
			$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
			$sql->condition('j.sp2dok', '1', '='); 
			$sql->condition('j.sp2dno', '', '<>'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasi = 0; 
					$sql = db_select('dokumen', 'j');
					$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
					$sql->condition('j.sp2dok', '1', '='); 
					$sql->condition('j.sp2dno', '', '<>'); 
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = (is_null($data->realisasi)?0: $data->realisasi);
					}
					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' =>apbd_fn($realisasi) , 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						if ($sumberdana!='ZZ') $query->condition('keg.sumberdana1', $sumberdana, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi = 0;
							$sql = db_select('dokumen', 'j');
							$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($sumberdana!='ZZ') $sql->condition('keg.sumberdana1', $sumberdana, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
							$sql->condition('j.sp2dok', '1', '='); 
							$sql->condition('j.sp2dno', '', '<>'); 
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = (is_null($data->realisasi)?0: $data->realisasi);
							}
							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

//PEMBIAYAAN
if ($kodeuk=='ZZ') {
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	
	
	$query->condition('k.kodek', '62', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$sql = db_select('dokumen', 'j');
		$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
		$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 		  
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
		$sql->condition('j.sp2dok', '1', '='); 
		$sql->condition('j.sp2dno', '', '<>'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}	

		$rows[] = array(
			array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);				

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');		
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');		
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasi = 0;
			$sql = db_select('dokumen', 'j');
			$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');			
			$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 			 			
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
			$sql->condition('j.sp2dok', '1', '='); 
			$sql->condition('j.sp2dno', '', '<>'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}	
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');				
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');								
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasi = 0;
					$sql = db_select('dokumen', 'j');
					$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
					
					$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');					 					 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
					$sql->condition('j.sp2dok', '1', '='); 
					$sql->condition('j.sp2dno', '', '<>'); 
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = (is_null($data->realisasi)?0: $data->realisasi);
					}	
					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' =>apbd_fn($realisasi) , 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						
						
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi = 0; 
							$sql = db_select('dokumen', 'j');
							$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
							$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <=:month', array('month' => $bulan));
							$sql->condition('j.sp2dok', '1', '='); 
							$sql->condition('j.sp2dno', '', '<>'); 
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}	
							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '285px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	
	}	//foreach ($results as $datas)	
}


$rows[] = array(
		array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



?>

