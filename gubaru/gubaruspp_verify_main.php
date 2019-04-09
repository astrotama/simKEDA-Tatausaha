<?php

function gubaruspp_verify_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$dokid = arg(2);	
	$output_form = drupal_get_form('gubaruspp_verify_main_form');
	return drupal_render($output_form);// . $output;
	
}

function gubaruspp_verify_main_form($form, &$form_state) {

	$dokid = arg(2);

	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$tglawal = $spptgl; $tglakhir = $spptgl; 
	$spdno = '';
	$spdtgl = '';

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'kodeuk',  'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 
			'sppok', 'spmok', 'sp2dok', 'spdno', 'tglawal', 'tglakhir'));
	
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SPP GU ' . $data->keperluan ;
		
		$kodeuk = $data->kodeuk;
		$sppok = $data->sppok;
		$spmok = $data->spmok;
		$sp2dok = $data->sp2dok;
		
		$dokid = $data->dokid;
		$sppno = $data->sppno;
		
		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;
		

		$keperluan = $data->keperluan;
		
		
		$jumlah = $data->jumlah;
		
		$spdno = $data->spdno;
		
	}
	
	drupal_set_title($title);

	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	


	
	//KEGIATAN
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		$form['formrekening' . $datakeg->kodekeg] = array (
			'#type' => 'fieldset',
			'#title'=> $nokeg . '. ' . $datakeg->kegiatan,
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);	
		
		//getLaporanbk5($kodeuk, $kodekeg, $tglawal, $tglakhir)
		$form['formrekening' . $datakeg->kodekeg]['bk5'] = array (
			'#type' => 'item',
			'#markup' => getLaporanbk5($kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),        
		);	

	}
	
	//FORM SUBMIT DECLARATION
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function gubaruspp_verify_main_form_validate($form, &$form_state) {
} 
 
function gubaruspp_verify_main_form_submit($form, &$form_state) {

$dokid = $form_state['values']['dokid'];

}


