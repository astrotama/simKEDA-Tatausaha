<?php

function sikd_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('sikd_edit_main_form');
	return drupal_render($output_form);// . $output;
		
}

function sikd_edit_main_form($form, &$form_state) {
   

	$bulan = arg(1);
	
	if ($bulan=='') $bulan = date('n');
	if ($bulan==1)
		$bulan = 12;
	else
		$bulan--;
	
	//drupal_set_message($bulan);
	
	$opt_bulan['1'] = 'Januari';
	$opt_bulan['2'] = 'Februari';
	$opt_bulan['3'] = 'Maret';
	$opt_bulan['4'] = 'April';
	$opt_bulan['5'] = 'Mei';
	$opt_bulan['6'] = 'Juni';
	$opt_bulan['7'] = 'Juli';
	$opt_bulan['8'] = 'Agustus';
	$opt_bulan['9'] = 'September';
	$opt_bulan['10'] = 'Oktober';
	$opt_bulan['11'] = 'Nopember';
	$opt_bulan['12'] = 'Desember';	
	$form['bulan'] = array (
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		'#default_value' => $bulan,
	);
	$form['submitlra']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"> LRA</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		
	);
	$form['submitdth']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"> DTH</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		
	);
	$form['submitapbd']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"> APBD</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	return $form;
}

function sikd_edit_main_form_submit($form, &$form_state) {
	
	//drupal_set_message('x');
	
	//updateRekening();
	//prepare_LRA_ByAgg(0);
	
	$bulan = '4';
	createXML($bulan);
}


function createXML($bulan) {
	
$writer = new XMLWriter();  
//$writer->openURI('php://output');   
//$writer->openURI('/test.xml');   
$writer->openMemory();
$writer->startDocument('1.0','UTF-8');   
$writer->setIndent(4);   

//file_put_contents('files/xml/2017991265LRA0' . $bulan . '.xml', $writer->flush(true));
$fname = 'files/xml/2018991265LRA0' . $bulan . '.xml';
file_put_contents($fname, $writer->flush(true));
$writer->startElement('ns2:realisasiAPBDWS'); 
$writer->writeAttribute('xmlns:ns2', 'http://service.sikd.app/'); 

	$writer->writeElement('kodeSatker', '991265'); 
	$writer->writeElement('kodePemda', '11.10');
	$writer->writeElement('namaPemda', 'Kabupaten Jepara');
	$writer->writeElement('tahunAnggaran', '2018');
	$writer->writeElement('periode', $bulan);
	$writer->writeElement('kodeData', '0');
	$writer->writeElement('jenisCOA', '1');
	$writer->writeElement('statusData', '0');
	$writer->writeElement('nomorPerda', 'Tidak Ada No Perda');
	$writer->writeElement('tanggalPerda', '2018-01-01');
	$writer->writeElement('userName', '991265');
	$writer->writeElement('password', '-29-35118-9840-765865');
	$writer->writeElement('namaAplikasi', 'simKEDA');
	$writer->writeElement('pengembangAplikasi', 'Astrotama');
	
	/*
	
	//PENDAPATAN 
	$i = 0;
	db_set_active('akuntansi');
	$reskegmaster = db_query('select distinct kodeuk from jurnal where jenis=:jenis and month(tanggal)<=:bulan', array(':jenis' => 'pad', ':bulan' => $bulan));
	foreach ($reskegmaster as $datakegmaster) {
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusan); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', '000'); 
			$writer->writeElement('namaProgram', 'Non Program'); 
			$writer->writeElement('kodeKegiatan', '000000'); 
			$writer->writeElement('namaKegiatan', 'Non Kegiatan'); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 

				//REKENING
				$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.jenis=:jenis AND LEFT(ji.kodero,1)=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':jenis' => 'pad', ':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
				foreach ($resrek as $datarek) {
					
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							$writer->startElement("kodeRekenings"); 
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobjek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->realisasi); 
							
							$writer->endElement();		//END REKENING
						}	
						
					
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}		
	}
	file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	db_set_active();
	
	*/
	
	//BELANJA
	$i=0;
	//Kegiatan
	//$reskegmaster = db_query('select distinct kodeuk, kodekeg from dokumen  where jenisdokumen in (1,2,3,4,5,7) and sp2dok=1 and kodeuk=:kodeuk and month(sp2dtgl)<=:bulan', array(':kodeuk' => '08', ':bulan' => $bulan));
	$reskegmaster = db_query('select distinct kodeuk, kodekeg from dokumen  where jenisdokumen in (1,2,3,4,5,7) and sp2dok=1 and month(sp2dtgl)<=:bulan', array(':bulan' => $bulan));
	foreach ($reskegmaster as $datakegmaster) {
		
		//drupal_set_message($datakegmaster->kodekeg);
		
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			//$writer->writeElement('kodeUrusanProgram', '1.20.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			//$writer->writeElement('namaUrusanProgram', 'OTDA, PEMERINTAHAN UMUM, ADM KEUANGAN'); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			//$writer->writeElement('kodeUrusanPelaksana', '1.20.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 
			//$writer->writeElement('namaUrusanPelaksana', 'OTDA, PEMERINTAHAN UMUM, ADM KEUANGAN'); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', $datakeg->kodepro); 
			$writer->writeElement('namaProgram', $datakeg->program); 
			$writer->writeElement('kodeKegiatan', substr($datakegmaster->kodekeg,-6)); 
			$writer->writeElement('namaKegiatan', $datakeg->kegiatan); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 
			//$writer->writeElement('kodeFungsi', '01'); 
			//$writer->writeElement('namaFungsi', 'Pelayana Umum'); 

				//REKENING
				$resrek = db_query('select dr.kodero, sum(dr.jumlah) realisasi FROM {dokumenrekening} dr INNER JOIN {dokumen} d ON dr.dokid=d.dokid WHERE d.sp2dok=1 AND d.kodekeg=:kodekeg AND month(d.sp2dtgl)<=:bulan GROUP BY dr.kodero', array(':kodekeg' => $datakegmaster->kodekeg, ':bulan' => $bulan));
				foreach ($resrek as $datarek) {
					$writer->startElement("kodeRekenings"); 
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobjek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->realisasi); 
						}	
						
					$writer->endElement();		//END REKENING
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}	
	}	 


$writer->endElement(); 			//ns2	

//$writer->endDocument();   
//$writer->flush(); 

file_put_contents($fname, $writer->flush(true), FILE_APPEND);
$zip = new ZipArchive();
$filename = "files/xml/2018991265LRA0" . $bulan . '.zip';

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	drupal_set_message('failed');	
   
} else {
	$zip->addFile($fname, basename($fname));
	$zip->close();
	drupal_set_message('ok');
}
//drupal_goto('http://tatausaha.simkedajepara.net/'.$filename);
}

