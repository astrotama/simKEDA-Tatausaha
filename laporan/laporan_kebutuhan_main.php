<?php
function laporan_kebutuhan_main() {
	 $output = getLaporan();
     apbd_ExportPDF('L', 'A4', $output, 'CEK');
}

function getLaporan(){
	$header=array();
	$rows[]=array(
		array('data' => '<b>PEMERINTAH KABUPATEN JEPARA</b>', 'width' => '750px','align'=>'center','style'=>'font-size:20px;'),
	);
	$rows[]=array(
		array('data' => '<b>DAFTAR KEBUTUHAN BARANG MILIK DAERAH</b>', 'width' => '750px','align'=>'center','style'=>'font-size:20px;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN 2017', 'width' => '750px','align'=>'center','style'=>'font-size:20px;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '750px','align'=>'left','style'=>'font-size:20px;'),
	);
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '100px','align'=>'left','style'=>'font-size:20px;'),
		array('data' => ": UPD DISDIKPORA BANGSRI", 'width' => '700px','align'=>'left','style'=>'font-size:20px;'),
	);

	$output = theme('table', array('header' => $header, 'rows' => $rows ));

	$rows=null;
	$header[]=array(
		array('data' => '<b>KODE REKENING</b>', 'width' => '100px', 'align'=>'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
		array('data' => '<b>NAMA / JENIS BARANG</b>', 'width' => '300px', 'align'=>'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
		array('data' => '<b> MERK / TIPE / UKURAN</b>', 'width' => '60px',' align'=>'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
		array('data' => '<b>JUMLAH BARANG</b>', 'width' => '60px', 'align'=>'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
		array('data' => '<b>HARGA SATUAN</b>', 'width' => '100px', 'align'=>'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
		array('data' => '<b>JUMLAH BIAYA</b>', 'width' => '100px', 'align'=>'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
		array('data' => '<b>KET.</b>', 'width' => '60px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:20px;'),
	);

	for($no=0;$no<10;$no++){
		$rows[]=array(
            array('data' => '', 'width' => '100px', 'align'=>'left', 'style'=>'border-left:1px solid black;  font-size:20px;'),
    		array('data' => '', 'width' => '300px', 'align'=>'left', 'style'=>'border-left:1px solid black;  font-size:20px;'),
    		array('data' => '', 'width' => '60px',' align'=>'right', 'style'=>'border-left:1px solid black; border-right: 1px solid black; font-size:20px;'),
    		array('data' => '', 'width' => '60px', 'align'=>'right', 'style'=>'border-left:1px solid black; border-right: 1px solid black;'),
    		array('data' => '', 'width' => '100px', 'align'=>'right', 'style'=>'border-left:1px solid black; border-right: 1px solid black;'),
    		array('data' => '', 'width' => '100px', 'align'=>'right', 'style'=>'border-left:1px solid black; border-right: 1px solid black;'),
    		array('data' => '', 'width' => '60px','align'=>'left','style'=>'border-left:1px solid black; border-right: 1px solid black;'),
        );
	}

	$rows[]=array(
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
			array('data' => '<b>TOTAL</b>', 'width' => '520px','align'=>'left','style'=>'border-left:1px solid black;border-top: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black;font-size:20px;'),
			array('data' => '', 'width' => '60px','align'=>'center','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:20px;'),
		);

	$output .= createT($header,$rows,null);
	return $output;
}

?>
