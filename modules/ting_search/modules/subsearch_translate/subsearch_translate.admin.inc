<?php
/**
 * @file
 * Configuration of module.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function subsearch_translate_form_ting_admin_ting_settings_alter(&$form, &$form_state) {

  $facets = array();
  foreach (variable_get('ding_facetbrowser_facets', array()) as $facet) {
    $facets[] = $facet['name'];
  }

  if (!in_array('facet.genreCategory', $facets)) {
    drupal_set_message(
      t(
        'Translated search requires !facet. Please insert on !adminpage',
        array(
          '!facet' => 'facet.genreCategory',
          '!adminpage' => l(
            t('Configure facets'),
            'admin/config/ting/facets'
          ),
        )
      ),
      'warning'
    );
  }

  $form['subsearch_settings']['subsearch_translation']['#type'] = 'fieldset';
  $form['subsearch_settings']['subsearch_translation']['#title'] = t('Subsearch Translation');
  $form['subsearch_settings']['subsearch_translation']['#tree'] = FALSE;

  $form['subsearch_settings']['subsearch_translation']['translated_suggestion_google_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Cloud Translation API key'),
    '#default_value' => variable_get('translated_suggestion_google_key', ''),
  );

  $form['subsearch_settings']['subsearch_translation']['google_translate_factor'] = array(
    '#type' => 'textfield',
    '#title' => t('Factor for calculating ration between nonfiction and fiction'),
    '#default_value' => variable_get('google_translate_factor', 2),
    '#description' => t('The condition is as following (nonfiction / factor) > fiction'),
  );

}