function updateRekening() {

$num = db_delete('rekeninglengkap')
  ->execute();
	  
$resrek = db_query('select * from rincianobyek where left(kodero,1)=:akun', array(':akun'=>'4'));
foreach ($resrek as $data) {
	
	drupal_set_message($data->kodero);
	
	$x = db_insert('rekeninglengkap')
		->fields(array('kodero', 'kodeakunutama', 'kodeakunkelompok', 'kodeakunjenis', 'kodeakunobjek', 'kodeakunrincian', 'namaakunrincian', 'akunrincian'))
		->values(array(
				'kodero'=> $data->kodero,
				'kodeakunutama' => substr($data->kodero, 0, 1),
				'kodeakunkelompok' => substr($data->kodero, 0, 2),
				'kodeakunjenis' => substr($data->kodero, 0, 3),
				'kodeakunobjek' => substr($data->kodero, 0, 5),
				'kodeakunrincian' => $data->kodero,
				'namaakunrincian' => $data->uraian,
				'akunrincian' => $data->kodero . ' - ' . $data->uraian,
				))
		->execute();
	drupal_set_message($x);		
		
}

$resrek = db_query('select * from rincianobyek where left(kodero,1)=:akun', array(':akun'=>'6'));
foreach ($resrek as $data) {
	
	drupal_set_message($data->kodero);
	
	$x = db_insert('rekeninglengkap')
		->fields(array('kodero', 'kodeakunutama', 'kodeakunkelompok', 'kodeakunjenis', 'kodeakunobjek', 'kodeakunrincian', 'namaakunrincian', 'akunrincian'))
		->values(array(
				'kodero'=> $data->kodero,
				'kodeakunutama' => substr($data->kodero, 0, 1),
				'kodeakunkelompok' => substr($data->kodero, 0, 2),
				'kodeakunjenis' => substr($data->kodero, 0, 3),
				'kodeakunobjek' => substr($data->kodero, 0, 5),
				'kodeakunrincian' => $data->kodero,
				'namaakunrincian' => $data->uraian,
				'akunrincian' => $data->kodero . ' - ' . $data->uraian,
				))
		->execute();	
		
}	

$resrek = db_query('select * from rincianobyek where left(kodero,2)=:akun', array(':akun'=>'51'));
foreach ($resrek as $data) {
	
	drupal_set_message($data->kodero);
	
	$x = db_insert('rekeninglengkap')
		->fields(array('kodero', 'kodeakunutama', 'kodeakunkelompok', 'kodeakunjenis', 'kodeakunobjek', 'kodeakunrincian', 'namaakunrincian', 'akunrincian'))
		->values(array(
				'kodero'=> $data->kodero,
				'kodeakunutama' => substr($data->kodero, 0, 1),
				'kodeakunkelompok' => substr($data->kodero, 0, 2),
				'kodeakunjenis' => substr($data->kodero, 0, 3),
				'kodeakunobjek' => substr($data->kodero, 0, 5),
				'kodeakunrincian' => $data->kodero,
				'namaakunrincian' => $data->uraian,
				'akunrincian' => $data->kodero . ' - ' . $data->uraian,
				))
		->execute();	
		
		
}	

$resrek = db_query('select * from rincianobyek where left(kodero,2)=:akun', array(':akun'=>'52'));
foreach ($resrek as $data) {
	
	drupal_set_message($data->kodero);
	
	$x = db_insert('rekeninglengkap')
		->fields(array('kodero', 'kodeakunutama', 'kodeakunkelompok', 'kodeakunjenis', 'kodeakunobjek', 'kodeakunrincian', 'namaakunrincian', 'akunrincian'))
		->values(array(
				'kodero'=> $data->kodero,
				'kodeakunutama' => substr($data->kodero, 0, 1),
				'kodeakunkelompok' => substr($data->kodero, 0, 2),
				'kodeakunjenis' => substr($data->kodero, 0, 3),
				'kodeakunobjek' => substr($data->kodero, 0, 5),
				'kodeakunrincian' => $data->kodero,
				'namaakunrincian' => $data->uraian,
				'akunrincian' => $data->kodero . ' - ' . $data->uraian,
				))
		->execute();	
		
}	

/*
drupal_set_message('hai...');
$x = db_query("update rekeninglengkap inner join anggaran on rekeninglengkap.kodeakunutama=anggaran.kodea
set rekeninglengkap.namaakunutama=anggaran.uraian, rekeninglengkap.akunutama=concat(anggaran.kodea, ' - ', anggaran.uraian");

$x = db_query("update rekeninglengkap inner join kelompok on rekeninglengkap.kodeakunkelompok=kelompok.kodek
set rekeninglengkap.namaakunkelompok=kelompok.uraian, rekeninglengkap.akunkelompok=concat(kelompok.kodek, ' - ', kelompok.uraian");

$x = db_query("update rekeninglengkap inner join jenis on rekeninglengkap.kodeakunjenis=jenis.kodej
set rekeninglengkap.namaakunjenis=jenis.uraian, rekeninglengkap.akunjenis=concat(jenis.kodej, ' - ', jenis.uraian");

$x = db_query("update rekeninglengkap inner join obyek on rekeninglengkap.kodeakunobjek=obyek.kodeo
set rekeninglengkap.namaakunobjek=obyek.uraian, rekeninglengkap.akunobjek=concat(obyek.kodeo, ' - ', obyek.uraian");
*/

/*
update rekeninglengkap inner join anggaran on rekeninglengkap.kodeakunutama=anggaran.kodea
set rekeninglengkap.namaakunutama=anggaran.uraian, rekeninglengkap.akunutama=concat(anggaran.kodea, ' - ', anggaran.uraian)

update rekeninglengkap inner join kelompok on rekeninglengkap.kodeakunkelompok=kelompok.kodek
set rekeninglengkap.namaakunkelompok=kelompok.uraian, rekeninglengkap.akunkelompok=concat(kelompok.kodek, ' - ', kelompok.uraian)

update rekeninglengkap inner join jenis on rekeninglengkap.kodeakunjenis=jenis.kodej
set rekeninglengkap.namaakunjenis=jenis.uraian, rekeninglengkap.akunjenis=concat(jenis.kodej, ' - ', jenis.uraian)

update rekeninglengkap inner join obyek on rekeninglengkap.kodeakunobjek=obyek.kodeo
set rekeninglengkap.namaakunobjek=obyek.uraian, rekeninglengkap.akunobjek=concat(obyek.kodeo, ' - ', obyek.uraian)

*/	
}

function updateRekening_LAMA() {
	
$resrek = db_query('select * from rincianobyek where kodero not in (select kodero from rekeninglengkap)');
foreach ($resrek as $data) {
	
	//drupal_set_message($data->kodero);
	
	$x = db_insert('rekeninglengkap1')
		->fields(array('kodero', 'kodeakunutama', 'kodeakunkelompok', 'kodeakunjenis', 'kodeakunobjek', 'kodeakunrincian', 'namaakunrincian', 'akunrincian'))
		->values(array(
				'kodero'=> $data->kodero,
				'kodeakunutama' => substr($data->kodero, 0, 1),
				'kodeakunkelompok' => substr($data->kodero, 0, 2),
				'kodeakunjenis' => substr($data->kodero, 0, 3),
				'kodeakunobjek' => substr($data->kodero, 0, 5),
				'kodeakunrincian' => $data->kodero,
				'namaakunrincian' => $data->uraian,
				'akunrincian' => $data->kodero . ' - ' . $data->uraian,
				))
		->execute();	
		
	drupal_set_message($x);
}	
}


