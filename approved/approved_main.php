<?php
function approved_main($arg=NULL, $nama=NULL) {
		$output_form = drupal_get_form('approved_main_form');
		//$output .= theme('pager');
		//$btn = '<em class="text-info pull-right"><span class="badge">' . 'Total Data : '. approved(). '</em><br/>';
		return drupal_render($output_form);		//.$btn;// . $output;
	
}

function approved_main_form($form, &$form_state) {
	
	drupal_add_js("
    function checkUncheckAll(theElement){
      var theForm = theElement.form, z = 0;
      for(z=0; z<theForm.length;z++){
        if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall'){
          theForm[z].checked = theElement.checked;
        }
      }
    }
  ", 'inline');
	//KELENGKAPAN
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'SPM'  . '<em><small class="text-info pull-right">SPM Masuk : <span class="badge">' . approved() . '</small></em>',		//'#attributes' => array('class' => ,
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formkelengkapan']['checkall'] = array(
		'#type' => 'checkbox',
		'#title' => t('PILIH SEMUA'),
		'#default_value'=> 1, 
		'#attributes' => array('onclick' => 'checkUncheckAll(this);'),
	);
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">Pilih</th><th width="10px">No.</th><th width="100px">No SPM</th><th width="100px">Tgl SPM</th><th>Keperluan</th><th width="100px">Jumlah</th><th>SKPD</th><th width="53px">eSPM</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0; $n = 0;
	
	$results = db_query("select d.dokid, d.spmno,d.spmtgl,d.keperluan,d.jumlah,d.approved,u.namasingkat from dokumen as d inner join unitkerja as u on d.kodeuk = u.kodeuk where d.spmok=1 and d.approved = 0 order by spmtgl asc limit 30");
	
	
	$approved = '1';	///$data->approved;
	foreach ($results as $data) {

		if (is_eSPMExists($data->dokid)) {
			$i++; 
			
			$espm =  l('eSPM', 'http://simkedajepara.web.id/edoc2019/spm/E_SPM_' . $data->dokid . '.PDF', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-info btn-xs btn-block')));

	
			$form['formkelengkapan']['tablekelengkapan']['dokid' . $i]= array(
					'#type' => 'value',
					'#value' => $data->dokid,
			); 
			
			$form['formkelengkapan']['tablekelengkapan']['approved' . $i]= array(
				'#type'         => 'checkbox', 
				'#default_value'=> $approved, 
				'#prefix' => '<tr><td>',
				'#suffix' => '</td>',
			);
			$form['formkelengkapan']['tablekelengkapan']['no' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $i, 
				'#suffix' => '</td>',
			); 
			$form['formkelengkapan']['tablekelengkapan']['spmno' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->spmno, 
				'#suffix' => '</td>',
			); 
			$form['formkelengkapan']['tablekelengkapan']['spmtgl' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> apbd_fd($data->spmtgl), 
				'#suffix' => '</td>',
			);
			$form['formkelengkapan']['tablekelengkapan']['keperluan' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->keperluan, 
				'#suffix' => '</td>',
			); 
			$form['formkelengkapan']['tablekelengkapan']['jumlah' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> '<p class="text-right">' . apbd_fn($data->jumlah) . '</p>', 
				'#suffix' => '</td>',
			);
			$form['formkelengkapan']['tablekelengkapan']['namasingkat' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->namasingkat, 
				'#suffix' => '</td>',
			); 
			$form['formkelengkapan']['tablekelengkapan']['espm' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $espm, 
				'#suffix' => '</td></tr>',
			); 
		
		} else {
			$n++;
		}
	}
	
	if ($n>0) {
		$form['formkelengkapan']['keterangan'] = array (
			'#type' => 'item',
			'#markup'=> '<em><small class="text-info pull-right">eSPM belum ditandatangani : <span class="badge">' . $n . '</small></em>',		//'#attributes' => array('class' => ,
		);		
	}
	
	$form['jumlah']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Approve',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}


	
function approved_main_form_submit($form, &$form_state) {
$jumlah = $form_state['values']['jumlah'];

for ($n=1; $n <= $jumlah; $n++) {
	$dokid = $form_state['values']['dokid'.$n];
	$approved = $form_state['values']['approved'.$n];
	if($approved) {
		//drupal_set_message($dokid);
		
		$query = db_update('dokumen')
				->fields( 
				array(
					'approved' => '1',

				)
			);
			
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();		
		
	}
}
	
	//drupal_goto('spmgajiarsip');
	//drupal_goto();
}
function approved(){
	$query = db_select('dokumen', 'd');
	$query->addExpression('COUNT(dokid)', 'jumlah');
	$query->condition('d.approved', 0, '=');
	$query->condition('d.spmok', 1, '=');
		
	$jumlah = 0;
	# execute the query
	$results = $query->execute();	
	foreach ($results as $data) {
		$jumlah = $data->jumlah;	
	}	
	return $jumlah;
}

?>