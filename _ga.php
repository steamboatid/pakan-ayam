<?php

// function ga_is_good_agent_single(array $aagent): bool
// {
//   global $protein_min, $protein_max, $karbo_min, $karbo_max, $price_max;

//   $is_bad = $aagent['non_zero'] < 3 ||
//     $aagent['sum_part'] > 20 || $aagent['sum_part'] < 5;
//   // $aagent['min_percent'] < 5 ||
//   // $aagent['protein'] < $protein_min ||
//   // $aagent['karbo'] < $karbo_min ||
//   // $aagent['price_per_kg'] > $price_max;
//   return !$is_bad;
// }

function ga_compute_agent_fitness(array &$aagent)
{
  global $protein_min, $protein_max, $karbo_min, $karbo_max, $price_max;
  global $lemak_max, $serat_max;
  global $master_matr_arr, $def_mix_cap;


  $aagent['str_mul'] = '';
  $aagent['err'] = '';
  $aagent['protein'] = floatval($aagent['protein']);
  $aagent['karbo'] = floatval($aagent['karbo']);
  // print_rdie($aagent);

  $fit_mul = 1.0;
  $str_mul = array();
  $str_err = array();

  // bad agents
  $nz_lo = 1;
  $nz_hi = 9;
  if (!is_ayam_starter() && $aagent['non_zero'] <= $nz_lo) {
    $fit_mul *= 2;
    $str_mul[] = "nonzero <= $nz_lo";
    $str_err[] = "non zero <= $nz_lo ";
  }
  //---
  elseif ($aagent['non_zero'] > $nz_hi) {
    $fit_mul *= 3;
    $str_mul[] = "nonzero > $nz_hi";
    $str_err[] = "non zero > $nz_hi";
  }

  $aval = round($aagent['sum_part'], 1);
  if ($aagent['sum_part'] > TOO_MANY_PARTS) {
    $fit_mul *= 2;
    $str_mul[] = 'sumpart-many';
    $str_err[] = "too many parts: $aval";
  }
  //---
  elseif ($aagent['sum_part'] < 5) {
    $fit_mul *= 2;
    $str_mul[] = 'sumpart-few';
    $str_err[] = "too few parts: $aval";
  }

  // min percent too small
  if ($aagent['min_percent'] <= TOO_SMALL_PERCENT) {
    $fit_mul *= 200;
    $str_mul[] = 'minperc';
    $str_err[] = 'too small percent';
  }
  // max percent too big
  elseif ($aagent['max_percent'] >= TOO_BIG_PERCENT) {
    $fit_mul *= 300;
    $str_mul[] = 'maxperc';
    $str_err[] = 'too big percent';
  }

  if ($aagent['lemak'] > $lemak_max) {
    $str_err[] = 'lemak too high';
    $fit_mul *= 2;
    $str_mul[] = 'lemak-max';
  }

  if ($aagent['serat'] > $serat_max) {
    $fit_mul *= 2;
    $str_mul[] = 'serat-max';
    $str_err[] = 'serat too high';
  }

  $ok_prote = false;
  if ($aagent['protein'] < $protein_min) {
    $fit_mul *= 300;
    $str_mul[] = 'prote-min';
    $str_err[] = 'protein too low';
  }
  //---
  elseif ($aagent['protein'] > $protein_max) {
    $fit_mul *= 400;
    $str_mul[] = 'prote-max';
    $str_err[] = 'protein too high';
  }
  //---
  else $ok_prote = true;

  $ok_karbo = false;
  if ($aagent['karbo'] < $karbo_min) {
    $fit_mul *= 500;
    $str_mul[] = 'karbo-min';
    $str_err[] = 'karbo too low';
  }
  //---
  elseif ($aagent['karbo'] > $karbo_max) {
    $fit_mul *= 600;
    $str_mul[] = 'karbo-max';
    $str_err[] = 'karbo too high';
  }
  //---
  else $ok_karbo = true;

  if ((!$ok_prote && $ok_karbo) || ($ok_prote && !$ok_karbo)) {
    $fit_mul *= 500;
    $aagent['err'] = 'prote-karbo-mismatch';
    $str_mul[] = 'prote-karbo-mismatch';
  }

  if ($aagent['price_per_kg'] > $price_max) {
    $fit_mul *= 600;
    $str_mul[] = 'price-max';
    $str_err[] = 'price too high';
  }



  $matr_names = array_keys($master_matr_arr);
  foreach ($matr_names as $amat_name) {
    $apart = 0;
    $aperc = 0;
    if (isset($aagent['mats'][$amat_name]['part_percent']))
      $aperc = $aagent['mats'][$amat_name]['part_percent'];
    if (isset($aagent['mats'][$amat_name]['part']))
      $apart = $aagent['mats'][$amat_name]['part'];
    // print_rdie($aagent['mats'][$amat_name]);

    $aamust = (int) $master_matr_arr[$amat_name]['amust'];
    $amax_perc = (float) $master_matr_arr[$amat_name]['maxperc'];
    $amin_perc = (float) $master_matr_arr[$amat_name]['minperc'];
    // print_rdie($master_matr_arr[$amat_name]);

    if ($aamust < 1) {
      if (is_zero($apart) || is_zero($aperc))
        continue;
      //
      elseif ($aperc < $amin_perc && !is_zero($amin_perc)) {
        $fit_mul *= 200;
        $str_mul[] = 'too-small-' . $amat_name;
        //
      } elseif ($aperc > $amax_perc && !is_zero($amax_perc)) {
        $fit_mul *= 300;
        $str_mul[] = 'too-big-' . $amat_name;
        //

      }
      //

    } else {
      if ($aperc < $amin_perc && !is_zero($amin_perc)) {
        $fit_mul *= 400;
        $str_mul[] = 'amust-too-small-' . $amat_name;
        //

      } elseif ($aperc > $amax_perc && !is_zero($amax_perc)) {
        $fit_mul *= 500;
        $str_mul[] = 'amust-too-big-' . $amat_name;
        //
      }
    }
  }





  /*
  // bekatul
  $amat_name = 'bekatul';
  if (
    isset($aagent['mats'][$amat_name]) &&
    $aagent['mats'][$amat_name]['part'] > 0 && $aagent['mats'][$amat_name]['part_percent'] > 3
  ) {
    $fit_mul *= 0.5;
    $str_mul[] = 'bekatul>3%';
  }

  // jagung
  $amat_name = 'jagung';
  if (
    isset($aagent['mats'][$amat_name]) &&
    $aagent['mats'][$amat_name]['part'] > 0 && $aagent['mats'][$amat_name]['part_percent'] > 3
  ) {
    $fit_mul *= 0.5;
    $str_mul[] = 'jagung>3%';
  }

  // tikan
  $amat_name = 'tikan';
  if (
    isset($aagent['mats'][$amat_name]) &&
    $aagent['mats'][$amat_name]['part'] > 0 && $aagent['mats'][$amat_name]['part_percent'] > 3
  ) {
    $fit_mul *= 0.5;
    $str_mul[] = 'tikan>3%';
  }

  // tkedelai
  $amat_name = 'tkedelai';
  if (
    isset($aagent['mats'][$amat_name]) &&
    $aagent['mats'][$amat_name]['part'] > 0 && $aagent['mats'][$amat_name]['part_percent'] > 3
  ) {
    $fit_mul *= 0.5;
    $str_mul[] = 'tkedelai>3%';
  }
  //*/

  //print_rdie($aagent);



  // check amust part
  $exist_pakan_starter = is_agent_mat_part_exists($aagent, 'crum591');
  $exist_pakan_finisher = is_agent_mat_part_exists($aagent, 'crum592') || is_agent_mat_part_exists($aagent, 'cp112');
  if (is_ayam_starter() && $exist_pakan_finisher) {
    $fit_mul *= 800;
    $str_mul[] = 'amust-wrong';
    //

  } elseif (is_ayam_grower_finisher() && $exist_pakan_starter) {
    $fit_mul *= 800;
    $str_mul[] = 'amust-wrong';
    //
  }


  $amust_part = 0.0;
  if (is_ayam_starter())
    $amust_part = (float) ($aagent['mats']['crum591']['part'] ?? 0.0);

  elseif (is_ayam_grower_finisher()) {
    if (isset($aagent['mats']['crum592']))
      $amust_part = (float) ($aagent['mats']['crum592']['part'] ?? 0.0);
    elseif (isset($aagent['mats']['cp112']))
      $amust_part = (float) ($aagent['mats']['cp112']['part'] ?? 0.0);
  }

  $amust_perc = 0.0;
  if (is_ayam_starter())
    $amust_perc = (float) ($aagent['mats']['crum591']['part_percent'] ?? 0.0);

  elseif (is_ayam_grower_finisher()) {
    if (isset($aagent['mats']['crum592']))
      $amust_perc = (float) ($aagent['mats']['crum592']['part_percent'] ?? 0.0);
    elseif (isset($aagent['mats']['cp112']))
      $amust_perc = (float) ($aagent['mats']['cp112']['part_percent'] ?? 0.0);
  }

  // print_rdie($amust_perc);
  if (is_ayam_starter() && $amust_perc > 50) {
    $fit_mul *= 3;
    $str_mul[] = 'amust-perc-many';
  } elseif ($amust_perc > 20) {
    $fit_mul *= 3;
    $str_mul[] = 'amust-perc-many';
  }


  // make sure always bigger 1000 for division rounding at fitness below
  if ($aagent['price_per_kg'] < 1000) {
    $aagent['err'] = 'price too LOW';
    $fit_mul *= 900;
    $str_mul[] = 'price-min';
  }


  $karbo_protein = 1.0;
  if (is_ayam_starter())
    $karbo_protein = (round($aagent['protein'], 0) * 5) + (round($aagent['karbo'], 0) * 1);
  // elseif (is_ayam_grower())
  //   $karbo_protein = round($aagent['protein'], 0) + (round($aagent['karbo'], 0) * 2);
  elseif (is_ayam_finisher())
    $karbo_protein = (round($aagent['protein'], 0) * 1) + (round($aagent['karbo'], 0) * 5);
  // elseif (is_ayam_sow())
  //   $karbo_protein = (round($aagent['protein'], 0) * 2) + (round($aagent['karbo'], 0) * 1);

  $karbo_protein = round($karbo_protein, 0);


  $lemak_serat = $aagent['serat'] + $aagent['lemak'];
  $lemak_serat = round($lemak_serat, 0);

  // $parts_config = $aagent['avg_part'] * $aagent['sum_part'] * $aagent['non_zero'];
  $parts_config = $aagent['sum_part'] * pow($aagent['non_zero'], 1.5);
  $parts_config = round($parts_config, 1);
  // print_rdie($parts_config);

  // $karbo_protein = $aagent['protein'] + ($aagent['karbo'] * 5);
  // $fitness = $aagent['price_per_kg'] * $lemak_serat / ($aagent['protein'] + 10 * $aagent['karbo'] + 100 * $aagent['non_zero']);
  // $fitness = $aagent['price_per_kg'] * $lemak_serat * $amust_part * $aagent['non_zero'] * $aagent['avg_part'] * $aagent['sum_part']  / ($aagent['protein'] + $aagent['karbo']);
  // $fitness = $aagent['price_per_kg'] / ($aagent['karbo']);

  $price_near = round($aagent['price_per_kg'] / 1000, 0);
  $price_pow = round(pow($price_near, 1.2), 1);

  $fitness = $price_pow * $parts_config / $karbo_protein * $lemak_serat * $fit_mul;
  $aagent['fitness'] = round($fitness, 3);

  // dbg fit_mul
  $aagent['str_mul'] = implode(', ', $str_mul);
  $aagent['err'] = implode(', ', $str_err);

  // print_rdie($aagent);
}

