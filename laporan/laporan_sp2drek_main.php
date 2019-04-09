<?php
function laporan_sp2drek_main($arg=NULL, $nama=NULL) {
   

	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodej = arg(3);
				$kodeo = arg(4);
				$kodero = arg(5);
				$bulan = arg(6);
				$pengali = arg(7);
				$cetakpdf = arg(8);
				
				drupal_set_message($kodej);
				
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$kodej = '511';
		$kodeo = '51101';
		$kodero = '51101001';
		$bulan = date('n');
		$pengali = '14';
		$cetakpdf = '';
	}
	
	
	if ($cetakpdf=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Realisasi SP2D.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi($kodej, $kodeo, $kodero, $bulan, $pengali);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($kodej, $kodeo, $kodero, $bulan, $pengali);
		$output_form = drupal_get_form('laporan_sp2drek_main_form');	
		
		/*
		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporan/filter/' . $bulan . '/'. urlencode($kodeuk) .'/'.$tingkat.'/'.$margin.'/' . urlencode($tanggal) . '/' . $hal1 . '/pdf">Realisasi Kumulatif</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/'. urlencode($kodeuk) .'/'.$tingkat.'/'.$margin.'/' . urlencode($tanggal) . '/' . $hal1 . '/pdfp">Realisasi Periodik</a></li>' .
					'</ul>' .
				'</div>';		
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporan/filter/' . $bulan . '/'. urlencode($kodeuk) .'/'.$tingkat.'/'.$margin.'/' . urlencode($tanggal) . '/excel">Realisasi Kumulatif</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/'. urlencode($kodeuk) .'/'.$tingkat.'/'.$margin.'/' . urlencode($tanggal) . '/excel2">Realisasi Periodik</a></li>' .
					'</ul>' .
				'</div>';		
		
		*/
		$btn = l('<span class="glyphicon glyphicon-retweet" aria-hidden="true"></span> Excel', 'laporan/realisasisp2d/filter/' . $kodej . '/'. $kodeo . '/' . $kodero . '/' . $bulan . '/' . $pengali . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary btn-sm')));
		
		return drupal_render($output_form) . $output . $btn;
		
	}		
}

function laporan_sp2drek_main_form ($form, &$form_state) {

	$kodej = arg(2);
	$kodeo = arg(3);
	$kodero = arg(4);
	$bulan = arg(5);
	$pengali = arg(6);
		
	if ($kodej == '') {
		$kodej = '511';
		$kodeo = '51101';
		$kodero = '51101001';
		$bulan = date('n');
		$pengali = '14';
	}
	

	//AJAX
	// Jenis dropdown list
	$form['kodej'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => _load_jenis(),
		'#default_value' => $kodej,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_obyek',
			'wrapper' => 'obyek-wrapper',
		),
	);


	// Wrapper for rekdetil dropdown list
	$form['wrapperobyek'] = array(
		'#prefix' => '<div id="obyek-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Obyek -');
	if (isset($form_state['values']['kodej'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_obyek($form_state['values']['kodej']);
	} else
		$options = _load_obyek($kodej);

	// Detil dropdown list
	$form['wrapperobyek']['kodeo'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $options,
		'#default_value' => $kodeo,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekening',
			'wrapper' => 'rekening-wrapper',
		),
		
	);

	// Wrapper for rekdetil dropdown list
	$form['wrapperrekening'] = array(
		'#prefix' => '<div id="rekening-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$rekenings = array('- Pilih Rincian -');
	if (isset($form_state['values']['kodeo'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings = _load_rekening($form_state['values']['kodeo']);
	} else
		$rekenings = _load_rekening($kodeo);

	// Detil dropdown list
	$form['wrapperrekening']['kodero'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => $rekenings,
		'#default_value' => $kodero,
		'#validated' => TRUE,
	);	
	//END AJAX

	$form['bulan'] = array(
		'#title' => t('Batas Bulan'),
		'#type' => 'select',
		'#options' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
		'#default_value' => $bulan,
		'#validated' => TRUE,

	);
	$form['pengali'] = array(
		'#title' => t('Pengali'),
		'#type' => 'textfield',
		//'#options' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
		'#default_value' => $pengali,
		'#validated' => TRUE,

	);	
	$form['submitprint']= array(
		'#type' => 'submit',
		//'#value' => 'Tampilkan',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
	);	
	

	return $form;
}


function _ajax_obyek($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperobyek'];
}


function _ajax_rekening($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperrekening'];
}

/**
 * Function for populating rekening
 */
function _load_jenis() {
	$jenis = array('- Pilih Jenis -');


	// Select table
	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));
	$query->condition("j.kodek", db_like('5') . '%', 'LIKE');
	// Order by name
	$query->orderBy("j.kodej");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenis[$row->kodej] = $row->kodej . ' - ' . $row->uraian;
	}

	return $jenis;
}

