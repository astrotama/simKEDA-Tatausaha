<?php
function rekening_obyek_main($arg=NULL, $nama=NULL) {

	$kodej=arg(2);
	if ($kodej=='') $kodej = "522";
	$opsi=arg(3);
	if ($opsi=='') $opsi = "view";
	
	
	$output = '';
	if ($opsi=='view') {
		$output = gen_view_obyek($kodej);
		$output_form = drupal_get_form('rekening_obyek_main_form');
		return drupal_render($output_form) . $output;
		
	} else if ($opsi=='excel') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=rekening_obyek.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$outputexcel = gen_view_obyek($kodej);
		echo $outputexcel;
		
	} else if ($opsi=='input') {
		$output_form = drupal_get_form('rekening_obyek_main_form');
		return drupal_render($output_form);
		
	}
	
	
}

function rekening_obyek_main_form($form, &$form_state) {

	$kodej=arg(2);
	if ($kodej=='') $kodej = "522";
	$opsi=arg(3);
	if ($opsi=='') $opsi = "view";
	
	//drupal_set_message($kodej);
	
	$results = db_query('SELECT kodej,uraian from jenis where kodek=:kodek order by kodej', array(':kodek'=>substr($kodej, 0, 2)));
	foreach ($results as $data) {
		$options[$data->kodej]= $data->kodej . ' - ' . $data->uraian;
	}
	$form['kodej']= array(
		'#type'=>'select',
		'#options'=>$options,
		'#default_value'=>$kodej,
	);
	$form['e_kodej']= array(
		'#type'=>'value',
		'#value'=>$kodej,
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
		
			$results = db_query("SELECT kodeo, uraian from {obyek} where kodej=:kodej order by kodeo", array(':kodej'=>$kodej));
		
		$i = 0;
		foreach ($results as $data) {

			$i++; 
			$form['formdokumen']['tablerek']['e_kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodeo,
			); 
			$form['formdokumen']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['kodeo' . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#default_value' => $data->kodeo,
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
			$form['formdokumen']['tablerek']['e_kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => 'new',
			); 
			$form['formdokumen']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formdokumen']['tablerek']['kodeo' . $i]= array(
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


function rekening_obyek_main_form_validate($form, &$form_state) {
	
}
	
function rekening_obyek_main_form_submit($form, &$form_state) {

	$kodej = $form_state['values']['kodej'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['input']) {
		
		drupal_goto('rekening/obyek/' . $kodej . '/input');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['view']) {
		
		drupal_goto('rekening/obyek/' . $kodej . '/view');
		
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['excel']) {
		
		drupal_goto('rekening/obyek/' . $kodej . '/excel');

	}elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		
		$jumlahrek = $form_state['values']['jumlahrek'];
		$kodej = $form_state['values']['e_kodej'];
	
		for($n=1; $n<=$jumlahrek; $n++){

			$kodeo=$form_state['values']['kodeo' . $n];
			$e_kodeo=$form_state['values']['e_kodeo' . $n];
			$uraian=$form_state['values']['uraian' . $n];
			
			if ($kodeo=='') {
				
				if($e_kodeo != 'new'){
					db_delete('obyek')
				->condition('kodeo',$e_kodeo,'=')
				->execute();
					db_delete('rincianobyek')
				->condition('kodeo',$e_kodeo,'=')
				->execute();
				}
				
			} else {
				if($e_kodeo == 'new'){
					db_insert('obyek')
					->fields(array(
							'kodej' => $kodej,
							'kodeo' => $kodeo,
							'uraian' => $uraian,
							))
					->execute();
				} else {
					db_update('obyek')
					->fields(array(
							'kodeo' => $kodeo,
							'uraian' => $uraian,
							))
				->condition('kodeo',$e_kodeo,'=')
				->execute();
				}
				
			}  //end kodero==''
			
		}  //end loop
		
		
	}  //end submit
		

	//drupal_goto('spmuparsip');
	//drupal_goto();

}

function gen_view_obyek($kodej) {

//TABEL
$header = array (
	array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
	array('data' => 'Kode', 'width' => '20px','valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
);
$rows = array();

//AKUN
	$results = db_query("SELECT kodeo, uraian  from {obyek} where kodej=:kodej ORDER BY kodeo", array(':kodej'=>$kodej));


$n = 0;
foreach ($results as $datas) {

	$n++;
	
	$obyek =  l($datas->uraian, 'rekening/rincian/' . $datas->kodeo, array('attributes' => array('class' => null)));
	
	$rows[] = array(
		array('data' => $n, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->kodeo, 'align' => 'left', 'valign'=>'top'),
		array('data' => $obyek, 'align' => 'left', 'valign'=>'top'),
	);


}	//foreach ($results as $datas)


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

return $tabel_data;

}


?>