function createXML_APBD() {
	
$writer = new XMLWriter();  
//$writer->openURI('php://output');   
//$writer->openURI('/test.xml');   
$writer->openMemory();
$writer->startDocument('1.0','UTF-8');   
$writer->setIndent(4);   

$pendapatan_t = 0;
$belanja_t = 0;

//file_put_contents('files/xml/2018991265LRA0' . $bulan . '.xml', $writer->flush(true));
$fname = 'files/xml/2018991265APBD00.xml';
file_put_contents($fname, $writer->flush(true));
$writer->startElement('ns2:realisasiAPBDWS'); 
$writer->writeAttribute('xmlns:ns2', 'http://service.sikd.app/'); 

	$writer->writeElement('kodeSatker', '991265'); 
	$writer->writeElement('kodePemda', '11.10');
	$writer->writeElement('namaPemda', 'Kabupaten Jepara');
	$writer->writeElement('tahunAnggaran', '2018');
	$writer->writeElement('kodeData', '0');
	$writer->writeElement('jenisCOA', '1');
	$writer->writeElement('statusData', '0');
	$writer->writeElement('nomorPerda', 'Tidak Ada No Perda');
	$writer->writeElement('tanggalPerda', '2018-01-01');
	$writer->writeElement('userName', '991265');
	$writer->writeElement('password', '-29-35118-9840-765865');
	$writer->writeElement('namaAplikasi', 'simKEDA');
	$writer->writeElement('pengembangAplikasi', 'Astrotama');
	
	//PENDAPATAN 
	$i = 0;
	$reskegmaster = db_query('select distinct kodeuk from anggperuk_sikd}');
	foreach ($reskegmaster as $datakegmaster) {
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusan); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', '000'); 
			$writer->writeElement('namaProgram', 'Non Program'); 
			$writer->writeElement('kodeKegiatan', '000000'); 
			$writer->writeElement('namaKegiatan', 'Non Kegiatan'); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 

				//REKENING
				$resrek = db_query('SELECT kodero, jumlah FROM {anggperuk_sikd} WHERE kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
				foreach ($resrek as $datarek) {
					
						$pendapatan_t += $datarek->jumlah;
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							$writer->startElement("kodeRekenings"); 
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobjek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->jumlah); 
							
							$writer->endElement();		//END REKENING
						}	
						
					
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}		
	}
	file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	
	//BELANJA
	$i=0;
	$reskegmaster = db_query('select kodeuk, kodekeg from kegiatanskpd_sikd where inaktif=:inaktif', array(':inaktif' => '0'));
	foreach ($reskegmaster as $datakegmaster) {
		
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd_sikd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 

			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', $datakeg->kodepro); 
			$writer->writeElement('namaProgram', $datakeg->program); 
			$writer->writeElement('kodeKegiatan', substr($datakegmaster->kodekeg,-6)); 
			$writer->writeElement('namaKegiatan', $datakeg->kegiatan); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 

				//REKENING
				$resrek = db_query('select kodero, jumlah FROM {anggperkeg_sikd} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
				foreach ($resrek as $datarek) {
					$writer->startElement("kodeRekenings"); 
						
						$belanja_t += $datarek->jumlah;
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobjek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->jumlah); 
						}	
						
					$writer->endElement();		//END REKENING
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}	
	}	
	
	//PEMBIAYAAN 
	
	$penerimaan = 0;
	$pengeluaran = 0;
	
	$i = 0;
	$reskegmaster = db_query('select kodeuk from unitkerja where kodeuk=:kodeuk', array(':kodeuk'=>'81'));
	foreach ($reskegmaster as $datakegmaster) {
		$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
		foreach ($reskeg as $datakeg) {
		 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
			$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
			$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
			$writer->writeElement('namaUrusanPelaksana', $datakeg->urusan); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeuk . '00'); 
			$writer->writeElement('namaSKPD', $datakeg->namauk); 
			$writer->writeElement('kodeProgram', '000'); 
			$writer->writeElement('namaProgram', 'Non Program'); 
			$writer->writeElement('kodeKegiatan', '000000'); 
			$writer->writeElement('namaKegiatan', 'Non Kegiatan'); 
			$writer->writeElement('kodeFungsi', $datakeg->kodef); 
			$writer->writeElement('namaFungsi', $datakeg->fungsi); 

				//REKENING
				$resrek = db_query('SELECT kodero, jumlah FROM {anggperda_sikd}');
				foreach ($resrek as $datarek) {
					
						if (substr($datarek->kodero, 0,2)=='61')
							$penerimaan += $datarek->jumlah;
						else
							$pengeluaran += $datarek->jumlah;
						
						$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
						foreach ($resinforek as $datainforek) {
							$writer->startElement("kodeRekenings"); 
							
							$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
							$writer->writeElement('namaAkunUtama', $datainforek->namaakunutama); 
							
							$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
							$writer->writeElement('namaAkunKelompok', $datainforek->namaakunkelompok); 
							
							$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
							$writer->writeElement('namaAkunJenis', $datainforek->namaakunjenis); 
							
							$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
							$writer->writeElement('namaAkunObjek', $datainforek->namaakunobjek); 
							
							$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
							$writer->writeElement('namaAkunRincian', $datainforek->namaakunrincian); 

							$writer->writeElement('kodeAkunSub', ''); 
							$writer->writeElement('namaAkunSub', '');

							$writer->writeElement('nilaiAnggaran', $datarek->jumlah); 
							
							$writer->endElement();		//END REKENING
						}	
						
					
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}		
	}
	file_put_contents($fname, $writer->flush(true), FILE_APPEND);	


$writer->endElement(); 			//ns2	

drupal_set_message($pendapatan_t);
drupal_set_message($belanja_t);

//drupal_set_message($penerimaan);
//drupal_set_message($pengeluaran);
//drupal_set_message('http://tatausaha.simkedajepara.net/'.$fname);
file_put_contents($fname, $writer->flush(true), FILE_APPEND);
//drupal_goto('http://tatausaha.simkedajepara.net/'.$fname);

/*
	$zip = new ZipArchive();
	$filename = 'files/xml/2018991265APBD00.zip';

	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
		drupal_set_message('failed');	
	   
	} else {
		$zip->addFile($fname);
		$zip->close();
		drupal_set_message('ok');
	}
	drupal_goto('http://tatausaha.simkedajepara.net/'.$filename);
*/	
}

