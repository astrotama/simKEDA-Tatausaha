<?php
function sp2d_arsip_main($arg=NULL, $nama=NULL) {
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
				$sp2dok = arg(4);
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
			$kodeuk = $_SESSION["sp2d_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["sp2d_arsip_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$sp2dok = $_SESSION["sp2d_arsip_sp2dok"];
		if ($sp2dok=='') $sp2dok = 'ZZ';

		$jenisgaji = $_SESSION["sp2d_arsip_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';
		
		$jenisdokumen = $_SESSION["sp2d_arsip_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = 'ZZ';
		
		$kata_kunci = $_SESSION["sp2d_arsip_katakunci"];		
	}
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('sp2d_arsip_main_form');
	if (isSuperuser())
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '100px', 'field'=> 'sp2dtgl', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			//array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Jenis', 'field'=> 'jenisgaji', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Bruto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Potongan', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Netto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
	else 
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SP2D', 'field'=> 'sp2dno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '100px', 'field'=> 'sp2dtgl', 'valign'=>'top'),
			//array('data' => 'SPP', 'field'=> 'sppno', 'valign'=>'top'),
			array('data' => 'SPM', 'field'=> 'spmno', 'valign'=>'top'),
			array('data' => 'Bulan', 'field'=> 'bulan', 'valign'=>'top'),
			array('data' => 'Jenis', 'field'=> 'jenisgaji', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Bruto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Potongan', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => 'Netto', 'width' => '90px', 'field'=> 'jumlah',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
		

	$query = db_select('dokumen', 'd')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('d', array('dokid', 'bulan', 'jenisdokumen', 'keperluan', 'kodeuk', 'spmno', 'sppno', 'sp2dno', 'sp2dtgl', 'jumlah', 'potongan', 'netto', 'sp2dok', 'jenisgaji', 'cetaksp2d'));
	$query->fields('u', array('namasingkat'));
	
	//GAJI
	//$query->condition('d.jenisdokumen', 3, '=');
	if ($jenisdokumen !='ZZ') {
			$query->condition('d.jenisdokumen', $jenisdokumen, '=');
	}
	
	//SKPD
	if ($kodeuk =='QQ') {
		global $user;
		$username = $user->name;		
		
		/*
		$sql = db_select('userskpd', 'u');
		$sql->fields('u', array('kodeuk'));
		$sql->condition('u.username', $username, '=');
		$res = $sql->execute();
		
		$or = db_or();
		foreach ($res as $datauk) {
			$or->condition('d.kodeuk', $datauk->kodeuk, '=');
		}
		$query->condition($or);
		*/
		$query->innerJoin('userskpd', 'us', 'd.kodeuk=us.kodeuk');
		$query->condition('us.username', $username, '=');
				
	} else if ($kodeuk !='ZZ') 
		$query->condition('d.kodeuk', $kodeuk, '=');
	
	if ($bulan !='0') $query->condition('d.bulan', $bulan, '=');
	
	if ($jenisgaji =='ZZ') 
		$query->condition('d.jenisgaji', 4, '<');
	else 
		$query->condition('d.jenisgaji', $jenisgaji, '=');
	
	if ($kata_kunci!='') {
		$or = db_or();
		$or->condition('d.keperluan', '%' . db_like($kata_kunci) . '%', 'LIKE');
		$or->condition('d.penerimanama', '%' . db_like($kata_kunci) . '%', 'LIKE');
		
		$query->condition($or);
	}
	$query->condition('d.spmok', '1', '=');
	$query->condition('d.approved', '1', '=');
	if ($sp2dok != 'ZZ' ) $query->condition('d.sp2dok', $sp2dok, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('u.namasingkat', 'ASC');
	$query->limit($limit);
	
	/*
	if (isAdministrator()) {
	dpq($query);
		
	}
	*/
	
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
		
		
		$esp2dlink = "eSP2D";
		//$editlink = apbd_button_jurnal('sp2dgaji/edit/' . $data->dokid);
		if ($data->jenisdokumen == '0'){
			$editlink = apbd_button_jurnal('sp2duptu/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '1'){
			$editlink = apbd_button_jurnal('gantiuangsp2d/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '2'){
			$editlink = apbd_button_jurnal('sp2duptu/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '3'){
			$editlink = apbd_button_jurnal('sp2dgaji/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '4'){
			$editlink = apbd_button_jurnal('barangjasasp2d/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '5'){
			$editlink = apbd_button_jurnal('sp2dnihil/edit/' . $data->dokid);
		} else if ($data->jenisdokumen == '7'){
			$editlink = apbd_button_jurnal('sp2dnihil/edit/' . $data->dokid);
		}
		
		if($data->sp2dok=='2')
			$sp2dok = apbd_icon_valid();
		else if($data->sp2dok=='1') {
			$sp2dok = apbd_icon_sudah();
			$esp2dlink = apbd_button_esp2d($data->dokid);
			
			//CETAK
			//if ((isSuperuser()) and  ($data->sp2dno!='')) $editlink .= apbd_button_cetak('sp2dgaji/edit/' . $data->dokid . '/pdf');
			
		} else 
			$sp2dok = apbd_icon_belum();
		
		if ($data->sp2dno=='') 
			$sp2dtgl = '';
		else
			$sp2dtgl = apbd_fd($data->sp2dtgl);

		//ESPM
		//$espmlink = "eSPM";
		//if (($data->kodeuk=='81') or ($data->kodeuk=='03')) {
			$espmlink = apbd_button_espm($data->dokid);
		//}
		
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
		
		//drupal_goto('sp2dgaji/edit/' . $dokid . '/pdf');
		if (isSuperuser()) {

			//CETAK
			if (($data->sp2dok=='1') and  ($data->sp2dno!='')) 
				$cetaklink = apbd_button_cetak('sp2dgaji/edit/' . $data->dokid . '/pdf'  . $data->cetaksp2d);
			else
				$cetaklink = 'Cetak';
			
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $sp2dok,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => $sp2dtgl,'align' => 'center', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						//array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),						
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $str_jenis, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->potongan),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->netto),'align' => 'right', 'valign'=>'top'),
						$editlink,
						$cetaklink,
						$esp2dlink,
						$espmlink,
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		} else 
			$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $sp2dok,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->sp2dno,'align' => 'left', 'valign'=>'top'),
						array('data' => $sp2dtgl,'align' => 'center', 'valign'=>'top'),
						//array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
						array('data' => $data->spmno,'align' => 'left', 'valign'=>'top'),						
						array('data' => apbd_getbulan($data->bulan), 'align' => 'left', 'valign'=>'top'),
						array('data' => $str_jenis, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->potongan),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->netto),'align' => 'right', 'valign'=>'top'),
						$editlink,
						$esp2dlink,
						$espmlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);			
	}
	
	
	//BUTTON
	if (isSuperuser()) {
		$btn = l('<span class="glyphicon glyphicon-retweet" aria-hidden="true"></span> PFK', 'pfk/edit' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary btn-sm')));
		$btn .= "&nbsp;" . apbd_button_print('/cetakregister/' . $kodeuk . '/' . date('n'));
	} else {
		$btn = apbd_button_print('/cetakregister/' . $kodeuk . '/' . date('n') );
	}
		
	$btn .= "&nbsp;" . apbd_button_excel('');	
	$btn .= '<em class="text-info pull-right"><span class="badge">' . get_new_sp2d() . '</span> Nomor Terakhir : ' . apbd_getmaxnosp2d('3', '0') . '</em>';

	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function get_new_sp2d(){
	$query = db_select('dokumen', 'd');
	$query->addExpression('COUNT(dokid)', 'jumlah');
	$query->condition('d.sp2dok', 0, '=');
	$query->condition('d.spmok', 1, '=');
	$query->condition('d.jenisdokumen', 3, '=');
	$query->condition('d.jenisgaji', 4, '<');
		
	$jumlah = 0;
	# execute the query
	$results = $query->execute();	
	foreach ($results as $data) {
		$jumlah = $data->jumlah;	
	}	
	return $jumlah;
}



function sp2d_arsip_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$bulan = $form_state['values']['bulan'];
	$sp2dok = $form_state['values']['sp2dok'];
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

	$_SESSION["sp2d_arsip_kodeuk"] = $kodeuk;
	$_SESSION["sp2d_arsip_bulan"] = $bulan;
	$_SESSION["sp2d_arsip_sp2dok"] = $sp2dok;
	$_SESSION["sp2d_arsip_jenisgaji"] = $jenisgaji;
	$_SESSION["sp2d_arsip_jenisdokumen"] = $jenisdokumen;
	$_SESSION["sp2d_arsip_katakunci"] = $kata_kunci;

	$uri = 'sp2darsip/filter/' . $kodeuk . '/' . $bulan . '/' . $sp2dok . '/' . $jenisgaji .'/'. $jenisdokumen .'/'. $kata_kunci;
	drupal_goto($uri);
	
}

function sp2d_arsip_main_form($form, &$form_state) {
	
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$sp2dok = arg(4);
		$jenisgaji = arg(5);
		$jenisdokumen = arg(6);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["sp2d_arsip_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["sp2d_arsip_bulan"];
		if ($bulan=='') $bulan = date('m');
		
		$sp2dok = $_SESSION["sp2d_arsip_sp2dok"];
		if ($sp2dok=='') $sp2dok = 'ZZ';	

		$jenisgaji = $_SESSION["sp2d_arsip_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';

		$jenisdokumen = $_SESSION["sp2d_arsip_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = 'ZZ';
		
	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA' . '<em><small class="text-info pull-right">' . get_label_data($bulan, $jenisgaji, $sp2dok) . '</small></em>',		//'#attributes' => array('class' => array('container-inline')),
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
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
	} else if (isSuperuser()){
		global $user;
		$username = $user->name;		
	
		$option_skpd['ZZ'] = 'SELURUH SKPD';	
		$option_skpd['QQ'] = 'SKPD VERIFIKATOR'; 
		$result = db_query('SELECT unitkerja.kodeuk, unitkerja.namasingkat FROM unitkerja INNER JOIN userskpd ON unitkerja.kodeuk=userskpd.kodeuk WHERE userskpd.username=:username ORDER BY unitkerja.namasingkat', array(':username' => $username));	
		while($row = $result->fetchObject()){
			$option_skpd[$row->kodeuk] = $row->namasingkat; 
		}
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodeuk,
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
	$option_skpd['QQ'] = 'SKPD VERIFIKATOR'; 	
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
	$form['formdata']['jenisgaji'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Gaji'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $opt_gaji,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisgaji,
	);	 
	

	$opt_sp2d['ZZ'] ='SEMUA';
	$opt_sp2d['0'] = 'BELUM VERIFIKASI';
	$opt_sp2d['1'] = 'SUDAH VERIFIKASI';	
	//$opt_sp2d['2'] = 'SUDAH VALIDASI';	
	$form['formdata']['sp2dok'] = array(
		'#type' => 'select',
		'#title' =>  t('Verifikasi'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',
		'#options' => $opt_sp2d,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sp2dok,
	);	 
	 
	$jns_dok['ZZ'] ='SEMUA';
	$jns_dok['0'] = 'UP';
	$jns_dok['1'] = 'GU';
	$jns_dok['2'] = 'TU';
	$jns_dok['3'] = 'GAJI';	
	$jns_dok['4'] = 'BARANG JASA';	
	$jns_dok['5'] = 'NIHIL';	
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

if ($status=='ZZ')
	$label .= '/Semua SP2D';
else if ($status=='0')	
	$label .= '/Belum verifikasi';
else if ($status=='1')	
	$label .= '/Sudah verifikasi';

$label .= ' (Klik disini untuk mengganti pilihan data)';
return $label;
}


?>
