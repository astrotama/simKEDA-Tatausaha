<?php
function upload_edit($arg=NULL, $nama=NULL) {

	echo '<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
		  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(".fancybox").fancybox({           
				  afterShow: function(){
					fancyboxRotation();
				  }
				});

			function fancyboxRotation(){
				$(".fancybox-wrap").css("webkitTransform", rotate(-90deg));
				$(".fancybox-wrap").css("mozTransform", rotate(-90deg));
			}
		</script>
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
		
		$dokid = arg(2);
		
		$num_deleted = db_delete('dokumenfile')
	  ->condition('id', arg(4))
	  ->execute();
		//echo 'HAPUS';
		
		drupal_goto('upload/' . arg(1) . '/' . $dokid);
		drupal_set_message('Gambar telah dihapus');
		
		
		
	}else{
		$output_form = drupal_get_form('upload_edit_form');
		return drupal_render($output_form);//.$output;
	}
	
}

function tabimage($data,$tot){
	$string='<script>
		//alert("rotate");
		document.getElementById("rotatetes").style.backgroundColor = "#0f0";
		alert(document.getElementById("rotatetes").style.backgroundColor);
	</script>
	</br><table align="center">';
	for($n=0;$n<$tot;$n++){
		if($n==0){
			$string.="<tr>";
		}else if($n%4==0 && $n!=0){
			$string.="</tr><tr>";
		}
		
		//ROTATE
		/*
		$string.='<td   valign="top" align="left" width="150px"><div id="rotatetes" ><img src="/files/rotate.png" height="20px" width=auto></img></div></br><a data-fancybox="gallery" href="'.$data[0][$n].'"><img  class="rotateimg" src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a></td><td width="100px">'.modal($n,'Hapus','Apakah anda akan menghapus Gambar ini ?',array('data'=>'Ya','link'=>'upload/' . arg(1) . '/'.arg(2).'/del/'.$data[1][$n]),'<img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img>').'</td>';
		*/
		
		$string.='<td   valign="top" align="left" width="150px"></br><a data-fancybox="gallery" href="'.$data[0][$n].'"><img  class="rotateimg" src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a></td><td width="100px">'.modal($n,'Hapus','Apakah anda akan menghapus Gambar ini ?',array('data'=>'Ya','link'=>'upload/' . arg(1) . '/'.arg(2).'/del/'.$data[1][$n]),'<img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img>').'</td>';
		if(($n+1)==$tot){
			$string.="</tr>";
		}
		//Hapus...............
		
		//END HARUS ..........
	}
	$string.="</table>";
	return $string;
	
}
function tabimagesp2d($data,$tot){
	$string='<script>
		//alert("rotate");
		
	</script>
	</br><table align="center">';
	for($n=0;$n<$tot;$n++){
		if($n==0){
			$string.="<tr>";
		}else if($n%4==0 && $n!=0){
			$string.="</tr><tr>";
		}
		//$editlink2=modal($data->setorid,'Hapus','Apakah anda akan menghapus data ini '.$data->uraian.'?',array('data'=>'Ya','link'=>'setorarsip/'.$data->setorid.'/delete'));
		/*$string.='<td  valign="top" width="300px"><a data-fancybox="gallery" href="'.$data[0][$n].'"><img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a><img class="del" src="http://ekir.astrotama.com/files/delete.png" width="20px" heigh="20px" ></img></td>';*/
		$string.='<td   valign="top" align="left" width="150px"><a data-fancybox="gallery" href="'.$data[0][$n].'"><img  class="rotateimg" src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a></td>';
		if(($n+1)==$tot){
			$string.="</tr>";
		}
		//Hapus...............
		
		//END HARUS ..........
	}
	$string.="</table>";
	return $string;
	
}


function tabimage_lama($data,$tot){
	$string="</br><table class='table' align='center'>";
	for($n=0;$n<$tot;$n++){
		if($n==0){
			$string.="<tr>";
		}else if($n%4==0 && $n!=0){
			$string.="</tr><tr>";
		}
		$string.='<td width="300px"><img src="'.$data[$n].'" width="150px" heigh="150px" ></img></td>';
		if(($n+1)==$tot){
			$string.="</tr>";
		}
	}
	$string.="</table>";
	return $string;
	
}

