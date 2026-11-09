<?php

function ga_is_good_agent_single(array $aagent): bool
{
  global $protein_min, $protein_max, $karbo_min, $karbo_max, $price_max;

  $is_bad = $aagent['non_zero'] < 3 ||
    $aagent['sum_part'] > 20 || $aagent['sum_part'] < 5;
  // $aagent['min_percent'] < 5 ||
  // $aagent['protein'] < $protein_min ||
  // $aagent['karbo'] < $karbo_min ||
  // $aagent['price_per_kg'] > $price_max;
  return !$is_bad;
}

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

  // bad agents
  if (!is_ayam_starter() && $aagent['non_zero'] <= 4) {
    $aagent['err'] = 'non zero <= 4 ';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 2;
    $str_mul[] = 'nonzero <= 4';
    //

  }
  // elseif ($aagent['non_zero'] > 5) {
  //   $aagent['err'] = 'non zero > 5';
  //   $aagent['fitness'] = PHP_INT_MAX;
  //   // print_rdie($aagent);
  //   // return;
  //   $fit_mul *= 30000;
  //   $str_mul[] = 'nonzero > 5';
  // }

  if ($aagent['sum_part'] > 20) {
    $aagent['err'] = 'too many parts';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 2000;
    $str_mul[] = 'sumpart-many';
  }

  if ($aagent['sum_part'] < 5) {
    $aagent['err'] = 'too few parts';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 200;
    $str_mul[] = 'sumpart-few';
  }

  if ($aagent['min_percent'] < 3) {
    $aagent['err'] = 'too small percent';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 200;
    $str_mul[] = 'minperc';
  }

  if ($aagent['lemak'] > $lemak_max) {
    $aagent['err'] = 'lemak too high';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 2;
    $str_mul[] = 'lemak-max';
  }

  if ($aagent['serat'] > $serat_max) {
    $aagent['err'] = 'serat too high';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 20;
    $str_mul[] = 'serat-max';
  }

  if ($aagent['protein'] < $protein_min) {
    $aagent['err'] = 'protein too low';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 20;
    $str_mul[] = 'prote-min';
    //

  } elseif ($aagent['protein'] > $protein_max) {
    $aagent['err'] = 'protein too high';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 3000;
    $str_mul[] = 'prote-max';
  }

  if ($aagent['karbo'] < $karbo_min) {
    $aagent['err'] = 'karbo too low';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    //return;
    $fit_mul *= 200;
    $str_mul[] = 'karbo-min';
    //

  } elseif ($aagent['karbo'] > $karbo_max) {
    $aagent['err'] = 'karbo too high';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    //return;
    $fit_mul *= 3000;
    $str_mul[] = 'karbo-max';
  }

  if ($aagent['price_per_kg'] > $price_max) {
    $aagent['err'] = 'price too high';
    // $aagent['fitness'] = PHP_INT_MAX;
    // print_rdie($aagent);
    // return;
    $fit_mul *= 50000;
    $str_mul[] = 'price-max';
    //

  }
  // elseif ($aagent['price_per_kg'] <= $price_max) {
  //   // $aagent['err'] = 'price too high';
  //   // $aagent['fitness'] = PHP_INT_MAX;
  //   // print_rdie($aagent);
  //   // return;
  //   $fit_mul *= 0.1;
  //   $str_mul[] = '< price-ok';
  // }


  $non_zero = (int) $aagent['non_zero'];
  $matr_names = array_keys($master_matr_arr);
  // if ($non_zero > 2)
  //   foreach ($matr_names as $amat_name) {
  //     if (stristr($amat_name, 'crum')) continue;

  //     $aperc = 0;
  //     if (isset($aagent['mats'][$amat_name]['part_percent']))
  //       $aperc = $aagent['mats'][$amat_name]['part_percent'];

  //     if (is_zero($aperc)) continue;

  //     if ($aperc < 3) { // too small
  //       $fit_mul *= 20000;
  //       $str_mul[] = 'mat-too-small-' . $amat_name;
  //     } elseif ($aperc > 60) { // too big
  //       $fit_mul *= 30000;
  //       $str_mul[] = 'mat-too-high-' . $amat_name;
  //     }
  //   }


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
        //
      } elseif ($aperc > $amax_perc && !is_zero($amax_perc)) {
        $fit_mul *= 300;
        //

      }
      //

    } else {
      if ($aperc < $amin_perc && !is_zero($amin_perc)) {
        $fit_mul *= 2000;
        $str_mul[] = 'amust-too-small-' . $amat_name;
        //

      } elseif ($aperc > $amax_perc && !is_zero($amax_perc)) {
        $fit_mul *= 3000;
        $str_mul[] = 'amust-too-big-' . $amat_name;
        //
      }
    }
  }

  // if ($non_zero > 2 && $non_zero <= 6) {
  //   $fit_mul *= 0.5;
  //   $str_mul[] = 'non-zero >2 && <=6';
  // }

  // if (!empty($str_mul)) print_rdie(array($str_mul, $fit_mul));





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
  $exist_crum591 = is_agent_mat_part_exists($aagent, 'crum591');
  $exist_crum592 = is_agent_mat_part_exists($aagent, 'crum592');
  if (is_ayam_starter() && $exist_crum592) {
    $fit_mul *= 30000;
    $str_mul[] = 'amust-wrong';
    //

  } elseif (is_ayam_grower_finisher() && $exist_crum591) {
    $fit_mul *= 30000;
    $str_mul[] = 'amust-wrong';
    //
  }


  $amust_part = 0.0;
  if (is_ayam_starter()) $amust_part = (float) $aagent['mats']['crum591']['part'];
  elseif (is_ayam_grower_finisher()) $amust_part = (float) $aagent['mats']['crum592']['part'];

  $amust_perc = 0.0;
  if (is_ayam_starter()) $amust_perc = (float) $aagent['mats']['crum591']['part_percent'];
  elseif (is_ayam_grower_finisher()) $amust_perc = (float) $aagent['mats']['crum592']['part_percent'];
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
    $fit_mul *= 50000000;
    $str_mul[] = 'price-min';
  }


  $karbo_protein = 1.0;
  if (is_ayam_starter())
    $karbo_protein = (round($aagent['protein'], 0) * 5) + (round($aagent['karbo'], 0) * 1);
  elseif (is_ayam_grower())
    $karbo_protein = round($aagent['protein'], 0) + (round($aagent['karbo'], 0) * 2);
  elseif (is_ayam_finisher())
    $karbo_protein = round($aagent['protein'], 0) + (round($aagent['karbo'], 0) * 5);
  // elseif (is_ayam_sow())
  //   $karbo_protein = (round($aagent['protein'], 0) * 2) + (round($aagent['karbo'], 0) * 1);

  $karbo_protein = round($karbo_protein, 1);


  $lemak_serat = $aagent['serat'] + $aagent['lemak'];
  // $parts_config = $aagent['avg_part'] * $aagent['sum_part'] * $aagent['non_zero'];
  $parts_config = $aagent['sum_part'] * $aagent['non_zero'];
  $parts_config = round($parts_config, 1);
  // print_rdie($parts_config);

  // $karbo_protein = $aagent['protein'] + ($aagent['karbo'] * 5);
  // $fitness = $aagent['price_per_kg'] * $lemak_serat / ($aagent['protein'] + 10 * $aagent['karbo'] + 100 * $aagent['non_zero']);
  // $fitness = $aagent['price_per_kg'] * $lemak_serat * $amust_part * $aagent['non_zero'] * $aagent['avg_part'] * $aagent['sum_part']  / ($aagent['protein'] + $aagent['karbo']);
  // $fitness = $aagent['price_per_kg'] / ($aagent['karbo']);

  $price_near = round($aagent['price_per_kg'] / 1000, 1);
  $price_pow = round(pow($price_near, 2), 2);

  $fitness = $price_pow * $parts_config / $karbo_protein * $fit_mul;
  $aagent['fitness'] = $fitness;

  // dbg fit_mul
  $aagent['str_mul'] = implode(', ', $str_mul);

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
    $aagent[$anut_name] = $apercent;
  }

  //print_rdie($aagent);
}

