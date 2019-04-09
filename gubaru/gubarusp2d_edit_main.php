	<?php
function gubarusp2d_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$dokid = arg(2);	
	if (arg(3)=='pdf0') {			

		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
	
		$output = printsp2d_digital($dokid);
		//return $output;
		
		$fname = str_replace('/', '_', 'SP2D_' . $dokid . '.PDF');
		apbd_ExportSP2D_Lengkap($output, $url, $fname);		
		drupal_goto('files/sp2d/' . $fname);
		
	} else if (arg(3)=='pdf1') {			
		
		//drupal_set_message(arg(3));
		
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
	
		$output = printsp2d_digital($dokid);
		
		$fname = str_replace('/', '_', 'SP2D_' . $dokid . '.PDF');
		apbd_ExportSP2D_LengkapView($output, $url, $fname);				
		
	} else if (arg(3)=='soap') {
		$output = soap_sp2d_add($dokid);
		return $output;
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('gubarusp2d_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}
function kegiatan($dokid, $kodekeg, $tanggalsql){
	//PENGEMBALIAN
	$header=array();
	
	$nokeg = 0;
	$rows[] =array(
		array('data' => '<b>NO</b>', 'width' => '5px','align'=>'left','style'=>''),
		array('data' => '<b>KODE</b>', 'width' => '20px','align'=>'left','style'=>''),
		array('data' => '<b>URAIAN</b>', 'width' => '300px','align'=>'left','style'=>''),
		array('data' => '<b>ANGGARAN</b>', 'width' => '70px', 'align'=>'left','style'=>''),
		array('data' => '<b>CAIR</b>', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '<b>BATAS</b>', 'width' => '60px','align'=>'left','style'=>''),
		array('data' => '<b>JUMLAH</b>', 'width' => '60px', 'align'=>'left','style'=>''),
		array('data' => '<b>SALDO</b>', 'width' => '20px', 'align'=>'left','style'=>''),
	);
	$totalkeg = 0; $totalagg = 0; $totalcair = 0; 
	$i = 0;
	$results = db_query('select ro.kodero, ro.uraian, dr.jumlah, a.anggaran from dokumenrekening dr inner join rincianobyek ro on dr.kodero=ro.kodero inner join anggperkeg a on dr.kodekeg=a.kodekeg and dr.kodero=a.kodero where dr.dokid=:dokid and dr.kodekeg=:kodekeg order by dr.kodero', array(':dokid'=>$dokid, ':kodekeg'=>$kodekeg));
		
	foreach ($results as $data) {
		$i++; 
		
		
		$cair = 0;
		$cair = apbd_readrealisasikegiatan_rekening($dokid, $kodekeg, $data->kodero, $tanggalsql);
		$batas = $data->anggaran - $cair;

		$lewatbatas = ($batas<0);
		
		$jumlah = $data->jumlah;
		$totalkeg += $data->jumlah;
		$totalagg += $data->anggaran;
		$totalcair += $cair;
		
		if ($batas>=$jumlah) {
			$strkode = $kodero;
			$struraian = $uraian;
			$stranggaran = '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>';
			$strcair = '<p class="text-right">' . apbd_fn($cair) . '</p>';
			
			$strbatas =  '<p class="text-right">' . apbd_fn($batas) . '</p>';
			$strjumlah = '<p class="text-right">' . apbd_fn($jumlah) . '</p>';
			$strsaldo = '<p class="text-right">' . apbd_fn($batas -  $jumlah) . '</p>';
			
		} else {
			$strkode = '<p style="color:red">' . $kodero . '</p>';
			$struraian = '<p style="color:red">' . $uraian . '</p>';
			$stranggaran = '<p class="text-right" style="color:red">' . apbd_fn($data->anggaran) . '</p>';
			$strcair = '<p class="text-right" style="color:red">' . apbd_fn($cair) . '</p>';

			$strbatas =  '<p class="text-right" style="color:red">' . apbd_fn($batas) . '</p>';
			$strjumlah = '<p class="text-right" style="color:red">' . apbd_fn($jumlah) . '</p>';
			$strsaldo = '<p class="text-right" style="color:red">' . apbd_fn($batas - $jumlah) . '</p>';
		}	

		$uraian =  l($data->uraian, 'laporan/realisasikegsp2d/filter/' . $kodekeg . '/' . $data->kodero . '/' . $tanggalsql, array('attributes' => array('class' => null)));
		
		$rows[]=array(
			array('data' => $i , 'width' => '5px','align'=>'left','style'=>''),
			array('data' => $data->kodero , 'width' => '20px','align'=>'left','style'=>''),
			array('data' => $uraian, 'width' => '300px','align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>', 'width' => '70px', 'align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($cair) . '</p>', 'width' => '20px', 'align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($batas) . '</p>', 'width' => '60px','align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($jumlah) . '</p>', 'width' => '60px', 'align'=>'left','style'=>''),
			array('data' => '<p class="text-right">' . apbd_fn($batas - $jumlah) . '</p>', 'width' => '20px', 'align'=>'left','style'=>''),
		);
	}
	$rows[]=array(
		array('data' => '' , 'width' => '5px','align'=>'left','style'=>''),
		array('data' => '' , 'width' => '20px','align'=>'left','style'=>''),
		array('data' => '<strong>TOTAL</strong>', 'width' => '300px','align'=>'left','style'=>''),
		array('data' => '<p class="text-right">' . apbd_fn($totalagg) . '</p>', 'width' => '70px', 'align'=>'left','style'=>''),
		array('data' => '<p class="text-right">' . apbd_fn($totalcair) . '</p>', 'width' => '20px', 'align'=>'left','style'=>''),
		array('data' => '<p class="text-right">' . apbd_fn($totalagg - $totalcair) . '</p>', 'width' => '60px','align'=>'left','style'=>''),
		array('data' => '<p class="text-right">' . apbd_fn($totalkeg) . '</p>', 'width' => '60px', 'align'=>'left','style'=>''),
		array('data' => '<p class="text-right">' . apbd_fn($totalagg - $totalcair - $totalkeg) . '</p>', 'width' => '20px', 'align'=>'left','style'=>''),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}
function gubarusp2d_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["sp2dgulastpage"] = $referer;
	else
		$referer = $_SESSION["sp2dgulastpage"];
	
	$kodekeg = '';
	$sp2dno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('m');
	$sp2dtgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	
	$tglawal = $sp2dtgl; $tglakhir = $sp2dtgl; 
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Ganti Uang ' . apbd_tahun();
	$jeniskegiatan = '1';
	$penerimanama = '';
	$penerimanip = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
	//$pptknama = '';
	//$pptknip = '';
	$jumlah = 0;
	$pajak = 0;
	$potongan = 0;
	$netto = 0;
	
	$dokid = arg(2);
	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid', 'kodeuk',  'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'kodekeg', 'bulan', 'keperluan','penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp','kodebank',  'jumlah', 'sppok', 'spmok', 'sp2dok', 'spdno', 'tglawal', 'tglakhir', 'spjlink'));
	
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SP2D GU ' . $data->keperluan ;
		
		$sppok = $data->sppok;
		$spmok = $data->spmok;
		$sp2dok = $data->sp2dok;
		
		$dokid = $data->dokid;

		$sp2dno = $data->sp2dno;
		if ($sp2dno != '') $nosudah = true;

		if (isSuperuser())
			if (is_null($data->sp2dtgl)){
				$sp2dtgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
				$tanggalsql = apbd_tahun() . '-' . date('m') . '-' . date('d');
			} else {
				$sp2dtgl = strtotime($data->sp2dtgl);	
				$tanggalsql = $data->sp2dtgl;	
			}
			
		else 
			if ($sp2dno == '') {
				$sp2dno = 'Belum Terbit';
				$sp2dtgl = $sp2dno;		
				$tanggalsql = apbd_tahun() . '-' . date('m') . '-' . date('d');
				
			} else {
				$sp2dtgl = apbd_fd_long($data->sp2dtgl);		
				$tanggalsql = $data->sp2dtgl;
			}
			
		$sp2dtgl = dateapi_convert_timestamp_to_datetime($data->sp2dtgl);
		
		$sppno = $data->sppno . ', ' . apbd_fd_long($data->spptgl);
		$spmno = $data->spmno . ', ' . apbd_fd_long($data->spmtgl);
		
		$bulan = $data->bulan;
		
		$tglawal = $data->tglawal;
		$tglakhir = $data->tglakhir;
		$periode =  apbd_fd_long($data->tglawal) . ' s/d. ' . apbd_fd_long($data->tglakhir);
		
		$tanggalsql = $data->spptgl;
		
		$kodeuk = $data->kodeuk;

		$keperluan = $data->keperluan;
		
		$penerimanama = $data->penerimanama;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;
		$kodebank = $data->kodebank;
		
		$jumlah = $data->jumlah;
		
		$spdno = $data->spdno;
		$adagambar = strlen($data->spjlink);
		
	}
	
	drupal_set_title($title);
	
	$form['formspp']['id']= array(
		'#markup' => 'ID: <strong>' . $dokid . '</strong>',
	);
	//SP2D
	if (isSuperuser()) {
		if ($nosudah) {
			$form['SP2D'] = array (
				'#type' => 'fieldset',
				'#title'=> 'SP2D',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);	

			$form['SP2D']['formsp2dno'] = array(
				'#prefix' => '<div class="col-md-6">',
				'#suffix' => '</div>',				
			);
				$form['SP2D']['formsp2dno']['formsp2dno_title'] = array(
					'#markup' => '<small>Nomor SP2D</small>',
				);			
				$form['SP2D']['formsp2dno']['sp2dno'] = array(
					'#type' => 'textfield',
					// The entire enclosing div created here gets replaced when dropdown_first
					// is changed.
					//'#disabled' => true,
					'#default_value' => $sp2dno,
				);

			$form['SP2D']['formsp2dtgl'] = array(
				'#prefix' => '<div class="col-md-3">',
				'#suffix' => '</div>',				
			);				
				$form['SP2D']['formsp2dtgl']['sp2dtgl_title'] = array(
				'#markup' => '<small>Tanggal SP2D</small>',
				);
				$form['SP2D']['formsp2dtgl']['sp2dtgl']= array(
				'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
				'#default_value' => $sp2dtgl, 								 
				'#date_format' => 'd-m-Y',
				'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
				'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
				//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
				'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
				//'#description' => 'Tanggal',
				);				

			$form['SP2D']['formsp2dcmd'] = array(
				'#prefix' => '<div class="col-md-3">',
				'#suffix' => '</div>',				
			);				
			$ret = soap_enlisting($dokid);
			$form['SP2D']['formsp2dcmd']['submitsoap2']= array(
				'#markup' => '<a href="'.$ret.'" target="_blank" class="btn btn-danger btn-sm pull-right"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Penguji 2</a>',
				//'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right'),'target'=>array('_blank')),
			);
			$form['SP2D']['formsp2dcmd']['submitsoap']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Penguji',
				'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
			);				
			$form['SP2D']['formsp2dcmd']['submitprint']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);				
			
		} else {
 
			$form['SP2D'] = array (
				'#type' => 'fieldset',
				'#title'=> 'SP2D<em class="text-info pull-right">Nomor Terakhir : ' . apbd_getmaxnosp2d('4', '0') . '</em>',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);	 
		
			if (arg(3)=='auto') {
				$sp2dno = apbd_getnosp2d('1', '0');
			}

			if ($sp2dok) {
				$form['SP2D']['formsp2dno'] = array(
					'#prefix' => '<div class="col-md-6">',
					'#suffix' => '</div>',				
				);
					$form['SP2D']['formsp2dno']['formsp2dno_title'] = array(
						'#markup' => '<small>Nomor SP2D</small>',
					);			
					$form['SP2D']['formsp2dno']['sp2dno'] = array(
						'#type' => 'textfield',
						// The entire enclosing div created here gets replaced when dropdown_first
						// is changed.
						//'#disabled' => true,
						'#default_value' => $sp2dno,
					);
				$form['SP2D']['formsp2dcmd'] = array(
					'#prefix' => '<div class="col-md-3">',
					'#suffix' => '</div>',				
				);
					$form['SP2D']['formsp2dcmd']['submit']= array(
						'#type' => 'submit',
						'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
						'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
					);
					$form['SP2D']['formsp2dcmd']['submitauto']= array(
						'#type' => 'submit',
						'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Otomatis',
						'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
					);
				
			} else {
				$form['SP2D']['formsp2dno'] = array(
					'#prefix' => '<div class="col-md-6">',
					'#suffix' => '</div>',				
				);
					$form['SP2D']['formsp2dno']['formsp2dno_title'] = array(
						'#markup' => '<small>Nomor SP2D</small>',
					);				
					$form['SP2D']['formsp2dno']['sp2dno'] = array(
						'#type' => 'item',
						//'#title' =>  t('No. SP2D'),
						// The entire enclosing div created here gets replaced when dropdown_first
						// is changed.
						//'#disabled' => true,
						//'#suffix' => '<button class="btn btn-info btn-sm btn btn-sm btn-default form-submit" value="submitauto">OTOMATIS</button>',
						'#markup' => '<font style="color:red">SP2D Belum Diverifikasi</font>',
					);
			}
			$form['SP2D']['formsp2dtgl'] = array(
				'#prefix' => '<div class="col-md-3">',
				'#suffix' => '</div>',				
			);				
				$form['SP2D']['formsp2dtgl']['sp2dtgl_title'] = array(
				'#markup' => '<small>Tanggal SP2D</small>',
				);
				$form['SP2D']['formsp2dtgl']['sp2dtgl']= array(
				'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
				'#default_value' => $sp2dtgl, 								 
				'#date_format' => 'd-m-Y',
				'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
				'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
				//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
				'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
				//'#description' => 'Tanggal',
				);			
		}



	
	} else {								//SKPD
		$form['SP2D'] = array (
			'#type' => 'fieldset',
			'#title'=> 'SP2D',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);	

		$form['SP2D']['sp2dno'] = array(
			//'#type' => 'item',
			//'#title' =>  t('No. SP2D'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#suffix' => '<button class="btn btn-info btn-sm btn btn-sm btn-default form-submit" value="submitauto">OTOMATIS</button>',
			'#markup' => '<div class="alert alert-info" role="alert"><p align="right">Nomor/Tanggal : <strong>' . $sp2dno . ', ' . $sp2dtgl . '</strong></div>',
			//'#markup' => '<p align="right"><strong>' . $sp2dno . '</strong></p>',
		);
		
	}

	$form['SP2D']['e_sp2dno'] = array(
		'#type' => 'value',
		'#value' => $sp2dno,
	);			

	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	

	
	//SKPD
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			

	$form['tglawal'] = array(
		'#type' => 'value',
		'#value' => $tglawal,
	);			
	$form['tglakhir'] = array(
		'#type' => 'value',
		'#value' => $tglakhir,
	);			
	
	$form['sppspm'] = array (
		'#type' => 'fieldset',
		'#title'=> 'SPP/SPM',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['sppspm']['spmno'] = array(
			'#type' => 'item',
			'#title' =>  'SPM',
			'#prefix' => '<div class="col-md-6">',
			 '#suffix' => '</div>',
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#required' => TRUE,
			'#markup' => '<p>' . l($data->spmno . ', ' . apbd_fd_long($data->spmtgl), 'http://simkedajepara.web.id/edoc2019/spm/E_SPM_' . $dokid . '.PDF', array('attributes' => array('class' => null))) . '</p>',
		);	
		///edoc2019/spm/E_SPM_19C701099.PDF
		$form['sppspm']['periode'] = array(
			'#type' => 'item',
			'#title' =>  t('Periode SPJ'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',				
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#required' => TRUE,
			'#markup' => '<p><strong>' . $periode . '</strong></p>',
		);		

		$form['sppspm']['sppno'] = array(
			'#type' => 'item',
			'#title' =>  'SPP',
			'#prefix' => '<div class="col-md-6">',
			 '#suffix' => '</div>',
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#required' => TRUE,
			'#markup' => '<p>' . l($data->sppno . ', ' . apbd_fd_long($data->spptgl), 'gubaruspp/edit/' . $dokid, array('attributes' => array('class' => null))) . '</p>',
		);	
		
		//drupal_set_message($tanggalsql);
		$form['sppspm']['keperluan'] = array(
			'#type' => 'item',
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',		
			'#title' =>  t('Keperluan'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			'#markup' => '<p><strong>' . $keperluan . '</strong></p>',
		);
	
	//bendahara
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PENERIMA',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'item', 
			'#title' =>  t('Nama'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',			
			//'#required' => TRUE,
			'#markup'=> '<p><strong>' . $penerimanama . '</strong></p>', 
		);				
		$form['formpenerima']['penerimabankrekening']= array(
			'#type'         => 'item', 
			'#title' =>  t('Rekening'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',			
			//'#required' => TRUE,
			'#markup'=> '<p><strong>' . $penerimabankrekening . '</strong></p>', 
		);		
		 					
		$form['formpenerima']['penerimabanknama']= array(
			'#type'         => 'item', 
			'#title' =>  t('Bank'),
			//'#required' => TRUE,
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',			
			'#markup'=> '<p><strong>' . $kodebank . ' - ' . $penerimabanknama . '</strong></p>', 
		);				
		$form['formpenerima']['penerimanpwp']= array(
			'#type'         => 'item', 
			'#title' =>  t('NPWP'),
			//'#required' => TRUE,
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',			
			'#markup'=> '<p><strong>' . $penerimanpwp . '</strong></p>', 
		);	
			
	//KEGIATAN
	if ($kodekeg=='') {
		$kodekeg = $_SESSION["kodekeg"];
	} else {
		$_SESSION["kodekeg"] = $kodekeg;
	}
	if ($tanggalsql=='') {
		$tanggalsql = $_SESSION["tanggalsql"];
	} else {
		$_SESSION["tanggalsql"] = $tanggalsql;
	}
	if ($dokid=='') {
		$dokid = $_SESSION["verifikasi_spp_gu"];
	} else {
		$_SESSION["verifikasi_spp_gu"] = $dokid;
	}

	$form['formkegiatan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KEGIATAN<em class="text-info pull-right">' . apbd_fn($jumlah) . '</em>',
		//'#prefix' => '<div class="col-md-12">',
		//'#suffix' => '</div>',					
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$nokeg = 0;
	$reskeg = db_query('select k.kodekeg,k.kegiatan,sum(dr.jumlah) jumlah from {kegiatanskpd} k inner join {dokumenrekening} dr on k.kodekeg=dr.kodekeg where dr.dokid=:dokid group by k.kodekeg,k.kegiatan order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		$form['formkegiatan']['kegiatan'. $nokeg]= array(
			'#type'         => 'checkbox', 
			'#title' =>  t($nokeg . '. ' . $datakeg->kegiatan) . ', sebesar <strong>' . apbd_fn($datakeg->jumlah) . '</strong>',
			'#default_value'=> 0 , 
			'#validated' => TRUE,
			'#ajax' => array(
				'event'=>'change',
				'callback' => '_ajax_kegiatan'. $nokeg,
				'wrapper' => 'kegiatan'.$nokeg.'-wrapper',
			),					
		);				
		
		// Wrapper for rekdetil dropdown list
		$form['formkegiatan']['wrapperkegiatan'. $nokeg] = array(
			'#prefix' => '<div id="kegiatan'.$nokeg.'-wrapper">',
			'#suffix' => '</div>',
		);

		if (isset($form_state['values']['kegiatan'.$nokeg])) {
			$kegiatan = $form_state['values']['kegiatan'.$nokeg];
		} else {
			$kegiatan = 0;
		}
		//drupal_set_message($nokeg);
		if ($kegiatan == '1') {
			
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => kegiatan($dokid, $datakeg->kodekeg, $tanggalsql),
			);		
		}	else {
			$form['formkegiatan']['wrapperkegiatan'.$nokeg]['bk5'] = array (
				'#type' => 'item',
				'#markup' => '', //verifikasi_bk5($dokid, $kodeuk, $datakeg->kodekeg, $tglawal, $tglakhir),
			);	
		}		
	}		
	
	//KELENGKAPAN
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KELENGKAPAN DOKUMEN',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'ada', 'tidakada'));
	$query->fields('lt', array('uraian'));
	$query->condition('dk.dokid', $dokid, '=');
	//$query->condition('dk.ada', '1', '=');
	$query->orderBy('lt.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++; 
		$kode = $data->kodekelengkapan;
		$uraian = $data->uraian;
		$ada = $data->ada;
		$tidakada = $data->tidakada;
		$form['formkelengkapan']['tablekelengkapan']['kodekelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraiankelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formkelengkapan']['tablekelengkapan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td></tr>',
		); 
	}
	
	if ($i==0) {
		$form['formkelengkapan']['tablekelengkapan']['kodekelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => '',
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraiankelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => '',
		); 
		
		$form['formkelengkapan']['tablekelengkapan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p style="color:red">Tidak ada kelengkapan</p>', 
			'#suffix' => '</td></tr>',
		); 		
	}
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	//FORM SIMPAN ENABLE
		if ($sp2dok==0) {
			/*
			$form['formdata']['ket'] = array(
			'#type' => 'item',
			//'#title' =>  t('Keterangan'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#required' => TRUE,
			'#markup' => '<p style="color:red;">espm tidak tersedia</p>',
			);
			*/
			$disabled_ver = !is_eSPMExists($dokid);
			//$disabled_ver = false;
			$form['formdata']['submitsp2dok']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
				'#disabled' => $disabled_ver,
				'#attributes' => array('class' => array('btn btn-info btn-sm')),
			);
			$form['formdata']['submitsp2dtolak']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Tolak',
				'#attributes' => array('class' => array('btn btn-danger btn-sm')),
			);
		} else {
			
			
			$form['formdata']['submitsp2dnotok']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Batalkan',
				'#attributes' => array('class' => array('btn btn-danger btn-sm')),
			);
			$form['formdata']['submitsp2dkoreksi']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-edit-sign" aria-hidden="true"></span> Koreksi',
				'#attributes' => array('class' => array('btn btn-info btn-sm')),
			);
				
			
		}

		$form['formdata']['submitinfo']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Info',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
		);

		$form['formdata']['submit']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
			'#attributes' => array('class' => array('btn btn-success btn-sm')),
			'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		);
	
	return $form;
}

