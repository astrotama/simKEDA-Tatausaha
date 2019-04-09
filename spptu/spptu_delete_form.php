<?php

function spptu_delete_form() {
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
				'#title'=> 'Konfirmasi Penghapusan SPP-TU',
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
				$_SESSION["spptulastpage"] = $referer;
			else
				$referer = $_SESSION["spptulastpage"];

			return confirm_form($form,
								'Anda yakin menghapus SPP-TU Nomor : ' . $sppno . '/' . $spptgl,
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
function spptu_delete_form_validate($form, &$form_state) {
}

function spptu_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $dokid = $form_state['values']['dokid'];

		//dokumenkelengkapan
		$num = db_delete('dokumenkelengkapan')
		  ->condition('dokid', $dokid)
		  ->execute();
		
		//dokumen
		$num = db_delete('dokumen')
		  ->condition('dokid', $dokid)
		  ->execute();
        if ($num) {
			
			//drupal_set_message($dokid);
            drupal_set_message('Penghapusan berhasil dilakukan');
			
			$referer = $_SESSION["spptulastpage"];
            drupal_goto($referer);
        }
        
    }
}
?>