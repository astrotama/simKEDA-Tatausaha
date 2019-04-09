<?php

function gantiuangspp_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $dokid = arg(2);
		
    if (isset($dokid)) {

		$query = db_select('dokumen', 'd');
		$query->fields('d', array('dokid', 'sppno', 'spptgl', 'keperluan', 'jumlah', 'potongan', 'netto'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');
		$query->condition('d.sppok', 0, '=');
		
		//dpq($query);	
		
		$ada = false;		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$dokid = $data->dokid;
			$sppno = $data->sppno;
			$spptgl = apbd_fd_long($data->spptgl); 
			$keperluan = $data->keperluan . ', sebesar ' . apbd_fn($data->jumlah); 
			
			//$jumlah = apbd_fn($data->jumlah);
			//$potongan = apbd_fn($data->potongan);
			//$netto = apbd_fn($data->netto);
			$ada = true;		
			
			//drupal_set_message('x1');
		}	
		
	 
        if ($ada) {
            
			//drupal_set_message('x');		
			$form['formdata'] = array (
				'#type' => 'fieldset',
				'#title'=> 'Konfirmasi Penghapusan SPP',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			
			
			$form['formdata']['dokid'] = array('#type' => 'value', '#value' => $dokid);
			$form['formdata']['keterangan'] = array (
						//'#type' => 'markup',
						'#markup' => $keperluan,
						);
			
			//FORM NAVIGATION	
			$current_url = url(current_path(), array('absolute' => TRUE));
			$referer = $_SERVER['HTTP_REFERER'];
			if ($current_url != $referer)
				$_SESSION["gantiuangspp_lastpage"] = $referer;
			else
				$referer = $_SESSION["gantiuangspp_lastpage"];

			return confirm_form($form,
								'Anda yakin menghapus SPP Nomor : ' . $sppno . '/' . $spptgl,
								$referer,
								'PERHATIAN : SPP yang dihapus tidak bisa dikembalikan lagi.',
								//'<button type="button" class="btn btn-danger">Hapus</button>',
								//'<em class="btn btn-danger">Hapus</em>',
								//'<input class="btn btn-danger" type="button" value="Hapus">',
								'Hapus',
								'Batal');
        }
    }
}
function gantiuangspp_delete_form_validate($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];
	
	$query = db_query('select count(kodero) as rekening from dokumenrekening where dokid=:dokid', array(':dokid' => $dokid));
	foreach($query AS $data){
		$rekening = $data->rekening;
	}
	
	if ($rekening > 0){
		form_set_error($rekening,'Maaf, Masih Ada rekening yang digunakan');
	}
	

}
function gantiuangspp_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $dokid = $form_state['values']['dokid'];
		$kodeuk = substr($dokid, 0,2);
		//drupal_set_message($kodeuk);
		//dokumenkelengkapan
		$num = db_delete('dokumenkelengkapan')
		  ->condition('dokid', $dokid)
		  ->execute();

		//dokumenpajak
		$num = db_delete('dokumenpajak')
		  ->condition('dokid', $dokid)
		  ->execute();
		  
		//dokumenrekening
		$num = db_delete('dokumenrekening')
		  ->condition('dokid', $dokid)
		  ->execute();
		
		$num = db_delete('dokumen')
		  ->condition('dokid', $dokid)
		  ->execute();
		
		//reset bendahara
		db_set_active('bendahara');
		$query = db_update('bendahara' . $kodeuk)
				->fields(array(
					'sudahproses' => '0',
					'dokid' => '',
					)
				 )
		->condition('dokid', $dokid, '=')
		->execute();
		db_set_active('');
	
        if ($num) {
			
			//drupal_set_message($dokid);
            drupal_set_message('Penghapusan berhasil dilakukan');
			
			$referer = $_SESSION["gantiuangspp_lastpage"];
            drupal_goto($referer);
        }
        
    }
}
?>