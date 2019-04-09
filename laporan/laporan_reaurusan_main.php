<?php
function laporan_reaurusan_main($arg=NULL, $nama=NULL) {
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
				$jenis = arg(4);
				$cetakpdf = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$jenis = 'ZZ';
	}
	$output = gen_report_realisasi_print($bulan, $jenis);
	if ($cetakpdf=='pdf') {
		
		apbd_ExportPDF_P($output, 10, "Realisasi per Urusan.pdf");
		//apbd_ExportPDF_P($htmlContent, $marginatas, $pdfFiel)
		//return $output;
		
	} else if($cetakpdf == 'excel'){

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Realisasi_per_Urusan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $jenis);
		$output_form = drupal_get_form('laporan_reaurusan_main_form');	 
		
		$btn = l('Cetak', 'laporan/realisasiurusan/filter/' . $bulan . '/' . $jenis . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp' . l('Excel', 'laporan/realisasiurusan/filter/' . $bulan . '/' . $jenis . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_reaurusan_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$jenis= $form_state['values']['jenis'];
	
	$uri = 'laporan/realisasiurusan/filter/' . $bulan . '/' . $jenis;
	drupal_goto($uri);
	
}


function laporan_reaurusan_main_form($form, &$form_state) {
	
	$bulan = date('n');
	$jenis = 'ZZ';
	
	if(arg(3)!=null){		
		$bulan = arg(3);
		$jenis = arg(4);
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
		   
  $opt_jenis['ZZ'] ='SEMUA JENIS BELANJA';
	$opt_jenis['1'] = 'BELANJA TIDAK LANGSUNG';
	$opt_jenis['2'] = 'BELANJA LANGSUNG';	
		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $opt_bulan[$bulan] . '|'. $opt_jenis[$jenis] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
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
	
	$form['formdata']['jenis'] = array(
		'#type' => 'select',
		'#title' =>  t('JENIS BELANJA'),
		'#options' => $opt_jenis,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenis,
	);		
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan, $jenis) {

//TABEL
$header = array (
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'Urusan', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '50px', 'valign'=>'top'),
);
$rows = array();

$results = db_query('SELECT kodeu,urusan from {urusan} order by kodeu');

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

$sqljenis = ($jenis=='ZZ'? '': ' and k.jenis=' . $jenis);
foreach ($results as $datas) {
	
	$anggaran = 0;
	$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k inner join {program} as p on k.kodepro=p.kodepro where inaktif=0 and total>0 and p.kodeu=:kodeu AND k.sumberdana1!=:pembiayaan' . $sqljenis, array(':kodeu'=>$datas->kodeu, ':pembiayaan'=>'PEMBIAYAAN'));
	foreach ($res as $data) {
		$anggaran = $data->anggaran;	
	}
	
	$realisasi = 0; $lalu = 0; $ini = 0;
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$sql->innerJoin('program', 'p', 'k.kodepro=p.kodepro');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('p.kodeu', $datas->kodeu, '='); 
	if ($jenis!='ZZ') $sql->condition('k.jenis', $jenis, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <=:month', array('month' => $bulan));
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
	
	$urusan =  l($datas->urusan, 'laporan/realisasiuk/filter/' . $bulan . '/##/' . $jenis . '/' . $datas->kodeu, array('attributes' => array('class' => null)));
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $urusan, 'align' => 'left', 'valign'=>'top'),
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


function gen_report_realisasi_print($bulan, $jenis) {


$rows[] = array(
	array('data' => 'LAPORAN REALISASI URUSAN (SP2D)', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);
if ($jenis!='ZZ') {
	$rows[] = array(
		array('data' => ($jenis=='1'? 'BELANJA TIDAK LANGSUNG':'BELANJA LANGSUNG'), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
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
	array('data' => 'Urusan','width' => '250px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Anggaran', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Realisasi', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();

$results = db_query('SELECT kodeu,urusan from {urusan} order by kodeu');

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

$sqljenis = ($jenis=='ZZ'? '': ' and jenis=' . $jenis);
foreach ($results as $datas) {

	$anggaran = 0;
	$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k inner join {program} as p on k.kodepro=p.kodepro where inaktif=0 and total>0 and p.kodeu=:kodeu AND k.sumberdana1!=:pembiayaan' . $sqljenis, array(':kodeu'=>$datas->kodeu, ':pembiayaan'=>'PEMBIAYAAN'));
	foreach ($res as $data) {
		$anggaran = $data->anggaran;	
	}
	
	$realisasi = 0; $lalu = 0; $ini = 0;

	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$sql->innerJoin('program', 'p', 'k.kodepro=p.kodepro');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('p.kodeu', $datas->kodeu, '='); 
	if ($jenis!='ZZ') $sql->condition('k.jenis', $jenis, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <=:month', array('month' => $bulan));
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
		array('data' => $datas->urusan, 'width' => '250px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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

function gen_report_realisasi_fungsi($bulan, $jenis) {

//TABEL
$header = array (
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'Fungsi', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 's/d. ' . label_bulan_lalu($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 's/d. ' . label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '50px', 'valign'=>'top'),
);
$rows = array();

$results = db_query('SELECT kodef,fungsi from {fungsi} order by kodef');

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;

$sqljenis = ($jenis=='ZZ'? '': ' and k.jenis=' . $jenis);
foreach ($results as $datas) {
	
	$anggaran = 0;
	$res = db_query('SELECT sum(k.total) as anggaran from {kegiatanskpd} as k inner join {program} as p on k.kodepro=p.kodepro where inaktif=0 and total>0 and p.kodef=:kodef AND k.sumberdana1!=:pembiayaan' . $sqljenis, array(':kodef'=>$datas->kodef, ':pembiayaan'=>'PEMBIAYAAN'));
	foreach ($res as $data) {
		$anggaran = $data->anggaran;	
	}
	
	$realisasi = 0; $lalu = 0; $ini = 0;

	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$sql->innerJoin('program', 'p', 'k.kodepro=p.kodepro');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('p.kodeu', $datas->kodeu, '='); 
	if ($jenis!='ZZ') $sql->condition('k.jenis', $jenis, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <:month', array('month' => $bulan));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$lalu = $data->realisasi;
	}
	$sql = db_select('dokumen', 'd'); $sql->innerJoin('dokumenrekening', 'dr', 'd.dokid=dr.dokid');
	$sql->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$sql->innerJoin('program', 'p', 'k.kodepro=p.kodepro');
	$sql->addExpression('SUM(dr.jumlah)', 'realisasi');
	$sql->condition('p.kodeu', $datas->kodeu, '='); 
	if ($jenis!='ZZ') $sql->condition('k.jenis', $jenis, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) =:month', array('month' => $bulan));
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
	
	$urusan =  l($datas->urusan, 'laporan/realisasiuk/filter/' . $bulan . '/##/' . $jenis . '/' . $datas->kodeu, array('attributes' => array('class' => null)));
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $urusan, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($anggaran), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($lalu), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($ini), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)
	$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($anggaran_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn1(apbd_hitungpersen($anggaran_t, $realisasi_t)) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>