function ga_compute_agent_nutrition_percent(array &$aagent)
{
  global $master_matr_arr, $def_mix_cap;

  $sum_part = 0;
  $matr_arr_idx = array_keys($master_matr_arr);
  // shuffle($matr_arr_idx);

  // init zeroing
  $aagent = array_replace($aagent, array(
    'protein' => 0,
    'karbo' => 0,
    'lemak' => 0,
    'serat' => 0,
    'lain' => 0,
  ));
  // print_rdie($aagent);


  $nuts_arr = array('protein', 'karbo', 'lemak', 'serat', 'lain');

  // compute kg nutrition per material
  foreach ($aagent['mats'] as $amat => $apart) {
    $amat_data = $master_matr_arr[$amat];
    // print_rdie(array($amat_data, $amat, $apart));

    $sum_nut_kg = 0;
    $apart_nuts = array();
    foreach ($nuts_arr as $anut_name) {
      $anut_kg = $amat_data[$anut_name] / 100 * $apart['part_kg'];
      // $anut_kg = round($anut_kg, 1);
      $apart_nuts[$anut_name] = $anut_kg;

      // sum check
      $sum_nut_kg += $anut_kg;

      $idx_name = 'kg_' . $anut_name;
      $apart[$idx_name] = $anut_kg;

      // merge
      $aagent['mats'][$amat] = $apart;
    }
    //  print_rdie($apart_nuts);

    // check delta
    $del_sum = abs($sum_nut_kg - $apart['part_kg']);

    // print_rdie($del_sum);
    if (!is_zero($del_sum, 0)) print_rdie(array($del_sum, $apart_nuts));
  }


  // compute sum kg nutrition for all material
  $all_sum = 0;
  $sum_nut_kg_all = array();
  foreach ($nuts_arr as $anut_name) {
    $sum_nut_kg = 0;
    $idx_name = 'kg_' . $anut_name;

    foreach ($aagent['mats'] as $amat => $apart) {
      // print_rdie(array($apart, $apart[$idx_name], $anut_name));
      $sum_nut_kg += $apart[$idx_name];
    }

    $sum_nut_kg_all[$anut_name] = $sum_nut_kg;
    $all_sum += $sum_nut_kg;
  }
  // print_rdie(array($sum_nut_kg_all, $all_sum));

  // check delta
  $del_sum = abs($all_sum - $def_mix_cap);
  if (!is_zero($del_sum)) print_rdie(array($aagent, $del_sum));


  // compute percent nutrition
  foreach ($nuts_arr as $anut_name) {
    $apercent = $sum_nut_kg_all[$anut_name] / $def_mix_cap * 100;
    $apercent = round($apercent, 1);
    $aagent[$anut_name] = $apercent;
  }

  // print_rdie($aagent);
}