function ga_init_agent(array $aref = array(), float $min_part = 0.0): array
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

  $avg_part = 0;
  $sum_part = 0;
  $matr_arr_idx = array_keys($master_matr_arr);
  // shuffle($matr_arr_idx);
  // print_rdie($matr_arr_idx);

  // init part|bagian
  $non_zero = 0;
  $aagent_mats = array();
  foreach ($matr_arr_idx as $amat_name) {
    $aagent_mats[$amat_name] = array();

    if (is_ayam_starter()) {
      if ($amat_name == 'crum591') $apart = 1;
      elseif (stristr($amat_name, 'crum')) $apart = 0;
      else $apart = random_float($min_part, 2);
      //

    } else { // finisher, grower
      if ($amat_name == 'crum592') $apart = 1;
      elseif (stristr($amat_name, 'crum')) $apart = 0;
      else $apart = random_float($min_part, 2);
      //
    }
    // if ($non_zero < 3) $apart = random_int(1, 2);

    $aagent_mats[$amat_name]['part'] = $apart; // part per material
    $sum_part += $apart;
    if ($apart > 0) $non_zero++;
  }
  // print_rdie($aagent_mats);


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
      $apart = random_int(1, 3);
      $aagent_mats[$amat_name]['part'] = $apart; // part per material
      $sum_part += $apart;
      if ($apart > 0) $non_zero++;
    }
  }


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
      $apart = random_int(1, 3);
      $aagent_mats[$amat_name]['part'] = $apart; // part per material
      $sum_part += $apart;
      if ($apart > 0) $non_zero++;
    }
  }

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
  $tot_price = 0;
  foreach ($matr_arr_idx as $amat_name) {
    $apercent = $aagent_mats[$amat_name]['part'] / $sum_part * 100;
    // $apercent = round($apercent, 1);
    if ($apercent > 0) $min_percent = min($apercent, $min_percent);

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

  $price_per_kg = $tot_price / $def_mix_cap;
  $price_per_kg = round($price_per_kg, 0);

  $avg_part = $sum_part / $non_zero;

  $aagent = array(
    'mats' => $aagent_mats,
    'tot_price' => $tot_price,
    'price_per_kg' => $price_per_kg,
    'non_zero' => $non_zero, // non zero parts
    'sum_part' => $sum_part,
    'avg_part' => $avg_part,
    'min_percent' => $min_percent,
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
    if (stristr($amat_name, 'crum')) continue;

    $aperc = 0;
    if (isset($aagent['mats'][$amat_name]['part_percent']))
      $aperc = $aagent['mats'][$amat_name]['part_percent'];

    if ($aperc < 3) { // too small
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
  $fails = 0;

  for ($i = 0; $i < $num; $i++) {

    $aagent = ga_init_agent();
    ga_compute_agent_part_percent_price($aagent);
    ga_fix_agent_mat_part($aagent);

    ga_compute_agent_nutrition_percent($aagent);
    ga_compute_agent_fitness($aagent);
    // print_rdie($aagent);

    $afit = (float) $aagent['fitness'];
    $adel = (float) abs($afit - (float) PHP_INT_MAX);
    if (is_zero($adel)) {
      $fails++;
      if ($fails < $num) $num--;
    }

    $agent_arr[] = $aagent;
  }



  // use all mats
  for ($i = 0; $i < $num; $i++) {

    $aagent = ga_init_agent(array(), 1.0);
    ga_compute_agent_part_percent_price($aagent);
    ga_fix_agent_mat_part($aagent);

    ga_compute_agent_nutrition_percent($aagent);
    ga_compute_agent_fitness($aagent);
    // print_rdie($aagent);

    $afit = (float) $aagent['fitness'];
    $adel = (float) abs($afit - (float) PHP_INT_MAX);
    if (is_zero($adel)) {
      $fails++;
      if ($fails < $num) $num--;
    }

    $agent_arr[] = $aagent;
  }

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
  $ag1 = array_pop($apop);
  $ag2 = array_pop($apop);
  $aga = $ag1['fitness'] < $ag2['fitness'] ? $ag1 : $ag2;
  $bpop[] = $aga;

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

  $ag1 = ga_init_agent();
  $ag2 = ga_init_agent();
  $ag3 = ga_init_agent();
  $ag4 = ga_init_agent();
  // print_rdie($ag1);

  $matr_arr_idx = array_keys($master_matr_arr);
  // shuffle($matr_arr_idx);
  // print_rdie($matr_arr_idx);

  // compute part|bagian
  $amust_min = 0.9;
  foreach ($matr_arr_idx as $mat_name) {
    // print_rdie($aga['mats'][$mat_name]);

    $ag1['mats'][$mat_name]['part'] = round(($aga['mats'][$mat_name]['part'] + $agb['mats'][$mat_name]['part']) / 2, 1);

    $ag2['mats'][$mat_name]['part'] = round($aga['mats'][$mat_name]['part'] + $agb['mats'][$mat_name]['part'], 1);
    $ag3['mats'][$mat_name]['part'] = round(max($aga['mats'][$mat_name]['part'], $agb['mats'][$mat_name]['part']), 1);
    $ag4['mats'][$mat_name]['part'] = round(min($aga['mats'][$mat_name]['part'], $agb['mats'][$mat_name]['part']), 1);

    // forcing crum always exists
    if ($mat_name == 'crum591' && is_ayam_grower_finisher()) {
      $ag1['mats'][$mat_name]['part'] = max($ag1['mats'][$mat_name]['part'], $amust_min);
      $ag2['mats'][$mat_name]['part'] = max($ag2['mats'][$mat_name]['part'], $amust_min);
      $ag3['mats'][$mat_name]['part'] = max($ag3['mats'][$mat_name]['part'], $amust_min);
      $ag4['mats'][$mat_name]['part'] = max($ag4['mats'][$mat_name]['part'], $amust_min);
    } elseif ($mat_name == 'crum591' && is_ayam_starter()) {
      $ag1['mats'][$mat_name]['part'] = max($ag1['mats'][$mat_name]['part'], $amust_min);
      $ag2['mats'][$mat_name]['part'] = max($ag2['mats'][$mat_name]['part'], $amust_min);
      $ag3['mats'][$mat_name]['part'] = max($ag3['mats'][$mat_name]['part'], $amust_min);
      $ag4['mats'][$mat_name]['part'] = max($ag4['mats'][$mat_name]['part'], $amust_min);
    }
  }
  // print_rdie($ag4);

  // compute non_zero
  $non_zero1 = 0;
  $non_zero2 = 0;
  $non_zero3 = 0;
  $non_zero4 = 0;
  foreach ($matr_arr_idx as $mat_name) {
    if ($ag1['mats'][$mat_name]['part'] > 0) $non_zero1++;
    if ($ag2['mats'][$mat_name]['part'] > 0) $non_zero2++;
    if ($ag3['mats'][$mat_name]['part'] > 0) $non_zero3++;
    if ($ag4['mats'][$mat_name]['part'] > 0) $non_zero4++;
  }


  $ag1['non_zero'] = $non_zero1;
  $ag2['non_zero'] = $non_zero2;
  $ag3['non_zero'] = $non_zero3;
  $ag4['non_zero'] = $non_zero4;

  $ag1['ops'] = 'crossover1';
  $ag2['ops'] = 'crossover2';
  $ag3['ops'] = 'crossover3';
  $ag4['ops'] = 'crossover4';

  $ags = array();
  if ($non_zero1 > 0) $ags[] = $ag1;
  if ($non_zero2 > 0) $ags[] = $ag2;
  if ($non_zero3 > 0) $ags[] = $ag3;
  if ($non_zero4 > 0) $ags[] = $ag4;

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
  // shuffle($matr_arr_idx);
  $the_pop = array();
  $amust_min = 0.9;

  for ($i = 0; $i < $num; $i++) {
    $aga = $apop[$i];
    $ag1 = ga_init_agent($aga);
    $ag2 = ga_init_agent($aga);
    $ag3 = ga_init_agent($aga);
    $ag4 = $aga;
    $ag5 = $aga;

    foreach ($matr_arr_idx as $mat_name) {
      $ag1['mats'][$mat_name]['part'] = round($aga['mats'][$mat_name]['part'] + random_int(0, 2), 1);
      $ag2['mats'][$mat_name]['part'] = round($aga['mats'][$mat_name]['part'] / random_int(2, 5), 1);
      if (is_zero($aga['mats'][$mat_name]['part'])) $ag3['mats'][$mat_name]['part'] = random_int(0, 2);
      // else $ag3['mats'][$mat_name]['part'] = 0;

      // forcing crum always exists
      if ($mat_name == 'crum592' && is_ayam_grower_finisher()) {
        $ag1['mats'][$mat_name]['part'] = max($ag1['mats'][$mat_name]['part'], $amust_min);
        $ag2['mats'][$mat_name]['part'] = max($ag2['mats'][$mat_name]['part'], $amust_min);
        $ag3['mats'][$mat_name]['part'] = max($ag3['mats'][$mat_name]['part'], $amust_min);
      } elseif ($mat_name == 'crum591' && is_ayam_starter()) {
        $ag1['mats'][$mat_name]['part'] = max($ag1['mats'][$mat_name]['part'], $amust_min);
        $ag2['mats'][$mat_name]['part'] = max($ag2['mats'][$mat_name]['part'], $amust_min);
        $ag3['mats'][$mat_name]['part'] = max($ag3['mats'][$mat_name]['part'], $amust_min);
      }
    }

    // compute non_zero
    $non_zero1 = 0;
    $non_zero2 = 0;
    $non_zero3 = 0;
    $non_zero4 = 0;
    $non_zero5 = 0;

    // forcing tikan
    $mat_name = 'tikan';
    if (isset($ag4['mats'][$mat_name])) {
      $ag4['mats'][$mat_name]['part'] = random_float(0.5, 2.1);
    }

    // forcing bkedeleai
    $mat_name = 'bkedeleai';
    if (isset($ag4['mats'][$mat_name])) {
      $ag4['mats'][$mat_name]['part'] = random_float(0.5, 2.1);
    }

    foreach ($matr_arr_idx as $mat_name) {
      if ($ag1['mats'][$mat_name]['part'] > 0) $non_zero1++;
      if ($ag2['mats'][$mat_name]['part'] > 0) $non_zero2++;
      if ($ag3['mats'][$mat_name]['part'] > 0) $non_zero3++;
      if ($ag4['mats'][$mat_name]['part'] > 0) $non_zero4++;
      if ($ag5['mats'][$mat_name]['part'] > 0) $non_zero5++;
    }

    $ag1['non_zero'] = $non_zero1;
    $ag2['non_zero'] = $non_zero2;
    $ag3['non_zero'] = $non_zero3;
    $ag4['non_zero'] = $non_zero4;
    $ag4['non_zero'] = $non_zero5;

    $ag1['ops'] = "mutation: $mut_type";
    $ag2['ops'] = "mutation: $mut_type";
    $ag3['ops'] = "mutation: $mut_type";
    $ag4['ops'] = "mutation: $mut_type";
    $ag5['ops'] = "mutation: $mut_type";
    // print_rdie($ag1);
    // print_rdie($ag2);

    // merge
    if ($non_zero1 > 0) $the_pop[] = $ag1;
    if ($non_zero2 > 0) $the_pop[] = $ag2;
    if ($non_zero3 > 0) $the_pop[] = $ag3;
    if ($non_zero4 > 0) $the_pop[] = $ag4;
    if ($non_zero5 > 0) $the_pop[] = $ag5;
  }

  return $the_pop;
}


/**
 * recompute
 */
function ga_compute_agent_fixing(array &$aagent) {}

function ga_compute_population(array &$apop)
{
  foreach ($apop as $idx => $aagent) {
    ga_compute_agent_fixing($aagent);
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
    $is_good = $dist_mat > 0.1;
  }

  // check nutrition distance
  if ($is_good) {
    $dist_nutri = ga_dist_nutri($aga, $agb);
    $is_good = $dist_nutri > 0.1;
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

  if ($is_good) {
    // check fitness
    $del_fitness = (float) abs($aga['fitness'] - $agb['fitness']);
    $is_good = $del_fitness > 0.1;
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
function ga_merge_main_population(array &$main_pop, array &$res_pop): int
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
      ga_is_good_agent_single($aagent) &&
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

  echo "\t new=$good_num";

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
  $final = sprintf("\n%s \tProtein=%-.1f%% \tKarbo=%-.1f%% \tLemak=%-.1f%% \tSerat=%-.1f%% \tPrice=%s %s %s \n%s \n", $pre, round($aagent['protein'], 1), round($aagent['karbo'], 1), round($aagent['lemak'], 1), round($aagent['serat'], 1), $price_kg, $the_err, $aagent['str_mul'], $the_mats);
  $final = "\n\n--- AYAM TARGET: $ayam_target $final";

  echo $final;
}


function ga_print_pop(array $apop, int $num = 3)
{
  for ($i = $num - 1; $i >= 0; $i--) {
    ga_print_agent($apop[$i], strval($i + 1) . ".");
  }
}
