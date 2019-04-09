<?php

function anggaranupdate_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('anggaranupdate_edit_main_form');
	return drupal_render($output_form);// . $output;
		
}

function anggaranupdate_edit_main_form($form, &$form_state) {
   

	$periode = arg(1);
	
	if ($periode=='') $periode = '2';
	
	//drupal_set_message($periode);
	
	$opt_periode['2'] = 'Revisi I';
	$opt_periode['3'] = 'Revisi II';
	$opt_periode['4'] = 'Revisi III';
	$opt_periode['5'] = 'Revisi IV';
	$opt_periode['6'] = 'Revisi V';

	$form['periode'] = array (
		'#type' => 'select',
		'#title' =>  t('Revisi'),
		'#options' => $opt_periode,
		'#default_value' => $periode,
	);
	$form['submitlra']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-repeat" aria-hidden="true"> Update</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		
	);

	return $form;
}

function anggaranupdate_edit_main_form_submit($form, &$form_state) {
	$periode = $form_state['values']['periode'];
	
	updateAnggaran($periode);
	
}


function updateAnggaran($periode) {
$res = db_query('delete from {kegiatanskpd}');
$res = db_query('delete from {anggperkeg}');
$res = db_query('delete from {kegiatandpa' . $periode . '}');

$sql = 'insert into {kegiatanskpd} (kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, inaktif, isgaji, isppkd, periode, anggaran) select kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, totalp as total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1p as tw1, tw2p  as tw2, tw3p  as tw3, tw4p as tw4, inaktif, isgaji, isppkd, periode, totalp as anggaran from {kegiatanperubahan} where bintang=0';
$res = db_query($sql);

$sql = 'insert into {anggperkeg} (kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum, anggaran) select kodero, kodekeg, uraian, jumlahp, jumlahsesudah, jumlahsebelum, jumlahp as anggaran from {anggperkegperubahan} where bintang=0';
$res = db_query($sql);

$sql = 'insert into {kegiatanskpd} (kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, inaktif, isgaji, isppkd, periode, anggaran) select kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, totalp as total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1p as tw1, tw2p  as tw2, tw3p  as tw3, tw4p as tw4, inaktif, isgaji, isppkd, periode, anggaran from {kegiatanperubahan} where bintang=1';
$res = db_query($sql);

$sql = 'insert into {anggperkeg} (kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum, anggaran) select kodero, kodekeg, uraian, jumlahp, jumlahsesudah, jumlahsebelum, anggaran from {anggperkegperubahan} where bintang=1';
$res = db_query($sql);


//NO DPA
$sql = "insert into kegiatandpa" . $periode . " (kodekeg, dpano, dpatgl) SELECT k.kodekeg, concat(d.btlno, '-P/BTL/', u.kodedinas, '.' , k.kodepro , '.' , right(k.kodekeg,3),  '/2017') as dpano, '29 September 2017' as dpatgl FROM kegiatanperubahan as k inner join dpanomor2 as d on k.kodeuk=d.kodeuk inner join unitkerja as u on k.kodeuk=u.kodeuk where k.periode=" . $periode . " and k.jenis=1";
$res = db_query($sql);

$sql = "insert into kegiatandpa" . $periode . " (kodekeg, dpano, dpatgl) SELECT k.kodekeg, concat(d.btlno, '-P/BL/', u.kodedinas, '.' , k.kodepro , '.' , right(k.kodekeg,3),  '/2017') as dpano, '29 September 2017' as dpatgl FROM kegiatanperubahan as k inner join dpanomor2 as d on k.kodeuk=d.kodeuk inner join unitkerja as u on k.kodeuk=u.kodeuk where k.periode=" . $periode . " and k.jenis=2";
$res = db_query($sql);

}

function updateAnggaran_lama($periode) {
$res = db_query('delete from {kegiatanskpd} where kodekeg in (select kodekeg from {kegiatanperubahan} where periode=' . $periode . ')');
$res = db_query('delete from {anggperkeg} where kodekeg in (select kodekeg from {kegiatanperubahan} where periode=' . $periode . ')');

$sql = 'insert into {kegiatanskpd} (kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, inaktif, isgaji, isppkd, periode, anggaran) select kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, totalp as total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1p as tw1, tw2p  as tw2, tw3p  as tw3, tw4p as tw4, inaktif, isgaji, isppkd, periode, anggaran from {kegiatanperubahan} where kodekeg in (select kodekeg from {kegiatanperubahan} where periode=' . $periode . ')';
$res = db_query($sql);

$sql = 'insert into {anggperkeg} (kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum, anggaran) select kodero, kodekeg, uraian, jumlahp, jumlahsesudah, jumlahsebelum, anggaran from {anggperkegperubahan} where kodekeg in (select kodekeg from {kegiatanperubahan} where periode=' . $periode . ')';
$res = db_query($sql);
}
?>