function ga_init_agent(array $aref = array(), float $min_part = 1.0): array
{
  global $master_matr_arr, $def_mix_cap, $ayam_target;

  $aref_ops = isset($aref['ops']) ? $aref['ops'] : '';

  $aagent = array(
    // material parts
    'mats' => array(),

    // prices
    'tot_price' => 0,
    'price_per_kg' => 0,

    // nutritions, in percent
    'protein' => 0,
    'karbo' => 0,
    'lemak' => 0,
    'serat' => 0,
    'lain' => 0,

    // non zero parts
    'non_zero' => 0,
    'sum_part' => 0,
    'fitness' => PHP_INT_MAX,
    'err' => '',
    'ops' => $aref_ops,
  );

  $matr_arr_idx = array_keys($master_matr_arr);

  // init material parts
  $aagent_mats = array();
  foreach ($matr_arr_idx as $amat_name) {
    $aagent_mats[$amat_name]['part'] = 0.0;
  }


  // init part|bagian
  $max_part = 5;
  $non_zero = 0;
  $avg_part = 0;
  $sum_part = 0;
  shuffle($matr_arr_idx);
  shuffle($matr_arr_idx);
  // print_rdie($matr_arr_idx);
  foreach ($matr_arr_idx as $amat_name) {
    $apart = 0;
    $aagent_mats[$amat_name] = array();

    if (is_ayam_starter()) {
      if (is_mat_for_starter($amat_name)) $apart = 1;
      elseif (stristr($amat_name, 'crum') || stristr($amat_name, 'cp')) $apart = 0;
      else $apart = random_float($min_part, $max_part);
      //

    } else { // finisher, grower
      if (is_mat_for_grower_finisher($amat_name)) $apart = 1;
      elseif (stristr($amat_name, 'crum')) $apart = 0;
      else $apart = random_float($min_part, $max_part);
      //
    }

    if (
      !(stristr($amat_name, 'pur') ||
        stristr($amat_name, 'kons') ||
        stristr($amat_name, 'cp') ||
        stristr($amat_name, 'crum')) &&
      $non_zero < 3 &&
      is_zero($apart)
    ) {
      $apart = random_float($min_part, $max_part);
    }

    // rounding
    $apart = round($apart, 1);
    $apart = $non_zero <= 3 ? round($apart, 1) : 0;

    $aagent_mats[$amat_name]['part'] = $apart; // part per material
    $sum_part += $apart;
    if ($apart > 0) $non_zero++;
  }
  // print_rdie($aagent_mats);


  /*
  // make sure non_zero > 3
  $aloop = 0;
  $matr_arr_idx_random = $matr_arr_idx;
  $max_loop = count($matr_arr_idx_random);
  while ($non_zero < 4 && $aloop < $max_loop) {
    $aloop++;
    shuffle($matr_arr_idx_random);
    $amat_name = array_pop($matr_arr_idx_random);
    // print_rdie($amat_name);

    if ($aagent_mats[$amat_name]['part'] < 1) {
      $apart = round(random_float(1, 3), 1);
      $aagent_mats[$amat_name]['part'] += $apart; // part per material
      $sum_part += $apart;
      if ($apart > 0) $non_zero++;
    }
  }
  print_rdie($aagent_mats);


  // make sure sum_part > 5
  $aloop = 0;
  $matr_arr_idx_random = $matr_arr_idx;
  $max_loop = count($matr_arr_idx_random);
  while ($sum_part < 5 && $aloop < $max_loop) {
    $aloop++;
    shuffle($matr_arr_idx_random);
    $amat_name = array_pop($matr_arr_idx_random);
    // print_rdie($amat_name);

    if ($aagent_mats[$amat_name]['part'] < 1) {
      $apart = round(random_float(1, 3), 1);
      $aagent_mats[$amat_name]['part'] += $apart; // part per material
      $sum_part += $apart;
      if ($apart > 0) $non_zero++;
    }
  }
  print_rdie($aagent_mats);
  //*/


  $avg_part = $sum_part / $non_zero;

  $aagent = array(
    'mats' => $aagent_mats,
    'non_zero' => $non_zero, // non zero parts
    'sum_part' => $sum_part,
    'avg_part' => $avg_part,
    'fitness' => PHP_INT_MAX,
  );

  return $aagent;
}

