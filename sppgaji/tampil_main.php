<?php
function tampil_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
	
	$output_form = drupal_get_form('tampil_main_form');
	
		return drupal_render($output_form);
	
	
}

function tampil_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	$_SESSION["tampil_kodeuk"] = $kodeuk;
	
	$uri = 'tampil/filter/' . $kodeuk;
	drupal_goto($uri);
	
}


function tampil_main_form($form, &$form_state) {
	if ($arg = 'filter') {
		$kodeuk = arg(2);
	}
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',		//'#attributes' => array('class' => array('container-inline')),
		//'#collapsible' => TRUE,
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

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	//DATA
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'DATA',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="150px">NO SPM</th><th width="150px">TANGGAL</th><th>KEPERLUAN</th><th width="100">JUMLAH</th><th width="50px">SPM</th><th width="50px">Ada</th></tr>',
		 '#suffix' => '</table></div>',
	);
	$ada = 0;
	$a = 1;
	
	
	
	$query = db_select('dokumen', 'd');
	$query->fields('d', array('kodeuk', 'spmno', 'spmtgl', 'keperluan', 'jumlah', 'spmok'));
	$query->condition('d.spmok', 1, '=');
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	
	$query->orderBy('spmno', 'ASC');
	
	# execute the query
	$results = $query->execute();
		
		foreach($results as $data) {
			$spmno = $data->spmno;
			$spmtgl = $data->spmtgl;
			$keperluan = $data->keperluan;
			$jumlah = $data->jumlah;
			
			$form['formkelengkapan']['tablekelengkapan']['nomor' . $a]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $a,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['nospm' . $a]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $spmno, 
			'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['tanggal' . $a]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $spmtgl, 
			'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['keperluan' . $a]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $keperluan, 
			'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['jumlah' . $a]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $jumlah, 
			'#suffix' => '</td>',
		); 
		
		$form['formkelengkapan']['tablekelengkapan']['spm' . $a]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> 'spm', 
			'#suffix' => '</td>',
		);
		
		$form['formkelengkapan']['tablekelengkapan']['adakelengkapan' . $a]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $ada, 
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		
		$a++;
		}
	
	return $form;
}

?>
