<?php

function form_cetak_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('form_cetak_main_form');

	return drupal_render($output_form);
}

function form_cetak_main_form($form, &$form_state) {

	$dokid = arg(2);
	$print = arg(3);

	//FORM NAVIGATION
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["gucetaklastpage"] = $referer;
	else
		$referer = $_SESSION["gucetaklastpage"];
	//drupal_set_message($referer);

	$query = db_select('dokumen', 'd');
	$query->fields('d', array('keperluan'));

	$query->condition('d.dokid', $dokid, '=');

	//dpq($query);

	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {

		$title = 'SPP GU ' . $data->keperluan ;

	}

	drupal_set_title($title);

	//CETAK ATAS
	$gambar=0;
	$res=db_query("select url from dokumenfile where dokid=:dokid",array(":dokid"=>arg(2)));
	foreach($res as $dat){
		$gambar++;
	}
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);
	//CETAK BAWAH
	$form['formdata']['submitsppa21']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' =>  '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> A2-1</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitspp3']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' =>  ' <h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 3</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitspp2']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 2</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitspp1']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 1</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitsppkelengkapan']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Kelengkapan</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitsppsp']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span>Pernyataan</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitsppket']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Keterangan</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);

	$form['formdata']['submitpernyataan1']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Surat 1</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitpernyataan2']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Surat 2</h4>',
		'#attributes' => array('class' => array('btn btn-success  btn-block')),
	);
	$form['formdata']['submitpernyataan3']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Surat 3</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitpernyataan4']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Surat 4</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submitpernyataan5']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Surat 5</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['rincian']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Rincian</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['pernyataan']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Pernyataan</h4>',
		'#attributes' => array('class' => array('btn btn-success btn-block')),
	);
	$form['formdata']['submdsitback']= array(
		'#type' => 'item',
		'#prefix' => '<div class="col-md-9">',
		'#suffix' => '</div>',
	);
	$form['formdata']['referer']= array(
		'#type' => 'value',
		'#default_value' => $referer,
	);
	$form['formdata']['submitback']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-3">',
		'#suffix' => '</div>',
		'#value' => '<h4><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP</h4>',
		'#attributes' => array('class' => array('btn btn-danger btn-block pull-right')),
	);

	return $form;
}



function form_cetak_main_form_submit($form, &$form_state) {

$dokid = $form_state['values']['dokid'];
$referer = $form_state['values']['referer'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp1']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/spp1');

} else  if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp2']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/spp2');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitspp3']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/spp3');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppkelengkapan']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sppkelengkapan');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppsp']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/sp');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppket']) {
	drupal_goto('gubaruspp/edit/' . $dokid . '/ket');

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitsppa21']) {
	drupal_goto('kuitansi/edit/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan1']) {
	drupal_goto('laporan/surat_pernyataan1/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan2']) {
	drupal_goto('laporan/surat_pernyataan2/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan3']) {
	drupal_goto('laporan/surat_pernyataan3/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan4']) {
	drupal_goto('laporan/surat_pernyataan4/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpernyataan5']) {
	drupal_goto('laporan/surat_pernyataan5/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['rincian']) {
	drupal_goto('laporan/rincian/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['pernyataan']) {
	drupal_goto('laporan/pernyataan/' . $dokid);

} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitback']) {
	drupal_goto($referer);
}
}
