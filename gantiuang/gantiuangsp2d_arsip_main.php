<?php
function gantiuangsp2d_arsip_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 5;
    
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
				$sp2dok = arg(4);

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
			$kodeuk = $_SESSION["gantiuangsp2d_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["gantiuangsp2d_arsip_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$sp2dok = $_SESSION["gantiuangsp2d_arsip_sp2dok"];
		if ($sp2dok=='') $sp2dok = 'ZZ';

		
	}
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('gantiuangsp2d_arsip_main_form');
	if (isSuperuser())
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px', 'field'=> 'sp2dtgl', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Penerima', 'field'=> 'penerimanama', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	else 
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px', 'field'=> 'sp2dtgl', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
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
	$query->fields('d', array('dokid', 'bulan', 'keperluan', 'kodeuk', 'spmno', 'sppno', 'sp2dno', 'sp2dtgl', 'jumlah', 'potongan', 'netto', 'sp2dok', 'penerimanama'));
	$query->fields('u', array('namasingkat'));
	
	//GAJI
	$query->condition('d.jenisdokumen', 1, '=');
	
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan!='0') {
		$or = db_or();
		$or->condition('d.bulan', $bulan, '=');
		$or->where('EXTRACT(MONTH FROM d.spmtgl) = :month', array('month' => $bulan));
		$or->where('EXTRACT(MONTH FROM d.sp2dtgl) = :month', array('month' => $bulan));
		
		$query->condition($or);
	}
	
	$query->condition('d.spmok', '1', '=');
	if ($sp2dok != 'ZZ' ) $query->condition('d.sp2dok', $sp2dok, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('u.namasingkat', 'ASC');
	$query->limit($limit);
	
	
	
	
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
		
		
		$editlink = apbd_button_jurnal('gubarusp2d/edit/' . $data->dokid);
		$esp2dlink = "eSP2D";
		
		if($data->sp2dok=='2')
			$sp2dok = apbd_icon_valid();
		else if($data->sp2dok=='1') {
			$sp2dok = apbd_icon_sudah();
			$esp2dlink = apbd_button_esp2d($data->dokid);
			
		} else 
			$sp2dok = apbd_icon_belum();
		
		if ($data->sp2dno=='') 
			$sp2dtgl = '';
		else
			$sp2dtgl = apbd_fd($data->sp2dtgl);
		
		//ESPM
		$espmlink = "eSPM";
		//if (($data->kodeuk=='81') or ($data->kodeuk=='03')) {
			$espmlink = apbd_button_espm($data->dokid);
		//}
		
		//drupal_goto('gantiuangsp2d/edit/' . $dokid . '/pdf');
		if (isSuperuser()) {
			
			//CETAK
			if (($data->sp2dok=='1') and  ($data->sp2dno!='')) 
				$cetaklink = apbd_button_cetak('gantiuangsp2d/edit/' . $data->dokid . '/pdf');
			else
				$cetaklink = 'Cetak';
			
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $sp2dok,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => $sp2dtgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),						
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanama,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						$editlink,
						$cetaklink,
						$esp2dlink,
						$espmlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		} else 
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $sp2dok,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => $sp2dtgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),						
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanama,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						//$editlink,
						$esp2dlink,
						$espmlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);			
	}
	
	
	//BUTTON
	/*
	$btn = apbd_button_print('/cetakregister/' . $kodeuk . '/' . date('n') );
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

function gantiuangsp2d_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$sp2dok = $form_state['values']['sp2dok'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/

	$_SESSION["gantiuangsp2d_arsip_kodeuk"] = $kodeuk;
	$_SESSION["gantiuangsp2d_arsip_bulan"] = $bulan;
	$_SESSION["gantiuangsp2d_arsip_sp2dok"] = $sp2dok;

	$uri = 'gusp2darsip/filter/' . $kodeuk . '/' . $bulan . '/' . $sp2dok ;
	drupal_goto($uri);
	
}


function gantiuangsp2d_arsip_main_form($form, &$form_state) {
	
	/*
	$kodeuk = 'ZZ';
	//$bulan = date('m');
	$bulan = '1';
	$sp2dok = 'ZZ';
	*/
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$sp2dok = arg(4);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["gantiuangsp2d_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["gantiuangsp2d_arsip_bulan"];
		if ($bulan=='') $bulan = date('m');
		
		$sp2dok = $_SESSION["gantiuangsp2d_arsip_sp2dok"];
		if ($sp2dok=='') $sp2dok = 'ZZ';	

		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $sp2dok) . '</small></em>',		//'#attributes' => array('class' => array('container-inline')),
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
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
			'#prefix' => '<div class="col-md-4">',
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
			'#prefix' => '<div class="col-md-4">',
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
		'#prefix' => '<div class="col-md-4">',
		'#suffix' => '</div>',
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);


	$opt_sp2d['ZZ'] ='SEMUA';
	$opt_sp2d['0'] = 'BELUM VERIFIKASI';
	$opt_sp2d['1'] = 'SUDAH VERIFIKASI';	
	//$opt_sp2d['2'] = 'SUDAH VALIDASI';	
	$form['formdata']['sp2dok'] = array(
		'#type' => 'select',
		'#title' =>  t('Verifikasi'),
		'#prefix' => '<div class="col-md-4">',
		'#suffix' => '</div>',
		'#options' => $opt_sp2d,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sp2dok,
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
	$label .= '/Semua SP2D';
else if ($status=='0')	
	$label .= '/Belum verifikasi';
else if ($status=='1')	
	$label .= '/Sudah verifikasi';

$label .= ' (Klik disini untuk mengganti pilihan data)';
return $label;
}

?>