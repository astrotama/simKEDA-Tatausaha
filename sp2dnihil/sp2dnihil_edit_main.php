<?php
function sp2dnihil_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$x = $_SERVER['HTTP_REFERER'];
	
	//drupal_set_message('abc : ' . $x);
	
	$dokid = arg(2);	
	if (arg(3)=='pdf0') {			

		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
	
		$output = printsp2d_digital($dokid);
		
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
		//drupal_set_message();
		$output_form = drupal_get_form('sp2dnihil_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function sp2dnihil_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["sp2dnihillastpage"] = $referer;
	else
		$referer = $_SESSION["sp2dnihillastpage"];
	
	$jenisdokumen = 7;
	$kodeuk = '';
	$kodekeg = '';
	$kegiatan = '';
	$jenisbelanja = 1;
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('d');
	$sp2dtgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Gaji Januari ' . apbd_tahun();
	$jeniskegiatan = '1';
	$penerimanama = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
	$pptknama = '';
	$pptknip = '';
	$jumlah = 0;
	$pajak = 0;
	$potongan = 0;
	$netto = 0;
	$sp2dok = 0;
	
	$nosudah = false;
	
	$dokid = arg(2);
	/*
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->fields('d', array('dokid', 'spmno', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'pptknama', 'pptknip', 'sp2dok', 'jenisgaji'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	# execute the query	
	$results = $query->execute();
	*/
	
	$results = db_query('select d.dokid, d.spmno, d.jenisdokumen, d.sp2dno, d.sp2dtgl, d.kodekeg, d.bulan, d.keperluan, d.jumlah, d.potongan, d.netto, d.penerimanama, d.penerimabankrekening, d.penerimabanknama, d.penerimanpwp, d.pptknama, d.pptknip, d.sp2dok, d.jenisgaji, u.kodeuk, u.namasingkat, d.spjlink from {dokumen} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where d.dokid=:dokid', array(':dokid' => $dokid));
	foreach ($results as $data) {
		
		
		
		$dokid = $data->dokid;
		$sp2dno = $data->sp2dno;
		
		$jenisdokumen = $data->jenisdokumen;
		if ($jenisdokumen=='7')
			$title = 'SP2D TU-Nihil';
		elseif ($jenisdokumen=='5')
			$title = 'SP2D GU-Nihil';
		else
			$title = 'Jenis SP2D tidak dikenal.';
		
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
		
		//$sp2dtgl = dateapi_convert_timestamp_to_datetime($data->sp2dtgl);
		$spmno = $data->spmno;
		$bulan = $data->bulan;
		
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$keperluan = $data->keperluan;
		
		$penerimanama = $data->penerimanama;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;

		$pptknama = $data->pptknama;
		$pptknip = $data->pptknip;
		
		$jumlah = $data->jumlah;
		$potongan = $data->potongan;
		$netto = $data->netto;
		
		$sp2dok = $data->sp2dok;
		$jenisgaji = $data->jenisgaji;
		$adagambar = strlen($data->spjlink);
		
	}
	
	drupal_set_title($title);

	//CETAK ATAS	
	$form['formcetak']['submitgambar']= array(
		'#type' => 'item',
		'#markup' => apbd_button_image('upload/edit/' . $dokid, $adagambar),
	);
	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);
	$form['bulan'] = array(
		'#type' => 'value',
		'#value' => $bulan,
	);

	$form['e_sp2dno'] = array(
		'#type' => 'value',
		'#value' => $sp2dno,
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
			
			$form['SP2D']['sp2dno'] = array(
				'#type' => 'textfield',
				'#title' =>  t('No. SP2D'),
				// The entire enclosing div created here gets replaced when dropdown_first
				// is changed.
				//'#disabled' => true,
				'#default_value' => $sp2dno,
			);
		/*
			$ret = soap_enlisting($dokid);
			$form['SP2D']['submitsoap2']= array(
				'#markup' => '<a href="'.$ret.'" target="_blank" class="btn btn-danger btn-sm pull-right"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Penguji 2</a>',
				//'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right'),'target'=>array('_blank')),
			);
			$form['SP2D']['submitsoap']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Penguji',
				'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
			);
		*/
			$form['SP2D']['submitprint']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);				
			
		} else {

			$form['SP2D'] = array (
				'#type' => 'fieldset',
				'#title'=> 'SP2D<em class="text-info pull-right">Nomor Terakhir : ' . apbd_getmaxnosp2d('3', $jenisgaji) . '</em>',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);	
		
			if (arg(3)=='auto') {
				$sp2dno = apbd_getnosp2d($jenisdokumen, $jenisgaji);
			}
			
			if ($sp2dok) {
				$form['SP2D']['sp2dno'] = array(
					'#type' => 'textfield',
					'#title' =>  t('No. SP2D'),
					// The entire enclosing div created here gets replaced when dropdown_first
					// is changed.
					//'#disabled' => true,
					//'#suffix' => '<button class="btn btn-info btn-sm btn btn-sm btn-default form-submit" value="submitauto">OTOMATIS</button>',
					'#default_value' => $sp2dno,
				);
				$form['SP2D']['submit']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
					'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
				);
				$form['SP2D']['submitauto']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Otomatis',
					'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
				);
				
			} else {
				$form['SP2D']['sp2dno'] = array(
					'#type' => 'item',
					//'#title' =>  t('No. SP2D'),
					// The entire enclosing div created here gets replaced when dropdown_first
					// is changed.
					//'#disabled' => true,
					//'#suffix' => '<button class="btn btn-info btn-sm btn btn-sm btn-default form-submit" value="submitauto">OTOMATIS</button>',
					'#markup' => '<font style="color:red">SP2D Belum Diverifikasi</font>',
				);
			}		
		}
		$form['SP2D']['sp2dtgl'] = array(
			'#type' => 'date',
			'#title' =>  t('Tanggal SP2D'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			'#default_value' => $sp2dtgl,
			'#default_value'=> array(
				'year' => format_date($sp2dtgl, 'custom', 'Y'),
				'month' => format_date($sp2dtgl, 'custom', 'n'), 
				'day' => format_date($sp2dtgl, 'custom', 'j'), 
			  ), 
			
		);			
	
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
			'#markup' => '<div class="alert alert-info" role="alert"><p align="right">Nomor : <strong>' . $sp2dno . '</strong></div>',
			//'#markup' => '<p align="right"><strong>' . $sp2dno . '</strong></p>',
		);
		$form['SP2D']['sp2dtgl'] = array(
			//'#type' => 'item',
			//'#title' =>  t('Tanggal SP2D'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#markup' => '<p align="right"><strong>' . $sp2dtgl . '</strong></p>',
			'#markup' => '<div class="alert alert-info" role="alert"><p align="right">Tanggal : <strong>' . $sp2dtgl . '</strong></div>',
			
		);	
		
	}

	$form['SP2D']['e_sp2dno'] = array(
		'#type' => 'value',
		'#value' => $sp2dno,
	);	

	//SKPD
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	
	$form['spmno'] = array(
		'#type' => 'item',
		'#title' =>  t('No. SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#markup' => '<p>' . $spmno . '</p>',
	);


	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);


	//'pimpinannama', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'BENDAHARA',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanama, 
		);				
		$form['formpenerima']['penerimabankrekening']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Rekening'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabankrekening, 
		);				
		$form['formpenerima']['penerimabanknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Bank'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabanknama, 
		);				
		$form['formpenerima']['penerimanpwp']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NPWP'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanpwp, 
		);	
	

	//KELENGKAPAN
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KELENGKAPAN DOKUMEN',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0;
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'ada', 'tidakada'));
	$query->fields('lt', array('uraian'));
	$query->condition('dk.dokid', $dokid, '=');
	$query->condition('dk.ada', '1', '=');
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
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);	

	//REKENING
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		'#title'=> 'REKENING<em class="text-info pull-right">' . apbd_fn($jumlah) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formrekening']['table']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="90px">ANGGARAN</th><th width="50px">CAIR</th><th width="90px">BATAS</th><th width="90px">JUMLAH</th><th width="90px">SISA</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	
	$i = 0;
	
	$results = db_query('select r.kodero, r.uraian, a.anggaran, di.jumlah from {rincianobyek} r inner join {dokumenrekening} di on r.kodero=di.kodero inner join {anggperkeg} a on di.kodero=a.kodero where di.dokid=:dokid and di.jumlah>0 and a.kodekeg=:kodekeg', array(':dokid'=>$dokid, ':kodekeg'=>$kodekeg));
	foreach ($results as $data) {
		$i++; 
		$kodero = $data->kodero;
		//$uraian = $data->uraian;
		$jumlah = $data->jumlah;

		$uraian =  l($data->uraian, 'laporan/realisasikegsp2d/filter/' . $kodekeg . '/' . $data->kodero . '/' . $tanggalsql, array('attributes' => array('class' => null)));
		
		$cair = apbd_readrealisasikegiatan_rekening($dokid, $kodekeg, $data->kodero, $tanggalsql);
		$batas = $data->anggaran - $cair;
		
		if ($batas>=$jumlah) {
			$strkode = $kodero;
			$struraian = $uraian;
			$stranggaran = '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>';
			$strcair = '<p class="text-right">' . apbd_fn($cair) . '</p>';
			
			$strbatas =  '<p class="text-right">' . apbd_fn($batas) . '</p>';
			$strjumlah = '<p class="text-right">' . apbd_fn($jumlah) . '</p>';
			$strsaldo = '<p class="text-right">' . apbd_fn($batas - $jumlah) . '</p>';
			
		} else {
			$strkode = '<p style="color:red">' . $kodero . '</p>';
			$struraian = '<p style="color:red">' . $uraian . '</p>';
			$stranggaran = '<p class="text-right" style="color:red">' . apbd_fn($data->anggaran) . '</p>';
			$strcair = '<p class="text-right" style="color:red">' . apbd_fn($cair) . '</p>';

			$strbatas =  '<p class="text-right" style="color:red">' . apbd_fn($batas) . '</p>';
			$strjumlah = '<p class="text-right" style="color:red">' . apbd_fn($jumlah) . '</p>';
			$strjumlah = '<p class="text-right" style="color:red">' . apbd_fn($batas - $jumlah) . '</p>';
		}
		
		$form['formrekening']['table']['batas' . $i]= array(
				'#type' => 'value',
				'#value' => $batas,
		); 
		$form['formrekening']['table']['jumlahapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $jumlah,
		); 
		$form['formrekening']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $kodero,
		); 
		$form['formrekening']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formrekening']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $strkode,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $struraian, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['anggaran' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $stranggaran, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['cair' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $strcair, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['strbatas' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $strbatas, 
			'#suffix' => '</td>',
		); 		
		$form['formrekening']['table']['jumlahsisastr' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $strjumlah, 
			'#suffix' => '</td>',
		);			
		$form['formrekening']['table']['jumlahapbdstr' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $strsaldo, 
			'#suffix' => '</td></tr>',
		);			
	}	

	$form['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);

	/*		
	//POTONGAN	
	$form['formpotongan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'POTONGAN<em class="text-info pull-right">' . apbd_fn($potongan) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formpotongan']['tablepotongan']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="260px">KETERANGAN</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('dokumenpotongan', 'dp');
	$query->join('ltpotongan', 'p', 'dp.kodepotongan=p.kodepotongan');
	$query->fields('p', array('kodepotongan', 'uraian'));
	$query->fields('dp', array('jumlah', 'keterangan'));
	$query->condition('dp.dokid', $dokid, '=');
	$query->condition('dp.jumlah', 0, '>');
	$query->orderBy('dp.kodepotongan', 'ASC');
	$results = $query->execute();
	//dpq($query);
	foreach ($results as $data) {

		$i++; 
		$kode = $data->kodepotongan;
		$uraian = $data->uraian;
		$jumlah = $data->jumlah;
		$keterangan = $data->keterangan;
		$form['formpotongan']['tablepotongan']['kodepotongan' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formpotongan']['tablepotongan']['uraianpotongan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpotongan']['tablepotongan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpotongan']['tablepotongan']['kode' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kode,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpotongan']['tablepotongan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpotongan']['tablepotongan']['jumlahpotongan' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpotongan']['tablepotongan']['keteranganpotongan' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $keterangan, 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	
		
	}
	$form['jumlahrekpotongan']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	*/
	
	/*
	//PAJAK	
	$form['formpajak'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formpajak']['tablepajak']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="260px">KETERANGAN</th></tr>',
			 '#suffix' => '</table>',
		);	
		$i = 0;
		$query = db_select('dokumenpajak', 'dp');
		$query->join('ltpajak', 'p', 'dp.kodepajak=p.kodepajak');
		$query->fields('p', array('kodepajak', 'uraian'));
		$query->fields('dp', array('jumlah', 'keterangan'));
		$query->condition('dp.dokid', $dokid, '=');
		$query->condition('dp.jumlah', 0, '>');
		$query->orderBy('dp.kodepajak', 'ASC');
		$results = $query->execute();
		foreach ($results as $data) {

			$i++; 
			$kode = $data->kodepajak;
			$uraian = $data->uraian;
			$jumlah = $data->jumlah;
			$keterangan = $data->keterangan;
			$form['formpajak']['tablepajak']['kodepajak' . $i]= array(
					'#type' => 'value',
					'#value' => $kode,
			); 
			$form['formpajak']['tablepajak']['uraianpajak' . $i]= array(
					'#type' => 'value',
					'#value' => $uraian,
			); 
			
			$form['formpajak']['tablepajak']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i, 
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpajak']['tablepajak']['kode' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $kode,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formpajak']['tablepajak']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraian, 
				'#suffix' => '</td>',
			); 
			$form['formpajak']['tablepajak']['jumlahpajak' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);	
			$form['formpajak']['tablepajak']['keteranganpajak' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $keterangan, 
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	
			
		}
		$form['jumlahrekpajak']= array(
			'#type' => 'value',
			'#value' => $i,
		);

	*/
	
	/*	
	//PNS	
	$query = db_select('dokumenpns', 'dp');
	$query->addExpression('SUM(pns)', 'jumlahpns');
	$query->condition('dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$jumlahpns = $data->jumlahpns;
	}
	$form['formpns'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JUMLAH PNS<em class="text-info pull-right">' .  $jumlahpns . ' orang</em>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formpns']['tablepns']= array(
		'#prefix' => '<table class="table table-hover"><tr><th></th><th width="130px">PNS</th><th width="130px">ISTRI/SUAMI</th><th width="130px">ANAK</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 5;
	$query = db_select('dokumenpns', 'dp');
	$query->fields('dp', array('golongan', 'pns', 'istri', 'anak'));
	$query->condition('dp.dokid', $dokid, '=');
	$query->orderBy('dp.golongan', 'DESC');
	$results = $query->execute();
	foreach ($results as $data) {
		
		if ($data->golongan==4)
			$strG = 'IV';
		elseif ($data->golongan==3)
			$strG = 'III';
		elseif ($data->golongan==2)
			$strG = 'II';
		else
			$strG = 'I';
		
		$i = $data->golongan;
		
		$uraian = 'GOLONGAN ' . $strG;
		$pns = $data->pns;
		$istri = $data->istri;
		$anak = $data->anak;
		
		$form['formpns']['tablepns']['kodepns' . $i]= array(
				'#type' => 'value',
				'#value' => $i,
		); 
		$form['formpns']['tablepns']['uraianPNS' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpns']['tablepns']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpns']['tablepns']['jumlahpnspns' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $pns, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsistri' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $istri, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpns']['tablepns']['jumlahpnsanak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $anak, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	
	}
	*/


	if (isSuperuser()) {	
		
		$form['formdata']['submitprint']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
		
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
			
		}

		$form['formdata']['submit']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
			'#attributes' => array('class' => array('btn btn-success btn-sm')),
			'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		);
		
		
	} else {
		$form['formdata']['submittutup']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-exit" aria-hidden="true"></span> Tutup',
			'#attributes' => array('class' => array('btn btn-info btn-sm')),
		);
		
	}
	
	return $form;
}

