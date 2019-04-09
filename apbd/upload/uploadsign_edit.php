<?php
function uploadsign_edit($arg=NULL, $nama=NULL) {

	$ada = true;
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		if ($kodeuk=='') $kodeuk = '00';
		
		$ada = false;
		$result=db_query('select kodeuk from dokumensigner where kodeuk=:kodeuk',array(':kodeuk'=>$kodeuk));
		foreach($result as $data){
		  $ada = true;
		}
	}
	
	if ($ada) {
		drupal_goto('');	
	} else {
		$output_form = drupal_get_form('uploadsign_edit_form');
		return drupal_render($output_form);//.$output;
	}

	
}

function uploadsign_edit_form(){

	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'uploadsign'))
		$referer = $_SESSION["uploadsignlastpage"];
	else
		$_SESSION["uploadsignlastpage"] = $referer;
	

	$form['referer'] = array(
    	'#type' => 'value',
    	'#default_value' => $referer,
	);	
 
	$form['keterangan1'] = array(
    	'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#type' => 'item',
    	'#markup' => '<p>Mohon upload file setting <abbr title="Program untuk menandatangani eSPM"><strong>eSPM Signer</strong></abbr> untuk di-update agar mengarah ke server yang baru. Setelah di-update, file setting eSPM ini harus ditempatkan di komputer Pengguna Anggaran agar program <strong>eSPM Signer</strong> bisa membaca dan menandatangani eSPM di server yang baru.</p><p></p>',
	);
	$form['keterangan2'] = array(
    	'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#type' => 'item',
    	'#markup' => 'Untuk meng-upload Seting eSPM Signer, ikut langkah sebagai sebagai berikut:<ol><li>Klik <strong>Choose file</strong></li>
<li>Pilih Drive <strong>C</strong>.</li><li>Lalu Pilih folder <strong>Program Files (x86)</strong>. Bila folder Program Files (x86) tidak ada, pilih Program Files.</li><li>Lalu Pilih folder <strong>Default Company Name</strong>.</li>
<li>Lalu Pilih folder <strong>Setup eSPM Signer</strong>.</li><li>Pilih file <strong>setting spm.json</strong>.</li>
<li>Kemudian klik <strong>Upload</strong>.</li><li>Selesai.</li><li>Pengiriman/Upload terakhir pada hari Jumat, tanggal 27 Juli 2018.</li></ol>
<p></p>',
	);
	
	$form['file'] = array(
    	'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#type' => 'file',
    	//'#title' => t('File Gambar'),
    	'#description' => t('Upload file setting eSPM Signer dengan mengllik tombol Choose file.'),
	);
	$form['submit'] = array(
		'#prefix' => '<div class="col-md-4">',	
		'#type' => 'submit',
		'#value' => '<span class="btn btn-primary btn-sm">Upload</span>',
		//'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-primary'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a></div>",
		'#suffix' => '</div>',
		
	);

	$form['keterangan3'] = array(
    	'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#type' => 'item',
    	'#markup' => '<blockquote>Perhatian</blockquote><p>Yang di-upload adalah file setting json, bukan file sertifikat digital. File sertifikat digital tidak boleh diberikan kepada pihak lain.</p><p></p>',
	);
	
  return $form;
}

function uploadsign_edit_form_validate($form, &$form_state) {
  /*
  $file = file_save_upload('file', array(
  	'file_validate_is_image' => array(),
  	'file_validate_extensions' => array('json'),
  ));
  */
  $file = file_save_upload('file', array(
  	'file_validate_extensions' => array('json'),
  ));  if ($file) {
    if ($file = file_move($file, 'public://json')) {
      $form_state['values']['file'] = $file;
    }
    else {
      form_set_error('file', t('Failed to write the uploaded file the site\'s file folder.'));
    }
  }
  else {
    form_set_error('file', t('No file was uploaded.'));
  }
}
function uploadsign_edit_form_file_presave($file) {
  //$parts = pathinfo($file->filename);
  //$file->filename = 'CEK';
}
function uploadsign_edit_form_submit($form, &$form_state) {
	
	
	$file = $form_state['values']['file'];
	
	$referer = $form_state['values']['referer'];
	$kodeuk = apbd_getuseruk();
	if ($kodeuk=='') $kodeuk = '00';
	
	
	unset($form_state['values']['file']);
	$file->status = FILE_STATUS_PERMANENT;
	file_save($file);

	$url = substr($file->uri, 8);
	
	//drupal_set_message($url);
	
	db_insert('dokumensigner')
	->fields(array('kodeuk', 'url'))
	->values(array(
			'kodeuk'=> $kodeuk,
			'url'=> $url,
	  ))
	->execute();
	
	drupal_set_message('File setting eSPM berhasil di-upload. Terimaskasih.');

	
	//drupal_goto('');

}
?>