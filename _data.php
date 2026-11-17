<?php

declare(strict_types=1);

$ori_master_matr_arr = array(
	'cp112' => array(
		14000, // harga
		38,    // protein
		33.5,  // karbo
		3,     // lemak
		6.5,   // serat
		0,     // lain-lain
		1,     // is_amust
		25,    // max percent (1-100)
		1,     // min percent (1-100), must > 0
		2900,  // kilo kalori per kg
	),
	'crum592' => array(
		14000, // harga
		16,    // protein
		0,     // karbo
		3,     // lemak
		7,     // serat
		0,     // lain-lain
		1,     // is_amust
		25,    // max percent (1-100)
		1,     // min percent (1-100), must > 0
		3300,  // kilo kalori per kg
	),
	'crum591' => array(
		14000, // harga
		22,    // protein
		0,     // karbo
		5,     // lemak
		5,     // serat
		0,     // lain-lain
		1,     // is_amust
		25,    // max percent (1-100)
		1,     // min percent (1-100), must > 0
		3150,  // kilo kalori per kg
	),

	'jagung' => array(
		8000,  // harga
		9,     // protein
		70,    // karbo
		5.5,   // lemak
		2.5,   // serat
		0,     // lain-lain
		0,     // amust
		0,     // max percent (1-100)
		0,     // min percent (1-100)
		3550,  // kilo kalori per kg
	),
	'bekatul' => array(
		7000,  // harga
		12,    // protein
		54,    // karbo
		15,    // lemak
		14,    // serat
		0,     // lain-lain
		0,     // amust
		0,     // max percent (1-100)
		0,     // min percent (1-100)
		3100,  // kilo kalori per kg
	),
	'dedak' => array(
		2500,  // harga
		8,     // protein
		42,    // karbo
		12,    // lemak
		13,    // serat
		0,     // lain-lain
		0,     // amust
		0,     // max percent (1-100)
		0,     // min percent (1-100)
		2200,  // kilo kalori per kg
	),

	// udanawu, blitar. pohong selep: 2600/kg
	// 'tgaplek' => array(
	//   6500, // harga
	//   2,    // protein
	//   82,   // karbo
	//   1,    // lemak
	//   3,    // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   3500, // kilo kalori per kg
	// ),
	// 'tkedelai' => array(
	//   7000, // harga
	//   37.8, // protein
	//   31.9, // karbo
	//   20.6, // lemak
	//   9.6,  // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   3800, // kilo kalori per kg
	// ),

	// desa jati, udanawu, blitar
	// 'troti' => array(
	//   4500, // harga
	//   12,   // protein
	//   74,   // karbo
	//   3.5,  // lemak
	//   4.25, // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   4000, // kilo kalori per kg
	// ),
	// 'tmie' => array(
	//   7500, // harga
	//   10.5, // protein
	//   64,   // karbo
	//   13.5, // lemak
	//   1.4,  // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   4150, // kilo kalori per kg
	// ),
	// 'kulit_kedelai' => array(
	//   5500, // harga
	//   18,   // protein
	//   45,   // karbo
	//   5,    // lemak
	//   22,   // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   2100, // kilo kalori per kg
	// ),

	// tunge, kediri
	'tikan' => array(
		7500,  // harga
		40,    // protein
		3,     // karbo
		10,    // lemak
		2,     // serat
		0,     // lain-lain
		0,     // amust
		20,    // max percent (1-100)
		0,     // min percent (1-100)
		3400,  // kilo kalori per kg
	),
	'trajukerang' => array(
		4500,  // harga
		35,    // protein
		7.5,   // karbo
		2,     // lemak
		15,    // serat
		0,     // lain-lain
		0,     // amust
		9,     // max percent (1-100)
		0,     // min percent (1-100)
		1800,  // kilo kalori per kg
	),
	'tkangkung' => array(
		3000,  // harga
		20,    // protein
		37.5,  // karbo
		3.25,  // lemak
		16.5,  // serat
		0,     // lain-lain
		0,     // amust
		9,     // max percent (1-100)
		0,     // min percent (1-100)
		2000,  // kilo kalori per kg
	),
	'tsawit' => array(
		3000,  // harga
		15,    // protein
		48,    // karbo
		9,     // lemak
		17,    // serat
		0,     // lain-lain
		0,     // amust
		9,     // max percent (1-100)
		0,     // min percent (1-100)
		3200,  // kilo kalori per kg
	),

	// kepung, kediri
	// 'susureject' => array(
	// 	5000, // harga
	// 	45,   // protein
	// 	40,   // karbo
	// 	1,    // lemak
	// 	2,    // serat
	// 	0,    // lain-lain
	// 	0,    // amust
	// 	15,   // max percent (1-100)
	// 	0,    // min percent (1-100)
	// 	4400, // kilo kalori per kg
	// ),

	// bahan mamel
	'gamblong' => array(
		1500,  // harga
		2,     // protein
		78,    // karbo
		1,     // lemak
		5,     // serat
		0,     // lain-lain
		0,     // amust
		25,    // max percent (1-100)
		0,     // min percent (1-100)
		3200,  // kilo kalori per kg
	),

	// wates, kediri
	'ampkelapa' => array(
		4000,  // harga
		7,     // protein
		45,    // karbo
		18,    // lemak
		14,    // serat
		0,     // lain-lain
		0,     // amust
		0,     // max percent (1-100)
		0,     // min percent (1-100)
		2700,  // kilo kalori per kg
	),
	'karak' => array(
		5500,  // harga
		7.5,   // protein
		72,    // karbo
		1.5,   // lemak
		1,     // serat
		0,     // lain-lain
		0,     // amust
		0,     // max percent (1-100)
		0,     // min percent (1-100)
		3650,  // kilo kalori per kg
	),

	// 'ampastahu_kering' => array(
	//   3800, // harga
	//   23,   // protein
	//   35,   // karbo
	//   6,    // lemak
	//   15,   // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   3200, // kilo kalori per kg
	// ),
	// 'bungkil_kopra' => array(
	//   3500, // harga
	//   21,   // protein
	//   47,   // karbo
	//   8,    // lemak
	//   14,   // serat
	//   0,    // lain-lain
	//   0,    // amust
	//   0,    // max percent (1-100)
	//   0,    // min percent (1-100)
	//   2800, // kilo kalori per kg
	// ),

	// 'jagkatul_kunjang' => array(
	//   3500, // harga
	//   10.5, // protein
	//   55.0, // karbo
	//   3.0,  // lemak
	//   14.0, // serat
	//   0,    // lain-lain
	//   1,    // is_amust
	//   10,   // max percent (1-100)
	//   0,    // min percent (1-100)
	//   2900, // kilo kalori per kg
	// ),
);


// prepare master matr
$master_matr_arr = array();

foreach ($ori_master_matr_arr as $nama_bahan => $aa) {
	if (count($aa) < 9) print_rdie(array('bad material', $aa));
	// print_rdie($aa);

	$a['harga'] = $aa[0];
	$a['protein'] = $aa[1];
	$a['karbo'] = $aa[2];
	$a['lemak'] = $aa[3];
	$a['serat'] = $aa[4];
	// $a['lain']= $aa[5];

	$a['lain'] = 100 - ($aa[1] + $aa[2] + $aa[3] + $aa[4]);
	if ($a['lain'] < 0) print_rdie(array($nama_bahan, $a));

	$a['amust'] = (int) $aa[6];
	$a['maxperc'] = (float) $aa[7];
	$a['minperc'] = (float) $aa[8];
	$a['kkalkg'] = (float) $aa[9];

	$master_matr_arr[$nama_bahan] = $a;
}

// print_rdie($master_matr_arr);
