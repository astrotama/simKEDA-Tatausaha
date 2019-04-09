<?php
function register_all_main($arg=NULL, $nama=NULL) {
		$bulan = arg(1);
		$batch = arg(2);
		$last = arg(3);
		
		$marginatas = 10;
		
		$_SESSION["register-sp2d-batch"] = $batch;
	if(arg(4)=='pdf'){
		if ($batch == '1') {			
			$output = getlaporanregister_all_batch_pertama($bulan);
			//apbd_ExportPDF('L', 'F4', $output, 'Register SP2D.pdf');
			print_laporan_register($output, 'Register SP2D - 1', $marginatas);

		} else if ($last == 'last') {
			$output = getlaporanregister_all_batch_terakhir($bulan, $batch);
			//pbd_ExportPDF('L', 'F4', $output, 'Register SP2D');
			print_laporan_register($output, 'Register SP2D - ' . $batch, $marginatas);
			
		} else {
			$output = getlaporanregister_all_batch($bulan, $batch);
			//apbd_ExportPDF('L', 'F4', $output, 'Register SP2D');
			print_laporan_register($output, 'Register SP2D - ' . $batch, $marginatas);
		}

	} else if(arg(4)=='cetak'){	
		if ($batch == '1') {			
			$output = getlaporanregister_all_batch_pertama($bulan);
			return $output;

		} else if ($last == 'last') {
			$output = getlaporanregister_all_batch_terakhir($bulan, $batch);
			return $output;
			
		} else {
			$output = getlaporanregister_all_batch($bulan, $batch);
			return $output;
		}
		
	}  else if(arg(4)=='excel'){	
		if ($batch == '1') {
			$output = getlaporanregister_all_batch_pertama($bulan);
			header( "Content-Type: application/vnd.ms-excel" );
			header( "Content-disposition: attachment; filename= Register_SP2D-1.xls" );
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo $output;
		} else if ($last == 'last') {
			$output = getlaporanregister_all_batch_terakhir($bulan, $batch);
			header( "Content-Type: application/vnd.ms-excel" );
			header( "Content-disposition: attachment; filename= Register_SP2D-$batch.xls" );
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo $output;

		}else {
			$output = getlaporanregister_all_batch($bulan, $batch);
			header( "Content-Type: application/vnd.ms-excel" );
			header( "Content-disposition: attachment; filename= Register_SP2D-$batch.xls" );
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo $output;
		}
	} else {
	
		
		$output_form = drupal_get_form('register_all_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function register_all_main_form($form, &$form_state) {

	$bulan = arg(2);
	
	if ($bulan=='') {
		$bulan = $_SESSION["register-sp2d-lastbulan"];
		if ($bulan=='') $bulan = date('n');
	}
	
	$arr_bulan=array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['bulan']= array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#options' => $arr_bulan,
		'#default_value'=> $bulan, 
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_report',
			'wrapper' => 'rekening-wrapper',
		),		
	);	
	$form['wrapperreport'] = array(
			'#prefix' => '<div id="rekening-wrapper">',
			'#suffix' => '</div>', 
	);
	
	if (isset($form_state['values']['bulan'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$batchitem = _load_item($form_state['values']['bulan']);
	} else
		$batchitem = _load_item($bulan);

	
	$form['wrapperreport']['item'] = array(
			'#title' => t('Item'),
			'#markup' => $batchitem,
	);	
	
	$form['paging']= array(
		'#type' => 'submit',
		'#value' => 'PAGING',
	);			
	
	return $form;
}

function _ajax_report($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperreport'];
}

function register_all_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function register_all_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	
	//if($form_state['clicked_button']['#value'] == $form_state['values']['paging']) 
	apbd_register_all_paging($bulan);
	

}

function getlaporanregister_all_batch_pertama($bulan){
	set_time_limit(0);
	ini_set('memory_limit','920M');
	
	$batch = 1;

	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'colspan' => '13','width' => '875px','align'=>'center','style'=>'font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER SP2D', 'colspan' => '13','width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
	);
	if (!isSuperuser()) {
		$rows[]=array(
			array('data' => $namauk, 'colspan' => '13','width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
		);
	}
	$rows[]=array(
		array('data' => 'BULAN ' . $bulan . ' TAHUN ' . apbd_tahun(), 'colspan' => '13','width' => '875px','align'=>'center','style'=>'border:none;font-size:80%;'),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	$header=array(
		array('data' => 'NO',  'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
		array('data' => 'TANGGAL',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NO. SP2D',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJ', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'RUT', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NHL', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'PFK', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'SKPD', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'URAIAN', 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJI', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'RUTIN', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NIHIL', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'PFK', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
	);
	
	
	$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk inner join {dokumenregisterall} dr  on d.dokid=dr.dokid WHERE (MONTH(d.sp2dtgl) = :bulan) and dr.batch=:batch and d.sp2dok=1  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ',array(':bulan'=>$bulan, ':batch' => '1'));

	$n=0;

	foreach($results as $data){

		$n++;
		$rutin = '';
		$gaji = '';
		$pfk = '';
		$nhl = '';

		$rutin_rp='';
		$nhl_rp='';
		$gaji_rp='';
		$pfk_rp='';
		
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){					//UP
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
		}
			
		else if($data->jenisdokumen==1){			//GU
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==2){			//TU
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==3){			//GAJI
			$gaji='x';
			$gaji_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==4) {			//LS
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';

			
		}

		else if($data->jenisdokumen==6) {			//PAD
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';

			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
		} 
		else {
			$pfk='x';
			$pfk_rp = apbd_fn($data->jumlah);
			
		}	
		
		$rows[]=array(
			array('data' => $n, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fd($data->sp2dtgl), 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->sp2dno, 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->namasingkat, 'width' => '80px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $keperluan, 'width' => '265px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $gaji_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			
		);
		
	}
	
	$rows[]=array(
		array('data' => '', 'colspan' => '13','width' => '875px','align'=>'left','style'=>'border-top:1px solid black;font-size:70%;'),
	);

	$nb = $batch+1;	
	$rows[]=array(
		array('data' => 'Bersambung ke halaman ' . $nb . '-1', 'colspan' => '13', 'width' => '875px','align'=>'left','style'=>'border:none;font-size:70%;'),
	);

	$results=db_query('SELECT d.sp2dno, d.sp2dtgl, d.jumlah, d.keperluan FROM  {dokumen} d INNER JOIN {dokumenregisterall} dr  on d.dokid=dr.dokid WHERE (MONTH(d.sp2dtgl) = :bulan) and dr.batch=:batch'. $where . ' and d.sp2dok=1 ORDER BY d.sp2dtgl,d.sp2dno LIMIT 1',array(':bulan'=>$bulan, ':batch' => $nb));
	
	$n++;	
	foreach($results as $data){	
		$rows[]=array(
			array('data' => 'No: ' . $n . '. ' . $data->sp2dno . ', tanggal ' . apbd_fd($data->sp2dtgl) . ', ' . $data->keperluan . ', sebesar ' .  apbd_fn($data->jumlah) . ',00', 'colspan' => '13', 'width' => '875px','align'=>'left','style'=>'border:none;font-size:70%;'),
		);			
	}
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));

	return $output;
}


function getlaporanregister_all_batch($bulan, $batch){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$header=array(
		array('data' => 'NO',  'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
		array('data' => 'TANGGAL',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NO. SP2D',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJ', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'RUT', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NHL', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'PFK', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'SKPD', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'URAIAN', 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJI', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'RUTIN', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NIHIL', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'PFK', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
	);
	
	$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk inner join {dokumenregisterall} dr  on d.dokid=dr.dokid WHERE (MONTH(d.sp2dtgl) = :bulan) and dr.batch=:batch and d.sp2dok=1  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ',array(':bulan'=>$bulan, ':batch' => $batch));

	
	$n= ((int) $batch - 1) * 170;

	foreach($results as $data){

		$n++;
		$rutin = '';
		$gaji = '';
		$pfk = '';
		$nhl = '';

		$rutin_rp='';
		$nhl_rp='';
		$gaji_rp='';
		$pfk_rp='';
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){					//UP
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
		}
			
		else if($data->jenisdokumen==1){			//GU
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==2){			//TU
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==3){			//GAJI
			$gaji='x';
			$gaji_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==4) {			//LS
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';

			
		}

		else if($data->jenisdokumen==6) {			//PAD
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';

			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
		} 
		else {
			$pfk='x';
			$pfk_rp = apbd_fn($data->jumlah);
			
		}	
		$rows[]=array(
			array('data' => $n, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fd($data->sp2dtgl), 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->sp2dno, 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->namasingkat, 'width' => '80px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $keperluan, 'width' => '265px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $gaji_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			
		);
	}

	$rows[]=array(
		array('data' => '', 'colspan' => '13','width' => '875px','align'=>'left','style'=>'border-top:1px solid black;font-size:70%;'),
	);

		
	$nb = $batch+1;	
	$rows[]=array(
		array('data' => 'Bersambung ke halaman ' . $nb . '-1','colspan' => '13', 'width' => '875px','align'=>'left','style'=>'border:none;font-size:70%;'),
	);			

	$results=db_query('SELECT d.sp2dno, d.sp2dtgl, d.jumlah, d.keperluan FROM  {dokumen} d INNER JOIN {dokumenregisterall} dr  on d.dokid=dr.dokid WHERE (MONTH(d.sp2dtgl) = :bulan) and dr.batch=:batch'. $where . ' and d.sp2dok=1 ORDER BY d.sp2dtgl,d.sp2dno LIMIT 1',array(':bulan'=>$bulan, ':batch' => $nb));
	
	$n++;	
	foreach($results as $data){	
		$rows[]=array(
			array('data' => 'No: ' . $n . '. ' . $data->sp2dno . ', tanggal ' . apbd_fd($data->sp2dtgl) . ', ' . $data->keperluan . ', sebesar ' .  apbd_fn($data->jumlah) . ',00','colspan' => '13', 'width' => '875px','align'=>'left','style'=>'border:none;font-size:70%;'),
		);			
	}
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));

	return $output;
}


function getlaporanregister_all_batch_terakhir($bulan, $batch){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$header=array(
		array('data' => 'NO',  'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
		array('data' => 'TANGGAL',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NO. SP2D',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJ', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'RUT', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NHL', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'PFK', 'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'SKPD', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'URAIAN', 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJI', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'RUTIN', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NIHIL', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'PFK', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
	);
	
	//Content

	$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk inner join {dokumenregisterall} dr  on d.dokid=dr.dokid WHERE  (MONTH(d.sp2dtgl) = :bulan) and dr.batch=:batch and d.sp2dok=1  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ',array(':bulan'=>$bulan, ':batch' => $batch));
		
	
	$n= ((int) $batch - 1) * 170;

	foreach($results as $data){

		$n++;
		$rutin = '';
		$gaji = '';
		$pfk = '';
		$nhl = '';

		$rutin_rp='';
		$nhl_rp='';
		$gaji_rp='';
		$pfk_rp='';
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){					//UP
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
		}
			
		else if($data->jenisdokumen==1){			//GU
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==2){			//TU
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==3){			//GAJI
			$gaji='x';
			$gaji_rp = apbd_fn($data->jumlah);
			
		}
			
		else if($data->jenisdokumen==4) {			//LS
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';

			
		}

		else if($data->jenisdokumen==6) {			//PAD
			$rutin='x';
			$rutin_rp = apbd_fn($data->jumlah);
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';

			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
		} 
		else {
			$pfk='x';
			$pfk_rp = apbd_fn($data->jumlah);
			
		}	
		$rows[]=array(
			array('data' => $n, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fd($data->sp2dtgl), 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->sp2dno, 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk,'width' => '25px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->namasingkat, 'width' => '80px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $keperluan, 'width' => '265px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $gaji_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk_rp, 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			
		);
	}

	//REKAP
	$sumgaji = 0;
	$sumrutin = 0;
	$sumnhl = 0;
	$sumpfk = 0;
	$total = 0;

	$totgaji = 0;
	$totrutin = 0;
	$totnhl = 0;
	$totpfk = 0;
	
	$results=db_query("SELECT jenisdokumen,COUNT(dokid) AS banyaknya,SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) = :bulan) and sp2dok=1 and sp2dno<>'' GROUP BY jenisdokumen",array(':bulan'=>$bulan));
	
	foreach($results as $data){
		
		$total += $data->total;
		
		if($data->jenisdokumen==0){				//UP
			$sumrutin = $data->banyaknya;
			$totrutin = $data->total;
		}
			
		else if($data->jenisdokumen==1){		//GU
			$sumrutin = $data->banyaknya;
			$totrutin = $data->total;
		}
		else if($data->jenisdokumen==2){		//TU
			$sumrutin = $data->banyaknya;
			$totrutin = $data->total;
		}
			
		else if($data->jenisdokumen==3){		//GAJI
			$sumgaji = $data->banyaknya;
			$totgaji = $data->total;
		}
			
		else if (($data->jenisdokumen==4) or ($data->jenisdokumen==6)) {		//LS PAD
			$sumrutin = $data->banyaknya;
			$totrutin = $data->total;
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$sumnhl = $data->banyaknya;
			$totnhl = $data->total;
		}	
		else if($data->jenisdokumen==8){
			$sumpfk = $data->banyaknya;
			$totpfk = $data->total;
		}
			
	
	}		
	
	$rows[]=array(
			array('data' => 'JUMLAH', 'width' => '110px', 'colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumgaji,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumrutin,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumnhl,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumpfk,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumgaji+$sumrutin+$sumnhl+$sumpfk, 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($total), 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totrutin), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totnhl), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totpfk), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			
		);

	//SEBELUMNYA
	$sumgaji_lalu = 0;
	$sumrutin_lalu = 0;
	$sumnhl_lalu = 0;
	$sumpfk_lalu = 0;
	$total_lalu = 0;

	$totgaji_lalu = 0;
	$totrutin_lalu = 0;
	$totnhl_lalu = 0;
	$totpfk_lalu = 0;
	
	$results=db_query('SELECT jenisdokumen,COUNT(dokid) AS banyaknya,SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (sp2dok=1) GROUP BY jenisdokumen',array(':bulan'=>$bulan));
			
	foreach($results as $data){
		$total_lalu += $data->total;
		
		if($data->jenisdokumen==0){
			$sumrutin_lalu += $data->banyaknya;
			$totrutin_lalu += $data->total;
			
		}
			
		else if($data->jenisdokumen==1){
			$sumrutin_lalu += $data->banyaknya;
			$totrutin_lalu += $data->total;
		}
			
		else if($data->jenisdokumen==2){
			$sumrutin_lalu += $data->banyaknya;
			$totrutin_lalu += $data->total;
		}
			
		else if($data->jenisdokumen==3){
			$sumgaji_lalu = $data->banyaknya;
			$totgaji_lalu = $data->total;
		}
			
		else if(($data->jenisdokumen==4) or ($data->jenisdokumen==6)){
			$sumrutin_lalu += $data->banyaknya;
			$totrutin_lalu += $data->total;
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$sumnhl_lalu = $data->banyaknya;
			$totnhl_lalu += $data->total;
		}	
		else if($data->jenisdokumen==8){
			$sumpfk_lalu += $data->banyaknya;
			$totpfk_lalu += $data->total;
		}
	
	}		

	$rows[]=array(
			array('data' => 'SEBELUMNYA', 'width' => '110px', 'colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumrutin_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumgaji_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumnhl_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumpfk_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => $sumrutin_lalu+$sumgaji_lalu+$sumnhl_lalu+$sumpfk_lalu, 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($total_lalu), 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totrutin_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totnhl_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			array('data' => apbd_fn($totpfk_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:70%;'),
			
		);

	$rows[]=array(
			array('data' => 'KESELURUHAN', 'width' => '110px', 'colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumrutin + $sumrutin_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl + $sumnhl_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumpfk + $sumpfk_lalu,'width' => '25px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => ($sumgaji+$sumrutin+$sumnhl+$sumpfk) + ($sumgaji_lalu+$sumrutin_lalu+$sumnhl_lalu+$sumpfk_lalu), 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total + $total_lalu), 'width' => '265px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji + $totgaji_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totrutin + $totrutin_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totnhl + $totnhl_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totpfk + $totpfk_lalu), 'width' => '80px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);		
	$output = theme('table', array('header' => $header, 'rows' => $rows ));

	return $output;
}

function getlaporanregister($kodeuk, $bulan, $tanggal,$jenisdokumen){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER SP2D', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
	);
	if (!isSuperuser()) {
		$rows[]=array(
			array('data' => $namauk, 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
		);
	}
	$rows[]=array(
		array('data' => 'BULAN ' . $bulan . ' TAHUN ' . apbd_tahun(), 'width' => '875px','align'=>'center','style'=>'border:none;font-size:80%;'),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	$header=array(
		array('data' => 'NO',  'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
		array('data' => 'TANGGAL',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NO. SP2D',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'UP', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GU', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'TU', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GJ', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'LS', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'NH', 'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'SKPD', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'URAIAN', 'width' => '305px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'UP', 'width' => '75px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GU', 'width' => '75px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'TU', 'width' => '75px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJI', 'width' => '75px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'LS', 'width' => '75px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
	);
	$where='';
	if ($jenisdokumen=='ZZ') {
		$where=' and (d.jenisdokumen=0 or d.jenisdokumen=1 or d.jenisdokumen=2 or d.jenisdokumen=3 or d.jenisdokumen=4  or d.jenisdokumen=6 or d.jenisgaji=4)';
		
	} else if ($jenisdokumen=='UP')
		$where=' and d.jenisdokumen=0 ';
	else if ($jenisdokumen=='GU')
		$where=' and d.jenisdokumen=1 ';
	else if ($jenisdokumen=='TU')
		$where=' and d.jenisdokumen=2 ';
	else if ($jenisdokumen=='LS')
		$where=' and d.jenisdokumen=4 ';
	
	else if ($jenisdokumen=='TP') {
		$where=' and d.jenisdokumen=3 and d.jenisgaji=4 ';
		
	}
	else if ($jenisdokumen=='RB') {
		$where=' and d.jenisdokumen=6 ';
		//$query->condition('d.jenisgaji', 4, '=');
	}
	//Content
	if ($bulan=='0') {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.sp2dok=1'.$where.'  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ');
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8)'.$where.' and (d.sp2dok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ');
		}
		
	} else {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.sp2dtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.sp2dok=1  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ',array(':bulan'=>$bulan));
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.sp2dtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and (d.sp2dok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
		}
	}
	$n=0;

	$sumrutin = 0;
	$sumpfk = 0;
	$sumtu = 0;
	$sumgaji = 0;
	$sumls = 0;
	$sumnhl = 0;
	$total = 0;

	$totrutin = 0;
	$totgu = 0;
	$tottu = 0;
	$totgaji = 0;
	$totpfk = 0;
	$totnhl = 0;
	
	foreach($results as $data){

		$n++;
		$rutin='';
		$rutin='';
		$tu='';
		$gaji='';
		$pfk='';
		$nhl='';

		$rutin_rp='';
		$rutin_rp='';
		$nhl_rp='';
		$gaji_rp='';
		$pfk_rp='';
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){
			$rutin='x';
			$sumrutin+=1;
			$rutin_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			$totrutin += $data->jumlah;
		}
			
		else if($data->jenisdokumen==1){
			$rutin='x';
			$sumpfk+=1;
			$rutin_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgu += $data->jumlah;
			
		}
			
		else if($data->jenisdokumen==2){
			$tu='x';
			$sumtu+=1;
			$nhl_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$tottu += $data->jumlah;
			
		}
			
		else if($data->jenisdokumen==3){
			$gaji='x';
			$sumgaji+=1;
			$gaji_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgaji += $data->jumlah;
			
		}
			
		else if(($data->jenisdokumen==4) or ($data->jenisdokumen==6)){
			$pfk='x';
			$sumls+=1;
			$pfk_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totpfk += $data->jumlah;
			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
			//$jumlah = '0';
			
			$keperluan = $data->keperluan . ', sebesar Rp ' . apbd_fn($data->jumlah) . ',00';
		}
			
		$rows[]=array(
			array('data' => $n, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fd($data->sp2dtgl), 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->sp2dno, 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $tu,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->namasingkat, 'width' => '80px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $keperluan, 'width' => '305px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $rutin_rp, 'width' => '75px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $rutin_rp, 'width' => '75px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl_rp, 'width' => '75px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji_rp, 'width' => '75px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $pfk_rp, 'width' => '75px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			
		);
	}
	$rows[]=array(
			array('data' => 'JUMLAH', 'width' => '110px', 'colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumrutin,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumpfk,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumrutin+$sumpfk+$sumtu+$sumgaji+$sumls+$sumnhl, 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total), 'width' => '305px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totrutin), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totpfk), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);

	//SEBELUMNYA
	$sumrutin_lalu = 0;
	$sumpfk_lalu = 0;
	$sumtu_lalu = 0;
	$sumgaji_lalu = 0;
	$sumls_lalu = 0;
	$sumnhl_lalu = 0;
	$total_lalu = 0;

	$totrutin_lalu = 0;
	$totgu_lalu = 0;
	$tottu_lalu = 0;
	$totgaji_lalu = 0;
	$totpfk_lalu = 0;
	$totnhl_lalu = 0;
	
	if($kodeuk == 'ZZ') {
		$results=db_query('SELECT jenisdokumen,COUNT(dokid) AS banyaknya,SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (jenisdokumen<>6 and jenisdokumen<>8) and (sp2dok=1) GROUP BY jenisdokumen',array(':bulan'=>$bulan));
	
	} else {
		$results=db_query('SELECT jenisdokumen,COUNT(dokid) AS banyaknya,SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (jenisdokumen<>6 and jenisdokumen<>8) and (sp2dok=1) and (kodeuk=:kodeuk) GROUP BY jenisdokumen', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
	}
		
	foreach($results as $data){
		
		$total_lalu += $data->total;
		
		if($data->jenisdokumen==0){
			$sumrutin_lalu = $data->banyaknya;
			$totrutin_lalu = $data->total;
		}
			
		else if($data->jenisdokumen==1){
			$sumpfk_lalu = $data->banyaknya;
			$totgu_lalu = $data->total;
			
		}
			
		else if($data->jenisdokumen==2){
			$sumtu_lalu = $data->banyaknya;
			$tottu_lalu = $data->total;
			
		}
			
		else if($data->jenisdokumen==3){
			$sumgaji_lalu = $data->banyaknya;
			$totgaji_lalu = $data->total;
			
		}
			
		else if(($data->jenisdokumen==4) or ($data->jenisdokumen==6)){
			$sumls_lalu = $data->banyaknya;
			$totpfk_lalu = $data->total;
			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$sumnhl_lalu = $data->banyaknya;
		}	
	
	}		

	$rows[]=array(
			array('data' => 'SEBELUMNYA', 'width' => '110px', 'colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumrutin_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumpfk_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumrutin_lalu+$sumpfk_lalu+$sumtu_lalu+$sumgaji_lalu+$sumls_lalu+$sumnhl_lalu, 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total_lalu), 'width' => '305px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totrutin_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totpfk_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);

	$rows[]=array(
			array('data' => 'KESELURUHAN', 'width' => '110px', 'colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumrutin + $sumrutin_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumpfk + $sumpfk_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls + $sumls_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl + $sumnhl_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => ($sumrutin+$sumpfk+$sumtu+$sumgaji+$sumls+$sumnhl) + ($sumrutin_lalu+$sumpfk_lalu+$sumtu_lalu+$sumgaji_lalu+$sumls_lalu+$sumnhl_lalu), 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total + $total_lalu), 'width' => '305px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totrutin + $totrutin_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu + $totgu_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu + $tottu_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji + $totgaji_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totpfk + $totpfk_lalu), 'width' => '75px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);		
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	if ($kodeuk=='ZZ') {
		$rows[] = array(
						array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
						array('data' => 'Jepara, ' . $tanggal,'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
				);
		$rows[] = array(
						array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
						array('data' => apbd_bud_jabatan(),'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
				);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
		
		$rows[] = array(
						array('data' => '', 'width' => '435px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:70%;'),
						array('data' => apbd_bud_nama(), 'width' => '440px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:70%;'),
		);
		$rows[] = array(
						array('data' => '', 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
						array('data' => 'NIP. ' . apbd_bud_nip(), 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
	
	} else {
		$results=db_query('SELECT kodeuk,namauk,bendaharanama,bendaharanip,pimpinannama,pimpinannip,pimpinanjabatan FROM {unitkerja} WHERE kodeuk=:kodeuk', array(':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$namauk = $data->namauk; 
			
			$bendaharanama = $data->bendaharanama; 
			$bendaharanip = $data->bendaharanip;
			$pimpinanjabatan = $data->pimpinanjabatan;			
			$pimpinannama = $data->pimpinannama; 
			$pimpinannip = $data->pimpinannip; 
		}	

		$rows[] = array(
						array('data' => 'Mengetahui,','width' => '435px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
						array('data' => 'Jepara, ' . $tanggal,'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
				);
		$rows[] = array(
						array('data' => $pimpinanjabatan,'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
						array('data' => 'BENDAHARA PENGELUARAN','width' => '440px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
				);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);
		
		$rows[] = array(
						array('data' => $pimpinannama, 'width' => '435px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:70%;'),
						array('data' => $bendaharanama, 'width' => '440px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:70%;'),
		);
		$rows[] = array(
						array('data' => 'NIP. ' . $pimpinannip, 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
						array('data' => 'NIP. ' . $bendaharanip, 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:70%;'),
		);		
		
	}
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

function getlaporanregister_all_backup($kodeuk, $bulan, $tanggal,$jenisdokumen){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER SP2D', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
	);
	if (!isSuperuser()) {
		$rows[]=array(
			array('data' => $namauk, 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
		);
	}
	$rows[]=array(
		array('data' => 'BULAN ' . $bulan . ' TAHUN ' . apbd_tahun(), 'width' => '875px','align'=>'center','style'=>'border:none;font-size:80%;'),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	$header=array(
		array('data' => 'NO',  'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
		array('data' => 'TANGGAL',  'width' => '60px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'NO. SP2D',  'width' => '60px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'UP', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'GU', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'TU', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'GAJI', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'LS', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'NHL', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'SKPD', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'URAIAN', 'width' => '345px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
		array('data' => 'JUMLAH', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
	);
	$where='';
	if ($jenisdokumen=='ZZ') {
		$where=' and (d.jenisdokumen=0 or d.jenisdokumen=1 or d.jenisdokumen=2 or d.jenisdokumen=3 or d.jenisdokumen=4  or d.jenisdokumen=6 or d.jenisgaji=4)';
		
	} else if ($jenisdokumen=='UP')
		$where=' and d.jenisdokumen=0 ';
	else if ($jenisdokumen=='GU')
		$where=' and d.jenisdokumen=1 ';
	else if ($jenisdokumen=='TU')
		$where=' and d.jenisdokumen=2 ';
	else if ($jenisdokumen=='LS')
		$where=' and d.jenisdokumen=4 ';
	
	else if ($jenisdokumen=='TP') {
		$where=' and d.jenisdokumen=3 and d.jenisgaji=4 ';
		
	}
	else if ($jenisdokumen=='RB') {
		$where=' and d.jenisdokumen=6 ';
		//$query->condition('d.jenisgaji', 4, '=');
	}
	//Content
	if ($bulan=='0') {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.sp2dok=1'.$where.'  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ');
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8)'.$where.' and (d.sp2dok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ');
		}
		
	} else {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.sp2dtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.sp2dok=1  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ',array(':bulan'=>$bulan));
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.sp2dtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and (d.sp2dok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
		}
	}
	$n=0;

	$sumrutin = 0;
	$sumpfk = 0;
	$sumtu = 0;
	$sumgaji = 0;
	$sumls = 0;
	$sumnhl = 0;
	$total = 0;

	$totrutin = 0;
	$totgu = 0;
	$tottu = 0;
	$totgaji = 0;
	$totpfk = 0;
	$totnhl = 0;
	
	foreach($results as $data){

		$n++;
		$rutin='';
		$rutin='';
		$tu='';
		$gaji='';
		$pfk='';
		$nhl='';
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){
			$rutin='x';
			$sumrutin+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			$totrutin += $data->jumlah;
		}
			
		else if($data->jenisdokumen==1){
			$rutin='x';
			$sumpfk+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgu += $data->jumlah;
			
		}
			
		else if($data->jenisdokumen==2){
			$tu='x';
			$sumtu+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$tottu += $data->jumlah;
			
		}
			
		else if($data->jenisdokumen==3){
			$gaji='x';
			$sumgaji+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgaji += $data->jumlah;
			
		}
			
		else if(($data->jenisdokumen==4) or ($data->jenisdokumen==6)){
			$pfk='x';
			$sumls+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totpfk += $data->jumlah;
			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
			$jumlah = '0';
			
			$keperluan = $data->keperluan . ', sebesar Rp ' . apbd_fn($data->jumlah) . ',00';
		}
			
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fd($data->sp2dtgl), 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $data->sp2dno, 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $rutin,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $rutin,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $tu,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $gaji,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $pfk,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $nhl,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $data->namasingkat, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => $keperluan, 'width' => '345px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => $jumlah, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			
		);
	}
	$rows[]=array(
			array('data' => 'BANYAKNYA SP2D', 'width' => '150px', 'rows-span'=>'3', 'align'=>'right','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumrutin,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumpfk,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumtu,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumgaji,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumls,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumnhl,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => 'JUMLAH NOMINAL SP2D', 'width' => '445px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => apbd_fn($total), 'width' => '100px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			
		);
		
	
	/*
	$totrutin = 0;
	$totgu = 0;
	$tottu = 0;
	$totgaji = 0;
	$totpfk = 0;
	*/
	
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => '- Uang Persediaan (UP) ', 'width' => '345px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fn($totrutin), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			
		);
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => '- Ganti Uang (GU) ', 'width' => '345px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fn($totgu), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			
		);
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => '- Tambahan Uang (TU) ', 'width' => '345px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fn($tottu), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			
		);
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => '- Gaji ', 'width' => '345px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fn($totgaji), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
		);
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => '- Langsung (LS) ', 'width' => '345px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:80%;'),
			array('data' => apbd_fn($totpfk), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
			
		);
	
	//SEBELUMNYA
	if($kodeuk == 'ZZ') {
		$results=db_query('SELECT SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (jenisdokumen<>5 and jenisdokumen<>6 and jenisdokumen<>7 and jenisdokumen<>8) and (sp2dok=1)',array(':bulan'=>$bulan));
	
	} else {
		$results=db_query('SELECT SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (jenisdokumen<>5 and jenisdokumen<>6 and jenisdokumen<>7 and jenisdokumen<>8) and (sp2dok=1) and (kodeuk=:kodeuk)', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
	}
		
	foreach($results as $data){
		$totallalu = $data->total;
	}
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => 'JUMLAH SP2D SEBELUMNYA', 'width' => '445px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => apbd_fn($totallalu), 'width' => '100px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			
		);
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => 'JUMLAH SP2D KESELURUHAN', 'width' => '445px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => apbd_fn($totallalu + $total), 'width' => '100px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			
		);
		
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	if ($kodeuk=='ZZ') {
		$rows[] = array(
						array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
						array('data' => 'Jepara, ' . $tanggal,'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
				);
		$rows[] = array(
						array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
						array('data' => apbd_bud_jabatan(),'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
				);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
		
		$rows[] = array(
						array('data' => '', 'width' => '435px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:80%;'),
						array('data' => apbd_bud_nama(), 'width' => '440px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:80%;'),
		);
		$rows[] = array(
						array('data' => '', 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
						array('data' => 'NIP. ' . apbd_bud_nip(), 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
	
	} else {
		$results=db_query('SELECT kodeuk,namauk,bendaharanama,bendaharanip,pimpinannama,pimpinannip,pimpinanjabatan FROM {unitkerja} WHERE kodeuk=:kodeuk', array(':kodeuk'=>$kodeuk));
		foreach($results as $data){
			$namauk = $data->namauk; 
			
			$bendaharanama = $data->bendaharanama; 
			$bendaharanip = $data->bendaharanip;
			$pimpinanjabatan = $data->pimpinanjabatan;			
			$pimpinannama = $data->pimpinannama; 
			$pimpinannip = $data->pimpinannip; 
		}	

		$rows[] = array(
						array('data' => 'Mengetahui,','width' => '435px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
						array('data' => 'Jepara, ' . $tanggal,'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
				);
		$rows[] = array(
						array('data' => $pimpinanjabatan,'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
						array('data' => 'BENDAHARA PENGELUARAN','width' => '440px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
				);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
		$rows[] = array(
						array('data' => '','width' => '875px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);
		
		$rows[] = array(
						array('data' => $pimpinannama, 'width' => '435px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:80%;'),
						array('data' => $bendaharanama, 'width' => '440px', 'align'=>'center','style'=>'border:none;text-decoration: underline;font-size:80%;'),
		);
		$rows[] = array(
						array('data' => 'NIP. ' . $pimpinannip, 'width' => '435px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
						array('data' => 'NIP. ' . $bendaharanip, 'width' => '440px', 'align'=>'center','style'=>'border:none;font-size:80%;'),
		);		
		
	}
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

function apbd_register_all_paging($bulan) {
	
	//drupal_set_message($bulan);
	
	$num = db_delete('dokumenregisterall')
	  ->condition('bulan', $bulan)
	  ->execute();
	
	$batch = 1;
	$i  = 0;
	$batas = 170;
	
	$res_keg = db_query('select dokid from {dokumen} where sp2dok=1 and month(sp2dtgl)=:bulan order by sp2dtgl,sp2dno', array(':bulan' => $bulan));
	foreach ($res_keg as $datakeg) {		
		
		$i++;

		$query = db_insert('dokumenregisterall') // Table name no longer needs {}
			->fields(array(
				  'dokid' => $datakeg->dokid,
				  'batch' => $batch,
				  'bulan' => $bulan,					  
			))
			->execute();	
		
		if ($i == $batas) {
			$batch++ ;
			$i = 0;
		}	
			
	}	

}

function _load_item($bulan) {
	$_SESSION["register-sp2d-lastbulan"] = $bulan;
	
	$i = 0;
	$res_page = db_query('select distinct batch from {dokumenregisterall} where bulan=:bulan order by batch desc', array(':bulan' => $bulan));
	foreach ($res_page as $data) {
		if ($i==0) {
			$strmenuc = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/last/cetak" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Tampilkan Bagian Terakhir</a>';
			$strmenu = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/last/pdf" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Cetak Bagian Terakhir (PDF)</a>';
			$strmenux = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/last/excel" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Simpan Bagian Terakhir (Excel)</a>';
			
			$i++;
			
		} else {
			$strmenuc = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/no/cetak" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Tampilkan Bagian ke-' . $data->batch . '</a>' . $strmenuc;
			$strmenu = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/no/pdf" target="_blank" class="list-group-item glyphicon glyphicon-print"> Cetak Bagian ke-' . $data->batch . ' (PDF)</a>' . $strmenu;
			$strmenux = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/no/excel" target="_blank" class="list-group-item glyphicon glyphicon-floppy-disk"> Simpan Bagian ke-' . $data->batch . ' (Excel)</a>' . $strmenux;
			
		}
	}


	$reportitem	= '<div class="list-group col-md-4">' . $strmenuc . 
				'</div><div class="list-group col-md-4">' . $strmenu . 
				'</div><div class="list-group col-md-4">' . $strmenux . 
				'</div>'; 

	return $reportitem;	
	
	/*
	$_SESSION["register-sp2d-lastbulan"] = $bulan;
	
	$i = 0;
	$res_page = db_query('select distinct batch from {dokumenregisterall} where bulan=:bulan order by batch desc', array(':bulan' => $bulan));
	foreach ($res_page as $data) {
		if ($i==0) {
			$strmenu = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/last/pdf" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Bagian Terakhir PDF</a>';
			$strmenux = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/last/excel" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Bagian Terakhir Excel</a>';
			
			$i++;
			
		} else {
			$strmenu = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/no/pdf" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Bagian ke-' . $data->batch . ' PDF</a>' . $strmenu;
			$strmenux = '<a href="/cetakregisterall/' . $bulan . '/' . $data->batch . '/no/excel" target="_blank" class="list-group-item glyphicon glyphicon-th-large"> Bagian ke-' . $data->batch . ' Excel</a>' . $strmenux;
			
		}
	}


	$reportitem	= '<div class="list-group col-md-6">' . $strmenu . 
				'</div><div class="list-group col-md-6">' . $strmenux . 
				'</div>';

	return $reportitem;	
	*/
}

function print_laporan_register($htmlContent, $pdfFiel, $marginatas) {
    require_once('files/tcpdf/config/lang/eng.php');
    require_once('files/tcpdf/tcpdf.php');

	class MYPDF extends TCPDF {  
	   // Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			//$this->SetY(-10);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			
			// Page number = 
		    $batch = $_SESSION["register-sp2d-batch"]; 
			$this->Cell(0, 0, $batch . '-' . $this->PageNo(), 'T', 0, 'R');
		}      
	} 
	
    $pdf = new MYPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);
    set_time_limit(0);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SIPPD');
    $pdf->SetTitle('PDF Gen');
    $pdf->SetSubject('PDF Gen');
    $pdf->SetKeywords('APBD');
    $pdf->setPrintHeader(false);
    $pdf->setFooterFont(array('helvetica','', 9));
    $pdf->setFooterMargin(10);
	$pdf->setRightMargin(1);

	//$pdf->setHeaderMargin(20);
	//$pdf->SetMargins(10,20);

	$pdf->setHeaderMargin(20);
	$pdf->SetMargins(10, $marginatas);
	
	//$pdf->SetMargins(15,15);
    $pdf->SetAutoPageBreak(true, 11);
    $pdf->setLanguageArray($l);
    //$pdf->SetFont('helvetica','', 20);
    $pdf->AddPage();
    $pdf->writeHTML($htmlContent, true, 0, true, 0);
	

    $pdf->Output($pdfFiel, 'I');
	
}


?>