function createXML_DTHRTH($bulan) {
	
$writer = new XMLWriter();  
//$writer->openURI('php://output');   
//$writer->openURI('/test.xml');   
$writer->openMemory();
$writer->startDocument('1.0','UTF-8');   
$writer->setIndent(4);   

//file_put_contents('files/xml/2018991265LRA0' . $bulan . '.xml', $writer->flush(true));
$fname = 'files/xml/2018991265DTH0' . $bulan . '.xml';
file_put_contents($fname, $writer->flush(true));
$writer->startElement('ns2:realisasiAPBDWS'); 
$writer->writeAttribute('xmlns:ns2', 'http://service.sikd.app/'); 

	$writer->writeElement('kodeSatker', '991265'); 
	$writer->writeElement('kodePemda', '11.10');
	$writer->writeElement('namaPemda', 'Kabupaten Jepara');
	$writer->writeElement('tahunAnggaran', '2018');
	$writer->writeElement('periode', $bulan);
	$writer->writeElement('statusData', '0');
	$writer->writeElement('userName', '991265');
	$writer->writeElement('password', '-29-35118-9840-765865');
	$writer->writeElement('namaAplikasi', 'simKEDA');
	$writer->writeElement('pengembangAplikasi', 'Astrotama4');
	
	//BELANJA KELOMPOK 0
	$i=0;
	$res_sp2d = db_query('select d.kodeuk, d.dokid, d.spmno, d.sp2dno, d.sp2dtgl, d.spmno, d.spdno, d.jenisdokumen, d.jumlah, d.potongan, d.penerimanpwp, d.penerimanama, d.keperluan, d.kodekeg, u.namasingkat, u.bendaharanpwp, u.bendaharanama from {dokumen} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where d.sp2dok=1 and month(d.sp2dtgl)=:bulan and u.kelompok=0', array(':bulan' => $bulan));
	foreach ($res_sp2d as $data_sp2d) {
		
		$i++;
		
		$sumberdana = 1;

		$totalpajak = hitung_pajak($data_sp2d->dokid);
		
		//tw
		$triwulan = substr($data_sp2d->spdno, -1);
		if ($triwulan=='d') $triwulan = '0';
		
		//penerima
		if (($data_sp2d->jenisdokumen=='4') or ($data_sp2d->jenisdokumen=='6') or ($data_sp2d->jenisdokumen=='8'))
			$penerimanama = $data_sp2d->penerimanama;
		else
			$penerimanama = 'BEND PENGEL ' . $data_sp2d->namasingkat . ' (' . $data_sp2d->bendaharanama . ')';
		
		$writer->startElement('rincians');
		$writer->writeElement('nomorSPM', $data_sp2d->spmno);
		$writer->writeElement('nomorSP2D', $data_sp2d->sp2dno);
		$writer->writeElement('jenisSP2D', $data_sp2d->jenisdokumen);
		$writer->writeElement('tanggalSP2D', $data_sp2d->sp2dtgl);
		$writer->writeElement('nilaiSP2D', $data_sp2d->jumlah);
		$writer->writeElement('nilaiTotalPajak', $totalpajak);
		$writer->writeElement('nilaiTotalPotongan', $data_sp2d->potongan);
		$writer->writeElement('npwpBUD', '000000');
		$writer->writeElement('npwpSKPD', $data_sp2d->bendaharanpwp);
		$writer->writeElement('npwpPenerima', $data_sp2d->penerimanpwp);
		$writer->writeElement('namaPenerima', $penerimanama);
		$writer->writeElement('sumberDana', $sumberdana);
		$writer->writeElement('subSumberDana', '');
		$writer->writeElement('tahapSalurDana', $triwulan);
		$writer->writeElement('keterangan', $data_sp2d->keperluan);
		
		if (($data_sp2d->jenisdokumen=='0') or ($data_sp2d->jenisdokumen=='2') or ($data_sp2d->jenisdokumen=='6') or ($data_sp2d->jenisdokumen=='8')) {		//UP+TU
			$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, u.urusan as urusandinas, uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $data_sp2d->kodeuk));
			
		} else {
			$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $data_sp2d->kodekeg));

		}
		foreach ($reskeg as $datakeg) {
			
			$writer->startElement("kegiatans"); 
			
			if (($data_sp2d->jenisdokumen=='0') or ($data_sp2d->jenisdokumen=='2') or ($data_sp2d->jenisdokumen=='6') or ($data_sp2d->jenisdokumen=='8')) {		//UP+TU
				$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
				$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
				$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
				$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 
				$writer->writeElement('kodeSKPD', $data_sp2d->kodeuk . '00'); 
				$writer->writeElement('namaSKPD', $datakeg->namauk); 
				$writer->writeElement('kodeProgram', '000'); 
				$writer->writeElement('namaProgram', 'Non Program'); 
				$writer->writeElement('kodeKegiatan', '000000'); 
				$writer->writeElement('namaKegiatan', 'Non Kegiatan'); 
				//$writer->writeElement('kodeFungsi', $datakeg->kodef); 
				//$writer->writeElement('namaFungsi', $datakeg->fungsi);		
			
			} else {
				$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
				$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
				$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
				$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 
				$writer->writeElement('kodeSKPD', $data_sp2d->kodeuk . '00'); 
				$writer->writeElement('namaSKPD', $datakeg->namauk); 
				$writer->writeElement('kodeProgram', $datakeg->kodepro); 
				$writer->writeElement('namaProgram', $datakeg->program); 
				$writer->writeElement('kodeKegiatan', substr($data_sp2d->kodekeg,-6)); 
				$writer->writeElement('namaKegiatan', $datakeg->kegiatan); 
				//$writer->writeElement('kodeFungsi', $datakeg->kodef); 
				//$writer->writeElement('namaFungsi', $datakeg->fungsi);			
			}
			
			//REKENING
			//UP+TU
			if (($data_sp2d->jenisdokumen=='0') or ($data_sp2d->jenisdokumen=='2')) {
				
				//11102001
				$kodekasbp = apbd_getKodeROKasBendaharaPengeluaran();
				$writer->startElement("akuns"); 
					
				$writer->writeElement('kodeAkunUtama', substr($kodekasbp, 0,1)); 
				$writer->writeElement('namaAkunUtama', 'KAS'); 
				
				$writer->writeElement('kodeAkunKelompok', substr($kodekasbp, 1,1)); 
				$writer->writeElement('namaAkunKelompok', 'Kas di Bendahara Pengeluaran'); 
				
				$writer->writeElement('kodeAkunJenis', substr($kodekasbp, 2,1)); 
				$writer->writeElement('namaAkunJenis', 'Kas di Bendahara Pengeluaran'); 
				
				$writer->writeElement('kodeAkunObjek', substr($kodekasbp, 3,2)); 
				$writer->writeElement('namaAkunObjek', 'Kas di Bendahara Pengeluaran'); 
				
				$writer->writeElement('kodeAkunRincian', substr($kodekasbp, -3)); 
				$writer->writeElement('namaAkunRincian', 'Kas di Bendahara Pengeluaran'); 

				$writer->writeElement('kodeAkunSub', ''); 
				$writer->writeElement('namaAkunSub', '');

				$writer->writeElement('nilaiRekening', $datarek->jumlah); 
					
				$writer->endElement();		//END akuns	
				
			} else {
				$resrek = db_query('select r.namaakunutama, r.namaakunkelompok, r.namaakunjenis, r.namaakunobjek, r.namaakunrincian, dr.kodero,  dr.jumlah FROM {dokumenrekening} dr INNER JOIN {rekeninglengkap} r ON dr.kodero=r.kodero where dr.jumlah>0 and dr.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("akuns"); 
						
					$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
					$writer->writeElement('namaAkunUtama', $datarek->namaakunutama); 
					
					$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
					$writer->writeElement('namaAkunKelompok', $datarek->namaakunkelompok); 
					
					$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
					$writer->writeElement('namaAkunJenis', $datarek->namaakunjenis); 
					
					$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
					$writer->writeElement('namaAkunObjek', $datarek->namaakunobjek); 
					
					$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
					$writer->writeElement('namaAkunRincian', $datarek->namaakunrincian); 

					$writer->writeElement('kodeAkunSub', ''); 
					$writer->writeElement('namaAkunSub', '');

					$writer->writeElement('nilaiRekening', $datarek->jumlah); 
						
					$writer->endElement();		//END akuns
				}
			}	//END Jenis Dokumen
			$writer->endElement(); 			//END KEGIATAN	
			
			//POTONGAN
			if ($data_sp2d->jenisdokumen=='3') {
				$resrek = db_query('select p.kodepotongan, p.uraian, dp.jumlah FROM {dokumenpotongan} dp INNER JOIN {ltpotongan} p ON dp.kodepotongan=p.kodepotongan where dp.jumlah>0 and dp.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("pajaks");
					
					$writer->writeElement('kodeAkunPajak', '2.1.1.01.'. $datarek->kodepotongan); 
					$writer->writeElement('namaAkunPajak', $datarek->uraian); 
					$writer->writeElement('jenisPajak', '10' . $datarek->kodepotongan); 
					$writer->writeElement('nilaiPotongan', $datarek->jumlah); 

					$writer->endElement(); 			//END POTONGAN
				}	
			}
			
			//PAJAK
			//GU
			if (($data_sp2d->jenisdokumen=='1') or ($data_sp2d->jenisdokumen=='5') or ($data_sp2d->jenisdokumen=='7')) {
				
				db_set_active('bendahara');
				$resrek = db_query('select p.kodepajak, p.uraian, bp.jumlah FROM {bendaharapajak} bp inner join {ltpajak} p on bp.kodepajak=p.kodepajak inner join {bendahara} b on bp.bendid=b.bendid where b.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("pajaks");
					
					$writer->writeElement('kodeAkunPajak', '2.2.1.01.'. $datarek->kodepajak); 
					$writer->writeElement('namaAkunPajak', $datarek->uraian); 
					$writer->writeElement('jenisPajak', '11' . $datarek->kodepajak); 
					$writer->writeElement('nilaiPotongan', $datarek->jumlah); 

					$writer->endElement(); 			//END PAJAK
				}				
				db_set_active();
			
			//LS
			} else {
				$resrek = db_query('select p.kodepajak, p.uraian, dp.jumlah FROM {dokumenpajak} dp INNER JOIN {ltpajak} p ON dp.kodepajak=p.kodepajak where dp.jumlah>0 and dp.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("pajaks");
					
					$writer->writeElement('kodeAkunPajak', '2.2.1.01.'. $datarek->kodepajak); 
					$writer->writeElement('namaAkunPajak', $datarek->uraian); 
					$writer->writeElement('jenisPajak', '11' . $datarek->kodepajak); 
					$writer->writeElement('nilaiPotongan', $datarek->jumlah); 

					$writer->endElement(); 			//END PAJAK
				}	
			}
		}
			

		if ($i==10) {
			file_put_contents($fname, $writer->flush(true), FILE_APPEND);
			$i = 0;
		} else
			$i++;
		
		$writer->endElement(); 			//END RINCIAN (sp2d)
	}	


	//BELANJA KELOMPOK > 0
	$i=0;
	$res_sp2d = db_query('select d.kodeuk, d.dokid, d.spmno, d.sp2dno, d.sp2dtgl, d.spmno, d.spdno, d.jenisdokumen, d.jumlah, d.potongan, d.penerimanpwp, d.penerimanama, d.keperluan, d.kodekeg, u.namasingkat, u.bendaharanpwp, u.bendaharanama from {dokumen} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where d.sp2dok=1 and month(d.sp2dtgl)=:bulan and u.kelompok>0', array(':bulan' => $bulan));
	foreach ($res_sp2d as $data_sp2d) {
		
		$i++;
		
		$sumberdana = 1;

		$totalpajak = hitung_pajak($data_sp2d->dokid);
		
		//tw
		$triwulan = substr($data_sp2d->spdno, -1);
		if ($triwulan=='d') $triwulan = '0';
		
		//penerima
		if (($data_sp2d->jenisdokumen=='4') or ($data_sp2d->jenisdokumen=='6') or ($data_sp2d->jenisdokumen=='8'))
			$penerimanama = $data_sp2d->penerimanama;
		else
			$penerimanama = 'BEND PENGEL ' . $data_sp2d->namasingkat . ' (' . $data_sp2d->bendaharanama . ')';
		
		$writer->startElement('rincians');
		$writer->writeElement('nomorSPM', $data_sp2d->spmno);
		$writer->writeElement('nomorSP2D', $data_sp2d->sp2dno);
		$writer->writeElement('jenisSP2D', $data_sp2d->jenisdokumen);
		$writer->writeElement('tanggalSP2D', $data_sp2d->sp2dtgl);
		$writer->writeElement('nilaiSP2D', $data_sp2d->jumlah);
		$writer->writeElement('nilaiTotalPajak', $totalpajak);
		$writer->writeElement('nilaiTotalPotongan', $data_sp2d->potongan);
		$writer->writeElement('npwpBUD', '000000');
		$writer->writeElement('npwpSKPD', $data_sp2d->bendaharanpwp);
		$writer->writeElement('npwpPenerima', $data_sp2d->penerimanpwp);
		$writer->writeElement('namaPenerima', $penerimanama);
		$writer->writeElement('sumberDana', $sumberdana);
		$writer->writeElement('subSumberDana', '');
		$writer->writeElement('tahapSalurDana', $triwulan);
		$writer->writeElement('keterangan', $data_sp2d->keperluan);
		
		if (($data_sp2d->jenisdokumen=='0') or ($data_sp2d->jenisdokumen=='2') or ($data_sp2d->jenisdokumen=='6') or ($data_sp2d->jenisdokumen=='8')) {		//UP+TU
			$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, u.urusan as urusandinas, uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $data_sp2d->kodeuk));
			
		} else {
			$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $data_sp2d->kodekeg));

		}
		foreach ($reskeg as $datakeg) {
			
			$writer->startElement("kegiatans"); 
			
			if (($data_sp2d->jenisdokumen=='0') or ($data_sp2d->jenisdokumen=='2') or ($data_sp2d->jenisdokumen=='6') or ($data_sp2d->jenisdokumen=='8')) {		//UP+TU
				$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
				$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
				$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
				$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 
				$writer->writeElement('kodeSKPD', $data_sp2d->kodeuk . '00'); 
				$writer->writeElement('namaSKPD', $datakeg->namauk); 
				$writer->writeElement('kodeProgram', '000'); 
				$writer->writeElement('namaProgram', 'Non Program'); 
				$writer->writeElement('kodeKegiatan', '000000'); 
				$writer->writeElement('namaKegiatan', 'Non Kegiatan'); 
				//$writer->writeElement('kodeFungsi', $datakeg->kodef); 
				//$writer->writeElement('namaFungsi', $datakeg->fungsi);		
			
			} else {
				$writer->writeElement('kodeUrusanProgram', substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.') ; 
				$writer->writeElement('namaUrusanProgram', $datakeg->urusan); 
				$writer->writeElement('kodeUrusanPelaksana', substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.'); 
				$writer->writeElement('namaUrusanPelaksana', $datakeg->urusandinas); 
				$writer->writeElement('kodeSKPD', $data_sp2d->kodeuk . '00'); 
				$writer->writeElement('namaSKPD', $datakeg->namauk); 
				$writer->writeElement('kodeProgram', $datakeg->kodepro); 
				$writer->writeElement('namaProgram', $datakeg->program); 
				$writer->writeElement('kodeKegiatan', substr($data_sp2d->kodekeg,-6)); 
				$writer->writeElement('namaKegiatan', $datakeg->kegiatan); 
				//$writer->writeElement('kodeFungsi', $datakeg->kodef); 
				//$writer->writeElement('namaFungsi', $datakeg->fungsi);			
			}
			
			//REKENING
			//UP+TU
			if (($data_sp2d->jenisdokumen=='0') or ($data_sp2d->jenisdokumen=='2')) {
				
				//11102001
				$kodekasbp = apbd_getKodeROKasBendaharaPengeluaran();
				$writer->startElement("akuns"); 
					
				$writer->writeElement('kodeAkunUtama', substr($kodekasbp, 0,1)); 
				$writer->writeElement('namaAkunUtama', 'KAS'); 
				
				$writer->writeElement('kodeAkunKelompok', substr($kodekasbp, 1,1)); 
				$writer->writeElement('namaAkunKelompok', 'Kas di Bendahara Pengeluaran'); 
				
				$writer->writeElement('kodeAkunJenis', substr($kodekasbp, 2,1)); 
				$writer->writeElement('namaAkunJenis', 'Kas di Bendahara Pengeluaran'); 
				
				$writer->writeElement('kodeAkunObjek', substr($kodekasbp, 3,2)); 
				$writer->writeElement('namaAkunObjek', 'Kas di Bendahara Pengeluaran'); 
				
				$writer->writeElement('kodeAkunRincian', substr($kodekasbp, -3)); 
				$writer->writeElement('namaAkunRincian', 'Kas di Bendahara Pengeluaran'); 

				$writer->writeElement('kodeAkunSub', ''); 
				$writer->writeElement('namaAkunSub', '');

				$writer->writeElement('nilaiRekening', $datarek->jumlah); 
					
				$writer->endElement();		//END akuns	
				
			} else {
				$resrek = db_query('select r.namaakunutama, r.namaakunkelompok, r.namaakunjenis, r.namaakunobjek, r.namaakunrincian, dr.kodero,  dr.jumlah FROM {dokumenrekening} dr INNER JOIN {rekeninglengkap} r ON dr.kodero=r.kodero where dr.jumlah>0 and dr.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("akuns"); 
						
					$writer->writeElement('kodeAkunUtama', substr($datarek->kodero, 0,1)); 
					$writer->writeElement('namaAkunUtama', $datarek->namaakunutama); 
					
					$writer->writeElement('kodeAkunKelompok', substr($datarek->kodero, 1,1)); 
					$writer->writeElement('namaAkunKelompok', $datarek->namaakunkelompok); 
					
					$writer->writeElement('kodeAkunJenis', substr($datarek->kodero, 2,1)); 
					$writer->writeElement('namaAkunJenis', $datarek->namaakunjenis); 
					
					$writer->writeElement('kodeAkunObjek', substr($datarek->kodero, 3,2)); 
					$writer->writeElement('namaAkunObjek', $datarek->namaakunobjek); 
					
					$writer->writeElement('kodeAkunRincian', substr($datarek->kodero, -3)); 
					$writer->writeElement('namaAkunRincian', $datarek->namaakunrincian); 

					$writer->writeElement('kodeAkunSub', ''); 
					$writer->writeElement('namaAkunSub', '');

					$writer->writeElement('nilaiRekening', $datarek->jumlah); 
						
					$writer->endElement();		//END akuns
				}
			}	//END Jenis Dokumen
			$writer->endElement(); 			//END KEGIATAN	
			
			//POTONGAN
			if ($data_sp2d->jenisdokumen=='3') {
				$resrek = db_query('select p.kodepotongan, p.uraian, dp.jumlah FROM {dokumenpotongan} dp INNER JOIN {ltpotongan} p ON dp.kodepotongan=p.kodepotongan where dp.jumlah>0 and dp.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("pajaks");
					
					$writer->writeElement('kodeAkunPajak', '2.1.1.01.'. $datarek->kodepotongan); 
					$writer->writeElement('namaAkunPajak', $datarek->uraian); 
					$writer->writeElement('jenisPajak', '10' . $datarek->kodepotongan); 
					$writer->writeElement('nilaiPotongan', $datarek->jumlah); 

					$writer->endElement(); 			//END POTONGAN
				}	
			}
			
			//PAJAK
			//GU
			if (($data_sp2d->jenisdokumen=='1') or ($data_sp2d->jenisdokumen=='5') or ($data_sp2d->jenisdokumen=='7')) {
				
				db_set_active('bendahara');
				$resrek = db_query('select p.kodepajak, p.uraian, bp.jumlah FROM {bendaharapajak} bp inner join {ltpajak} p on bp.kodepajak=p.kodepajak inner join {bendahara} b on bp.bendid=b.bendid where b.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("pajaks");
					
					$writer->writeElement('kodeAkunPajak', '2.2.1.01.'. $datarek->kodepajak); 
					$writer->writeElement('namaAkunPajak', $datarek->uraian); 
					$writer->writeElement('jenisPajak', '11' . $datarek->kodepajak); 
					$writer->writeElement('nilaiPotongan', $datarek->jumlah); 

					$writer->endElement(); 			//END PAJAK
				}				
				db_set_active();
			
			//LS
			} else {
				$resrek = db_query('select p.kodepajak, p.uraian, dp.jumlah FROM {dokumenpajak} dp INNER JOIN {ltpajak} p ON dp.kodepajak=p.kodepajak where dp.jumlah>0 and dp.dokid=:dokid', array(':dokid' => $data_sp2d->dokid));
				foreach ($resrek as $datarek) {
					$writer->startElement("pajaks");
					
					$writer->writeElement('kodeAkunPajak', '2.2.1.01.'. $datarek->kodepajak); 
					$writer->writeElement('namaAkunPajak', $datarek->uraian); 
					$writer->writeElement('jenisPajak', '11' . $datarek->kodepajak); 
					$writer->writeElement('nilaiPotongan', $datarek->jumlah); 

					$writer->endElement(); 			//END PAJAK
				}	
			}
		}
			

		if ($i==10) {
			file_put_contents($fname, $writer->flush(true), FILE_APPEND);
			$i = 0;
		} else
			$i++;
		
		$writer->endElement(); 			//END RINCIAN (sp2d)
	}	


$writer->endElement(); 			//ns2	

//$writer->endDocument();   
//$writer->flush(); 

file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	$zip = new ZipArchive();
	$filename = 'files/xml/2018991265APBD00.zip';

	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
		drupal_set_message('failed');	
	   
	} else {
		$zip->addFile($fname);
		$zip->close();
		drupal_set_message('ok');
	}
	drupal_goto('http://tatausaha.simkedajepara.net/'.$filename);

}

