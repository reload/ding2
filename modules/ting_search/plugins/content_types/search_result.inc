<?php

/**
 * @file
 * Panels render to display search results.
 */

$plugin = array(
  'single' => TRUE,
  'title' => t('Ting search - Search results'),
  'description' => t('The results of a search using keywords.'),
  'required context' => new ctools_context_required(t('Keywords'), 'string'),
  'category' => t('Widgets'),
  'content_types' => array('search_result'),
  'defaults' => array(
    'type' => 'node',
    'log' => TRUE,
    'override_empty' => FALSE,
    'empty_title' => '',
    'empty' => '',
    'empty_format' => filter_fallback_format(),
    'override_no_key' => FALSE,
    'no_key_title' => '',
    'no_key' => '',
    'no_key_format' => filter_fallback_format(),
  ),
  'render first' => TRUE,
  'render last' => FALSE,
);

/**
 * Render the custom content type.
 */
function ting_search_search_result_content_type_render($subtype, $conf, $panel_args, $context) {
  $search_info = search_get_info();
  if (empty($search_info[$conf['type']])) {
    return;
  }
  $info = $search_info[$conf['type']];

  $keys = NULL;
  if (!empty($context) && isset($context->data)) {
    $keys = $context->data;
  }

  $conditions = NULL;
  if (isset($info['conditions_callback']) && function_exists($info['conditions_callback'])) {
    // Build an optional array of more search conditions.
    $conditions = $info['conditions_callback']($keys);
  }

  // Display nothing at all if no keywords were entered.
  if (empty($keys) && empty($conditions)) {
    if (!empty($conf['override_no_key'])) {
      $block = new stdClass();
      $block->title = $conf['no_key_title'];
      $block->content = check_markup($conf['no_key'], $conf['no_key_format'], FALSE);
      return $block;
    }
    return;
  }

  // Check if the search should be built with another view mode.
  $params = drupal_get_query_parameters();
  if (!empty($params['view'])) {
    $allowed_modes = array('result_list');
    if (in_array($params['view'], $allowed_modes)) {
      $conditions['view_mode'] = $params['view'];
    }
  }

  // Build the content type block.
  $block = new stdClass();
  $block->module = 'search';
  $block->delta = 'result';

  $classes = array(
    'search-results',
  );
  // Set message.
  $message = '';
  $results = '';
  $prefix = '';

  // Only search if there are keywords or non-empty conditions.
  if ($keys || !empty($conditions)) {
    // Collect the search results.
    $results = search_data($keys, $info['module'], $conditions);
    $search_result = drupal_static('ting_search_results');
    $prefix = implode(module_invoke_all('ting_search_results', $keys, $conditions, $search_result));
  }

  if (!empty($conf['log'])) {
    // Log the search keys:
    watchdog('search', 'Searched %type for %keys.', array('%keys' => $keys, '%type' => $info['title']), WATCHDOG_NOTICE, l(t('results'), $_GET['q']));
  }

  if (!empty($results['#results'])) {
    // Get message limit and raw data well search results.
    $limit = variable_get('ting_search_result_message_limit', 100);

    $view_toggle_button = '';

    // When a zero-hit search is executed and we're able to match "correct"
    // keys, a new search is done with the "correct" keys.
    // Then use the query-parameter original-keys to display what was done to
    // the visitor.
    $parameters = drupal_get_query_parameters();
    if (isset($parameters['original-search'])) {
      $msg = t('The search for "!original-keys" returned 0 hits. Did you mean "!keys?" which gave !num-results. The search was executed.', array(
        '!original-keys' => $parameters['original-search'],
        '!keys' => $keys,
        '!num-results' => $search_result->numTotalObjects,
      ));
      $message .= '<div class="messages warning">' . $msg . '</div>';
    }

    // If the search had five or less results and we can match "correct" keys
    // We will display that there maybe is better keys to find what the visitor
    // is looking for.
    if ($search_result->numTotalObjects <= 5) {
      $suggestion = search_zerohit_suggest($keys);
      if ($suggestion) {
        // Do aditional search.
        $suggested_result = search_do_secondary_search($suggestion);
        if ($suggested_result->numTotalObjects >= 10) {
          $msg = t('The search for "!keys" returned !num-results hits. Did you mean "<a href="/search/ting/!suggested-keys">!suggested-keys</a>?" which will return !suggested-num-results hits.', array(
            '!suggested-keys' => strtolower($suggestion),
            '!suggested-num-results' => $suggested_result->numTotalObjects,
            '!keys' => $keys,
            '!num-results' => $search_result->numTotalObjects,
          ));
          $message .= '<div class="messages warning">' . $msg . '</div>';
        }
      }
    }

    $frequency_rank = array(
      'rank_main_title' => t('Title'),
      'rank_title' => t('Title'),
      'rank_creator' => t('Creator'),
      'rank_subject' => t('Subject')
    );

    if (isset($search_result) && empty($conditions['sort']) && in_array($search_result->sortUsed, array_keys($frequency_rank))) {
      $msg = t('Records are sorted by %sortUsed . Select another sort if it is not suited', array('%sortUsed' => $frequency_rank[$search_result->sortUsed]));
      $message .= '<div class="messages status">' . $msg . '</div>';
    }

    if (isset($search_result) && $limit < $search_result->numTotalObjects) {
      $msg = t('Your search gave more than %s results. Try to search more specific or use the facets to filter the result.');
      $msg = sprintf($msg, $limit);
      $message .= '<div class="messages search-field-in-content--message">' . $msg . '</div>';
    }

    // Display result list view mode toggle button if enabled.
    if (variable_get('ting_search_result_view_mode_toggle', FALSE)) {

      if (empty($conditions['view_mode'])) {
        $params += array('view' => 'result_list');
        $list = l('', $_GET['q'], array(
          'attributes' => array(
            'class' => array('icon-list'),
            'title' => t('Click to switch to a more compact results list.'),
          ),
          'html' => 0,
          'query' => $params,
        ));
        $teaser = '<i class="icon-th-list"></i>';
      }
      else {
        $classes[] = 'search-results-list-results';
        $list = '<i class="icon-list"></i>';
        unset($params['view']);
        $teaser = l('', $_GET['q'], array(
          'attributes' => array(
            'class' => array('icon-th-list'),
            'title' => t('Click to switch to the default results list.'),
          ),
          'html' => 0,
          'query' => $params,
        ));
      }
      // Put toggle first in prefix.
      $prefix = theme('item_list', array('attributes' => array('class' => array('search-results-toggle-buttons')),'items' => array($list, $teaser))) . $prefix;
    }

    $prefix .= $message;

    $block->title = t('Search results');
    $block->content = $prefix . theme('ting_search_results', array(
      'results' => $results['#results'],
      'module' => $conf['type'],
      // Property should be attributes but it's intercepted later on.
      'attribs' => array(
        'class' => $classes,
      ),
    ));
  }
  else {
    // The search result returned zero-hits so we need to see if "correct" keys
    // can yield a better result.
    $search_result = drupal_static('ting_search_results');
    // Find suggestions for "correct" search phrase.
    $suggestion = search_zerohit_suggest($keys);

    if (!empty($suggestion)) {
      // Do secondary search for "correct" keys.
      $secondary_result = search_do_secondary_search($suggestion);
      // Only redirect to "correct" keys search if it has a positive result.
      if ($secondary_result->numTotalObjects > 0) {
        $params['query'] = array(
          'original-search' => $search_result->search_key
        );
        drupal_goto('search/ting/' . strtolower($suggestion), $params);
      }
    }
    if (empty($conf['override_empty'])) {
      $block->title = t('Your search yielded no results');
      $block->content = search_help('search#noresults', drupal_help_arg());
    }
    else {
      $block->title = $conf['empty_title'];
      $block->content = check_markup($conf['empty'], $conf['empty_format'], FALSE);
    }
  }

  return $block;
}

