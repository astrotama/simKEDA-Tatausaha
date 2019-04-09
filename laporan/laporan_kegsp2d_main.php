<?php
function laporan_kegsp2d_main($arg=NULL, $nama=NULL) {
	$cetakpdf = '';
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodekeg = arg(3);
				$kodero = arg(4);
				$tanggal = arg(5);
				$cetakpdf = arg(6);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$kodekeg = '000000';
		$kodero = '00';
		$tanggal = '00';
	}

	if ($kodero=='') $kodero = '00';
	if ($tanggal=='') $tanggal = '00';
	
	$results = db_query('SELECT kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach ($results as $datas) {
		$kegiatan = $datas->kegiatan;
	}
	
	if ($kodero!='00') {
		$results = db_query('SELECT uraian from {rincianobyek} where kodero=:kodero', array(':kodero'=>$kodero));
		foreach ($results as $datas) {
			$kegiatan .= '|' . $datas->uraian;
		}
		
	}
	drupal_set_title('Daftar SP2D|' . $kegiatan);
	
	
	
	if ($cetakpdf=='pdf') {
		if ($kodero=='00') { 
			$output = gen_report_realisasi_print($kodekeg);
			apbd_ExportPDF_P($output, 10, "Daftar SP2D per Kegiatan.pdf");
		} else {
			$output = gen_report_realisasi_print($kodekeg);
			apbd_ExportPDF_P($output, 10, "Daftar SP2D per Kegiatan.pdf");
		}
	} else {
		//drupal_set_message(arg(4));
		if ($kodero=='00') 
			$output = gen_report_realisasi($kodekeg, $tanggal);
		else
			$output = gen_report_realisasi_rekening($kodekeg, $kodero, $tanggal);
		
		$btn = l('Cetak', 'cetakregisterskpd/kegiatan/' . $kodekeg . '/' . $kodero . '/pdf', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return $btn . $output . $btn;
		
	}	
	
}

function laporan_kegsp2d_main_form_submit($form, &$form_state) {
	
}