function sp2dnihil_edit_main_form_validate($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$sp2dno = $form_state['values']['sp2dno'];
		$e_sp2dno = $form_state['values']['e_sp2dno'];		

		if ($sp2dno != $e_sp2dno) {
			if (apbd_is_duplikasi($sp2dno)) form_set_error('sp2dno', 'Nomor SP2D sudah ada. Ganti dengan nomor lain, bisa diketik manual atau klik tombol Otomatis');
		}
	}	

}
	
function sp2dnihil_edit_main_form_submit($form, &$form_state) {
$dokid = $form_state['values']['dokid'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
	drupal_goto('sp2dnihil/edit/' . $dokid . '/pdf1');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submittutup']) {
	drupal_goto('sp2dnihilarsip');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitauto']) {
	drupal_goto('sp2dnihil/edit/' . $dokid . '/auto');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap']) {
	$ret = soap_sp2d_add($dokid);
	drupal_set_message($ret);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dtolak']) {
	
	drupal_goto('tolak/edit/'. $dokid);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap2']) {
	$ret = soap_enlisting($dokid);
	
	//$ret = sendrest($dokid);
	drupal_goto($ret);

}else {	
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$sp2dno = $form_state['values']['sp2dno'];
	$sp2dtgl = $form_state['values']['sp2dtgl'];
	$sp2dtglsql = $sp2dtgl['year'] . '-' . $sp2dtgl['month'] . '-' . $sp2dtgl['day'];

	$keperluan = $form_state['values']['keperluan'];
	
	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	//BEGIN TRANSACTION
	$transaction = db_transaction();
	
	//JURNAL
	try {	

		//DOKUMEN
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dok']) {

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
						'pptknama' => $pptknama,
						'pptknip' => $pptknip,							
						
						'sp2dok' => '1',
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		
		} else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dnotok']) {

			$query = db_update('dokumen')
					->fields( 
					array(
						'keperluan' => $keperluan,
						
						'sp2dok' => '0',
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		
			
		} else {	
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
						'pptknama' => $pptknama,
						'pptknip' => $pptknip,							
						
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
		}	
	
	}
		catch (Exception $e) {
		$transaction->rollback();
		atchdog_exception('sp2dnihil-' . $nourut, $e);
	}
	//if ($res) drupal_goto('kaskeluarantrian');
	//drupal_goto('sp2dnihilarsip');
	//drupal_goto(drupal_get_destination());
}	
}