/**
 * Do a secondary search juggling around the drupal static.
 *
 * @param string $keys
 *   The search phrase.
 *
 * @return Object TingClientSearchResult
 *   The search result of the secondary search.
 */
function search_do_secondary_search($keys) {

  // Store search result temporarily. We need to make seconday search.
  $temporary_search_result = drupal_static('ting_search_results');

  // Reset the static containing search result to be able do new search.
  drupal_static_reset('ting_search_search_execute');

  // Search for the secondary keys.
  ting_search_search_execute($keys, array('sort' => ''));
  $secondary_result = drupal_static('ting_search_results');

  // Overwrite the static with the original search.
  $revert = &drupal_static('ting_search_results');
  $revert = $temporary_search_result;

  return $secondary_result;
}

/**
 * Find the first suggestion for given keys.
 *
 * @param string $keys
 *   The original keys that you want to find suggestion for.
 *
 * @return string
 *   The suggestion.
 */
function search_zerohit_suggest($keys) {

  $settings = ting_search_autocomplete_settings();
  $url = variable_get('ting_search_autocomplete_suggestion_url', '') . 'rest/facetSpell';

  $options['query'] = array('query' => check_plain($keys)) + $settings;
  $options['maxTime'] = 300;
  $url = url($url, $options);
  $curl = new MicroCURL();
  $headers[] = "Accept: application/json";
  $curl->set_option(CURLOPT_HTTPHEADER, $headers);
  $result = json_decode($curl->get(array($url)));
  $curl->close();

  $items = array();
  // First child of $result changes name from suggestions to suggestion when
  // switching between version 3.0 and 3.5
  if (!empty(current($result))) {
    foreach (current($result) as $suggestion) {
      return $suggestion->phrase;
    }
  }
  if (empty($items)) {
    return FALSE;
  }
}
/**
 * Returns an edit form for custom type settings.
 */
