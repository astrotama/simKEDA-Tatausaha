<?php
function tunihilspp_newspj_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$kodekeg = arg(2);	
	if(arg(3)=='pdf'){		
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('tunihilspp_newspj_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function tunihilspp_newspj_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	/*$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjgajilastpage"] = $referer;
	else
		$referer = $_SESSION["spjgajilastpage"];*/
	
	db_set_active('bendahara');
	$kodekeg = arg(2);
	$kodeuk = substr($kodekeg, 4,2);
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);	
	
	$form['formdokumen']['tablespj']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">No.</th><th width="100px">No. SPJ</th><th width="100px">Tanggal</th><th>Keperluan</th><th>Penerima</th><th width="100px">Jumlah</th><th width="50px">Pilih</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	//$query = db_query('SELECT bendid, spjno, tanggal, keperluan, total FROM `bendahara`  WHERE sudahproses=0 and kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));

	$query = db_select('bendahara' . $kodeuk, 'b');

	# get the desired fields from the database
	//$query->fields('b', array('bendid',  'spjno', 'tanggal', 'keperluan', 'penerimanama', 'total'));
	$query->fields('b', array('bendid',  'spjno', 'jenis', 'tanggal', 'keperluan', 'penerimanama', 'total', 'sudahproses'));
	$query->condition('b.kodekeg', $kodekeg, '=');
	$query->condition('b.sudahproses', 0, '=');

	$query->condition('b.jenis', 'tu-spj' , '=');
	
	$query->orderBy('b.tanggal', 'ASC');
	
	//$query->range(0,10);
	
	dpq ($query);

	# execute the query
	$results = $query->execute();

	foreach ($results as $data) {

		$i++; 
		$spjno = $data->spjno;
		$tanggal = $data->tanggal;
		$keperluan = $data->keperluan;
		$penerimanama = $data->penerimanama;
		$jumlah = $data->total;
		
		if ($data->jenis=='pindahbuku') {
			$resx = db_query('select sum(jumlah) as jumlahnya from {bendaharaitem } where bendid=:bendid and jumlah>0', array(':bendid'=>$data->bendid));
			foreach ($resx as $datax) {
				$jumlah = $datax->jumlahnya;
			}
		}
		
		if ($data->sudahproses)
			$sudahproses = 'Sudah';
		else
			$sudahproses = 'Belum';
		
		$form['formdokumen']['tablespj']['bendid' . $i]= array(
				'#type' => 'value',
				'#value' => $data->bendid,
		); 
		 
		$form['formdokumen']['tablespj']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['spjno' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $spjno,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['tanggal' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_fd($tanggal),
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['uraian' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $keperluan,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['penerimanama' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $penerimanama,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdokumen']['tablespj']['jumlah' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p align="right">' . apbd_fn($jumlah) . '</p>',
				//'#size' => 10,
				'#suffix' => '</td>',
		);
		$form['formdokumen']['tablespj']['pilih' . $i]= array(
			'#type'         => 'checkbox', 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	

	}
	db_set_active();
	$form['formdokumen']['jumlahdok']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
    
	//CETAK BAWAH
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> SPP',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}

function tunihilspp_newspj_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function tunihilspp_newspj_main_form_submit($form, &$form_state) {
	$kodekeg = $form_state['values']['kodekeg'];
	
	$jumlahdok = $form_state['values']['jumlahdok'];
	$str='';$item=0;
	for($n=1;$n<=$jumlahdok;$n++){
		//$str='CEK';
		//$str.=$form_state['values']['bendid' . $n];
		if($form_state['values']['pilih' . $n]!=0){
			$str.=$form_state['values']['bendid' . $n];
			if($n!=$jumlahdok)$str.=',';
			//$item++;
		}
		
		
	}
	$_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]=$str;
	
	//drupal_set_message($_SESSION['bendid']);

    drupal_goto('tunihilspp/new/' . $kodekeg);

}



?>
