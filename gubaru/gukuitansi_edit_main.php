<?php

function gukuitansi_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$spjid = arg(2);	
	$kodero = arg(3);	
	if ($kodero =='') $kodero = 'ZZ';
	$print = arg(4);	
	//drupal_set_message($kodero);
	if($print=='a21') {			

		$tanggal = arg(5);	
		$penerimanama = arg(6);	
		$penerimanalamat = arg(7);	
		$jumlah = arg(8);
		
		//drupal_set_message($jumlah);
		
		$output = printspp_a21($spjid, $kodero, $tanggal, $penerimanama, $penerimanalamat, $jumlah);
		apbd_ExportSPP_No_Footer($output, 'A2-1');
		//return $output;

	} else {
	
		$output_form = drupal_get_form('gukuitansi_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
		
}

function gukuitansi_edit_main_form($form, &$form_state) {
   
	$referer = $_SERVER['HTTP_REFERER'];

		
	$spjid = arg(2);
	$kodero = arg(3);
	if ($kodero =='') $kodero = 'ZZ';

	$query = db_select('spjgu', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('tanggal', 'jumlah', 'keperluan'));
	$query->fields('k', array('kegiatan'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.spjid', $spjid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$kegiatan = $data->kegiatan;
		$jumlah = $data->jumlah;
		$terbilang = apbd_terbilang($data->jumlah);
		$keperluan = $data->keperluan;
		
		$tanggal = apbd_fd_long($data->tanggal);
		
		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
	}
	$penerimanama = '';
	
	$form['spjid'] = array(
		'#type' => 'value', 
		'#value' => $spjid
	);
	$form['kodero'] = array(
		'#type' => 'value', 
		'#value' => $kodero
	);
	
	//REKENING
	if ($kodero != 'ZZ') {
		$query = db_select('rincianobyek', 'ro');
		$query->join('spjgurekening', 'dr', 'ro.kodero=dr.kodero');
		$query->fields('ro', array('uraian'));
		$query->fields('dr', array('jumlah'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('ro.kodero', $kodero, '=');
		$query->condition('dr.spjid', $spjid, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$uraian = $data->uraian;
			$jumlah = $data->jumlah;
		}	
		$form['rekening'] = array (
			'#title' =>  t('Rekening'),
			//'#type' => 'textfield',
			//'#default_value' => $keperluan,
			//'#type' => 'textfield',
			'#type' => 'item',
			'#markup' => '<p>' . $uraian . '</p>',
		);
		
	}
	$form['keperluan'] = array (
		'#title' =>  t('Keperluan'),
		//'#type' => 'textfield',
		//'#default_value' => $keperluan,
		//'#type' => 'textfield',
		'#type' => 'item',
		'#markup' => '<p>' . $keperluan . '</p>',
	);
	$form['jumlah'] = array (
		'#title' =>  t('Jumlah'),
		//'#type' => 'item',
		'#type' => 'textfield',
		'#attributes' => array('style' => 'text-align: right'),	
		'#default_value' => $jumlah,
		//'#markup' => '<p align="right">' . $jumlah . '</p>',
	);

	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PENERIMA',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formpenerima']['tanggal']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Tanggal'),
			//'#required' => TRUE,
			'#default_value'=> $tanggal, 
		);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanama, 
		);	
		$form['formpenerima']['penerimanalamat']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Alamat'),
			//'#required' => TRUE,
			'#default_value'=> '', 
		);	
	$form['submit']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Batal</a>",
		
	);
	return $form;
}

function gukuitansi_edit_main_form_submit($form, &$form_state) {
	$spjid = $form_state['values']['spjid'];
	$kodero = $form_state['values']['kodero'];
	$tanggal = $form_state['values']['tanggal'];
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanalamat = $form_state['values']['penerimanalamat'];
	$jumlah = $form_state['values']['jumlah'];
	
	if ($tanggal=='') $tanggal = 'x';
	if ($penerimanama=='') $penerimanama = 'x';
	if ($penerimanalamat=='') $penerimanalamat = 'x';
	
	drupal_goto('gukuitansi/edit/' . $spjid . '/' . $kodero . '/a21/' . $tanggal . '/'. $penerimanama . '/'. $penerimanalamat . '/' . $jumlah);
	
}

function printspp_a21($spjid, $kodero, $tanggal, $nama, $alamat, $jumlah){
	
	if ($tanggal=='x') $tanggal = '';
	if ($nama=='x') $nama = '';
	if ($alamat=='x') $alamat = '';
	
	//READ UP
	$query = db_select('spjgu', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('tanggal', 'jumlah', 'keperluan'));
	$query->fields('k', array('kegiatan', 'kodepro', 'kodekeg'));
	$query->fields('uk', array('kodeuk', 'kodedinas', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.spjid', $spjid, '=');

	//dpq($query);
	
	$results = $query->execute();
	foreach ($results as $data) {
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3) . ' - ' . $data->kegiatan;
		//$jumlah = apbd_fn($data->jumlah);
		$keperluan = $data->keperluan;
		
		//$spptgl = apbd_fd_long($data->spptgl);
		
		$bendaharanama = $data->bendaharanama;
		$bendaharanip = $data->bendaharanip;
		
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		
	}
	

	$rekening = 'Belanja Langsung';

	if ($kodero != 'ZZ') {
		$query = db_select('rincianobyek', 'ro');
		//$query->innerJoin('dokumenrekening', 'dr', 'ro.kodero=dr.kodero');
		$query->fields('ro', array('uraian'));
		//$query->fields('dr', array('jumlah'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('ro.kodero', $kodero, '=');
		//$query->condition('dr.spjid', $spjid, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$rekening = $kodero . ' - ' . $data->uraian;
			//$jumlah = $data->jumlah;
		}	

		
	}
		
	$terbilang = apbd_terbilang($jumlah);

	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '300px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'A2-1', 'width' => '110px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'Nama Kegiatan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $kegiatan, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Rekening', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $rekening, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Tahun anggaran', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TANDA BUKTI PENGELUARAN', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Sudah terima dari', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Uang sejumlah', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $terbilang, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => 'Untuk', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '400px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '510px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => '<div style="vertical-align:middle;">TERBILANG Rp </div>', 'width' => '115px','rowspan'=>'3','align'=>'left','style'=>'border-top:2px solid black;border-bottom:2px solid black;font-size:135%;vertical-align: middle;'),
		array('data' => '#' . apbd_fn($jumlah), 'width' => '135px','rowspan'=>'3','align'=>'right','style'=>'border-top:2px solid black;border-bottom:2px solid black;font-size:175%;vertical-align: middle;'),
		array('data' => '', 'width' => '25px','rowspan'=>'3','align'=>'left','style'=>'border:none'),
		array('data' => 'Jepara, ' . $tanggal, 'width' => '225px','align'=>'left','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => 'Yang berhak menerima', 'width' => '260px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanda tangan', 'width' => '90px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '135px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '275px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nama', 'width' => '90px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $nama, 'width' => '135px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '275px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Alamat', 'width' => '90px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $alamat, 'width' => '135px','align'=>'left','style'=>'border-bottom:0.5px dashed grey;'),
	);
	$rows[] = array(
					array('data' => 'Setuju dibayarkan,','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Pengguna Anggaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Bendahara Pengeluaran','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	/*
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	*/
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => $bendaharanama,'width' => '255px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. ' . $bendaharanip,'width' => '255px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}



?>