function createXML_LRA_2016() {
	
$writer = new XMLWriter();  
//$writer->openURI('php://output');   
//$writer->openURI('/test.xml');   
$writer->openMemory();
$writer->startDocument('1.0','UTF-8');   
$writer->setIndent(4);   

//file_put_contents('files/xml/2018991265LRA0' . $bulan . '.xml', $writer->flush(true));
$fname = 'files/xml/2016991265LRA0.xml';
file_put_contents($fname, $writer->flush(true));
$writer->startElement('ns2:realisasiAPBDWS'); 
$writer->writeAttribute('xmlns:ns2', 'http://service.sikd.app/'); 

	$writer->writeElement('kodeSatker', '991265'); 
	$writer->writeElement('kodePemda', '11.10');
	$writer->writeElement('namaPemda', 'Kabupaten Jepara');
	$writer->writeElement('tahunAnggaran', '2016');
	$writer->writeElement('periode', '0');
	$writer->writeElement('kodeData', '0');
	$writer->writeElement('jenisCOA', '1');
	$writer->writeElement('statusData', '0');
	$writer->writeElement('nomorPerda', '-');
	$writer->writeElement('tanggalPerda', '-');
	$writer->writeElement('userName', '991265');
	$writer->writeElement('password', '-29-35118-9840-765865');
	$writer->writeElement('namaAplikasi', 'simKEDA');
	$writer->writeElement('pengembangAplikasi', 'Astrotama');
	
	
	//PENDAPATAN 
	$i = 0;
	$reskegmaster = db_query('select distinct kodeUrusanProgram,namaUrusanProgram,kodeUrusanPelaksana, namaUrusanPelaksana,kodeSKPD,namaSKPD,kodeProgram,namaProgram,kodeKegiatan,namaKegiatan,kodeFungsi,namaFungsi from {realisasi_sikd_2016} where kodeAkunUtama=:akun', array(':akun'=>'4'));
	foreach ($reskegmaster as $datakegmaster) {

		 
		$writer->startElement("kegiatans"); 
		$writer->writeElement('kodeUrusanProgram', $datakegmaster->kodeUrusanProgram) ; 
		$writer->writeElement('namaUrusanProgram', $datakegmaster->namaUrusanProgram); 
		$writer->writeElement('kodeUrusanPelaksana', $datakegmaster->kodeUrusanPelaksana); 
		$writer->writeElement('namaUrusanPelaksana', $datakegmaster->namaUrusanPelaksana); 
		$writer->writeElement('kodeSKPD', $datakegmaster->kodeSKPD); 
		$writer->writeElement('namaSKPD', $datakegmaster->namaSKPD); 
		$writer->writeElement('kodeProgram', $datakegmaster->kodeProgram); 
		$writer->writeElement('namaProgram', $datakegmaster->namaProgram); 
		$writer->writeElement('kodeKegiatan', $datakegmaster->kodeKegiatan); 
		$writer->writeElement('namaKegiatan', $datakegmaster->namaKegiatan); 
		$writer->writeElement('kodeFungsi', $datakegmaster->kodeFungsi); 
		$writer->writeElement('namaFungsi', $datakegmaster->namaFungsi); 

			//REKENING
			$resrek = db_query('SELECT kodeAkunUtama,namaAkunUtama,kodeAkunKelompok,namaAkunKelompok, kodeAkunJenis,namaAkunJenis,kodeAkunObjek,namaAkunObjek,kodeAkunRincian,namaAkunRincian,nilaiRealisasi FROM {realisasi_sikd_2016} WHERE kodeAkunUtama=:akun and kodeSKPD=:kodeSKPD', array(':akun' => '4', ':kodeSKPD' => $datakegmaster->kodeSKPD));
			foreach ($resrek as $datarek) {
				
					
				$writer->startElement("kodeRekenings"); 
				
				$writer->writeElement('kodeAkunUtama', $datarek->kodeAkunUtama); 
				$writer->writeElement('namaAkunUtama', $datarek->namaAkunUtama); 
				
				$writer->writeElement('kodeAkunKelompok', $datarek->kodeAkunKelompok); 
				$writer->writeElement('namaAkunKelompok', $datarek->namaAkunKelompok); 
				
				$writer->writeElement('kodeAkunJenis', $datarek->kodeAkunJenis); 
				$writer->writeElement('namaAkunJenis', $datarek->namaAkunJenis); 
				
				$writer->writeElement('kodeAkunObjek', $datarek->kodeAkunObjek); 
				$writer->writeElement('namaAkunObjek', $datarek->namaAkunObjek); 
				
				$writer->writeElement('kodeAkunRincian', $datarek->kodeAkunRincian); 
				$writer->writeElement('namaAkunRincian', $datarek->kodeAkunRincian); 

				$writer->writeElement('kodeAkunSub', ''); 
				$writer->writeElement('namaAkunSub', '');

				$writer->writeElement('nilaiRealisasi', $datarek->nilaiRealisasi); 
				
				$writer->endElement();		//END REKENING
		
			}

		$writer->endElement(); 			//END KEGIATAN	
		
		if ($i==50) {
			file_put_contents($fname, $writer->flush(true), FILE_APPEND);
			$i = 0;
		} else
			$i++;
	}
	file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	
	
	
	//BELANJA 
	$i = 0;

	//$resuk = db_query('select distinct kodeSKPD from {realisasi_sikd_2016} where kodeAkunUtama=:akun', array(':akun'=>'5'));
	$resuk = db_query("select distinct kodeSKPD from {realisasi_sikd_2016} where kodeSKPD>'7500' and kodeAkunUtama='5'");
	foreach ($resuk as $datauk) {
		$reskegmaster = db_query('select distinct kodeUrusanProgram,namaUrusanProgram,kodeUrusanPelaksana, namaUrusanPelaksana,kodeSKPD,namaSKPD,kodeProgram,namaProgram,kodeKegiatan,namaKegiatan,kodeFungsi,namaFungsi from {realisasi_sikd_2016} where kodeAkunUtama=:akun and kodeSKPD=:kodeSKPD', array(':akun'=>'5', ':kodeSKPD'=>$datauk->kodeSKPD));
		foreach ($reskegmaster as $datakegmaster) {

			 
			$writer->startElement("kegiatans"); 
			$writer->writeElement('kodeUrusanProgram', $datakegmaster->kodeUrusanProgram) ; 
			$writer->writeElement('namaUrusanProgram', $datakegmaster->namaUrusanProgram); 
			$writer->writeElement('kodeUrusanPelaksana', $datakegmaster->kodeUrusanPelaksana); 
			$writer->writeElement('namaUrusanPelaksana', $datakegmaster->namaUrusanPelaksana); 
			$writer->writeElement('kodeSKPD', $datakegmaster->kodeSKPD); 
			$writer->writeElement('namaSKPD', $datakegmaster->namaSKPD); 
			$writer->writeElement('kodeProgram', $datakegmaster->kodeProgram); 
			$writer->writeElement('namaProgram', $datakegmaster->namaProgram); 
			$writer->writeElement('kodeKegiatan', $datakegmaster->kodeKegiatan); 
			$writer->writeElement('namaKegiatan', $datakegmaster->namaKegiatan); 
			$writer->writeElement('kodeFungsi', $datakegmaster->kodeFungsi); 
			$writer->writeElement('namaFungsi', $datakegmaster->namaFungsi); 

				//REKENING
				$resrek = db_query('SELECT kodeAkunUtama,namaAkunUtama,kodeAkunKelompok,namaAkunKelompok, kodeAkunJenis,namaAkunJenis,kodeAkunObjek,namaAkunObjek,kodeAkunRincian,namaAkunRincian,nilaiRealisasi FROM {realisasi_sikd_2016} WHERE kodeAkunUtama=:akun and kodeSKPD=:kodeSKPD', array(':akun' => '5', ':kodeSKPD' => $datakegmaster->kodeSKPD));
				foreach ($resrek as $datarek) {
					
						
					$writer->startElement("kodeRekenings"); 
					
					$writer->writeElement('kodeAkunUtama', $datarek->kodeAkunUtama); 
					$writer->writeElement('namaAkunUtama', $datarek->namaAkunUtama); 
					
					$writer->writeElement('kodeAkunKelompok', $datarek->kodeAkunKelompok); 
					$writer->writeElement('namaAkunKelompok', $datarek->namaAkunKelompok); 
					
					$writer->writeElement('kodeAkunJenis', $datarek->kodeAkunJenis); 
					$writer->writeElement('namaAkunJenis', $datarek->namaAkunJenis); 
					
					$writer->writeElement('kodeAkunObjek', $datarek->kodeAkunObjek); 
					$writer->writeElement('namaAkunObjek', $datarek->namaAkunObjek); 
					
					$writer->writeElement('kodeAkunRincian', $datarek->kodeAkunRincian); 
					$writer->writeElement('namaAkunRincian', $datarek->kodeAkunRincian); 

					$writer->writeElement('kodeAkunSub', ''); 
					$writer->writeElement('namaAkunSub', '');

					$writer->writeElement('nilaiRealisasi', $datarek->nilaiRealisasi); 
					
					$writer->endElement();		//END REKENING
			
				}

			$writer->endElement(); 			//END KEGIATAN	
			
			if ($i==50) {
				file_put_contents($fname, $writer->flush(true), FILE_APPEND);
				$i = 0;
			} else
				$i++;
		}
		file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	}
		

		
	//PEMBIAYAAN 
	/*
	$i = 0;
	$reskegmaster = db_query('select distinct kodeUrusanProgram,namaUrusanProgram,kodeUrusanPelaksana, namaUrusanPelaksana,kodeSKPD,namaSKPD,kodeProgram,namaProgram,kodeKegiatan,namaKegiatan,kodeFungsi,namaFungsi from {realisasi_sikd_2016} where kodeAkunUtama=:akun', array(':akun'=>'6'));
	foreach ($reskegmaster as $datakegmaster) {

		 
		$writer->startElement("kegiatans"); 
		$writer->writeElement('kodeUrusanProgram', $datakegmaster->kodeUrusanProgram) ; 
		$writer->writeElement('namaUrusanProgram', $datakegmaster->namaUrusanProgram); 
		$writer->writeElement('kodeUrusanPelaksana', $datakegmaster->kodeUrusanPelaksana); 
		$writer->writeElement('namaUrusanPelaksana', $datakegmaster->namaUrusanPelaksana); 
		$writer->writeElement('kodeSKPD', $datakegmaster->kodeSKPD); 
		$writer->writeElement('namaSKPD', $datakegmaster->namaSKPD); 
		$writer->writeElement('kodeProgram', $datakegmaster->kodeProgram); 
		$writer->writeElement('namaProgram', $datakegmaster->namaProgram); 
		$writer->writeElement('kodeKegiatan', $datakegmaster->kodeKegiatan); 
		$writer->writeElement('namaKegiatan', $datakegmaster->namaKegiatan); 
		$writer->writeElement('kodeFungsi', $datakegmaster->kodeFungsi); 
		$writer->writeElement('namaFungsi', $datakegmaster->namaFungsi); 

			//REKENING
			$resrek = db_query('SELECT kodeAkunUtama,namaAkunUtama,kodeAkunKelompok,namaAkunKelompok, kodeAkunJenis,namaAkunJenis,kodeAkunObjek,namaAkunObjek,kodeAkunRincian,namaAkunRincian,nilaiRealisasi FROM {realisasi_sikd_2016} WHERE kodeAkunUtama=:akun and kodeSKPD=:kodeSKPD', array(':akun' => '6', ':kodeSKPD' => $datakegmaster->kodeSKPD));
			foreach ($resrek as $datarek) {
				
					
				$writer->startElement("kodeRekenings"); 
				
				$writer->writeElement('kodeAkunUtama', $datarek->kodeAkunUtama); 
				$writer->writeElement('namaAkunUtama', $datarek->namaAkunUtama); 
				
				$writer->writeElement('kodeAkunKelompok', $datarek->kodeAkunKelompok); 
				$writer->writeElement('namaAkunKelompok', $datarek->namaAkunKelompok); 
				
				$writer->writeElement('kodeAkunJenis', $datarek->kodeAkunJenis); 
				$writer->writeElement('namaAkunJenis', $datarek->namaAkunJenis); 
				
				$writer->writeElement('kodeAkunObjek', $datarek->kodeAkunObjek); 
				$writer->writeElement('namaAkunObjek', $datarek->namaAkunObjek); 
				
				$writer->writeElement('kodeAkunRincian', $datarek->kodeAkunRincian); 
				$writer->writeElement('namaAkunRincian', $datarek->kodeAkunRincian); 

				$writer->writeElement('kodeAkunSub', ''); 
				$writer->writeElement('namaAkunSub', '');

				$writer->writeElement('nilaiRealisasi', $datarek->nilaiRealisasi); 
				
				$writer->endElement();		//END REKENING
		
			}

		$writer->endElement(); 			//END KEGIATAN	
		
		if ($i==50) {
			file_put_contents($fname, $writer->flush(true), FILE_APPEND);
			$i = 0;
		} else
			$i++;
	}
	file_put_contents($fname, $writer->flush(true), FILE_APPEND);
	
	*/
	
$writer->endElement(); 			//ns2	

$writer->endDocument();   
$writer->flush(); 

file_put_contents($fname, $writer->flush(true), FILE_APPEND);
$zip = new ZipArchive();
$filename = 'files/xml/2016991265LRA0.zip';

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	drupal_set_message('failed');	
   
} else {
	$zip->addFile($fname, basename($fname));
	$zip->close();
	drupal_set_message('ok');
}
//drupal_goto('http://akt.simkedajepara.net/'.$filename);

}


