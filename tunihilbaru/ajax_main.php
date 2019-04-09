<?php

function ajax_main($arg=NULL, $nama=NULL) {

	$dokid = arg(2);	
	$print = arg(3);
		$output_form = drupal_get_form('ajax_main_form');
		return drupal_render($output_form);// . $output;
			
	
}

function ajax_main_form($form, &$form_state) {

	
	$dokid = arg(2);
	
	
	//KEGIATAN
	//$dokid = '0500073';
	$nokeg = 0;
	$reskeg = db_query('select kodekeg,kegiatan from {kegiatanskpd} where kodekeg in (select kodekeg from {dokumenrekening} where dokid=:dokid) order by kegiatan', array(':dokid'=>$dokid));
	foreach ($reskeg as $datakeg) {

		$form['remove_command_example_fieldset' . $datakeg->kodekeg] = array(
			'#type' => 'fieldset',
			'#title' => t("Shows the Ajax 'remove' command. " . $datakeg->kodekeg),
		);
			
		$form['remove_command_example_fieldset' . $datakeg->kodekeg]['show_command_satu' . $datakeg->kodekeg] = array(
			'#title' => t("Pilihan A" . $datakeg->kodekeg),
			'#type' => 'checkbox',
			'#default_value' => FALSE,
			'#ajax' => array(
					'callback' => 'ajax_mainindex_callback',
				),
			//'#suffix' => "<div id='remove_div'><div id='remove_text'>text to be removed</div></div>
			//              <div id='remove_status'>'After' Command Status: Unknown</div>",
			//'#suffix' => "<div id='remove_div" . $datakeg->kodekeg . "'><div id='remove_text'" . $datakeg->kodekeg . "></div></div>",
		);

		$form['remove_command_example_fieldset' . $datakeg->kodekeg]['show_command_dua' . $datakeg->kodekeg] = array(
			'#title' => t("Pilihan B." . $datakeg->kodekeg),
			'#type' => 'checkbox',
			'#default_value' => FALSE,
			'#ajax' => array(
					'callback' => 'ajax_mainindex_callback',
				),
		);

		$form['remove_command_example_fieldset' . $datakeg->kodekeg]['display' . $datakeg->kodekeg] = array(
			'#type' => 'item',
			'#markup' => "<div id='remove_div" . $datakeg->kodekeg . "'><div id='remove_text'" . $datakeg->kodekeg . "></div></div>",
		);
		
		
	}
	
	
	return $form; 
}

function ajax_mainindex_callback($form, $form_state) {
	$commands = array();
	
	$fieldname = $form_state['triggering_element']['#name'];
	
	$index = substr($fieldname,-12);
	drupal_set_message('fn . ' . $fieldname);
	drupal_set_message('index . ' . $index);
	$should_show = $form_state['values'][$fieldname];
	
	if ($should_show) {
		$commands[] = ajax_command_html('#remove_div' . $index, "<div id='remove_text" . $index . "'>" . get_table() . "</div>");
	} else {
		$commands[] = ajax_command_remove('#remove_text' . $index);
	}

	return array('#type' => 'ajax', '#commands' => $commands);
}


function get_table(){
	$style='border-right:1px solid black;';
	
	$rows=null;

	db_set_active('bendahara');

	$rows[]=array(
		array('data' => 'No', 'width' => '30px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
		array('data' => 'Kode', 'width' => '60px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Uraian', 'width' => '225px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Anggaran', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Sebelumnya', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'UP/GU', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'TU', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'LS', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Total', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
		array('data' => 'Sisa', 'width' => '80px','align'=>'center','style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-size:90%;'),
	);
	
	
	$n = 0; $anggaran_total =0;
	$lalu_total = 0; $ls_total = 0; $gu_total = 0; $tu_total = 0; $rea_total = 0;
	
	//contents
	$query = db_select('anggperkeg', 'a');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('a', array('anggaran'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('a.kodekeg', $kodekeg, '=');
	$query->orderBy('ro.kodero', 'ASC');
	//dpq($query);	
		
	# execute the query	
	$res_rek = $query->execute();
	foreach ($res_rek as $data_rek) {
		$n++;
		
		//lalu
		$lalu = verifikasi_bk5_read_sebelumnya($kodekeg, $data_rek->kodero, $tglawal);
		
		//transaksi
		$ls = 0; $gu = 0; $tu = 0;
		verifikasi_bk5_read_sekarang($kodekeg, $data_rek->kodero, $tglawal, $tglakhir, $ls, $gu, $tu);
		
		$anggaran_total += $data_rek->anggaran;
		
		$lalu_total += $lalu;
		
		$ls_total += $ls;
		$gu_total += $gu; 
		$tu_total += $tu; 
					
		$realisai = $ls + $gu + $tu + $lalu;
		$rea_total += $realisai;
		
		//LALU
		
		//$uraian = l($data_rek->uraian,  '', array ('html' => true));
		//$uraian = l(t('Edit'), '', array('query' => '')),
		
		$uraian = '<a href="/verifikasisppgu/bk6rek/' . $dokid . '/' . $kodekeg . '/' . $data_rek->kodero . '">' .  $data_rek->uraian . '</a>';
		 
		//$uraian = $data_rek->uraian;
		$rows[]=array(
			array('data' => $n, 'width' => '30px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_format_rek_rincianobyek($data_rek->kodero), 'width' => '60px','align'=>'center','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => $uraian, 'width' => '225px','align'=>'left','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($lalu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($gu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($tu), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;border-left:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($ls), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($realisai), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			array('data' => apbd_fn($data_rek->anggaran - $realisai), 'width' => '80px','align'=>'right','style'=>'border-right:1px solid black;font-size:90%;'),
			
		);
	}
	$rows[]=array(
			array('data' => '', 'width' => '30px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => '', 'width' => '60px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => 'TOTAL', 'width' => '225px','align'=>'center','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($anggaran_total),'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($lalu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($gu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($tu_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($ls_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($rea_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			array('data' => apbd_fn($anggaran_total - $rea_total), 'width' => '80px','align'=>'right','style'=>'border:1px solid black;font-weight:bold;font-size:90%;'),
			 
	); 
	db_set_active();
	
	
	$table_element = array(
		'#theme' => 'table',
		'#header' => null,
		'#rows' => $rows,
		'#empty' =>t('Your table is empty'),
	);
	$output = drupal_render($table_element);
	
	
	//$output = createT($header, $rows);
	//$output = theme('table', array('header' => null, 'rows' => $rows ));
	return $output;
}



?>