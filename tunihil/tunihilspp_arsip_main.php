<?php
function tunihilspp_arsip_main($arg=NULL, $nama=NULL) {
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
				$sppok = arg(4);

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
			if(!isset($_SESSION["tunihilspp_arsip_kodeuk"]))
				$_SESSION["tunihilspp_arsip_kodeuk"]='ZZ';
			$kodeuk = $_SESSION["tunihilspp_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		//$bulan = $_SESSION["tunihilspp_arsip_bulan"];
		//if ($bulan=='') $bulan = date('m');
		$bulan = date('m');
		
		//$sppok = $_SESSION["tunihilspp_arsip_sppok"];
		//if ($sppok=='') $sppok = 'ZZ';
		$sppok = 'ZZ';
		
	}
	
	/*
	if (isUserSKPD()) {
		drupal_set_message('SKPD');
		drupal_set_message(apbd_getuseruk());
	} else
		drupal_set_message('Not SKPD');
	*/
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('tunihilspp_arsip_main_form');
	if (isSuperuser()) 
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'Tanggal','width' => '90px',  'field'=> 'spptgl', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'kodeuk', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'Tanggal','width' => '90px',  'field'=> 'spptgl', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		);

	$query = db_select('dokumen', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('dokid', 'jenisdokumen', 'sppno', 'spmno', 'sp2dno', 'sp2dok', 'spptgl', 'bulan', 'kodeuk', 'jumlah', 'potongan', 'netto', 'sppok', 'keperluan'));
	$query->fields('u', array('namasingkat'));
	
	$query->condition('d.jenisdokumen', 7, '=');
	
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if (($sppok =='0') or ($sppok =='1'))
		$query->condition('d.sppok', $sppok, '=');
	else if ($sppok =='2') {
		$query->condition('d.sppok', '1', '=');
		$query->condition('d.spmok', '1', '=');
	} else if ($sppok =='3') {
		$query->condition('d.sppok', '1', '=');
		$query->condition('d.spmok', '1', '=');
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.sp2dsudah', '1', '=');
	}
	
	$query->orderByHeader($header);
	$query->orderBy('u.namasingkat', 'ASC');
	$query->limit($limit);
		
	dpq($query);
	
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
		if($data->sppok=='1')
			if ($data->sp2dok=='1')
				$proses = apbd_icon_valid();
			else
				$proses = apbd_icon_sudah();
		else
			$proses = apbd_icon_belum();
		
		$editlink = apbd_button_jurnal('tunihilspp/edit/' . $data->dokid);
		if ($data->sppok=='0') $editlink .=  apbd_button_hapus('tunihilspp/delete/' . $data->dokid);
		

		if ($data->sppno=='') 
			$spptgl = '';
		else
			$spptgl = apbd_fd($data->spptgl);
		
		if (isSuperuser())
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $spptgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),						
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		else
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $spptgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),						
						array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);			
	}
	
	
	//BUTTON
	if (isUserSKPD()) {
		$btn = apbd_button_baru('spjgu/baru');
		$btn .= "&nbsp;" . apbd_button_print('');
	} else 
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

function tunihilspp_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$sppok = $form_state['values']['sppok'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	$_SESSION["tunihilspp_arsip_kodeuk"] = $kodeuk;
	$_SESSION["tunihilspp_arsip_bulan"] = $bulan;
	$_SESSION["tunihilspp_arsip_sppok"] = $sppok;
	
	$uri = 'tunihilspparsip/filter/' . $kodeuk . '/' . $bulan  . '/' . $sppok;
	drupal_goto($uri);
	
}


function tunihilspp_arsip_main_form($form, &$form_state) {
	
	//$kodeuk = 'ZZ';
	//$bulan = date('m');
	//$bulan = '1';
	//$sppok = 'ZZ';
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$sppok = arg(4);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["tunihilspp_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		//$bulan = $_SESSION["tunihilspp_arsip_bulan"];
		//if ($bulan=='') $bulan = '1';
		$bulan = '0';
		
		//$sppok = $_SESSION["tunihilspp_arsip_sppok"];
		//if ($sppok=='') $sppok = 'ZZ';
		
		$sppok = 'ZZ';
		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $sppok) . '</small></em>',
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
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);
	
	//SPP
	$opt_jurnal['ZZ'] ='SEMUA SPP';
	$opt_jurnal['0'] = 'SPP BELUM DIVERIFIKASI';
	$opt_jurnal['1'] = 'SPP SUDAH DIVERIFIKASI';	
	$opt_jurnal['2'] = 'SPP SUDAH TERBIT SPM';
	$opt_jurnal['3'] = 'SPP SUDAH TERBIT SP2D';
	$form['formdata']['sppok'] = array(
		'#type' => 'select',
		'#title' =>  t('Status'),
		'#options' => $opt_jurnal,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sppok,
	);	 

	$form['formdata']['submit']= array(
		'#type' => 'submit',
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
	$label .= '/Semua SPP';
else if ($status=='0')	
	$label .= '/Belum verifikasi';
else if ($status=='1')	
	$label .= '/Sudah verifikasi';
else if ($status=='2')
	$label .= '/Sudah SPM';
else if ($status=='3')	
	$label .= '/Sudah SP2D';

$label .= ' (Klik disini untuk mengganti pilihan data)';
return $label;
}

?>