function gubarusp2d_edit_main_form_validate($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dok']) {
		///if (isAdministrator()) {
		//	drupal_set_message('x');
		//}
		
		

		//CEK KEPERLUAN
		$keperluan = $form_state['values']['keperluan'];
		if ($keperluan == '') {
			// form_set_error('keperluan', 'Keperluan SPM belum diisikan');
		}		
		
		//CEK Jumlah
		/*
		$strerr_batas = '';
		$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
		for ($n=1; $n <= $jumlahrekrekening; $n++) {
			$jumlah = $form_state['values']['jumlahapbd' . $n];			
			$batas = $form_state['values']['batas' . $n];
			
			if ($jumlah>$batas) $strerr_batas .= $form_state['values']['uraianapbd' . $n] . '; '; 	
		}		
		if ($strerr_batas != '') {
			form_set_error('', 'Rekening sbb : ' . $strerr_batas ' melebihi batas anggaran);
		}		
		*/		
		
	}	
}
	
function gubarusp2d_edit_main_form_submit($form, &$form_state) {
$dokid = $form_state['values']['dokid'];
	
if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
	drupal_goto('gubarusp2d/edit/' . $dokid . '/pdf1');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submittutup']) {
	drupal_goto('gusp2darsip');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitauto']) {
	drupal_goto('gubarusp2d/edit/' . $dokid . '/auto');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dtolak']) {
	
	drupal_goto('tolak/edit/'. $dokid);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap']) {
	$ret = soap_sp2d_add($dokid);
	
	//$ret = sendrest($dokid);
	drupal_set_message($ret);
	
}  else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap2']) {
	$ret = soap_enlisting($dokid);
	
	//$ret = sendrest($dokid);
	drupal_goto($ret);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitinfo']) {
	drupal_goto('verifikasisp2dguinfo/bk5/' . $dokid);
		

}else {	
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$sp2dno = $form_state['values']['sp2dno'];
	//$sp2dtgl = $form_state['values']['sp2dtgl'];
	//$sp2dtglsql = $sp2dtgl['year'] . '-' . $sp2dtgl['month'] . '-' . $sp2dtgl['day'];
	$sp2dtglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['sp2dtgl']);

	//$keperluan = $form_state['values']['keperluan'];
	$kodebank = $form_state['values']['kodebank'];
	
	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];

	//BEGIN TRANSACTION
	$transaction = db_transaction();
	
	//JURNAL
	try {	
		
		//DOKUMEN
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dok']) {
			/*
			$query = db_update('dokumen')
					->fields( 
					array(
						'keperluan' => $keperluan,

						'sp2dno' =>$sp2dno,
						'sp2dtgl' =>$sp2dtglsql,

						'penerimanama' => $penerimanama,
						'penerimabanknama' => $penerimabanknama,
						'penerimabankrekening' => $penerimabankrekening,
						'penerimanpwp' => $penerimanpwp,
						
						'kodebank' => $kodebank,
						

						'sp2dok' => '1',
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
			*/
			drupal_goto('verifikasisp2dgu/spp2/' . $dokid);
		
		} else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dnotok']) {

			$query = db_update('dokumen')
					->fields( 
					array(
						//'keperluan' => $keperluan,

						
						'sp2dok' => '0',
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		
			
		} else {	
			$query = db_update('dokumen')
					->fields( 
					array(
						//'keperluan' => $keperluan,

						'sp2dno' =>$sp2dno,
						'sp2dtgl' =>$sp2dtglsql,

						// 'penerimanama' => $penerimanama,
						// 'penerimabanknama' => $penerimabanknama,
						// 'penerimabankrekening' => $penerimabankrekening,
						// 'penerimanpwp' => $penerimanpwp,
						
						'kodebank' => $kodebank,
						
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		}	
	
	}
		catch (Exception $e) {
		$transaction->rollback();
		atchdog_exception('gantiuangsp2d-' . $nourut, $e);
	}
	//if ($res) drupal_goto('kaskeluarantrian');
	//drupal_goto('gantiuangsp2darsip');
	//drupal_goto(drupal_get_destination());
}	
}


