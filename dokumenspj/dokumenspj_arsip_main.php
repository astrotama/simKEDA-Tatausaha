<?php
function dokumenspj_arsip_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$kodeuk = arg(2);
				$bulan = arg(3);

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
			if(!isset($_SESSION["dokumenspj_arsip_kodeuk"]))
				$_SESSION["dokumenspj_arsip_kodeuk"]='ZZ';
			$kodeuk = $_SESSION["dokumenspj_arsip_kodeuk"];
			//if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		if(!isset($_SESSION["dokumenspj_arsip_bulan"]))
			$_SESSION["dokumenspj_arsip_bulan"] = date('m');
		$bulan = $_SESSION["dokumenspj_arsip_bulan"];
		//if ($bulan=='') $bulan = date('m');
		if(!isset($_SESSION["dokumenspj_arsip_sppok"]))
				$_SESSION["dokumenspj_arsip_sppok"]='ZZ';
		$sppok = $_SESSION["dokumenspj_arsip_sppok"];
		//if ($sppok=='') $sppok = 'ZZ';
		
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
	
	//drupal_set_message($bulan);
	
	$output_form = drupal_get_form('dokumenspj_arsip_main_form');
	if (isSuperuser()) 
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Tgl Awal','width' => '90px',  'field'=> 'tglawal', 'valign'=>'top'),
			array('data' => 'Tgl Akhir','width' => '90px',  'field'=> 'tglakhir', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Tgl Awal','width' => '90px',  'field'=> 'tglawal', 'valign'=>'top'),
			array('data' => 'Tgl Akhir','width' => '90px',  'field'=> 'tglakhir', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
		);

	$query = db_select('dokumenspj', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('dokspjid', 'tglawal', 'tglakhir', 'uraian'));
	$query->fields('u', array('namasingkat'));
	
	if ($bulan!='0') $query->where('EXTRACT(MONTH FROM d.tglawal) = :month', array('month' => $bulan));
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	
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
		$proses = apbd_icon_sudah();
		
		//EDIT LINK
		$editlink = apbd_button_jurnal('dokumenspj/edit/' . $data->dokspjid);
		$editlink .=  apbd_button_hapus('dokumenspj/delete/' . $data->dokspjid);
		

		
		if (isSuperuser())
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->tglawal,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->tglakhir,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		else
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $proses,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->tglawal,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->tglakhir,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		
	}
	
	
	//BUTTON
	if (isUserSKPD()) $btn = apbd_button_baru('dokumenspj/new');
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	return drupal_render($output_form) . $btn . $output . $btn;
	
}



function dokumenspj_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];

	
	$_SESSION["dokumenspj_arsip_kodeuk"] = $kodeuk;
	$_SESSION["dokumenspj_arsip_bulan"] = $bulan;
	
	$uri = 'dokumenspjarsip/filter/' . $kodeuk . '/' . $bulan;
	drupal_goto($uri);
	
}


function dokumenspj_arsip_main_form($form, &$form_state) {
	
	//$kodeuk = 'ZZ';
	//$bulan = date('m');
	//$bulan = '1';
	//$sppok = 'ZZ';
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["dokumenspj_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["dokumenspj_arsip_bulan"];
		if ($bulan=='') $bulan = '1';
		
		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		'#title'=>  'PILIHAN DATA',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
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
	

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}

?>
