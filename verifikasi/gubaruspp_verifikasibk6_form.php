<?php

function gubaruspp_verifikasibk6_keg_form() {
	//$dokid = arg(2);
	//$kodekeg = arg(3);

	$tglawal = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$tglakhir = $tglawal; 
	
	
	$res = db_query('select tglawal,tglakhir from {dokumen} where dokid=:dokid', array(':dokid'=>$dokid));
	foreach ($res as $data) {
		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;		
	}
	$form['dokid'] = array (
		'#type' => 'value',
		'#dokid'=> $dokid,
	);	
	
	
	//KEGIATAN
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		//REKENING
		$form['kegiatan'] = array (
			'#type' => 'item',
			'#markup'=> '<h3>' . $datakeg->kegiatan . '</h3>',
		);	
		
		
		
		$form['bk6'] = array (
			'#type' => 'item',
			'#markup' => 'x',	//verifikasi_bk6($kodekeg, $tglawal, $tglakhir),        
		);
			
	

	}		
	
	
	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	
	return $form;	
}

function gubaruspp_verifikasibk6_keg_form_submit($form, &$form_state) {
	goback();
}

function goback() {
	$dokid = $form_state['values']['dokid'];
	drupal_goto('gubaruspp/edit/' . $dokid);
}

function gubaruspp_verifikasibk6_rek_form() {
	$dokid = arg(2);
	$kodekeg = arg(3);
	$kodero = arg(4);

	$tglawal = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$tglakhir = $tglawal; 
	
	$res = db_query('select tglawal,tglakhir from {dokumen} where dokid=:dokid', array(':dokid'=>$dokid));
	foreach ($res as $data) {
		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;		
	}
	$form['dokid'] = array (
		'#type' => 'value',
		'#dokid'=> $dokid,
	);	
		
	//KEGIATAN
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {

		$form['kegiatan'] = array (
			'#type' => 'item',
			'#markup'=> $datakeg->kegiatan,
		);	
		
		
		
		$form['bk6'] = array (
			'#type' => 'item',
			'#markup' => verifikasi_bk6_rek($kodekeg, $kodero, $tglawal, $tglakhir),        
		);
			
	

	}		

	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	
	return $form;	
}

function gubaruspp_verifikasibk6_rek_form_submit($form, &$form_state) {
	goback();
}


