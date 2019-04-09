<?php
function register_spm_main($arg=NULL, $nama=NULL) {
	$kodeuk = arg(1);
	$bulan = arg(2);
	$sumberdana = arg(3);
	$jenisdokumen=arg(4);
	$margin = arg(5);
	$tanggal = arg(6);
	
	
	if (arg(1)=='kegiatan') {
		$kodekeg = arg(2);
		
		$output = getlaporanregisterkegiatan($kodekeg);
		apbd_ExportPDF('L', 'F4', $output, 'Register SPM.pdf');
	
	} else if(arg(7)=='pdf'){	

		
		$output = getlaporanregister($kodeuk, $sumberdana, $bulan, $tanggal,$jenisdokumen);
		apbd_ExportPDF('L', 'F4', $output, 'Register SPM.pdf');

		//$output_form = drupal_get_form('register_spm_main_form');
		//return drupal_render($output_form) . $output;// . $output;
		
	} else if(arg(7) == 'excel'){
		$output = getlaporanregister($kodeuk, $sumberdana, $bulan, $tanggal,$jenisdokumen);
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename= Register_SPM.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;

	} else if(arg(7) == 'cetak'){
		$output = getlaporanregister($kodeuk, $sumberdana, $bulan, $tanggal,$jenisdokumen);
		$output_form = drupal_get_form('register_spm_main_form');
		return drupal_render($output_form) . $output;
	
	} else {
	
		
		$output_form = drupal_get_form('register_spm_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function register_spm_main_form($form, &$form_state) {

	$kodeuk = arg(1);
	$bulan = arg(2);
	$sumberdana = arg(3);
	$jenisdokumen=arg(4);
	$margin = arg(5);
	$tanggal = arg(6);
	
	
	if ($kodeuk=='') $kodeuk = 'ZZ';
	if ($bulan=='') {
		$bulan = $_SESSION["register-spm-lastbulan"];
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
		'#title' =>  t('JENIS SPM'),
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

	$results = db_query('SELECT sumberdana from {sumberdana} order by sumberdana');
	$opt_sumberdana['ZZ'] ='SEMUA SUMBER DANA';
	while($row = $results->fetchObject()){
		$opt_sumberdana[$row->sumberdana] = $row->sumberdana; 
	}	
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

function register_spm_main_form_validate($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$sumberdana = $form_state['values']['sumberdana'];
	
	if (($kodeuk=='ZZ')	and ($jenisdokumen=='ZZ') and ($sumberdana=='ZZ')) {
		form_set_error('kodeuk', 'Tidak bisa menampilkan seluruh SKPD untuk semua sumberdana dan untuk semua jenis SP2D, salah satu pilihan harus spesifik');
	}
}
	
function register_spm_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$margin = $form_state['values']['margin'];
	$bulan = $form_state['values']['bulan'];
	$tanggal = $form_state['values']['tanggal'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$sumberdana = $form_state['values']['sumberdana'];

	
	$_SESSION["register-spm-lastbulan"] = $bulan;

	if($form_state['clicked_button']['#value'] == $form_state['values']['cetakpdf']){
		drupal_goto('cetakregisterspm/' . $kodeuk . '/' . $bulan . '/' . $sumberdana . '/' . $jenisdokumen . '/' . $margin . '/'. $tanggal . '/pdf');
	}if($form_state['clicked_button']['#value'] == $form_state['values']['cetak']){
		drupal_goto('cetakregisterspm/' . $kodeuk . '/' . $bulan . '/' . $sumberdana . '/' . $jenisdokumen . '/' . $margin . '/'. $tanggal . '/cetak');
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['cetakexcel']){
		drupal_goto('cetakregisterspm/' . $kodeuk . '/' . $bulan . '/' . $sumberdana . '/' . $jenisdokumen . '/' . $margin . '/'. $tanggal . '/excel');
	}

}

function getlaporanregister($kodeuk, $sumberdana, $bulan, $tanggal,$jenisdokumen){
	set_time_limit(0);
	ini_set('memory_limit','920M');

	$where = '';
	$labeljenis = '';

	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER SPM' . $labeljenis, 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
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
		array('data' => 'NO. SPM',  'width' => '45px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:70%;'),
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

	//Content
	$query = db_select('dokumen', 'd');

	# get the desired fields from the database
	$query->fields('d', array('dokid', 'sppno', 'spmtgl', 'spmno', 'sp2dno', 'sp2dno', 'sp2dtgl', 'jenisdokumen', 'bulan', 'kodeuk', 'jumlah', 'potongan', 'netto', 'sppok', 'keperluan', 'jenisgaji', 'sp2dok', 'sp2dsudah', 'penerimanama', 'penerimabanknama', 'penerimabankrekening'));
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->fields('u', array('namasingkat'));
	
	$query->condition('d.spmok', '1', '=');

	if ($kodeuk != 'ZZ') {
		$query->condition('d.kodeuk', $kodeuk, '=');
	}	

	if ($jenisdokumen=='UP') {
		$query->condition('d.jenisdokumen', '0', '=');
		$labeljenis = ' - UANG PERSEDIAAN';
		
	} else if ($jenisdokumen=='GU') {
		$query->condition('d.jenisdokumen', '1', '=');
		$labeljenis = ' - GANTI UANG PERSEDIAAN';
		
	} else if ($jenisdokumen=='TU') {
		$query->condition('d.jenisdokumen', '2', '=');
		$labeljenis = ' - TAMBAHAN UANG PERSEDIAAN';
		
	} else if ($jenisdokumen=='LS') {
		$query->condition('d.jenisdokumen', '4', '=');
		$labeljenis = ' - LS BARANG DAN JASA';

	} else if ($jenisdokumen=='GJ') {
		$query->condition('d.jenisdokumen', '3', '=');
		$query->condition('d.jenisgaji', '4', '<');
		$labeljenis = ' - GAJI';
		
	} else if ($jenisdokumen=='TP') {
		$query->condition('d.jenisdokumen', '3', '=');
		$query->condition('d.jenisgaji', '4', '=');
		$labeljenis = ' - TAMBAHAN PENGHASILAN';
		
	} else if ($jenisdokumen=='PF') {
		$query->condition('d.jenisdokumen', '8', '=');
		$labeljenis = ' - P F K';
	}
	else if ($jenisdokumen=='RB') {
		$query->condition('d.jenisdokumen', '6', '=');
		$labeljenis = ' - RESTITUSI PAD';
	}
	if ($bulan!='0') {
		$query->where('EXTRACT(MONTH FROM d.spmtgl) = :month', array('month' => $bulan));
	}		
	if ($sumberdana != 'ZZ') {
		$query->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
		$query->condition('k.sumberdana1', $sumberdana, '=');
	}		
	
	//dpq($query);
	
	$query->orderBy('d.spmtgl', 'ASC');
	$results = $query->execute();
	
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
			
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';


		}
			
		else if($data->jenisdokumen==8){
			$gaji='x';
			$sumgaji+=1;
			$gaji_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgaji += $data->jumlah;
			
		}
			
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
			//$jumlah = '0';
			
			$keperluan = $data->keperluan . ', sebesar Rp ' . apbd_fn($data->jumlah) . ',00';
		}
			
		$rows[]=array(
			array('data' => $n, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fd($data->spmtgl), 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->spmno, 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
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
	if (arg(7) == 'excel') {
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
	
	if ($bulan>1) {
		
		
		$query = db_select('dokumen', 'd');

		# get the desired fields from the database
		$query->fields('d', array('jenisdokumen'));
		if ($kodeuk != 'ZZ') {
			$query->condition('d.kodeuk', $kodeuk, '=');
		}	
		$query->condition('d.spmok', '1', '=');

		if ($jenisdokumen=='UP') {
			$query->condition('d.jenisdokumen', '0', '=');
			
		} else if ($jenisdokumen=='GU') {
			$query->condition('d.jenisdokumen', '1', '=');
			
		} else if ($jenisdokumen=='TU') {
			$query->condition('d.jenisdokumen', '2', '=');
			
		} else if ($jenisdokumen=='LS') {
			$query->condition('d.jenisdokumen', '4', '=');

		} else if ($jenisdokumen=='GJ') {
			$query->condition('d.jenisdokumen', '3', '=');
			$query->condition('d.jenisgaji', '4', '<');
			
		} else if ($jenisdokumen=='TP') {
			$query->condition('d.jenisdokumen', '3', '=');
			$query->condition('d.jenisgaji', '4', '=');
			
		} else if ($jenisdokumen=='PF') {
			$query->condition('d.jenisdokumen', '8', '=');
		} else if ($jenisdokumen=='RB') {
			$query->condition('d.jenisdokumen', '6', '=');
		}
		
		$query->where('EXTRACT(MONTH FROM d.spmtgl) < :month', array('month' => $bulan));

		$query->addExpression('COUNT(d.dokid)', 'banyaknya');	
		$query->addExpression('SUM(d.jumlah)', 'total');	
		
		
		if ($sumberdana != 'ZZ') {
			$query->innerJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
			$query->condition('k.sumberdana1', $sumberdana, '=');
		}		
		
		$query->groupBy('d.jenisdokumen'); 
		
		//dpq($query);
		
		$results_b = $query->execute();
			
			
		foreach($results_b as $data){
			
			
			
			if($data->jenisdokumen==0){
				$sumup_lalu = $data->banyaknya;
				$totup_lalu = $data->total;
				
				$total_lalu += $data->total;
			}
				
			else if($data->jenisdokumen==1){
				$sumgu_lalu = $data->banyaknya;
				$totgu_lalu = $data->total;
				
				$total_lalu += $data->total;
				
			}
				
			else if($data->jenisdokumen==2){
				$sumtu_lalu = $data->banyaknya;
				$tottu_lalu = $data->total;
				
				$total_lalu += $data->total;
			}
				
			else if($data->jenisdokumen==3){
				$sumgaji_lalu = $data->banyaknya;
				$totgaji_lalu = $data->total;
				
				$total_lalu += $data->total;
			}
				
			else if(($data->jenisdokumen==4) or ($data->jenisdokumen==6)){
				$sumls_lalu += $data->banyaknya;
				$totls_lalu += $data->total;
				
				$total_lalu += $data->total;
			}
			else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
				$sumnhl_lalu += $data->banyaknya;
			}	
			else if($data->jenisdokumen==8){
				$sumgaji_lalu = $data->banyaknya;
				$totgaji_lalu = $data->total;
				
				$total_lalu += $data->total;
			}
		
		}		
	}
	

	if (arg(7) == 'excel') {
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
			array('data' => 'KESELURUHAN','colspan' => '3', 'width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
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
	if ($kodeuk != 'ZZ') {

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

function getlaporanregisterkegiatan($kodekeg){

	$res = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach($res as $data){
		$kegiatan = $data->kegiatan;
	}
	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:80%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER SP2D KEGIATAN' . $labeljenis, 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => $kegiatan, 'width' => '875px','align'=>'center','style'=>'border:none;font-size:80%;'),
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

	//Content
	$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.spmok, d.sp2dsudah, u.namasingkat, d.penerimanama, d.penerimabanknama, d.penerimabankrekening  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.spmok=1) and (d.kodekeg=:kodekeg) ORDER BY d.spmtgl ASC,d.spmno ASC ', array(':kodekeg'=>$kodekeg));
		

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
			
			$keperluan .= ' (' . $data->penerimanama . ', ' . $data->penerimabanknama . '/' . $data->penerimabankrekening . ')';


		}
			
		else if($data->jenisdokumen==8){
			$gaji='x';
			$sumgaji+=1;
			$gaji_rp = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totgaji += $data->jumlah;
			
		}
			
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
			//$jumlah = '0';
			
			$keperluan = $data->keperluan . ', sebesar Rp ' . apbd_fn($data->jumlah) . ',00';
		}
			
		$rows[]=array(
			array('data' => $n, 'width' => '20px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:70%;'),
			array('data' => apbd_fd($data->spmtgl), 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
			array('data' => $data->spmno, 'width' => '45px','align'=>'center','style'=>'border-right:1px solid black;font-size:70%;'),
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
	$rows[]=array(
			array('data' => 'JUMLAH', 'width' => '110px', 'cols-span'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:70%;'),
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


	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}



function getlaporanregister_spm_backup($kodeuk, $bulan, $tanggal,$jenisdokumen){
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
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.spmok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.spmok=1'.$where.'  ORDER BY d.spmtgl ASC,d.spmno ASC ');
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.spmok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE (d.jenisdokumen<>6 and d.jenisdokumen<>8)'.$where.' and (d.spmok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.spmtgl ASC,d.spmno ASC ');
		}
		
	} else {
		if($kodeuk == 'ZZ') {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.spmok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.spmtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and d.spmok=1  ORDER BY d.spmtgl ASC,d.spmno ASC ',array(':bulan'=>$bulan));
		
		} else {
			$results=db_query('SELECT d.dokid, d.sppno, d.spptgl, d.spmno, d.jenisdokumen, d.bulan, d.kodeuk, d.jumlah, d.potongan, d.netto, d.sppok, d.keperluan, d.jenisgaji, d.spmok, d.sp2dsudah, u.namasingkat  FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (MONTH(d.spmtgl) = :bulan)'.$where.' and (d.jenisdokumen<>6 and d.jenisdokumen<>8) and (d.spmok=1) and (d.kodeuk=:kodeuk)  ORDER BY d.spmtgl ASC,d.spmno ASC ', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
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
		
		$keperluan = $data->keperluan;
		if($data->jenisdokumen==0){
			$up='x';
			$sumup+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			$totup += $data->jumlah;
		}
			
		else if($data->jenisdokumen==1){
			$gu='x';
			$sumgu+=1;
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
			$ls='x';
			$sumls+=1;
			$jumlah = apbd_fn($data->jumlah);
			$total+=$data->jumlah;
			
			$totls += $data->jumlah;
			
		}
		else if (($data->jenisdokumen==5) or ($data->jenisdokumen==7)) {
			$nhl = 'x';
			$sumnhl += 1;
			$jumlah = '0';
			
			$keperluan = $data->keperluan . ', sebesar Rp ' . apbd_fn($data->jumlah) . ',00';
		}
			
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fd($data->spmtgl), 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $data->spmno, 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $up,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $gu,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $tu,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $gaji,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $ls,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $nhl,'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
			array('data' => $data->namasingkat, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => $keperluan, 'width' => '345px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => $jumlah, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			
		);
	}
	$rows[]=array(
			array('data' => 'BANYAKNYA SP2D', 'width' => '150px', 'rows-span'=>'3', 'align'=>'right','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumup,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumgu,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumtu,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumgaji,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumls,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => $sumnhl,'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => 'JUMLAH NOMINAL SP2D', 'width' => '445px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			array('data' => apbd_fn($total), 'width' => '100px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:2px solid black;font-size:80%;'),
			
		);
		
	
	/*
	$totup = 0;
	$totgu = 0;
	$tottu = 0;
	$totgaji = 0;
	$totls = 0;
	*/
	
	$rows[]=array(
			array('data' => '', 'width' => '330px', 'rows-span'=>'9', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			array('data' => '- Uang Persediaan (UP) ', 'width' => '345px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
			array('data' => apbd_fn($totup), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
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
			array('data' => apbd_fn($totls), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
			
		);
	
	//SEBELUMNYA
	if($kodeuk == 'ZZ') {
		$results=db_query('SELECT SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(spmtgl) < :bulan) and (jenisdokumen<>5 and jenisdokumen<>6 and jenisdokumen<>7 and jenisdokumen<>8) and (spmok=1)',array(':bulan'=>$bulan));
	
	} else {
		$results=db_query('SELECT SUM(jumlah) AS total FROM  {dokumen} WHERE  (MONTH(spmtgl) < :bulan) and (jenisdokumen<>5 and jenisdokumen<>6 and jenisdokumen<>7 and jenisdokumen<>8) and (spmok=1) and (kodeuk=:kodeuk)', array(':bulan'=>$bulan, ':kodeuk'=>$kodeuk));
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


?>