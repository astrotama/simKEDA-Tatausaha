<?php
function usulan_upload_edit($arg=NULL, $nama=NULL) {

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
		
		/* $dokid = arg(2);
		
		$num_deleted = db_delete('dokumenfile')
	  ->condition('id', arg(4))
	  ->execute();
		//echo 'HAPUS';
		
		drupal_goto('upload/' . arg(1) . '/' . $dokid);
		drupal_set_message('Gambar telah dihapus'); */
		
		
		
	}else{
		$output_form = drupal_get_form('usulan_upload_edit_form');
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

function usulan_upload_edit_form(){

	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'upload'))
		$referer = $_SESSION["uploadlastpage"];
	else
		$_SESSION["uploadlastpage"] = $referer;
	
	$spjid = arg(2);
	$istu = arg(3);
	
	$spjlink = '';
	if ($istu=='')
		$res=db_query('select spjlink from spjgu where spjid=:spjid', array(':spjid'=>$spjid));	
	else
		$res=db_query('select spjlink from spjtu where spjid=:spjid', array(':spjid'=>$spjid));	
	
	//drupal_set_message($istu);
	
	foreach($res as $dat){
		$spjlink = $dat->spjlink;
	}

	
	/* $m=0;
	$b = '';
	$res=db_query('select id,url from dokumenfile where dokid=:dokid', array(':dokid'=>$dokid));	
	foreach($res as $dat){
		$img = str_replace('sikeda', 'simkeda', $dat->url);
		$dataimg[1][$m] = $dat->id;
		$dataimg[0][$m] = $img;
		
		$m++;
	}
	if($m>0){
		$b = tabimage($dataimg,$m);
	}	 */

	$form['fileshow'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',

    	'#type' => 'item',
    	'#markup' => $b,
	);	
	
	$form['spjid'] = array(
    	'#type' => 'value',
    	'#value' => $spjid,
	);
	$form['istu'] = array(
    	'#type' => 'value',
    	'#value' => $istu,
	);
	
	/*
	$form['grid']= array(
		'#prefix' => '<div class="col-md-8">',
		'#suffix' => '</div>',
	);	
	*/

	$form['fileshow1'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
	
    	'#type' => 'item',
    	'#markup' => $spjlink,
	);		
	$form['spjlink'] = array(
		'#prefix' => '<div class="col-md-8">',
		'#suffix' => '</div>',
		'#type'         => 'textarea', 
		'#title' =>  t('Link Gambar SPJ'),
		'#default_value' =>  $spjlink,
    	//'#description' => t('Isikan URL Gambar SPJ yang telah di-upload kesini, untuk upload gambar, klik tombol Upload Gambar dibawah.'),
	); 
	 $form['doklink'] = array(
		'#prefix' => '<div class="col-md-4">',
		'#suffix' => '</div>',
		'#type'         => 'item', 
		'#title' =>  'Klik untuk Upload Gambar SPJ',
		'#markup' =>  "</br><a href='http://simkeda.online' target='_blank' class='btn btn-info '><span class='glyphicon glyphicon-upload' aria-hidden='true'></span>Upload Gambar SPJ</a>",
		//'#required' => TRUE,
	);
	$form['submit'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#type' => 'submit',
		'#value' => t('Simpan'),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	$form['manlinkspace'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#type'         => 'item', 
		//'#title' =>  'Klik untuk Upload Gambar SPJ',
		'#markup' =>  '<p></p><p></p>',
		//'#required' => TRUE,
	);
	$form['manlink'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#type'         => 'item', 
		//'#title' =>  'Klik untuk Upload Gambar SPJ',
		'#markup' =>  getmanual(),
		//'#required' => TRUE,
	); 
  return $form;
}