function verifikasi_bk6($kodekeg, $tglawal, $tglakhir){
	
	$kodeuk  = _getkodeuk_from_kodekeg($kodekeg);
	
	db_set_active('bendahara');
	
	//Rekening
	$rows=null;
	
	$query = db_select('anggperkeg', 'a');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('a', array('anggaran'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	//dpq($query);	
		
	# execute the query	
	$res_rek = $query->execute();
	$i = 0;
	foreach ($res_rek as $data_rek) {
		if (verifikasi_bk6_is_ada_transaksi($kodekeg, $data_rek->kodero, $tglawal, $tglakhir)) {
			
			$i++;
			$rows[]=array(
				array('data' => $i . '. ' . $data_rek->kodero . ' - ' . $data_rek->uraian, 'colspan'=>5,'width' => '430px','align'=>'left','style'=>'border:none;font-size:80%;font-weight: bold;'),
				array('data' => 'Anggaran : ', 'width' => '70px','align'=>'right','style'=>'border:none;font-size:80%;font-weight: bold;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px','align'=>'right','style'=>'border:none;font-size:80%;font-weight: bold;'),			
			);
			
			$rows[]=array(
				array('data' => 'No.', 'width' => '10px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				array('data' => 'Tanggal', 'width' => '50px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				array('data' => 'Uraian', 'width' => '200px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				array('data' => 'Keterangan', 'width' => '100px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				
				array('data' => 'JUMLAH SPJ', 'width' => '210px','colspan'=>3,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'LS', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
				array('data' => 'UP/GU', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
				array('data' => 'TU', 'width' => '70px','align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
			);
			
			$query = db_select('bendahara' . $kodeuk, 'b');
			$query->innerJoin('bendaharaitem' . $kodeuk, 'bi', 'b.bendid=bi.bendid');
			$query->fields('b', array('tanggal', 'keperluan', 'jenis', 'jenispanjar', 'penerimanama'));
			$query->fields('bi', array('jumlah', 'keterangan'));
			$query->condition('b.kodekeg', $kodekeg, '=');
			$query->condition('bi.kodero', $data_rek->kodero, '=');
			$query->condition('bi.jumlah', 0, '<>');

			$query->condition('b.tanggal', $tglawal, '>=');
			$query->condition('b.tanggal', $tglakhir, '<=');
			
			$or = db_or();
			$or->condition('b.jenis', 'gaji', '=');
			$or->condition('b.jenis', 'ls', '=');
			$or->condition('b.jenis', 'tu-spj', '=');
			$or->condition('b.jenis', 'gu-spj', '=');
			$or->condition('b.jenis', 'ret-spj', '=');
			$or->condition('b.jenis', 'pindahbuku', '=');
			$query->condition($or);
			
			$query->orderBy('b.tanggal', 'ASC');
			
			$total_ls = 0; $total_gu = 0; $total_tu = 0;
			
			# execute the query	
			$no = 0;
			$res_spj = $query->execute();
			foreach ($res_spj as $data_spj) {
				$no++;
				
				$ls = 0; $gu = 0; $tu = 0;
				if ($data_spj->jenis == 'gu-spj')
					$gu = $data_spj->jumlah;
				
				else if (($data_spj->jenis == 'ls') or ($data_spj->jenis == 'gaji'))
					$ls += $data_spj->jumlah;
				
				else if ($data_spj->jenis == 'ret-spj') {
					
					if ($data_spj->jenispanjar == 'gu')
						$gu = -$data_spj->jumlah;
					else if ($data_spj->jenispanjar == 'ls')
						$ls = -$data_spj->jumlah;
					else			
						$tu = -$data_spj->jumlah;
				
				} else
					$tu = $data_spj->jumlah;
				 
				$total_ls += $ls; $total_gu += $gu; $total_tu += $tu;
				
				$ketdetil = $data_spj->penerimanama;
				if ($data_spj->keterangan<>'') $ketdetil .= ' (' . $data_spj->keterangan . ')';
				$rows[] = array(
					array('data' => $no, 'width' => '10px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
					array('data' => apbd_fd($data_spj->tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => $data_spj->keperluan, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => $ketdetil, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					
					array('data' => apbd_fn($ls), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => apbd_fn($gu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => apbd_fn($tu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				);		

			}
			if ($no==0) {
				$rows[] = array(
					array('data' => '', 'width' => '10px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => 'Tidak ada', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => 'Tidak ada', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				);					
			}
			 
			//SEBELUMNYA
			$ls_lalu = 0; $gu_lalu = 0; $tu_lalu = 0; 
			verifikasi_bk6_read_sebelumnya($kodekeg, $data_rek->kodero, $tglawal, $ls_lalu, $gu_lalu, $tu_lalu);
			
			$rows[]=array(
				array('data' => 'Jumlah periode ini', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;font-size:80%;'),
				
				array('data' => apbd_fn($total_ls), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_gu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_tu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah sampai dengan periode sebelumnya', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				
				array('data' => apbd_fn($ls_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($gu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah sampai dengan periode ini', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				
				array('data' => apbd_fn($total_ls + $ls_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_gu + $gu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_tu + $tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah Total Pengeluaran', 'colspan'=>6,'width' => '540px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-weight:bold;border-bottom:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_ls+$total_gu+$total_tu + $ls_lalu + $gu_lalu + $tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-right:1px solid black;font-size:80%;'),
			);
			
			//space
			$rows[]=array(
				array('data' => '', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			);		
			
		
		}	//ada transaksi
	}	//end rekening		
	
	db_set_active();
				//render
	//$output = createT($header, $rows);

	$table_element = array(
		'#theme' => 'table',
		'#header' => null,
		'#rows' => $rows,
		'#empty' =>t('Your table is empty'),
	);
	$output = drupal_render($table_element);	

	return $output;
}


function verifikasi_bk6_rek($kodekeg, $kodero, $tglawal, $tglakhir){
	
	$kodeuk  = _getkodeuk_from_kodekeg($kodekeg);
	
	db_set_active('bendahara');
	
	//Rekening
	$rows=null;
	
	$query = db_select('anggperkeg', 'a');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('a', array('anggaran'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->condition('a.kodero', $kodero, '=');
	//dpq($query);	
		
	# execute the query	
	$res_rek = $query->execute();
	foreach ($res_rek as $data_rek) {
		if (verifikasi_bk6_is_ada_transaksi($kodekeg, $data_rek->kodero, $tglawal, $tglakhir)) {
			
			
			$rows[]=array(
				array('data' => $data_rek->kodero . ' - ' . $data_rek->uraian, 'colspan'=>5,'width' => '430px','align'=>'left','style'=>'border:none;font-size:80%;font-weight: bold'),
				array('data' => 'Anggaran : ', 'width' => '70px','align'=>'right','style'=>'border:none;font-size:80%;font-weight: bold'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px','align'=>'right','style'=>'border:none;font-size:80%;font-weight: bold'),			
			);
			
			$rows[]=array(
				array('data' => 'No.', 'width' => '10px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				array('data' => 'Tanggal', 'width' => '50px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				array('data' => 'Uraian', 'width' => '200px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				array('data' => 'Keterangan', 'width' => '100px','rowspan'=>2,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				
				array('data' => 'JUMLAH SPJ', 'width' => '210px','colspan'=>3,'align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'LS', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
				array('data' => 'UP/GU', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:80%;'),
				array('data' => 'TU', 'width' => '70px','align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
			);
			
			$query = db_select('bendahara' . $kodeuk, 'b');
			$query->innerJoin('bendaharaitem' . $kodeuk, 'bi', 'b.bendid=bi.bendid');
			$query->fields('b', array('tanggal', 'keperluan', 'jenis', 'jenispanjar', 'penerimanama'));
			$query->fields('bi', array('jumlah', 'keterangan'));
			$query->condition('b.kodekeg', $kodekeg, '=');
			$query->condition('bi.kodero', $data_rek->kodero, '=');
			$query->condition('bi.jumlah', 0, '<>');

			$query->condition('b.tanggal', $tglawal, '>=');
			$query->condition('b.tanggal', $tglakhir, '<=');
			
			$or = db_or();
			$or->condition('b.jenis', 'gaji', '=');
			$or->condition('b.jenis', 'ls', '=');
			$or->condition('b.jenis', 'tu-spj', '=');
			$or->condition('b.jenis', 'gu-spj', '=');
			$or->condition('b.jenis', 'ret-spj', '=');
			$or->condition('b.jenis', 'pindahbuku', '=');
			$query->condition($or);
			
			$query->orderBy('b.tanggal', 'ASC');
			
			$total_ls = 0; $total_gu = 0; $total_tu = 0;
			
			# execute the query	
			$no = 0;
			$res_spj = $query->execute();
			foreach ($res_spj as $data_spj) {
				$no++;
				
				$ls = 0; $gu = 0; $tu = 0;
				if ($data_spj->jenis == 'gu-spj')
					$gu = $data_spj->jumlah;
				
				else if (($data_spj->jenis == 'ls') or ($data_spj->jenis == 'gaji'))
					$ls += $data_spj->jumlah;
				
				else if ($data_spj->jenis == 'ret-spj') {
					
					if ($data_spj->jenispanjar == 'gu')
						$gu = -$data_spj->jumlah;
					else if ($data_spj->jenispanjar == 'ls')
						$ls = -$data_spj->jumlah;
					else			
						$tu = -$data_spj->jumlah;
				
				} else
					$tu = $data_spj->jumlah;
				 
				$total_ls += $ls; $total_gu += $gu; $total_tu += $tu;
				
				$ketdetil = $data_spj->penerimanama;
				if ($data_spj->keterangan<>'') $ketdetil .= ' (' . $data_spj->keterangan . ')';
				$rows[] = array(
					array('data' => $no, 'width' => '10px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
					array('data' => apbd_fd($data_spj->tanggal), 'width' => '50px','align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => $data_spj->keperluan, 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => $ketdetil, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					
					array('data' => apbd_fn($ls), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => apbd_fn($gu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => apbd_fn($tu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				);		

			}
			if ($no==0) {
				$rows[] = array(
					array('data' => '', 'width' => '10px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => 'Tidak ada', 'width' => '200px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => 'Tidak ada', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;font-size:80%;'),
					
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
					array('data' => '', 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				);					
			}
			 
			//SEBELUMNYA
			$ls_lalu = 0; $gu_lalu = 0; $tu_lalu = 0; 
			verifikasi_bk6_read_sebelumnya($kodekeg, $data_rek->kodero, $tglawal, $ls_lalu, $gu_lalu, $tu_lalu);
			
			$rows[]=array(
				array('data' => 'Jumlah periode ini', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;font-size:80%;'),
				
				array('data' => apbd_fn($total_ls), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_gu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_tu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah sampai dengan periode sebelumnya', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				
				array('data' => apbd_fn($ls_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($gu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah sampai dengan periode ini', 'colspan'=>4,'width' => '400px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:80%;'),
				
				array('data' => apbd_fn($total_ls + $ls_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_gu + $gu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_tu + $tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-right:1px solid black;font-size:80%;'),
			);
			$rows[]=array(
				array('data' => 'Jumlah Total Pengeluaran', 'colspan'=>6,'width' => '540px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-weight:bold;border-bottom:1px solid black;border-top:1px solid black;font-size:80%;'),
				array('data' => apbd_fn($total_ls+$total_gu+$total_tu + $ls_lalu + $gu_lalu + $tu_lalu), 'width' => '70px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-right:1px solid black;font-size:80%;'),
			);
			
			//space
			$rows[]=array(
				array('data' => '', 'align'=>'center','style'=>'border:none;font-size:80%;'),
			);		
			
		
		}	//ada transaksi
	}	//end rekening		
	
	db_set_active();
				//render
	//$output = createT($header, $rows);

	$table_element = array(
		'#theme' => 'table',
		'#header' => null,
		'#rows' => $rows,
		'#empty' =>t('Your table is empty'),
	);
	$output = drupal_render($table_element);	

	return $output;
}

