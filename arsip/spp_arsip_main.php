<head>
	<style>
		p{
		  white-space: nowrap;
		  overflow: hidden;
		  width: 100%;
		  animation: linearwipe 1s steps(60, end); 
		}

		@keyframes linearwipe{ 
		  from { width: 0; } 
		}  
	</style>
</head>
<?php
function spp_arsip_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
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
				$sppok = arg(4);
				$jenisgaji = arg(5);
				$jenisdokumen = arg(6);
				$kata_kunci = arg(7);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {

	
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			if(!isset($_SESSION["spparsip_kodeuk"]))
				$_SESSION["spparsip_kodeuk"]='ZZ';
			$kodeuk = $_SESSION["spparsip_kodeuk"];
			//if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		if(!isset($_SESSION["spparsip_bulan"]))
				$_SESSION["spparsip_bulan"]=date('m');
		$bulan = $_SESSION["spparsip_bulan"];
		//if ($bulan=='') $bulan = date('m');
		
		if(!isset($_SESSION["spparsip_sppok"]))
				$_SESSION["spparsip_sppok"]='ZZ';
		$sppok = $_SESSION["spparsip_sppok"];
		//if ($sppok=='') $sppok = 'ZZ';
		
		if(!isset($_SESSION["spparsip_jenisgaji"]))
				$_SESSION["spparsip_jenisgaji"]='ZZ';
		$jenisgaji = $_SESSION["spparsip_jenisgaji"];
		//if ($jenisgaji=='') $jenisgaji = 'ZZ';
		if(!isset($_SESSION["spparsip_jenisdokumen"]))
				$_SESSION["spparsip_jenisdokumen"]='ZZ';
		$jenisdokumen = $_SESSION["spparsip_jenisdokumen"];
		
		$kata_kunci = $_SESSION["spparsip_katakunci"];	
	}
	
	/*
	if (isUserSKPD()) {
		drupal_set_message('SKPD');
		drupal_set_message(apbd_getuseruk());
	} else
		drupal_set_message('Not SKPD');
	*/
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('spp_arsip_main_form');
	if (isSuperuser()) 
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'Tanggal','width' => '90px',  'field'=> 'spptgl', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'kodeuk', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Jenis', 'field'=> 'jenisgaji', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Bruto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Potongan', 'width' => '80px', 'field'=> 'potongan',  'valign'=>'top'),
			array('data' => 'Netto', 'width' => '90px', 'field'=> 'netto',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),			
		);
	else
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'Tanggal','width' => '90px',  'field'=> 'spptgl', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Jenis', 'field'=> 'jenisgaji', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Bruto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Potongan', 'width' => '80px', 'field'=> 'potongan',  'valign'=>'top'),
			array('data' => 'Netto', 'width' => '90px', 'field'=> 'netto',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),			
		);		

	$query = db_select('dokumen', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('dokid', 'sppno', 'jenisdokumen','spptgl', 'spmno', 'sp2dno', 'bulan', 'kodeuk', 'jumlah', 'potongan', 'netto', 'sppok', 'keperluan', 'jenisgaji', 'sp2dok', 'sp2dsudah'));
	$query->fields('u', array('namasingkat'));
	
	//GAJI
	//$query->condition('d.jenisdokumen', 3, '=');
	if ($jenisdokumen !='ZZ') {
		//$query->condition('d.jenisdokumen', $jenisdokumen, '=');
			$query->condition('d.jenisdokumen', $jenisdokumen, '=');
	}
	
	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->condition('d.bulan', $bulan, '=');
	//if ($bulan !='0') $query->condition('month(d.spptgl)', $bulan, '=');
	if ($jenisgaji !='ZZ') $query->condition('d.jenisgaji', $jenisgaji, '=');
	
	//drupal_set_message('sppok '. $sppok);
	
	if (($sppok =='0') or ($sppok =='1'))
		$query->condition('d.sppok', $sppok, '=');
	else if ($sppok =='2') {
		$query->condition('d.sppok', '1', '=');
		$query->condition('d.spmok', '1', '=');
	} else if ($sppok =='3') {
		$query->condition('d.sppok', '1', '=');
		$query->condition('d.spmok', '1', '=');
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.sp2dsudah', '1', '=');
	}
	if ($kata_kunci!='') {
		$or = db_or();
		$or->condition('d.keperluan', '%' . db_like($kata_kunci) . '%', 'LIKE');
		$or->condition('d.penerimanama', '%' . db_like($kata_kunci) . '%', 'LIKE');
		
		$query->condition($or);
	}
	//if ($sppok !='ZZ') $query->condition('d.sppok', $sppok, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('u.namasingkat', 'ASC');
	$query->limit($limit);
		
	//dpq($query);
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		//icon
		if($data->sppok=='1')
			if ($data->sp2dok=='1')
				$proses = apbd_icon_valid();
			else
				$proses = apbd_icon_sudah();
		else
			$proses = apbd_icon_belum();
		
		//EDIT LINK
		//$editlink = apbd_button_jurnal('sppgaji/edit/' . $data->dokid);
		if ($data->jenisdokumen == '0'){
			$editlink = apbd_button_jurnal('sppup/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '1'){
			$editlink = apbd_button_jurnal('guspp/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '2'){
			$editlink = apbd_button_jurnal('spptu/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '3'){
			$editlink = apbd_button_jurnal('sppgaji/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '4'){
			$editlink = apbd_button_jurnal('barangjasaspp/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '5'){
			$editlink = apbd_button_jurnal('sppnihil/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '7'){
			$editlink = apbd_button_jurnal('sppnihil/edit/' . $data->dokid);
		}
		if($data->sppok=='0') $editlink .=  apbd_button_hapus('sppgaji/delete/' . $data->dokid);		//'<a class="btn //btn-danger btn-xs 
		//	$editlink .=  apbd_button_hapus_disabled();
		//else
		//	$editlink .=  apbd_button_hapus('sppgaji/delete/' . $data->dokid);		//'<a class="btn btn-danger btn-xs 

		if ($data->sppno=='') 
			$spptgl = '';
		else
			$spptgl = apbd_fd($data->spptgl);
		
		//jenis gaji
		if ($data->jenisgaji=='0')
			$str_jenis = 'Reg';
		else if ($data->jenisgaji=='1')
			$str_jenis = 'Krg';
		else if ($data->jenisgaji=='2')
			$str_jenis = 'Sus';
		elseif ($data->jenisgaji=='3')
			$str_jenis = 'Ter';
		else
			$str_jenis = 'Tam';
		
		if (isSuperuser())
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => '<p>'. $proses .'</p>','align' => 'right', 'valign'=>'top'),
							array('data' => '<p>'. $data->sppno .'</p>','align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. $spptgl .'</p>','align' => 'center', 'valign'=>'top'),
							array('data' => '<p>'. $data->spmno .'</p>','align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. $data->sp2dno .'</p>','align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. $data->namasingkat .'</p>',  'align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. apbd_getbulan($data->bulan) .'</p>', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. $str_jenis .'</p>', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. $data->keperluan .'</p>', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<p>'. apbd_fn($data->jumlah) .'</p>','align' => 'right', 'valign'=>'top'),
							array('data' => '<p>'. apbd_fn($data->potongan) .'</p>','align' => 'right', 'valign'=>'top'),
							array('data' => '<p>'. apbd_fn($data->netto) .'</p>','align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		else
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => $proses,'align' => 'right', 'valign'=>'top'),
							array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
							array('data' => $spptgl,'align' => 'center', 'valign'=>'top'),
							array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),
							array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
							array('data' => $str_jenis, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->potongan),'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->netto),'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);			
	}
	
	
	//BUTTON
	if (isUserSKPD()) {
		//$btn = apbd_button_baru('sppgaji/newmanual');
		//$btn .= "&nbsp;" . apbd_button_baru_custom('sppgaji/newmanual/tamsil', 'Tamsil');
		//$btn .= "&nbsp;" . apbd_button_print('');
		$btn .= "&nbsp;" . apbd_button_print('cetakregister');
	} else 
		$btn = apbd_button_print('cetakregister');
	$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(6)=='pdf'){
		$output = getlaporanregister();
		apbd_ExportPDF('L', 'F4', $output, 'CEK');
		//$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		//print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	
}

function spp_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$sppok = $form_state['values']['sppok'];
	$jenisgaji = $form_state['values']['jenisgaji'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$kata_kunci = $form_state['values']['kata_kunci'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	$_SESSION["spparsip_kodeuk"] = $kodeuk;
	$_SESSION["spparsip_bulan"] = $bulan;
	$_SESSION["spparsip_sppok"] = $sppok;
	$_SESSION["spparsip_jenisgaji"] = $jenisgaji;
	$_SESSION["spparsip_jenisdokumen"] = $jenisdokumen;
	$_SESSION["spparsip_katakunci"] = $kata_kunci;
	
	$uri = 'spparsip/filter/' . $kodeuk . '/' . $bulan  . '/' . $sppok . '/' . $jenisgaji .'/'. $jenisdokumen .'/'. $kata_kunci;
	drupal_goto($uri);
	
}


function spp_arsip_main_form($form, &$form_state) {
	
	//$kodeuk = 'ZZ';
	//$bulan = date('m');
	//$bulan = '1';
	//$sppok = 'ZZ';
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$sppok = arg(4);
		$jenisgaji  = arg(5);
		$jenisdokumen  = arg(6);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["spparsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["spparsip_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$sppok = $_SESSION["spparsip_sppok"];
		if ($sppok=='') $sppok = 'ZZ';
		
		$jenisgaji = $_SESSION["spparsip_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = '#';
		
		$jenisgaji = $_SESSION["spparsip_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = '#';
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="span4 text-info pull-right">' . get_label_data($bulan, $jenisgaji, $sppok) . '</small></em>',		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
	
	//SKPD
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['formdata']['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);			
	} else {	
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		$option_skpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $option_skpd[$data->kodeuk] = $data->namasingkat; 
			}
		}		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',	
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#prefix' => '<div id="skpd-replace">',
			//'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodeuk,
		);
	}
	
	//BULAN
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',	
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);
	
	//JENIS GAJI
	$opt_gaji['ZZ'] = 'SEMUA';
	$opt_gaji['0'] = 'REGULER';
	$opt_gaji['1'] = 'KEKURANGAN';	
	$opt_gaji['2'] = 'SUSULAN';	
	$opt_gaji['3'] = 'TERUSAN';	
	$opt_gaji['4'] = 'TAMSIL';	
	$form['formdata']['jenisgaji'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Gaji'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $opt_gaji,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisgaji,
	);	 

	//SPP
	$opt_jurnal['ZZ'] ='SEMUA SPP';
	$opt_jurnal['0'] = 'SPP BELUM DIVERIFIKASI';
	$opt_jurnal['1'] = 'SPP SUDAH DIVERIFIKASI';	
	$opt_jurnal['2'] = 'SPP SUDAH TERBIT SPM';
	$opt_jurnal['3'] = 'SPP SUDAH TERBIT SP2D';
	$form['formdata']['sppok'] = array(
		'#type' => 'select',
		'#title' =>  t('Status'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $opt_jurnal,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sppok,
	);	 
	$jns_dok['ZZ'] ='SEMUA';
	$jns_dok['0'] = 'UP';
	$jns_dok['1'] = 'GU';
	$jns_dok['2'] = 'TU';
	$jns_dok['3'] = 'GAJI';	
	$jns_dok['4'] = 'BARANG JASA';	
	$jns_dok['5'] = 'TU NIHIL';	
	$jns_dok['7'] = 'GU NIHIL';	
	$form['formdata']['jenisdokumen'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Dokumen'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $jns_dok,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisdokumen,
	);	
	
	$form['formdata']['kata_kunci'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Cari '),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#attributes' => array('placeholder' => t('Cari Berdasarkan Kegiatan/penerima nama'),),
		'#default_value' => $kata_kunci,
	);	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}


function get_label_data($bulan, $jenisgaji, $status) {
if ($bulan=='0')
	$label = 'Setahun';
else 
	$label = 'Bulan ' . apbd_get_namabulan($bulan);

if ($jenisgaji=='ZZ')
	$label .= '/Semua gaji';
else if ($jenisgaji=='0')	
	$label .= '/Gaji reguler';
else if ($jenisgaji=='1')	
	$label .= '/Kekurangan gaji';
else if ($jenisgaji=='2')
	$label .= '/Gaji susulan';
else if ($jenisgaji=='3')	
	$label .= '/Gaji terusan';
else if ($jenisgaji=='4')	
	$label .= '/Tamsil';

if ($status=='ZZ')
	$label .= '/Semua SPP';
else if ($status=='0')	
	$label .= '/Belum verifikasi';
else if ($status=='1')	
	$label .= '/Sudah verifikasi';
else if ($status=='2')
	$label .= '/Sudah SPM';
else if ($status=='3')	
	$label .= '/Sudah SP2D';

$label .= ' (Klik disini untuk mengganti pilihan data)';
return $label;
}
function getlaporanregister(){
	$styleheader='border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '875px','align'=>'center','style'=>'font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'REGISTER    SP2D', 'width' => '875px','align'=>'center','style'=>'font-weight:bold;font-size:120%;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN 2017', 'width' => '875px','align'=>'center','style'=>'border:none;font-size:100%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '500px','align'=>'center','style'=>'border:none;font-weight:bold;'),
	);
	
	$rows[]=array(
		array('data' => 'No. Urut',  'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => 'Tanggal',  'width' => '105px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'No SP2D',  'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'UP', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'GU', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'TU', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'GAJI', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'LS', 'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian', 'width' => '280px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Jumlah', 'width' => '140px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);
	
	
	//Content
	$results=db_query('SELECT d.dokid AS dokid, d.sppno AS sppno, d.spptgl AS spptgl, d.spmno AS spmno, d.sp2dno AS sp2dno, d.jenisdokumen,d.bulan AS bulan, d.kodeuk AS kodeuk, d.jumlah AS jumlah, d.potongan AS potongan, d.netto AS netto, d.sppok AS sppok, d.keperluan AS keperluan, d.jenisgaji AS jenisgaji, d.sp2dok AS sp2dok, d.sp2dsudah AS sp2dsudah, u.namasingkat AS namasingkat FROM  {dokumen} d INNER JOIN {unitkerja} u ON d.kodeuk=u.kodeuk WHERE  (d.bulan = :bulan)  ORDER BY u.namasingkat ASC',array(':bulan'=>3));
	$n=0;
	$sumup=0;
	$sumgu=0;
	$sumtu=0;
	$sumgaji=0;
	$sumls=0;
	$total=0;
	foreach($results as $data){
		if ($data->sppno=='') 
			$spptgl = '';
		else
			$spptgl = apbd_fd($data->spptgl);
		$n++;
		$total+=$data->jumlah;
		if($data->jenisdokumen==0){
			$up=1;
			$sumup+=1;
		}
			
		else if($data->jenisdokumen==1){
			$gu=1;
			$sumgu+=1;
		}
			
		else if($data->jenisdokumen==2){
			$tu=1;
			$sumtu+=1;
		}
			
		else if($data->jenisdokumen==3){
			$gaji=1;
			$sumgaji+=1;
		}
			
		else if($data->jenisdokumen==4){
			$ls=1;
			$sumls+=1;
		}
			
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => $spptgl, 'width' => '105px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $data->sp2dno, 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $up,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $gu,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $tu,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $gaji,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $ls,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;'),
			array('data' => $data->keperluan, 'width' => '280px','align'=>'left','style'=>'border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => apbd_fn($data->jumlah), 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			
		);
	}
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '105px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => $sumup,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => $sumgu,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => $sumtu,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => $sumgaji,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => $sumls,'width' => '44px','align'=>'center','style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'JUMLAH', 'width' => '280px','align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => apbd_fn($total), 'width' => '140px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
			
		);
	
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Jepara, 5 Januari 2016','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'Mengesahkan','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => '','width' => '670px', 'align'=>'center','style'=>'border:none;'),
	);
	$rows[] = array(
					array('data' => 'NIP.','width' => '435px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP.','width' => '440px', 'align'=>'center','style'=>'border:none;'),
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}

?>
