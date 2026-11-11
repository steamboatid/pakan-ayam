<?php

// include __DIR__ . "/_libs.php";

$ori_master_matr_arr = array(
	'cp112' => array( //ayam grower finisher, buras, konsentrat
		14000, //harga
		38, //protein
		33.5, //karbo
		3, //lemak
		6.5, //serat
		0, //lain-lain
		1, //is_amust
		5, //max percent (1-100)
		1, //min percent (1-100), must > 0
	),
	'crum592' => array( //ayam grower finisher, buras
		14000, //harga
		16, //protein
		0, //karbo
		3, //lemak
		7, //serat
		0, //lain-lain
		1, //is_amust
		5, //max percent (1-100)
		1, //min percent (1-100), must > 0
	),
	'crum591' => array( //ayam starter, buras
		14000, //harga
		22, //protein
		0, //karbo
		5, //lemak
		5, //serat
		0, //lain-lain
		1, //is_amust
		5, //max percent (1-100)
		1, //min percent (1-100), must > 0
	),


	'jagung' => array(
		8000, //harga
		9, //protein
		70, //karbo
		5.5, //lemak
		2.5, //serat
		0, //lain-lain
		0, //amust
		0, //max percent (1-100)
		0, //min percent (1-100)
	),
	'bekatul' => array(
		7000, //harga
		12, //protein
		54, //karbo
		15, //lemak
		14, //serat
		0, //lain-lain
		0, //amust
		0, //max percent (1-100)
		0, //min percent (1-100)
	),
	'dedak' => array(
		2500, // harga per kg (estimasi)
		8, // protein
		42,   // karbo
		12,    // lemak
		13,   // serat
		0,    // lain-lain
		0,    // amust
		30,   // max percent (1-100)
		0,    // min percent (1-100)
	),


	'tgaplek' => array(
		4500, //harga 			was 8000
		2, //protein
		82, //karbo
		1, //lemak
		3, //serat
		0, //lain-lain
		0, //amust
		20, //max percent (1-100)
		1, //min percent (1-100)
	),

	'troti' => array(
		4500, //harga			was 5000
		12, //protein
		74, //karbo
		3.5, //lemak
		4.25, //serat
		0, //lain-lain
		0, //amust
		0, //max percent (1-100)
		0, //min percent (1-100)
	),
	'tikan' => array(
		7500, //harga
		40, //protein
		3, //karbo
		10, //lemak
		2, //serat
		0, //lain-lain
		0, //amust
		0, //max percent (1-100)
		0, //min percent (1-100)
	),
	'susureject' => array(
		5000, //harga			was 5500
		45, //protein
		40, //karbo
		1, //lemak
		2, //serat
		0, //lain-lain
		0, //amust
		15, //max percent (1-100)
		1, //min percent (1-100)
	),
	'tkedelai' => array(
		7000, //harga
		37.8, //protein
		31.9, //karbo
		20.6, //lemak
		9.6, //serat
		0, //lain-lain
		0, //amust
		0, //max percent (1-100)
		0, //min percent (1-100)
	),

	'tmie' => array(
		7000, //harga
		10.5, //protein
		64, //karbo
		13.5, //lemak
		1.4, //serat
		0, //lain-lain
		0, //amust
		0, //max percent (1-100)
		10, //min percent (1-100)
	),
	'trajukerang' => array(
		3000, //harga
		35, //protein
		7.5, //karbo
		2, //lemak
		15, //serat
		0, //lain-lain
		0, //amust
		10, //max percent (1-100)
		0, //min percent (1-100)
	),
	'tkangkung' => array(
		3000, //harga
		20, //protein
		37.5, //karbo
		3.25, //lemak
		16.5, //serat
		0, //lain-lain
		0, //amust
		10, //max percent (1-100)
		0, //min percent (1-100)
	),
	'tsawit' => array(
		3000, //harga
		15, //protein
		42, //karbo
		9, //lemak
		16, //serat
		0, //lain-lain
		0, //amust
		5, //max percent (1-100)
		0, //min percent (1-100)
	),

	// bahan mamel
	'gamblong' => array(
		1500, //harga
		2, //protein
		78, //karbo
		1, //lemak
		5, //serat, 15
		0, //lain-lain
		0, //amust
		15, //max percent (1-100)
		0, //min percent (1-100)
	),


	'ampkelapa' => array(
		4000, //harga
		7, //protein
		45, //karbo
		18, //lemak
		14, //serat
		0, //lain-lain
		0, //amust
		15, //max percent (1-100)
		0, //min percent (1-100)
	),
	'karak' => array(
		5500, //harga
		7.5, //protein
		72, //karbo
		1.5, //lemak
		1, //serat
		0, //lain-lain
		0, //amust
		50, //max percent (1-100)
		0, //min percent (1-100)
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

	// min percent
	if (isset($aa[8])) $a['minperc'] = (float) $aa[8];
	else $a['minperc'] = 0;

	$master_matr_arr[$nama_bahan] = $a;
}

// print_rdie($master_matr_arr);
