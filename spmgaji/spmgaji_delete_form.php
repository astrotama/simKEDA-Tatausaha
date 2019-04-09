<?php

function spmgaji_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $dokid = arg(2);
	
    if (isset($dokid)) {

		$query = db_select('dokumen', 'd');
		$query->fields('d', array('dokid', 'spmno', 'spmtgl', 'keperluan', 'jumlah', 'potongan', 'netto'));
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('d.dokid', $dokid, '=');
		$query->condition('d.spmok', 1, '=');
		$query->condition('d.sp2dok', 0, '=');
		
		//dpq($query);	
		
		$ada = false;		
		# execute the query	
		$results = $query->execute();
		foreach ($results as $data) {
			$dokid = $data->dokid;
			$spmno = $data->spmno;
			$spmtgl = apbd_fd_long($data->spmtgl); 
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
				'#title'=> 'Pembatalan Verifikasi SPM',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			
			
			$form['formdata']['dokid'] = array('#type' => 'value', '#value' => $dokid);
			$form['formdata']['keterangan'] = array (
						//'#type' => 'markup',
						'#markup' => $keperluan,
						);

			//FORM NAVIGATION	
			//$current_url = url(current_path(), array('absolute' => TRUE));
			$referer = $_SERVER['HTTP_REFERER'];
			//if ($current_url != $referer)
			//	$_SESSION["spmgajilastpage"] = $referer;
			//else
			//	$referer = $_SESSION["spmgajilastpage"];
						
			return confirm_form($form,
								'Anda yakin membatalkan Verifikasi SPM Nomor : ' . $spmno . '/' . $spmtgl,
								$referer,
								'SPM hanya akan dibatalkan verifkasinya, tidak dihapus dari dalam sistem',
								//'<button type="button" class="btn btn-danger">Hapus</button>',
								//'<em class="btn btn-danger">Hapus</em>',
								//'<input class="btn btn-danger" type="button" value="Hapus">',
								'Batalkan',
								'Tutup');
        }
    }
}
function spmgaji_delete_form_validate($form, &$form_state) {
}
function spmgaji_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $dokid = $form_state['values']['dokid'];

		$query = db_update('dokumen')
				->fields( 
				array(
					'spmok' => '0',
				)
			);
		$query->condition('dokid', $dokid, '=');
		$num = $query->execute();
			
        if ($num) {
			
			//drupal_set_message($dokid);
            drupal_set_message('Pembatalan SPM berhasil dilakukan');

			$referer = $_SESSION["spmgajilastpage"];
            drupal_goto($referer);
        }
        
    }
}
?>