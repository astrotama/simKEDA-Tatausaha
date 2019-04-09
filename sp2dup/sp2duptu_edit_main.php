<?php
function sp2duptu_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$x = $_SERVER['HTTP_REFERER'];
	
	//drupal_set_message('abc : ' . $x);
	
	$dokid = arg(2);	
	if (arg(3)=='pdf1'){
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);

		
		$output = printsp2d_digital($dokid);

		$fname = str_replace('/', '_', 'SP2D_' . $dokid . '.PDF');
		
		apbd_ExportSP2D_Lengkap($output, $url, $fname);
		drupal_goto('files/sp2d/' . $fname);	
		/*$output = printsp2d2($dokid);
		$output2 = footer2($dokid);
		//print_pdf_p_sp2d($output,$output2);
		apbd_ExportSP2D($output,$output2,'SP2D');*/
		//return $output;
		
	} else if (arg(3)=='pdf0'){
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
		$output_form = drupal_get_form('sp2duptu_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function sp2duptu_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["sp2duptulastpage"] = $referer;
	else
		$referer = $_SESSION["sp2duptulastpage"];
	
	$jenisdokumen = 0;
	$kodeuk = '';
	$kegiatan = '';
	$jenisbelanja = 1;
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('m');
	$spmtgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'SPM-TU Tahun ' . apbd_tahun();
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

	
	$dokid = arg(2);
	/*
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->fields('d', array('dokid', 'spmno', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'pptknama', 'pptknip', 'sp2dok', 'spmok', 'jenisdokumen'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	*/
	
	$results = db_query('select d.dokid, d.spmno, d.sp2dno, d.sp2dtgl, d.kodekeg, d.bulan, d.keperluan, d.jumlah, d.potongan, d.netto, d.penerimanama, d.penerimabankrekening, d.penerimabanknama, d.penerimanpwp, d.pptknama, d.pptknip, d.sp2dok, d.spmok, d.jenisdokumen, u.kodeuk, u.namasingkat, d.kodebank, d.spjlink from {dokumen} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where d.dokid=:dokid', array(':dokid' => $dokid));
	foreach ($results as $data) {
		
		$sp2dok = $data->sp2dok;
		
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


		$jenisdokumen = $data->jenisdokumen;
		
		$spmno = $data->spmno;	
		
		$bulan = $data->bulan;
		
		$kodeuk = $data->kodeuk;

		$keperluan = $data->keperluan;
		$jumlah = $data->jumlah;
		
		$penerimanama = $data->penerimanama;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;
		
		$kodebank = $data->kodebank;
		$adagambar = strlen($data->spjlink);

		
	}
	
	if ($jenisdokumen==0)
		$title = 'SP2D-UP Tahun ' . apbd_tahun();
	else
		$title = 'SP2D-TU Tahun ' . apbd_tahun();
	
	drupal_set_title($title);

	//CETAK ATAS	
	$form['id']= array(
		'#markup' => 'ID: <strong>' . $dokid . '</strong>',
	);
	$form['formcetak']['submitgambar']= array(
		'#type' => 'item',
		'#markup' => apbd_button_image('upload/edit/' . $dokid, $adagambar),
	);

	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['jenisdokumen'] = array(
		'#type' => 'value',
		'#value' => $jenisdokumen,
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
			$form['SP2D']['submitprint']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);				
			
		} else {

			$form['SP2D'] = array (
				'#type' => 'fieldset',
				'#title'=> 'SP2D<em class="text-info pull-right">Nomor Terakhir : ' . apbd_getmaxnosp2d($jenisdokumen, '0') . '</em>',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);	
		
			if (arg(3)=='auto') {
				$sp2dno = apbd_getnosp2d($jenisdokumen, '0');
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
				$form['SP2D']['submitauto']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Otomatis',
					'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
				);
				$form['SP2D']['submit']= array(
					'#type' => 'submit',
					'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
					'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
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
	$form['jumlah'] = array(
		'#type' => 'item',
		'#title' =>  t('Jumlah'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p class="text-right">' . apbd_fn($jumlah) . '</p>' ,
	);

	//'pimpinannama', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'BENDAHARA',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
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
		
		$option_bank[''] = 'ISIKAN KODE BANK';
		$results = db_query('select kodebank, namabank from {bank} order by nourut, namabank');
		# build the table fields
		if($results){
			foreach($results as $data) {
				$option_bank[$data->kodebank] = $data->kodebank . ' - ' . $data->namabank; 
			}
		}		
		$form['formpenerima']['kodebank'] = array(
			'#type' => 'select',
			'#title' =>  t('Kode Bank'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#prefix' => '<div id="skpd-replace">',
			//'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_bank,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodebank,
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
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th>URAIAN</th></tr>',
		 '#suffix' => '</table>',
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




	if (isSuperuser()) {	
		
		$form['formdata']['submitprint']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
		
		if ($sp2dok==0) {
			$form['formdata']['ket'] = array(
			'#type' => 'item',
			//'#title' =>  t('Keterangan'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			//'#required' => TRUE,
			'#markup' => '<p style="color:red;">espm tidak tersedia</p>',
			);
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

function sp2duptu_edit_main_form_validate($form, &$form_state) {
	$kodebank = $form_state['values']['kodebank'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$sp2dno = $form_state['values']['sp2dno'];
		$e_sp2dno = $form_state['values']['e_sp2dno'];		
		if ($kodebank=='') {
			form_set_error('kodebank', 'Isikan Kode Bank');
		}
		if ($sp2dno != $e_sp2dno) {
			if (apbd_is_duplikasi($sp2dno)) form_set_error('sp2dno', 'Nomor SP2D sudah ada. Ganti dengan nomor lain, bisa diketik manual atau klik tombol Otomatis');
		}
	}	

}

function sp2duptu_edit_main_form_submit($form, &$form_state) {
$dokid = $form_state['values']['dokid'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
	drupal_goto('sp2duptu/edit/' . $dokid . '/pdf');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submittutup']) {
	drupal_goto('sp2duptuarsip');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitauto']) {
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	drupal_goto('sp2duptu/edit/' . $dokid . '/auto/' . $jenisdokumen );

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap']) {
	$ret = soap_sp2d_add($dokid);
	
	//$ret = sendrest($dokid);
	drupal_set_message($ret);
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsp2dtolak']) {
	
	drupal_goto('tolak/edit/'. $dokid);
	
}else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsoap2']) {
	$ret = soap_enlisting($dokid);
	
	//$ret = sendrest($dokid);
	drupal_goto($ret);

} else {	
	$kodeuk = $form_state['values']['kodeuk'];
		
	$e_sp2dno = $form_state['values']['e_sp2dno'];
	$sp2dno = $form_state['values']['sp2dno'];
	$sp2dtgl = $form_state['values']['sp2dtgl'];
	$sp2dtglsql = $sp2dtgl['year'] . '-' . $sp2dtgl['month'] . '-' . $sp2dtgl['day'];

	$keperluan = $form_state['values']['keperluan'];
	
	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];
	$kodebank = $form_state['values']['kodebank'];


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
						
						'kodebank' => $kodebank,
						
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
						
						'kodebank' => $kodebank,
						
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
			
			//SOAP
			//if () $output = soap_sp2d_add($dokid);
		}	
	
	}
		catch (Exception $e) {
		$transaction->rollback();
		atchdog_exception('sp2duptu-' . $nourut, $e);
	}
	//if ($res) drupal_goto('kaskeluarantrian');
	//drupal_goto('sp2duptuarsip');
	//drupal_goto(drupal_get_destination());
}	
}

function printsp2d($dokid) {
	
	$str_tu = '';
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'jenisdokumen', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'spdno'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	
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
		
		$spdkode = $data->spdno;
		
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
		
		if ($data->jenisdokumen=='2') $str_tu = 'Tambahan ';
	}		

	//SPD
	$spdno = '................';
	$spdtgl = $spdno;
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah'));
	$query->condition('s.spdkode', $spdkode, '=');		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$spdno = $data->spdno;
		$spdtgl = $data->spdtgl;
	}	
	
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '50px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '70px','align'=>'left','style'=>'font-size:150%;border:none;'),
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
		array('data' => 'Bank/Pos : BANK JATENG CABANG JEPARA', 'width' => '608px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Hendaklah mencairkan/memindahbukukan dari RKUD nomor : 1.015.03256.5', 'width' => '608px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'width' => '608px','align'=>'left','style'=>'border-bottom:0.1px solid black;'),
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
		array('data' => ' Kode Rekening', 'width' => '110px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Uraian', 'width' => '368px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => ' Jumlah (Rp)', 'width' => '90px','align'=>'left','style'=>'border-top:1px solid black;border-bottom:1px solid black;'),
	);
	
	//baris pertama
	$rows[]=array(
		array('data' => $n, 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => $nomorkeg . '...', 'width' => '110px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '368px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>''),
	);
	

	$rows[]=array(
		array('data' => '1', 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => '... 520.00.000', 'width' => '110px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => $str_tu . 'Uang Persediaan', 'width' => '368px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>''),
	);
		

	
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
	$rows[]=array(
		array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => 'Tidak ada potongan', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '138px','align'=>'left','style'=>''),
	);
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
	$rows[]=array(
		array('data' => '', 'width' => '40px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => 'Tidak ada pajak', 'width' => '300px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '130px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '138px','align'=>'left','style'=>''),
	);
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


function printsp2d2($dokid) {
	
	$str_tu = '';
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	
	$query->fields('d', array('dokid', 'jenisdokumen', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'spdno'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	
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
		
		$spdkode = $data->spdno;
		
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
		
		if ($data->jenisdokumen=='2') $str_tu = 'Tambahan ';
	}		

	//SPD
	$spdno = '................';
	$spdtgl = $spdno;
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah'));
	$query->condition('s.spdkode', $spdkode, '=');		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$spdno = $data->spdno;
		$spdtgl = $data->spdtgl;
	}	
	
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	
	$rows[]=array(
		array('data' => '', 'width' => '330px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '70px','align'=>'left','style'=>'font-size:150%;border:none;'),
		array('data' => $sp2dno, 'width' => '70px','align'=>'right','style'=>'border:none;font-size:140%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '480px','align'=>'left','style'=>'border:none;'),
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
	

	$rows[]=array(
		array('data' => '1', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => '... 520.00.000', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => $str_tu . 'Uang Persediaan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>''),
	);
		

	
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
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => 'Tidak ada potongan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>''),
	);
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
	$rows[]=array(
		array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-right:1px solid black;'),
		array('data' => 'Tidak ada pajak', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '90px','align'=>'left','style'=>''),
	);
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
	
	$query->fields('d', array('dokid', 'jenisdokumen', 'spmno', 'spmtgl', 'sp2dno', 'sp2dtgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'spdno'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$sp2dno = $data->sp2dno;
		$sp2dtgl = apbd_fd_long($data->sp2dtgl);

		$penerimanama = 'BENDAHARA PENGELUARAN ' . $data->namasingkat . ' (' . $data->penerimanama . ')';
		$penerimanama=$data->penerimanama;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;
		
		$spdkode = $data->spdno;
		
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
		
		if ($data->jenisdokumen=='2') $str_tu = 'Tambahan ';
	}		

	//SPD
	$spdno = '................';
	$spdtgl = $spdno;
	$query = db_select('spd', 's');
	$query->fields('s', array('spdkode', 'spdno', 'spdtgl', 'jumlah'));
	$query->condition('s.spdkode', $spdkode, '=');		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		$spdno = $data->spdno;
		$spdtgl = $data->spdtgl;
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
		array('data' => $namauk, 'width' => '420px','align'=>'left','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),
		//array('data' => '', 'width' => '80px','align'=>'left','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
		//array('data' => '', 'width' => '50px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$minkep=0;
	if(strlen($namauk)>65){
		$minkep+=1;
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
	$rows[]=array(
		array('data' => 'Uang sebesar : Rp ' . $jumlah . ' (' . $terbilang .  ')', 'width' => '480px','align'=>'left','style'=>'border-left:1px solid black;border-bottom:0.1px solid black;border-right:1px solid black;'),
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
		array('data' => $keperluan, 'width' => '370px','align'=>'left','style'=>'border-right:1px solid black;'),
		
	);
	
	if(strlen($keperluan)>65){
		$minkep+=1;
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
	

	$rows[]=array(
		array('data' => '1', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '... 520.00.000', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => $str_tu . 'Uang Persediaan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => $jumlah, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
	);
	for($a=($minkep+$n+$tmp);$a<16;$a++){
		$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}
	$overrek=0;
	if(($n+$tmp)>16)	
		$overrek=($n+$tmp)-15;		

	
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
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Tidak ada potongan', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);$n=1;
	}
	for($a=$n;$a<(6-$overrek);$a++){
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
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Tidak ada pajak', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}	
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
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, ' . $sp2dtgl,'width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => apbd_bud_jabatan(),'width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
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
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => apbd_bud_nama(),'width' => '300px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '350px', 'align'=>'center','style'=>'border:none;'),
					array('data' => apbd_bud_nip(),'width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function sendsoap_enlisting($dokid) {
	
	$results = db_query('select d.sp2dno, d.sp2dtgl, d.kodebank, d.jumlah, d.penerimanama, d.penerimabankrekening, u.kodeuk, u.namauk, d.kodebank from {dokumen} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where d.dokid=:dokid', array(':dokid' => $dokid));
	foreach ($results as $data) {  
		
		if ($data->kodeuk=='10')
			$idbilling = '20S0' . substr($data->sp2dno, 1,4);	
		else
			$idbilling = '20' . $data->kodeuk . substr($data->sp2dno, 1,4);
		
		$tanggal = date("Ydm", strtotime($data->sp2dtgl));
		
		$data = urlencode('{"nominal":"' . $data->jumlah . '","no_sp2d":"' . $data->sp2dno . '","penerima":"' . $data->penerimanama . '","rekening":"' . $data->penerimabankrekening . '","kode":"20","waktu":"' . date('His')	 . '","kode_bank":"' . $data->kodebank . '","id_billing":"' . $idbilling . '","skpd":"' . $data->namauk . '","tanggal":"' . $tanggal . '"}');
		
	}
	
	//$data = urlencode('{"nominal":"53050000","no_sp2d":"01761/LS","penerima":"BEND1 PENGEL SETDA JEPARA","rekening":"1015034204","kode":"09","waktu":"080200","kode_bank":"113","id_billing":"20102233","skpd":"SEKRETARIAT DAERAH","tanggal":"20170410"}');

	$data = str_replace('+', '%20', $data);
	$uri = 'http://30.86.30.33:82/sp2d/get/' . $data;
	
	$ret_json = file_get_contents($uri);
	$ret_array = json_decode($ret_json, true);
	
	drupal_set_message($idbilling);
	drupal_set_message($tanggal);
	drupal_set_message($uri);

/*
  //retreive and parse the json from URL.
  $request = drupal_http_request($uri);
  $decoded_json = drupal_json_decode($request->data);


  //get the array position needed for retrieve the 'data' element.
  $data = $decoded_json['recordSet']['resp_code'];


  //loop through the json data and add it to the $output array. 
  foreach($data as $info) {

    //Format the output with the info array you just created.

    //First item in the json data output
    $output .= $info[0];
  
    //Second item in the json data output
    $output .= $info[1];

  }
 */ 
  return $uri;
}

?>
