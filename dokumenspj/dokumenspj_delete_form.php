<?php

function dokumenspj_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $dokspjid = arg(2);
	
    if (isset($dokspjid)) {

		$query = db_select('dokumenspj', 'd');
		$query->fields('d', array('dokspjid', 'tglawal', 'tglakhir', 'uraian'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokspjid', $dokspjid, '=');
		
		//dpq($query);	
		
		$ada = false;		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$dokspjid = $data->dokspjid;
			$tglawal = apbd_fd_long($data->tglawal);
			$tglakhir = apbd_fd_long($data->tglakhir);			
			$uraian = $data->uraian; 
			
			$ada = true;		
		}	
		
	 
        if ($ada) {
            
			//drupal_set_message('x');		
			$form['formdata'] = array (
				'#type' => 'fieldset',
				'#title'=> 'Konfirmasi Penghapusan SPJ',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			
			
			$form['formdata']['dokspjid'] = array('#type' => 'value', '#value' => $dokspjid);
			$form['formdata']['keterangan'] = array (
						//'#type' => 'markup',
						'#markup' => $uraian,
						);
			
			//FORM NAVIGATION	
			$current_url = url(current_path(), array('absolute' => TRUE));
			$referer = $_SERVER['HTTP_REFERER'];
			if ($current_url != $referer)
				$_SESSION["dokumenspj_lastpage"] = $referer;
			else
				$referer = $_SESSION["dokumenspj_lastpage"];

			return confirm_form($form,
								'Anda yakin menghapus SPJ tanggal ' . $tglawal . ' - ' . $tglakhir,
								$referer,
								'PERHATIAN : SPJ yang dihapus tidak bisa dikembalikan lagi.',
								//'<button type="button" class="btn btn-danger">Hapus</button>',
								//'<em class="btn btn-danger">Hapus</em>',
								//'<input class="btn btn-danger" type="button" value="Hapus">',
								'Hapus',
								'Batal');
        }
    }
}
function dokumenspj_delete_form_validate($form, &$form_state) {
}
function dokumenspj_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $dokspjid = $form_state['values']['dokspjid'];
		
		$num = db_delete('dokumenspj')
		  ->condition('dokspjid', $dokspjid)
		  ->execute();

		$num = db_delete('dokumenspjitem')
		  ->condition('dokspjid', $dokspjid)
		  ->execute();

		$num_deleted = db_delete('dokumenfile')
		  ->condition('dokid', $dokspjid)
		  ->execute();
		  
        if ($num) {
			
			//drupal_set_message($dokspjid);
            drupal_set_message('Penghapusan berhasil dilakukan');
			
			$referer = $_SESSION["dokumenspj_lastpage"];
            drupal_goto($referer);
        }
        
    }
}
?>