function getLaporanbk5($kodeuk, $kodekeg, $tglawal, $tglakhir){
	$style='border-right:1px solid black;';
	
	db_set_active('bendahara');

	$header=null;
	$rows=null;
	$header[]=array(
		array('data' => 'No', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
		array('data' => 'Kode', 'width' => '60px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Uraian', 'width' => '225px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Anggaran', 'rowspan'=>2,'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Sebelumnya', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'UP/GU', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'TU', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'LS', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Total', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Sisa', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
	);
	
	
	$n = 0;
	$lalu_total = 0; $ls_total = 0; $gu_total = 0; $tu_total = 0; $rea_total = 0;
	
	//contents
	$query = db_select('anggperkeg', 'a');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('a', array('anggaran'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	//dpq($query);	
		
	# execute the query	
	$res_rek = $query->execute();
	foreach ($res_rek as $data_rek) {
		$n++;
		
		//lalu
		$lalu = read_sebelumnya($kodekeg, $data_rek->kodero, $tglawal);
		
		//transaksi
		$ls = 0; $gu = 0; $tu = 0;
		read_sekarang($kodekeg, $data_rek->kodero, $tglawal, $tglakhir, $ls, $gu, $tu);
		
		$anggaran_total += $data_rek->anggaran;
		
		$lalu_total += $lalu;
		
		$ls_total += $ls;
		$gu_total += $gu; 
		$tu_total += $tu;
					
		$realisai = $ls + $gu + $tu + $lalu;
		$rea_total += $realisai;
		
		//LALU
		
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_format_rek_rincianobyek($data_rek->kodero), 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => $data_rek->uraian, 'width' => '225px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($lalu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($gu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($tu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($ls), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($realisai), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($data_rek->anggaran - $realisai), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			
		);
	}
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => '', 'width' => '60px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => 'TOTAL', 'width' => '315px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($anggaran_total),'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($lalu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($gu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($tu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($ls_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($rea_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($anggaran_total - $rea_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			
	);
	db_set_active();
	
	
	$output.=createT($header, $rows);
	
	return $output;
}

function read_sekarang($kodekeg, $kodero, $tglawal, $tglakhir, &$ls, &$gu, &$tu) {
	$ls = 0; $gu = 0; $tu = 0;
	
	//realisasi
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->fields('b', array('jenis'));
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');
	
	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$or->condition('b.jenis', 'tu-spj', '=');
	$or->condition('b.jenis', 'gu-spj', '=');
	$query->condition($or);
	$query->groupBy('b.jenis');
	
	$result = $query->execute();
	foreach ($result as $data_spj) {
		
		if ($data_spj->jenis == 'gu-spj')
			$gu += $data_spj->total;
		else if (($data_spj->jenis == 'ls') or ($data_spj->jenis == 'gaji'))
			$ls += $data_spj->total;
		else
			$tu += $data_spj->total;
		
	}	

	//pindahbuku  
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->fields('b', array('jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');
	$query->condition('b.jenis', 'pindahbuku', '=');
	//$query->condition('bi.jumlah', '0', '>=');

	$query->groupBy('b.jenispanjar');
	$result = $query->execute();
	foreach ($result as $data_spj) {
		
		if ($data_spj->jenispanjar == 'gu')
			$gu += $data_spj->total;
		else if ($data_spj->jenispanjar == 'ls')
			$ls += $data_spj->total;
		else
			$tu += $data_spj->total;
		
	}
	
	
	//ret
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->fields('b', array('jenispanjar'));
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '>=');
	$query->condition('b.tanggal', $tglakhir, '<=');
	$query->condition('b.jenis', 'ret-spj', '=');

	$query->groupBy('b.jenispanjar');
	
	$result = $query->execute();
	foreach ($result as $data_spj) {
		
		if ($data_spj->jenispanjar == 'gu')
			$gu -= $data_spj->total;
		else if ($data_spj->jenispanjar == 'ls')
			$ls -= $data_spj->total;
		else
			$tu -= $data_spj->total;
		
	}		
}


function read_sebelumnya($kodekeg, $kodero, $tglawal) {
	$val = 0;
	
	//rea	
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '<');
	
	$or = db_or();
	$or->condition('b.jenis', 'gaji', '=');
	$or->condition('b.jenis', 'ls', '=');
	$or->condition('b.jenis', 'tu-spj', '=');
	$or->condition('b.jenis', 'gu-spj', '=');
	$or->condition('b.jenis', 'pindahbuku', '=');
	$query->condition($or);
	
	//dpq($query);
	
	$result = $query->execute();
	foreach ($result as $data_spj) {
		
		$val = $data_spj->total;
		
	}	

	//ret	
	$query = db_select('bendahara', 'b');
	$query->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$query->addExpression('SUM(bi.jumlah)', 'total');
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('bi.kodero', $kodero, '=');

	$query->condition('b.tanggal', $tglawal, '<');
	$query->condition('b.jenis', 'ret-spj', '=');
	
	$result = $query->execute();
	foreach ($result as $data_spj) {
		
		$val -= $data_spj->total;
		
	}	
	
	return $val;
}

function printspp_2($dokid) {
	
	//READ UP DATA
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah',  
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'penerimanip', 'spdno'));
	//$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'header1'));
	$query->fields('uk', array('bendaharanama','bendaharanip','kodeuk','pimpinannama', 'kodedinas', 'namauk', 'header1'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);
		
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$kodeuk = $data->kodeuk;
		$namauk = $data->namauk;
		$pimpinannama = $data->pimpinannama;
		
		$alamat = $data->header1;
		
		$jumlah = $data->jumlah;		
		
		$keperluan = $data->keperluan;
		$penerimanama=$data->penerimanama;
		$bendaharanama = $data->bendaharanama;
		//$bendaharanama = $data->penerimanama;
		$rekening = $data->penerimabanknama . ' No. Rek . ' . $data->penerimabankrekening;
		$bendaharanip = $data->bendaharanip;
		
		$spdkode = $data->spdno;
		
	}	
	
	//dpa
	if ($kodeuk=='00') {
		$query = db_select("dpanomor", "d");
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->fields('d', array('btlno', 'btltgl'));
		$query->fields('uk', array('kodedinas'));
		$query->condition('d.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$dpano = $data->btlno;
			$dpatgl = $data->btltgl;
		}
	} else {
		$query = db_select("dpanomor", "d");
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->fields('d', array('blno', 'bltgl'));
		$query->fields('uk', array('kodedinas'));
		$query->condition('d.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$dpano = $data->blno . '/BL/' . $data->kodedinas . '/' . apbd_tahun();
			$dpatgl = $data->bltgl;
		}
	}
	
	//Anggaran
	$query = db_select("kegiatanskpd","k");
	$query->addExpression('SUM(k.anggaran)', 'totalanggaran');
	$query->condition('k.kodeuk', $kodeuk, '=');	
	if ($kodeuk=='00')
		$query->condition('k.jenis', '1', '=');
	else		
		$query->condition('k.jenis', '2', '=');
	$query->condition('k.inaktif', '0', '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$anggaran = $data->totalanggaran;
	}
	
	//ANGGARAN DAN TRIWULAN
	$twkumulatif = 0;
	$twaktif = 0;
	if ($spdkode=='') $spdkode = 'ZZZZZ';
	
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah', 'tw'));
	if ($kodeuk=='00')
		$query->condition('s.jenis', 1, '=');	
	else
		$query->condition('s.jenis', 2, '=');	
	$query->condition('s.kodeuk', $kodeuk, '=');	
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		if ($data->spdkode<= $spdkode) $twkumulatif +=  $data->jumlah;
		if ($data->spdkode== $spdkode) {
			$spdno = $data->spdno;
			$spdtgl = $data->spdtgl;
			$twaktif =  $data->jumlah;
		}
		switch ($data->tw) {
			case '1':
				$spdno1 = $data->spdno;
				$spdtgl1 = $data->spdtgl;
				$spdjumlah1 = $data->jumlah;
				break;
			case '2':
				$spdno2 = $data->spdno;
				$spdtgl2 = $data->spdtgl;
				$spdjumlah2 = $data->jumlah;		
				break;
			case '3':
				$spdno3 = $data->spdno;
				$spdtgl3 = $data->spdtgl;
				$spdjumlah3 = $data->jumlah;
				break;
			case '4':
				$spdno4 = $data->spdno;
				$spdtgl4 = $data->spdtgl;
				$spdjumlah4 = $data->jumlah;
				break;
		}
	}
	if ($twkumulatif>$anggaran) $twkumulatif = $anggaran;
	if ($spdno=='') {
		$spdno = '................';
		$spdtgl = '................';		
	}
	$spd = 'SPD Nomor : ' . $spdno . ', tanggal : ' . $spdtgl;
	
	$sudahcair = 0;
	$pengembalian = 0;
	
	$totalkeluar = $sudahcair - $pengembalian + $jumlah;
	$saldoakhir = $twkumulatif - $totalkeluar;
	
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Asli', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 1', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 2', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '290px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Salinan 3', 'width' => '40px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => ':', 'width' => '5px','align'=>'left','style'=>'border:none;font-size:80%;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '200px','align'=>'left','style'=>'border:none;font-size:80%;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '510','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERMINTAAN PEMBAYARAN (SPP)', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : ' . $sppno, 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'SPP-2', 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'UANG PERSEDIAAN [SPP-UP]', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);

	$rows[]=array(
		array('data' => '1.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Jenis Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'BELANJA LANGSUNG', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '2.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nomor dan Nama Kegiatan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Uang Persediaan', 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '3.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'SKPD', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $skpd, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '4.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Alamat SKPD', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Pimpinan SKPD', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $pimpinannama, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Nama dan No. Rekening Bank', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $rekening, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '7.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Untuk Pekerjaan / Keperluan', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => '8.', 'width' => '20px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'Dasar Pengeluaran', 'width' => '155px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => $spd, 'width' => '325px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);	
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		
	);	
	
	$rows[]=array(
		array('data' => 'NO.', 'width' => '25px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'URAIAN', 'width' => '245px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'JUMLAH MATA UANG BERSANGKUTAN', 'width' => '240px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'I', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'DPA-SKPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Nomor', 'width' => '40px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpano, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '40px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => $dpatgl, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'II', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'SPD', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '1.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl1, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno1, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah1), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '2.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl2, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno2, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah2), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '3.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl3, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno3, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah3), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '4.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tgl. ' . $spdtgl4, 'width' => '110px','align'=>'left','style'=>'border:none;'),
		array('data' => 'No. ' . $spdno4, 'width' => '120px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($spdjumlah4), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($twaktif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($twkumulatif), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'III', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'SP2D', 'width' => '245px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'a.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'SP2D belanja langsung sebelumnya', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($sudahcair), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'b.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Pengembalian', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($pengembalian), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'c.', 'width' => '15px','align'=>'left','style'=>'border:none;'),
		array('data' => 'SP2D UP diminta', 'width' => '230px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($jumlah), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($totalkeluar), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => apbd_fn($saldoakhir), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	
	$rows[] = array(
		array('data' => 'Pada SPP ini ditetapkan lampiran-lampiran yang diperlukan sebagaimana tertera pada daftar kelengkapan dokumen SPP-1','width' => '510px', 'align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);

	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, ' . $spptgl,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
			);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Bendahara Pengeluaran','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}



?>