function ga_compute_agent_part_percent_price(array &$aagent)
{
  global $master_matr_arr, $def_mix_cap;
  global $suplemen_price_per_kg, $suplemen_price_per_mix;


  $sum_part = 0;
  $matr_arr_idx = array_keys($master_matr_arr);
  // shuffle($matr_arr_idx);
  // print_rdie($matr_arr_idx);

  $non_zero = 0;
  $aagent_mats = $aagent['mats'];
  foreach ($matr_arr_idx as $amat_name) {
    $apart = $aagent_mats[$amat_name]['part']; // part per material
    $sum_part += $apart;
    if ($apart > 0) $non_zero++;
  }
  // print_rdie($aagent_mats);


  if (is_zero($sum_part)) {
    print_rdie($aagent);
  }

  // compute prosen & kgpart
  $min_percent = PHP_INT_MAX;
  $max_percent = 0;
  $tot_price = 0;
  foreach ($matr_arr_idx as $amat_name) {
    $apercent = $aagent_mats[$amat_name]['part'] / $sum_part * 100;
    // $apercent = round($apercent, 1);
    if ($apercent > 0) {
      $min_percent = min($apercent, $min_percent);
      $max_percent = max($apercent, $max_percent);
    }

    $akgpart = $apercent / 100 * $def_mix_cap;
    // $akgpart = round($akgpart, 1);
    $aagent_mats[$amat_name]['part_percent'] = $apercent; // percent per material
    $aagent_mats[$amat_name]['part_kg'] = $akgpart; // kg per material, see $def_mix_cap
    // print_rdie($aagent_mats);

    // compute price per material
    $apart_price = $akgpart * $master_matr_arr[$amat_name]['harga'];
    // $apart_price = round($apart_price, 1);
    //print_rdie($amat_price);
    $aagent_mats[$amat_name]['part_price'] = $apart_price;

    $tot_price += $apart_price;
  }
  // print_rdie($aagent_mats);
  // print_rdie($tot_price);

  // add suplemen price per mix to total price, if any
  if (!is_zero($suplemen_price_per_mix)) {
    $tot_price += $suplemen_price_per_mix;
  }

  $price_per_kg = $tot_price / $def_mix_cap;
  $price_per_kg = ceil($price_per_kg);

  $avg_part = $sum_part / $non_zero;

  $aagent = array(
    'mats' => $aagent_mats,
    'tot_price' => $tot_price,
    'price_per_kg' => $price_per_kg,
    'non_zero' => $non_zero, // non zero parts
    'sum_part' => $sum_part,
    'avg_part' => $avg_part,
    'min_percent' => $min_percent,
    'max_percent' => $max_percent,
    'fitness' => PHP_INT_MAX,
    'err' => '',
  );
  // print_rdie($aagent);
}

function ga_fix_agent_mat_part(array &$aagent)
{
  global $master_matr_arr, $def_mix_cap;

  $non_zero = (int) $aagent['non_zero'];
  if ($non_zero <= 5) return;

  $mat_names = array_keys($master_matr_arr);
  foreach ($mat_names as $amat_name) {
    if (
      stristr($amat_name, 'pur') ||
      stristr($amat_name, 'kons') ||
      stristr($amat_name, 'cp') ||
      stristr($amat_name, 'crum')
    ) continue;

    $aperc = 0;
    if (isset($aagent['mats'][$amat_name]['part_percent']))
      $aperc = $aagent['mats'][$amat_name]['part_percent'];

    if ($aperc <= TOO_SMALL_PERCENT) { // too small
      // print_rdie($aagent);
      $aagent['mats'][$amat_name]['part'] = 0;
      $aagent['mats'][$amat_name]['part_percent'] = 0.0;
      $aagent['mats'][$amat_name]['part_kg'] = 0.0;
      $aagent['mats'][$amat_name]['part_price'] = 0.0;
    }
  }

  ga_compute_agent_part_percent_price($aagent);
}

/**
 * init main population
 */
function ga_init_pop(int $num = 100): array
{
  $agent_arr = array();
  // $fails = 0;

  for ($i = 0; $i < $num; $i++) {

    $aagent = ga_init_agent();
    ga_compute_agent_part_percent_price($aagent);
    ga_fix_agent_mat_part($aagent);

    ga_compute_agent_nutrition_percent($aagent);
    ga_compute_agent_fitness($aagent);
    // print_rdie($aagent);

    // $afit = (float) $aagent['fitness'];
    // $adel = (float) abs($afit - (float) PHP_INT_MAX);
    // if (is_zero($adel)) {
    //   $fails++;
    //   if ($fails < $num) $num--;
    // }

    // make sure distance is OK, not too close to other agents
    if (count($agent_arr) > 0) {
      $is_good = ga_is_good_agent_to_population($agent_arr, $aagent);
      if (!$is_good) {
        echo ".";
        $i--;
        continue;
      }
    }

    $agent_arr[] = $aagent;
  }



  /*
  // use all mats
  for ($i = 0; $i < $num; $i++) {

    $aagent = ga_init_agent(array(), 1.0);
    ga_compute_agent_part_percent_price($aagent);
    ga_fix_agent_mat_part($aagent);

    ga_compute_agent_nutrition_percent($aagent);
    ga_compute_agent_fitness($aagent);
    // print_rdie($aagent);

    // $afit = (float) $aagent['fitness'];
    // $adel = (float) abs($afit - (float) PHP_INT_MAX);
    // if (is_zero($adel)) {
    //   $fails++;
    //   if ($fails < $num) $num--;
    // }

    $agent_arr[] = $aagent;
  }
  //*/

  return $agent_arr;
}


// ascending sort
function ga_sort_population(array &$apop)
{
  $num = count($apop);
  for ($i = 0; $i < $num - 1; $i++) {
    $agent_i = $apop[$i];
    for ($j = $i + 1; $j < $num; $j++) {
      $agent_j = $apop[$j];
      if ($agent_j['fitness'] < $agent_i['fitness']) {
        $swap = $agent_i;
        $apop[$i] = $agent_j;
        $apop[$j] = $swap;

        $agent_i = $apop[$i];
        $agent_j = $apop[$j];
      }
    }
  }

  for ($i = 0; $i < $num; $i++) {
    $agent_i = $apop[$i];
    $afit_i = $agent_i['fitness'];
    $aerr_i = $agent_i['err'];
    // echo "\n $afit_i \t$aerr_i";
  }
  // print_rdie($afit_i);

  // $apop = array_values($apop);
}


function ga_select(array $apop): array
{
  if (count($apop) < 5) print_rdie('FATAL ERROR');

  // init and auto append worst agent
  $agent_worst = array_pop($apop);
  $bpop = array($agent_worst);

  shuffle($apop);
  shuffle($apop);
  $ag1 = array_pop($apop);
  $ag2 = array_pop($apop);
  $aga = $ag1['fitness'] < $ag2['fitness'] ? $ag1 : $ag2;
  $bpop[] = $aga;

  shuffle($apop);
  shuffle($apop);
  $ag1 = array_pop($apop);
  $ag2 = array_pop($apop);
  $agb = $ag1['fitness'] < $ag2['fitness'] ? $ag1 : $ag2;
  $bpop[] = $agb;

  return $bpop;
}


