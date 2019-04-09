<?php
function usulan_arsip_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
	
	if ($arg) {
		switch($arg) {
			case 'filter':
				$skpd = arg(3);
				$bulan = arg(4);
				$posting = arg(5);
			break;

			default:
				//drupal_access_denied();
			break;
		}
		
	} else { 
		$skpd = '0';
		$bulan = '0';
		$posting = '0';
		
	}

	$kodeuk = apbd_getuseruk();
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '','width' => '3px',  'valign'=>'top'),
		array('data' => 'Tanggal','width' => '90px',  'field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kodekeg',  'valign'=>'top'),
		array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
		array('data' => 'No. SPP', 'width' => '80px', 'valign'=>'top'),
		array('data' => 'Tgl. SPP','width' => '90px',  'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '90px', 'field'=> 'jumlah', 'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	);

	$no=0;

	$query = db_select('spjtu', 's')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('kegiatanskpd', 'k', 's.kodekeg=k.kodekeg');
	//$query->leftJoin('dokumen', 'd', 's.dokid=d.dokid');

	# get the desired fields from the database
	$query->fields('s', array('spjid', 'tanggal', 'kodeuk', 'jumlah', 'keperluan', 'posting'));
	$query->fields('k', array('kegiatan'));
	//$query->fields('d', array('spptgl', 'sppno'));
	
	$query->condition('k.kodeuk', $kodeuk, '=');
	if ($skpd !='0') $query->condition('s.kodeuk', $skpd, '=');
	if ($bulan!='0') $query->where('EXTRACT(MONTH FROM s.tanggal) = :month', array('month' => $bulan));
	if ($posting !='xx') $query->condition('s.posting', $posting, '=');
	
	//if ($bulan !='0') $query->condition('s.month(tanggal)', $bulan, '=');
	
	
	$query->orderByHeader($header);
	$query->orderBy('s.tanggal', 'DESC');
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

		if ($data->posting=='1') {
			$proses = apbd_icon_sudah();
			$editlink1= 'Hapus';

		} else {
			$proses = apbd_icon_belum();
			$editlink1=  apbd_button_hapus('tunihilbaru/delete/' . $data->spjid);
		}
		
		$editlink = apbd_button_jurnal('tunihilbaru/edit/' . $data->spjid);
		
		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $proses, 'align' => 'center', 'valign'=>'top'),
			array('data' => apbd_fd($data->tanggal),'align' => 'right', 'valign'=>'top'),
			array('data' => $data->kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => $data->keperluan,'align' => 'left', 'valign'=>'top'),
			array('data' => $data->sppno,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fd($data->spptgl),'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
			$editlink,
			$editlink1,			
		);
	}
	
	
	//BUTTON
	$btn = '';
	$btn = apbd_button_baru('tunihilbaruspp/pilihkeg/usulan') . "&nbsp;";
	
	$output_form = drupal_get_form('usulan_arsip_main_form');
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $btn . $output;
	
	
}

function usulan_arsip_main_form($form, &$form_state) {
	$skpd = apbd_getuseruk();
				$bulan = arg(4);
				$posting = arg(5);
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',	
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	if (isSuperuser()){
	
	$results = db_query('select DISTINCT kodeuk, namasingkat from unitkerja');
	$arr_skpd[0] = 'SEMUA SKPD';
	foreach ($results as $data) {
		$arr_skpd[$data->kodeuk] = $data->namasingkat;
	}
	
	$form['formdata']['skpd']= array(
		'#type'         => 'select', 
		'#title' =>  t('SKPD'),
		'#options' => $arr_skpd,	
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',		
		'#default_value'=> $skpd,
	);
	}else {
	
	$form['formdata']['skpd']= array(
		'#type'         => 'value', 		
		'#value'=> $skpd,
	);
	}
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
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
	
	$option_posting =array('xx' => 'Semua',0 => 'Belum Posting', 1 =>'Sudah Posting');
	$form['formdata']['posting'] = array(
		'#type' => 'select',
		'#title' =>  t('Posting'),
		'#prefix' => '<div class="col-md-6">',
		'#suffix' => '</div>',	
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_posting,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$posting,
	);
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Tampilkan',
		'#prefix' => '<div class="col-md-12">',
		'#suffix' => '</div>',
		//'#suffix' => "&nbsp;<a href='http://aset.simkedajepara.link/laporan/laporan_tanah/pdf/" . $kodeuk . "' class='btn btn-warning btn-sm'><span class='glyphicon glyphicon-print' aria-hidden='true'></span>Cetak</a>&nbsp;<a href='http://aset.simkedajepara.link/laporan/laporan_tanah/excel/" . $kodeuk ."' class='btn btn-warning btn-sm'><span class='glyphicon glyphicon-file' aria-hidden='true'></span>Excel</a></div>",		
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}
function usulan_arsip_main_form_submit($form, &$form_state){
	$skpd = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	$posting = $form_state['values']['posting'];
	
	$url = 'tunihilbaru/arsip/filter/' . $skpd . '/' . $bulan . '/' . $posting;
	drupal_goto($url);
}

?>
