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
  $block = new stdClass();

  // Scan to find JavaScript file names. Our current built pipeline adds hashes
  // to file names so we not not have static names to refer to.
  $js_files = file_scan_directory(drupal_get_path('module', 'ding_user_frontend_js') . '/js/app/build/static/js', '/\.js$/');
  $js_attachments = array_map(function($js_file) {
    $uri = $js_file->uri;
    return [
      'type' => 'file',
      'data' => $uri,
      // With our current build pipeline some files need to go in
      // the header for bootstrapping and others can be loaded later on. This
      // should be addressed with a more specialized build pipeline.
      'scope' => (strpos($uri, 'main')) ? 'header' : 'footer',
    ];
  }, $js_files);

  $block->content = [
    '#markup' => '<div id="ding-user-frontend-js-user-dashboard"/>',
    '#attached' => [
      'css' => [
        // We do not used the built CSS file. Drupal should be sufficient for
        // optimizing it.
        drupal_get_path('module', 'ding_user_frontend_js') . '/js/app/src/css/UserDashboard.css'
      ],
      'js' => $js_attachments
    ]
  ];
  return $block;
}

/**
 * Plugin settings form.
 */
function ding_user_frontend_js_user_dashboard_edit_form($form, &$form_state) {
  return $form;
}
