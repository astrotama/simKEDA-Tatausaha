<?php
function register_rekapspm_main($arg=NULL, $nama=NULL) {
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
				$bulan = arg(2);
				$kodeuk = arg(3);
				$cetakpdf = arg(4);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$kodeuk = 'ZZ';
	}
	$output = gen_report_realisasi_print($bulan, $kodeuk);
	if ($cetakpdf=='pdf') {
		
		apbd_ExportPDF_P($output, 10, "Rekap Register SPM.pdf");
		//apbd_ExportPDF_P($htmlContent, $marginatas, $pdfFiel)
		//return $output;
		
	} else if($cetakpdf == 'excel'){

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Rekap_Register_SPM.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $kodeuk);
		$output_form = drupal_get_form('register_rekapspm_main_form');	 
		
		$btn = l('Cetak', 'rekapregisterspm/filter/' . $bulan . '/' .  $kodeuk . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp' . l('Excel', 'rekapregisterspm/filter/' . $bulan . '/' .  $kodeuk . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function register_rekapspm_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk= $form_state['values']['kodeuk'];
	
	$uri = 'rekapregisterspm/filter/' . $bulan . '/' . $kodeuk;
	drupal_goto($uri);
	
}


function register_rekapspm_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = 'ZZ';
	}
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('n');
	
	if(arg(1)!=null){		
		$bulan = arg(2);
		$kodeuk = arg(3);
	}
	
	//drupal_set_message(arg(3));
	
	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('p.kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= '|' . $data->namasingkat;
			}
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
		   	
		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $opt_bulan[$bulan] . $namasingkat .  '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
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
			'#default_value' => $kodeuk,
		);
	}
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => $opt_bulan,
	);
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan, $kodeuk) {

//TABEL
$header = array (
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'SPM', 'valign'=>'top'),
	array('data' => 'Jml. s/d. ' . label_bulan_lalu($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Rp. s/d. ' . label_bulan_lalu($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Jml. ' . label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Rp. ' . label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Jml. s/d. ' . label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Rp. s/d. ' . label_bulan_ini($bulan), 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

$arr_dokumen = array(	
		 '0' => t('UP'), 	
		 '1' => t('GU'), 	
		 '2' => t('TU'),
		 '3' => t('GAJI'),	
		 '4' => t('LS BARANG JASA'),	
		 '5' => t('GU NIHIL'),	
		 '6' => t('RESTITUSI'),	
		 '7' => t('TU NIHIL'),	
		 '8' => t('PFK'),	
	   );

$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;
$realisasi_t_n = 0;
$lalu_t_n = 0;
$ini_t_n = 0;

$realisasi_t_kas = 0;
$lalu_t_kas = 0;
$ini_t_kas = 0;
$realisasi_t_kas_n = 0;
$lalu_t_kas_n = 0;
$ini_t_kas_n = 0;

$realisasi_t_bel = 0;
$lalu_t_bel = 0;
$ini_t_bel = 0;
$realisasi_t_bel_n = 0;
$lalu_t_bel_n = 0;
$ini_t_bel_n = 0;

$realisasi_t_nhl = 0;
$lalu_t_nhl = 0;
$ini_t_nhl = 0;
$realisasi_t_nhl_n = 0;
$lalu_t_nhl_n = 0;
$ini_t_nhl_n = 0;

for ($n=0; $n<=8; $n++) {
	
	$realisasi = 0; $lalu = 0; $ini = 0;
	$realisasi_n = 0; $lalu_n = 0; $ini_n = 0;
	
	$sql = db_select('dokumen', 'd');
	$sql->addExpression('SUM(d.jumlah)', 'realisasi');
	$sql->addExpression('COUNT(d.dokid)', 'realisasi_n');
	if ($kodeuk!='ZZ') $sql->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.SPMtgl) <:month', array('month' => $bulan));
	$sql->condition('d.jenisdokumen', $n, '=');
	$sql->condition('d.spmok', '1', '='); 
	//$sql->condition('d.SPMno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$lalu = $data->realisasi;
		$lalu_n = $data->realisasi_n;
	}

	$sql = db_select('dokumen', 'd');
	$sql->addExpression('SUM(d.jumlah)', 'realisasi');
	$sql->addExpression('COUNT(d.dokid)', 'realisasi_n');
	if ($kodeuk!='ZZ') $sql->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.SPMtgl) =:month', array('month' => $bulan));
	$sql->condition('d.jenisdokumen', $n, '=');
	$sql->condition('d.spmok', '1', '='); 
	//$sql->condition('d.SPMno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$ini = $data->realisasi;
		$ini_n = $data->realisasi_n;
	}
	
	$realisasi = $lalu + $ini;	
	$lalu_t += $lalu;
	$ini_t += $ini;
	$realisasi_t += $realisasi;
	
	$realisasi_n = $lalu_n + $ini_n;	
	$lalu_t_n += $lalu_n;
	$ini_t_n += $ini_n;
	$realisasi_t_n += $realisasi_n;
	
	
	//KASI
	if (($n==0) or ($n==1) or ($n==2) or ($n==3) or ($n==4) or ($n==0)) {
			$lalu_t_kas_n += $lalu_n;
    	$ini_t_kas_n += $ini_n;
	    $realisasi_t_kas_n += $realisasi_n;
	    
	    $lalu_t_kas += $lalu;
    	$ini_t_kas += $ini;
	    $realisasi_t_kas += $realisasi;
	}
	
	//BELANJA
	if (($n==1) or ($n==3) or ($n==4) or ($n==5) or ($n==7)) {
			$lalu_t_bel_n += $lalu_n;
    	$ini_t_bel_n += $ini_n;
	    $realisasi_t_bel_n += $realisasi_n;
	    
	    $lalu_t_bel += $lalu;
    	$ini_t_bel += $ini;
	    $realisasi_t_bel += $realisasi;
	}
	
	//NHL
	if (($n==5) or ($n==7)) {
			$lalu_t_nhl_n += $lalu_n;
    	$ini_t_nhl_n += $ini_n;
	    $realisasi_t_nhl_n += $realisasi_n;
	    
	    $lalu_t_nhl += $lalu;
    	$ini_t_nhl += $ini;
	    $realisasi_t_nhl += $realisasi;
	}
	
	
	$SPM =  l($arr_dokumen[$n], 'cetakregisterspm/' . $kodeuk . '/' . $bulan . '/##/' . $n . '/10/........./pdf', array('attributes' => array('class' => null)));

	$rows[] = array(
		array('data' => $n+1, 'align' => 'left', 'valign'=>'top'),
		array('data' => $SPM, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($lalu_n), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($lalu), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($ini_n), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($ini), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi_n), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)


