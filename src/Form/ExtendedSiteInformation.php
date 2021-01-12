<?php

namespace Drupal\axl_test\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/**
 * Implments new field to site information.
 */
class ExtendedSiteInformation extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $siteConfig = $this->config('system.site');
    $form = parent::buildForm($form, $form_state);
    // Update label of save conig button.
    $form['actions']['submit']['#value'] = $this->t('Update configuration');
    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site API Key'),
      '#default_value' => $siteConfig->get('siteapikey') ?: 'No API Key yet',
      '#description' => t("Field to set the API Key"),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('system.site')
      ->set('siteapikey', $form_state->getValue('siteapikey'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
