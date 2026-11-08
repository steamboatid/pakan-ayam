<?php

// include __DIR__ . "/_libs.php";

$ori_master_matr_arr = array(
	'kons152' => array(
		12000, //harga
		41, //protein
		26.4, //karbo
		3, //lemak
		8, //serat
		0, //lain-lain
		1, //is_amust
		0 //max percent (1-100)
	),
	'kons157' => array(
		13000, //harga
		41, //protein
		26.4, //karbo
		3, //lemak
		8, //serat
		0, //lain-lain
		1, //is_amust
		0 //max percent (1-100)
	),
	'pur551' => array(
		13000, //harga
		19.5, //protein
		59.9, //karbo
		4, //lemak
		6, //serat
		0, //lain-lain
		1, //is_amust
		0 //max percent (1-100)
	),

	'jagung' => array(
		8000, //harga
		9, //protein
		70, //karbo
		5.5, //lemak
		2.5, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),
	'bekatul' => array(
		7000, //harga
		12, //protein
		54, //karbo
		15, //lemak
		14, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),

	'tgaplek' => array(
		6500, //harga 			was 8000
		2, //protein
		82, //karbo
		1, //lemak
		3, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),

	// 'tkachijau' => array(
	// 	5500, // was= 5000
	// 	23,
	// 	46,
	// 	1.3,
	// 	16,
	// 	0
	// ),
	// 'tkupkachijau' => array(
	// 	5000, // was= 5000
	// 	11,
	// 	55,
	// 	1.5,
	// 	18,
	// 	0
	// ),

	'troti' => array(
		4500, //harga			was 5000
		12, //protein
		74, //karbo
		3.5, //lemak
		4.25, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),
	'tikan' => array(
		7500, //harga
		40, //protein
		3, //karbo
		10, //lemak
		2, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),
	'susureject' => array(
		5000, //harga			was 5500
		45, //protein
		40, //karbo
		1, //lemak
		2, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),
	'tkedelai' => array(
		7000, //harga
		37.8, //protein
		31.9, //karbo
		20.6, //lemak
		9.6, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),

	'tmie' => array(
		4000, //harga
		10.5, //protein
		64, //karbo
		13.5, //lemak
		1.4, //serat
		0, //lain-lain
		0, //amust
		0 //max percent (1-100)
	),
	'trajukerang' => array(
		3000, //harga
		35, //protein
		7.5, //karbo
		2, //lemak
		15, //serat
		0, //lain-lain
		0, //amust
		5 //max percent (1-100)
	),
	'tkangkung' => array(
		3000, //harga
		20, //protein
		37.5, //karbo
		3.25, //lemak
		16.5, //serat
		0, //lain-lain
		0, //amust
		5 //max percent (1-100)
	),
	'tsawit' => array(
		3000, //harga
		15, //protein
		42, //karbo
		9, //lemak
		16, //serat
		0, //lain-lain
		0, //amust
		5 //max percent (1-100)
	),
);

// prepare master matr
$master_matr_arr = array();

foreach ($ori_master_matr_arr as $nama_bahan => $aa) {
	// print_rdie($aa);
	$a['harga'] = $aa[0];
	$a['protein'] = $aa[1];
	$a['karbo'] = $aa[2];
	$a['lemak'] = $aa[3];
	$a['serat'] = $aa[4];
	// $a['lain']= $aa[5];

	$a['lain'] = 100 - ($aa[1] + $aa[2] + $aa[3] + $aa[4]);
	// print_rdie($a);
	if ($a['lain'] < 0) print_rdie(array($nama_bahan, $a));

	// amust
	if (isset($aa[6])) $a['amust'] = (int) $aa[6];
	else $a['amust'] = 0;

	// max percent
	if (isset($aa[7])) $a['maxperc'] = (float) $aa[7];
	else $a['maxperc'] = 0;

	$master_matr_arr[$nama_bahan] = $a;
}

// print_rdie($master_matr_arr);
