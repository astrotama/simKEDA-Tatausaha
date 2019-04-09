<?php
function laporan_reauk_main($arg=NULL, $nama=NULL) {
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
				$sumberdana = arg(4);
				$jenis = arg(5);
				$kodeu = arg(6);
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
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = '81';
		}
		$sumberdana = 'ZZ';
		$jenis = 'ZZ';
		$kodeu = 'ZZ';
		
	}
	
	//drupal_set_message($cetakpdf);
	$output = gen_report_realisasi_print($bulan, $sumberdana, $jenis, $kodeu);
	if ($cetakpdf=='pdf') {
		
		apbd_ExportPDF_P($output, 10, "Realisasi per SKPD.pdf");
		//apbd_ExportPDF_P($htmlContent, $marginatas, $pdfFiel)
		//return $output;
		
	} else if($cetakpdf == 'excel'){

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Realisasi_per_SKPD.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $sumberdana, $jenis, $kodeu);
		$output_form = drupal_get_form('laporan_reauk_main_form');	
		
		//$uri = 'laporan/realisasiuk/filter/' . $bulan . '/'. $sumberdana . '/' . $jenis . '/' . $kodeu;
		$btn = l('Cetak', 'laporan/realisasiuk/filter/' . $bulan . '/'. $sumberdana . '/' . $jenis . '/' . $kodeu . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp' .  l('Excel', 'laporan/realisasiuk/filter/' . $bulan . '/'. $sumberdana . '/' . $jenis . '/' . $kodeu . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_reauk_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$sumberdana = $form_state['values']['sumberdana'];
	$jenis = $form_state['values']['jenis'];
	$kodeu = $form_state['values']['kodeu'];

	$uri = 'laporan/realisasiuk/filter/' . $bulan . '/'. $sumberdana . '/' . $jenis . '/' . $kodeu;
	drupal_goto($uri);
	
}


