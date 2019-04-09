<?php
function manageuseredit_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    require './includes/password.inc';
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$kodeuk = arg(2);
				$bulan = arg(3);
				$jenisdokumen = arg(4);
				$statusjurnal = arg(5);
				$keyword = arg(6);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$kodeuk = 'ZZ';
		//$bulan = date('m');
		$bulan = '9';
		$jenisdokumen='ZZ';
		$statusjurnal = '0';
		$keyword = 'ZZ';
	}
	
	
	$output_form = drupal_get_form('manageuseredit_main_form');
	return drupal_render($output_form);
	
}




function manageuseredit_main_form($form, &$form_state) {
	$n=getrole();
	drupal_set_message($n);
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		//'#collapsible' => TRUE,
		//'#collapsed' => TRUE,        
	);		
	
	//SKPD
	if(arg(1)!=null){
		$query = db_select('users', 'p');
		# get the desired fields from the database
		$query->fields('p', array('uid','name','mail'))
				->condition('uid',arg(1), '=');
		# execute the query
		$results = $query->execute();
		# build the table fields
		//$option_skpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $name = $data->name; 
			}
		}
		$query = db_select('users_roles', 'p');
		# get the desired fields from the database
		$query->fields('p', array('uid','rid'))
				->condition('uid',arg(1), '=');
		# execute the query
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
			  $role = $data->rid; 
			}
		}
		$disable=false;
	}
	else{
		$name='';
		$role=1;
		$disable=false;
	}
	
		
	$form['formdata']['user'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Username'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#default_value' => $name,
	);
	$form['formdata']['password'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Password'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#disabled' => $disable,
	);
	$optionskpd = array();
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('kodeuk','namasingkat'))
				->orderby('namasingkat', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		//$option_skpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
				$optionskpd[$data->kodeuk] = $data->namasingkat;
			}
		}
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'Unit Kerja',
		'#options'		=> $optionskpd,
		//'#description'  => 'kodeuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> 1, 
	);
	# build the table fields
	//$option_skpd['ZZ'] = 'SELURUH SKPD'; 
	
	$option=array();
	$query = db_select('role', 'p');
	# get the desired fields from the database
	$query->fields('p', array('rid','name'))
			->orderby('rid','ASC');
	# execute the query
	$results = $query->execute();
	# build the table fields
	//$option_skpd['ZZ'] = 'SELURUH SKPD'; 
	if($results){
		foreach($results as $data) {
		  $option[$data->rid] = $data->name; 
		}
	}
	$form['formdata']['role'] = array(
		'#type' => 'select',
		'#title' =>  t('Role'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' =>$option,
		'#default_value' => $role,
	);
	$form['formdata']['uid'] = array(
		'#type' => 'value',
		'#value' =>arg(1),
		//'#default_value' => $role,
	);
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => 'Simpan',
		'#attributes' => array('class' => array('btn btn-success')),
	);
	
	
	
	return $form;
}

function manageuseredit_main_form_submit($form, &$form_state) {
	$role = $form_state['values']['role'];
	$username = $form_state['values']['user'];
	$password = $form_state['values']['password'];
	$kodeuk = $form_state['values']['kodeuk'];
	$uid = $form_state['values']['uid'];
	//$user = user_save(null, array('name'=> $username, 'pass'=>$password, 'status'=>1, 'roles' => $role));
	/*
	db_update('users')
	  ->fields(array('pass' => user_hash_password('some_password')))
	  ->condition('uid', 1)
	  ->execute();
	*/
	if($uid!=''){
		drupal_set_message($role);
		$query = db_update('users')//->extend('PagerDefault')->extend('TableSort');
		# get the desired fields from the database
		->fields(
				array(
					'name' => $username,
					'pass' => user_hash_password($password),
				)
			);
		$query->condition('uid', arg(1), '=');
		$results = $query->execute();
		$query = db_update('users_roles')//->extend('PagerDefault')->extend('TableSort');
		# get the desired fields from the database
		->fields(
				array(
					'rid' => $role,
				)
			);
		$query->condition('uid', arg(1), '=');
		$results = $query->execute();
	}
	else{
		$query=db_insert('users') // Table name no longer needs {}
			->fields(array(
			  'name' => $username,
			  'pass' => user_hash_password($password),
			))
			->execute();
		$query=db_insert('users_roles') // Table name no longer needs {}
			->fields(array(
			  'name' => $username,
			  'pass' => user_hash_password($password),
			))
			->execute();
	}
	
	/*if($result)
		drupal_set_message('Role have update');
	else
		drupal_set_message('Failed update role');
	*/
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	/*$uri = 'manageuseredit/filter/' . $kodeuk . '/' . $bulan . '/' . $jenisdokumen . '/' . $statusjurnal . '/' . $keyword;
	drupal_goto($uri);*/
	
}



?>