/**
 * return array population
 */
function ga_doing_crossover(array $aga, array $agb): array
{
  global $master_matr_arr, $def_mix_cap;

  $ag1 = ga_init_agent(); // blx alpha
  $ag2 = ga_init_agent(); // middle
  $ag3 = ga_init_agent(); // add
  $ag4 = ga_init_agent(); // max
  $ag5 = ga_init_agent(); // min
  $ag6 = ga_init_agent(); // delta
  $ag7 = ga_init_agent(); // 1 value of any parent
  // print_rdie($ag1);

  $matr_arr_idx = array_keys($master_matr_arr);
  // shuffle($matr_arr_idx);
  // print_rdie($matr_arr_idx);

  // compute part|bagian
  $alpha = 0.2; // 20%
  $amust_min = 0.9;
  foreach ($matr_arr_idx as $mat_name) {
    // print_rdie($aga['mats'][$mat_name]);

    // blx alpha
    $vala = $aga['mats'][$mat_name]['part'];
    $valb = $agb['mats'][$mat_name]['part'];
    $amax = round(max($vala, $valb), 1);
    $amin = round(min($vala, $valb), 1);
    $adel = abs($amax - $amin);
    $bmin = max(0.1, $amin - ($alpha * $adel));
    $bmax = $amax + ($alpha * $adel);
    $bmin = min($bmin, $bmax);
    $bmax = max($bmin, $bmax);
    $ag1['mats'][$mat_name]['part'] = !is_zero($adel) ? round(random_float($bmin, $bmax), 1) : 0;

    // middle
    $ag2['mats'][$mat_name]['part'] = round(($amin + $amax) / 2, 1);

    // add
    $ag3['mats'][$mat_name]['part'] = round($amin + $amax, 1);

    // max
    $ag4['mats'][$mat_name]['part'] = round($amax, 1);

    // min
    $ag5['mats'][$mat_name]['part'] = round($amin, 1);

    // delta
    $ag6['mats'][$mat_name]['part'] = round($adel, 1);

    // 1 value of any parent
    $ag7['mats'][$mat_name]['part'] = random_int(1, 10) < 5 ? round($vala, 1) : round($valb, 1);


    // forcing crum always exists
    $b1 = is_mat_for_grower_finisher($mat_name) && is_ayam_grower_finisher();
    $b2 = is_mat_for_starter($mat_name) && is_ayam_starter();
    if ($b1 || $b2) {
      $ag1['mats'][$mat_name]['part'] = max($ag1['mats'][$mat_name]['part'], $amust_min);
      $ag2['mats'][$mat_name]['part'] = max($ag2['mats'][$mat_name]['part'], $amust_min);
      $ag3['mats'][$mat_name]['part'] = max($ag3['mats'][$mat_name]['part'], $amust_min);
      $ag4['mats'][$mat_name]['part'] = max($ag4['mats'][$mat_name]['part'], $amust_min);
      $ag5['mats'][$mat_name]['part'] = max($ag5['mats'][$mat_name]['part'], $amust_min);
      $ag6['mats'][$mat_name]['part'] = max($ag6['mats'][$mat_name]['part'], $amust_min);
      $ag7['mats'][$mat_name]['part'] = max($ag7['mats'][$mat_name]['part'], $amust_min);
    }
  }
  // print_rdie($ag4);

  // compute non_zero
  $non_zero1 = 0;
  $non_zero2 = 0;
  $non_zero3 = 0;
  $non_zero4 = 0;
  $non_zero5 = 0;
  $non_zero6 = 0;
  $non_zero7 = 0;
  foreach ($matr_arr_idx as $mat_name) {
    if ($ag1['mats'][$mat_name]['part'] > 0) $non_zero1++;
    if ($ag2['mats'][$mat_name]['part'] > 0) $non_zero2++;
    if ($ag3['mats'][$mat_name]['part'] > 0) $non_zero3++;
    if ($ag4['mats'][$mat_name]['part'] > 0) $non_zero4++;
    if ($ag5['mats'][$mat_name]['part'] > 0) $non_zero5++;
    if ($ag6['mats'][$mat_name]['part'] > 0) $non_zero6++;
    if ($ag7['mats'][$mat_name]['part'] > 0) $non_zero7++;
  }


  $ag1['non_zero'] = $non_zero1;
  $ag2['non_zero'] = $non_zero2;
  $ag3['non_zero'] = $non_zero3;
  $ag4['non_zero'] = $non_zero4;
  $ag5['non_zero'] = $non_zero5;
  $ag6['non_zero'] = $non_zero6;
  $ag7['non_zero'] = $non_zero7;

  $ag1['ops'] = 'crossover1';
  $ag2['ops'] = 'crossover2';
  $ag3['ops'] = 'crossover3';
  $ag4['ops'] = 'crossover4';
  $ag5['ops'] = 'crossover5';
  $ag6['ops'] = 'crossover6';
  $ag7['ops'] = 'crossover7';

  $ags = array();
  if ($non_zero1 > 0) $ags[] = $ag1;
  if ($non_zero2 > 0) $ags[] = $ag2;
  if ($non_zero3 > 0) $ags[] = $ag3;
  if ($non_zero4 > 0) $ags[] = $ag4;
  if ($non_zero5 > 0) $ags[] = $ag5;
  if ($non_zero6 > 0) $ags[] = $ag6;
  if ($non_zero7 > 0) $ags[] = $ag7;

  return $ags;
}

function ga_crossover(array $apop): array
{
  $num = count($apop);
  if ($num < 3) print_rdie('FATAL ERROR');


  $the_pop = array();
  for ($i = 0; $i < $num - 1; $i++) {
    $aga = $apop[$i];
    for ($j = $i + 1; $j < $num; $j++) {
      $agb = $apop[$j];

      // ga_doing_crossover returning population = array of agents
      $cr_pop = ga_doing_crossover($aga, $agb);
      $the_pop = array_merge($the_pop, $cr_pop);
    }
  }

  return $the_pop;
}

