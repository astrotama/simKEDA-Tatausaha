<?php
function plafonup_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 20;
	
	if (arg(1) == 'pdf') {
		
		$output = plafonup_main_print();
		apbd_ExportPDF('P', 'A4', $output, 'CEK');
		
	} else {
	//drupal_set_title('PENDAPATAN #' . $bulan);
	
		//$output_form = drupal_get_form('apbdop_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD',  'field'=> 'namasingkat', 'valign'=>'top'), 
		array('data' => 'Anggaran', 'width' => '100px', 'valign'=>'top'),
		array('data' => 'Dasar UP', 'width' => '100px', 'valign'=>'top'),
		array('data' => 'Plafon UP',  'width' => '100px', 'valign'=>'top'),
	);
		
		$query = db_select('unitkerja', 'u')->extend('PagerDefault')->extend('TableSort');
		
		# get the desired fields from the database
		$query->fields('u', array('kodeuk', 'namauk'));
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
			$query->condition('u.kodeuk', $kodeuk , '=');
		}
		$query->orderByHeader($header);
		$query->orderBy('u.kodedinas', 'ASC');
		$query->limit($limit);	
		
			
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
			$anggaran = 0;
			$dasarup = 0;
			$plafonup = 0;
			
			//anggaran		
			$sql = db_select('anggperkeg', 'a');
			$sql->innerjoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$sql->addExpression('SUM(a.jumlah)', 'anggaran');
			$sql->condition('k.kodeuk', $data->kodeuk, '=');
			if ($data->kodeuk!='00') {
				$sql->condition('k.jenis', '2', '=');
			}
			# execute the query
			$res = $sql->execute();
			foreach ($res as $datax) {
				$anggaran = $datax->anggaran;
			}
			if ($data->kodeuk == '00') {
				$plafonup = 100000000;
			} else {
			//dasar up		
			$sql = db_select('anggperkeg', 'a');
			$sql->innerjoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$sql->addExpression('SUM(a.jumlah)', 'dasarup');
			$sql->condition('k.kodeuk', $data->kodeuk, '=');
			if ($data->kodeuk!='00') {
				$sql->condition('k.jenis', '2', '=');
			}	
				//BLUD BOS
				$sql->condition('a.kodero', '52104001', '<>');
				$sql->condition('a.kodero', '52105001', '<>');

				//BLUD BOS
				$sql->condition('a.kodero', '52220001', '<>');
				$sql->condition('a.kodero', '52228001', '<>');
				
				$sql->condition('a.kodero', '523%', 'NOT LIKE');
			
			
			
			
			# execute the query
			$res = $sql->execute();
			foreach ($res as $datax) {
				$dasarup = $datax->dasarup;
			}
			
			if ($dasarup <=900000000) {
				$plafonup = round($dasarup/12000,0) * 1000;
				if ($plafonup>50000000) $plafonup = 50000000;
			
			} elseif ($dasarup <=2400000000) {
				$plafonup = round($dasarup/18000,0) * 1000;
				if ($plafonup>100000000) $plafonup = 100000000;
				
			} elseif ($dasarup <=6000000000) {
				$plafonup = round($dasarup/24000,0) * 1000;
				if ($plafonup>200000000) $plafonup = 200000000;
				
			} else {
				$plafonup = round($dasarup/30000,0) * 1000;
				if ($plafonup>400000000) $plafonup = 400000000;
			}
			}

				
			$rows[] = array(
				array('data' => $no,'width' => '10px', 'valign'=>'top'),
				array('data' => $data->namauk,  'field'=> 'skpd', 'valign'=>'top'), 
				array('data' => apbd_fn($anggaran) , 'align' => 'right',  'valign'=>'top'),
				array('data' => apbd_fn($dasarup) , 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($plafonup) , 'align' => 'right', 'valign'=>'top'),
			);
		}
		if (isUserSKPD()){
			$btn = null;
		}else{
		$btn = l('PDF', 'plafonup/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		}
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');

		//return drupal_render($output_form) . $btn . $output . $btn;
		return $btn . $output ;
	}
}

