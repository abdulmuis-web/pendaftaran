<?php

/**
 * @file
 * Contains Drupal\pendaftaran\PendaftaranService.
 */

namespace Drupal\pendaftaran;

class PendaftaranService {
  
  protected $pendaftaran_value;
  
  public function __construct() {
	$user = \Drupal::currentUser();
	
    $this->pendaftaran_value = $user->getUsername();
  }
  
  public function getPendaftaranValue() {
    return $this->pendaftaran_value;
  }
  
}
