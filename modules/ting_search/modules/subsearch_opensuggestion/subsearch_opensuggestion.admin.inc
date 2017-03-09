<?php
/**
 * @file
 * Configuration of module.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function subsearch_opensuggestion_form_ting_admin_ting_settings_alter(&$form, &$form_state) {
  $form['subsearch_settings']['#type'] = 'fieldset';
  $form['subsearch_settings']['#title'] = t('Ting subsearch settings');
  $form['subsearch_settings']['#tree'] = FALSE;

  $form['subsearch_settings']['subsearch_os_trigger_limit'] = array(
    '#type' => 'textfield',
    '#title' => t('Number of hits to trigger Open Suggest subsearch'),
    '#default_value' => variable_get('subsearch_os_trigger_limit', 5),
    '#description' => t('In order to trigger a subsearch which will provide a suggestion to the user, the user-provided search string must trigger at most this number of hits. NOTE: User-provided searches that trigger 0 hits always trigger a subsearch.'),
  );


  if(isset($form['subsearch_settings']['subsearch_service']['#options'])) {
    $options = $form['subsearch_settings']['subsearch_service']['#options'];
  }
  $form['subsearch_settings']['subsearch_service'] = array(
    '#type' => 'radios',
    '#title' => t('Service'),
    '#default_value' => variable_get('subsearch_service', FALSE),
    '#options' => array(),
    '#description' => t('Choose the subsearch service'),
  );

  $options['os'] = t('Open Suggestion Service');
  $form['subsearch_settings']['subsearch_service']['#options'] = $options;
}
