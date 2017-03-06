<?php
/**
 * @file
 * Configuration of module.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function subsearch_opensuggestion_form_ting_admin_ting_settings_alter(&$form, &$form_state) {
  $form['additional_search']['#type'] = 'fieldset';
  $form['additional_search']['#title'] = t('Additional Search');
  $form['additional_search']['#tree'] = FALSE;

  if(isset($form['additional_search']['do_you_mean_guess']['#options'])) {
    $options = $form['additional_search']['do_you_mean_guess']['#options'];
  }
  $form['additional_search']['do_you_mean_guess'] = array(
    '#type' => 'radios',
    '#title' => t('Guess engine'),
    '#default_value' => variable_get('do_you_mean_guess', FALSE),
    '#options' => array(),
    '#description' => t('Choose the guess engine'),
  );

  $options['os'] = t('OpenSuggestion');
  $form['additional_search']['do_you_mean_guess']['#options'] = $options;
}