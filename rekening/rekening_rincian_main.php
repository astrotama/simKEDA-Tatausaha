<?php
function rekening_rincian_main($arg=NULL, $nama=NULL) {

	$kodeo=arg(2);
	if ($kodeo=='') $kodeo = "52201";
	$opsi=arg(3);
	if ($opsi=='') $opsi = "view";
	
	
	$output = '';
	if ($opsi=='view') {
		$output = gen_view_rincian($kodeo);
		$output_form = drupal_get_form('rekening_rincian_main_form');
		return drupal_render($output_form) . $output;
		
	} else if ($opsi=='excel') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=rekening_rincian.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$outputexcel = gen_view_rincian($kodeo);
		echo $outputexcel;
		
	} else if ($opsi=='input') {
		$output_form = drupal_get_form('rekening_rincian_main_form');
		return drupal_render($output_form);
		
	}
	
	
}

function rekening_rincian_main_form($form, &$form_state) {

	$kodeo=arg(2);
	if ($kodeo=='') $kodeo = "52201";
	$opsi=arg(3);
	if ($opsi=='') $opsi = "view";
	
	//drupal_set_message($kodeo);
	
	$results = db_query('SELECT kodeo,uraian from {obyek} where kodej=:kodej order by kodeo', array(':kodej'=>substr($kodeo, 0, 3)));
	foreach ($results as $data) {
		$options[$data->kodeo]= $data->kodeo . ' - ' . $data->uraian;
	}
	$form['kodeo']= array(
		'#type'=>'select',
		'#options'=>$options,
		'#default_value'=>$kodeo,
	);
	$form['e_kodeo']= array(
		'#type'=>'value',
		'#value'=>$kodeo,
	);
	
	$form['view']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['input']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-saved" aria-hidden="true"></span> Input',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['excel']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Excel',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	if ($opsi=='input') {
		$form['formdokumen']['tablerek']= array(
			'#prefix' => '<table class="table table-hover"><tr><th>No</th><th>Kode</th><th>Uraian</th></tr>',
			 '#suffix' => '</table>',
		);
		
			$results = db_query("SELECT kodero, uraian from {rincianobyek} where kodeo=:kodeo order by kodero", array(':kodeo'=>$kodeo));
		
		$i = 0;
		foreach ($results as $data) {

			$i++; 
			$form['formdokumen']['tablerek']['e_kodero' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 
			$form['formdokumen']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#default_value' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#default_value' => $data->uraian,
					'#size' => 70,
					'#suffix' => '</td></tr>',
			); 
			
		}
		
		//NEW
		for ($x=1; $x<=5; $x++) {
		  $i++; 
			$form['formdokumen']['tablerek']['e_kodero' . $i]= array(
					'#type' => 'value',
					'#value' => 'new',
			); 
			$form['formdokumen']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#default_value' => '',
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#default_value' => '',
					'#size' => 70,
					'#suffix' => '</td></tr>',
			); 
		}
		
			
		$form['formdokumen']['jumlahrek']= array(
			'#type' => 'value',
			'#value' => $i,
		);
		$form['formdata']['submit']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> SIMPAN',
			'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		);
	}
	return $form;
}


function rekening_rincian_main_form_validate($form, &$form_state) {
	
}
	
function rekening_rincian_main_form_submit($form, &$form_state) {

	$kodeo = $form_state['values']['kodeo'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['input']) {
		
		drupal_goto('rekening/rincian/' . $kodeo . '/input');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['view']) {
		
		drupal_goto('rekening/rincian/' . $kodeo . '/view');
		
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['excel']) {
		
		drupal_goto('rekening/rincian/' . $kodeo . '/excel');

	}elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		
		$jumlahrek = $form_state['values']['jumlahrek'];
		$kodeo = $form_state['values']['e_kodeo'];
	
		for($n=1; $n<=$jumlahrek; $n++){

			$kodero=$form_state['values']['kodero' . $n];
			$e_kodero=$form_state['values']['e_kodero' . $n];
			$uraian=$form_state['values']['uraian' . $n];
			
			if ($kodero=='') {
				
				if($e_kodero != 'new'){
					db_delete('rincianobyek')
				->condition('kodero',$e_kodero,'=')
				->execute();
					
				}
				
			} else {
				if($e_kodero == 'new'){
					db_insert('rincianobyek')
					->fields(array(
							'kodeo' => $kodeo,
							'kodero' => $kodero,
							'uraian' => $uraian,
							))
					->execute();
				} else {
					db_update('rincianobyek')
					->fields(array(
							'kodero' => $kodero,
							'uraian' => $uraian,
							))
				->condition('kodero',$e_kodero,'=')
				->execute();
				}
				
			}  //end kodero==''
			
		}  //end loop
		
		
	}  //end submit
		

	//drupal_goto('spmuparsip');
	//drupal_goto();

}

function gen_view_rincian($kodeo) {

//TABEL
$header = array (
	array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
	array('data' => 'Kode', 'width' => '25px','valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
);
$rows = array();

//AKUN
	$results = db_query("SELECT kodero, uraian  from {rincianobyek} where kodeo=:kodeo ORDER BY kodero", array(':kodeo'=>$kodeo));


$n = 0;
foreach ($results as $datas) {

	$n++;
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->kodero, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->uraian, 'align' => 'left', 'valign'=>'top'),
	);


}	//foreach ($results as $datas)


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

return $tabel_data;

}


?>