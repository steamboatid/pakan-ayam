<?php

// ------------------------------------------------------------------------
//
// INI UNTUK AYAM
//
// ------------------------------------------------------------------------


include __DIR__ . "/_libs.php";
include __DIR__ . "/_data.php";
include __DIR__ . "/_ga.php";

global $master_matr_arr;
global $main_pop, $sel_pop;
global $protein_min, $protein_max, $karbo_min, $karbo_max, $price_max;
global $lemak_max, $serat_max;
global $susu_max, $roti_max, $khijau_max;


// print_rdie($master_matr_arr);

// starter, grower, finisher, bunting, nyusui
$ayam_target = 'bunting';
// $ayam_target = 'nyusui';
// $ayam_target = 'starter';
// $ayam_target = 'grower';
// $ayam_target = 'finisher';

// print_rdie($argv);
if (isset($argv[1])) $ayam_target = strtolower($argv[1]);

$protein_min = 0;
$protein_max = 0;
$karbo_min = 0;
$karbo_max = 0;
$lemak_max = 5;
$serat_max = 5;

// $susu_max = 30;
// $roti_max = 30;
// $khijau_max = 30;

$def_mix_cap = 100; // 100 kg mixing cap
$price_max = 7000;


if ($ayam_target == 'starter') {
  // usia 0-4 minggu
  $protein_min = 19;
  $protein_max = 23;
  $karbo_min = 54;
  $karbo_max = 60;
  $lemak_max = 5;
  $serat_max = 5;
  // $price_max = 7000;

  // not used materials
  // unset($master_matr_arr['tikan']);
  unset($master_matr_arr['crum592']);
  //

} elseif ($ayam_target == 'finisher') {
  // usia 4-16/24 minggu
  $protein_min = 16;
  $protein_max = 20;
  $karbo_min = 60;
  $karbo_max = 100;
  $lemak_max = 6;
  $serat_max = 6;
  // $price_max = 6500;

  // not used materials
  unset($master_matr_arr['crum591']);
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
// ga_sort_population($main_pop);

// sort ascending, lowest fitness at front
ga_sort_population($main_pop);


// pre
$pre_best_fit = PHP_INT_MAX;
$unchanged_loop = 0;

for ($gen_loop = 0; $gen_loop < $max_gen; $gen_loop++) {
  // select + tournament
  $mate_pop = ga_select($main_pop);

  // crossover
  $cr_pop = ga_crossover($mate_pop);
  // print_rdie($cr_pop);

  // mutation
  $mu_pop1 = ga_mutation($mate_pop, 'mate');
  $mu_pop2 = ga_mutation($cr_pop, 'cr');
  // print_rdie($mu_pop2);

  // merge all ops
  $res_pop = array_merge($cr_pop, $mu_pop1, $mu_pop2);
  ga_compute_population($res_pop);

  // merge main pop, delete bad agents
  $new_good = ga_merge_main_population($main_pop, $res_pop);
  // print_rdie($main_pop[0]);

  // auto extend loop
  // if ($new_good > 0) $max_gen = (int) round($max_gen * 1.05);
  if ($new_good > 0) {
    $max_gen += $max_gen_delta * $new_good;
    $unchanged_loop -= 100;
  }

  // ga_sort_population($main_pop);

  // best agent
  $agent_best = $main_pop[0];
  $best_fitness = round((float) $agent_best['fitness'], 5);
  $best_price = (float) $agent_best['price_per_kg'];
  $best_price = round($best_price);

  // worst agent
  $last_idx = array_key_last($main_pop);
  $agent_worst = $main_pop[$last_idx];
  $worst_fitness = round((float) $agent_worst['fitness'], 5);
  $worst_price = (float) $agent_worst['price_per_kg'];
  $worst_price = round($worst_price);

  echo "\n LOOP=$gen_loop of $max_gen \tbest=$best_price,$best_fitness \tworst=$worst_price,$worst_fitness";
  // print_r(array_keys($main_pop));

  $del_price = abs($best_price - $worst_price);
  $del_fitness = abs($best_fitness - $worst_fitness);
  if (is_zero($del_fitness)) break;

  if (!is_zero($worst_fitness)) {
    $pro_fitness = (float) abs($del_fitness / $worst_fitness * 100);
    if ($pro_fitness < 5) break;
  }

  // check if best fitness not changed after N loop
  $del_fit_pre = abs($pre_best_fit - $best_fitness);
  if (!is_zero($del_fit_pre)) {
    $unchanged_loop = 0;
    $pre_best_fit = $best_fitness;
  } else {
    $unchanged_loop++;
    if ($unchanged_loop > 1000) break;
  }
}

echo "\n";
// print_r($main_pop[1]);
print_r($main_pop[0]);

echo "\n\n--- AYAM TARGET: $ayam_target ";
ga_print_pop($main_pop, 5);
// ga_print_agent($main_pop[1]);
// ga_print_agent($main_pop[0]);

echo "\n\n--- AYAM TARGET: $ayam_target \n";
