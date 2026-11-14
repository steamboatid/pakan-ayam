<?php

global $master_supl_arr, $def_mix_cap, $suplemen_price_per_kg;

// topmix (medion)	150
// prozyme (medion)	50
// belazyme / natuzyme	50
// herbal growth promoter	100
//
// bawang putih bubuk	50
// arang bubuk	50
// tepung rajungan+kerang	900
// tepung kangkung	150


$ori_master_supl_arr = array(
  'topmix_medion' => array(
    25000, //harga, per kemasan
    500, //gram, kemasan
    150, //porsi, per 100kg pakan
  ),
  'prozyme_medion' => array(
    70000, //harga, per kemasan
    1000, //gram, kemasan
    50, //porsi, per 100kg pakan
  ),
  'belazyme_natuzyme' => array(
    80000, //harga, per kemasan
    1000, //gram, kemasan
    50, //porsi, per 100kg pakan
  ),
  'herbal_growth_promoter' => array(
    25000, //harga, per kemasan
    1000, //gram, kemasan
    100, //porsi, per 100kg pakan
  ),
  'bawang_putih' => array(
    80000, //harga, per kemasan
    1000, //gram, kemasan
    50, //porsi, per 100kg pakan
  ),
  'arang_bubuk' => array(
    35000, //harga, per kemasan
    1000, //gram, kemasan
    50, //porsi, per 100kg pakan
  ),

  'tepung_rajungan_kerang' => array(
    4500, //harga, per kemasan
    1000, //gram, kemasan
    900, //porsi, per 100kg pakan
  ),
  'tepung_kangkung' => array(
    3000, //harga, per kemasan
    1000, //gram, kemasan
    150, //porsi, per 100kg pakan
  ),
);


// print_rdie($master_supl_arr);

$master_supl_arr = array();
$sum_price_per_mix = 0;

foreach ($ori_master_supl_arr as $nama_bahan => $aa) {
  $a['harga'] = (float)$aa[0];
  $a['gram'] = (float) $aa[1];
  $a['porsi'] = (float) $aa[2];

  $price_per_gram = round($a['harga'] / $a['gram'], 1);
  $price_per_mix = round($price_per_gram * $a['porsi'], 1);
  $a['price_per_mix'] = (float) $price_per_mix;

  $master_supl_arr[$nama_bahan] = $a;
  $sum_price_per_mix += $a['price_per_mix'];
}


$suplemen_price_per_kg = ceil($sum_price_per_mix / $def_mix_cap);
print_rdie(
  array(
    'suplemen_price_per_kg' => $suplemen_price_per_kg,
    'sum_price_per_mix' => $sum_price_per_mix,
    'def_mix_cap' => $def_mix_cap
  )
);
