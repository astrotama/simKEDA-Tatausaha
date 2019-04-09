<?php

function laporan_kontrak_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	$tgl = arg(1);	
	$dokid = arg(2);	
	$print = arg(3);	
	//drupal_set_message($dokid);
	$output = getlaporankontrak($dokid,$tgl);
	apbd_Exportkontrak_Logo($output, 'Kontrak');
	
	//apbd_ExportPDF('P', 'F4', $output, 'Kontrak');

	
}

function getlaporankontrak($dokid,$tgl){
	$query=db_query('select k.sumberdana1,k.kegiatan,d.kodekeg,d.dpano,d.kodeuk,d.dpatgl,a.kodero from dokumen as d inner join anggperkeg as a on d.kodekeg=a.kodekeg inner join kegiatanskpd as k on d.kodekeg=k.kodekeg where d.dokid=:dokid',array('dokid'=>$dokid));
	foreach($query as $data){
		$kodeuk=$data->kodeuk;
		$kodekeg=$data->kodekeg;
		$dpano=$data->dpano;
		$dpatgl=$data->dpatgl;
		$kodero=$data->kodero;
		$kegiatan=$data->kegiatan;
		$sumberdana=$data->sumberdana1;
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
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '1.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor dan Tanggal DPA OPD', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $dpano.' dan '.$dpatgl, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '2.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Kegiatan/Sub Kegiatan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $kegiatan, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '3.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Kode Rekening', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_format_rek_rincianobyek($kodero), 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '4.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor dan Tanggal Kontrak/SPK', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $kontrakno.' dan '.apbd_fd_long($kontraktgl), 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '5.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor dan Tanggal Adendum', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '6.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nama Perusahaan /Kontraktor', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $namaperusahaan, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '7.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Alamat Perusahaan/Kontraktor', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $alamatperusahaan, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '8.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nilai Kontrak', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_fn($kontraknilai), 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '9.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Sumber Dana', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $sumberdana, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '10.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Uraian dan Volume Pekerjaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $uraian, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '11.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Cara Pembayaran', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $carabayar, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '12.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Jangka Waktu Pelaksanaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $jangkawaktupelaksanaan, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '13.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tanggal Penyelesaian Pekerjaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_fd_long($kontrakselesai), 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '14.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Jangka Waktu Pemeliharaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $jangkawaktupemeliharaan, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
		
		array('data' => '15.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Ketentuan Sanksi', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => $sanksi, 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Jepara, '.$tgl, 'width' => '200px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Pengguna Anggaran/PPKom', 'width' => '200px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => $pimpinannama, 'width' => '200px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => 'NIP.'.$pimpinannip, 'width' => '200px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}



?>
