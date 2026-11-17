<?php

declare(strict_types=1);

global $prng;

class rng_service
{
  private Random\Randomizer $randomizer;

  // Seed: int, 16-byte string, or null (auto-seeded from OS CSPRNG)
  public function __construct(int|string|null $seed = null)
  {
    $engine = new Random\Engine\PcgOneseq128XslRr64($seed);
    $this->randomizer = new Random\Randomizer($engine);
  }

  // Inclusive range [min, max]
  public function get_int(int $min, int $max): int
  {
    return $this->randomizer->getInt($min, $max);
  }

  // Default range [min, max)
  public function get_float(float $min, float $max): float
  {
    return $this->randomizer->getFloat($min, $max);
  }

  // Closed range [min, max]
  public function get_float_closed(float $min, float $max): float
  {
    return $this->randomizer->getFloat($min, $max, Random\IntervalBoundary::ClosedClosed);
  }


  // Returns a new shuffled array (original untouched, keys reindexed)
  public function shuffle_array(array &$items)
  {
    $items = $this->randomizer->shuffleArray($items);
  }

  // Unicode-safe string shuffle (per character, not per byte)
  public function shuffle_string(string &$text)
  {
    $chars = mb_str_split($text, 1, 'UTF-8');
    $text = implode('', $this->randomizer->shuffleArray($chars));
  }

  // Pick $count unique random keys (replacement for array_rand())
  public function pick_keys(array $items, int $count): array
  {
    return $this->randomizer->pickArrayKeys($items, $count);
  }
}

// Usage
$prng = new rng_service(119922);


/*
$int_value = $prng->get_int(1, 100);
$float_value = $prng->get_float(0.0, 1.0);

// Same seed gives the same sequence
$prng_a = new rng_service(42);
$prng_b = new rng_service(42);
var_dump($prng_a->get_int(1, 1000) === $prng_b->get_int(1, 1000)); // bool(true)
//*/