function _load_obyek($kodej) {
	$obyek = array('- Pilih Obyek -');


	// Select table
	$query = db_select("obyek", "o");
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));
	// Filter the active ones only
	$query->condition("o.kodej", $kodej, '=');
	
	// Order by name
	$query->orderBy("o.kodeo");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$obyek[$row->kodeo] =  $row->kodeo . ' - ' . $row->uraian;
	}

	return $obyek;
}

function _load_rekening($kodeo) {
	$rekening = array('- Pilih Rekening -');
	//$rekening = array($kodeo);
	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodeo", $kodeo, "=");
	// Order by name
	$query->orderBy("r.kodero");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$rekening[$row->kodero] = $row->kodero . ' - ' . $row->uraian;
	}

	return $rekening;
}



function laporan_sp2drek_main_form_submit($form, &$form_state) {
	//akuntansi/buku/kodeo/41201006/kodej
	$kodej = $form_state['values']['kodej'];
	$kodeo = $form_state['values']['kodeo'];
	$kodero = $form_state['values']['kodero'];
	$bulan = $form_state['values']['bulan'];
	$pengali = $form_state['values']['pengali'];
	
	$uri = '/laporan/realisasisp2d/filter/' . $kodej . '/'. $kodeo . '/' . $kodero . '/' . $bulan . '/' . $pengali	;
	drupal_goto($uri);

}

function gen_report_realisasi($kodej, $kodeo, $kodero, $bulan, $pengali) {

$realisasi = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); 
$arr_total = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0); 
$no = 0;
$itotal = 12; $irerata = 13; $iproyeksi = 14; $ianggaran = 15; $iat = 16; $iap = 17;


