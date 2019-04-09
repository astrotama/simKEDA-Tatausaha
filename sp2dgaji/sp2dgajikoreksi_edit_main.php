<?php
function sp2dgajikoreksi_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$x = $_SERVER['HTTP_REFERER'];
	
	//drupal_set_message('abc : ' . $x);

	$dokid = arg(2);
	//$dokid = arg(2);	
	//$cekar=soap_enlisting($dokid);
	//$cekar=array("CEK"=>5,"ab"=>"ddd");
	//var_dump($cekar);	
	if(arg(3)=='pdf'){			
		/*
		$output = printsp2d($dokid);
		$output2 = footer($dokid);
		//print_pdf_p_sp2d($output,$output2);
		apbd_ExportSP2D($output,$output2,'SP2D');
		//return $output;
		*/
		$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
	
		$output = printsp2d_digital($dokid);
		
		$fname = str_replace('/', '_', 'SP2D_' . $dokid . '.PDF');
		apbd_ExportSP2D_Lengkap($output, $url, $fname);		
		drupal_goto('files/sp2d/' . $fname);
		
	} else if (arg(3)=='soap') {
		//$output = soap_sp2d_add($dokid);
		//return $output; 
		
		//$output = sendrest($dokid);	
		//return $output;
	
	} else {
	
		//$btn = l('Cetak', '');
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		//drupal_set_message();
		$output_form = drupal_get_form('sp2dgajikoreksi_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function sp2dgajikoreksi_edit_main_form($form, &$form_state) {
	$res=db_query("select * from dokumen where dokid=:dokid",array(':dokid'=>arg(2)))	;
	foreach($res as $data){
		$kodekeg = $data->kodekeg;
		$sp2dno = $data->sp2dno;
		$spmno = $data->spmno;
	}
	
	//drupal_set_message($kodekeg);	
	$title = 'Koreksi SP2D';
	
	$dokid = '';
	$jenisdokumen = 4;
	$kodeuk = '';
	$jenisbelanja = 2;
	$sppno = '';
	//mktime(hour,minute,second,month,day,year,is_dst)
	$bulan = date('m');
	$spptgl = mktime(0,0,0,$bulan,date('d'),apbd_tahun());
	$spdno = '';
	$spdtgl = '';
	$keperluan = 'Pembayaran LS ';
	$jeniskegiatan = '2';
	$penerimanama = '';
	$penerimanip = '';
	$penerimapimpinan = '';
	$penerimaalamat = '';
	$penerimabanknama = '';
	$penerimabankrekening = '';
	$penerimanpwp = '';
	$pptknama = '';
	$pptknip = '';
	$jumlah = 0;
	$pajak = 0;
	$potongan = 0;
	$netto = 0;
	

	if (isUserSKPD()) 
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = '81';	
	
	//KEGIATAN
	$query = db_select('kegiatanskpd', 'k');
	$query->fields('k', array('kodekeg', 'kodeuk' , 'kegiatan'));
	$query->condition('k.jenis', 2, '=');
	$query->condition('k.kodekeg', $kodekeg, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		//$kodekeg = $data->kodekeg;
		$kodeuk = $data->kodeuk;
		$keperluan = $data->kegiatan;
	}
	//dpq($query);	
		
	# execute the query	
	$results = $query->execute();
	
	drupal_set_title($title);
	
				
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);

	$form['sp2dno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SP2D'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		////'#required' => TRUE,
		'#default_value' => $sp2dno,
	);
	$form['spmno'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#required' => TRUE,
		'#default_value' => $spmno,
	);
	$dokid=arg(2);
	$results = db_query('select d.dokid,d.kodeuk, d.spmno, d.sp2dno, d.sp2dtgl, d.kodekeg, d.bulan, d.keperluan,d.reffid ,  d.jumlah, d.pajak, d.penerimanama, d.penerimabankrekening, d.penerimabanknama, d.penerimanpwp, d.pptknama, d.pptknip, d.sp2dok, d.jenisgaji, u.kodeuk, u.namasingkat, d.kodebank from {dokumen} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where d.dokid=:dokid', array(':dokid' => $dokid));
	//drupal_set_message(arg(2));
	foreach ($results as $data) {
		
		$title = 'SP2D Barang Jasa ' . $data->bulan ;
		
		$dokid = $data->dokid;
		$sp2dno = $data->sp2dno;
		
		if ($sp2dno != '') $nosudah = true;
		
		if (isSuperuser())
			if (is_null($data->sp2dtgl)){
				$sp2dtgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
				$tanggalsql = apbd_tahun() . '-' . date('m') . '-' . date('d');
			} else {
				$sp2dtgl = strtotime($data->sp2dtgl);	
				$tanggalsql = $data->sp2dtgl;	
			}
			
		else 
			if ($sp2dno == '') {
				$sp2dno = 'Belum Terbit';
				$sp2dtgl = $sp2dno;		
				$tanggalsql = apbd_tahun() . '-' . date('m') . '-' . date('d');
				
			} else {
				$sp2dtgl = apbd_fd_long($data->sp2dtgl);		
				$tanggalsql = apbd_tahun() . '-' . date('m') . '-' . date('d');
			}
			
		//drupal_set_message($tanggalsql);	
		
		$spmno = $data->spmno;
		$bulan = $data->bulan;
		
		$kodeuk = $data->kodeuk;
		
		//$res=db_query("select * from dokumen where dokid=:dokid",array(':dokid'=>$data->reffid));
		//foreach($res as $dat){
		//	$koreksi=$dat->sp2dno;
		//}
		
		$kodekeg = $data->kodekeg;
		$keperluan = $data->keperluan." (Koreksi SP2D Nomor ". $data->sp2dno . ")";
		
		$penerimanama = $data->penerimanama;
		$penerimabanknama = $data->penerimabanknama;
		$penerimabankrekening = $data->penerimabankrekening;
		$penerimanpwp = $data->penerimanpwp;

		$pptknama = $data->pptknama;
		$pptknip = $data->pptknip;
		
		$jumlah = $data->jumlah;
		$pajak = $data->pajak;
		
		$sp2dok = $data->sp2dok;
		$jenisgaji = $data->jenisgaji;
		
		$kodebank = $data->kodebank;
		
	}
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => arg(2),
	);
	$sp2dtgl = mktime(0,0,0,date('m'),date('d'),apbd_tahun());
	$form['SP2D']['sp2dtgl'] = array(
			'#type' => 'date',
			'#title' =>  t('Tanggal SP2D'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			'#default_value' => $sp2dtgl,
			'#default_value'=> array(
				'year' => format_date($sp2dtgl, 'custom', 'Y'),
				'month' => format_date($sp2dtgl, 'custom', 'n'), 
				'day' => format_date($sp2dtgl, 'custom', 'j'), 
			  ), 
			
		);
	
	/*
	$opt_bulan['1'] = 'Januari';
	$opt_bulan['2'] = 'Februari';
	$opt_bulan['3'] = 'Maret';
	$opt_bulan['4'] = 'April';
	$opt_bulan['5'] = 'Mei';
	$opt_bulan['6'] = 'Juni';
	$opt_bulan['7'] = 'Juli';
	$opt_bulan['8'] = 'Agustus';
	$opt_bulan['9'] = 'September';
	$opt_bulan['10'] = 'Oktober';
	$opt_bulan['11'] = 'Nopember';
	$opt_bulan['12'] = 'Desember';
	$form['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $bulan,
	);
	*/

	$form['bulan'] = array(
		'#type' => 'value',
		'#value' => $bulan,
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);


	//BENDAHARA
	$form['formpenerima'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PENERIMA',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
		$form['formpenerima']['penerimanama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $penerimanama, 
		);				
		$form['formpenerima']['penerimabankrekening']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Rekening'),
			//'#required' => TRUE,
			'#default_value'=> $penerimabankrekening, 
		);
		//		
		
		$option_bank[''] = 'ISIKAN KODE BANK';
		$results = db_query('select kodebank, namabank from {bank} order by nourut, namabank');
		# build the table fields
		if($results){
			foreach($results as $data) {
				$option_bank[$data->kodebank] = $data->kodebank . ' - ' . $data->namabank; 
			}
		}		
		$form['formpenerima']['kodebank'] = array(
			'#type' => 'select',
			'#title' =>  t('Kode Bank'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#prefix' => '<div id="skpd-replace">',
			//'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_bank,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodebank,
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
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th>URAIAN</th><th width="50px">Ada</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('ltkelengkapandokumen', 'dk');
	$query->fields('dk', array('kodekelengkapan', 'uraian', 'nomor'));
	$query->condition('dk.jenis', 4, '=');
	$query->orderBy('dk.nomor', 'ASC');
	$results = $query->execute();
	$jumlahkelengkapan=0;
	foreach ($results as $data) {
		$query = db_select('dokumenkelengkapan', 'dk');
		$query->join('ltkelengkapandokumen', 'lt', 'dk.kodekelengkapan=lt.kodekelengkapan');
		$query->fields('dk', array('kodekelengkapan', 'ada', 'tidakada'));
		$query->fields('lt', array('uraian'));
		$query->condition('dk.dokid', $dokid, '=');
		$query->condition('dk.ada', '1', '=');
		$query->orderBy('lt.nomor', 'ASC');
		$res = $query->execute();
		$ada=array();
		$ada[$data->kodekelengkapan]=0;
		foreach ($res as $dat) {
			$ada[$dat->kodekelengkapan]=$dat->ada;
			$tidakada[$dat->kodekelengkapan]=$dat->tidakada;
			$jumlahkelengkapan++;
		}

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
			'#default_value'=> $ada[$kode], 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formkelengkapan']['tablekelengkapan']['tidakadakelengkapan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $tidakada[$kode], 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		
		
		

	}
	$form['jumlahrekkelengkapan']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
		
	//ITEM REKENING
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		//'#title'=> 'REKENING',
		'#title'=> 'REKENING<em class="text-info pull-right">' . apbd_fn($jumlah) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formrekening']['table']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">ANGGARAN</th><th width="50px">CAIR</th><th width="130px">BATAS</th><th width="130px">JUMLAH</th></tr>',
		 '#suffix' => '</table>',
	);	
 
		//'belanjagajipokok', 'belanjatunjangankeluarga', 'belanjatunjanganjabatan', 
		//'belanjatunjanganfungsional', 'belanjatunjanganumum', 'belanjatunjanganberas', 'belanjatunjanganpajak', 
		//'belanjapembulatan', 'belanjaaskes', 'belanjajkk', 'belanjajkm'
	$i = 0; 
	$cair = 0;
	$jumlah = 0;
	$query = db_select('anggperkeg', 'a');
	$query->join('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('a', array('anggaran'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	
	
	$query->orderBy('ro.kodero', 'ASC');
	if (isAdministrator()) {
		//dpq ($query);
	}

	$results = $query->execute();
	
	//$jumlahrekening=0;
	foreach ($results as $data) {
		$jumlah=0;
		$result = db_query('select r.kodero, r.uraian, a.anggaran, di.jumlah from {rincianobyek} r inner join {dokumenrekening} di on r.kodero=di.kodero inner join {anggperkeg} a on di.kodero=a.kodero where di.dokid=:dokid and di.jumlah>0 and a.kodekeg=:kodekeg and r.kodero=:kodero', array(':dokid'=>$dokid, ':kodekeg'=>$kodekeg,':kodero'=>$data->kodero));
		foreach ($result as $dat) {
			$jumlah=$dat->jumlah;
			//$jumlahrekening++;
		}
		$i++; 
		
		$kodero = $data->kodero;
		$uraian = $data->uraian;
		$form['formrekening']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $kodero,
		); 
		$form['formrekening']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formrekening']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kodero,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['anggaran' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data->anggaran) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['cair' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($cair) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['batas' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data->anggaran - $cair) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['formrekening']['table']['jumlahapbd' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);				
	}
	
	$form['jumlahrekrekening']= array(
		'#type' => 'value',
		'#value' => $i,
	);

	//PAJAK	
	$form['formpajak'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PAJAK<em class="text-info pull-right">' . apbd_fn($pajak) . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formpajak']['tablepajak']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th><th width="20px">Lns</th><th width="260px">KETERANGAN</th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	$query = db_select('ltpajak', 'p');
	$query->fields('p', array('kodepajak', 'uraian'));
	$query->orderBy('p.kodepajak', 'ASC');
	$results = $query->execute();
	$jumlahpajak=0;
	foreach ($results as $data) {
		$jumlah = '0';
		$keterangan = '';
		$query = db_select('dokumenpajak', 'dp');
		$query->join('ltpajak', 'p', 'dp.kodepajak=p.kodepajak');
		$query->fields('p', array('kodepajak', 'uraian'));
		$query->fields('dp', array('jumlah', 'keterangan', 'sudahbayar'));
		$query->condition('dp.dokid', $dokid, '=');
		$query->condition('dp.jumlah', 0, '>');
		$query->condition('dp.kodepajak', $data->kodepajak, '=');
		$query->orderBy('dp.kodepajak', 'ASC');
		$result = $query->execute();
		foreach ($result as $dat) {
			$jumlah=$dat->jumlah;
			$keterangan=$dat->keterangan;
			$sudahbayar=$dat->sudahbayar;
			$jumlahpajak++;
		}
		$i++; 
		$kode = $data->kodepajak;
		$uraian = $data->uraian;
		
		$form['formpajak']['tablepajak']['kodepajak' . $i]= array(
				'#type' => 'value',
				'#value' => $kode,
		); 
		$form['formpajak']['tablepajak']['uraianpajak' . $i]= array(
				'#type' => 'value',
				'#value' => $uraian,
		); 
		
		$form['formpajak']['tablepajak']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpajak']['tablepajak']['kode' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => $kode,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formpajak']['tablepajak']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $uraian, 
			'#suffix' => '</td>',
		); 
		$form['formpajak']['tablepajak']['jumlahpajak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $jumlah, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['formpajak']['tablepajak']['sudahbayar' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> $sudahbayar, 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['formpajak']['tablepajak']['keteranganpajak' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $keterangan, 
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	
		
	}
	$form['jumlahrekpajak']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	//PPTK
	$form['formpptk'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PPTK',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
		$form['formpptk']['pptknama']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('Nama'),
			//'#required' => TRUE,
			'#default_value'=> $pptknama, 
		);				
		$form['formpptk']['pptknip']= array(
			'#type'         => 'textfield', 
			'#title' =>  t('NIP'),
			//'#required' => TRUE,
			'#default_value'=> $pptknip, 
		);	
		
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	/*
	$form['formdata']['submitspp1']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 1',
		'#attributes' => array('class' => array('btn btn-success btn-sm disabled')),
	);
	$form['formdata']['submitspp2']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 2',
		'#attributes' => array('class' => array('btn btn-success btn-sm disabled')),
	);
	$form['formdata']['submitspp3']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> SPP 3',
		'#attributes' => array('class' => array('btn btn-success btn-sm disabled')),
	);
	*/

	return $form;
}
function sp2dgajikoreksi_edit_main_form_validate($form, &$form_state) {
	
} 

