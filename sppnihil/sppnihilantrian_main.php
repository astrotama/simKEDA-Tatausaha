<?php
function sppnihilantrian_main($arg=NULL, $nama=NULL) {
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
				$proses = arg(4);

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
			$kodeuk = $_SESSION["sppnihilantrian_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["sppnihilantrian_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$proses = $_SESSION["sppnihilantrian_proses"];
		if ($proses=='') $proses = 'ZZ';
	}
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('sppnihilantrian_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'kodeuk', 'valign'=>'top'),
		array('data' => 'Bulan', 'field'=> 'kodeuk', 'valign'=>'top'),
		array('data' => 'Bruto', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
		array('data' => 'Potongan', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
		array('data' => 'Netto', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('gaji', 'g')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'g.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('g', array('nourut', 'bulan', 'kodeuk', 'totalkotor', 'totalpotongan', 'totalbersih', 'proses', 'dokid'));
	$query->fields('u', array('namasingkat'));
	
	
	if ($kodeuk !='ZZ') $query->condition('g.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->condition('g.bulan', $bulan, '=');
	
	if ($proses !='ZZ') $query->condition('g.proses', $proses, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('u.namasingkat', 'ASC');
	$query->limit($limit);
		
	//dpq($query);
	
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
		
		
		if($data->proses=='1'){
			$proses = apbd_icon_sudah();
			$editlink = apbd_button_jurnal('sppnihil/edit/' . $data->dokid);
		
		} else {
			$proses = apbd_icon_belum();
			$editlink = apbd_button_jurnalkan('sppnihil/new/' . $data->nourut);
		}

		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->totalkotor),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->totalpotongan),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->totalbersih),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	if (isUserSKPD()) {
		$btn = apbd_button_baru('sppnihil/newmanual');
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

function sppnihilantrian_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$proses = $form_state['values']['proses'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/

	$_SESSION["sppnihilantrian_kodeuk"] = $kodeuk;
	$_SESSION["sppnihilantrian_bulan"] = $bulan;
	$_SESSION["sppnihilantrian_proses"] = $proses;
	
	$uri = 'sppnihilantrian/filter/' . $kodeuk . '/' . $bulan . '/' . $proses ;
	drupal_goto($uri);
	
}


function sppnihilantrian_main_form($form, &$form_state) {
	
	/*
	$kodeuk = 'ZZ';
	//$bulan = date('m');
	$bulan = '1';
	$proses = '0';
	*/
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$proses = arg(4);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["sppnihilantrian_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["sppnihilantrian_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$proses = $_SESSION["sppnihilantrian_proses"];
		if ($proses=='') $proses = 'ZZ';		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		

	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['formdata']['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);			
	} else {	
		//SKPD
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

	$opt_jurnal['ZZ'] ='SEMUA';
	$opt_jurnal['0'] = 'ANTRIAN LEDGER GAJI';
	$opt_jurnal['1'] = 'SUDAH DIBUATKAN SPP';	
	$form['formdata']['proses'] = array(
		'#type' => 'select',
		'#title' =>  t('SPP Gaji'),
		'#options' => $opt_jurnal,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $proses,
	);	 

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}



?>
