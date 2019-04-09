<?php

function userskpd_main($arg=NULL, $nama=NULL) {
		$output_form = drupal_get_form('userskpd_main_form');
		return drupal_render($output_form);
}

function userskpd_main_form($form, &$form_state) {
	$referer = $_SERVER['HTTP_REFERER'];
	$username = arg(1);
	if(empty($username)){
		$username = 'dewi';
	}

	$form['username']= array(
		'#type' => 'value',
		'#value' => $username,
	);	
	$form['referer']= array(
		'#type' => 'value',
		'#value' => $referer,
	);	
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> "USER : $username",
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formdata']['table']= array(
		//'#prefix' => '<div class="table-responsive"><table class="table"><th width="50px"></th><tr><th>SKPD</th></tr>',
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="20px"></th><th>SKPD</th><th>VERIFIKATOR</th></tr>',
		'#suffix' => '</table></div>',
	);
	$query = db_query("SELECT kodeuk, namauk FROM unitkerja order by kodedinas");
	$i = 0;
	foreach ($query as $data) {
		
		$kodeuk = $data->kodeuk;
		$ada = false;
		$num_v = 0;
		
		$verifikator = '';
		/*
		$query1 = db_query("SELECT username FROM userskpd WHERE username = :username AND kodeuk = :kodeuk", array(':kodeuk' => $kodeuk, ':username' => $username));
		foreach ($query1 as $data1) {
			$ada = true;
		}
		
		*/
		$res = db_query("SELECT username FROM userskpd WHERE kodeuk = :kodeuk", array(':kodeuk' => $kodeuk));
		foreach ($res as $data1) {
			
			$num_v++;
			
			if ($username == $data1->username) $ada = true;
			$verifikator .= $data1->username . '; ';
		}
		
		if ($num_v==0)
			$namauk = '<strong>' . $data->namauk . '</strong>';
		else
			$namauk = $data->namauk;
		$i++;
		$form['formdata']['table']['kodeuk' . $i]= array(
				'#type' => 'value',
				'#value' => $kodeuk,
		);		
		$form['formdata']['table']['ada' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $ada, 
			'#prefix' => '<tr><td>',
			'#suffix' => '</td>',
		);	
		$form['formdata']['table']['namasingkat' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $namauk, 
			'#suffix' => '</td>',
		);
		$form['formdata']['table']['verifikator' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $verifikator, 
			'#suffix' => '</td></tr>',
		);
		
	}
	$form['formdata']['jumlah']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	
	return $form;
}

function userskpd_main_form_submit($form, &$form_state){
	$jumlah = $form_state['values']['jumlah'];
	$username = $form_state['values']['username'];
	$referer = $form_state['values']['referer'];
	
	for ($n=1; $n <= $jumlah; $n++) {
		$kodeuk = $form_state['values']['kodeuk' . $n];
		$ada = $form_state['values']['ada' . $n];
		
		if($ada){
			$d_ada = 0;
			$query1 = db_query("SELECT COUNT(kodeuk) AS d_ada FROM userskpd WHERE username= :username AND kodeuk = :kodeuk", array(':username' => $username, ':kodeuk' => $kodeuk));
			foreach($query1 as $data1){
				$d_ada = $data1->d_ada;
			}
			if($d_ada == 0 ){
				$insert = db_query("INSERT INTO userskpd(username, kodeuk) VALUES(:username, :kodeuk)", array(':username' => $username, ':kodeuk' => $kodeuk));
				// drupal_set_message('ada');
			}
		} else {
			$delete = db_query("DELETE FROM userskpd WHERE username= :username AND kodeuk = :kodeuk", array(':username' => $username, ':kodeuk' => $kodeuk));
			
		}	
	}
	drupal_goto($referer);

}


?>
