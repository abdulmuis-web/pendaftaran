<?php

/**
 * @file
 * Contains Drupal\pendaftaran\PendaftaranService.
 */

namespace Drupal\pendaftaran;

class PendaftaranService {
  
  protected $pendaftaran_value;
  
  public function __construct() {
    $this->pendaftaran_value = 'Upchuk';
  }
  
  public function getPendaftaranValue() {
    return $this->pendaftaran_value;
  }
  
}
