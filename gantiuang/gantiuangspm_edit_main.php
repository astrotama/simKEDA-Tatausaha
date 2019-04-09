<?php
function gantiuangspm_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$dokid = arg(2);	
	//drupal_set_message($dokid);
	if (arg(3)=='pdf-e') {			
		 
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
	
		$kodeuk = '00'; 
		$output = printspm($dokid, $kodeuk); 
		
		if ($kodeuk=='') $kodeuk = substr($dokid,0,2);
		if ($kodeuk=='02') $kodeuk = '03';
		$fname = $kodeuk . '/'. str_replace('/', '_', 'SPM_' . $dokid . '.PDF');
		
		//drupal_set_message($fname);
		
		apbd_ExportSPM_File($output, $url, $fname);		
		drupal_goto('files/spm/' . $fname);
		
	} else if(arg(3)=='pdf'){		
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$kodeuk = '00';
		$output = printspm($dokid, $kodeuk);
		apbd_ExportSPM($output, 'SPM_' . $dokid . '.PDF', $url);
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('gantiuangspm_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function gantiuangspm_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spmgulastpage"] = $referer;
	else
		$referer = $_SESSION["spmgulastpage"];
	
	$jenisdokumen = 4;
	$kodeuk = '';
	$kodekeg = '';
	$kegiatan = '';
	$jenisbelanja = 1;
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('m');
	$spmtgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'SPM GU ' . apbd_tahun();
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
	$cetakspm = 0;
	
	$dokid = arg(2);
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->fields('d', array('dokid', 'sppno', 'spmno', 'spmtgl', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'pptknama', 'pptknip', 'sp2dok', 'spmok', 'cetakspm'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	foreach ($results as $data) {
		
		$spmok = $data->spmok;
		$sp2dok = $data->sp2dok;
		
		
		$dokid = $data->dokid;
		$spmno = $data->spmno;

		if (is_null($data->spmtgl)) {
			$tanggalsql = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
		} else {
			$tanggalsql = $data->spmtgl;
		}		
		
		$spmtgl = dateapi_convert_timestamp_to_datetime($tanggalsql);
		
		$sppno = $data->sppno . ', ' . apbd_fd_long($data->spptgl);
		
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
		$cetakspm = $data->cetakspm;
		
		
	}
	
	drupal_set_title('SPM GU - ' . $keperluan);
	
	//CETAK ATAS
	if ($spmok=='1') {
		if (isAdministrator()) {
			$form['formcetak']['submitprint-submit']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span>Submit',
				'#attributes' => array('class' => array('btn btn-warning btn-sm pull-right')),
			);
			$form['formcetak']['submitprint-reset']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span>Reset',
				'#attributes' => array('class' => array('btn btn-warning btn-sm pull-right')),
			);
		}		
		if ($cetakspm=='0') {
			$form['formcetak']['submitprint-e']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span>eSPM',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);
		} else {
			$form['formcetak']['submitprint-s']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span>eSPM',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);
		}

	}
	$form['formcetak']['submitprint']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);

	//SKPD
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	
	$form['spmno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Nomor SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => $spmno,
	);
	/*
	$form['spmtgl'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $spmtgl,
		'#default_value'=> array(
			'year' => format_date($spmtgl, 'custom', 'Y'),
			'month' => format_date($spmtgl, 'custom', 'n'), 
			'day' => format_date($spmtgl, 'custom', 'j'), 
		  ), 
		
	);*/
	
		$form['spmtgl_title'] = array(
		'#markup' => 'Tanggal SPM',
		);
		$form['spmtgl']= array(
		'#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
		'#default_value' => $spmtgl, 
			
		//'#default_value'=> array(
		//	'year' => format_date($TANGGAL, 'custom', 'Y'),
		//	'month' => format_date($TANGGAL, 'custom', 'n'), 
		//	'day' => format_date($TANGGAL, 'custom', 'j'), 
		// ), 
	 
		'#date_format' => 'd-m-Y',
		'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		//'#description' => 'Tanggal',
		);

	$form['sppno'] = array(
		'#type' => 'item',
		'#title' =>  t('Nomor dan Tanggal SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#markup' => '<p>' . $sppno . '</p>',
	);
	
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
	$form['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $bulan,
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
	$lewatbatas = false;
	
	//drupal_set_message($kodekeg);
	//drupal_set_message($dokid);
	//drupal_set_message($tanggalsql);
	
	$results = db_query('select r.kodero, r.uraian, a.anggaran, di.jumlah from {rincianobyek} r inner join {dokumenrekening} di on r.kodero=di.kodero inner join {anggperkeg} a on di.kodero=a.kodero where di.dokid=:dokid and di.jumlah>0 and a.kodekeg=:kodekeg', array(':dokid'=>$dokid, ':kodekeg'=>$kodekeg));
	foreach ($results as $data) {
		$i++; 
		$kodero = $data->kodero;
		$uraian =  l($data->uraian, 'laporan/realisasikegsp2d/filter/' . $kodekeg . '/' . $data->kodero . '/' . $tanggalsql, array('attributes' => array('class' => null)));
		$jumlah = $data->jumlah;

		$cair = apbd_readrealisasikegiatan_rekening($dokid, $kodekeg, $data->kodero, $tanggalsql);
		//$cair = 0;
		$batas = $data->anggaran - $cair;
		
		$lewatbatas = $lewatbatas or ($batas<0);
		
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
		$form['formrekening']['table']['jumlahapbdstr' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $strjumlah, 
			'#suffix' => '</td>',
		);			
		$form['formrekening']['table']['jumlahsisastr' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $strsaldo, 
			'#suffix' => '</td></tr>',
		);			
	}	

	
	$form['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);



	//PPTK
	/*
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
		);	729

	*/
	
	//FORM SIMPAN ENABLE
	$disable_simpan = TRUE;	
	if (apbd_gutu_aktif()) {	
		if ($spmok=='0') {
			$form['formdata']['submitspmok']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Verifikasi',
				'#disabled' => $lewatbatas,
				'#attributes' => array('class' => array('btn btn-info btn-sm')),
			);
			$disable_simpan = FALSE;
			
		} elseif (($spmok=='1') and ($sp2dok=='0') and (isSuperuser())) {	
			$form['formdata']['submitspmnotok']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> Batalkan',
				'#attributes' => array('class' => array('btn btn-danger btn-sm')),
			);
		}
	}
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	
	//CETAK BAWAH
	if ($spmok=='1') {
		if ($cetakspm=='0') {
			$form['formdata']['submitprint-e']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span>eSPM',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);
		} else {
			$form['formcetak']['submitprint-s']= array(
				'#type' => 'submit',
				'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span>eSPM',
				'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
			);
		}				
	}
	
	$form['formdata']['submitprint']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}

function gantiuangspm_edit_main_form_validate($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitspmok']) {
		///if (isAdministrator()) {
		//	drupal_set_message('x');
		//}
		
		//CEK NOMOR SPM
		$spmno = $form_state['values']['spmno'];
		if ($spmno == '') {
			form_set_error('spmno', 'Nomor SPM belum diisikan');
		}		

		//CEK KEPERLUAN
		$keperluan = $form_state['values']['keperluan'];
		if ($keperluan == '') {
			form_set_error('keperluan', 'Keperluan SPM belum diisikan');
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
	
function gantiuangspm_edit_main_form_submit($form, &$form_state) {
$dokid = $form_state['values']['dokid'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
	drupal_goto('guspm/edit/' . $dokid . '/pdf');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint-e']) {
	//drupal_goto('http://simkedajepara.link/guspm/edit/' . $dokid . '/pdf-e');
	drupal_goto('guspm/edit/' . $dokid . '/pdf-e');
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint-s']) {
	drupal_goto(apbd_button_espm_link($dokid), array('external' => TRUE));
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspmnotok']) {
	drupal_goto('guspm/delete/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint-reset']) {

	$query = db_update('dokumen')
			->fields( 
			array(
				'cetakspm' => '0',

			)
		);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprint-submit']) {

	$query = db_update('dokumen')
			->fields( 
			array(
				'cetakspm' => '1',

			)
		);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();
	
} else {	
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$spmno = $form_state['values']['spmno'];
	//$spmtgl = $form_state['values']['spmtgl'];
	//$spmtglsql = $spmtgl['year'] . '-' . $spmtgl['month'] . '-' . $spmtgl['day'];

	$spmtglsql = dateapi_convert_timestamp_to_datetime($form_state['values']['spmtgl']);
	
	$keperluan = $form_state['values']['keperluan'];
	$bulan = $form_state['values']['bulan'];
	
	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	//PPTK
	$pptknama = $form_state['values']['pptknama'];
	$pptknip = $form_state['values']['pptknip'];

	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	//$jumlahrekpajak = $form_state['values']['jumlahrekpajak'];

	//BEGIN TRANSACTION
	$transaction = db_transaction();
	
	//JURNAL
	try {	


		//DOKUMEN
		$query = db_update('dokumen')
				->fields( 
				array(
					'keperluan' => $keperluan,
					
					'spmno' =>$spmno,
					'bulan' => $bulan,

					'spmtgl' =>$spmtglsql,

					'penerimanama' => $penerimanama,
					'penerimabanknama' => $penerimabanknama,
					'penerimabankrekening' => $penerimabankrekening,
					'penerimanpwp' => $penerimanpwp,
					//'pptknama' => $pptknama,
					//'pptknip' => $pptknip,							
					
				)
			);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();
		
	
	}
		catch (Exception $e) {
		$transaction->rollback();
		atchdog_exception('spmgu-' . $nourut, $e);
	}
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitspmok'])  drupal_goto('guspm/verify/' . $dokid);
	//drupal_goto('spmguarsip');
	//drupal_goto();
}	
}

function printspm($dokid, &$kodeuk) {

	$num_rek = 15; $num_pot = 7; $num_pajak = 2;
	
	$query = db_select('dokumen', 'd');
	$query->join('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	
	$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'sppno', 'spptgl', 'kodekeg', 'bulan', 'keperluan', 'jumlah', 'potongan', 'netto', 
			'penerimanama', 'penerimabankrekening', 'penerimabanknama', 'penerimanpwp', 'spdno', 'spmok'));
	$query->fields('uk', array('kodeuk', 'pimpinannama', 'kodedinas', 'namauk', 'namasingkat', 'header1', 'pimpinanjabatan', 'pimpinannip'));
	$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan', 'anggaran', 'tw1', 'tw2', 'tw3', 'tw4'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');

	$results = $query->execute();
	foreach ($results as $data) {
		$spmok = $data->spmok;
		
		$spmno = $data->spmno;
		$spmtgl = apbd_fd_long($data->spmtgl);
		
		$kodeuk = $data->kodeuk;
		
		$sppno = $data->sppno;
		$spptgl = apbd_fd_long($data->spptgl);

		$skpd = $data->namauk;
		$bendaharanama =  $data->penerimanama;
		$rekening = $data->penerimabanknama . ' No. Rek . ' . $data->penerimabankrekening;
		$npwp = $data->penerimanpwp;

		$spdno = '.................';
		$spdtgl = '.................';
		
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
		
		$spdkode = $data->spdno;

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
		$spdjumlah = apbd_fn($data->jumlah);
		$twaktif = $data->jumlah;
	}
	
	if ($spmno=='') {
		$spmno = '<strong style="color:red">BELUM ADA</strong>';
		$spmtgl = '<strong style="color:red">tanggal belum diisi</strong>';
	}
	$ttdwarning = '';
	if ($spmok==0) $ttdwarning = '<em style="color:red">*draft*draft*draft*draft*draft*</em>';
	
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'SURAT PERINTAH MEMBAYAR (SPM)', 'width' => '510px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '510px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => 'ID : ' . $dokid, 'width' => '255px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor SPM : ' . $spmno, 'width' => '255px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => $ttdwarning . '(Diisi oleh PPK-SKPD)' . $ttdwarning, 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'KUASA BENDAHARA UMUM DAERAH', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'KABUPATEN JEPARA', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Supaya menerbitkan SP2D kepada :', 'width' => '510px','align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'SKPD', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $skpd, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Penerima', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $bendaharanama, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Nomor Rekening Bank', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $rekening, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'NPWP', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $npwp, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Dasar Pengeluaran / No. SPD', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $spdno, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '10px','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => 'Tgl. SPD', 'width' => '140px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $spdtgl, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Untuk Keperluan', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => $keperluan, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);

	$strbelanja = '';
	if ($kodeuk=='00') $strbelanja = 'TIDAK ';
	
	$rows[]=array(
		array('data' => 'Jenis Belanja', 'width' => '150','align'=>'left','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border:none'),
		array('data' => 'BELANJA ' . $strbelanja . 'LANGSUNG', 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
	);
	
	
	//REKENING
	$rows[]=array(
		array('data' => 'PEMBEBANAN PADA REKENING', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Kode Rekening', 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Uraian', 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
		
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
		$num_rek--;
		$rows[]=array(
			array('data' => $n, 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $nomorkeg . '.' . $data->kodero, 'width' => '125px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => substr($data->uraian, 0, 48), 'width' => '270px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
		);
	}
	

	$rows[]=array(
		array('data' => 'Jumlah SPP yang Diminta', 'width' => '150px','align'=>'right','style'=>'border-left:1px solid black;border-top:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border-top:1px solid black;'),
		array('data' => $jumlah, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '150px','align'=>'right','style'=>'border-left:1px solid black;'),
		array('data' => '', 'width' => '10px','align'=>'center','style'=>''),
		array('data' => 'Terbilang : ' . $terbilang, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
		
		
	);
	$rows[]=array(
		array('data' => 'Nomor & Tanggal SPP', 'width' => '150px','align'=>'right','style'=>'border-left:1px solid black;'),
		array('data' => ':', 'width' => '10px','align'=>'center','style'=>''),
		array('data' => $sppno . ', tanggal ' . $spptgl, 'width' => '350px','align'=>'left','style'=>'border-right:1px solid black;'),
		
		
	);
	
	
	//POTONGAN
	$rows[]=array(
		array('data' => 'POTONGAN-POTONGAN', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Uraian', 'width' => '210px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Keterangan', 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
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
	$n = 0;
	foreach ($results as $data) {
		$n++;
		$num_pot--;
		$rows[]=array(
			array('data' => $n, 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $data->uraian, 'width' => '210px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}
	
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => 'Tidak ada potongan', 'width' => '210px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		);		
	}
	
	$rows[]=array(
			array('data' => 'Jumlah Potongan', 'width' => '235px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
			array('data' => $potongan, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);
	
	$rows[]=array(
		array('data' => 'PAJAK (TIDAK MENGURANGI JUMLAH SPM)', 'width' => '510px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'No.', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Uraian', 'width' => '210px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Keterangan', 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		
	);
	$query = db_select('dokumenpajak', 'dp');
	$query->join('ltpajak', 'p', 'dp.kodepajak=p.kodepajak');
	$query->fields('p', array('kodepajak', 'uraian'));
	$query->fields('dp', array('jumlah', 'keterangan'));
	$query->condition('dp.dokid', $dokid, '=');
	$query->condition('dp.jumlah', 0, '>');
	$query->orderBy('dp.kodepajak', 'ASC');
	$results = $query->execute();
	$n = 0;
	$totalpajak =0;
	foreach ($results as $data) {
		$n++;
		$num_pajak--;
		$totalpajak += $data->jumlah;
		$rows[]=array(
			array('data' => $n, 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $data->uraian, 'width' => '210px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => $data->keterangan, 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}	
	/*
	for($n=1;$n<=2;$n++){
		$rows[]=array(
			array('data' => $n, 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '', 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '100px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '220px','align'=>'left','style'=>'border-right:1px solid black;'),
		);
	}
	*/
	if ($n==0) {
		$rows[]=array(
			array('data' => '', 'width' => '25px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;'),
			array('data' => 'Tidak ada pajak', 'width' => '210px','align'=>'left','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;'),
			array('data' => '', 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;'),
		);		
	}	
	$rows[]=array(
			array('data' => 'Jumlah Pajak', 'width' => '235px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
			array('data' => apbd_fn($totalpajak), 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '', 'width' => '185px','align'=>'left','style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);

	$rows[]=array(
		array('data' => 'JUMLAH SPM', 'width' => '420px','align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => $netto, 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);
	
	$rows[]=array(
			array('data' => 'Uang Sejumlah', 'width' => '90px','align'=>'right','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => ':', 'width' => '10px','align'=>'center','style'=>'border-bottom:1px solid black;'),
			array('data' => $terbilangnetto, 'width' => '410px','align'=>'left','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		);

	$batas = $num_pajak+$num_pot+$num_rek;
	for ($i=0; $i<=$batas; $i++) { 	
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
				);
	}
	
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, ' . $spmtgl ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $ttdwarning,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $ttdwarning,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $ttdwarning,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $ttdwarning,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $ttdwarning,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);
	$rows[]=array(
		array('data' => 'SPM INI SAH APABILA DITANDATANGANI OLEH ' . $pimpinanjabatan, 'width' => '510px','align'=>'center','style'=>'border:1px solid black;'),
	);		
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}


?>
