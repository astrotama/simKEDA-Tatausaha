
<?php
function laporan_kontrak_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 140px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	
	
	drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	$pdf=arg(2);
	
	
	
	if($pdf=='pdf') {			  

		//echo 'PDF';
		$output = getlaporankontrak();
		//print_pdf_p($output);
		apbd_ExportSPP($output, 'Kelengkapan');
	
	}else{
		apbd_ExportSPP("<h1>fwwg</h1>", 'Kontrak');
	}
	
	//apbd_ExportPDFm($margin,'P', 'F4', $output, 'CEK');
	//apbd_ExportPDF('P', 'F4', $output, 'CEK');
	//print_pdf_p($output,$output2);
	
	
}



/**
 * Selects just the second dropdown to be returned for re-rendering.
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return array
 *   Renderable array (the second dropdown)
 */

function getlaporankontrak(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	
	$rows[]=array(
		array('data' => 'RINGKASAN KONTRAK1', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;font-size:120%;text-decoration:underline;'),
	);
	
	
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '1.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor dan Tanggal DPA OPD', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '099/BL/40402.352.003/2017 , 29-12-2016', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '2.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Kegiatan/Sub Kegiatan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Upgrade dan pemeliharaan program penyusunan APBD Tahun 2017', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '3.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Kode Rekening', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '522.03.006', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '4.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor dan Tanggal Kontrak/SPK', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '050/    /BPKAD', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '5.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor dan Tanggal Adendum', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '050/    /BPKAD', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '6.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nama Perusahaan /Kontraktor', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '7.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Alamat Perusahaan/Kontraktor', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '8.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nilai Kontrak', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '9.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Sumber Dana', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '10.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Uraian dan Volume Pekerjaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '11.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Cara Pembayaran', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '12.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Jangka Waktu Pelaksanaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '13.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tanggal Penyelesaian Pekerjaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '14.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Jangka Waktu Pemeliharaan', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
		
		array('data' => '15.', 'width' => '30px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Ketentuan Sanksi', 'width' => '170px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
		
		
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;text-decoration:underline;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Jepara,  Mei 2017', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Pengguna Anggaran/PPKom', 'width' => '200px','align'=>'right','style'=>'border:none;'),
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
		array('data' => 'SUBIYANTO, SE', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'center','style'=>'border:none;'),
		array('data' => 'NIP. 19710111 199803 1 013', 'width' => '200px','align'=>'right','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'center','style'=>'border:none;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */



?>






