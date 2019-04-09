<?php

function kuitansi_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$dokid = arg(2);	
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
		
		$output = printspp_a21($dokid, $kodero, $tanggal, $penerimanama, $penerimanalamat, $jumlah);
		apbd_ExportSPP_No_Footer($output, 'A2-1');
		//return $output;

	} else {
	
		$output_form = drupal_get_form('kuitansi_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
		
}

function kuitansi_edit_main_form($form, &$form_state) {
   
	$referer = $_SERVER['HTTP_REFERER'];

		
	$dokid = arg(2);
	$kodero_param = arg(3);
	if ($kodero_param =='') $kodero_param = 'ZZ';

	if (strlen($kodero_param)<=8) {
		$kodero = $kodero_param;
		
		$query = db_select('dokumen', 'd');
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
		
		$query->fields('d', array('dokid', 'jenisdokumen', 'sppno', 'spptgl', 'jumlah', 'keperluan', 'penerimanama', 'penerimanip'));
		$query->fields('k', array('kegiatan'));
		$query->fields('uk', array('kodeuk', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$kegiatan = $data->kegiatan;
			$jumlah = $data->jumlah;
			$terbilang = apbd_terbilang($data->jumlah);
			$keperluan = $data->keperluan;
			
			$spptgl = apbd_fd_long($data->spptgl);
			
			$jenisdokumen = $data->jenisdokumen;
			
			$bendaharanama = $data->bendaharanama;
			$bendaharanip = $data->bendaharanip;
			
			$pimpinannama = $data->pimpinannama;
			$pimpinannip = $data->pimpinannip;
			
			if ($data->jenisdokumen=='4') 
				$penerimanama = $data->penerimanama;
			else
				$penerimanama = '';
		}
	
	} else {
		$kodekeg = substr($kodero_param, 0, 12);
		$kodero = substr($kodero_param, 12);

		$query = db_select('dokumen', 'd');
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		
		$query->fields('d', array('dokid', 'jenisdokumen', 'sppno', 'spptgl', 'jumlah', 'keperluan', 'penerimanama', 'penerimanip'));
		$query->fields('uk', array('kodeuk', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$jumlah = $data->jumlah;
			$terbilang = apbd_terbilang($data->jumlah);
			$keperluan = $data->keperluan;
			
			$spptgl = apbd_fd_long($data->spptgl);
			
			$jenisdokumen = $data->jenisdokumen;
			
			$bendaharanama = $data->bendaharanama;
			$bendaharanip = $data->bendaharanip;
			
			$pimpinannama = $data->pimpinannama;
			$pimpinannip = $data->pimpinannip;
			
			if ($data->jenisdokumen=='4') 
				$penerimanama = $data->penerimanama;
			else
				$penerimanama = '';
		}		

		$query = db_select('kegiatanskpd', 'k');
		$query->fields('k', array('kegiatan'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('k.kodekeg', $kodekeg, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$kegiatan = $data->kegiatan;
		}		
	}	
	
	$form['dokid'] = array(
		'#type' => 'value', 
		'#value' => $dokid
	);
	$form['kodero'] = array(
		'#type' => 'value', 
		'#value' => $kodero_param
	);
	
	//REKENING
	if ($kodero != 'ZZ') {
		$query = db_select('rincianobyek', 'ro');
		$query->join('dokumenrekening', 'dr', 'ro.kodero=dr.kodero');
		$query->fields('ro', array('uraian'));
		$query->fields('dr', array('jumlah'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('ro.kodero', $kodero, '=');
		$query->condition('dr.dokid', $dokid, '=');

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
			'#default_value'=> $spptgl, 
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

function kuitansi_edit_main_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];
	$kodero = $form_state['values']['kodero'];
	$tanggal = $form_state['values']['tanggal'];
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanalamat = $form_state['values']['penerimanalamat'];
	$jumlah = $form_state['values']['jumlah'];
	
	if ($tanggal=='') $tanggal = 'x';
	if ($penerimanama=='') $penerimanama = 'x';
	if ($penerimanalamat=='') $penerimanalamat = 'x';
	
	drupal_goto('kuitansi/edit/' . $dokid . '/' . $kodero . '/a21/' . $tanggal . '/'. $penerimanama . '/'. $penerimanalamat . '/' . $jumlah);
	
}

function printspp_a21($dokid, $kodero, $tanggal, $nama, $alamat, $jumlah){
	
	if ($tanggal=='x') $tanggal = '';
	if ($nama=='x') $nama = '';
	if ($alamat=='x') $alamat = '';
	
	//READ UP
	if (strlen($kodero)<=8) { 
		$query = db_select('dokumen', 'd');
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
		
		$query->fields('d', array('dokid', 'jenisdokumen', 'sppno', 'spptgl', 'jumlah', 'keperluan', 'penerimanama', 'penerimanip'));
		$query->fields('k', array('kegiatan', 'kodepro', 'kodekeg'));
		$query->fields('uk', array('kodeuk', 'kodedinas', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');

		//dpq($query);
		
		$results = $query->execute();
		foreach ($results as $data) {
			$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3) . ' - ' . $data->kegiatan;
			//$jumlah = apbd_fn($data->jumlah);
			$keperluan = $data->keperluan;
			
			//$spptgl = apbd_fd_long($data->spptgl);
			
			$jenisdokumen = $data->jenisdokumen;
			
			$bendaharanama = $data->bendaharanama;
			$bendaharanip = $data->bendaharanip;
			
			$pimpinannama = $data->pimpinannama;
			$pimpinannip = $data->pimpinannip;
			
		}
		
	} else {
		$kodekeg = substr($kodero, 0, 12);
		$kodero = substr($kodero, 12);		
		
		$query = db_select('dokumen', 'd');
		$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		
		$query->fields('d', array('dokid', 'jenisdokumen', 'sppno', 'spptgl', 'jumlah', 'keperluan', 'penerimanama', 'penerimanip'));
		$query->fields('uk', array('kodeuk', 'kodedinas', 'pimpinannama', 'pimpinannip', 'bendaharanama', 'bendaharanip'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');

		//dpq($query);
		
		$results = $query->execute();
		foreach ($results as $data) {
			//$jumlah = apbd_fn($data->jumlah);
			
			$kegiatan = $data->kodedinas;
			
			$keperluan = $data->keperluan;
			
			//$spptgl = apbd_fd_long($data->spptgl);
			
			$jenisdokumen = $data->jenisdokumen;
			
			$bendaharanama = $data->bendaharanama;
			$bendaharanip = $data->bendaharanip;
			
			$pimpinannama = $data->pimpinannama;
			$pimpinannip = $data->pimpinannip;
			
		}
		
		$query = db_select('kegiatanskpd', 'k');
		$query->fields('k', array('kegiatan', 'kodepro', 'kodekeg'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('k.kodekeg', $kodekeg, '=');

		$results = $query->execute();
		foreach ($results as $data) {
			$kegiatan .= '.' . $data->kodepro . '.' . substr($data->kodekeg, -3) . ' - ' . $data->kegiatan;

		}		
	}
	
	if ($jenisdokumen=='0') {
		$kegiatan = 'Permintaan Uang Persediaan';
		$rekening = 'Uang Persediaan';
	} else if ($jenisdokumen=='2') {
		$kegiatan = 'Permintaan Tambahan Uang Persediaan';
		$rekening = 'Tambahan Uang Persediaan';
	} else if ($jenisdokumen=='3') {
		$rekening = 'Belanja Gaji Pegawai';
	} else if ($jenisdokumen=='8') {
		$kegiatan = 'Pembayaran PFK';
		$rekening = 'PFK';
	} else {
		$rekening = 'Belanja Langsung';

		if ($kodero != 'ZZ') {
			$query = db_select('rincianobyek', 'ro');
			//$query->innerJoin('dokumenrekening', 'dr', 'ro.kodero=dr.kodero');
			$query->fields('ro', array('uraian'));
			//$query->fields('dr', array('jumlah'));
			
			//$query->fields('u', array('namasingkat'));
			$query->condition('ro.kodero', $kodero, '=');
			//$query->condition('dr.dokid', $dokid, '=');

			$results = $query->execute();
			foreach ($results as $data) {
				$rekening = $kodero . ' - ' . $data->uraian;
				//$jumlah = $data->jumlah;
			}	

			
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
