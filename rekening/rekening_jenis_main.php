<?php
function rekening_jenis_main($arg=NULL, $nama=NULL) {

		//drupal_set_message(arg(4));
		$output = gen_output_rekening();
		$output_form = drupal_get_form('rekening_jenis_main_form');	
		
		$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	
}


function rekening_jenis_main_form($form, &$form_state) {
	
	
}

function gen_output_rekening() {
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '20px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('anggaran', 'a');
$query->fields('a', array('kodea', 'uraian'));
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->fields('j', array('kodej', 'uraian'));
			$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$jenis =  l($data_jen->uraian, 'rekening/obyek/' . $data_jen->kodej, array('attributes' => array('class' => null)));
	
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $jenis, 'align' => 'left', 'valign'=>'top'),
			);
			
			

		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));
//$tabel_data = createT($header, $rows);


return $tabel_data;

}



?>