<?php


function tolak_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	 


		//$btn = l('Cetak', '');
		//$btn .= l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('tolak_edit_form');
		return drupal_render($output_form);// . $output;
		 
	
}

    
function tolak_edit_form($form, &$form_state){
    /*
	$form = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	*/
    //drupal_add_css('files/css/kegiatancam.css');	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["sp2dtolaklastpage"] = $referer;
	else
		$referer = $_SESSION["sp2dtolaklastpage"];
	drupal_set_title('Penolakan SPM');

	$dokid = arg(2);	
	$alasan1 = '';
	$alasan2 = '';
	$alasan3 = '';
	$alasan4 = '';
	$alasan5 = '';
	
	$res_tolak = db_query('select alasan1,alasan2,alasan3,alasan4,alasan5 from penolakan where dokid=:dokid', array(':dokid'=>$dokid));
	foreach ($res_tolak as $datolak) {
		$alasan1 = $datolak->alasan1;
		$alasan2 = $datolak->alasan2;
		$alasan3 = $datolak->alasan3;
		$alasan4 = $datolak->alasan4;
		$alasan5 = $datolak->alasan5;
		
		//$arr_alasan = array('', $datolak->alasan1, $datolak->alasan2, $datolak->alasan3, $datolak->alasan4, $datolak->alasan5);
	}
	
	
 
	$form['dokid']= array(
		'#type'         => 'value', 
		'#title'        => 'kodeu',
		'#value'		=> $dokid, 
	); 
	
	$form['tolak_karena']= array(
		'#type'    => 'markup', 
		'#markup'        => 'Ditolak Karena', 
		//'#description'  => 'namauk', 
		//'#weight' => 3,
	); 
	$form['alasan1']= array(
		'#type'         => 'textfield', 
		'#title'        => '1.', 
		'#default_value'=> $alasan1, 
	); 
	$form['alasan2']= array(
		'#type'         => 'textfield', 
		'#title'        => '2.', 
		'#default_value'=> $alasan2, 
	); 
	$form['alasan3']= array(
		'#type'         => 'textfield', 
		'#title'        => '3.', 
		'#default_value'=> $alasan3, 
	); 
	$form['alasan4']= array(
		'#type'         => 'textfield', 
		'#title'        => '4.', 
		'#default_value'=> $alasan4, 
	); 
	$form['alasan5']= array(
		'#type'         => 'textfield', 
		'#title'        => '5.', 
		'#default_value'=> $alasan5, 
	); 
	
	$form['submit'] = array (
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Tolak',
		'#disabled' => !isSuperuser(),
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
		//'#weight' => 20,
	);
		
    return $form;
}

function tolak_edit_form_validate($form, &$form_state) {
//$kodeuk = arg(3);
//    if (!isset($kodeuk)) {
//        if (strlen($form_state['values']['kodeuk']) < 8 ) {
//            form_set_error('', 'kodeuk harus terdiri atas 8 karakter');
//        }            
//    }
}

function tolak_edit_form_submit($form, &$form_state) {
    
    
	$dokid = $form_state['values']['dokid'];
	$alasan1 = $form_state['values']['alasan1'];
	$alasan2 = $form_state['values']['alasan2'];
	$alasan3 = $form_state['values']['alasan3'];
	$alasan4 = $form_state['values']['alasan4'];
	$alasan5 = $form_state['values']['alasan5'];
	
	//drupal_set_message($header1);
	
	db_insert('penolakan')
				->fields(array('dokid', 'alasan1', 'alasan2', 'alasan3','alasan4','alasan5'))
				->values(array(
					'dokid'=> $dokid,
					'alasan1' => $alasan1,
					'alasan2' => $alasan2,
					'alasan3' => $alasan3,
					'alasan4' => $alasan4,
					'alasan5' => $alasan5,
				))
			->execute();
			
	$query = db_update('dokumen')
		->fields( 
			array(
			'spmok' => '3',
			)
	);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();
	
	drupal_goto('');
}
?>