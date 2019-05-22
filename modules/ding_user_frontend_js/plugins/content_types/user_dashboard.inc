<?php

/**
 * @file
 * User dashboard frontend component.
 */

$plugin = array(
  'title' => t('User dashboard'),
  'description' => t('User dashboard - powered by JavaScript'),
  'single' => TRUE,
  'content_types' => array('user_dashboard'),
  'render callback' => 'ding_user_frontend_js_user_dashboard_render',
  'required context' => new ctools_context_required(t('User'), 'user'),
  'category' => t('User'),
);

/**
 * Render callback function.
 */
function ding_user_frontend_js_user_dashboard_render($subtype, $conf, $panel_args, $context) {
  $token = ding_user_frontend_js_user_token();
  if (empty($token)) {
    watchdog('ding_user_frontend_js', 'Unable to render user dashboard. No API token available.', [],WATCHDOG_WARNING);
    return;
  }

  $block = new stdClass();

  $block->content = [
    '#markup' => '<div id="ding-user-frontend-js-user-dashboard" data-api-token="'. $token . '"/>',
  ];

  $js_path = drupal_get_path('module', 'ding_user_frontend_js') . '/js/app/dist';
  $block->content['#attached'] = [
    'js' => [
      [
        'type' => 'file',
        'data' => $js_path . '/runtime.js',
        'scope' => 'header'
      ],
      [
        'type' => 'file',
        'data' => $js_path . '/bundle.js',
        'scope' => 'header'
      ],
      [
        'type' => 'file',
        'data' => $js_path . '/dashboard.js',
        'scope' => 'footer'
      ],
      [
        'type' => 'file',
        'data' => $js_path . '/translations.js',
        'scope' => 'footer'
      ]
    ],
  ];

  return $block;
}

/**
 * Plugin settings form.
 */
function ding_user_frontend_js_user_dashboard_edit_form($form, &$form_state) {
  return $form;
}