function hitung_pajak($dokid) {
	
	drupal_set_message($dokid);
	
	$pajak = 0;
	$respajak = db_query('SELECT sum(jumlah) total FROM {dokumenpajak} WHERE dokid=:dokid', array(':dokid' => $dokid));
	foreach ($respajak as $data) {
		$pajak = $data->total;
	}	
	drupal_set_message($pajak);
	return $pajak;
}

function prepare_LRA_ByAgg($bulan) {

//RESET
db_delete('anggaran_sikd')
	->execute();

//PENDAPATAN 
$reskegmaster = db_query('select kodeuk,kodero,jumlah from anggperuk');
//$reskegmaster = db_query('select distinct kodeuk from jurnal where month(tanggal)<=:bulan and jurnalid in (select jurnalid from jurnalitem where left(kodero,1)=:empat)', array(':bulan' => $bulan, ':empat' => '4'));
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
				
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {

			db_insert('anggaran_sikd')
			->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiAnggaran'))
			->values(array(
					
				'periode' => $bulan, 
				'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
				'namaUrusanProgram' => $datakeg->urusan, 
				'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
				'namaUrusanPelaksana' => $datakeg->urusan, 
				'kodeSKPD' => $datakegmaster->kodeuk . '00', 
				'namaSKPD' => $datakeg->namauk, 
				'kodeProgram' => '000', 
				'namaProgram' => 'Non Program', 
				'kodeKegiatan' => '000000', 
				'namaKegiatan' => 'Non Kegiatan', 
				'kodeFungsi' => $datakeg->kodef, 
				'namaFungsi' => $datakeg->fungsi, 
				'kodeAkunUtama' => substr($datakegmaster->kodero, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datakegmaster->kodero, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datakegmaster->kodero, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datakegmaster->kodero, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'kodeAkunRincian' => substr($datakegmaster->kodero, -3), 
				'namaAkunRincian' => $datainforek->namaakunrincian, 
				'kodeAkunSub' => '', 
				'namaAkunSub' => '', 
				'nilaiAnggaran' => $datakegmaster->jumlah, 
				))
			->execute();							
		}	
		
	

	}		
}

