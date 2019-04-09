<?php
function laporan_reakeg_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	
	//drupal_set_message('x');
	
	$qlike=''; 
	$limit = 10;
    $cetakpdf = '';
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(3);
				$kodeuk = arg(4);
				$sumberdana = arg(5);
				$cetakpdf = arg(6);
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
			$kodeuk = '81';
		}
		$sumberdana = 'ZZ';
		
	}
	
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($bulan, $kodeuk, $sumberdana);	
		apbd_ExportPDF_P($output, 10, "Daftar Realisasi Kegiatan.pdf");
		//apbd_ExportPDF_P($htmlContent, $marginatas, $pdfFiel)
		//return $output;
		
	} else if($cetakpdf == 'excel'){

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Daftar_Realisasi_Kegiatan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
		
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $kodeuk,  $sumberdana);
		$output_form = drupal_get_form('laporan_reakeg_main_form');	
		
		$btn = l('Cetak', 'laporan/realisasikeg/filter/' . $bulan . '/'. $kodeuk . '/' . $sumberdana . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp' . l('Excel', 'laporan/realisasikeg/filter/' . $bulan . '/'. $kodeuk . '/' . $sumberdana . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_reakeg_main_form_validate($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$sumberdana = $form_state['values']['sumberdana'];

	if (($kodeuk=='ZZ')and ($sumberdana=='ZZ')) {
		form_set_error('kodeuk', 'Tidak bisa menampilkan seluruh SKPD untuk semua sumberdana, salah satu pilihan harus spesifik');		
	}	
}

function laporan_reakeg_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$sumberdana = $form_state['values']['sumberdana'];

	$uri = 'laporan/realisasikeg/filter/' . $bulan . '/'. $kodeuk . '/' . $sumberdana;
	drupal_goto($uri);
	
}


function laporan_reakeg_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = '81';
	}
	$bulan = date('n');
	$tingkat = '3';
	$sumberdana = 'ZZ';
	
	if(arg(3)!=null){
		
		$bulan = arg(3);
		$kodeuk = arg(4);
		$sumberdana = arg(5);
		
	}
	
	$query = db_select('unitkerja', 'p');
	$query->fields('p', array('namasingkat','kodeuk'))
		  ->condition('kodeuk',$kodeuk,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$namasingkat= '|' . $data->namasingkat;
		}
	}	
	
	$opt_bulan = array(	
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
		   );
		   
	//SKPD
	if (isUserSKPD()) {
		$form['formdata']['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);
	  $namasingkat = '';
	} else {
		$result = db_query('SELECT kodeuk, namasingkat FROM unitkerja ORDER BY kodedinas');	
		$option_skpd['ZZ'] = 'SELURUH OPD'; 
		while($row = $result->fetchObject()){
			$option_skpd[$row->kodeuk] = $row->namasingkat; 
		}
		
		$namasingkat = '|' . $option_skpd[$kodeuk];
	}
	
	$results = db_query('SELECT sumberdana from {sumberdana} order by sumberdana');
	$opt_sumberdana['ZZ'] ='SEMUA SUMBER DANA';
	while($row = $results->fetchObject()){
		$opt_sumberdana[$row->sumberdana] = $row->sumberdana; 
	}

		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $opt_bulan[$bulan] . $namasingkat . '|' . $opt_sumberdana[$sumberdana] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => $opt_bulan,
	);
	
	
		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
		);
	

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

function gen_report_realisasi_resume($bulan, $kodeuk, $sumberdana) {

//TABEL
$header = array (
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'Kode', 'valign'=>'top'),
	array('data' => 'Kegiatan', 'valign'=>'top'),
	array('data' => 'Sumberdana', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 's/d. ' . label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '50px', 'valign'=>'top'),
);
$rows = array();

if ($sumberdana=='ZZ')
	$results = db_query('SELECT kodekeg, kegiatan, total,totalsesudah, sumberdana1 from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk order by kegiatan', array(':kodeuk'=>$kodeuk));
else
	$results = db_query('SELECT kodekeg, kegiatan, total,totalsesudah, sumberdana1 from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk and sumberdana1=:sumberdana order by kegiatan', array(':kodeuk'=>$kodeuk, ':sumberdana'=>$sumberdana));

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

