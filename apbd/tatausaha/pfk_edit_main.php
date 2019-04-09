<?php
function pfk_edit_main($arg=NULL, $nama=NULL) {
	
	drupal_set_title('Rekap PFK ' . apbd_get_namabulan(arg(2)));
	$output_form = drupal_get_form('pfk_edit_main_form');
	return drupal_render($output_form);// . $output;
}

function pfk_edit_main_form($form, &$form_state) {

	$bulan = arg(2);
	//drupal_set_message($bulan);
	if ($bulan=='') $bulan = date('m');
	
	if ($bulan=='01')
		$sp2dtgl = apbd_tahun() . '-' . $bulan . '-02';
	elseif ($bulan=='06')
		$sp2dtgl = apbd_tahun() . '-' . $bulan . '-03';
	elseif ($bulan=='09')
		$sp2dtgl = apbd_tahun() . '-' . $bulan . '-02';
	elseif ($bulan=='12')
		$sp2dtgl = apbd_tahun() . '-' . $bulan . '-02';
	elseif ($bulan=='61')
		$sp2dtgl = apbd_tahun() . '-06-06';
	elseif ($bulan=='13')
		$sp2dtgl = apbd_tahun() . '-07-02';
	else
		$sp2dtgl = apbd_tahun() . '-' . $bulan . '-01';
	
	//drupal_set_message($sp2dtgl);
	
	$form['formpfk'] = array (
		'#type' => 'fieldset',
		'#title'=> 'REKAP PFK GAJI BULAN #' . $bulan . ' TAHUN ' . apbd_tahun(),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	//class="table table-striped header-fixed"
	$form['formpfk']['tablepfk']= array(
		'#prefix' => '<table class="table table-hover"><tr><th>SKPD</th><th width="90px">Gapok</th><th width="90px">Tungkel</th><th width="90px">IWP8</th><th width="90px">IWP2</th><th width="90px">Taperum</th><th width="90px">Askes</th><th width="90px">PPh</th><th width="90px">JKK</th><th width="90px">JKM</th><th width="90px">Bulat</th></tr>',
		 '#suffix' => '</table>',
	);	

	//SKPD
	$query = db_select('unitkerja', 'uk');
	$query->fields('uk', array('kodeuk', 'kodedinas', 'namasingkat'));
	$query->orderBy('uk.kodedinas');
	
	//dpq ($query);
 
	$t_gapok = 0; $t_tungkel = 0;
	$t_iwp8 = 0; $t_iwp2 = 0; 
	$t_taperum = 0; $t_askes = 0; $t_pph = 0;
	$t_jkk = 0; $t_jkm = 0; $t_bulat =0;
	
	$i = 0; 
	$result = $query->execute();
	foreach ($result as $datauk) {
		
		$uk_sudah = false;
		
		$sql = db_select('dokumen', 'd');
		$sql->fields('d', array('dokid', 'jenisgaji'));
		$sql->condition('d.sp2dok', 1, '=');
		$sql->condition('d.sp2dtgl', $sp2dtgl, '='); 
		$sql->condition('d.jenisdokumen', 3, '=');
		$sql->condition('d.kodeuk', $datauk->kodeuk, '=');
		
		
		if ($bulan=='13') {
			//$sql->condition('d.keperluan', '%13%', 'LIKE');
			$sql->condition('d.tag', '1', '=');
		} //else {
		//	$sql->condition('d.tag', '0', '=');
		//}
		
		$res_dok = $sql->execute();	
		foreach ($res_dok as $data_dok) {
		
			$i++;
			 
			$gapok = 0; $tungkel = 0;
			$iwp8 = 0; $iwp2 = 0; 
			$taperum = 0; $askes = 0; $pph = 0;
			$jkk = 0; $jkm = 0; $bulat =0;
			
			
			//REKENING
			$sql = db_select('dokumenrekening', 'di');
			$sql->fields('di', array('kodero', 'jumlah'));
			$sql->condition('di.dokid', $data_dok->dokid, '='); 
			
			$db_or = db_or();
			$db_or->condition('di.kodero', '51101001' , '=');
			$db_or->condition('di.kodero', '51101002' , '=');
			$sql->condition($db_or);
			
			//if ($datauk->kodeuk=='58') dpq ($sql);
			
			$res = $sql->execute();	
			foreach ($res as $data) {
				if ($data->kodero=='51101001')
					$gapok = $data->jumlah;
				else
					$tungkel = $data->jumlah;
			}

			//POTONGAN
			if (($gapok + $tungkel) > 0) {
				$sql = db_select('dokumenpotongan', 'di');
				$sql->fields('di', array('kodepotongan', 'jumlah'));
				$sql->condition('di.dokid', $data_dok->dokid, '='); 
				$res = $sql->execute();	
				foreach ($res as $data) {
					switch ($data->kodepotongan) {
						case '01':
							$iwp8 = $data->jumlah;
							break;
						case '02':
							$iwp2 = $data->jumlah;
							break;
						case '03':
							$taperum = $data->jumlah;
							break;
						case '04':
							$askes = $data->jumlah;
							break;
						case '05':
							$pph = $data->jumlah;
							break;
						case '06':
							$jkk = $data->jumlah;
							break;
						case '07':
							$jkm = $data->jumlah;
							break;
						case '08':
							$bulat = $data->jumlah;
							break;
							
					}
				}	
				
				//RENDER
				if ($uk_sudah) {
					$namasingkat = '';
				} else {
					$namasingkat = $datauk->namasingkat;
					$uk_sudah = true;
				}
				$form['formpfk']['tablepfk']['nomor' . $i]= array(
						'#prefix' => '<tr><td>',
						'#markup' => $namasingkat,
						//'#size' => 10,
						'#suffix' => '</td>',
				); 
				$form['formpfk']['tablepfk']['gapok' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($gapok) . '</p>', 
					'#suffix' => '</td>',
				); 			
				$form['formpfk']['tablepfk']['tungkel' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($tungkel) . '</p>', 
					'#suffix' => '</td>',
				); 			
				
				$form['formpfk']['tablepfk']['iwp8' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($iwp8) . '</p>', 
					'#suffix' => '</td>',
				); 			
				$form['formpfk']['tablepfk']['iwp2' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($iwp2) . '</p>', 
					'#suffix' => '</td>',
				); 			

				$form['formpfk']['tablepfk']['taperum' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($taperum) . '</p>', 
					'#suffix' => '</td>',
				); 			
				$form['formpfk']['tablepfk']['askes' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($askes) . '</p>', 
					'#suffix' => '</td>',
				); 			
				$form['formpfk']['tablepfk']['pph' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($pph) . '</p>', 
					'#suffix' => '</td>',
				); 			

				$form['formpfk']['tablepfk']['jkk' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($jkk) . '</p>', 
					'#suffix' => '</td>',
				); 			
				$form['formpfk']['tablepfk']['jkm' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($jkm) . '</p>', 
					'#suffix' => '</td>',
				); 			
				$form['formpfk']['tablepfk']['bulat' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<p class="text-right">' . apbd_fn($bulat) . '</p>', 
					'#suffix' => '</td></tr>',
				); 			
				
				$t_gapok += $gapok; $t_tungkel += $tungkel;
				$t_iwp8 += $iwp8; $t_iwp2 += $iwp2; 
				$t_taperum += $taperum; $t_askes += $askes; $t_pph += $pph;
				$t_jkk += $jkk; $t_jkm += $jkm; $t_bulat += $bulat;
			}
			
		}
	}	

	//TOTAL
	$i++;
	$form['formpfk']['tablepfk']['nomor' . $i]= array(
			'#prefix' => '<tr><td>',
			'#markup' => '<strong>TOTAL</strong>',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formpfk']['tablepfk']['gapok' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_gapok) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['tungkel' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_tungkel) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	
	$form['formpfk']['tablepfk']['iwp8' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_iwp8) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['iwp2' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_iwp2) . '</strong>', 
		'#suffix' => '</td>',
	); 			

	$form['formpfk']['tablepfk']['taperum' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_taperum) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['askes' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_askes) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['pph' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_pph) . '</strong>', 
		'#suffix' => '</td>',
	); 			

	$form['formpfk']['tablepfk']['jkk' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_jkk) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['jkm' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<strong class="text-right">' . apbd_fn($t_jkm) . '</strong>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['bulat' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right"><strong>' . apbd_fn($t_bulat) . '</strong></p>', 
		'#suffix' => '</td></tr>',
	); 			

	//FOOTER
	$i++;
	$form['formpfk']['tablepfk']['nomor' . $i]= array(
			'#prefix' => '<tr><td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['formpfk']['tablepfk']['gapok' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">Gapok</p>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['tungkel' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">Tungkel</p>', 
		'#suffix' => '</td>',
	); 			
	
	$form['formpfk']['tablepfk']['iwp8' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">IWP8</p>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['iwp2' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">IWP2</p>', 
		'#suffix' => '</td>',
	); 			

	$form['formpfk']['tablepfk']['taperum' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">Taperum</p>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['askes' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">Askes</p>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['pph' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">PPh</p>', 
		'#suffix' => '</td>',
	); 			

	$form['formpfk']['tablepfk']['jkk' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">JKK</p>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['jkm' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">JKM</p>', 
		'#suffix' => '</td>',
	); 			
	$form['formpfk']['tablepfk']['bulat' . $i]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right">Bulat</p>', 
		'#suffix' => '</td></tr>',
	); 			
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Tutup',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	

	return $form;
}

function pfk_edit_main_form_submit($form, &$form_state) {

drupal_goto('');

}

?>