//BELANJA
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0');
foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT kodero, jumlah FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('anggaran_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiAnggaran'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiAnggaran' => $datarek->jumlah, 
				))
				->execute();	
			}	
				
		}

	}	
}	

/*

//PENERIMAAN PEMBIAYAAN
$reskegmaster = db_query('select kodeuk,kodero,jumlah from anggperda where kodero like :penerimaan', array(':penerimaan' => '61%'));
//$reskegmaster = db_query('select distinct kodeuk from jurnal where month(tanggal)<=:bulan and jurnalid in (select jurnalid from jurnalitem where left(kodero,1)=:empat)', array(':bulan' => $bulan, ':empat' => '4'));
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
				
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {

			db_insert('anggaran_sikd')
			->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiAnggaran'))
			->values(array(
					
				'periode' => $bulan, 
				'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
				'namaUrusanProgram' => $datakeg->urusan, 
				'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
				'namaUrusanPelaksana' => $datakeg->urusan, 
				'kodeSKPD' => $datakegmaster->kodeuk . '00', 
				'namaSKPD' => $datakeg->namauk, 
				'kodeProgram' => '000', 
				'namaProgram' => 'Non Program', 
				'kodeKegiatan' => '000000', 
				'namaKegiatan' => 'Non Kegiatan', 
				'kodeFungsi' => $datakeg->kodef, 
				'namaFungsi' => $datakeg->fungsi, 
				'kodeAkunUtama' => substr($datakegmaster->kodero, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datakegmaster->kodero, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datakegmaster->kodero, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datakegmaster->kodero, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'kodeAkunRincian' => substr($datakegmaster->kodero, -3), 
				'namaAkunRincian' => $datainforek->namaakunrincian, 
				'kodeAkunSub' => '', 
				'namaAkunSub' => '', 
				'nilaiAnggaran' => $datakegmaster->jumlah, 
				))
			->execute();							
		}	
		
	

	}		
}

*/

}


