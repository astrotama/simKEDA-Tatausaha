<?php

function verifikasi_form() {
	$dokid = arg(1);
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if ($current_url != $referer)
		$_SESSION["verifikasilastpage"] = $referer;
	else
		$referer = $_SESSION["verifikasilastpage"];
	
	//GAMBAR
	/*
	
	$m=0;
	
		$res=db_query('select kodekelengkapan id,url from dokumenkelengkapan where dokid=:dokid', array(':dokid'=>$dokid));
	
	foreach($res as $dat){
		if ($dat->url!='') {
			$img = str_replace('sikeda', 'simkeda', $dat->url);
			$dataimg[1][$m] = $dat->id;
			$dataimg[0][$m] = $img;
			
			$m++;
		}
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
	//SPP2
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {
		$nokeg++;
		//REKENING
		$form['formrekening_spp2' . $datakeg->kodekeg] = array (
			'#type' => 'fieldset',
			'#title'=> $nokeg . '. ' . $datakeg->kegiatan,
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);	
		
		
		$form['formrekening_spp2' . $datakeg->kodekeg]['spp2'] = array (
			'#type' => 'item',
			'#markup' => verifikasi_spp2($dokid, $datakeg->kodekeg),        
		);
			
	

	}		
	
	//KELENGKAPAN
	$form['KELENGKAPAN'] = array (
			'#type' => 'fieldset',
			'#title'=> 'KELENGKAPAN',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);	
	$form['KELENGKAPAN']['tablekelengkapan']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th>URAIAN</th><th width="5px">Ada</th></th><th width="50px">Valid</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	$i = 0;	
	$query = db_select('dokumenkelengkapan', 'dk');
	$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
	$query->fields('dk', array('kodekelengkapan', 'url', 'valid'));
	$query->fields('lt', array('uraian', 'tipe'));
	$query->condition('dk.dokid', $dokid, '=');
	$query->orderBy('lt.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++; 

		if ($data->tipe=='0') {
			if ($data->url=='') {
				$uraian = $data->uraian;
				$simbol = apbd_icon_belumada();
				
			} else {
				$uraian = l($data->uraian, $data->url, array('html'=>true));	
				$simbol = apbd_icon_sudahada();
			}
			
		
		} else {
			$uraian = $data->uraian;
			$simbol = apbd_icon_belumada();
		}

		$form['KELENGKAPAN']['tablekelengkapan']['kodekelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $data->kodekelengkapan,
		); 
		$form['KELENGKAPAN']['tablekelengkapan']['uraiankelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		 
		$form['KELENGKAPAN']['tablekelengkapan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['KELENGKAPAN']['tablekelengkapan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['KELENGKAPAN']['tablekelengkapan']['simbol' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $simbol, 
			'#suffix' => '</td>',
		); 
		
		$form['KELENGKAPAN']['tablekelengkapan']['valid' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'checkbox', 
			'#default_value'=> $data->valid, 
			'#suffix' => '</td></tr>',
		);	
		

	}
	
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
	*/
	
	$form['referer']= array(
		'#type' => 'value',
		'#default_value' => $referer,
	);	
	$form['dokid']= array(
		'#type' => 'value',
		'#default_value' => $dokid,
	);	
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span> Verifikasi',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		//'#disabled' => $disable_simpan,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-danger btn-sm pull-right'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	/*
	$form['submitback']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Kembali ke SPP',
		'#attributes' => array('class' => array('btn btn-danger btn-sm pull-right')),
	);
	*/
	return $form;	
}

function verifikasi_form_submit($form, &$form_state) {
	$dokid = $form_state['values']['dokid'];
	$referer = $form_state['values']['referer'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	
		/*
		$jenis = 1;
		$result = db_query('select jenisdokumen from dokumen where dokid=:dokid', array(':dokid'=>$dokid));
		foreach ($result as $data) {
			$jenis = $data->jenisdokumen;
		}
		
		for ($n=1; $n <= $jumlahrekkelengkapan; $n++) {
			$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
			$valid = $form_state['values']['valid' . $n];
			
			$query = db_update('dokumenkelengkapan')
			->fields( 
					array(
						'valid' => $valid,
					)
				);
			$query->condition('dokid', $dokid, '=');
			$query->condition('kodekelengkapan', $kodekelengkapan, '=');
			$res = $query->execute();

		}
	
		//KELENGKAPAN
		$num_deleted = db_delete('dokumenkelengkapanspm')
		  ->condition('dokid', $dokid)
		  ->execute();		
		  
		$res = db_query('select kodekelengkapan from ltkelengkapanspm where jenis=:jenis order by nomor', array(':jenis'=>$jenis));
		foreach ($res as $data) {
			db_insert('dokumenkelengkapanspm')
				->fields(array('dokid', 'kodekelengkapan'))
				->values(array(
						'dokid'=> $dokid,
						'kodekelengkapan' => $data->kodekelengkapan,
						))
				->execute();
		}				
		*/
		
		$query = db_update('dokumen')
		->fields( 
				array(
					'sppok' => '1',
				)
			);
		$query->condition('dokid', $dokid, '=');
		$res = $query->execute();
		
		drupal_goto($referer);	
		
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
		//$editlink2=modal($data->setorid,'Hapus','Apakah anda akan menghapus data ini '.$data->uraian.'?',array('data'=>'Ya','link'=>'setorarsip/'.$data->setorid.'/delete'));
		/*$string.='<td  valign="top" width="300px"><a data-fancybox="gallery" href="'.$data[0][$n].'"><img src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a><img class="del" src="http://ekir.astrotama.com/files/delete.png" width="20px" heigh="20px" ></img></td>';*/
		$string.='<td   valign="top" align="left" width="150px"></br><a href="'.$data[0][$n].'"><img  class="rotateimg" src="'.$data[0][$n].'" width="150px" heigh="150px" ></img></a></td><td width="100px">'.modal($n,'Hapus','Apakah anda akan menghapus Gambar ini ?').'</td>';
		if(($n+1)==$tot){
			$string.="</tr>";
		}
		//Hapus...............
		
		//END HARUS ..........
	}
	$string.="</table>";
	return $string;
	
}

