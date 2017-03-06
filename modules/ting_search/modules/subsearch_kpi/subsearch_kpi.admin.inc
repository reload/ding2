<?php
/**
 * @file
 * Configuration of module.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function subsearch_kpi_form_ting_admin_ting_settings_alter(&$form, &$form_state) {
  $form['additional_search']['#type'] = 'fieldset';
  $form['additional_search']['#title'] = t('Ting Subsearch');
  $form['additional_search']['#description'] = t('Feature modules allow additional searches to be made based on predefined conditions.');
  $form['additional_search']['#tree'] = FALSE;

  $form['additional_search']['do_you_mean_case_4_minimum'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum results for case 4'),
    '#default_value' => variable_get('do_you_mean_case_4_minimum', 5),
    '#description' => t('In order to trigger case 4 we need a minimum of results.'),
  );

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

  $options['kpi'] = t('KPI Index');
  $form['additional_search']['do_you_mean_guess']['#options'] = $options;


  $form['additional_search']['do_you_mean_kpi'] = array(
    '#type' => 'fieldset',
    '#title' => t('KPI settings'),
    '#states' => array('visible' => array(':input[name="do_you_mean_guess"]' => array('value' => 'kpi'))),
  );

  $default_value = 'https://ws.webtrends.com/v3/Reporting/profiles/77577/reports/sprt2Xll7b7/';
  $form['additional_search']['do_you_mean_kpi']['subsearch_kpi_webservice_endpoint'] = array(
    '#type' => 'textfield',
    '#title' => t('Webservice Endpoint'),
    '#default_value' => variable_get('subsearch_kpi_webservice_endpoint', $default_value),
    '#description' => t('Text to be determined'),
  );
  $params = array(
    'start_period' => 'current_day-365',
    'end_period' => 'current_day-1',
    'language' => 'en-US',
    'format' => 'json',
  );
  foreach ($params as $key => $value) {
    $form['additional_search']['do_you_mean_kpi']['subsearch_kpi_' . $key] = array(
      '#type' => 'textfield',
      '#title' => t('Parameter: !key', array('!key' => $key)),
      '#default_value' => variable_get('subsearch_kpi_' . $key, $value),
      '#description' => t('Text to be determined'),
    );
  }
  $form['additional_search']['do_you_mean_kpi']['subsearch_kpi_account'] = array(
    '#type' => 'textfield',
    '#title' => t('Account'),
    '#default_value' => variable_get('subsearch_kpi_account', ''),
    '#description' => t('Account\Username.'),
  );
  $form['additional_search']['do_you_mean_kpi']['subsearch_kpi_password'] = array(
    '#type' => 'textfield',
    '#title' => t('Password'),
    '#default_value' => variable_get('subsearch_kpi_password', ''),

    '#description' => t('The password'),
  );

  $form['additional_search']['do_you_mean_kpi']['do_you_mean_kpi_distance'] = array(
    '#type' => 'textfield',
    '#title' => t('Levenshtein distance'),
    '#default_value' => variable_get('do_you_mean_kpi_distance', 3),

    '#description' => t('The max allowed distance between words for it to be similar. Higher distance, less similarity.'),
  );
  $form['additional_search']['do_you_mean_kpi']['do_you_mean_minimum_searches'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum of searches for keys'),
    '#default_value' => variable_get('do_you_mean_minimum_searches', 1),
    '#description' => t('The KPI Index has a lot of keys with only 1 search. But how few is minimum?'),
  );
}