<?php
function register_koreksi_main($arg=NULL, $nama=NULL) {
	$bulan = arg(1);	
	$marginatas = 10;
	$output = getlaporanregister('ZZ', $bulan, $tanggal,'ZZ');
	if(arg(2)=='pdf'){
		//apbd_ExportPDF('L', 'F4', $output, 'Register SP2D');
		print_laporan_register($output, 'Register SP2D - ' . $batch, $marginatas);

		//$output_form = drupal_get_form('register_koreksi_main_form');
		//return drupal_render($output_form) . $output;// . $output;
		
	} else if(arg(2) == 'excel'){

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Register_SP2D-$batch.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	} else if(arg(2) == 'cetak'){
		$output_form = drupal_get_form('register_koreksi_main_form');
		return drupal_render($output_form) . $output;
	} else {
	
		
		$output_form = drupal_get_form('register_koreksi_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function register_koreksi_main_form($form, &$form_state) {

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
	);	
	$form['cetak']= array(
		'#type' => 'submit',
		'#value' => 'CETAK',
	);
	$form['cetakpdf']= array(
		'#type' => 'submit',
		'#value' => 'PDF',
	);

	$form['cetakexcel']= array(
		'#type' => 'submit',
		'#value' => 'EXCEL',
	);		
	
	return $form;
}

function register_koreksi_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function register_koreksi_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	if($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']){
		drupal_goto('cetakregisterkoreksi/' . $bulan . '/pdf');
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['cetak']){
		drupal_goto('cetakregisterkoreksi/' . $bulan . '/cetak');
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']){
		drupal_goto('cetakregisterkoreksi/' . $bulan . '/excel');
	}

}