function ga_mutation(array $apop, string $mut_type = ''): array
{
  global $master_matr_arr, $def_mix_cap;

  $num = count($apop);
  if ($num < 3) print_rdie('FATAL ERROR');

  $matr_arr_idx = array_keys($master_matr_arr);
  $the_pop = array();
  $amust_min = 0.9;

  for ($i = 0; $i < $num; $i++) {
    $aga = $apop[$i];
    $ag1 = $aga; // add random
    $ag2 = $aga; // divide 2-5
    $ag3 = $aga; // unzero
    $ag4 = $aga; // zero one material
    $ag5 = $aga; // multiply by 1.1-2.0

    $ag_tikan = $aga; // forcing tikan
    $ag_tkedelai = $aga; // forcing tkedelai

    $done1 = 0;
    $done2 = 0;
    $done3 = 0;
    $done4 = 0;
    $done5 = 0;

    shuffle($matr_arr_idx);
    shuffle($matr_arr_idx);
    foreach ($matr_arr_idx as $mat_name) {

      // add random part
      if ($done1 < 1 && !is_zero($ag1['mats'][$mat_name]['part'])) {
        $add_part = random_int(0, 2);
        if ($add_part > 0) {
          $ag1['mats'][$mat_name]['part'] = round($ag1['mats'][$mat_name]['part'] + $add_part, 1);
          $done1++;
        }
      }

      // divide part by 2-5
      if ($done2 < 1 && !is_zero($ag2['mats'][$mat_name]['part'])) {
        $div_part = random_int(2, 5);
        $ag2['mats'][$mat_name]['part'] = round($ag2['mats'][$mat_name]['part'] / $div_part, 1);
        $done2++;
      }

      // unzero empty part
      if ($done3 < 1 && is_zero($ag3['mats'][$mat_name]['part'])) {
        $not_zero_part = random_int(0, 2);
        if ($not_zero_part > 0) {
          $ag3['mats'][$mat_name]['part'] = $not_zero_part;
          $done3++;
        }
      }

      // zero one material
      if ($done4 < 1 && !is_zero($ag4['mats'][$mat_name]['part'])) {
        $ag4['mats'][$mat_name]['part'] = 0.0;
        $done4++;
      }

      // multiply by factor: 1.1-2.0
      if ($done5 < 1 && !is_zero($ag5['mats'][$mat_name]['part'])) {
        $mul_part = random_float(1.1, 2.0);
        $ag5['mats'][$mat_name]['part'] *= $mul_part;
        $done5++;
      }


      // $ag1['mats'][$mat_name]['part'] = round($aga['mats'][$mat_name]['part'] + random_int(0, 2), 1);
      // $ag2['mats'][$mat_name]['part'] = round($aga['mats'][$mat_name]['part'] / random_int(2, 5), 1);
      // if (is_zero($aga['mats'][$mat_name]['part'])) $ag3['mats'][$mat_name]['part'] = random_int(0, 2);
      // else $ag3['mats'][$mat_name]['part'] = 0;

      // forcing crum always exists
      $b1 = is_mat_for_grower_finisher($mat_name) && is_ayam_grower_finisher();
      $b2 = is_mat_for_starter($mat_name) && is_ayam_starter();
      if ($b1 || $b2) {
        $ag1['mats'][$mat_name]['part'] = max($ag1['mats'][$mat_name]['part'], $amust_min);
        $ag2['mats'][$mat_name]['part'] = max($ag2['mats'][$mat_name]['part'], $amust_min);
        $ag3['mats'][$mat_name]['part'] = max($ag3['mats'][$mat_name]['part'], $amust_min);
        $ag4['mats'][$mat_name]['part'] = max($ag4['mats'][$mat_name]['part'], $amust_min);
        $ag5['mats'][$mat_name]['part'] = max($ag5['mats'][$mat_name]['part'], $amust_min);
      }
    }

    // forcing tikan
    $mat_name = 'tikan';
    if (isset($ag_tikan['mats'][$mat_name])) {
      $ag_tikan['mats'][$mat_name]['part'] = random_float(0.5, 2.1);
    }

    // forcing tkedelai
    $mat_name = 'tkedelai';
    if (isset($ag_tkedelai['mats'][$mat_name])) {
      $ag_tkedelai['mats'][$mat_name]['part'] = random_float(0.5, 2.1);
    }

    // compute non_zero
    $non_zero1 = 0;
    $non_zero2 = 0;
    $non_zero3 = 0;
    $non_zero4 = 0;
    $non_zero5 = 0;
    $non_zero_tikan = 0;
    $non_zero_tkedelai = 0;

    foreach ($matr_arr_idx as $mat_name) {
      if ($ag1['mats'][$mat_name]['part'] > 0) $non_zero1++;
      if ($ag2['mats'][$mat_name]['part'] > 0) $non_zero2++;
      if ($ag3['mats'][$mat_name]['part'] > 0) $non_zero3++;
      if ($ag4['mats'][$mat_name]['part'] > 0) $non_zero4++;
      if ($ag5['mats'][$mat_name]['part'] > 0) $non_zero5++;
      if ($ag_tikan['mats'][$mat_name]['part'] > 0) $non_zero_tikan++;
      if ($ag_tkedelai['mats'][$mat_name]['part'] > 0) $non_zero_tkedelai++;
    }

    $ag1['non_zero'] = $non_zero1;
    $ag2['non_zero'] = $non_zero2;
    $ag3['non_zero'] = $non_zero3;
    $ag4['non_zero'] = $non_zero4;
    $ag5['non_zero'] = $non_zero5;
    $ag_tikan['non_zero'] = $non_zero_tikan;
    $ag_tkedelai['non_zero'] = $non_zero_tkedelai;

    $ag1['ops'] = "mutation: $mut_type";
    $ag2['ops'] = "mutation: $mut_type";
    $ag3['ops'] = "mutation: $mut_type";
    $ag4['ops'] = "mutation: $mut_type";
    $ag5['ops'] = "mutation: $mut_type";
    $ag_tikan['ops'] = "mutation: $mut_type";
    $ag_tkedelai['ops'] = "mutation: $mut_type";
    // print_rdie($ag1);
    // print_rdie($ag2);

    // merge
    if ($non_zero1 > 0) $the_pop[] = $ag1;
    if ($non_zero2 > 0) $the_pop[] = $ag2;
    if ($non_zero3 > 0) $the_pop[] = $ag3;
    if ($non_zero4 > 0) $the_pop[] = $ag4;
    if ($non_zero5 > 0) $the_pop[] = $ag5;
    if ($non_zero_tikan > 0) $the_pop[] = $ag_tikan;
    if ($non_zero_tkedelai > 0) $the_pop[] = $ag_tkedelai;
  }

  return $the_pop;
}