function ting_search_search_result_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $types = array();
  foreach (search_get_info() as $module => $info) {
    $types[$module] = $info['title'];
  }

  $form['type'] = array(
    '#type' => 'select',
    '#title' => t('Search type'),
    '#options' => $types,
    '#default_value' => $conf['type'],
  );

  $form['log'] = array(
    '#type' => 'checkbox',
    '#default_value' => $conf['log'],
    '#title' => t('Record a watchdog log entry when searches are made'),
  );

  $form['override_empty'] = array(
    '#type' => 'checkbox',
    '#default_value' => $conf['override_empty'],
    '#title' => t('Override "no result" text'),
  );

  $form['empty_title'] = array(
    '#title' => t('Title'),
    '#type' => 'textfield',
    '#default_value' => $conf['empty_title'],
    '#dependency' => array('edit-override-empty' => array(1)),
  );

  $form['empty_field'] = array(
    '#type' => 'text_format',
    '#title' => t('No result text'),
    '#default_value' => $conf['empty'],
    '#format' => $conf['empty_format'],
    '#dependency' => array('edit-override-empty' => array(1)),
  );

  $form['override_no_key'] = array(
    '#type' => 'checkbox',
    '#default_value' => $conf['override_no_key'],
    '#title' => t('Display text if no search keywords were submitted'),
  );

  $form['no_key_title'] = array(
    '#title' => t('Title'),
    '#type' => 'textfield',
    '#default_value' => $conf['no_key_title'],
    '#dependency' => array('edit-override-no-key' => array(1)),
  );

  $form['no_key_field'] = array(
    '#type' => 'text_format',
    '#title' => t('No result text'),
    '#default_value' => $conf['no_key'],
    '#format' => $conf['no_key_format'],
    '#dependency' => array('edit-override-no-key' => array(1)),
  );

  return $form;
}

/**
 * Submit handler for search form.
 */
function ting_search_search_result_content_type_edit_form_submit($form, &$form_state) {
  // Copy the text_format values over to where we normally store them.
  $form_state['values']['empty'] = $form_state['values']['empty_field']['value'];
  $form_state['values']['empty_format'] = $form_state['values']['empty_field']['format'];
  $form_state['values']['no_key'] = $form_state['values']['no_key_field']['value'];
  $form_state['values']['no_key_format'] = $form_state['values']['no_key_field']['format'];
  // Copy everything from our defaults.
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

/**
 * Returns the administrative title for a type.
 */
function ting_search_search_result_content_type_admin_title($subtype, $conf, $context) {
  $info = search_get_info();
  $type = isset($info[$conf['type']]['title']) ? $info[$conf['type']]['title'] : t('Missing/broken type');
  return t('@type search result', array('@type' => $type));
}

/**
 * Do a secondary search juggling around the drupal static.
 *
 * @param string $keys
 *   The search phrase.
 *
 * @return TingClientSearchResult
 *   The search result of the secondary search.
 */
function ting_search_do_secondary_search($keys, $options = array()) {

  // Store search result temporarily. We need to make seconday search.
  $temporary_search_result = drupal_static('ting_search_results');

  // Reset the static containing search result to be able do new search.
  drupal_static_reset('ting_search_search_execute');

  // Load original variables to temporary before changing them.
  if (!empty($options['profile'])) {
    $temporary_profile = variable_get('ting_search_profile', '');
    variable_set('ting_search_profile', $options['profile']);
  }
  if (!empty($options['agency'])) {
    $temporary_agency = variable_get('ting_agency', FALSE);
    variable_set('ting_agency', $options['agency']);
  }


  // Search for the secondary keys.
  ting_search_search_execute($keys, array('sort' => ''));

  $secondary_result = drupal_static('ting_search_results');

  // Overwrite the static with the original search.
  $revert = &drupal_static('ting_search_results');
  $revert = $temporary_search_result;

  if (!empty($temporary_profile)) {
    variable_set('ting_search_profile', $temporary_profile);
  }
  if (!empty($temporary_agency)) {
    variable_set('ting_agency', $temporary_agency);
  }

  return $secondary_result;
}
