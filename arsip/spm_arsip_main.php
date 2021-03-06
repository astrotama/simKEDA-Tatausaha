<?php
function spm_arsip_main($arg=NULL, $nama=NULL) {
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
				$jenisdokumen = arg(5);
				$kata_kunci = arg(6);

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
			$kodeuk = $_SESSION["spm_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["spm_arsip_bulan"];
		if ($bulan=='') $bulan = date('m');
		
		$jenisdokumen = $_SESSION["spm_arsip_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = 'ZZ';

		$kata_kunci = $_SESSION["spm_arsip_katakunci"];
		$spmok = $_SESSION["spm_arsip_spmok"];
		if ($spmok=='') $spmok = 'ZZ';
	}
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('spm_arsip_main_form');
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
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Penerima', 'field'=> 'penerimanama', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
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
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Penerima', 'field'=> 'penerimanama', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);		

	$query = db_select('dokumen', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('dokid', 'jenisdokumen', 'bulan', 'keperluan', 'kodeuk', 'sppno', 'spmno', 'spmtgl', 'sp2dno', 'sp2dok', 'jumlah', 'spmok', 'sp2dok', 'penerimanama','cetakspm'));
	$query->fields('u', array('namasingkat'));
	
	//$query->condition('d.jenisdokumen', '4', '=');
	if ($jenisdokumen !='ZZ') {
		//$query->condition('d.jenisdokumen', $jenisdokumen, '=');
			$query->condition('d.jenisdokumen', $jenisdokumen, '=');
		
	}
	/*
	Jenis Dokumen
	$jns_dok['0'] = 'UP / TU';
	$jns_dok['1'] = 'GU';
	$jns_dok['3'] = 'GAJI';	
	$jns_dok['4'] = 'BARANG JASA';	
	$jns_dok['5'] = 'NIHIL';
	*/
	//UP TU	
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan!='0') {
		$or = db_or();
		$or->condition('d.bulan', $bulan, '=');
		$or->where('EXTRACT(MONTH FROM d.spmtgl) = :month', array('month' => $bulan));
		$or->where('EXTRACT(MONTH FROM d.spptgl) = :month', array('month' => $bulan));
		
		$query->condition($or);
	}
	
	$query->condition('d.sppok', '1', '=');
	if (($spmok =='0') or ($spmok =='1'))
		$query->condition('d.spmok', $spmok, '=');
	
	else if ($spmok =='2') {
		$query->condition('d.spmok', '1', '=');
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.sp2dsudah', '1', '=');
	}
	if ($kata_kunci!='') {
		$or = db_or();
		$or->condition('d.keperluan', '%' . db_like($kata_kunci) . '%', 'LIKE');
		$or->condition('d.penerimanama', '%' . db_like($kata_kunci) . '%', 'LIKE');
		
		$query->condition($or);
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
		if($data->spmok=='1')
			if ($data->sp2dok=='1')
				$proses = apbd_icon_valid();
			else
				$proses = apbd_icon_sudah();
		else
			$proses = apbd_icon_belum();
		

		if ($data->spmno=='') 
			$spmtgl = '';
		else
			$spmtgl = apbd_fd($data->spmtgl);
		
		$jns_dok['0'] = 'UP';
		$jns_dok['1'] = 'GU';
		$jns_dok['2'] = 'TU';
		$jns_dok['3'] = 'GAJI';
		$jns_dok['4'] = 'BARANG JASA';	
		$jns_dok['5'] = 'NIHIL';
		
		if ($data->jenisdokumen == '0'){
			$editlink = apbd_button_jurnal('spmup/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '1'){
			$editlink = apbd_button_jurnal('guspm/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '2'){
			$editlink = apbd_button_jurnal('spmtu/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '3'){
			$editlink = apbd_button_jurnal('spmgaji/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '4'){
			$editlink = apbd_button_jurnal('barangjasaspm/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '5'){
			$editlink = apbd_button_jurnal('spmnihil/edit/' . $data->dokid);
		}
		if ($data->cetakspm=='0')
			$espmlink = '<p align="center">eSPM</p>';
		else
			$espmlink = apbd_button_espm($data->dokid);
		
		if (isSuperuser())
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses ,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),
						array('data' => $spmtgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanama,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						$editlink,$espmlink,
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
						array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanama,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						$editlink,$espmlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);			
	}
	
	
	//BUTTON
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
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	
}

function spm_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$spmok = $form_state['values']['spmok'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$kata_kunci = $form_state['values']['kata_kunci'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/

	$_SESSION["spm_arsip_kodeuk"] = $kodeuk;
	$_SESSION["spm_arsip_bulan"] = $bulan;
	$_SESSION["spm_arsip_spmok"] = $spmok;
	$_SESSION["spm_arsip_jenisdokumen"] = $jenisdokumen;
	$_SESSION["spm_arsip_katakunci"] = $kata_kunci;
	
	
	$uri = 'spmarsip/filter/' . $kodeuk . '/' . $bulan . '/' . $spmok . '/' . $jenisdokumen . '/' . $kata_kunci ;
	drupal_goto($uri);
	
}


function spm_arsip_main_form($form, &$form_state) {
	
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

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["barangjasaspm_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["barangjasaspm_arsip_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$jenisdokumen = $_SESSION["barangjasaspm_arsip_jenisdokumen"];
		if ($jenisdokumen=='') $spmok = 'ZZ';		

		$spmok = $_SESSION["barangjasaspm_arsip_spmok"];
		if ($spmok=='') $spmok = 'ZZ';		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $spmok) . '</small></em>',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
	
	//SKPD
		if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['formdata']['kodeuk'] = array(
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
	$option_skpd['ZZ'] = 'SELURUH SKPD'; 
	if($results){
		foreach($results as $data) {
		  $option_skpd[$data->kodeuk] = $data->namasingkat; 
		}
	}		
	$form['formdata']['kodeuk'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		'#prefix' => '<div class="col-md-3">',
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
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	$opt_spm['ZZ'] ='SEMUA';
	$opt_spm['0'] = 'BELUM VERIFIKASI';
	$opt_spm['1'] = 'SUDAH VERIFIKASI';	
	$opt_spm['2'] = 'SUDAH TERBIT SP2D';	
	$form['formdata']['spmok'] = array(
		'#type' => 'select',
		'#title' =>  t('Status'),
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#options' => $opt_spm,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $spmok,
	);	 
	$jns_dok['ZZ'] ='SEMUA';
	$jns_dok['0'] = 'UP';
	$jns_dok['1'] = 'GU';
	$jns_dok['2'] = 'TU';
	$jns_dok['3'] = 'GAJI';	
	$jns_dok['4'] = 'BARANG JASA';	
	$jns_dok['5'] = 'NIHIL';	
	$form['formdata']['jenisdokumen'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Dokumen'),
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#options' => $jns_dok,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => '',
	);	
	
	$form['formdata']['kata_kunci'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Cari '),
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#attributes' => array('placeholder' => t('Cari Berdasarkan Kegiatan/penerima nama'),),
		'#default_value' => $kata_kunci,
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

function get_label_data($bulan, $status) {
if ($bulan=='0')
	$label = 'Setahun';
else 
	$label = 'Bulan ' . apbd_get_namabulan($bulan);

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