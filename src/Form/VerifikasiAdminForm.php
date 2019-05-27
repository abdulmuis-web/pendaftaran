<?php

namespace Drupal\pendaftaran\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\pendaftaran\Entity\Pendaftaran;
use Drupal\Core\Url;

/**
 * Class VerifikasiAdminForm.
 */
class VerifikasiAdminForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'verifikasi_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $user = User::load(\Drupal::currentUser()->id());
	$id = \Drupal::routeMatch()->getParameter('pendaftaran');
	$pendaftaran = Pendaftaran::load($id);
    $is_admin_sekolah = $user->hasRole('admin_sekolah');
	
	if(($is_admin_sekolah) && ($user->field_sekolah->target_id === $pendaftaran->pilihan_sekolah->target_id)){
      $form['tgl_verifikasi'] = [
        '#type' => 'datetime',
        '#title' => $this->t('Tanggal'),
        '#description' => $this->t('Tanggal verifikasi'),
	    '#default_value' => REQUEST_TIME,
        '#weight' => '0',
	    '#required' => TRUE,
      ];
      $form['lulus_verifikasi'] = [
        '#type' => 'radios',
        '#title' => $this->t('Lulus verifikasi'),
        '#description' => $this->t('Pernyataan lulus atau tidaknya verifikasi admin'),
        '#options' => ['0' => $this->t('Tidak lulus'), '1' => $this->t('Lulus')],
        '#default_value' => 0,
        '#weight' => '0',
	    '#required' => TRUE,
      ];
      $form['keterangan'] = [
        '#type' => 'textfield',
        '#size' => '191',
        '#title' => $this->t('Keterangan'),
        '#description' => $this->t('Keterangan terkait verifikasi'),
        '#weight' => '0',
	    '#required' => TRUE,
      ];
      $form['uid'] = [
        '#type' => 'value',
        '#weight' => '0',
        '#default_value' => $user->id(),
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
	}
	elseif($user->id()){
	  if($user && !$is_admin_sekolah){
	    drupal_set_message(t('Anda tidak bertugas sebagai admin sekolah, tidak memiliki akses untuk halaman ini'));
		//return FALSE;
	  }
	  elseif($user && $is_admin_sekolah){
        drupal_set_message(t('Anda bertugas di @sekolah, tidak dapat mengakses halaman verifikasi untuk siswa @name ini', array('@sekolah' => $user->field_sekolah->entity->label(), '@name'=> $pendaftaran->nama_lengkap->value)));
	    //return FALSE;
	  }
	  $form['action']['submit'] = [
		'#type' => 'submit',
		//'#submit' => array($this->backForm),
		'#submit' => array('::backForm'),
		'#value' => $this->t('Kembali'),
	  ];
	}
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
	$id = \Drupal::routeMatch()->getParameter('pendaftaran');
	$pendaftaran = Pendaftaran::load($id);
	$tgl_verifikasi = $form_state->getValue('tgl_verifikasi')->format('Y-m-d\Th:i:s');
	
	$pendaftaran->set('tgl_verifikasi', $tgl_verifikasi);
	$pendaftaran->set('admin_sekolah', $form_state->getValue('uid'));
	$pendaftaran->set('keterangan', $form_state->getValue('keterangan'));

    $pendaftaran->setNewRevision(TRUE); // enabling revision for the entity save.
    //$pendaftaran->setRevisionUserId(\Drupal::currentUser()->id()); // enabling revision for the entity save.
    $pendaftaran->setRevisionCreationTime(REQUEST_TIME);
    $pendaftaran->set('revision_user', \Drupal::currentUser()->id());

    $pendaftaran->setRevisionLogMessage($pendaftaran->get('keterangan')->value);
		  
	$this->createPendaftaran($pendaftaran);
	
	//dpm($pendaftaran->getValues('tgl_verifikasi'));  
	// Call the Static Service Container wrapper
    // We should inject the messenger service, but its beyond the scope of this example.
    $messenger = \Drupal::messenger();
    $messenger->addMessage('Title: '. $tgl_verifikasi);

  }

  /**
   * {@inheritdoc}
   */
  public function backForm(array &$form, FormStateInterface $form_state) {
	  $id = \Drupal::routeMatch()->getParameter('prodi_sekolah');
	  $url = Url::fromRoute('entity.prodi_sekolah.canonical', ['prodi_sekolah' => $id]);
      $form_state->setRedirectUrl($url);
  }
  /**
   * createBook.
   *
   * @return object
   *   Return pendaftaran_catalogue object.
   */
  public function createPendaftaran($pendaftaran){
        $database = \Drupal::database();
        $transaction = $database->startTransaction();
        try {
			$pendaftaran->save();
        }
        catch (\Exception $e) {
          $transaction->rollback();
          $pendaftaran = NULL;
          watchdog_exception('pendaftaran', $e, $e->getMessage());
          throw new \Exception(  $e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    return $pendaftaran;
  }
  
}