function printsp2d($dokid) {

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$sp2dno = $data->sp2dno;
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);

		$penerimanama = 'BENDAHARA PENGELUARAN ' . $data->namasingkat . ' (' . $data->penerimanama . ')';
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;

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
		
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);		

	}		
	
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'font-size:150%;border:none;'),
		array('data' => $sp2dno, 'width' => '70px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Nomor SPM', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $spmno, 'width' => '230px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Dari', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Kuasa BUD', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $spmtgl, 'width' => '230px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tahun Anggaran', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '130px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		array('data' => $namauk, 'width' => '138px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => '', 'width' => '138px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => '', 'width' => '20px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		array('data' => '', 'width' => '155px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'left','style'=>'border:none;'),
		array('data' => 'BANK JATENG CABANG JEPARA', 'width' => '453px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Hendaklah mencairkan/memindahbukukan dari RKUD nomor : 1.015.03256.5', 'width' => '608px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang . ')', 'width' => '608px','align'=>'left','style'=>'border-bottom:0.1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Kepada', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '480px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanpwp, 'width' => '480px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No. Rekening Bank', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabankrekening, 'width' => '480px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabanknama, 'width' => '480px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan', 'width' => '130px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '25px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '480px','align'=>'left','style'=>'border:none;'),
		
	);
	
	//REKENING
	$rows[]=array(
		array('data' => 'RINCIAN REKENING', 'width' => '608px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Kode Rekening', 'width' => '100px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '378px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah (Rp)', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
	);
	
	//baris pertama
	$rows[]=array(
		array('data' => $n, 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => $nomorkeg . '...', 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '378px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>''),
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
	foreach ($results as $data) {
		$n++;
		$rows[]=array(
			array('data' => $n, 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => '...' . $data->kodero, 'width' => '100px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '378px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>''),
		);
	}
		

	
	$rows[]=array(
		array('data' => 'Jumlah yang Diminta (1)', 'width' => '518px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;'),
	);
	
	//POTONGAN..........
	$rows[]=array(
		array('data' => 'POTONGAN - POTONGAN', 'width' => '608px','align'=>'center','style'=>'border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '300px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '130px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '138px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
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
			array('data' => $n, 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '138px','align'=>'left','style'=>''),
		);
	}		
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => 'Tidak ada potongan', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '138px','align'=>'left','style'=>''),
		);
	}
	$rows[]=array(
		array('data' => 'Jumlah Potongan (2)', 'width' => '340px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => $potongan, 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '', 'width' => '138px','align'=>'left','style'=>'border-top:1px solid black;'),
	);
	
	//PAJAK .....................
	$rows[]=array(
		array('data' => 'PAJAK(TIDAK MENGURANGI JUMLAH SP2D)', 'width' => '608px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '40px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '300px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '130px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '138px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
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
			array('data' => $n, 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '138px','align'=>'left','style'=>''),
		);
	}
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => 'Tidak ada pajak', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '138px','align'=>'left','style'=>''),
		);
	}	
	$rows[]=array(
		array('data' => 'Jumlah Pajak (3)', 'width' => '340px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => apbd_fn($totalpajak), 'width' => '130px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '138px','align'=>'left','style'=>'border-top:1px solid black;'),
	);
	
	//SP2D ............
	$rows[]=array(
		array('data' => 'SP2D YANG DIBAYARKAN', 'width' => '608px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Diminta (1)', 'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $jumlah, 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '138px','align'=>'left','style'=>''),
	);
	
	$rows[]=array(
			array('data' => ' Jumlah Potongan (2)', 'width' => '340px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $potongan, 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => ' ', 'width' => '138px','align'=>'left','style'=>''),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Dibayarkan(1)-(2)', 'width' => '340px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => $netto, 'width' => '130px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => ' ', 'width' => '138px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Uang Sejumlah :', 'width' => '608px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'ZZ' . $terbilangnetto . 'ZZ', 'width' => '608px','align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border:none;'),
	);
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}


function printsp2d_digital($dokid) {

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$sp2dno = $data->sp2dno;
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);

		$penerimanama =  $data->penerimanama;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;

		$spdno = '......................';
		$spdtgl = '......................';
		
		$namauk = $data->namauk;
		$pimpinannama = $data->pimpinannama;
		$pimpinanjabatan = $data->pimpinanjabatan;
		$pimpinannip = $data->pimpinannip;
		
		$keperluan = $data->keperluan;
		$jumlah = apbd_fn($data->jumlah);
		$terbilang = apbd_terbilang($data->jumlah);
		$bennama=$data->bendaharanama;
		$potongan = apbd_fn($data->potongan);
		$netto = apbd_fn($data->netto);
		$terbilangnetto = apbd_terbilang($data->netto);
		
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);		

	}		
	
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '70px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;'),
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '410px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '70px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'SURAT PERINTAH PENCAIRAN DANA (SP2D)', 'width' => '410px','align'=>'center','style'=>'border-right:1px solid black;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '70px','align'=>'center','style'=>'border-left:1px solid black;'),
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '410px','align'=>'center','style'=>'border-right:1px solid black;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '330px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => 'Nomor :', 'width' => '55px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => $sp2dno, 'width' => '85px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-size:110%;'),
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
		array('data' => $namauk, 'width' => '420px','align'=>'left','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
		//array('data' => '', 'width' => '80px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$overrek=0;
	if(strlen($namauk)>55){
		$overrek=1;
	}
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'BANK JATENG CABANG JEPARA', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => $dokid, 'width' => '45px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Hendaklah mencairkan/memindahbukukan dari RKUD nomor : 1.015.03256.5', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	if(strlen($terbilang)>50){
		$rows[]=array(
			array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:0.1px solid black;border-right:1px solid black;'),
		);
	}
	else{
		$rows[]=array(
			array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:0.1px solid black;border-right:1px solid black;'),
		);
	}
	$rows[]=array(
		array('data' => 'Kepada', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanpwp, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No. Rekening Bank', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabankrekening, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabanknama, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$minkep=0;
	if(strlen($keperluan)>65){
		$minkep=1;
	}
	//REKENING
	$rows[]=array(
		array('data' => 'RINCIAN REKENING', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Kode Rekening', 'width' => '90px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah (Rp)', 'width' => '90px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	//baris pertama
	$rows[]=array(
		array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $nomorkeg . '...', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
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
	$tmp=0;
	foreach ($results as $data) {
		$n++;
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '... ' . apbd_format_rek_rincianobyek($data->kodero), 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
		if(strlen($data->uraian)>48){
			$tmp++;
		}
	}
	for($a=($n+$tmp+$minkep);$a<16;$a++){
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}
	
	if(($n+$tmp)>16)	
		$overrek+=($n+$tmp)-15;	

	
	$rows[]=array(
		array('data' => 'Jumlah yang Diminta (1)', 'width' => '390px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//POTONGAN..........
	$rows[]=array(
		array('data' => 'POTONGAN - POTONGAN', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);

	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tidak ada potongan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$n=1;
	for($a=$n;$a<(8-$overrek);$a++){
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}
	$rows[]=array(
		array('data' => 'Jumlah Potongan (2)', 'width' => '300px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//PAJAK .....................
	$rows[]=array(
		array('data' => 'PAJAK(TIDAK MENGURANGI JUMLAH SP2D)', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
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
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tidak ada pajak', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Jumlah Pajak (3)', 'width' => '300px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => apbd_fn($totalpajak), 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//SP2D ............
	$rows[]=array(
		array('data' => 'SP2D YANG DIBAYARKAN', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Diminta (1)', 'width' => '300px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	
	$rows[]=array(
			array('data' => ' Jumlah Potongan (2)', 'width' => '300px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Dibayarkan(1)-(2)', 'width' => '300px','align'=>'left','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => $netto, 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Uang Sejumlah :', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'ZZ' . $terbilangnetto . 'ZZ', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	if(strlen($terbilangnetto)<85){
		$rows[] = array(
						array('data' => '','width' => '480px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		);
	}

	
	
	$rows[] = array(
					array('data' => '','width' => '480px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-top:1px solid black;border-left:1px solid black;'),
					array('data' => 'Jepara, ' . $sp2dtgl,'width' => '240px', 'align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => apbd_bud_jabatan(),'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;font-weight:bold;text-decoration:underline;'),
					array('data' => apbd_bud_nama(),'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'NIP. ' . apbd_bud_nip(),'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);	
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}	

	
function printsp2d2($dokid) {

	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$sp2dno = $data->sp2dno;
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);

		$penerimanama = 'BENDAHARA PENGELUARAN ' . $data->namasingkat . ' (' . $data->penerimanama . ')';
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;

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
		
		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . substr($data->kodekeg, -3);		

	}		
	$nosp2d=str_replace("-", " ",$sp2dno );
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	
	$rows[]=array(
		array('data' => '', 'width' => '330px','align'=>'left','style'=>'border-top:1px solid black;'),
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border-top:1px solid black;'),
		array('data' => '', 'width' => '35px','align'=>'left','style'=>'font-size:150%;border-top:1px solid black;'),
		array('data' => $nosp2d, 'width' => '110px','align'=>'right','style'=>'border-top:1px solid black;font-size:140%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '480px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
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
		array('data' => 'SKPD', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		array('data' => $namauk, 'width' => '420px','align'=>'left','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
		//array('data' => '', 'width' => '80px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '50px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'BANK JATENG CABANG JEPARA', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => $dokid, 'width' => '45px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Hendaklah mencairkan/memindahbukukan dari RKUD nomor : 1.015.03256.5', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'width' => '480px','align'=>'left','style'=>'border-bottom:0.1px solid black;border-right:1px solid black;border-left:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Kepada', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanpwp, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No. Rekening Bank', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabankrekening, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabanknama, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan', 'width' => '100px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '370px','align'=>'left','style'=>'border:none;border-right:1px solid black;'),
		
	);
	
	//REKENING
	$rows[]=array(
		array('data' => 'RINCIAN REKENING', 'width' => '480px','align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black'),
		array('data' => ' Kode Rekening', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah (Rp)', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	//baris pertama
	$rows[]=array(
		array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black'),
		array('data' => $nomorkeg . '...', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
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
	foreach ($results as $data) {
		$n++;
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '... ' . apbd_format_rek_rincianobyek($data->kodero), 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}
		

	
	$rows[]=array(
		array('data' => 'Jumlah yang Diminta (1)', 'width' => '390px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;border-left:1px solid black'),
	);
	
	//POTONGAN..........
	$rows[]=array(
		array('data' => 'POTONGAN - POTONGAN', 'width' => '480px','align'=>'center','style'=>'border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
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
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}		
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:!px solid black;border-right:1px solid black;'),
			array('data' => 'Tidak ada potongan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}
	$rows[]=array(
		array('data' => 'Jumlah Potongan (2)', 'width' => '300px','align'=>'right','style'=>'border-left:!px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//PAJAK .....................
	$rows[]=array(
		array('data' => 'PAJAK(TIDAK MENGURANGI JUMLAH SP2D)', 'width' => '480px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
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
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black'),
			array('data' => 'Tidak ada pajak', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}	
	$rows[]=array(
		array('data' => 'Jumlah Pajak (3)', 'width' => '300px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => apbd_fn($totalpajak), 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-right:1px solid black;'),
	);
	
	//SP2D ............
	$rows[]=array(
		array('data' => 'SP2D YANG DIBAYARKAN', 'width' => '480px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Diminta (1)', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	
	$rows[]=array(
			array('data' => ' Jumlah Potongan (2)', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Dibayarkan(1)-(2)', 'width' => '300px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;border-left:1px solid black;'),
			array('data' => $netto, 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Uang Sejumlah :', 'width' => '480px','align'=>'left','style'=>'border:none;border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'ZZ' . $terbilangnetto . 'ZZ', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '480px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[] = array(
					array('data' => '','width' => '608px', 'align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}	
function footer($dokid){

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('sp2dtgl'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);
	}
	$header=array();
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;border-left:1px solid black;'),
					array('data' => 'Jepara, ' . $sp2dtgl,'width' => '300px', 'align'=>'center','style'=>'border:none;border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => apbd_bud_jabatan(),'width' => '300px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-left:1px solid black;font-weight:bold;text-decoration:underline;'),
					array('data' => apbd_bud_nama(),'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-left:1px solid black;'),
					array('data' => apbd_bud_nip(),'width' => '240px', 'align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function footer2($dokid){

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('sp2dtgl'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);
	}
	$header=array();
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $sp2dtgl,'width' => '240px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border:none;'),
					array('data' => apbd_bud_jabatan(),'width' => '240px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '260px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '250px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => apbd_bud_nama(),'width' => '240px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '240px', 'align'=>'center','style'=>'border:none;'),
					array('data' => apbd_bud_nip(),'width' => '240px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

?>
