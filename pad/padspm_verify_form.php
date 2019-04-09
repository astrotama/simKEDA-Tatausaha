<?php

function padspm_verify_form($form, &$form_state) {
    //drupal_add_js("(function blink() {   $('.blink_me').fadeOut(500).fadeIn(500, blink); })();", 'inline');
	//drupal_add_js('files/js/common.js');
    
    $dokid = arg(2);
	
	//FORM NAVIGATION	//
	//$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
    if (isset($dokid)) {

		$query = db_select('dokumen', 'd');
		$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'keperluan', 'jumlah', 'potongan', 'netto'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');
		//$query->condition('d.spmok', 1, '=');
		//$query->condition('d.sp2dok', 0, '=');
		
		//dpq($query);	
		
		$ada = false;		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$dokid = $data->dokid;
			$spmno = $data->spmno;
			$spmtgl = apbd_fd_long($data->spmtgl); 
			$keperluan = $data->keperluan . ', sebesar ' . apbd_fn($data->jumlah); 
			//$jumlah = apbd_fn($data->jumlah);
			//$potongan = apbd_fn($data->potongan);
			//$netto = apbd_fn($data->netto);
			$ada = true;		
			
			//drupal_set_message('x1');
		}	
		
	 
        if ($ada) {
            
			
			$form['dokid'] = array(
				'#type' => 'value', 
				'#value' => $dokid
			);
			$form['uraian'] = array (
				//'#type' => 'markup',
				'#markup' => '<p style="color:red"><img src="/files/triangle-spin.gif" align="left" style="width:48px;height:48px;">PERHATIAN : SPM yang sudah diverifikasi tidak dapat diedit lagi. Jadi harus dipastikan bahwa dokumen SPM sudah benar dan dilengkapi dengan semua dokumen pendukungnya.</p>' .
							 '<p style="color:green">Anda bisa me-review SPM dalam tampilan dibawah ini. Dan setelah semuanya benar, verifikasi bisa dilakukan dengan mengklik tombol <strong>VERIFIKASI</strong> dibagian bawah layar.</p>',
				//'#markup' => '<span class="blink_me">This Will Blink</span>',
			);
			$form['keterangan'] = array (
				//'#type' => 'markup',
				'#markup' => printspm($dokid),
			);
  
			$form['keterangan1'] = array (
				//'#type' => 'markup',
				'#markup' => '<p>Klik tombol <strong>Verifikasi</strong> untuk memverfikasi SPM, klik tombol <strong>Batal</strong> untuk membatalkan verifikasi.</p>',
				//'#markup' => '<span class="blink_me">This Will Blink</span>',
			);
			$form['submit']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
				'#attributes' => array('class' => array('btn btn-danger btn-sm')),
				//'#disabled' => TRUE,
				'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Batal</a>",
				
			);
						
 
        } else {
			$form['submit']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
				'#attributes' => array('class' => array('btn btn-success btn-sm')),
				'#disabled' => TRUE,
				'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Batal</a>",
				
			);
			
		}
    }
	
	return $form;
}

function padspm_verify_form_validate($form, &$form_state) {
}

function padspm_verify_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];

	$query = db_update('dokumen')
			->fields( 
			array(
				'spmok' => '1',
			)
		);
	$query->condition('dokid', $dokid, '=');
	$num = $query->execute();
		
	if ($num) {
		
		//drupal_set_message($dokid);
		drupal_set_message('Verifikasi SPM berhasil dilakukan');

		$referer = $_SESSION["padspmlastpage"];
		drupal_goto($referer);
	}
}