function usulan_upload_edit_form_submit($form, &$form_state) {
	//$spjid = arg(2);
	$spjid = $form_state['values']['spjid'];
	$istu = $form_state['values']['istu'];
	$spjlink = $form_state['values']['spjlink'];
		
	// $query = db_update('spjgu')
				// ->fields( 
					// array(
						// 'spjlink' => $spjlink,
					// )
				// );
	// $query->condition('spjid', $spjid, '=');
	if ($istu=='')
		db_query("update spjgu set spjlink=:spjlink where spjid=:spjid", array(':spjid' => $spjid, ':spjlink' => $spjlink));
	else
		db_query("update spjtu set spjlink=:spjlink where spjid=:spjid", array(':spjid' => $spjid, ':spjlink' => $spjlink));
		
	// $res = $query->execute();
	//drupal_set_message($query);
}

function getmanual() {
$str = '<ul class="list-group">' . 
  '<li class="list-group-item">1) Klik Upload Gambar SPJ, anda akan masuk di Sistem Pengelolaan Dokumen Online simKEDA.</li>' . 
  '<li class="list-group-item">2) Klik <b>Masuk</b>, lalu masukkan Username dan Password. Username sama dengan Username di simKEDA, sedangkan Password, isikan 11111!!!!!.</li>' . 
  '<li class="list-group-item"><img src="http://simkeda.online/images/2018/10/24/Man-LayarDepan1a243ce487ee6358.md.jpg" alt="Man-LayarDepan1a243ce487ee6358.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item">3) Klik nama user anda (pojik kanan atas), lalu klik <b>Album</b>.<a href="http://simkeda.online/image/ypS2"></li>' . 
  '<li class="list-group-item"><img src="http://simkeda.online/images/2018/10/24/Man-PilihMenuAlbumb2d782659554c456.md.jpg" alt="Man-PilihMenuAlbumb2d782659554c456.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item">4) Buat Album baru. <b>Anda harus membuat satu album untuk satu SPP</b>.</li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/LZ1e"><img src="http://simkeda.online/images/2018/10/24/Man-BuatAlbum1e8d68f6c3d0b65d8.md.jpg" alt="Man-BuatAlbum1e8d68f6c3d0b65d8.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item">5) Isikan Nama Album. Lalu Klik <b>Simpan Perubahan</b> untuk menyimpan.</li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/LGCt"><img src="http://simkeda.online/images/2018/10/24/Man-BuatAlbum2dc0f6e71f34304b7.md.jpg" alt="Man-BuatAlbum2dc0f6e71f34304b7.md.jpg" border="0"></a>
</li>' . 
  '<li class="list-group-item">6) Unggah gambar. Anda bisa mengunggah gambar sebanyak yang diinginkan. Unggah gambar bisa dilakukan langsung untuk beberapa gambar.</li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/U45r"><img src="http://simkeda.online/images/2018/10/24/Man-Unggah108557a4608d58cfb.md.jpg" alt="Man-Unggah108557a4608d58cfb.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/UKik"><img src="http://simkeda.online/images/2018/10/24/Man-Unggah27fc3699b9716eb60.md.jpg" alt="Man-Unggah27fc3699b9716eb60.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/fWGL"><img src="http://simkeda.online/images/2018/10/24/Man-Unggah31f4d6a998f75ab2b.md.jpg" alt="Man-Unggah31f4d6a998f75ab2b.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item">7) Copy/Salin link Tautan gambar kecil HTML.</li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/M7tt"><img src="http://simkeda.online/images/2018/10/24/Man-TautanKecilf52ba4260df472fa.md.jpg" alt="Man-TautanKecilf52ba4260df472fa.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/MOIF"><img src="http://simkeda.online/images/2018/10/24/Man-TautanCopy61d48287d8608f2e.md.jpg" alt="Man-TautanCopy61d48287d8608f2e.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item">8) Paste link di Link Gambar SKP.</li>' . 
  '<li class="list-group-item"><a href="http://simkeda.online/image/A29n"><img src="http://simkeda.online/images/2018/10/24/Man-Simpanfe5458610d8ecd7e.md.jpg" alt="Man-Simpanfe5458610d8ecd7e.md.jpg" border="0"></a></li>' . 
  '<li class="list-group-item">9) Simpan</li>' . 
'</ul>'	;

return $str;
}

?>