function getlaporanregister($kodeuk, $bulan, $tanggal,$jenisdokumen){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER KOREKSI SP2D', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
	);
	if (!isSuperuser()) {
		$rows[]=array(
			array('data' => $namauk, 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
		);
	}
	if ($bulan=='0')
		$rows[]=array(
			array('data' => 'TAHUN ' . apbd_tahun(), 'width' => '875px','align'=>'center','style'=>'border:none;font-size:80%;'),
		);
	else
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
		array('data' => 'SKPD', 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'URAIAN', 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'UP', 'width' => '65px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GU', 'width' => '65px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'TU', 'width' => '65px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'GAJI', 'width' => '65px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => 'LS', 'width' => '65px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
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
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.sp2dok=2'.$where.'  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ');
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8)'.$where.' and (d.sp2dok=2) and (d.kodeuk=:kodeuk)  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ');
		}
		
	} else {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.sp2dtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.sp2dok=2  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ',array(':bulan'=>$bulan));
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.sp2dno, d.sp2dno, d.sp2dtgl, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sp2dok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.sp2dtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and (d.sp2dok=2) and (d.kodeuk=:kodeuk)  ORDER BY d.sp2dtgl ASC,d.sp2dno ASC ', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
		}
	}
	$n=0;

	$sumup = 0;
	$sumgu = 0;
	$sumtu = 0;
	$sumgaji = 0;
	$sumls = 0;
	$sumnhl = 0;
	$total = 0;

	$totup = 0;
	$totgu = 0;
	$tottu = 0;
	$totgaji = 0;
	$totls = 0;
	$totnhl = 0;
	
	foreach($results as $data){

		$n++;
		$up='';
		$gu='';
		$tu='';
		$gaji='';
		$ls='';
		$nhl='';

		$up_rp='';
		$gu_rp='';
		$tu_rp='';
		$gaji_rp='';
		$ls_rp='';
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){
			$up='x';
			$sumup+=1;
			$up_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			$totup += $data->jumlah;
		}
			
		else if($data->jenisdokumen==1){
			$gu='x';
			$sumgu+=1;
			$gu_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgu += $data->jumlah;
			
		}
			
		else if($data->jenisdokumen==2){
			$tu='x';
			$sumtu+=1;
			$tu_rp = apbd_fn($data->jumlah);
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
			$ls='x';
			$sumls+=1;
			$ls_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totls += $data->jumlah;
			
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
			array('data' => $up,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gu,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $tu,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $ls,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $nhl,'width' => '20px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->namasingkat, 'width' => '70px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $keperluan, 'width' => '250px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => $up_rp, 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gu_rp, 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $tu_rp, 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $gaji_rp, 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $ls_rp, 'width' => '65px','align'=>'right','style'=>'border-right:1px solid black;font-size:70%;'),
			
		);
	}
	if (arg(2) == 'excel') {
	$rows[]=array(
			array('data' => 'JUMLAH', 'colspan' => '3','width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => '','width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => '','width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup+$sumgu+$sumtu+$sumgaji+$sumls+$sumnhl, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total), 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totup), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totls), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);
	
	}else{
	$rows[]=array(
			array('data' => 'JUMLAH', 'colspan' => '3','width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup+$sumgu+$sumtu+$sumgaji+$sumls+$sumnhl, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total), 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totup), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totls), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);
	}
	//SEBELUMNYA
	$sumup_lalu = 0;
	$sumgu_lalu = 0;
	$sumtu_lalu = 0;
	$sumgaji_lalu = 0;
	$sumls_lalu = 0;
	$sumnhl_lalu = 0;
	$total_lalu = 0;

	$totup_lalu = 0;
	$totgu_lalu = 0;
	$tottu_lalu = 0;
	$totgaji_lalu = 0;
	$totls_lalu = 0;
	$totnhl_lalu = 0;
	
	if($kodeuk == 'ZZ') {
		$results=db_query('SELECT jenisdokumen,COUNT(dokid) AS banyaknya,SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (jenisdokumen<>6 and jenisdokumen<>8) and (sp2dok=2) GROUP BY jenisdokumen',array(':bulan'=>$bulan));
	
	} else {
		$results=db_query('SELECT jenisdokumen,COUNT(dokid) AS banyaknya,SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(sp2dtgl) < :bulan) and (jenisdokumen<>6 and jenisdokumen<>8) and (sp2dok=2) and (kodeuk=:kodeuk) GROUP BY jenisdokumen', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
	}
		
	foreach($results as $data){
		
		$total_lalu += $data->total;
		
		if($data->jenisdokumen==0){
			$sumup_lalu = $data->banyaknya;
			$totup_lalu = $data->total;
		}
			
		else if($data->jenisdokumen==1){
			$sumgu_lalu = $data->banyaknya;
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
			$totls_lalu = $data->total;
			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$sumnhl_lalu = $data->banyaknya;
		}	
	
	}		
	if (arg(2) == 'excel') {
	$rows[]=array(
			array('data' => 'SEBELUMNYA', 'colspan' => '3','width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => '','width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => '','width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup_lalu+$sumgu_lalu+$sumtu_lalu+$sumgaji_lalu+$sumls_lalu+$sumnhl_lalu, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total_lalu), 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totup_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totls_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);

	$rows[]=array(
			array('data' => 'KESELURUHAN', 'colspan' => '3','width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => '','width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => '','width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup + $sumup_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgu + $sumgu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls + $sumls_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl + $sumnhl_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => ($sumup+$sumgu+$sumtu+$sumgaji+$sumls+$sumnhl) + ($sumup_lalu+$sumgu_lalu+$sumtu_lalu+$sumgaji_lalu+$sumls_lalu+$sumnhl_lalu), 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total + $total_lalu), 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totup + $totup_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu + $totgu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu + $tottu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji + $totgaji_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totls + $totls_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);		
	}else{
	$rows[]=array(
			array('data' => 'SEBELUMNYA', 'colspan' => '3','width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup_lalu+$sumgu_lalu+$sumtu_lalu+$sumgaji_lalu+$sumls_lalu+$sumnhl_lalu, 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total_lalu), 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totup_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totls_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);

	$rows[]=array(
			array('data' => 'KESELURUHAN', 'colspan' => '3','width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumup + $sumup_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgu + $sumgu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumls + $sumls_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => $sumnhl + $sumnhl_lalu,'width' => '20px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => ($sumup+$sumgu+$sumtu+$sumgaji+$sumls+$sumnhl) + ($sumup_lalu+$sumgu_lalu+$sumtu_lalu+$sumgaji_lalu+$sumls_lalu+$sumnhl_lalu), 'width' => '70px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($total + $total_lalu), 'width' => '250px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totup + $totup_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgu + $totgu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($tottu + $tottu_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totgaji + $totgaji_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			array('data' => apbd_fn($totls + $totls_lalu), 'width' => '65px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
			
		);		
	}
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