function prepare_LRA_ByAgg_P() {

//RESET
db_delete('realisasi_sikd')->execute();
	
//PENDAPATAN 
$reskegmaster = db_query('select kodeuk,kodero,jumlah from anggperuk');
//$reskegmaster = db_query('select distinct kodeuk from jurnal where month(tanggal)<=:bulan and jurnalid in (select jurnalid from jurnalitem where left(kodero,1)=:empat)', array(':bulan' => $bulan, ':empat' => '4'));
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
				
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {

			db_insert('realisasi_sikd')
			->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
			->values(array(
					
				'periode' => $bulan, 
				'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
				'namaUrusanProgram' => $datakeg->urusan, 
				'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
				'namaUrusanPelaksana' => $datakeg->urusan, 
				'kodeSKPD' => $datakegmaster->kodeuk . '00', 
				'namaSKPD' => $datakeg->namauk, 
				'kodeProgram' => '000', 
				'namaProgram' => 'Non Program', 
				'kodeKegiatan' => '000000', 
				'namaKegiatan' => 'Non Kegiatan', 
				'kodeFungsi' => $datakeg->kodef, 
				'namaFungsi' => $datakeg->fungsi, 
				'kodeAkunUtama' => substr($datakegmaster->kodero, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datakegmaster->kodero, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datakegmaster->kodero, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datakegmaster->kodero, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'kodeAkunRincian' => substr($datakegmaster->kodero, -3), 
				'namaAkunRincian' => $datainforek->namaakunrincian, 
				'kodeAkunSub' => '', 
				'namaAkunSub' => '', 
				'nilaiRealisasi' => $datakegmaster->jumlah, 
				))
			->execute();							
		}	
		
	

	}		
}


}

function prepare_LRA_ByAgg_B1() {

$bulan = 0;
//BELANJA
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and kodeuk=:kodeuk', array(':kodeuk'=>'00'));
foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT kodero, jumlah FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('realisasi_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiRealisasi' => $datarek->jumlah, 
				))
				->execute();	
			}	
				
		}

	}	
}	


}

function prepare_LRA_ByAgg_B2() {
$bulan = 0;
//BELANJA
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=1 and isppkd=0 and kodeuk<>:ppkd', array(':ppkd'=>'00'));
foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT kodero, jumlah FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('realisasi_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiRealisasi' => $datarek->jumlah, 
				))
				->execute();	
			}	
				
		}

	}	
}	


}


function prepare_LRA_ByAgg_B3() {
$bulan = 0;
//BELANJA
//$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=2 and isppkd=0 and kodeuk<=:kodeuk', array(':kodeuk'=>'20'));

//$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=2 and isppkd=0 and kodeuk>=:kodeuk1 and kodeuk<=:kodeuk2', array(':kodeuk1'=>'21', ':kodeuk2'=>'25'));

//$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=2 and isppkd=0 and kodeuk>=:kodeuk1 and kodeuk<=:kodeuk2', array(':kodeuk1'=>'26', ':kodeuk2'=>'30'));

//$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=2 and isppkd=0 and kodeuk>=:kodeuk1 and kodeuk<=:kodeuk2', array(':kodeuk1'=>'26', ':kodeuk2'=>'30'));

$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=2 and isppkd=0 and kodeuk>=:kodeuk1 and kodeuk<=:kodeuk2', array(':kodeuk1'=>'31', ':kodeuk2'=>'35'));

foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT kodero, jumlah FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('realisasi_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiRealisasi' => $datarek->jumlah, 
				))
				->execute();	
			}	
				
		}

	}	
}	


}

function prepare_LRA_ByAgg_B4() {
$bulan = 0;
//BELANJA
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and jenis=2 and isppkd=0 and kodeuk>=:kodeuk', array(':kodeuk'=>'51'));
foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT kodero, jumlah FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('realisasi_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiRealisasi' => $datarek->jumlah, 
				))
				->execute();	
			}	
				
		}

	}	
}	


}

?>