function printspm($dokid) {

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'spdno'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);

		$skpd = $data->namauk;
		$penerimanama = $data->penerimanama;
		$rekening = $data->penerimabanknama . ' No. Rek . ' . $data->penerimabankrekening;
		$npwp = $data->penerimanpwp;

		$spdno = '......................';
		$spdtgl = '......................';
		
		$namauk = $data->namauk;
		$pimpinannama = $data->pimpinannama;
		$pimpinanjabatan = $data->pimpinanjabatan;
		$pimpinannip = $data->pimpinannip;
		
		$keperluan = $data->keperluan;
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		
		$potongan = apbd_fn($data->potongan);
		$netto = apbd_fn($data->netto);
		$terbilangnetto = apbd_terbilang($data->netto);
		
		$nomorkeg = $data->kodedinas . '.000.000';	

		$spdkode = $data->spdno;	

	}	
	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '625px', 'colspan'=>'8', 'align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERINTAH MEMBAYAR (SPM)', 'width' => '625px','colspan'=>'8', 'align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '625px','colspan'=>'8', 'align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'Nomor SPM : ' . $spmno, 'width' => '625px','colspan'=>'8', 'align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '(Diisi oleh PPK-SKPD)', 'width' => '625px','colspan'=>'8', 'align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'KUASA BENDAHARA UMUM DAERAH', 'width' => '625px','colspan'=>'8', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'KABUPATEN JEPARA', 'width' => '625px','colspan'=>'8', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Supaya menerbitkan SP2D kepada :', 'width' => '625px','colspan'=>'8', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '150','colspan'=>'3', 'align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $skpd, 'width' => '465px','colspan'=>'4', 'align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Penerima', 'width' => '150', 'colspan'=>'3','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $penerimanama, 'width' => '465px','colspan'=>'4', 'align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Nomor Rekening Bank', 'width' => '150','colspan'=>'3','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $rekening, 'width' => '465px','colspan'=>'4', 'align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '150', 'colspan'=>'3','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $npwp, 'width' => '465px','colspan'=>'4', 'align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Dasar Pengeluaran / No. SPD', 'width' => '150', 'colspan'=>'3','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $spdno, 'width' => '465px','colspan'=>'4', 'align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => 'Tgl. SPD', 'width' => '140px','colspan'=>'2','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $spdtgl, 'width' => '465px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan','colspan'=>'3', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $keperluan, 'width' => '465px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jenis Belanja', 'width' => '150','colspan'=>'3','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => 'PENGELUARAN RESTITUSI PAD', 'width' => '465px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	
	
	//REKENING
	$rows[]=array(
		array('data' => 'PEMBEBANAN PADA REKENING', 'width' => '625px','colspan'=>'8','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Kode Rekening', 'width' => '160px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Uraian', 'width' => '340px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
		
	);

	# get the desired fields from the database
	$query = db_select('dokumenrekening', 'di');
	$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
	
	$query->fields('di', array('jumlah'));
	$query->fields('ro', array('kodero', 'uraian'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('di.dokid', $dokid, '=');
	$query->condition('di.jumlah', 0, '>');
	$query->orderBy('ro.kodero', 'ASC');

	$results = $query->execute();
	$n = 0;
	$rek_pph = 0;
	$rek_pembulatan = 0;
	$rek_askes = 0;
	$rek_jkk = 0;
	$rek_jkm = 0;

	foreach ($results as $data) {
		$n++;	
		$rows[]=array(
			array('data' => $n, 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '160px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '340px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		
	}
	
 
	$rows[]=array(
		array('data' => 'Jumlah SPP yang Diminta', 'width' => '175px','colspan'=>'3','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border-top:1px solid black;'),
		array('data' => $jumlah, 'width' => '440px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Terbilang', 'width' => '175px','colspan'=>'3','align'=>'right','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>''),
		array('data' => $terbilang, 'width' => '440px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;'),
		
		
	);
	$rows[]=array(
		array('data' => 'Nomor & Tanggal SPP', 'width' => '175px','colspan'=>'3','align'=>'right','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>''),
		array('data' => $sppno . ', tanggal ' . $spptgl, 'width' => '440px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;'),
		
		
	);
	
	
	//POTONGAN
	$rows[]=array(
		array('data' => 'POTONGAN-POTONGAN', 'width' => '625px','colspan'=>'8','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Uraian', 'width' => '280px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Keterangan', 'width' => '220px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
	);

	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tidak ada potongan', 'width' => '280px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '220px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;'),
	);		
	
	$rows[]=array(
			array('data' => 'Jumlah Potongan', 'width' => '305px','colspan'=>'4','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
			array('data' => $potongan, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '220px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'PAJAK (TIDAK MENGURANGI JUMLAH SPM)', 'width' => '625px','colspan'=>'8','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Uraian', 'width' => '280px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Keterangan', 'width' => '220px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
	);

	$rows[]=array(
		array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tidak ada pajak', 'width' => '280px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '220px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;'),
	);		
	$rows[]=array(
			array('data' => 'Jumlah Pajak', 'width' => '305px','colspan'=>'4','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
			array('data' => apbd_fn($totalpajak), 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '220px','colspan'=>'3','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);

	$rows[]=array(
		array('data' => 'JUMLAH SPM', 'width' => '525px','colspan'=>'7','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => $netto, 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);
	
	$rows[]=array(
			array('data' => 'Uang Sejumlah', 'width' => '100px','colspan'=>'3','align'=>'right','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => ':', 'width' => '15px','align'=>'center','style'=>'border-bottom:1px solid black;'),
			array('data' => $terbilangnetto, 'width' => '510px','colspan'=>'4','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		);
		
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, ' . $spmtgl ,'width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan,'width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;'),
					
			); 
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinannama,'width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '','width' => '325px', 'colspan'=>'4','align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => 'NIP. ' . $pimpinannip,'width' => '300px', 'colspan'=>'4','align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);
	$rows[]=array(
		array('data' => 'SPM INI SAH APABILA DITANDATANGANI DAN DISTEMPEL OLEH ' . $pimpinanjabatan, 'width' => '625px','colspan'=>'8','align'=>'center','style'=>'border:1px solid black;'),
	);		
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}


?>