<?php
function tunihilspp_new_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$kodekeg = arg(2);	
	if(arg(3)=='pdf'){		
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		//$output = printspm($kodekeg);
		//apbd_ExportSPM($output, 'SPM', $url);
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('tunihilspp_new_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function tunihilspp_new_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	/*$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjguitemlastpage"] = $referer;
	else
		$referer = $_SESSION["spjguitemlastpage"];*/
	
	$kodekeg = arg(2);
	//drupal_set_message($kodekeg);
	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	

	$no = 0;
	$spptgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());	

	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);			
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);			
	
	$form['sppno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => '',
	);
	$form['spptgl'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value'=> array(
			'year' => format_date($spptgl, 'custom', 'Y'),
			'month' => format_date($spptgl, 'custom', 'n'), 
			'day' => format_date($spptgl, 'custom', 'j'), 
		  ), 
		
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);


	//BENDAHARA
	//'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('namasingkat', 'bendaharanama', 'bendaharanip', 'bendahararekening', 'bendaharabank', 'bendaharanpwp'));
	$query->condition('u.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$penerimanama = 'BENDAHARA PENGELUARAN ' . $data->namasingkat . ' (' . $data->bendaharanama . ')';
		$penerimanip = $data->bendaharanip;
		$penerimabankrekening = $data->bendahararekening;
		$penerimabanknama = $data->bendaharabank;
		$penerimanpwp = $data->bendaharanpwp;
	}
	$query=db_query("SELECT distinct kodeuk,penerimanama FROM `dokumen` where (penerimanama like 'bend pengel%' or penerimanama like 'bendpengel%' or penerimanama like 'upt dis%' ) and kodeuk=:kodeuk and jurnalsudah=1",array(':kodeuk'=>$kodeuk));
	foreach($query as $data){
		$penerimanama=$data->penerimanama;
	};

	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'BENDAHARA',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanama, 
		);				
		$form['formpenerima']['penerimanip']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NIP'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanip, 
		);				
		$form['formpenerima']['penerimabankrekening']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Rekening'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabankrekening, 
		);				
		$form['formpenerima']['penerimabanknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Bank'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabanknama, 
		);				
		$form['formpenerima']['penerimanpwp']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NPWP'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanpwp, 
		);			

	//KELENGKAPAN
	$form['formkelengkapan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KELENGKAPAN DOKUMEN',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formkelengkapan']['tablekelengkapan']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th><th width="50px">Tidak</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('ltkelengkapandokumen', 'dk');
	$query->fields('dk', array('kodekelengkapan', 'uraian', 'nomor'));
	$query->condition('dk.jenis', 1, '=');
	$query->orderBy('dk.nomor', 'ASC');
	$results = $query->execute();
	foreach ($results as $data) {

		$i++; 
		$kode = $data->kodekelengkapan;
		$uraian = $data->uraian;
		$form['formkelengkapan']['tablekelengkapan']['kodekelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraiankelengkapan' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formkelengkapan']['tablekelengkapan']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formkelengkapan']['tablekelengkapan']['adakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> '0', 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		
		$form['formkelengkapan']['tablekelengkapan']['tidakadakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> '0', 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		

	}
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);		
	
	//REKENING	
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		//'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
		'#title'=> 'REKENING',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	
	$form['formrekening']['tablerekening']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="150px">JUMLAH</th><th width="150px">KETERANGAN</th></tr>',
		 '#suffix' => '</table>',
	);	

	$i = 0;
	//drupal_set_message($_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]);
	$bendid=explode(',',$_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]);
	db_set_active('bendahara');
	//$query = db_query('select distinct bi.kodero as kr ,ro.uraian, (select sum(b.jumlah) from bendaharaitem as b where b.kodero=kr) as jumlah from bendaharaitem as bi inner join bendahara as b on bi.bendid=b.bendid inner join rincianobyek as ro on ro.kodero=bi.kodero where b.bendid in( :bendid ) order by kr', array(':bendid'=>$bendid));

	$query = db_query('select bi.kodero as kr ,ro.uraian, sum(bi.jumlah) as jumlahnya from bendaharaitem' . $kodeuk . ' as bi inner join rincianobyek as ro on bi.kodero=ro.kodero where bi.bendid in (:bendid ) group by bi.kodero, ro.uraian order by bi.kodero', array(':bendid'=>$bendid));

	foreach ($query as $data) {

		$i++; 
		$kode = $data->kr;
		$uraian = $data->uraian;
		$jumlah = $data->jumlahnya;
		//$tidakada = $data->tidakada;
		
		$form['formrekening']['tablerekening']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		
		$form['formrekening']['tablerekening']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['tablerekening']['kode' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kode,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['tablerekening']['uraian' . $i]= array(
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['tablerekening']['jumlahapbd' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#default_value'=> $jumlah, 
			'#suffix' => '</td>',
		);	
		$form['formrekening']['tablerekening']['keterangan' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#size' => 25,
			'#default_value'=> '', 
			'#suffix' => '</td></tr>',
		);	
		
	}
	$form['formrekening']['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
	
	db_set_active();


	//SIMPAN
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function tunihilspp_new_main_form_validate($form, &$form_state) {
	//$spjno = $form_state['values']['spjno'];
	//if (($spjno=='') or ($spjno=='BARU')) form_set_error('spjno', 'Nomor SPJ harap diisi dengan benar');
		
}
	
function tunihilspp_new_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$sppno = $form_state['values']['sppno'];
	$spptgl = $form_state['values']['spptgl'];
	$spptglsql = $spptgl['year'] . '-' . $spptgl['month'] . '-' . $spptgl['day'];

	$keperluan = $form_state['values']['keperluan'];
	$jenisgaji = $form_state['values']['jenisgaji'];

	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimanip = $form_state['values']['penerimanip'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	//PPTK
	$pptknama = '-';
	$pptknip = '-';
	
	$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
	$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
	
	//BEGIN TRANSACTION
	//$transaction = db_transaction();
	
	//DOKUMEN
	//try {
		$dokid = apbd_getkodedokumen($kodeuk);
		drupal_set_message($dokid);

		//KELENGKAPAN
		$num_deleted = db_delete('dokumenkelengkapan')
		  ->condition('dokid', $dokid)
		  ->execute();		
		
		for ($n=1; $n <= $jumlahrekkelengkapan; $n++){
			$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
			$ada = $form_state['values']['adakelengkapan' . $n];
			$tidakada = $form_state['values']['tidakadakelengkapan' . $n];
			
			db_insert('dokumenkelengkapan')
				->fields(array('dokid', 'kodekelengkapan', 'ada', 'tidakada'))
				->values(array(
						'dokid'=> $dokid,
						'kodekelengkapan' => $kodekelengkapan,
						'ada' => $ada,
						'tidakada' => $tidakada,
						))
				->execute();
		}		
		
		//REKENING
		$num_deleted = db_delete('dokumenrekening')
		  ->condition('dokid', $dokid)
		  ->execute();			
		$totaljumlah = 0;
		for ($n=1; $n <= $jumlahrekrekening; $n++){
			$kodero = $form_state['values']['koderoapbd' . $n];
			$jumlah = $form_state['values']['jumlahapbd' . $n];
			
			db_insert('dokumenrekening')
				->fields(array('dokid', 'kodero', 'jumlah'))
				->values(array(
						'dokid'=> $dokid,
						'kodero' => $kodero,
						'jumlah' => $jumlah,
						))
				->execute();
			
			$totaljumlah = $totaljumlah + $jumlah;
		}

		//DOKUMEN
		$query = db_insert('dokumen')
				->fields(array('dokid', 'bulan', 'kodekeg', 'jenisdokumen', 'kodeuk', 'sppno', 'spptgl', 'keperluan', 
								'jumlah', 'netto', 'penerimanama', 'penerimanip', 'penerimabanknama', 
								'penerimabankrekening', 'penerimanpwp', 'pptknama', 'pptknip'))
				->values(
					array(
						'dokid'=> $dokid,
						'bulan'=> date('m'),
						'kodekeg' => $kodekeg,
						'jenisdokumen' => 7,
						'kodeuk' => $kodeuk,
						'sppno' => $sppno,
						'spptgl' =>$spptglsql,
						'keperluan' => $keperluan, 
						'jumlah' => $totaljumlah,
						'netto' => $totaljumlah,
						'penerimanama' => $penerimanama,
						'penerimanip' => $penerimanip, 
						'penerimabanknama' => $penerimabanknama,
						'penerimabankrekening' => $penerimabankrekening,
						'penerimanpwp' => $penerimanpwp,
						'pptknama' => $pptknama,
						'pptknip' => $pptknip,
					)
				);
		//dpq $query;		
		//echo (string) $query;
		$res = $query->execute();
		

	
	db_set_active('bendahara');
	//$bendid=explode(',',$_SESSION['bendid']);
	$bendid=explode(',',$_SESSION[$GLOBALS['user']->uid.'bendid'.$kodekeg]);
	for($n=0;$n<sizeof($bendid);$n++){
		$query = db_update('bendahara' . $kodeuk) // Table name no longer needs {}
		->fields(array(
		  'sudahproses' => '1',
		  'dokid' => $dokid,
		   )
		 )
		->condition('bendid', $bendid[$n], '=')
		->execute();
	}
	db_set_active('');
	
	
	drupal_goto('');
	
}


?>
