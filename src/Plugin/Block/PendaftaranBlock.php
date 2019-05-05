<?php

namespace Drupal\pendaftaran\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Pendaftaran' block.
 *
 * @Block(
 *   id = "pendaftaran_block",
 *   admin_label = @Translation("Pendaftaran block"),
 * )
 */

class PendaftaranBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $config = $this->getConfiguration();

    $name = $this->t('to no one');
    if (isset($config['pendaftaran_block_settings']) && !empty($config['pendaftaran_block_settings'])) {
      $name = $config['pendaftaran_block_settings'];
    }
    
    return array(
      '#markup' => $this->t('Hello @name!', array('@name' => $name)),
    );  
  }
  
  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account, $return_as_object = FALSE) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }
  
  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    
    $form = parent::blockForm($form, $form_state);
    
    $config = $this->getConfiguration();

    $form['pendaftaran_block_settings'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Who'),
      '#description' => $this->t('Who do you want to say hello to?'),
      '#default_value' => isset($config['pendaftaran_block_settings']) ? $config['pendaftaran_block_settings'] : '',
    );
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('pendaftaran_block_settings', $form_state->getValue('pendaftaran_block_settings'));
  } 
  
}
