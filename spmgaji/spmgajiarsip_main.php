<?php
function spmgajiarsip_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$kodeuk = arg(2);
				$bulan = arg(3);
				$spmok = arg(4);
				$jenisgaji = arg(5);
				

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			if(!isset($_SESSION["spmgajiarsip_kodeuk"]))
				$_SESSION["spmgajiarsip_kodeuk"]='ZZ';
			$kodeuk = $_SESSION["spmgajiarsip_kodeuk"];
			//if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		
		if(!isset($_SESSION["spmgajiarsip_bulan"]))
				$_SESSION["spmgajiarsip_bulan"]=date('m');
		$bulan = $_SESSION["spmgajiarsip_bulan"];
		//if ($bulan=='') $bulan = date('m');
		
		if(!isset($_SESSION["spmgajiarsip_spmok"]))
				$_SESSION["spmgajiarsip_spmok"]='ZZ';
		$spmok = $_SESSION["spmgajiarsip_spmok"];
		//if ($spmok=='') $spmok = 'ZZ';

		if(!isset($_SESSION["spmgajiarsip_jenisgaji"]))
				$_SESSION["spmgajiarsip_jenisgaji"]='ZZ';
		$jenisgaji = $_SESSION["spmgajiarsip_jenisgaji"];
		//if ($jenisgaji=='') $jenisgaji = 'ZZ';
		
	}
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('spmgajiarsip_main_form');
	if (isSuperuser())
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px', 'field'=> 'spmtgl', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Jenis', 'field'=> 'jenisgaji', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Bruto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Potongan', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Netto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px', 'field'=> 'spmtgl', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Jenis', 'field'=> 'jenisgaji', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Bruto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Potongan', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Netto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		);
		

	$query = db_select('dokumen', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database 
	$query->fields('d', array('dokid', 'bulan', 'keperluan', 'kodeuk', 'sppno', 'spmno', 'spmtgl', 'sp2dno', 'sp2dok', 'jumlah', 'potongan', 'netto', 'spmok', 'sp2dok', 'jenisgaji', 'cetakspm'));
	$query->fields('u', array('namasingkat'));
	
	//GAJI
	$query->condition('d.jenisdokumen', 3, '=');
	
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	//if ($bulan !='0') $query->condition('d.bulan', $bulan, '=');
	if ($bulan!='0') {
		$or = db_or();
		$or->condition('d.bulan', $bulan, '=');
		$or->where('EXTRACT(MONTH FROM d.spmtgl) = :month', array('month' => $bulan));
		$or->where('EXTRACT(MONTH FROM d.spptgl) = :month', array('month' => $bulan));
		
		$query->condition($or);
	}
	
	if ($jenisgaji !='ZZ') $query->condition('d.jenisgaji', $jenisgaji, '=');

	
	$query->condition('d.sppok', '1', '=');
	if (($spmok =='0') or ($spmok =='1'))
		$query->condition('d.spmok', $spmok, '=');
	
	else if ($spmok =='2') {
		$query->condition('d.spmok', '1', '=');
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.sp2dsudah', '1', '=');
	} else if ($spmok =='3') {
		$query->condition('d.spmok', '3', '=');
	}

	
	$query->orderByHeader($header);
	$query->orderBy('u.namasingkat', 'ASC');
	$query->limit($limit);
	
	/*
	if (isAdministrator()) {
	dpq($query);
		
	}
	*/
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		
		//icon
		$ket_tolak = '';
		if($data->spmok=='1') {
			if ($data->sp2dok=='1')
				$proses = apbd_icon_valid();
			else
				$proses = apbd_icon_sudah();
		} else if ($data->spmok=='3') {
			$proses = apbd_icon_tolak();
			//tolak/edit/198100235
			
			//			$espm =  l('eSPM', 'http://simkedajepara.web.id/edoc2019/spm/E_SPM_' . $data->dokid . '.PDF', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-info btn-xs btn-block')));


			$res_tolak = db_query('select alasan1 from penolakan where dokid=:dokid', array(':dokid'=>$data->dokid));
			foreach ($res_tolak as $datolak) {
				$ket_tolak = l('<p style="color:red"><em><small>Ditolak karena : ' . $datolak->alasan1 . '...</small></em></p>', 'tolak/edit/' . $data->dokid, array ('html' => true));
;
			}
			
			
		} else {
			$proses = apbd_icon_belum();
		}

		if ($data->spmno=='') 
			$spmtgl = '';
		else
			$spmtgl = apbd_fd($data->spmtgl);


		$editlink = apbd_button_jurnal('spmgaji/edit/' . $data->dokid);

		//jenis gaji
		if ($data->jenisgaji=='0')
			$str_jenis = 'Reg';
		else if ($data->jenisgaji=='1')
			$str_jenis = 'Krg';
		else if ($data->jenisgaji=='2')
			$str_jenis = 'Sus';
		elseif ($data->jenisgaji=='3')
			$str_jenis = 'Ter';
		else
			$str_jenis = 'Tam';
		
		//if ($data->cetakspm=='0')
		if (!is_eSPMExists($data->dokid))
			$espmlink = '<p align="center">eSPM</p>'; 
		else
			$espmlink = apbd_button_espm($data->dokid);
		if (isSuperuser())
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),
						array('data' => $spmtgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $str_jenis, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan . $ket_tolak,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->potongan),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->netto),'align' => 'right', 'valign'=>'top'),
						$editlink,
						$espmlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',							
					);
		else
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),
						array('data' => $spmtgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $str_jenis, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan . $ket_tolak,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->potongan),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->netto),'align' => 'right', 'valign'=>'top'),
						$editlink,
						$espmlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',							
					);
			
	}
	
	
	//BUTTON
	/*
	$btn = apbd_button_print('');
	$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	*/
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $output;
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	
}

function spmgajiarsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$spmok = $form_state['values']['spmok'];
	$jenisgaji = $form_state['values']['jenisgaji'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/

	$_SESSION["spmgajiarsip_kodeuk"] = $kodeuk;
	$_SESSION["spmgajiarsip_bulan"] = $bulan;
	$_SESSION["spmgajiarsip_spmok"] = $spmok;
	$_SESSION["spmgajiarsip_jenisgaji"] = $jenisgaji;
	
	
	$uri = 'spmgajiarsip/filter/' . $kodeuk . '/' . $bulan . '/' . $spmok . '/' . $jenisgaji;
	drupal_goto($uri);
	
}


function spmgajiarsip_main_form($form, &$form_state) {
	
	/*
	$kodeuk = 'ZZ';
	//$bulan = date('m');
	$bulan = '1';
	$spmok = 'ZZ';
	*/
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$spmok = arg(4);
		$jenisgaji = arg(5);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["spmgajiarsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["spmgajiarsip_bulan"];
		if ($bulan=='') $bulan = date('m');
		
		$spmok = $_SESSION["spmgajiarsip_spmok"];
		if ($spmok=='') $spmok = 'ZZ';		

		$jenisgaji = $_SESSION["spmgajiarsip_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';		
		
	}
 
	//drupal_set_message($bulan);
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $jenisgaji, $spmok) . '</small></em>',		//'#attributes' => array('class' => array('container-inline')),
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
	
	//SKPD
	if (isUserSKPD()) {
		$form['formdata']['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);			
		$form['formdata']['namauk'] = array(
			'#type' => 'textfield',
			'#title' =>  t('SKPD'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',				
			'#default_value' => apbd_getuseruk_nama($kodeuk),
		);
		
	} else {	
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		$option_skpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $option_skpd[$data->kodeuk] = $data->namasingkat; 
			}
		}		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',
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
	
	//BULAN
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	//JENIS GAJI
	$opt_gaji['ZZ'] = 'SEMUA';
	$opt_gaji['0'] = 'REGULER';
	$opt_gaji['1'] = 'KEKURANGAN';	
	$opt_gaji['2'] = 'SUSULAN';	
	$opt_gaji['3'] = 'TERUSAN';	
	$opt_gaji['4'] = 'TAMSIL';	
	$form['formdata']['jenisgaji'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Gaji'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $opt_gaji,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisgaji,
	);	 
	
	//status
	$opt_spm['ZZ'] ='SEMUA';
	$opt_spm['0'] = 'BELUM VERIFIKASI';
	$opt_spm['1'] = 'SUDAH VERIFIKASI';	
	$opt_spm['2'] = 'SUDAH TERBIT SP2D';	
	$opt_spm['3'] = 'D I T O L A K';	
	$form['formdata']['spmok'] = array(
		'#type' => 'select',
		'#title' =>  t('Status'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $opt_spm,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $spmok,
	);	 

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}

function get_label_data($bulan, $jenisgaji, $status) {
if ($bulan=='0')
	$label = 'Setahun';
else 
	$label = 'Bulan ' . apbd_get_namabulan($bulan);

if ($jenisgaji=='ZZ')
	$label .= '/Semua gaji';
else if ($jenisgaji=='0')	
	$label .= '/Gaji reguler';
else if ($jenisgaji=='1')	
	$label .= '/Kekurangan gaji';
else if ($jenisgaji=='2')
	$label .= '/Gaji susulan';
else if ($jenisgaji=='3')	
	$label .= '/Gaji terusan';
else if ($jenisgaji=='4')	
	$label .= '/Tamsil';

if ($status=='ZZ')
	$label .= '/Semua SPM';
else if ($status=='0')	
	$label .= '/Belum verifikasi';
else if ($status=='1')	
	$label .= '/Sudah verifikasi';
else if ($status=='2')	
	$label .= '/Sudah SP2D';

$label .= ' (Klik disini untuk mengganti pilihan data)';
return $label;
}

?>