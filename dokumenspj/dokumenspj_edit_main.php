<?php

function dokumenspj_edit_main($arg=NULL, $nama=NULL) {

	echo '<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
		  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		
		<style>
		
		img{
			 opacity: 0.75;
		}
		img:hover{
			 opacity: 1;
			 cursor: pointer;
		}
		img.fancybox-image{
			opacity: 1;
		}
		img.del{
			opacity: 1;
			
		}
		td{
			color:red;
			vertical-align:top;
		}
		</style>
		'
		;

	if(arg(3)=='del'){		
		$num_deleted = db_delete('dokumenfile')
	  ->condition('id', arg(4))
	  ->execute();
		//echo 'HAPUS';
		drupal_goto('dokumenspjarsip');
		drupal_set_message('Gambar telah dihapus');
	}else{
		$output_form = drupal_get_form('dokumenspj_edit_main_form');
		return drupal_render($output_form);//.$output;
	}
	
	
}

function dokumenspj_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	//drupal_set_message($referer);
	
	if (strpos($referer, 'dokumenspj/edit'))
		$referer = $_SESSION["dokumenspjeditlastpage"];
	else
		$_SESSION["dokumenspjeditlastpage"] = $referer;

	
	$dokspjid = arg(2);
	$results = db_query('SELECT tglawal, tglakhir, uraian FROM {dokumenspj} WHERE dokspjid=:dokspjid', array(':dokspjid'=>$dokspjid));
	foreach ($results as $data) {
		
		$tglawal = dateapi_convert_timestamp_to_datetime($data->tglawal);
		$tglakhir = dateapi_convert_timestamp_to_datetime($data->tglakhir);
		$uraian = $data->uraian;
		
	}
	
	$m=0;
	$res=db_query('select id,url from dokumenfile where jenis=:jenis and dokid=:dokid', array(':jenis'=>'bk8',':dokid'=>$dokspjid));
	
	foreach($res as $dat){
		$img = str_replace('sikeda', 'simkeda', $dat->url);
		$dataimg[1][$m] = $dat->id;
		$dataimg[0][$m] = $img;
		
		$m++;
	}
	if($m>0){
		$b=tabimage($dataimg,$m);
	}else{
		$b="<em>Tidak ada gambar</em>";
	}	

	$form['dokspjid'] = array(
		'#type' => 'value',
		'#value' => $dokspjid,
	);	
	


	$form['tglawal_title'] = array(
		'#markup' => 'Mulai Tanggal',
		);
	$form['tglawal']= array(
		'#type' => 'date_select',
		'#default_value' => $tglawal, 
			
		'#date_format' => 'd-m-Y',
		'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		//'#description' => 'Tanggal',
	);
	$form['tglakhir_title'] = array(
		'#markup' => 'Sampai Tanggal',
		);
	$form['tglakhir']= array(
		'#type' => 'date_select',
		'#default_value' => $tglakhir, 
			
		'#date_format' => 'd-m-Y',
		'#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		'#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		//'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		'#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		//'#description' => 'Tanggal',
	);
	$form['uraian'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Uraian'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $uraian,
	);

	$form['gambar'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Gambar BK8',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
    //pilih gambar
	$form['gambar']['fileshow'] = array(
    	'#type' => 'item',
    	//'#title' => t('Image'),
    	'#markup' => $b,
	);		
	$form['gambar']['file'] = array(
    	'#type' => 'file',
    	'#title' => t('Input Gambar'),
    	'#description' => t('Upload gambar, file gambar yang diizinkan: jpg, jpeg, png, gif'),
	);
	$form['gambar']['submitgambar'] = array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"> Upload</span>',
			'#attributes' => array('class' => array('')),
			

	);
	
	//FORM SUBMIT DECLARATION
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		//'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function dokumenspj_edit_main_form_validate($form, &$form_state) {
if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambar']) {

	$file = file_save_upload('file', array(
		'file_validate_is_image' => array(),
		'file_validate_extensions' => array('png gif jpg jpeg'),
		));
	if ($file) {
		if ($file = file_move($file, 'public://upload')) {
			$form_state['values']['file'] = $file;
		} else {
			form_set_error('file', t('Failed to write the uploaded file the site\'s file folder.'));
		}
	}
	else {
		form_set_error('file', t('No file was uploaded.'));
	}
  
}
}

function dokumenspj_edit_main_form_submit($form, &$form_state) {

$dokspjid = $form_state['values']['dokspjid'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitgambar']) {

  $file=$form_state['values']['file'];
  unset($form_state['values']['file']);
  $file->status = FILE_STATUS_PERMANENT;
  //$fileurl = file_create_url($file->filepath);
  file_save($file);
  //drupal_set_message(t('The form has been submitted and the image has been saved, filename: @filename.', array('@filename' => $file->filename)));
  $result=db_query("select * from file_managed where filename= :filename order by uri desc limit 0,1",array(':filename'=>$file->filename));
  foreach($result as $data){
	  $uri=$data->uri;
  }
  $link=explode("//",$uri);
  $genlink='http://tatausaha.simkedajepara.net/files/'.$link[1];
  
  //drupal_set_message($genlink);
  db_insert('dokumenfile')
	->fields(array('dokid', 'url', 'jenis'))
	->values(array(
			'dokid'=> $dokspjid,
			'url'=> $genlink,
			'jenis'=> 'bk8',
	  ))
	->execute();

	
} else {
	
	$tglawal = dateapi_convert_timestamp_to_datetime($form_state['values']['tglawal']);
	$tglakhir = dateapi_convert_timestamp_to_datetime($form_state['values']['tglakhir']);

	$uraian = $form_state['values']['uraian'];

	$query = db_update('dokumenspj')
				->fields( 
					array(
					'tglawal' => $tglawal,
					'tglakhir' =>$tglakhir,
					'uraian' => $uraian, 
					)
				);
	$query->condition('dokspjid', $dokspjid, '=');
	$res = $query->execute();

}
}


function tabimage($data,$tot){
	$string="</br><table align='center'>";
	for($n=0;$n<$tot;$n++){
		if($n==0){
			$string.="<tr>";
		}else if($n%4==0 && $n!=0){
			$string.="</tr><tr>";
		}
		//$editlink2=modal($data->setorid,'Hapus','Apakah anda akan menghapus data ini '.$data->uraian.'?',array('data'=>'Ya','link'=>'setorarsip/'.$data->setorid.'/delete'));
		/*$string.='<td  valign="top" width="300px"><a data-fancybox="gallery" href="'.$data[0][$n].'"><img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a><img class="del" src="http://ekir.astrotama.com/files/delete.png" width="20px" heigh="20px" ></img></td>';*/
		$string.='<td   valign="top" align="left" width="150px"><a data-fancybox="gallery" href="'.$data[0][$n].'"><img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a></td><td width="100px">'.modal($n,'Hapus','Apakah anda akan menghapus Gambar ini ?',array('data'=>'Ya','link'=>'upload/edit/'.arg(2).'/del/'.$data[1][$n]),'<img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img>').'</td>';
		if(($n+1)==$tot){
			$string.="</tr>";
		}
		//Hapus...............
		
		//END HARUS ..........
	}
	$string.="</table>";
	return $string;
	
}


?>