foreach ($results as $datas) {
	
	$realisasi = 0; $lalu = 0; $ini = 0;
	/*
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('dr.kodekeg', $datas->kodekeg, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) < :month', array('month' => $bulan));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$lalu = $data->realisasi;
	}
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('dr.kodekeg', $datas->kodekeg, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) = :month', array('month' => $bulan));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$ini = $data->realisasi;
	}
	*/
	
	$realisasi = $datas->totalsesudah;		// $lalu + $ini;	
	$anggaran_t += $datas->total;
	$realisasi_t += $realisasi;
	$lalu_t += $lalu;
	$ini_t += $ini;
	
	if ($realisasi==0)
		$kegiatan =  $datas->kegiatan;
	else
		$kegiatan =  l($datas->kegiatan, 'laporan/realisasikegsp2d/filter/' . $datas->kodekeg, array('attributes' => array('class' => null)));
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => substr($datas->kodekeg, -6), 'align' => 'left', 'valign'=>'top'),
		array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->sumberdana1, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->total), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'align' => 'right', 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)
	$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($anggaran_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn1(apbd_hitungpersen($anggaran_t, $realisasi_t)) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi($bulan, $kodeuk, $sumberdana) {

//TABEL
$header = array ( 
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'Kode', 'valign'=>'top'),
	array('data' => 'Kegiatan', 'valign'=>'top'),
	array('data' => 'Sumberdana', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '50px', 'valign'=>'top'),
);
$rows = array();


 
$sql = db_select('kegiatanskpd', 'k');
$sql->fields('k', array('kodekeg', 'kegiatan', 'total', 'totalsesudah', 'sumberdana1'));
$sql->condition('k.inaktif', '0', '='); 
$sql->condition('k.total', '0', '>'); 

//if ($kodeuk !='ZZ') $sql->condition('k.kodeuk', $kodeuk, '='); 
//if ($sumberdana !='ZZ') $sql->condition('k.sumberdana1', $sumberdana, '='); 

if ($kodeuk !='ZZ') $sql->condition('k.kodeuk', $kodeuk, '='); 
if ($sumberdana !='ZZ') $sql->condition('k.sumberdana1', $sumberdana, '='); 

//$sql->orderBy('k.jenis');
//$sql->orderBy('k.kegiatan'); 

//dpq($sql);

$results = $sql->execute();

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

foreach ($results as $datas) {
	
	$realisasi = 0; 
	
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('dr.kodekeg', $datas->kodekeg, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <= :month', array('month' => $bulan));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}

	
	
	$anggaran_t += $datas->total;
	$realisasi_t += $realisasi;
	
	if ($realisasi==0)
		$kegiatan =  $datas->kegiatan;
	else
		$kegiatan =  l($datas->kegiatan, 'laporan/realisasikegsp2d/filter/' . $datas->kodekeg, array('attributes' => array('class' => null)));
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => substr($datas->kodekeg, -6), 'align' => 'left', 'valign'=>'top'),
		array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->sumberdana1, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->total), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'align' => 'right', 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)
	$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($anggaran_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn1(apbd_hitungpersen($anggaran_t, $realisasi_t)) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_resume($bulan, $kodeuk, $sumberdana) {

set_time_limit(0);
ini_set('memory_limit','940M');


$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$skpd = $datas->namauk;
};

$rows[] = array(
	array('data' => 'LAPORAN REALISASI KEGIATAN (SP2D)', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
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
	array('data' => 'No','width' => '20px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Kode','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Kegiatan','width' => '290px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Anggaran', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Realisasi', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();


if ($sumberdana=='ZZ')
	$results = db_query('SELECT kodekeg, kegiatan, total, totalsesudah from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk order by kegiatan', array(':kodeuk'=>$kodeuk));
else
	$results = db_query('SELECT kodekeg, kegiatan, total, totalsesudah from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk and sumberdana1=:sumberdana order by kegiatan', array(':kodeuk'=>$kodeuk, ':sumberdana'=>$sumberdana));

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

foreach ($results as $datas) {
	
	$realisasi = 0; 

	
	$realisasi = $datas->totalsesudah;		//$lalu + $ini;
	
	$anggaran_t += $datas->total;
	$realisasi_t += $realisasi;
	
	$n++;
	$rows[] = array(
		array('data' => $n . '.', 'width' => '20px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => substr($datas->kodekeg, -6), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->kegiatan, 'width' => '290px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->total), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => null, 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '290px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($anggaran_t) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn1(apbd_hitungpersen($anggaran_t, $realisasi_t)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);

$rows[] = array(
		array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($bulan, $kodeuk, $sumberdana) {

set_time_limit(0);
ini_set('memory_limit','940M');


$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$skpd = $datas->namauk;
};

$rows[] = array(
	array('data' => 'LAPORAN REALISASI KEGIATAN (SP2D)', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
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
	array('data' => 'No','width' => '20px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Kode','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Kegiatan','width' => '310px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Anggaran', 'width' => '60px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_ini($bulan), 'width' => '60px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();


$sql = db_select('kegiatanskpd', 'k');
$sql->fields('k', array('kodekeg', 'kegiatan', 'total', 'totalsesudah', 'sumberdana1'));
$sql->condition('k.inaktif', '0', '='); 
$sql->condition('k.total', '0', '>'); 

//if ($kodeuk !='ZZ') $sql->condition('k.kodeuk', $kodeuk, '='); 
//if ($sumberdana !='ZZ') $sql->condition('k.sumberdana1', $sumberdana, '='); 

if ($kodeuk !='ZZ') $sql->condition('k.kodeuk', $kodeuk, '='); 
if ($sumberdana !='ZZ') $sql->condition('k.sumberdana1', $sumberdana, '='); 

//$sql->orderBy('k.jenis');
//$sql->orderBy('k.kegiatan'); 

//dpq($sql);

$results = $sql->execute();

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

foreach ($results as $datas) {
	
	$realisasi = 0;
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('dr.kodekeg', $datas->kodekeg, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <= :month', array('month' => $bulan));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$lalu_t += $lalu;
	$ini_t += $ini;
	
	$anggaran_t += $datas->total;
	$realisasi_t += $realisasi;
	
	$n++;
	$rows[] = array(
		array('data' => $n . '.', 'width' => '20px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => substr($datas->kodekeg, -6), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->kegiatan, 'width' => '310px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->total), 'width' => '60px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '60px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '340px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($anggaran_t) . '</strong>', 'width' => '60px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t) . '</strong>', 'width' => '60px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn1(apbd_hitungpersen($anggaran_t, $realisasi_t)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);

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