function ga_compute_population(array &$apop)
{
  foreach ($apop as $idx => $aagent) {
    ga_compute_agent_part_percent_price($aagent);
    ga_fix_agent_mat_part($aagent);

    ga_compute_agent_nutrition_percent($aagent);
    ga_compute_agent_fitness($aagent);
    $apop[$idx] = $aagent;
    // print_rdie($aagent);
  }
}

function ga_dist_nutri(array $aga, array $agb): float
{
  $del_karbo = (float) abs($aga['karbo'] - $agb['karbo']);
  $del_protein = (float) abs($aga['protein'] - $agb['protein']);
  $del_lemak = (float) abs($aga['lemak'] - $agb['lemak']);
  $del_serat = (float) abs($aga['serat'] - $agb['serat']);
  $del_nutri = calculate_rms(array($del_karbo, $del_protein, $del_lemak, $del_serat));
  return $del_nutri;
}

function ga_dist_mat_part(array $aga, array $agb): float
{
  global $master_matr_arr, $def_mix_cap;

  // $dbg = array();
  $mat_names = array_keys($master_matr_arr);
  $mat_part_dists = array();
  foreach ($mat_names as $amat_name) {
    $mat_part_a = (float) $aga['mats'][$amat_name]['part'];
    $mat_part_b = (float) $agb['mats'][$amat_name]['part'];
    $mat_part_dists[] = abs($mat_part_a - $mat_part_b);

    // $dbg[] = array($mat_part_a, $mat_part_b);
  }
  // print_rdie($mat_part_dists);

  $dist_mat = calculate_rms($mat_part_dists);
  // if ($dist_mat < 0.1) print_rdie(array($mat_part_dists, $dist_mat, $dbg));

  return $dist_mat;
}


function ga_is_good_agent_to_agent(array $aga, array $agb): bool
{
  $is_good = true;

  // check material distance
  if ($is_good) {
    $dist_mat = ga_dist_mat_part($aga, $agb);
    $is_good = $dist_mat > 0.5;
  }

  // check nutrition distance
  if ($is_good) {
    $dist_nutri = ga_dist_nutri($aga, $agb);
    $is_good = $dist_nutri > 0.5;
  }

  /*
  if ($is_good) {
    // check nutrition
    $dist_nutri = ga_dist_nutri($aga, $agb);
    $is_bad1 = $dist_nutri < 0.1;

    // check material parts
    $dist_mat = ga_dist_mat_part($aga, $agb);
    $is_bad2 = $dist_mat < 0.1;

    // check part configs
    $del_non_zero = (float) abs($aga['non_zero'] - $agb['non_zero']);
    $del_sum_part = (float) abs($aga['sum_part'] - $agb['sum_part']);
    $del_avg_part = (float) abs($aga['avg_part'] - $agb['avg_part']);
    $del_min_percent = (float) abs($aga['min_percent'] - $agb['min_percent']);
    // $is_bad3 = $del_non_zero < 1 && $del_sum_part < 0.1 && $del_avg_part < 0.1 && $del_min_percent < 1;
    // $is_bad3 = $del_sum_part < 0.01 && $del_avg_part < 0.01;

    // check price_per_kg
    $del_price = abs($aga['price_per_kg'] - $agb['price_per_kg']);

    // $is_good = !$is_bad1 && !$is_bad2 && $del_price > 100;
    // $is_good = !$is_bad1 && $del_price > 10;
    $is_good = !$is_bad1 && $is_bad2;
    $is_good = !$is_bad1;
  }
  //*/

  // if ($is_good) {
  //   // check price_per_kg
  //   $del_price = (float) abs($aga['price_per_kg'] - $agb['price_per_kg']);
  //   $max_price = (float) max($aga['price_per_kg'], $agb['price_per_kg']);
  //   $pro_price = round($del_price / $max_price * 100);
  //   $is_good = $pro_price > 1; // more than 1% difference
  // }

  if ($is_good) {
    // check fitness
    $del_fitness = (float) abs($aga['fitness'] - $agb['fitness']);
    $max_fitness = (float) max($aga['fitness'], $agb['fitness']);
    $pro_fitness = round($del_fitness / $max_fitness * 100);
    $is_good = $pro_fitness > 5; // more than 5% difference in fitness
  }

  return $is_good;
}

function ga_is_good_agent_to_population(array &$main_pop, array $aagent): bool
{
  $is_good = false;
  foreach ($main_pop as $pop_agent) {
    $is_good = ga_is_good_agent_to_agent($pop_agent, $aagent);
    if (!$is_good) break;
  }
  return $is_good;
}

/**
 * return int number of new good agent
 */
function ga_merge_main_population(array &$main_pop, array &$res_pop, bool $doverbose = true): int
{
  global $pop_num;

  // worst agent
  $last_idx = array_key_last($main_pop);
  $agent_worst = $main_pop[$last_idx];
  $worst_fitness = (float) $agent_worst['fitness'];

  $good_num = 0;
  foreach ($res_pop as $idx => $aagent) {
    $aagent_fitness = (float) $aagent['fitness'];
    // print_rdie($aagent);

    if (
      $aagent_fitness < $worst_fitness &&
      // ga_is_good_agent_single($aagent) &&
      ga_is_good_agent_to_population($main_pop, $aagent)
    ) {
      $main_pop[] = $aagent;
      $good_num++;
    } else {
      unset($aagent);
      unset($res_pop[$idx]);
    }

    // print_rdie($aagent);
  }

  if ($good_num > 0) {
    if ($doverbose) echo "\t new=$good_num";
    else echo "$good_num ";
  }

  ga_sort_population($main_pop);
  $main_pop = array_slice($main_pop, 0, $pop_num);

  // ga_sort_population($main_pop);

  return $good_num;
}