function upload_edit_form(){

	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	//drupal_set_message($referer);
	
	if (strpos($referer, 'upload'))
		$referer = $_SESSION["uploadlastpage"];
	else
		$_SESSION["uploadlastpage"] = $referer;
	
	$dokid = arg(2);

	$jenis = arg(1);
	if ($jenis=='editspj')
		$jenis='spj';
	else
		$jenis='brg';
	
	//drupal_set_message('View : ' . $jenis);
	
	$m=0;
	$res=db_query('select id,url from dokumenfile where jenis=:jenis and dokid=:dokid', array(':jenis'=>$jenis,':dokid'=>$dokid));
	
	$islink = false;
	$sudahcek = false;
	foreach($res as $dat){
		$img = str_replace('sikeda', 'simkeda', $dat->url);
		$dataimg[1][$m] = $dat->id;
		if ($islink) {
			$dataimg[0][$m] = 'http://tu18.simkedajepara.link/' . $img;
		} else {
			if ($sudahcek) {
				$dataimg[0][$m] = $img;
			} else {
				if (is_efile_exist('http://tu18.simkedajepara.net/' . $img)) {
					$dataimg[0][$m] = $img;
				} else {
					$islink = true;
					$dataimg[0][$m] = 'http://tu18.simkedajepara.link/' . $img;
				}
				$sudahcek = true;
			}
		}
		$m++;
	}
	if($m>0){
		$b=tabimage($dataimg,$m);
	}else{
		$b="<em>Tidak ada gambar</em>";
	}	

	$form['fileshow'] = array(
    	'#type' => 'item',
    	'#markup' => $b,
	);	
	
	
	$form['dokid'] = array(
    	'#type' => 'value',
    	'#value' => $dokid,
	);	
	$form['file'] = array(
    	'#type' => 'file',
    	'#title' => t('File Gambar'),
    	'#description' => t('Upload a file, allowed extensions: jpg, jpeg, png, gif'),
	);
	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Upload'),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	

  return $form;
}

function upload_edit_form_validate($form, &$form_state) {
  $file = file_save_upload('file', array(
  	'file_validate_is_image' => array(),
  	'file_validate_extensions' => array('png gif jpg jpeg'),
  ));
  if ($file) {
    if ($file = file_move($file, 'public://upload')) {
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
function upload_edit_form_file_presave($file) {
  //$parts = pathinfo($file->filename);
  //$file->filename = 'CEK';
}
function upload_edit_form_submit($form, &$form_state) {
	
	$dokid=$form_state['values']['dokid'];
	$file=$form_state['values']['file'];
	
	$jenis = arg(1);
	if ($jenis=='editspj')
		$jenis='spj';
	else
		$jenis='brg';
	 
	//drupal_set_message('Save : ' . $jenis);
	
	unset($form_state['values']['file']);
	$file->status = FILE_STATUS_PERMANENT;
	
	//$fileurl = file_create_url($file->filepath); 
	file_save($file);
	
	/*
	//drupal_set_message(t('The form has been submitted and the image has been saved, filename: @filename.', array('@filename' => $file->filename)));
	$result=db_query("select * from file_managed where filename= :filename order by uri desc limit 0,1",array(':filename'=>$file->filename));
	foreach($result as $data){
	  $uri=$data->uri;
	}
	$link=explode("//",$uri);
	//$genlink='http://tu17.simkedajepara.com/files/upload/'.$link[1];
	$genlink='/files/upload/'.$link[1];
	$genlink='/files/'.$link[1];
	*/
	
	$url = '/files' . substr($file->uri, 8);

	//drupal_set_message('g . ' . $genlink);
	//drupal_set_message('u . ' . $file->uri);
	//drupal_set_message('u . ' . $url);
	
	db_insert('dokumenfile')
	->fields(array('dokid', 'jenis', 'url'))
	->values(array(
			'dokid'=> $dokid,
			'jenis'=> $jenis,
			'url'=> $url,
	  ))
	->execute();
}

function is_efile_exist($url) {
	
	
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);

    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200) {
            $ret = true;   
        }
    }

    curl_close($curl);

    return $ret;
}

?>