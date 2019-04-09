<?php

function kontrak_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$dokid = arg(2);	
	$print = arg(3);	
	$tanggal = arg(4);
	
	if ($print=='pdf') {
		$edoc = 'http://simkedajepara.web.id/edoc2019/spm/E_KTRK_' . $dokid . '.PDF';
		if (is_eDocExists($edoc)) {
			drupal_goto($edoc); 
			
		} else {			
			$output = getlaporankontrak($dokid, $tanggal);
			//apbd_Exportkontrak_Logo($output, 'Kontrak');
			$url = url(current_path(), array('absolute' => TRUE));		
			$url = str_replace('/pdf', '', $url); 
		
			$kodeuk = substr($dokid,2,2);
			$fname = $kodeuk . '/'. str_replace('/', '_', 'KTRK_' . $dokid . '.PDF');
			
			 
			
			apbd_ExportSPP_Logo_e($output, $url, $fname);		
			drupal_goto('files/spm/' . $fname);
			
		}
		
	} else {	
		$output_form = drupal_get_form('kontrak_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
}

function kontrak_edit_main_form($form, &$form_state) {
   
	$referer = $_SERVER['HTTP_REFERER'];
	$dokid = arg(2);	
	
	$query=db_query('select k.kegiatan,d.kodekeg,d.dpano,d.dpatgl,a.kodero,d.penerimanama,d.penerimaalamat,d.jumlah from dokumen as d inner join dokumenrekening as a on d.dokid=a.dokid inner join kegiatanskpd as k on d.kodekeg=k.kodekeg where d.dokid=:dokid',array('dokid'=>$dokid));
	foreach($query as $data){
		$kodekeg=$data->kodekeg;
		$dpano=$data->dpano;
		$dpatgl=$data->dpatgl;
		$kodero=$data->kodero;
		$kegiatan=$data->kegiatan;

		$namaperusahaan = $data->penerimanama;
		$alamatperusahaan = $data->penerimaalamat;
		$kontraknilai = $data->jumlah;
	}
	
	$kontrakno='';
	$kontraktgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$kontrakmulai = $kontraktgl;
	$kontrakselesai = $kontraktgl;
	$kontrakpenyerahan = $kontraktgl;

	$uraian='';
	$carabayar='Transfer Bank';
	$jaminanpemeliharaan='1 (Satu) Tahun';
	$sanksi='';
	$ada = false;
	
	$query=db_query("select * from dokumenkontrak where dokid=:dokid",array(':dokid'=>$dokid));
	foreach($query as $data){
		
		$ada = true;
		
		$kodekeg=$data->kodekeg;
		$dpano=$data->dpano;
		$dpatgl=$data->dpatgl;
		$kodero=$data->kodero;
		$kontrakno=$data->kontrakno;
		
		if (is_null($data->kontraktgl)) {
			$kontraktgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
		} else {
			$kontraktgl = strtotime($data->kontraktgl);		
		}				
		if (is_null($data->kontrakmulai)) {
			$kontrakmulai = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
		} else {
			$kontrakmulai = strtotime($data->kontrakmulai);		
		}		
		if (is_null($data->kontrakselesai)) {
			$kontrakselesai = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
		} else {
			$kontrakselesai = strtotime($data->kontrakselesai);		
		}		
		if (is_null($data->kontrakpenyerahan)) {
			$kontrakpenyerahan = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
		} else {
			$kontrakpenyerahan = strtotime($data->kontrakpenyerahan);		
		}		
		
		$kontraknilai=$data->kontraknilai;
		$uraian=$data->uraian;
		$carabayar=$data->carabayar;
		$jaminanpemeliharaan=$data->jaminanpemeliharaan;
		$sanksi=$data->sanksi;
		
		
		$namaperusahaan=$data->namaperusahaan;
		$alamatperusahaan=$data->alamatperusahaan;
		$jangkawaktupelaksanaan=$data->jangkawaktupelaksanaan;
		$jangkawaktupemeliharaan=$data->jangkawaktupemeliharaan;
		
	}
	$query=db_query("select * from kegiatandpa where kodekeg=:kodekeg",array(':kodekeg'=>$kodekeg));
	foreach($query as $data){
		$dpano=$data->dpano;
		$dpatgl=$data->dpatgl;
	}	
	
	
	$form['dokid'] = array(
		'#type' => 'value', 
		'#value' => $dokid,
	);
	$form['dpano'] = array(
		'#type' => 'value', 
		'#value' => $dpano,
	);
	$form['dpatgl'] = array(
		'#type' => 'value', 
		'#value' => $dpatgl,
	);
	$form['kodero'] = array(
		'#type' => 'value', 
		'#value' => $kodero,
	);
	$form['kodekeg'] = array(
		'#type' => 'value', 
		'#value' => $kodekeg,
	);
	
	//REKENING
	/*
	$form['mdpano'] = array (
		'#title' =>  t('No DPA'),
		//'#type' => 'textfield',
		//'#default_value' => $keperluan,
		//'#type' => 'textfield',
		'#type' => 'item',
		'#markup' => '<p>' .$dpano.'</p>',
	);
	$form['mdpatgl'] = array (
		'#title' =>  t('tgl DPA'),
		//'#type' => 'item',
		'#type' => 'item',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#markup' => '<p>' .$dpatgl.'</p>',
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['kegiatan'] = array (
		'#title' =>  t('Nama Kegiatan'),
		//'#type' => 'item',
		'#type' => 'item',
		'#attributes' => array('style' => 'text-align: left'),	
		'#markup' => '<p>'.$kegiatan.'</p>',
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	*/
	
	$form['kontrakno'] = array (
		'#title' =>  t('No. Kontrak'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: left'),
		'#default_value' => $kontrakno,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	
	$form['kontraktgl'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal Kontrak'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $kontraktgl,
		'#default_value'=> array(
			'year' => format_date($kontraktgl, 'custom', 'Y'),
			'month' => format_date($kontraktgl, 'custom', 'n'), 
			'day' => format_date($kontraktgl, 'custom', 'j'), 
		  ), 
		
	);
	$form['kontrakmulai'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal Mulai Kontrak'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $kontrakmulai,
		'#default_value'=> array(
			'year' => format_date($kontrakmulai, 'custom', 'Y'),
			'month' => format_date($kontrakmulai, 'custom', 'n'), 
			'day' => format_date($kontrakmulai, 'custom', 'j'), 
		  ), 
		
	);
	$form['kontrakselesai'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal Selesai Kontrak'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $kontrakselesai,
		'#default_value'=> array(
			'year' => format_date($kontrakselesai, 'custom', 'Y'),
			'month' => format_date($kontrakselesai, 'custom', 'n'), 
			'day' => format_date($kontrakselesai, 'custom', 'j'), 
		  ), 
		
	);
	$form['kontrakpenyerahan'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal Penyerahan Kontrak'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $kontrakpenyerahan,
		'#default_value'=> array(
			'year' => format_date($kontrakpenyerahan, 'custom', 'Y'),
			'month' => format_date($kontrakpenyerahan, 'custom', 'n'), 
			'day' => format_date($kontrakpenyerahan, 'custom', 'j'), 
		  ), 
		
	);

	$form['kontraknilai'] = array (
		'#title' =>  t('Nilai Kontrak'),
		//'#type' => 'item',
		'#type' => 'textfield',
		'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $kontraknilai,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	
	//PERUSAHAAN
	$form['namaperusahaan'] = array (
		'#title' =>  t('Nama Perusahaan'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $namaperusahaan,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['alamatperusahaan'] = array (
		'#title' =>  t('Alamat Perusahaan'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $alamatperusahaan,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['jangkawaktupelaksanaan'] = array (
		'#title' =>  t('Jangka Waktu Pelaksanaan'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $jangkawaktupelaksanaan,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['jangkawaktupemeliharaan'] = array (
		'#title' =>  t('Jangka Waktu Pemeliharaan'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $jangkawaktupemeliharaan,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['uraian'] = array (
		'#title' =>  t('Uraian dan Volume Pekerjaan'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $uraian,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['carabayar'] = array (
		'#title' =>  t('Cara Pembayaran'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $carabayar,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['jaminanpemeliharaan'] = array (
		'#title' =>  t('Jaminan Pemeliharaan'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $jaminanpemeliharaan,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['sanksi'] = array (
		'#title' =>  t('Sanksi'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $sanksi,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	$form['tanggalcetak'] = array (
		'#title' =>  t('Tanggal Cetak'),
		//'#type' => 'item',
		'#type' => 'textfield',
		//'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => date('d F y'),
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);
	
	
	if ($ada) {
		$form['submit']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Simpan',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
			//'#disabled' => TRUE,
			//'#suffix' => "&nbsp;<a href='/kontrak/" . $dokid . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-print' aria-hidden='true'></span> Cetak</a>",
			
		);
		$form['submit2']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
			//'#disabled' => TRUE,
						
		);
	} else {
		$form['submit']= array(
			'#type' => 'submit',
			'#value' =>  '<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Simpan',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
			'#description' => 'Simpan dahulu ringkasan kontrak, lalu Cetak',
			//'#disabled' => TRUE,
			//'#suffix' => "&nbsp;<a href='/kontrak/" . $dokid . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-print' aria-hidden='true'></span> Cetak</a>",
			
		);
		
	}
	return $form;
}

function kontrak_edit_main_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];
	$kodero = $form_state['values']['kodero'];
	$kodekeg = $form_state['values']['kodekeg'];
	$dpano = $form_state['values']['dpano'];
	$dpatgl = $form_state['values']['dpatgl'];
	$kontrakno = $form_state['values']['kontrakno'];
	$kontraknilai = $form_state['values']['kontraknilai'];
	$uraian = $form_state['values']['uraian'];
	$carabayar = $form_state['values']['carabayar'];
	$jaminanpemeliharaan = $form_state['values']['jaminanpemeliharaan'];
	$sanksi = $form_state['values']['sanksi'];
	$tanggalcetak = $form_state['values']['tanggalcetak'];
	
	$namaperusahaan = $form_state['values']['namaperusahaan'];
	$alamatperusahaan = $form_state['values']['alamatperusahaan'];
	$jangkawaktupelaksanaan = $form_state['values']['jangkawaktupelaksanaan'];
	$jangkawaktupemeliharaan = $form_state['values']['jangkawaktupemeliharaan'];
	
	
	$kontraktgl = $form_state['values']['kontraktgl'];
	$kontraktglsql = $kontraktgl['year'] . '-' . $kontraktgl['month'] . '-' . $kontraktgl['day'];
	
	$kontrakmulai = $form_state['values']['kontrakmulai'];
	$kontrakmulaisql = $kontrakmulai['year'] . '-' . $kontrakmulai['month'] . '-' . $kontrakmulai['day'];
	
	$kontrakselesai = $form_state['values']['kontrakselesai'];
	$kontrakselesaisql = $kontrakselesai['year'] . '-' . $kontrakselesai['month'] . '-' . $kontrakselesai['day'];
	
	$kontrakpenyerahan = $form_state['values']['kontrakpenyerahan'];
	$kontrakpenyerahansql = $kontrakpenyerahan['year'] . '-' . $kontrakpenyerahan['month'] . '-' . $kontrakpenyerahan['day'];
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$q=db_query("select count(dokid) as jumlah from dokumenkontrak where dokid=:dokid",array(':dokid'=>$dokid));
		foreach($q as $dat){
			$ada=$dat->jumlah;
		}
		if($ada>0){
			$query = db_update('dokumenkontrak') // Table name no longer needs {}
			->fields(array(
				'dokid' => $dokid,
				'kodero' => $kodero,
				'dpano' => $dpano,
				'dpatgl' => $dpatgl,
				'kodekeg' =>$kodekeg,
				'kontrakno' => $kontrakno,
				'kontraktgl' => $kontraktglsql,
				'kontrakmulai' => $kontrakmulaisql,
				'kontrakselesai' => $kontrakselesaisql,
				'kontrakpenyerahan' => $kontrakpenyerahansql,
				'kontraknilai' => $kontraknilai,
				'uraian' => $uraian,
				'carabayar' => $carabayar,
				'jaminanpemeliharaan' => $jaminanpemeliharaan,
				'sanksi' => $sanksi,
				
				'namaperusahaan'=>$namaperusahaan,
				'alamatperusahaan'=>$alamatperusahaan,
				'jangkawaktupelaksanaan'=>$jangkawaktupelaksanaan,
				'jangkawaktupemeliharaan'=>$jangkawaktupemeliharaan,
			))
			->condition('dokid', $dokid, '=')
			->execute();
			drupal_set_message('Data Telah Terupdate');
		}else{
			$query = db_insert('dokumenkontrak') // Table name no longer needs {}
			->fields(array(
				'dokid' => $dokid,
				'kodero' => $kodero,
				'dpano' => $dpano,
				'dpatgl' => $dpatgl,
				'kodekeg' =>$kodekeg,
				'kontrakno' => $kontrakno,
				'kontraktgl' => $kontraktglsql,
				'kontrakmulai' => $kontrakmulaisql,
				'kontrakselesai' => $kontrakselesaisql,
				'kontrakpenyerahan' => $kontrakpenyerahansql,
				'kontraknilai' => $kontraknilai,
				'uraian' => $uraian,
				'carabayar' => $carabayar,
				'jaminanpemeliharaan' => $jaminanpemeliharaan,
				'sanksi' => $sanksi,
				
				'namaperusahaan'=>$namaperusahaan,
				'alamatperusahaan'=>$alamatperusahaan,
				'jangkawaktupelaksanaan'=>$jangkawaktupelaksanaan,
				'jangkawaktupemeliharaan'=>$jangkawaktupemeliharaan,
			))
			->execute();
			drupal_set_message('Data Telah Masuk');
		}
		
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_goto('kontrak/edit/'. $dokid . '/pdf/' . $tanggalcetak );
		
	}
	
	
	
	
	/*$query = db_insert('dokumenkontrak')
					->fields( 
							array(
								'dokid' => $dokid,
								'kodero' => $kodero,
								'dpano' => $dpano,
								'dpatgl' => $dpatgl,
								'kodekeg' =>$kodekeg,
								'kontrano' => $kontrakno,
								'kontraktgl' => $kontraktglsql,
								'kontrakmulai' => $kontrakmulaisql,
								'kontrakselesai' => $kontrakselesaisql,
								'kontrakpenyerahan' => $kontrakpenyerahansql,
								'kontraknilai' => $kontraknilai,
								'uraian' => $uraian,
								'carabayar' => $carabayar,
								'jaminanpemeliharaan' => $jaminanpemeliharaan,
								'sanksi' => $sanksi,
									
							)
					);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();*/
	
	//drupal_goto('kuitansi/edit/' . $dokid . '/' . $kodero . '/a21/' . $tanggal . '/'. $penerimanama . '/'. $penerimanalamat . '/' . $jumlah);
	
}

function getlaporankontrak($dokid, $tgl){
	$query=db_query('select k.sumberdana1,k.kegiatan,k.kodepro,d.kodekeg,d.dpano,d.kodeuk,d.dpatgl,a.kodero from dokumen as d inner join dokumenrekening as a on d.dokid=a.dokid inner join kegiatanskpd as k on d.kodekeg=k.kodekeg where d.dokid=:dokid',array('dokid'=>$dokid));
	foreach($query as $data){
		$kodeuk=$data->kodeuk;
		$kodekeg=$data->kodekeg;
		$dpano=$data->dpano;
		$dpatgl=$data->dpatgl;
		$kodero=$data->kodero;
		$kegiatan=$data->kegiatan;
		$sumberdana=$data->sumberdana1;
		$kodepro = $data->kodepro;
	}
	$query=db_query("select * from dokumenkontrak where dokid=:dokid",array(':dokid'=>$dokid));
	foreach($query as $data){
		$kodekeg=$data->kodekeg;
		
		$kodero=$data->kodero;
		$kontrakno=$data->kontrakno;
		
		$kontraktgl=$data->kontraktgl;
		//$kontraktgl=strtotime($data->spptgl);		
		$kontrakmulai=$data->kontrakmulai;
		$kontrakselesai=$data->kontrakselesai;
		$kontrakpenyerahan=$data->kontrakpenyerahan;
		
		$kontraknilai=$data->kontraknilai;
		$uraian=$data->uraian;
		$carabayar=$data->carabayar;
		$jaminanpemeliharaan=$data->jaminanpemeliharaan;
		$sanksi=$data->sanksi;
		
		$namaperusahaan=$data->namaperusahaan;
		$alamatperusahaan=$data->alamatperusahaan;
		$jangkawaktupelaksanaan=$data->jangkawaktupelaksanaan;
		$jangkawaktupemeliharaan=$data->jangkawaktupemeliharaan;
		
	}
	$query=db_query("select * from kegiatandpa where kodekeg=:kodekeg",array(':kodekeg'=>$kodekeg));
	foreach($query as $data){
		$dpano=$data->dpano;
		$dpatgl=$data->dpatgl;
	}
	$query=db_query("select * from unitkerja where kodeuk=:kodeuk",array(':kodeuk'=>$kodeuk));
	foreach($query as $data){
		$namauk=$data->namauk;
		$header1=$data->header1;
		$header2=$data->header2;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$kodedinas = $data->kodedinas;
	}
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:110%;font-family:serif'),
	);
	$rows[]=array(
		array('data' => $namauk, 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => $header1, 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => $header2, 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border-top:2px solid black;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => 'RINGKASAN KONTRAK', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	
	
	$rows[]=array(
		
		
		array('data' => '1.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Nomor dan Tanggal DPA OPD', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $dpano.', '.$dpatgl, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		
		
		array('data' => '2.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Kegiatan/Sub Kegiatan', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $kegiatan, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
		
	);
	$rows[]=array(
		
		
		array('data' => '3.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Kode Rekening', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $kodedinas . '.' . $kodepro . '.' . substr($kodekeg, -3) . '.' . apbd_format_rek_rincianobyek($kodero), 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
		
	);
	$rows[]=array(
		
		
		array('data' => '4.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Nomor dan Tanggal Kontrak/SPK', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $kontrakno.', '.apbd_fd_long($kontraktgl), 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
		
	);
	$rows[]=array(
		array('data' => '5.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Jangka Waktu Pelaksanaan', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $jangkawaktupelaksanaan, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);	
	$rows[]=array(
		array('data' => '6.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Tanggal Mulai Kontrak', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => apbd_fd_long($kontrakmulai), 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '7.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Tanggal Berakhir Kontrak', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => apbd_fd_long($kontrakselesai), 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		
		
		array('data' => '8.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Nomor dan Tanggal Adendum', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		
		
		array('data' => '9.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Nama Perusahaan /Kontraktor', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $namaperusahaan, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		 
		array('data' => '10.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Alamat Perusahaan/Kontraktor', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $alamatperusahaan, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '11.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Nilai Kontrak', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => 'Rp ' . apbd_fn($kontraknilai) . ',00 (' . apbd_terbilang($kontraknilai) . ')', 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '12.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Sumber Dana', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $sumberdana, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	
	$rows[]=array(
		
		
		array('data' => '13.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Uraian dan Volume Pekerjaan', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $uraian, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '14.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Cara Pembayaran', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $carabayar, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '15.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Tanggal Penyelesaian Pekerjaan', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => apbd_fd_long($kontrakpenyerahan), 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '16.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Jangka Waktu Pemeliharaan', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $jangkawaktupemeliharaan, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '17.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Jaminan Pemeliharaan', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $jaminanpemeliharaan, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		
		
		array('data' => '18.', 'width' => '20px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => 'Ketentuan Sanksi', 'width' => '170px','align'=>'left','style'=>'border:none;font-size:90%;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $sanksi, 'width' => '300px','align'=>'left','style'=>'border:none;font-size:90%;'),
		
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => 'Jepara, '.$tgl, 'width' => '200px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => 'Pengguna Anggaran/PPKom', 'width' => '200px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => $pimpinannama, 'width' => '200px','align'=>'center','style'=>'border:none;font-size:90%;text-decoration:underline;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => 'NIP.'.$pimpinannip, 'width' => '200px','align'=>'center','style'=>'border:none;font-size:90%;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-size:90%;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}



?>