//TABEL
$header = array (
	array('data' => 'No','width' => '10px', 'valign'=>'top'),
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Jan', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Feb', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Mar', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Apr', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Mei', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Jun', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Jul', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Ags', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Sep', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Okt', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Nop', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Des', 'width' => '90px', 'valign'=>'top'),
	array('data' => '[T]Total', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Rerata', 'width' => '90px', 'valign'=>'top'),
	array('data' => '[P]Proyeksi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '[A]Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'A - T', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'A - P', 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

$results = db_query('select kodeuk, namasingkat from {unitkerja} order by kodedinas');
   
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$anggaran = 0;
	if ($kodero != '0') {
		$res = db_query('select sum(a.jumlah) as anggaran from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg where k.inaktif=0 and k.kodeuk=:kodeuk and a.kodero=:kodero', array(':kodeuk'=>$datas->kodeuk, ':kodero'=>$kodero));
		
	} else if ($kodeo != '0') {
		$res = db_query('select sum(a.jumlah) as anggaran from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg where k.inaktif=0 and k.kodeuk=:kodeuk and left(a.kodero,5)=:kodeo', array(':kodeuk'=>$datas->kodeuk, ':kodeo'=>$kodeo));
		
	} else {
		$res = db_query('select sum(a.jumlah) as anggaran from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg where k.inaktif=0 and k.kodeuk=:kodeuk and left(a.kodero, 3)=:kodej', array(':kodeuk'=>$datas->kodeuk, ':kodej'=>$kodej));		
		
	}		
	foreach ($res as $data) {
		$anggaran += $data->anggaran;
	}
		
	$realisasi[0] = 0; 
	$realisasi[1] = 0; 
	$realisasi[2] = 0; 
	$realisasi[3] = 0; 
	$realisasi[4] = 0; 
	$realisasi[5] = 0; 
	$realisasi[6] = 0; 
	$realisasi[7] = 0; 
	$realisasi[8] = 0; 
	$realisasi[9] = 0; 
	$realisasi[10] = 0; 
	$realisasi[11] = 0; 
	
	$total = 0;
	for ($i = 0; $i < $bulan; $i++) {
		
		//drupal_set_message($kodero);
		//drupal_set_message($i);
		if ($kodero != '0') {
			$res = db_query('select sum(dr.jumlah) as realisasi from {dokumen} as d inner join {dokumenrekening} as dr on d.dokid=dr.dokid where d.sp2dok=1 and month(d.sp2dtgl)=:bulan and d.kodeuk=:kodeuk and dr.kodero=:kodero and length(d.sp2dno)>0', array(':bulan'=>$i+1, ':kodeuk'=>$datas->kodeuk, ':kodero'=>$kodero));
			foreach ($res as $data) {
				$realisasi[$i] = $data->realisasi;
				$total += $data->realisasi;
			}
			
		} else if ($kodeo != '0') {
			$res = db_query('select sum(dr.jumlah) as realisasi from {dokumen} as d inner join {dokumenrekening} as dr on d.dokid=dr.dokid where d.sp2dok=1 and month(d.sp2dtgl)=:bulan and d.kodeuk=:kodeuk and left(dr.kodero,5)=:kodeo and length(d.sp2dno)>0', array(':bulan'=>$i+1, ':kodeuk'=>$datas->kodeuk, ':kodeo'=>$kodeo));
			foreach ($res as $data) {
				$realisasi[$i] = $data->realisasi;
				$total += $data->realisasi;
			}		
			
		} else {
			$res = db_query('select sum(dr.jumlah) as realisasi from {dokumen} as d inner join {dokumenrekening} as dr on d.dokid=dr.dokid where d.sp2dok=1 and month(d.sp2dtgl)=:bulan and d.kodeuk=:kodeuk and left(dr.kodero,3)=:kodej and length(d.sp2dno)>0', array(':bulan'=>$i+1, ':kodeuk'=>$datas->kodeuk, ':kodej'=>$kodej));
			foreach ($res as $data) {
				$realisasi[$i] = $data->realisasi;
				$total += $data->realisasi;
			}		
		}
		
		$arr_total[$i] += $realisasi[$i];
		
	} 
	$rerata = $total/$bulan;
	
	//$rerata = $realisasi[$bulan];
	$proyeksi = $rerata * $pengali;
	$at = $anggaran - $total;
	$ap = $anggaran - $proyeksi;
	
	$arr_total[$itotal] += $total;
	$arr_total[$irerata] += $rerata;
	$arr_total[$iproyeksi] += $proyeksi;
	$arr_total[$ianggaran] += $anggaran;
	$arr_total[$iat] += $at;
	$arr_total[$iap] += $ap;
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->namasingkat, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[0]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[1]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[2]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[3]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[4]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[5]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[6]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[7]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[8]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[9]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[10]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi[11]), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($total), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($rerata), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($proyeksi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($anggaran), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($at), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($ap), 'align' => 'right', 'valign'=>'top'),
	);

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => NULL, 'align' => 'left', 'valign'=>'top'),
	array('data' => 'TOTAL', 'align' => 'left', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[0]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[1]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[2]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[3]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[4]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[5]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[6]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[7]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[8]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[9]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[10]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[11]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[$itotal]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[$irerata]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[$iproyeksi]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[$ianggaran]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[$iat]), 'align' => 'right', 'valign'=>'top'),
	array('data' => apbd_fn($arr_total[$iap]), 'align' => 'right', 'valign'=>'top'),
);

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

	

?>
