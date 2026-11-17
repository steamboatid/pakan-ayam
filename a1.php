<?php

declare(strict_types=1);


// ------------------------------------------------------------------------
//
// INI UNTUK AYAM
//
// ------------------------------------------------------------------------

global $master_matr_arr;
global $main_pop, $sel_pop;
global $protein_min, $protein_max, $karbo_min, $karbo_max, $price_max, $kkalkg_min;
global $lemak_max, $serat_max;
global $susu_max, $roti_max, $khijau_max;
global $def_mix_cap, $suplemen_price_per_kg, $suplemen_price_per_mix;
global $min_price_agent, $cheap_agents, $best_agents;

// used by suplemen
$suplemen_price_per_kg = 0;
$suplemen_price_per_mix = 0;
$def_mix_cap = 100; // 100 kg mixing cap
$price_max = 17000;

$min_price_agent = null;
$cheap_agents = array();
$best_agents = array();


include __DIR__ . "/_random.php";
include __DIR__ . "/_consts.php";
include __DIR__ . "/_libs.php";
include __DIR__ . "/_data.php";
include __DIR__ . "/_suplemen.php";
include __DIR__ . "/_ga.php";


// print_rdie($master_matr_arr);

// starter, finisher
// $ayam_target = 'starter';
$ayam_target = 'finisher';

// print_rdie($argv);
if (isset($argv[1])) $ayam_target = strtolower($argv[1]);

$protein_min = 0;
$protein_max = 0;
$karbo_min = 0;
$karbo_max = 0;
$lemak_max = 9; // 7-8
$serat_max = 7; // 5-6
$kkalkg_min = 2900;

// $susu_max = 30;
// $roti_max = 30;
// $khijau_max = 30;


if ($ayam_target == 'starter') {
  // usia 0-4 minggu
  $protein_min = 16; // min=18
  $protein_max = 20; // max=20
  $karbo_min = 40; // 55-60
  $karbo_max = 55;
  // $lemak_max = 9;
  // $serat_max = 9;
  // $price_max = 20000;
  $kkalkg_min = 2900;

  // mod material
  $master_matr_arr['tikan']['maxperc']= 50;
  $master_matr_arr['tikan']['minperc']= 15;

  // not used materials
  unset($master_matr_arr['crum591']);
  unset($master_matr_arr['crum592']);
  unset($master_matr_arr['cp112']);

  unset($master_matr_arr['tgaplek']);
  unset($master_matr_arr['gamblong']);

  unset($master_matr_arr['trajukerang']); // masuk suplemen
  unset($master_matr_arr['tkangkung']); // masuk suplemen

  // unset($master_matr_arr['susureject']);
  // unset($master_matr_arr['troti']);
  // unset($master_matr_arr['tkedelai']);
  // unset($master_matr_arr['karak']);

  // unset($master_matr_arr['jagung']);
  // unset($master_matr_arr['bekatul']);
  // unset($master_matr_arr['tsawit']);

  //

} elseif ($ayam_target == 'finisher') {
  // usia 4-16/24 minggu
  $protein_min = 15; // min=15
  $protein_max = 20; // max=17
  $karbo_min = 50; // 60-65
  $karbo_max = 70;
  // $lemak_max = 7;
  // $serat_max = 6;
  // $price_max = 6500;
  $kkalkg_min = 3200;

  // not used materials
  unset($master_matr_arr['crum591']);
  unset($master_matr_arr['crum592']); // using cp112
  unset($master_matr_arr['cp112']);

  unset($master_matr_arr['tgaplek']);
  unset($master_matr_arr['gamblong']);

  unset($master_matr_arr['trajukerang']); // masuk suplemen
  unset($master_matr_arr['tkangkung']); // masuk suplemen

  // unset($master_matr_arr['susureject']);
  // unset($master_matr_arr['troti']);
  // unset($master_matr_arr['tkedelai']);
  // unset($master_matr_arr['karak']);

  // unset($master_matr_arr['jagung']);
  // unset($master_matr_arr['bekatul']);
  // unset($master_matr_arr['tsawit']);

  //

}
if ($protein_min < 1 || $protein_max < 1 || $karbo_min < 1 || $karbo_max < 1) print_rdie("FATAL ERROR: \$ayam_target $ayam_target");




$pop_num = 100; // maximum agents in population
$max_gen = 100; // maximum generation loop
// $max_gen_delta = (int) round($max_gen * .01);
$max_gen_delta = 5;

$main_pop = array();
$sel_pop = array();

// generate random agents
$main_pop = ga_init_pop($pop_num);

// sort ascending, lowest fitness at front
ga_sort_population($main_pop);


ga_main_loop($max_gen, $max_gen_delta, $main_pop, true, true);

// reloop using cheap agents
$tmp_pop = $cheap_agents;
if (!empty($tmp_pop)) ga_merge_main_population($tmp_pop, $best_agents, false);
else $tmp_pop = $best_agents;

if (count($tmp_pop) < 5) {
  $abest_pop = array_slice($main_pop, 0, 5);
  ga_merge_main_population($tmp_pop, $abest_pop, false);
}
if (count($tmp_pop) >= 5) {
  $price_max += 1000;
  $main_pop = $tmp_pop;
  ga_main_loop($max_gen * 2, $max_gen_delta * 5, $main_pop, true, true);
}



echo "\n";
// print_r($main_pop[1]);
print_r($main_pop[0]);

echo "\n\n--- AYAM TARGET: $ayam_target ";
ga_print_pop($main_pop, 5);
// if (!empty($cheap_agents)) ga_print_pop($cheap_agents, 3);
// ga_print_agent($main_pop[1]);
// ga_print_agent($main_pop[0]);

echo "\n\n--- AYAM TARGET: $ayam_target \n";
