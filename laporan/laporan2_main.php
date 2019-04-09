<?php
function laporan2_main($arg=NULL, $nama=NULL) {
	if(arg(8)=='pdf'){	

		$kodeuk = arg(1);
		$bulan = arg(2);
		$sumberdana = arg(3);
		$jenisdokumen=arg(4);
		$margin = arg(5);
		$tanggal = arg(6);
		
		$output = getlaporan2($kodeuk, $bulan, $tanggal,$jenisdokumen);
		apbd_ExportPDF('P', 'F4', $output, 'Register SPP.pdf');

		//$output_form = drupal_get_form('laporan2_main_form');
		//return drupal_render($output_form) . $output;// . $output;
		
	} else if(arg(8) == 'excel'){
		$output = getlaporan2($kodeuk, $bulan, $tanggal,$jenisdokumen);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Register_SPP.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
	} else if(arg(8) == 'cetak'){
		$output = getlaporan2html($kodeuk, $bulan, $tanggal,$jenisdokumen);
		
		return $output;
	} else {
	
		$output_form = drupal_get_form('laporan2_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function laporan2_main_form($form, &$form_state) {

	$kodeuk = arg(1);
	$bulan = arg(2);
	$sumberdana = arg(3);
	$jenisdokumen=arg(4);
	$margin = arg(5);
	$tanggal = arg(6);
	
	
	if ($kodeuk=='') $kodeuk = 'ZZ';
	if ($bulan=='') {
		$bulan = $_SESSION["register-spp-lastbulan"];
		if ($bulan=='') $bulan = date('n');
	}
	if ($margin=='') $margin = '10';
	if ($tanggal=='') $tanggal = date('j F Y');
	if ($jenisdokumen=='') $jenisdokumen = 'ZZ';
	if ($sumberdana=='') $sumberdana = 'ZZ';
	
	
	$form['margin']= array(
		'#title' => 'Margin Atas',
		'#type' => 'textfield',
		'#default_value' => $margin,
	);	

	if (isSuperuser()) {
		
		$opt_skpd['ZZ'] = 'SELURUH SKPD'; 
		$results=db_query('SELECT kodeuk,namasingkat  FROM  {unitkerja} ORDER BY kodedinas');
		foreach($results as $data){
			$opt_skpd[$data->kodeuk] = $data->namasingkat; 
			$form['kodeuk']= array(
				'#type' => 'select',
				'#title' => 'OPD',
				'#options' => $opt_skpd,
				'#default_value'=> $kodeuk, 
			);	
		}	
		
	} else {
		$form['kodeuk']= array(
			'#type' => 'value',
			'#value' => apbd_getuseruk(),
		);	
	}
		
	$opt_jenisdokumen['ZZ'] ='SEMUA';
	$opt_jenisdokumen['UP'] = 'UANG PERSEDIAAN';
	$opt_jenisdokumen['TU'] = 'TAMBAHAN UANG PERSEDIAAN';	
	$opt_jenisdokumen['GU'] = 'GANTI UANG PERSEDIAAN';	
	$opt_jenisdokumen['LS'] = 'BARANG DAN JASA';	
	$opt_jenisdokumen['GJ'] = 'G A J I';
	$opt_jenisdokumen['TP'] = 'TAMBAHAN PENGHASILAN';	
	$opt_jenisdokumen['RB'] = 'RESTITUSI';
	$opt_jenisdokumen['PF'] = 'P F K';	
	$form['formdata']['jenisdokumen'] = array(
		'#type' => 'select',
		'#title' =>  t('JENIS SPP'),
		'#options' => $opt_jenisdokumen,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisdokumen,
	);	 
	
	$arr_bulan=array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['bulan']= array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#options' => $arr_bulan,
		'#default_value'=> $bulan, 
	);	

	$opt_sumberdana['ZZ'] ='SEMUA';
	$opt_sumberdana['BANPROV'] = 'BANPROV';
	$opt_sumberdana['BANTUAN KHUSUS'] = 'BANTUAN KHUSUS';	
	$opt_sumberdana['BOS'] = 'BOS';	
	$opt_sumberdana['DAK'] = 'DAK';	
	$opt_sumberdana['DAU'] = 'DAU';	
	$opt_sumberdana['DBH'] = 'DBH';	
	$opt_sumberdana['DBH CHT'] = 'DBH CHT';	
	$opt_sumberdana['LAIN-LAIN PENDAPATAN'] = 'LAIN-LAIN PENDAPATAN';	
	$opt_sumberdana['PAD'] = 'PAD';	
	$form['formdata']['sumberdana'] = array(
		'#type' => 'select',
		'#title' =>  t('SUMBER DANA'),
		'#options' => $opt_sumberdana,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sumberdana,
	);	 

	$form['tanggal']= array(
		'#type'         => 'textfield', 
		'#title' =>  t('Tanggal'),
		//'#required' => TRUE,
		'#default_value'=> $tanggal, 
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

function laporan2_main_form_validate($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$sumberdana = $form_state['values']['sumberdana'];
	
	if (($kodeuk=='ZZ')	and ($jenisdokumen=='ZZ') and ($sumberdana=='ZZ')) {
		form_set_error('kodeuk', 'Tidak bisa menampilkan seluruh SKPD untuk semua sumberdana dan untuk semua jenis SPP, salah satu pilihan harus spesifik');
	}
}
	
function laporan2_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$tanggal = $form_state['values']['tanggal'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$sumberdana = $form_state['values']['sumberdana'];

	
	$_SESSION["register-spp-lastbulan"] = $bulan;

	if($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']){
		drupal_goto('laporan/laporan2_main/' . $kodeuk . '/' . $bulan . '/' . $sumberdana . '/' . $jenisdokumen . '/' . $margin . '/'. $tanggal . '/pdf');
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['cetak']){
		drupal_goto('laporan/laporan2_main/' . $kodeuk . '/' . $bulan . '/' . $sumberdana . '/' . $jenisdokumen . '/' . $margin . '/'. $tanggal . '/cetak');
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']){
		drupal_goto('laporan/laporan2_main/' . $kodeuk . '/' . $bulan . '/' . $sumberdana . '/' . $jenisdokumen . '/' . $margin . '/'. $tanggal . '/excel');
	}

}

function getlaporan2($kodeuk, $bulan, $tanggal,$jenisdokumen){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$where = '';
	$labeljenis = '';
	if ($jenisdokumen=='ZZ') {
		$where=' and (d.jenisdokumen<3) ';
		
	}
	
	$header=array();
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	
	$header=array(
		array('data' => 'No. Urut', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'TANGGAL', 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'NO. SP2D', 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'SKPD', 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'URAIAN', 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'JUMLAH','width' => '80px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
	);

	//Content
	if ($bulan=='0') {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.sppok=1) '. $where . '  ORDER BY d.spptgl ASC,d.sppno ASC ');
		
		} else {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.sppok=1) '. $where . '  and (d.kodeuk=:kodeuk)  ORDER BY d.spptgl ASC,d.sppno ASC ');
		}
		
	} else {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.spptgl) = :bulan)' . $where . ' and d.sppok=1  ORDER BY d.spptgl ASC,d.sppno ASC ',array(':bulan'=>$bulan));
		
		} else {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.spptgl) = :bulan) ' . $where . ' and (d.sppok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.spptgl ASC,d.sppno ASC ', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
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
		
		$sp2dtgl = $data->sp2dtgl;
		$sp2dno = $data->sp2dno;
		$namasingkat = $data->namasingkat;
		$keperluan = $data->keperluan;
		$jumlah = $data->jumlah;
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen<3){
			$up='x';
			$sumup+=1;
			$up_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			$totup += $data->jumlah;
		}
			
			$rows[]=array(
				array('data' => $n, 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black; font-size:20px; border-bottom: 1px solid black;'),
				array('data' => $sp2dtgl, 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black; font-size:20px; border-bottom: 1px solid black;'),
				array('data' => $sp2dno, 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
				array('data' => $namasingkat, 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
				array('data' => $keperluan, 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
				array('data' => $jumlah, 'width' => '80px','align'=>'center','style'=>'border-left:1px solid black; border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
			);
			
	}
	if (arg(8) == 'excel') {
	$rows[]=array(
			array('data' => 'JUMLAH', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => '','width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
		);
	}else{
	$rows[]=array(
			array('data' => 'JUMLAH', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
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
		$results=db_query('SELECT d.jenisdokumen,COUNT(d.dokid) AS banyaknya,SUM(d.jumlah) AS total FROM  {dokumen} d WHERE  (MONTH(d.spptgl) < :bulan) ' . $where . ' and (d.sppok=1) GROUP BY d.jenisdokumen',array(':bulan'=>$bulan));
	
	} else {
		$results=db_query('SELECT d.jenisdokumen,COUNT(d.dokid) AS banyaknya,SUM(d.jumlah) AS total FROM  {dokumen} d WHERE  (MONTH(d.spptgl) < :bulan) ' . $where . ' and (d.sppok=1) and (d.kodeuk=:kodeuk) GROUP BY d.jenisdokumen', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
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
		else if($data->jenisdokumen==8){
			$sumgaji_lalu = $data->banyaknya;
			$totgaji_lalu = $data->total;
			
		}
	
	}		
	if (arg(8) == 'excel') {
	$rows[]=array(
			array('data' => 'SEBELUMNYA', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => '','width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
		);

	$rows[]=array(
			array('data' => 'KESELURUHAN', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => '','width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup + $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu + $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
		);		
	}else{
	$rows[]=array(
			array('data' => 'SEBELUMNYA', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
		);

	$rows[]=array(
			array('data' => 'KESELURUHAN', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup + $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu + $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
		);		
	}
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
		return $output;
}

function getlaporan2html($kodeuk, $bulan, $tanggal,$jenisdokumen){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$where = '';
	$labeljenis = '';
	if ($jenisdokumen=='ZZ') {
		$where=' and (d.jenisdokumen<3) ';
		
	}
	
	$header=array();
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
	
	$header=array(
		array('data' => 'No. Urut', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'TANGGAL', 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'NO. SP2D', 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'SKPD', 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'URAIAN', 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
		array('data' => 'JUMLAH','width' => '80px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black; font-size:20px; border-bottom: 1px solid black;'),
	);

	//Content
	if ($bulan=='0') {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.sppok=1) '. $where . '  ORDER BY d.spptgl ASC,d.sppno ASC ');
		
		} else {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.sppok=1) '. $where . '  and (d.kodeuk=:kodeuk)  ORDER BY d.spptgl ASC,d.sppno ASC ');
		}
		
	} else {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.spptgl) = :bulan)' . $where . ' and d.sppok=1  ORDER BY d.spptgl ASC,d.sppno ASC ',array(':bulan'=>$bulan));
		
		} else {
			$results=db_query('SELECT d.dokid, d.sp2dtgl, d.sp2dno, d.spptgl, d.sppno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.sppok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.spptgl) = :bulan) ' . $where . ' and (d.sppok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.spptgl ASC,d.sppno ASC ', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
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
		
		$sp2dtgl = $data->sp2dtgl;
		$sp2dno = $data->sp2dno;
		$namasingkat = $data->namasingkat;
		$keperluan = $data->keperluan;
		$jumlah = $data->jumlah;
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen<3){
			$up='x';
			$sumup+=1;
			$up_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			$totup += $data->jumlah;
		}
			
			$rows[]=array(
				array('data' => $n, 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black; font-size:20px; border-bottom: 1px solid black;'),
				array('data' => $sp2dtgl, 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black; font-size:20px; border-bottom: 1px solid black;'),
				array('data' => $sp2dno, 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
				array('data' => $namasingkat, 'width' => '90px','align'=>'center','style'=>'border-left:1px solid black;border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
				array('data' => $keperluan, 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
				array('data' => $jumlah, 'width' => '80px','align'=>'center','style'=>'border-left:1px solid black; border-right: 1px solid black; font-size:20px;border-bottom: 1px solid black; '),
			);
			
	}
	if (arg(8) == 'excel') {
	$rows[]=array(
			array('data' => ' &nbsp ', 'width' => '20px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => 'JUMLAH', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => '','width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
		);
	}else{
	$rows[]=array(
			array('data' => ' &nbsp ', 'width' => '20px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => 'JUMLAH', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
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
		$results=db_query('SELECT d.jenisdokumen,COUNT(d.dokid) AS banyaknya,SUM(d.jumlah) AS total FROM  {dokumen} d WHERE  (MONTH(d.spptgl) < :bulan) ' . $where . ' and (d.sppok=1) GROUP BY d.jenisdokumen',array(':bulan'=>$bulan));
	
	} else {
		$results=db_query('SELECT d.jenisdokumen,COUNT(d.dokid) AS banyaknya,SUM(d.jumlah) AS total FROM  {dokumen} d WHERE  (MONTH(d.spptgl) < :bulan) ' . $where . ' and (d.sppok=1) and (d.kodeuk=:kodeuk) GROUP BY d.jenisdokumen', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
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
		else if($data->jenisdokumen==8){
			$sumgaji_lalu = $data->banyaknya;
			$totgaji_lalu = $data->total;
			
		}
	
	}		
	if (arg(8) == 'excel') {
	$rows[]=array(
			array('data' => ' &nbsp ', 'width' => '20px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => 'SEBELUMNYA', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => '','width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
		);

	$rows[]=array(
			array('data' => ' &nbsp ', 'width' => '20px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => 'KESELURUHAN', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => '','width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup + $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu + $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
		);		
	}else{
	$rows[]=array(
			array('data' => ' &nbsp ', 'width' => '20px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => 'SEBELUMNYA', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			
		);

	$rows[]=array(
			array('data' => ' &nbsp ', 'width' => '20px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => 'KESELURUHAN', 'width' => '120px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumup + $sumup_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgu + $sumgu_lalu,'width' => '90px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumtu + $sumtu_lalu,'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
			array('data' => $sumgaji + $sumgaji_lalu,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-size:20px;'),
		);		
	}
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	$header=null;
	$rows=null;
		return $output;
}



?>