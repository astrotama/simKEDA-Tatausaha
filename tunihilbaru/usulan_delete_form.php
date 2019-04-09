<?php

function usulan_delete_form() {
	
	$spjid = arg(2);
	
    if (isset($spjid)) {

		$query = db_select('spjtu', 's');
		$query->fields('s', array('spjid', 'kodekeg', 'keperluan', 'tanggal'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('s.spjid', $spjid, '=');
		
		//dpq($query);	
		
		$ada = false;		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$spjid = $data->spjid;
			$kodekeg = $data->kodekeg;
			$keperluan = $data->keperluan;
			$tanggal = $data->tanggal;
			
			$ada = true;
		}	
		
	 
        //if ($ada) {
            
			//drupal_set_message('x');		
			$form['formdata'] = array (
				'#type' => 'fieldset',
				'#title'=> 'Konfirmasi Penghapusan Usulan TU Nihil',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			
			
			$form['formdata']['spjid'] = array('#type' => 'value', '#value' => $spjid);
			$form['formdata']['keperluan'] = array (
						//'#type' => 'markup',
						'#markup' => $keperluan,
						);
			
			//FORM NAVIGATION	
			$current_url = url(current_path(), array('absolute' => TRUE));
			$referer = $_SERVER['HTTP_REFERER'];
			if ($current_url != $referer)
				$_SESSION["tunihilbaru_lastpage"] = $referer;
			else
				$referer = $_SESSION["tunihilbaru_lastpage"];

			return confirm_form($form,
								'Anda yakin menghapus TU Nihil Nomor : ' . $kodekeg . ' tanggal : ' . $tanggal . ' keperluan : ' . $keperluan ,
								$referer,
								'PERHATIAN : SPP yang dihapus tidak bisa dikembalikan lagi.',
								//'<button type="button" class="btn btn-danger">Hapus</button>',
								//'<em class="btn btn-danger">Hapus</em>',
								//'<input class="btn btn-danger" type="button" value="Hapus">',
								'Hapus',
								'Batal');
        //}
    }
}
function usulan_delete_form_validate($form, &$form_state) {
}
function usulan_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $spjid = $form_state['values']['spjid'];
		
		//dokumenkelengkapan
		$num = db_delete('spjtu')
		  ->condition('spjid', $spjid)
		  ->execute();
		  
		$num = db_delete('spjturekening')
		  ->condition('spjid', $spjid)
		  ->execute();
	
		
		$kodeuk = apbd_getuseruk();
		
		db_set_active('bendahara');


		$query = db_update('bendahara' . $kodeuk) 
				->fields(array(
				  'sudahproses' => '0',
				  'dokid' => '',
				   )
				 );
		$query->condition('dokid', $spjid);
		$res = $query->execute();

		db_set_active();	

		//if ($num) {
			
			//drupal_set_message($dokid);
            drupal_set_message('Penghapusan berhasil dilakukan');
			
            drupal_goto('tunihilbaru/arsip');
        //}
        
    }
}
?>