function plafonup_main_print() {
	
	$header=array();
	$rows[]=array(
		array('data' => 'PLAFON UANG PERSEDIAAN', 'width' => '515px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;font-size:125%;'), 
	);
	$rows[]=array(
		array('data' => 'TAHUN 2019', 'width' => '515px','align'=>'center','style'=>'border:none;'),		
	);
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	
	$header = array (
		array('data' => 'No','width' => '25px', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:27px;'),
		array('data' => 'SKPD',  'valign'=>'top','width' => '180px','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:27px;'), 
		array('data' => 'Anggaran',  'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:27px;'),
		array('data' => 'Dasar UP',  'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:27px;'),
		array('data' => 'Plafon UP',  'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:27px;'),
	);
		
		$query = db_select('unitkerja', 'u');
		
		# get the desired fields from the database
		$query->fields('u', array('kodeuk', 'namauk'));
		//$query->orderByHeader($header);
		$query->orderBy('u.kodedinas', 'ASC');
		
			
		# execute the query
		$results = $query->execute();
			
		# build the table fields
		$no=0;
		$rows = array();
		foreach ($results as $data) {
			
			$no++;  
			$anggaran = 0;
			$dasarup = 0;
			$plafonup = 0;
			
			//anggaran		
			$sql = db_select('anggperkeg', 'a');
			$sql->innerjoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$sql->addExpression('SUM(a.jumlah)', 'anggaran');
			$sql->condition('k.kodeuk', $data->kodeuk, '=');
			if ($data->kodeuk!='00') {
				$sql->condition('k.jenis', '2', '=');
			}
			# execute the query
			$res = $sql->execute();
			foreach ($res as $datax) {
				$anggaran = $datax->anggaran;
			}
			if ($data->kodeuk == '00') {
				$plafonup = 100000000;
			} else {
			//dasar up		
			$sql = db_select('anggperkeg', 'a');
			$sql->innerjoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$sql->addExpression('SUM(a.jumlah)', 'dasarup');
			$sql->condition('k.kodeuk', $data->kodeuk, '=');
			if ($data->kodeuk!='00') {
				$sql->condition('k.jenis', '2', '=');
				
				//BLUD BOS
				$sql->condition('a.kodero', '52104001', '<>');
				$sql->condition('a.kodero', '52105001', '<>');

				//BLUD BOS
				$sql->condition('a.kodero', '52220001', '<>');
				$sql->condition('a.kodero', '52228001', '<>');
				
				$sql->condition('a.kodero', '523%', 'NOT LIKE');
			
			}
			
			
			# execute the query
			$res = $sql->execute();
			foreach ($res as $datax) { 
				$dasarup = $datax->dasarup;
			}
			
			if ($dasarup <=900000000) {
				$plafonup = round($dasarup/12000,0) * 1000;
				if ($plafonup>50000000) $plafonup = 50000000;
			
			} elseif ($dasarup <=2400000000) {
				$plafonup = round($dasarup/18000,0) * 1000;
				if ($plafonup>100000000) $plafonup = 100000000;
				
			} elseif ($dasarup <=6000000000) {
				$plafonup = round($dasarup/24000,0) * 1000;
				if ($plafonup>200000000) $plafonup = 200000000;
				
			} else {
				$plafonup = round($dasarup/30000,0) * 1000;
				if ($plafonup>400000000) $plafonup = 400000000;
			}
			}

				
			$rows[] = array(
				array('data' => $no,'width' => '25px', 'valign'=>'top','align' => 'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black;  border-bottom: 1px solid grey; font-size:25px;'),
				array('data' => $data->namauk , 'valign'=>'top','width' => '180px','style'=>'border-left:1px solid black;border-top: 1px solid black; border-bottom: 0.5px solid grey; font-size:25px;'), 
				array('data' => apbd_fn($anggaran) , 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-bottom: 0.5px solid grey; font-size:25px;'),
				array('data' => apbd_fn($dasarup) , 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-bottom: 0.5px solid grey; font-size:25px;'),
				array('data' => apbd_fn($plafonup) , 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 0.5px solid grey; font-size:25px;'),
			);
			$anggarantotal += $anggaran;
			$dasaruptotal += $dasarup;
			$plafonuptotal += $plafonup;
		}
			$rows[] = array(
				array('data' => '','width' => '25px', 'valign'=>'top','align' => 'center', 'style'=>'border-left:1px solid black;border-top: 1px solid black;  border-bottom: 1px solid black; font-size:25px;'),
				array('data' => 'Total' , 'valign'=>'top','width' => '180px','style'=>'border-left:1px solid black;border-top: 1px solid black; border-bottom: 1px solid black; font-size:25px;font-weight:bold;'), 
				array('data' => apbd_fn($anggarantotal) , 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-bottom: 1px solid black; font-size:25px;font-weight:bold;'),
				array('data' => apbd_fn($dasaruptotal) , 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-bottom: 1px solid black; font-size:25px;font-weight:bold;'),
				array('data' => apbd_fn($plafonuptotal) , 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-top: 1px solid black; border-right: 1px solid black;  border-bottom: 1px solid black; font-size:25px;font-weight:bold;'),
			);
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));

		//return drupal_render($output_form) . $btn . $output . $btn;
		//return $output ;
		$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= createT($header,$rows,null);
		return $output;
		
}

?>