$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL SPM</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);
	
	
$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL SPM KAS</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_kas_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_kas) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_kas_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_kas) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_kas_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_kas) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);
	

$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL SPM BELANJA</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_bel_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_bel) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_bel_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_bel) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_bel_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_bel) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);
	
		$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL SPM NIHIL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_nhl_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($lalu_t_nhl) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_nhl_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($ini_t_nhl) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_nhl_n) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($realisasi_t_nhl) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);
	
//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($bulan, $kodeuk) {

$rows = array();
$rows[] = array(
	array('data' => 'LAPORAN REKAP SPM', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);
if ($kodeuk=='ZZ') {
	$rows[] = array(
		array('data' => 'KABUPATEN JEPARA', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namauk','kodeuk'))
			  ->condition('p.kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				
					$rows[] = array(
		array('data' => $data->namauk, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
				
			}
		}	
	
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

$tabel_data1 = theme('table', array('header' => null, 'rows' => $rows ));



$header = array();
if (arg(4) == 'excel') {
$header[] = array (
	array('data' => 'No','width' => '20px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SPM','width' => '145px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_lalu($bulan) .'Jml','width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_lalu($bulan) .'Rupiah','width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => label_bulan_ini($bulan) .'Jml', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => label_bulan_ini($bulan) .'Rupiah', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_ini($bulan) .'Jml', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_ini($bulan) .'Rupiah', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}else{
$header[] = array (
	array('data' => 'No','width' => '20px','rowspan' => '2', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SPM','width' => '145px', 'rowspan' => '2', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_lalu($bulan), 'width' => '115px','colspan' => '2', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => label_bulan_ini($bulan), 'width' => '115px','colspan' => '2', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 's/d. ' . label_bulan_ini($bulan), 'width' => '115px', 'colspan' => '2', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$header[] = array (
	array('data' => 'Jml','width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Rupiah','width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Jml', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Rupiah', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Jml', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Rupiah', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}

$rows = array();

$arr_dokumen = array(	
		 '0' => t('UP'), 	
		 '1' => t('GU'), 	
		 '2' => t('TU'),
		 '3' => t('GAJI'),	
		 '4' => t('LS BARANG JASA'),	
		 '5' => t('GU NIHIL'),	
		 '6' => t('RESTITUSI'),	
		 '7' => t('TU NIHIL'),	
		 '8' => t('PFK'),	
	   );

$realisasi_t = 0;
$lalu_t = 0;
$ini_t = 0;
$realisasi_t_n = 0;
$lalu_t_n = 0;
$ini_t_n = 0;

$realisasi_t_kas = 0;
$lalu_t_kas = 0;
$ini_t_kas = 0;
$realisasi_t_kas_n = 0;
$lalu_t_kas_n = 0;
$ini_t_kas_n = 0;

$realisasi_t_bel = 0;
$lalu_t_bel = 0;
$ini_t_bel = 0;
$realisasi_t_bel_n = 0;
$lalu_t_bel_n = 0;
$ini_t_bel_n = 0;

$realisasi_t_nhl = 0;
$lalu_t_nhl = 0;
$ini_t_nhl = 0;
$realisasi_t_nhl_n = 0;
$lalu_t_nhl_n = 0;
$ini_t_nhl_n = 0;

for ($n=0; $n<=8; $n++) {

	$realisasi = 0; $lalu = 0; $ini = 0;
	$realisasi_n = 0; $lalu_n = 0; $ini_n = 0;
	
	$sql = db_select('dokumen', 'd');
	$sql->addExpression('SUM(d.jumlah)', 'realisasi');
	$sql->addExpression('COUNT(d.dokid)', 'realisasi_n');
	if ($kodeuk!='ZZ') $sql->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.SPMtgl) <:month', array('month' => $bulan));
	$sql->condition('d.jenisdokumen', $n, '=');
	$sql->condition('d.spmok', '1', '='); 
	//$sql->condition('d.SPMno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$lalu = $data->realisasi;
		$lalu_n = $data->realisasi_n;
	}

	$sql = db_select('dokumen', 'd');
	$sql->addExpression('SUM(d.jumlah)', 'realisasi');
	$sql->addExpression('COUNT(d.dokid)', 'realisasi_n');
	if ($kodeuk!='ZZ') $sql->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM d.SPMtgl) =:month', array('month' => $bulan));
	$sql->condition('d.jenisdokumen', $n, '=');
	$sql->condition('d.spmok', '1', '='); 
	//$sql->condition('d.SPMno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$ini = $data->realisasi;
		$ini_n = $data->realisasi_n;
	}
	
	$realisasi = $lalu + $ini;	
	$lalu_t += $lalu;
	$ini_t += $ini;
	$realisasi_t += $realisasi;
	
	$realisasi_n = $lalu_n + $ini_n;	
	$lalu_t_n += $lalu_n;
	$ini_t_n += $ini_n;
	$realisasi_t_n += $realisasi_n;
	
	
	//KAS
	if (($n==0) or ($n==1) or ($n==2) or ($n==3) or ($n==4) or ($n==0)) {
			$lalu_t_kas_n += $lalu_n;
    	$ini_t_kas_n += $ini_n;
	    $realisasi_t_kas_n += $realisasi_n;
	    
	    $lalu_t_kas += $lalu;
    	$ini_t_kas += $ini;
	    $realisasi_t_kas += $realisasi;
	}
	
	//BELANJA
	if (($n==1) or ($n==3) or ($n==4) or ($n==5) or ($n==7)) {
			$lalu_t_bel_n += $lalu_n;
    	$ini_t_bel_n += $ini_n;
	    $realisasi_t_bel_n += $realisasi_n;
	    
	    $lalu_t_bel += $lalu;
    	$ini_t_bel += $ini;
	    $realisasi_t_bel += $realisasi;
	}
	
	//NHL
	if (($n==5) or ($n==7)) {
			$lalu_t_nhl_n += $lalu_n;
    	$ini_t_nhl_n += $ini_n;
	    $realisasi_t_nhl_n += $realisasi_n;
	    
	    $lalu_t_nhl += $lalu;
    	$ini_t_nhl += $ini;
	    $realisasi_t_nhl += $realisasi;
	}

	$rows[] = array(
		array('data' => $n+1 . '.', 'width' => '20px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $arr_dokumen[$n], 'width' => '145px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($lalu_n), 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($lalu), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($ini_n), 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($ini), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($realisasi_n), 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		
	);
	

}	//foreach ($results as $datas)

//KESELURUHAN
$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL SPM</strong>', 'width' => '145px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($lalu_t_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($lalu_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($ini_t_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($ini_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($realisasi_t_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
);

//KAS
$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL SPM KAS</strong>', 'width' => '145px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($lalu_t_kas_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($lalu_t_kas) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($ini_t_kas_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($ini_t_kas) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($realisasi_t_kas_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t_kas) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
);


//BELANJA
$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL SPM BELANJA</strong>', 'width' => '145px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($lalu_t_bel_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($lalu_t_bel) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($ini_t_bel_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($ini_t_bel) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($realisasi_t_bel_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t_bel) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
);

//NIHIL
$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL SPM NIHIL</strong>', 'width' => '145px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($lalu_t_nhl_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($lalu_t_nhl) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($ini_t_nhl_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($ini_t_nhl) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' .apbd_fn($realisasi_t_nhl_n) . '</strong>', 'width' => '40px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t_nhl) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	
);



$rows[] = array(
		array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);

//RENDER	
//$tabel_data2 = theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data2 = createT($header, $rows);

return $tabel_data1 . $tabel_data2;

}


?>