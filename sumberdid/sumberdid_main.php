<?php
function sumberdid_main($arg=NULL, $nama=NULL) {

	$kodeuk=arg(1);
	if ($kodeuk=='') $kodeuk = "ZZ";
	$opsi=arg(2);
	if ($opsi=='') $opsi = "view";
	
	
	$output = '';
	if ($opsi=='view') {
		$output = gen_view_did($kodeuk);
		$output_form = drupal_get_form('sumberdid_main_form');
		return drupal_render($output_form) . $output;
		
	} else if ($opsi=='excel') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Rekap DID.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$outputexcel = gen_view_did($kodeuk);
		echo $outputexcel;
		
	} else if ($opsi=='input') {
		$output_form = drupal_get_form('sumberdid_main_form');
		return drupal_render($output_form);
		
	}
	
	
}

function sumberdid_main_form($form, &$form_state) {

	$kodeuk=arg(1);
	if ($kodeuk=='') $kodeuk = "ZZ";
	$opsi=arg(2);
	if ($opsi=='') $opsi = "view";
	
	$options['ZZ'] = 'SELURUH SKPD';
	$results = db_query("SELECT kodeuk,namasingkat from unitkerja where kodeuk in (select kodeuk from kegiatanskpd where sumberdana1 ='LAIN-LAIN PENDAPATAN') order by namasingkat");
	foreach ($results as $data) {
		$options[$data->kodeuk]=$data->namasingkat;
	}
	$form['kodeuk']= array(
		'#type'=>'select',
		'#options'=>$options,
		'#default_value'=>$kodeuk,
	);
	
	$form['view']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['input']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-saved" aria-hidden="true"></span> Input',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['excel']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Excel',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	if ($opsi=='input') {
		$form['formdokumen']['tablerek']= array(
			'#prefix' => '<table class="table table-hover"><tr><th>No</th><th>Kegiatan</th><th>SKPD</th><th>Sumber Dana</th><th>Anggaran</th><th>Realisasi</th><th>%</th><th>Pilih</th></tr>',
			 '#suffix' => '</table>',
		);
		if ($kodeuk=='ZZ') {
			$results = db_query("SELECT k.kodekeg, k.kegiatan, k.sumberdana1, k.total, u.namasingkat from {kegiatanskpd} k inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.sumberdana1='LAIN-LAIN PENDAPATAN' ORDER BY k.sumberdana1,k.total");
		} else {
			$results = db_query("SELECT k.kodekeg, k.kegiatan, k.sumberdana1, k.total, u.namasingkat from {kegiatanskpd} k inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.kodeuk=:kodeuk and k.sumberdana1='LAIN-LAIN PENDAPATAN' ORDER BY k.sumberdana1,k.total", array(':kodeuk'=>$kodeuk));
		}
		
		$total_agg_did = 0;
		$total_rea_did = 0;
		
		foreach ($results as $data) {
			$res = db_query("SELECT count(kodekeg) as ada  from sumberdid where kodekeg=:kodekeg", array(':kodekeg'=>$data->kodekeg));
			$ada=0;
			foreach ($res as $dat) {
				$ada=$dat->ada;
			}
			if($ada>0){
				$ada=1;
			}
			$i++; 
			$form['formdokumen']['tablerek']['kodekeg' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodekeg,
			); 
			$form['formdokumen']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['kegiatan' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kegiatan,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['skpd' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->namasingkat,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['sumbedana' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->sumberdana1,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['total' . $i]= array(
					'#prefix' => '<td align="right">',
					'#markup' => apbd_fn($data->total),
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$rea = readrealisasi_kegiatan($data->kodekeg);
			$form['formdokumen']['tablerek']['realisasi' . $i]= array(
					'#prefix' => '<td align="right">',
					'#markup' => apbd_fn($rea),
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			
			$form['formdokumen']['tablerek']['persen' . $i]= array(
					'#prefix' => '<td align="right">',
					'#markup' => apbd_fn1(apbd_hitungpersen($data->total, $rea)),
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			
			if ($ada) {
				$total_agg_did += $data->total;
				$total_rea_did += $rea;			
			}
			$form['formdokumen']['tablerek']['pilih' . $i]= array(
				'#type'         => 'checkbox', 
				'#prefix' => '<td>',
				'#default_value'=>$ada,
				'#suffix' => '</td>',
			);	

		}
		
		//TOTAL
		$form['formdokumen']['tablerek']['kodekeg' . $i]= array(
				'#type' => 'value',
				'#value' => '000000',
		); 
		$form['formdokumen']['tablerek']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['kegiatan' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => 'TOTAL',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['skpd' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 	
		$form['formdokumen']['tablerek']['sumbedana' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => 'DID',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['total' . $i]= array(
				'#prefix' => '<td align="right">',
				'#markup' => apbd_fn($total_agg_did),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablerek']['realisasi' . $i]= array(
				'#prefix' => '<td align="right">',
				'#markup' => apbd_fn($total_rea_did),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		
		$form['formdokumen']['tablerek']['persen' . $i]= array(
				'#prefix' => '<td align="right">',
				'#markup' => apbd_fn1(apbd_hitungpersen($total_agg_did, $total_rea_did)),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		
		$form['formdokumen']['tablerek']['pilih' . $i]= array(
			'#type'     => 'markup', 
			'#markup'	=> '',
			'#suffix' 	=> '</tr>',
		);	
			
		$form['formdokumen']['jumlahrek']= array(
			'#type' => 'value',
			'#value' => $i,
		);
		$form['formdata']['submit']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> SIMPAN',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
	}
	return $form;
}


function sumberdid_main_form_validate($form, &$form_state) {
	
}
	
function sumberdid_main_form_submit($form, &$form_state) {
	$jumlahdok = $form_state['values']['jumlahrek'];
	$kodeuk = $form_state['values']['kodeuk'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['input']) {
		
		drupal_goto('sumberdid/' . $kodeuk . '/input');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['view']) {
		
		drupal_goto('sumberdid/' . $kodeuk . '/view');
		
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['excel']) {
		
		drupal_goto('sumberdid/' . $kodeuk . '/excel');

	}elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		for($n=1;$n<=$jumlahdok;$n++){

			$kodekeg=$form_state['values']['kodekeg' . $n];
			db_delete('sumberdid')
			->condition('kodekeg',$kodekeg,'=')
			->execute();
				
			if($form_state['values']['pilih' . $n]!=0){
				db_insert('sumberdid')
				->fields(array(
						'kodekeg' => $kodekeg,
						
						))
				->execute();
			}
			
		}
		
		
	}
		

	//drupal_goto('spmuparsip');
	//drupal_goto();

}

function readrealisasi_kegiatan($kodekeg) {
	$rea = 0;
	$results = db_query('SELECT SUM(jumlah) as rea FROM dokumen WHERE kodekeg=:kodekeg and sp2dok=1', array(':kodekeg'=>$kodekeg));	
	foreach ($results as $data) {
		$rea = $data->rea;
	}
	return $rea;
}

function gen_view_did($kodeuk) {

$agg_total = 0;
$rea_total = 0;

//TABEL
$header = array (
	array('data' => 'No','width' => '10px', 'valign'=>'top'),
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Kegiatan', 'valign'=>'top'),
	array('data' => 'Sumber Dana', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

//AKUN
if ($kodeuk=='ZZ') {
	$results = db_query("SELECT k.kodekeg, k.kegiatan, k.sumberdana1, k.total, u.namasingkat from {kegiatanskpd} k inner join {unitkerja} u on k.kodeuk=u.kodeuk inner join {sumberdid} s on k.kodekeg=s.kodekeg ORDER BY k.sumberdana1,k.total");
} else {
	$results = db_query("SELECT k.kodekeg, k.kegiatan, k.sumberdana1, k.total, u.namasingkat from {kegiatanskpd} k inner join {unitkerja} u on k.kodeuk=u.kodeuk inner join {sumberdid} s on k.kodekeg=s.kodekeg where k.kodeuk=:kodeuk ORDER BY k.sumberdana1,k.total", array(':kodeuk'=>$kodeuk));
}

$n = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$n++;
	$realisasi = readrealisasi_kegiatan($datas->kodekeg);
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->namasingkat, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->kegiatan, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->sumberdana1, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->total), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->total, $realisasi)), 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_total += $datas->total;
	$rea_total += $realisasi;

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong></strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>DID</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($agg_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_total, $rea_total)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>