function laporan_reauk_main_form($form, &$form_state) {
	
	$bulan = date('n');
	$sumberdana = 'ZZ';
	$jenis = 'ZZ';
	$kodeu = 'ZZ';
	
	if(arg(3)!=null){
		
		$bulan = arg(3);
		$sumberdana = arg(4);
		$jenis = arg(5);
		$kodeu = arg(6);
		
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
		   
	$opt_sumberdana['ZZ'] ='SEMUA SUMBER DANA';
	$opt_sumberdana['BANPROV'] = 'BANPROV';
	$opt_sumberdana['BANTUAN KHUSUS'] = 'BANTUAN KHUSUS';	
	$opt_sumberdana['BOS'] = 'BOS';	
	$opt_sumberdana['DAK'] = 'DAK';	
	$opt_sumberdana['DAU'] = 'DAU';	
	$opt_sumberdana['DBH'] = 'DBH';	
	$opt_sumberdana['DBH CHT'] = 'DBH CHT';	
	$opt_sumberdana['LAIN-LAIN PENDAPATAN'] = 'LAIN-LAIN PENDAPATAN';	
	$opt_sumberdana['PAD'] = 'PAD';	
	
	$opt_jenis['ZZ'] ='SEMUA JENIS BELANJA';
	$opt_jenis['1'] = 'BELANJA TIDAK LANGSUNG';
	$opt_jenis['2'] = 'BELANJA LANGSUNG';	
	
	$opt_urusan['ZZ'] ='SEMUA URUSAN';
	$res = db_query('SELECT kodeu,urusan from {urusan} order by kodeu');
	foreach ($res as $data) {
		$opt_urusan[$data->kodeu] = $data->urusan;
	}
	
		$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $opt_bulan[$bulan] . '|'. $opt_sumberdana[$sumberdana] . '|' . $opt_jenis[$jenis] . '|' . $opt_urusan[$kodeu] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
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
	
	
	
	$form['formdata']['sumberdana'] = array(
		'#type' => 'select',
		'#title' =>  t('SUMBER DANA'),
		'#options' => $opt_sumberdana,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sumberdana,
	);	 
	
	$form['formdata']['jenis'] = array(
		'#type' => 'select',
		'#title' =>  t('JENIS BELANJA'),
		'#options' => $opt_jenis,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenis,
	);	 	
	
	
	$form['formdata']['kodeu'] = array(
		'#type' => 'select',
		'#title' =>  t('URUSAN'),
		'#options' => $opt_urusan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kodeu,
	);	 	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan, $sumberdana, $jenis, $kodeu) {

//TABEL
$header = array (
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '50px', 'valign'=>'top'),
);
$rows = array();

if ($kodeu=='ZZ') {
	if ($sumberdana=='ZZ')
		$results = db_query('SELECT kodeuk, namasingkat from {unitkerja} order by kodedinas');
	else
		$results = db_query('SELECT kodeuk, namasingkat from {unitkerja} where kodeuk in (select kodeuk from {kegiatanskpd} where sumberdana1=:sumberdana and inaktif=0 and total>0) order by kodedinas', array(':sumberdana'=>$sumberdana));
	
} else {
	if ($sumberdana=='ZZ')
		$results = db_query('SELECT kodeuk, namasingkat from {unitkerja} where kodeuk in (select kodeuk from {kegiatanskpd} inner join {program} on {kegiatanskpd}.kodepro={program}.kodepro where {program}.kodeu=:kodeu and inaktif=0 and total>0) order by kodedinas', array(':kodeu'=>$kodeu));
	else
		$results = db_query('SELECT kodeuk, namasingkat from {unitkerja} where kodeuk in (select kodeuk from {kegiatanskpd} inner join {program} on {kegiatanskpd}.kodepro={program}.kodepro where {program}.kodeu=:kodeu and {kegiatanskpd}.sumberdana1=:sumberdana and inaktif=0 and total>0) order by kodedinas', array(':sumberdana'=>$sumberdana, ':kodeu'=>$kodeu));
	
}

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

$sqljenis = ($jenis=='ZZ'? '': ' and k.jenis=' . $jenis);
foreach ($results as $datas) {
	
	$anggaran = 0;
	if ($kodeu=='ZZ') {
		if ($sumberdana=='ZZ')
			$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k where k.inaktif=0 and k.total>0 and k.kodeuk=:kodeuk' . $sqljenis, array(':kodeuk'=>$datas->kodeuk));
		else
			$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k where k.inaktif=0 and k.total>0 and k.kodeuk=:kodeuk and k.sumberdana1=:sumberdana' . $sqljenis, array(':kodeuk'=>$datas->kodeuk, ':sumberdana'=>$sumberdana));

	} else {
		if ($sumberdana=='ZZ')
			$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k inner join {program} as p on k.kodepro=p.kodepro where k.inaktif=0 and k.total>0 and k.kodeuk=:kodeuk and p.kodeu=:kodeu' . $sqljenis, array(':kodeuk'=>$datas->kodeuk, ':kodeu'=>$kodeu));
		else
			$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k inner join {program} as p on k.kodepro=p.kodepro where k.inaktif=0 and k.total>0 and k.kodeuk=:kodeuk and p.kodeu=:kodeu and k.sumberdana1=:sumberdana' . $sqljenis, array(':kodeuk'=>$datas->kodeuk, ':kodeu'=>$kodeu, ':sumberdana'=>$sumberdana));

	}
	foreach ($res as $data) {
		$anggaran = $data->anggaran;	
	}
	
	$realisasi = 0; $lalu = 0; $ini = 0;
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->innerJoin('kegiatanskpd', 'k', 'dr.kodekeg=k.kodekeg');
	if ($kodeu != 'ZZ') {
		$sql->innerJoin('program', 'p', 'k.kodepro=p.kodepro');
		$sql->condition('p.kodeu', $kodeu, '='); 
	}	
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('d.kodeuk', $datas->kodeuk, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <= :month', array('month' => $bulan));
	if ($sumberdana!='ZZ') $sql->condition('k.sumberdana1', $sumberdana, '='); 
	if ($jenis!='ZZ') $sql->condition('k.jenis', $jenis, '='); 
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$ini = $data->realisasi;
	}

	$realisasi = $lalu + $ini;
	
	$anggaran_t += $anggaran;
	$lalu_t += $lalu;
	$ini_t += $ini;
	$realisasi_t += $realisasi;
	
	$skpd =  l($datas->namasingkat, 'laporan/realisasikeg/filter/' . $bulan . '/' . $datas->kodeuk . '/' . $sumberdana, array('attributes' => array('class' => null)));
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $skpd, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($anggaran), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($ini), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1(apbd_hitungpersen($anggaran, $ini)), 'align' => 'right', 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)
	$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($anggaran_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn1(apbd_hitungpersen($anggaran_t, $ini_t)) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($bulan, $sumberdana, $jenis) {


$rows_h[] = array(
	array('data' => 'LAPORAN REALISASI SKPD (SP2D)', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);
if ($sumberdana!='ZZ') {
	$rows_h[] = array(
		array('data' => 'SUMBER DANA : ' . $sumberdana, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

if ($jenis!='ZZ') {
	$rows_h[] = array(
		array('data' => ($jenis=='1'? 'BELANJA TIDAK LANGSUNG':'BELANJA LANGSUNG'), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

if ($bulan=='0') {
	$rows_h[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
	$rows_h[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows_h ));



//$header = array();
$header[] = array (
	array('data' => 'No','width' => '20px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'OPD','width' => '250px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Anggaran', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Realisasi', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();

if ($sumberdana=='ZZ')
	$results = db_query('SELECT kodeuk, namasingkat from {unitkerja} order by kodedinas');
else
	$results = db_query('SELECT kodeuk, namasingkat from {unitkerja} where kodeuk in (select kodeuk from {kegiatanskpd} where sumberdana1=:sumberdana and inaktif=0 and total>0) order by kodedinas', array(':sumberdana'=>$sumberdana));

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

foreach ($results as $datas) {

	$anggaran = 0;
	if ($sumberdana=='ZZ')
		$res = db_query('SELECT sum(total) as anggaran from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk', array(':kodeuk'=>$datas->kodeuk));
	else
		$res = db_query('SELECT sum(total) as anggaran from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk and sumberdana1=:sumberdana order by kegiatan', array(':kodeuk'=>$datas->kodeuk, ':sumberdana'=>$sumberdana));
	foreach ($res as $data) {
		$anggaran = $data->anggaran;	
	}
	
	$realisasi = 0; $lalu = 0; $ini = 0;
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->innerJoin('kegiatanskpd', 'k', 'dr.kodekeg=k.kodekeg');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('d.kodeuk', $datas->kodeuk, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <= :month', array('month' => $bulan));
	if ($sumberdana!='ZZ') $sql->condition('k.sumberdana1', $sumberdana, '='); 
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$ini = $data->realisasi;
	}

	$realisasi = $lalu + $ini;
	
	$anggaran_t += $anggaran;
	$lalu_t += $lalu;
	$ini_t += $ini;
	$realisasi_t += $realisasi;
	
	$n++;
	$rows[] = array(
		array('data' => $n . '.', 'width' => '20px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->namasingkat, 'width' => '250px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($anggaran), 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($ini), 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($anggaran, $ini)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '250px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($anggaran_t) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($ini_t) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn1(apbd_hitungpersen($anggaran_t, $ini_t)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);

$rows[] = array(
		array('data' => '', 'width' => '500px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);


//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>