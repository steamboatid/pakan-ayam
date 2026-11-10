<?php

function print_rdie(mixed $a)
{
  print_r($a);
  $aa = debug_backtrace();
  if (isset($aa[0])) {
    // print_r($aa[0]);
    $afile = $aa[0]['file'];
    $aline = $aa[0]['line'];
    echo "\n\n CALLER: $afile --- $aline \n\n";
  }
  die();
}

function is_zero(int|float $a, int $precision = 5): bool
{
  $aa = abs($a * pow(10, $precision));
  return $aa < 1;
}

function random_float(float $min, float $max): float
{
  $amin = min($min, $max);
  $amax = max($min, $max);
  return (float) (new Random\Randomizer())->getFloat($amin, $amax);
}

function is_ayam_starter(): bool
{
  global $ayam_target;
  return $ayam_target == 'starter';
}

// function is_ayam_grower(): bool
// {
//   global $ayam_target;
//   return $ayam_target == 'grower';
// }

function is_ayam_finisher(): bool
{
  global $ayam_target;
  return $ayam_target == 'finisher';
}

function is_ayam_grower_finisher(): bool
{
  global $ayam_target;
  return $ayam_target == 'finisher' || $ayam_target == 'grower';
}

// function is_ayam_sow(): bool
// {
//   global $ayam_target;
//   return $ayam_target == 'sow' || $ayam_target == 'bunting' || $ayam_target == 'nyusui';
// }

// crum591, crum592
function is_agent_mat_part_exists(array $aagent, string $amat_name = 'crum591'): bool
{
  if (!isset($aagent['mats'])) return false;
  if (!isset($aagent['mats'][$amat_name])) return false;
  return $aagent['mats'][$amat_name]['part'] > 0;
}


function calculate_rms(array $values): float
{
  $count = count($values);

  if ($count === 0) {
    throw new InvalidArgumentException('Array must not be empty.');
  }

  $sum = 0.0;

  // Accumulate squares directly, no intermediate array
  foreach ($values as $value) {
    $sum += $value * $value;
  }

  return sqrt($sum / $count);
}


function round_near(float $number, float $near = 1000): float {
  return round($number / $near) * $near;
}