function gen_report_realisasi($kodekeg, $tanggal) {

//TABEL
$header = array (
	array('data' => 'No.', 'width' => '10px', 'valign'=>'top'),
	array('data' => 'No. SP2D', 'width' => '70px', 'valign'=>'top'),
	array('data' => 'Tgl. SP2D', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Keperluan', 'valign'=>'top'),
	array('data' => 'Penerima', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
	array('data' => '', 'valign'=>'top'),
);
$rows = array();

if ($tanggal=='00')
	//$results = db_query('SELECT dokid, sp2dno, sp2dtgl, keperluan, penerimanama, penerimabanknama, penerimabankrekening, jumlah from {dokumen} where sp2dok=1 and kodekeg=:kodekeg order by sp2dtgl,sp2dno', array(':kodekeg'=>$kodekeg));

	$results = db_query('SELECT d.dokid, d.sp2dno, d.sp2dtgl, d.keperluan, d.penerimanama, d.penerimabanknama, d.penerimabankrekening, SUM(dr.jumlah) jumlah from {dokumen} d inner join {dokumenrekening} dr on d.dokid=dr.dokid where d.sp2dok=1 and dr.kodekeg=:kodekeg GROUP BY d.dokid ORDER BY d.sp2dtgl,d.sp2dno', array(':kodekeg'=>$kodekeg));
else
	$results = db_query('SELECT d.dokid, d.sp2dno, d.sp2dtgl, d.keperluan, d.penerimanama, d.penerimabanknama, d.penerimabankrekening, SUM(dr.jumlah) jumlah from {dokumen} d inner join {dokumenrekening} dr on d.dokid=dr.dokid where d.sp2dok=1 and dr.kodekeg=:kodekeg and d.sp2dtgl<=:sp2dtgl GROUP BY d.dokid ORDER BY d.sp2dtgl,d.sp2dno', array(':kodekeg'=>$kodekeg, ':sp2dtgl'=>$tanggal));
	
$n = 0;
$total = 0;
foreach ($results as $datas) {

	$total += $datas->jumlah;
	
	$esp2dlink = apbd_button_esp2d($datas->dokid);
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->sp2dno, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fd($datas->sp2dtgl), 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->keperluan, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->penerimanama . ' (' . $datas->penerimabankrekening . '/' . $datas->penerimabanknama . ')', 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->jumlah), 'align' => 'right', 'valign'=>'top'),
		array('data' => $esp2dlink, 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)
	$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($total) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($kodekeg) {

set_time_limit(0);
ini_set('memory_limit','940M');


$results = db_query('SELECT kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
foreach ($results as $datas) {
	$kegiatan = $datas->kegiatan;
}

$rows[] = array(
	array('data' => 'LAPORAN REALISASI KEGIATAN (SP2D)', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);
$rows[] = array(
	array('data' => $kegiatan, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);


$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

//$header = array();
$header[] = array (
	array('data' => 'NO','width' => '20px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KODE','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KEGIATAN','width' => '280px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();


if ($sumberdana=='ZZ')
	$results = db_query('SELECT kodekeg, kegiatan, total from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk order by kegiatan', array(':kodeuk'=>$kodeuk));
else
	$results = db_query('SELECT kodekeg, kegiatan, total from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk and sumberdana1=:sumberdana order by kegiatan', array(':kodeuk'=>$kodeuk, ':sumberdana'=>$sumberdana));

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
foreach ($results as $datas) {

	$sql = db_select('dokumen', 'd');
	$sql->addExpression('SUM(d.jumlah)', 'realisasi');
	$sql->condition('d.kodekeg', $datas->kodekeg, '='); 
	if ($kodekeg>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <= :month', array('month' => $kodekeg));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}

	$anggaran_t += $datas->total;
	$realisasi_t += $realisasi;
	
	$n++;
	$rows[] = array(
		array('data' => $n . '.', 'width' => '20px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => substr($datas->kodekeg, -6), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->kegiatan, 'width' => '280px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->total), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '310px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($anggaran_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
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


function gen_report_realisasi_rekening($kodekeg, $kodero, $tanggal) {

//TABEL
$header = array (
	array('data' => 'No.', 'width' => '10px', 'valign'=>'top'),
	array('data' => 'No. SP2D', 'width' => '70px', 'valign'=>'top'),
	array('data' => 'Tgl. SP2D', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Keperluan', 'valign'=>'top'),
	array('data' => 'Penerima', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
	array('data' => '', 'valign'=>'top'),
);
$rows = array();

if ($tanggal=='00') 
	$results = db_query('SELECT d.dokid, d.sp2dno, d.sp2dtgl, d.keperluan, d.penerimanama, d.penerimabanknama, d.penerimabankrekening, di.jumlah from {dokumen} as d inner join {dokumenrekening} as di on d.dokid=di.dokid where di.jumlah>0 and d.sp2dok=1 and d.kodekeg=:kodekeg and di.kodero=:kodero order by d.sp2dtgl,d.sp2dno', array(':kodekeg'=>$kodekeg, ':kodero'=>$kodero));
else
	$results = db_query('SELECT d.dokid, d.sp2dno, d.sp2dtgl, d.keperluan, d.penerimanama, d.penerimabanknama, d.penerimabankrekening, di.jumlah from {dokumen} as d inner join {dokumenrekening} as di on d.dokid=di.dokid where di.jumlah>0 and d.sp2dok=1 and d.kodekeg=:kodekeg and di.kodero=:kodero and d.sp2dtgl<=:sp2dtgl order by d.sp2dtgl,d.sp2dno', array(':kodekeg'=>$kodekeg, ':kodero'=>$kodero, ':sp2dtgl'=>$tanggal));
	
$n = 0;
$total = 0;
foreach ($results as $datas) {

	$total += $datas->jumlah;
	
	$esp2dlink = apbd_button_esp2d($datas->dokid);
	
	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->sp2dno, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fd($datas->sp2dtgl), 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->keperluan, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->penerimanama . ' (' . $datas->penerimabankrekening . '/' . $datas->penerimabanknama . ')', 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->jumlah), 'align' => 'right', 'valign'=>'top'),
		array('data' => $esp2dlink, 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)
	$rows[] = array(
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>TOTAL</STRONG>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<STRONG>' . apbd_fn($total) . '</STRONG>', 'align' => 'right', 'valign'=>'top'),
	);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_rekening($kodekeg, $kodero) {

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

if ($kodekeg=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $kodekeg . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));



//$header = array();
$header[] = array (
	array('data' => 'NO','width' => '20px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KODE','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KEGIATAN','width' => '280px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();


if ($sumberdana=='ZZ')
	$results = db_query('SELECT kodekeg, kegiatan, total from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk order by kegiatan', array(':kodeuk'=>$kodeuk));
else
	$results = db_query('SELECT kodekeg, kegiatan, total from {kegiatanskpd} where inaktif=0 and total>0 and kodeuk=:kodeuk and sumberdana1=:sumberdana order by kegiatan', array(':kodeuk'=>$kodeuk, ':sumberdana'=>$sumberdana));

$n = 0;
$anggaran_t = 0;
$realisasi_t = 0;
foreach ($results as $datas) {

	$sql = db_select('dokumen', 'd');
	$sql->addExpression('SUM(d.jumlah)', 'realisasi');
	$sql->condition('d.kodekeg', $datas->kodekeg, '='); 
	if ($kodekeg>0) $sql->where('EXTRACT(MONTH FROM d.sp2dtgl) <= :month', array('month' => $kodekeg));
	$sql->condition('d.sp2dok', '1', '='); 
	$sql->condition('d.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}

	$anggaran_t += $datas->total;
	$realisasi_t += $realisasi;
	
	$n++;
	$rows[] = array(
		array('data' => $n . '.', 'width' => '20px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => substr($datas->kodekeg, -6), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->kegiatan, 'width' => '280px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->total), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null, 'width' => '20px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '310px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($anggaran_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' .apbd_fn($realisasi_t) . '</strong>', 'width' => '75px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
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
