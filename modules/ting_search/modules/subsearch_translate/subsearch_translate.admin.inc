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
    drupal_set_message(t('Translated search requires !facet. Please insert on !adminpage', array('!facet' => 'facet.genreCategory', '!adminpage' => l('admin/config/ting/facets','admin/config/ting/facets'))), 'warning');
  }

  $form['additional_search']['#type'] = 'fieldset';
  $form['additional_search']['#title'] = t('Additional Search');
  $form['additional_search']['#tree'] = FALSE;

  $form['additional_search']['translated_suggestion_google_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Translated Search: Google Apikey'),
    '#default_value' => variable_get('translated_suggestion_google_key', '')
  );
}