function printsp2d_digital($dokid) {

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	//$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip', 'bendaharanama', 'bendaharanpwp', 'bendahararekening', 'bendaharabank', 'bendaharaatasnama'));
	//$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$sp2dno = $data->sp2dno;
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);

		$penerimanama =  $data->bendaharaatasnama;
		$penerimabanknama = $data->bendaharabank;
		$penerimabankrekening = $data->bendahararekening;
		$penerimanpwp = $data->bendaharanpwp;

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
		
		$kodedinas = $data->kodedinas;	// . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);	
		

	}		
	
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '70px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'colspan'=>'5', 'width' => '410px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '70px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'SURAT PERINTAH PENCAIRAN DANA (SP2D)', 'colspan'=>'5', 'width' => '410px','align'=>'center','style'=>'border-right:1px solid black;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '70px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'colspan'=>'5', 'width' => '410px','align'=>'center','style'=>'border-right:1px solid black;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => '', 'colspan'=>'6', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'colspan'=>'3', 'width' => '330px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => 'Nomor :', 'width' => '55px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => $sp2dno, 'width' => '85px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:140%;'),
	);
	$rows[]=array(
		array('data' => 'Nomor SPM', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $spmno, 'width' => '280px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Dari', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Kuasa BUD', 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $spmtgl, 'width' => '280px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tahun Anggaran', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '50px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		array('data' => $namauk, 'colspan'=>'4', 'width' => '420px','align'=>'left','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
		//array('data' => '', 'width' => '80px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);

	
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'BANK JATENG CABANG JEPARA','colspan'=>'3', 'width' => '365px','align'=>'left','style'=>'border:none;'),
		array('data' => $dokid, 'width' => '55px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Hendaklah mencairkan/memindahbukukan dari RKUD nomor : 1.015.03256.5', 'colspan'=>'6', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
	if(strlen($terbilang)>60){
		$rows[]=array(
			array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'colspan'=>'6', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:0.1px solid black;border-right:1px solid black;'),
		);
	}
	else{
		$rows[]=array(
			array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'colspan'=>'6', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'colspan'=>'6', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:0.1px solid black;border-right:1px solid black;'),
		);
	}
	$rows[]=array(
		array('data' => 'Kepada', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanama, 'colspan'=>'4', 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanpwp, 'colspan'=>'4', 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No. Rekening Bank', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabankrekening, 'colspan'=>'4', 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabanknama, 'colspan'=>'4', 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'colspan'=>'4', 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	
	
	//REKENING
	$numkeg = 0;
	$rows[]=array(
		array('data' => 'PEMBEBANAN PADA KEGIATAN', 'colspan'=>'6', 'width' => '480px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$res_keg = db_query('select distinct k.kodekeg, k.kodepro, k.kegiatan from dokumenrekening dr inner join kegiatanskpd k on dr.kodekeg=k.kodekeg where dr.dokid=:dokid order by k.kegiatan', array(':dokid'=>$dokid));
	foreach ($res_keg as $data_keg) {
		$numkeg++;
		$nomorkeg = $kodedinas . '.' . $data_keg->kodepro . '.' . substr($data_keg->kodekeg, -3);	
		$rows[]=array(
			array('data' => $numkeg . '.', 'width' => '15px','align'=>'left','style'=>'border-left:1px solid black;'),
			array('data' => $nomorkeg . ' - ' . $data_keg->kegiatan, 'colspan'=>'5', 'width' => '465px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
		
	
		$rows[]=array(
			array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
			array('data' => ' Kode Rekening', 'width' => '90px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
			array('data' => ' Uraian', 'colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
			array('data' => ' Jumlah (Rp)', 'width' => '90px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		);
	
		# get the desired fields from the database
		$query = db_select('dokumenrekening', 'di');
		$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
		
		$query->fields('di', array('jumlah'));
		$query->fields('ro', array('kodero', 'uraian'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('di.dokid', $dokid, '=');
		$query->condition('di.jumlah', 0, '>');
		$query->condition('di.kodekeg', $data_keg->kodekeg, '=');
		$query->orderBy('ro.kodero', 'ASC');

		$results = $query->execute();
		
		$n = 0; $subtotal = 0;

		$results = $query->execute();
		foreach ($results as $data) {
			$n++;
			$subtotal += $data->jumlah;
			$rows[]=array(
				array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '... ' . apbd_format_rek_rincianobyek($data->kodero), 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
				array('data' => $data->uraian,'colspan'=>'3',  'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			);

		}
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => 'Sub Total', 'colspan'=>'3', 'width' => '270px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => apbd_fn($subtotal), 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		);
	}

	$rows[]=array(
		array('data' => 'Jumlah yang Diminta (1)', 'colspan'=>'5', 'width' => '390px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//POTONGAN..........
	$rows[]=array(
		array('data' => 'POTONGAN - POTONGAN', 'colspan'=>'6', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian','colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);

	# get the desired fields from the database
	$query = db_select('dokumenpotongan', 'dp');
	$query->join('ltpotongan', 'p', 'dp.kodepotongan=p.kodepotongan');
	$query->fields('p', array('kodepotongan', 'uraian'));
	$query->fields('dp', array('jumlah', 'keterangan'));
	$query->condition('dp.dokid', $dokid, '=');
	$query->condition('dp.jumlah', 0, '>');
	$query->orderBy('dp.kodepotongan', 'ASC');
	$results = $query->execute();

	$results = $query->execute();
	$n = 0;
	foreach ($results as $data) {
		$n++;
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data->uraian, 'colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}		
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Tidak ada potongan', 'colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);$n=1;
	}
	
	$rows[]=array(
		array('data' => 'Jumlah Potongan (2)', 'colspan'=>'4', 'width' => '300px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//PAJAK .....................
	$rows[]=array(
		array('data' => 'PAJAK(TIDAK MENGURANGI JUMLAH SP2D)', 'colspan'=>'6', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);

	# get the desired fields from the database
	$query = db_select('dokumenpajak', 'dp');
	$query->join('ltpajak', 'p', 'dp.kodepajak=p.kodepajak');
	$query->fields('p', array('kodepajak', 'uraian'));
	$query->fields('dp', array('jumlah', 'keterangan'));
	$query->condition('dp.dokid', $dokid, '=');
	$query->condition('dp.jumlah', 0, '>');
	$query->orderBy('dp.kodepajak', 'ASC');
	$results = $query->execute();

	$results = $query->execute();
	$n = 0;
	$totalpajak = 0;
	foreach ($results as $data) {
		$n++;	
		$totalpajak += $data->jumlah;
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data->uraian, 'colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Tidak ada pajak', 'colspan'=>'3', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}	
	$rows[]=array(
		array('data' => 'Jumlah Pajak (3)', 'width' => '300px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => apbd_fn($totalpajak), 'colspan'=>'4', 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//SP2D ............
	$rows[]=array(
		array('data' => 'SP2D YANG DIBAYARKAN', 'colspan'=>'6', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Diminta (1)', 'colspan'=>'4', 'width' => '300px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	
	$rows[]=array(
			array('data' => ' Jumlah Potongan (2)', 'colspan'=>'4', 'width' => '300px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Dibayarkan(1)-(2)', 'colspan'=>'4', 'width' => '300px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => $netto, 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Uang Sejumlah :', 'colspan'=>'6', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '##' . $terbilangnetto . '##', 'colspan'=>'6', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
	
	
	if(strlen($terbilangnetto)<75){
		$rows[] = array(
						array('data' => '','colspan'=>'6',  'width' => '480px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		);
	}
	$rows[] = array(
					array('data' => '','width' => '480px', 'colspan'=>'6', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '240px','colspan'=>'3', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;'),
					array('data' => 'Jepara, ' . $sp2dtgl,'colspan'=>'3', 'width' => '240px', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => apbd_bud_jabatan(),'colspan'=>'3', 'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	); 
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;font-weight:bold;text-decoration:underline;'),
					array('data' => apbd_bud_nama(),'colspan'=>'3', 'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'NIP. ' . apbd_bud_nip(),'colspan'=>'3', 'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);	
	$rows[] = array(
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => '','width' => '240px', 'colspan'=>'3', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}	


?>