function sp2dgajikoreksi_edit_main_form_submit($form, &$form_state) {
$dokid = $form_state['values']['dokid']; 
$kodeuk = $form_state['values']['kodeuk'];
$sp2dtgl = $form_state['values']['sp2dtgl'];
$sp2dtglsql = $sp2dtgl['year'] . '-' . $sp2dtgl['month'] . '-' . $sp2dtgl['day'];
$newdokid=$form_state['values']['dokid']; 

//PPTK
$pptknama = $form_state['values']['pptknama'];
$pptknip = $form_state['values']['pptknip'];

$jumlahrekrekening = $form_state['values']['jumlahrekrekening'];
$jumlahrekkelengkapan = $form_state['values']['jumlahrekkelengkapan'];
$jumlahrekpajak = $form_state['values']['jumlahrekpajak'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
	//KELENGKAPAN
	$num_deleted = db_delete('dokumenkelengkapan')
		  ->condition('dokid', $newdokid)
		  ->execute();		
		
	for ($n=1; $n <= $jumlahrekkelengkapan; $n++){
		$kodekelengkapan = $form_state['values']['kodekelengkapan' . $n];
		$ada = $form_state['values']['adakelengkapan' . $n];
		$tidakada = $form_state['values']['tidakadakelengkapan' . $n];
		
		
		db_insert('dokumenkelengkapan')
			->fields(array('dokid', 'kodekelengkapan', 'ada', 'tidakada'))
			->values(array(
					'dokid'=> $newdokid,
					'kodekelengkapan' => $kodekelengkapan,
					'ada' => $ada,
					'tidakada' => $tidakada,
					
					))
			->execute();
	}
	
	
	//REKENING
		$num_deleted = db_delete('dokumenrekening')
		  ->condition('dokid', $newdokid)
		  ->execute();			
		$totaljumlah = 0;
		for ($n=1; $n <= $jumlahrekrekening; $n++){
			$kodero = $form_state['values']['koderoapbd' . $n];
			$jumlah = $form_state['values']['jumlahapbd' . $n];
			
			db_insert('dokumenrekening')
				->fields(array('dokid', 'kodero', 'jumlah'))
				->values(array(
						'dokid'=> $newdokid,
						'kodero' => $kodero,
						'jumlah' => $jumlah,
						))
				->execute();
			
			$totaljumlah = $totaljumlah + $jumlah;
		}

		//PAJAK
		$totalpajak = 0;
		$num_deleted = db_delete('dokumenpajak')
		  ->condition('dokid', $newdokid)
		  ->execute();			
		for ($n=1; $n <= $jumlahrekpajak; $n++) {
			$kodepajak = $form_state['values']['kodepajak' . $n];
			$jumlah = $form_state['values']['jumlahpajak' . $n];
			$keterangan = $form_state['values']['keteranganpajak' . $n];
			$sudahbayar = $form_state['values']['sudahbayar' . $n];
			
			db_insert('dokumenpajak')
				->fields(array('dokid', 'nourut', 'kodepajak', 'jumlah', 'keterangan', 'sudahbayar'))
				->values(array(
						'dokid'=> $newdokid,
						'nourut'=> $n,
						'kodepajak' => $kodepajak,
						'jumlah' => $jumlah,
						'keterangan' => $keterangan,
						'sudahbayar' => $sudahbayar, 
						))
				->execute();
			
			$totalpajak = $totalpajak + $jumlah;
			
		}
	$sp2dno = $form_state['values']['sp2dno'];
	$sp2dtgl = $form_state['values']['sp2dtgl'];
	$sp2dtglsql = $sp2dtgl['year'] . '-' . $sp2dtgl['month'] . '-' . $sp2dtgl['day'];

	$keperluan = $form_state['values']['keperluan'];
	$kodebank = $form_state['values']['kodebank'];
	
	//PENERIMA
	$penerimanama = $form_state['values']['penerimanama'];
	$penerimabanknama = $form_state['values']['penerimabanknama'];
	$penerimabankrekening = $form_state['values']['penerimabankrekening'];
	$penerimanpwp = $form_state['values']['penerimanpwp'];

	//PPTK
	$pptknama = $form_state['values']['pptknama'];
	$pptknip = $form_state['values']['pptknip'];
	$query = db_update('dokumen')
	->fields(
			array(
				'sp2dno' =>$sp2dno,
				'sp2dok' => 0,
				'keperluan'=>$keperluan,
				'jumlah'=> $totaljumlah,
				'penerimanama'=> $penerimanama,
				'penerimabanknama'=>    $penerimabanknama,
				'penerimabankrekening'=> $penerimabankrekening,   
				'penerimanpwp'=>    $penerimanpwp,
				'pptknama'=>    $pptknama,
				'pptknip'=>$pptknip,
				
			)
		);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();	
	
	
	drupal_goto('barangjasakoreksi/edit/'.$newdokid);

}	
}

?>