function ga_print_agent(array $aagent, string $pre = '')
{
  global $ayam_target;

  $the_mats = array();
  foreach ($aagent['mats'] as $mat_name => $aparts) {
    if (is_zero($aparts['part'])) continue;

    /*
    $the_mats[$mat_name] = array(
      'part' => $aparts['part'],
      'perc' => $aparts['part_percent'],
    );
    //*/

    // $the_mats[] = $mat_name . "\tpart=" . round($aparts['part'],1) . "\t" . round($aparts['part_percent'], 1)."%";
    $the_mats[] = sprintf("%10s \tpart=%-.1f \tperc=%.0f%%", $mat_name, round($aparts['part'], 1), round($aparts['part_percent'], 1));
  }
  $the_mats = implode("\n", $the_mats);


  /*
  $final = array(
    'mats' => $the_mats,
    'price_per_kg' => $aagent['price_per_kg'],
    'protein' => round($aagent['protein'], 1),
    'karbo' => round($aagent['karbo'], 1),
  );
  //*/

  $the_err = (string) trim($aagent['err']);
  $the_err = !empty($the_err) ? "\tErr: $the_err" : '';
  $price_kg = number_format($aagent['price_per_kg'], 0, '.', ',');
  $final = sprintf(
    "\n%s \tProtein=%-.1f%% \tKarbo=%-.1f%% \tLemak=%-.1f%% \tSerat=%-.1f%% \tPrice=%s %s Mul=%s Fit=%-.3f nz=%d \n%s \n",
    $pre,
    round($aagent['protein'], 1),
    round($aagent['karbo'], 1),
    round($aagent['lemak'], 1),
    round($aagent['serat'], 1),
    $price_kg,
    $the_err,
    $aagent['str_mul'],
    $aagent['fitness'],
    $aagent['non_zero'],
    $the_mats
  );
  $final = "\n\n--- AYAM TARGET: $ayam_target $final";

  echo $final;
}


function ga_print_pop(array $apop, int $num = 3)
{
  for ($i = $num - 1; $i >= 0; $i--) {
    ga_print_agent($apop[$i], strval($i + 1) . ".");
  }
}


/**
 * return int number of new good agent
 */
function ga_main_loop(
  int $max_gen,
  int $max_gen_delta,
  array &$main_pop,
  bool $dolongloop,
  bool $doverbose
): int {
  // pre
  $pre_best_fit = PHP_INT_MAX;
  $unchanged_loop = 0;
  $all_good = 0;

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
    $new_good = ga_merge_main_population($main_pop, $res_pop, $doverbose);
    $all_good += $new_good;
    // print_rdie($main_pop[0]);

    // auto extend loop
    // if ($new_good > 0) $max_gen = (int) round($max_gen * 1.05);
    if ($new_good > 0) {
      $inc_good = min(3, $new_good);
      if ($dolongloop) {
        $max_gen += $max_gen_delta * $inc_good;
        $unchanged_loop -= 100;
      }
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

    if ($doverbose)
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

  return $all_good;
}



function ga_best_params(array $main_pop): array
{

  $min_price = PHP_INT_MAX;
  $max_price = 0;
  $min_protein = PHP_INT_MAX;
  $max_protein = 0;
  $min_karbo = PHP_INT_MAX;
  $max_karbo = 0;

  $good_agents = array();
  foreach ($main_pop as $idx => $aagent) {
    if (!empty($aagent['str_mul'])) continue;

    $good_agents[] = $aagent;

    // get minimum price
    if ($aagent['price_per_kg'] < $min_price) {
      $min_price = (float) $aagent['price_per_kg'];
    }
    if ($aagent['price_per_kg'] > $max_price) {
      $max_price = (float) $aagent['price_per_kg'];
    }

    // get maximum & minimum protein
    if ($aagent['protein'] > $max_protein) {
      $max_protein = (float) $aagent['protein'];
    }
    if ($aagent['protein'] < $min_protein) {
      $min_protein = (float) $aagent['protein'];
    }

    // get maximum & minimum karbo
    if ($aagent['karbo'] > $max_karbo) {
      $max_karbo = (float) $aagent['karbo'];
    }
    if ($aagent['karbo'] < $min_karbo) {
      $min_karbo = (float) $aagent['karbo'];
    }
  }

  // $min_price = (float) round_near($min_price, 100);
  // $max_price = (float) round_near($max_price, 100);
  $min_protein = (float) floor($min_protein);
  $max_protein = (float) ceil($max_protein);
  $min_karbo = (float) floor($min_karbo);
  $max_karbo = (float) ceil($max_karbo);

  $rets = array(
    'good_agents' => $good_agents,
    'good_count' => count($good_agents),
    'min_price' => $min_price,
    'max_price' => $max_price,
    'min_protein' => $min_protein,
    'max_protein' => $max_protein,
    'min_karbo' => $min_karbo,
    'max_karbo' => $max_karbo,
  );
  // print_rdie($rets);

  return $rets;
}



function ga_more_loop(int $max_gen, int $max_gen_delta, int $pop_num, array &$main_pop)
{
  global $protein_min, $protein_max, $karbo_min, $karbo_max, $price_max;
  global $lemak_max, $serat_max;

  $half_gen = (int) ($max_gen / 2);
  $new_strs = array();
  $small_loop = 0;

  while ($small_loop < 10) {
    $small_loop++;

    $rets = ga_best_params($main_pop);
    $good_count = $rets['good_count'];
    echo "\t goodc= $good_count ";
    if ($good_count < 4) {
      ga_main_loop($half_gen, $max_gen_delta, $main_pop, false, false);
      continue;
    }

    $cur_min_price = $rets['min_price'];
    $cur_max_price = $rets['max_price'];
    $cur_min_protein = $rets['min_protein'];
    $cur_max_protein = $rets['max_protein'];
    $cur_min_karbo = $rets['min_karbo'];
    $cur_max_karbo = $rets['max_karbo'];

    $new_strs = array();
    if ($cur_max_price < $price_max) {
      $price_max = $cur_max_price + 1000;
      $new_strs[] = "NEW PRICE MAX: $price_max";
    }

    if ($cur_max_protein > $protein_min) {
      $protein_min = $cur_max_protein;
      $protein_max = max($protein_max, $cur_max_protein + 5);
      $new_strs[] = "NEW PROTEIN MIN: $protein_min";
    }

    // if ($cur_min_karbo > $karbo_min) {
    //   $karbo_min = $cur_max_karbo;
    //   $karbo_max = max($karbo_max, $cur_max_karbo + 10);
    //   $new_strs[] = "NEW KARBO MIN: $karbo_min";
    // }
    // print_rdie($new_strs);

    if (!empty($new_strs)) {
      echo "\n\n--- " . implode("\n--- ", $new_strs) . "\n\n";

      // $new_pop = ga_init_pop($pop_num);
      // $main_pop = array_merge($main_pop, $new_pop);

      $main_pop = ga_init_pop($pop_num);
      ga_sort_population($main_pop);

      ga_main_loop($max_gen, $max_gen_delta, $main_pop, true, true);
      echo "\n\n--- " . implode("\n--- ", $new_strs) . "\n";
      break;
    }
  }
}
