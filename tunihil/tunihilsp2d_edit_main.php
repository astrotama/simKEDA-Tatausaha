<?php
function tunihilsp2d_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$x = $_SERVER['HTTP_REFERER'];
	
	//drupal_set_message('abc : ' . $x);
	
	$dokid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = printsp2d($dokid);
		$output2 = footer($dokid);
		//print_pdf_p_sp2d($output,$output2);
		apbd_ExportSP2D($output,$output2,'SP2D');
		//return $output;
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
		$output_form = drupal_get_form('tunihilsp2d_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function tunihilsp2d_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["tunihilsp2dlastpage"] = $referer;
	else
		$referer = $_SESSION["tunihilsp2dlastpage"];
	
	$jenisdokumen = 3;
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
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->fields('d', array('dokid', 'spmno', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'pptknama', 'pptknip', 'sp2dok', 'jenisgaji'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SP2D Barang Jasa ' . $data->bulan ;
		
		$dokid = $data->dokid;
		$sp2dno = $data->sp2dno;
		
		if ($sp2dno != '') $nosudah = true;
		
		if (isSuperuser())
			if (is_null($data->sp2dtgl))
				$sp2dtgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
			else
				$sp2dtgl = strtotime($data->sp2dtgl);		
			
		else 
			if ($sp2dno == '') {
				$sp2dno = 'Belum Terbit';
				$sp2dtgl = $sp2dno;		
			} else {
				$sp2dtgl = apbd_fd_long($data->sp2dtgl);		
			}
		
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
		
	}
	
	drupal_set_title($title);

	
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
			$form['SP2D']['submitsoap']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Penguji',
				'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
			);				
			$form['SP2D']['submitprint']= array(
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
				$sp2dno = apbd_getnosp2d('4', '0');
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
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);			
	} else {
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		if($results){
			foreach($results as $data) {
			  $option_skpd[$data->kodeuk] = $data->namasingkat; 
			}
		}		
		$form['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#prefix' => '<div id="skpd-replace">',
			//'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodeuk,
		);	
	}
	
	$form['spmno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => $spmno,
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
		'#title'=> 'PENERIMA',
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
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0;
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'ada', 'tidakada'));
	$query->fields('lt', array('uraian'));
	$query->condition('dk.dokid', $dokid, '=');
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
			'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['adakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $ada, 
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		
		$form['formkelengkapan']['tablekelengkapan']['tidakadakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $tidakada, 
			'#prefix' => '<td>',
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
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">ANGGARAN</th><th width="50px">CAIR</th><th width="130px">BATAS</th><th width="130px">JUMLAH</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	
	$i = 0;
	$cair = 0;
	$query = db_select('dokumenrekening', 'di');
	$query->join('rincianobyek', 'ro', 'di.kodero=ro.kodero');
	$query->join('anggperkeg', 'a', 'di.kodero=a.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('di', array('jumlah'));
	$query->fields('a', array('anggaran'));
	$query->condition('di.dokid', $dokid, '=');
	$query->condition('di.jumlah', 0, '>');
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	
	//dpq ($query);
	
	$results = $query->execute();

	foreach ($results as $data) {
		$i++; 
		$kodero = $data->kodero;
		$uraian = $data->uraian;
		$jumlah = $data->jumlah;
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
				'#markup' => $kodero,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['anggaran' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['cair' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($cair) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['batas' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data->anggaran - $cair) . '</p>', 
			'#suffix' => '</td>',
		); 		
		$form['formrekening']['table']['jumlahapbd' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);			
	}	

	$form['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);

		


	$form['formpajak'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formpajak']['tablepajak']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="260px">KETERANGAN</th></tr>',
		 '#suffix' => '</table></div>',
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

	//PPTK
	$form['formpptk'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PPTK',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
		$form['formpptk']['pptknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $pptknama, 
		);				
		$form['formpptk']['pptknip']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NIP'),
			//'#required' => TRUE,
			'#default_value'=> $pptknip, 
		);	
	
	if (isSuperuser()) {	
		
		$form['formdata']['submitprint']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
		
		if ($sp2dok==0) {
			$form['formdata']['submitsp2dok']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
				'#attributes' => array('class' => array('btn btn-info btn-sm')),
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

function tunihilsp2d_edit_main_form_validate($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$sp2dno = $form_state['values']['sp2dno'];
		$e_sp2dno = $form_state['values']['e_sp2dno'];		

		if ($sp2dno != $e_sp2dno) {
			if (apbd_is_duplikasi($sp2dno)) form_set_error('sp2dno', 'Nomor SP2D sudah ada. Ganti dengan nomor lain, bisa diketik manual atau klik tombol Otomatis');
		}
	}	
}

function tunihilsp2d_edit_main_form_submit($form, &$form_state) {
$dokid = $form_state['values']['dokid'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
	drupal_goto('gusp2d/edit/' . $dokid . '/pdf');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submittutup']) {
	drupal_goto('gusp2darsip');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitauto']) {
	drupal_goto('gusp2d/edit/' . $dokid . '/auto');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap']) {
	$ret = soap_sp2d_add($dokid);

} else {	
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$spmno = $form_state['values']['spmno'];
	$sp2dno = $form_state['values']['sp2dno'];
	$sp2dtgl = $form_state['values']['sp2dtgl'];
	$sp2dtglsql = $sp2dtgl['year'] . '-' . $sp2dtgl['month'] . '-' . $sp2dtgl['day'];

	$keperluan = $form_state['values']['keperluan'];
	
	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	//PPTK
	$pptknama = $form_state['values']['pptknama'];
	$pptknip = $form_state['values']['pptknip'];

	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];

	//BEGIN TRANSACTION
	$transaction = db_transaction();
	
	//JURNAL
	try {	
		/*
		//KELENGKAPAN
		for ($n=1; $n <= $jumlahrekkelengkapan; $n++) {
			$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
			$ada = $form_state['values']['adakelengkapan' . $n];
			$tidakada = $form_state['values']['tidakadakelengkapan' . $n];
			
			$query = db_update('dokumenkelengkapan')
			->fields( 
					array(
						'ada' => $ada,
						'tidakada' => $tidakada,
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodekelengkapan', $kodekelengkapan, '=');
			$res = $query->execute();
			
		}

		//REKENING
		$totaljumlah = 0;
		for ($n=1; $n <= $jumlahrekrekening; $n++) {
			$kodero = $form_state['values']['koderoapbd' . $n];
			$jumlah = $form_state['values']['jumlahapbd' . $n];
			
			$query = db_update('dokumenrekening')
			->fields( 
					array(
						'jumlah' => $jumlah,
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodero', $kodero, '=');
			$res = $query->execute();
			
			$totaljumlah = $totaljumlah + $jumlah;
			
		}
		
		//POTONGAN
		$totalpotongan = 0;
		for ($n=1; $n <= $jumlahrekpotongan; $n++) {
			$kodepotongan = $form_state['values']['kodepotongan' . $n];
			$jumlah = $form_state['values']['jumlahpotongan' . $n];
			$keterangan = $form_state['values']['keteranganpotongan' . $n];
			
			$query = db_update('dokumenpotongan')
			->fields( 
					array(
						'jumlah' => $jumlah,
						'keterangan' => $keterangan, 
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodepotongan', $kodepotongan, '=');
			$res = $query->execute();
			
			$totalpotongan = $totalpotongan + $jumlah;
			
		}		

		//PNS
		for ($n=1; $n <= 4; $n++){
			$jumlahpnspns = $form_state['values']['jumlahpnspns' . $n];
			$jumlahpnsistri = $form_state['values']['jumlahpnsistri' . $n];
			$jumlahpnsanak = $form_state['values']['jumlahpnsanak' . $n];
			
			$query = db_update('dokumenpns')
			->fields( 
					array(
						'pns' => $jumlahpnspns,
						'istri' => $jumlahpnsistri,
						'anak' => $jumlahpnsanak,
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('golongan', $n, '=');
			$res = $query->execute();
			
		}
		
		*/
		
		//DOKUMEN
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dok']) {

			$query = db_update('dokumen')
					->fields( 
					array(
						'keperluan' => $keperluan,
						//'spmno' => $spmno,
						//'sp2dno' =>$sp2dno,
						//'sp2dtgl' =>$sp2dtglsql,
						//'jumlah' => $totaljumlah,
						//'potongan' => $totalpotongan,
						//'netto' => $totaljumlah - $totalpotongan,
						
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
						//'spmno' => $spmno,
						//'sp2dno' =>$sp2dno,
						//'sp2dtgl' =>$sp2dtglsql,
						//'jumlah' => $totaljumlah,
						//'potongan' => $totalpotongan,
						//'netto' => $totaljumlah - $totalpotongan,
						
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
						'spmno' => $spmno,
						'sp2dno' =>$sp2dno,
						'sp2dtgl' =>$sp2dtglsql,
						//'jumlah' => $totaljumlah,
						//'potongan' => $totalpotongan,
						//'netto' => $totaljumlah - $totalpotongan,
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
		atchdog_exception('tunihilsp2d-' . $nourut, $e);
	}
	//if ($res) drupal_goto('kaskeluarantrian');
	//drupal_goto('tunihilsp2darsip');
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
	);
	$rows[]=array(
		array('data' => '', 'width' => '330px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '25px','align'=>'left','style'=>'font-size:150%;border:none;'),
		array('data' => $sp2dno, 'width' => '70px','align'=>'right','style'=>'border:none;'),
	);
	
	$rows[]=array(
		array('data' => '', 'width' => '480px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Nomor SPM', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $spmno, 'width' => '280px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Dari', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'Kuasa BUD', 'width' => '50px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $spmtgl, 'width' => '280px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tahun Anggaran', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => apbd_tahun(), 'width' => '50px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		array('data' => $namauk, 'width' => '420px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '80px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => 'BANK JATENG CABANG JEPARA', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => $dokid, 'width' => '45px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Hendaklah mencairkan/memindahbukukan dari RKUD nomor : 1.015.03256.5', 'width' => '480px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'width' => '480px','align'=>'left','style'=>'border-bottom:0.1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Kepada', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanama, 'width' => '370px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimanpwp, 'width' => '370px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'No. Rekening Bank', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabankrekening, 'width' => '370px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
		//array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		//array('data' => '', 'width' => '138px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Bank/Pos', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $penerimabanknama, 'width' => '370px','align'=>'left','style'=>'border:none;'),
		
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none;'),
		array('data' => $keperluan, 'width' => '370px','align'=>'left','style'=>'border:none;'),
		
	);
	
	//REKENING
	$rows[]=array(
		array('data' => 'RINCIAN REKENING', 'width' => '480px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Kode Rekening', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah (Rp)', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
	);
	
	//baris pertama
	$rows[]=array(
		array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => $nomorkeg . '...', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
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
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => '... ' . apbd_format_rek_rincianobyek($data->kodero), 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>''),
		);
	}
		

	
	$rows[]=array(
		array('data' => 'Jumlah yang Diminta (1)', 'width' => '390px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-bottom:1px solid black;border-top:1px solid black;'),
	);
	
	//POTONGAN..........
	$rows[]=array(
		array('data' => 'POTONGAN - POTONGAN', 'width' => '480px','align'=>'center','style'=>'border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
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
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '90px','align'=>'left','style'=>''),
		);
	}		
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => 'Tidak ada potongan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>''),
		);
	}
	$rows[]=array(
		array('data' => 'Jumlah Potongan (2)', 'width' => '300px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;'),
	);
	
	//PAJAK .....................
	$rows[]=array(
		array('data' => 'PAJAK(TIDAK MENGURANGI JUMLAH SP2D)', 'width' => '480px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '270px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Keterangan', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
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
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => $data->uraian, 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '90px','align'=>'left','style'=>''),
		);
	}
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
			array('data' => 'Tidak ada pajak', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>''),
		);
	}	
	$rows[]=array(
		array('data' => 'Jumlah Pajak (3)', 'width' => '300px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => apbd_fn($totalpajak), 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;'),
	);
	
	//SP2D ............
	$rows[]=array(
		array('data' => 'SP2D YANG DIBAYARKAN', 'width' => '480px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold'),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Diminta (1)', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>''),
	);
	
	$rows[]=array(
			array('data' => ' Jumlah Potongan (2)', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>''),
	);
	$rows[]=array(
			array('data' => ' Jumlah yang Dibayarkan(1)-(2)', 'width' => '300px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => $netto, 'width' => '90px','align'=>'right','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
			array('data' => ' ', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'Uang Sejumlah :', 'width' => '480px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'ZZ' . $terbilangnetto . 'ZZ', 'width' => '480px','align'=>'left','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '480px', 'align'=>'center','style'=>'border:none;'),
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
