<?php

// Example of a configuration script for the Follow feature.
// Customize for local setup and preferences. Run with Drush:
// drush scr --script-path=/path/to/script/ configure_follow

///////////////////////////////////////////////////////////////////////////////
// CONFIGURATION

define('CONTACTOLOGY_KEY', '');
define('CONTACTOLOGY_CAMPAIGN', '');
define('SHORT_SITE_NAME', '');

$GLOBALS['follow_vocabs'] = array(
  'City',
  'Company',
  'Facility',
  'Movie',
  'Music Group',
  'Natural Feature',
  'Organization',
  'Person',
  'Published Medium',
  'Radio Program',
  'Radio Station',
  'TV Station',
);

// Shouldn't need to change this
define('CALAIS_KEY', '');

// END CONFIGURATION
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Enable modules

// This has the same effect as running "drush pm-enable" five times, once with each row of this array.
$modules = array(
  array('rdf'),
  array('calais_api', 'calais', 'calais_tagmods'),
  array('autoload'),
  array('messaging', 'messaging_mail', 'messaging_contactology', 'messaging_template'),
  array('notifications', 'notifications_tools', 'notifications_content', 'notifications_revision', 'notifications_subscribe', 'notifications_tags'),
);
drush_set_context('DRUSH_AFFIRMATIVE', TRUE);
foreach ($modules as $command) {
  // Roundabout way of running drush_invoke('pm-enable', 'module1', 'module2', ...)
  array_unshift($command, 'pm-enable');
  $result = call_user_func_array('drush_invoke', $command);
  drush_log('Running ' . implode(' ', $command), ($result ? 'ok' : 'failure'));
  // Bail out if any of the modules fail to enable.
  if (!$result) {
    exit(1);
  }
}

// End modules
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// Grant permissions for calais and follow
$permissions = array(
  'authenticated user' => array(
    'manage own subscriptions',
    'maintain own subscriptions',
    'subscribe to content',
    'subscribe to taxonomy terms',
  ),
  'Staff' => array(
    'skip notifications',
  ),
  'Reporter' => array(
    'access calais',
  ),
  'Editor' => array(
    'access calais',
  ),
  'Administrator' => array(
    'administer messaging',
    'edit message templates',
    'access calais',
    'access calais rdf',
    'administer calais',
    'administer calais api',
    'administer notifications',
    'manage all subscriptions',
  ),
);

foreach ($permissions as $role => $new_perms) {
  if (grant_permissions($role, $new_perms)) {
    drush_log("Setting permissions for $role", 'ok');
  } else {
    drush_log("Setting permissions for $role", 'failed');
  }
}

// End permissions
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Setup Calais

// API key
variable_set('calais_api_key', CALAIS_KEY);
// Store RDF locally?
variable_set('calais_store_rdf', 0);

// Enable all entity categories by default.
$all_vocabularies = calais_get_entity_vocabularies();
$entities = array();
foreach (array_keys($all_vocabularies) as $e) {
  $entities[$e] = $e;
}
variable_set('calais_applied_entities_global', $entities);

// Associate the editorial content type with Calais-created vocabularies
foreach ($all_vocabularies as $entity => $vid) {
  // Do a delete just in case the vocab is already associated with editorial, then add a record.
  db_query("DELETE FROM {vocabulary_node_types} WHERE vid='%d' and type='%s'", $vid, 'editorial');
  db_query("INSERT INTO {vocabulary_node_types} (vid, type) values('%d','%s') ", $vid, 'editorial');
}

// For editorial nodes, send Calais request automatically
variable_set('calais_node_editorial_request', '2');
// For editorial nodes, tag content always
variable_set('calais_node_editorial_process', 'AUTO');
// For editorial nodes, minimum suggested tag relevancy is 0.2
variable_set('calais_threshold_editorial', '0.2');
// For editorial nodes, use global settings for entity categories.
variable_set('calais_use_global_editorial', TRUE);

// Barebones blacklist for calais terms
variable_set('calais_tag_blacklist', 'CDATA,XML,Other');

drush_log('Configuring Calais', 'ok');


// End Calais
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// Messaging

// Disable simple mail so that only Contactology is enabled
variable_set('messaging_method_enabled', array('contactology' => 'contactology', 'mail' => 0));
// Contactology as the default send method.
variable_set('messaging_default_method', 'contactology');
// Rename Contactology to E-mail in case it appears in the interface.
variable_set('messaging_method_contactology', array('name' => 'E-mail', 'queue' => 0, 'log' => 0));
// Setup Contactology, if the info is available.
if (CONTACTOLOGY_KEY) {
  variable_set('messaging_contactology_api_key', CONTACTOLOGY_KEY);
}
if (CONTACTOLOGY_CAMPAIGN) {
  variable_set('messaging_contactology_campaign', CONTACTOLOGY_CAMPAIGN);
}
if (SHORT_SITE_NAME) {
  variable_set('messaging_contactology_contact_source', 'Followed Topic from ' . SHORT_SITE_NAME);
}
// Set Contactology filters to "No Filter"
variable_set('messaging_filters_contactology', array('body_format' => '', 'filter' => ''));

drush_log('Configuring Messaging', 'ok');

// End messaging
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// Messaging templates

/* Might have to be all manual. */

// End messaging templates
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// Notifications

// Notify poster of own posts: off
variable_set('notifications_sendself', 0);
// Immediate sending: off
variable_set('notifications_send_immediate', 0);
// Notifications sender: No one (All notifications will appear as coming from the web site)
variable_set('notifications_sender', 0);
// Default send interval: Immediately
variable_set('notifications_default_send_interval', 0);

// Turn on revision and insert events. Turn off update and comment events.
variable_set('notifications_event_enabled', array('node-revision' => 1, 'node-insert' => 1, 'node-update' => 0, 'node-comment' => 0));
// Set event templates to their defaults.
variable_set('notifications_event_template', array(
  'node-revision' => 'notifications-event-node-revision',
  'node-insert' => 'notifications-event-node-insert',
  'node-update' => 'notifications-event-node-update',
  'node-comment' => 'notifications-event-node-comment',
));

// Only allow one send interval.
variable_set('notifications_send_intervals', array(0 => 'Immediately'));
// No digesting for the immediate interval.
variable_set('notifications_digest_methods', array(0 => 'simple'));

// Turn on Tags and Thread subscription types, turn others off.
variable_set('notifications_subscription_types', array(
  'taxonomy' => 'taxonomy',
  'thread' => 'thread',
  'nodetype' => 0,
  'author' => 0,
  'typeauthor' => 0,
));

// Set up notifications separately for each content type.
variable_set('notifications_content_per_type', '1');
// Globally enabled subscription types (ignored because per type is true).
variable_set('notifications_content_type', array('taxonomy' => 'taxonomy', 'thread' => 'thread'));
// Same setup for editorial nodes.
variable_set('notifications_content_type_editorial', array('taxonomy' => 'taxonomy', 'thread' => 'thread'));

// Set enabled vocabularies to the list above.
variable_set('notifications_tags_vocabularies', enabled_vocabularies());
// Limit the displayed taxonomy terms: off
variable_set('notifications_tags_showsubscribed', 0);

drush_log('Configuring Notifications', 'ok');

// End notifications
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Closing thoughts

// Set access denied handler to a "please login to continue" page.
variable_set('site_403', 'login-required');

// Kick tires.
cache_clear_all();

drush_log("Automatic configuration done. Note that some manual steps are still needed.", 'ok');



function enabled_vocabularies() {
  $follow_vocabs = $GLOBALS['follow_vocabs'];
  $all_vocabs = taxonomy_get_vocabularies();
  $enabled = array();
  foreach ($all_vocabs as $v) {
    if (in_array($v->name, $follow_vocabs)) {
      $enabled[$v->vid] = $v->vid;
    }
  }
  return $enabled;
}

function grant_permissions($role, $new_perms, $flush_cache = false) {
  // Lookup current permissions by role name
  $result = db_query("SELECT r.rid, p.perm FROM {role} r JOIN {permission} p ON r.rid = p.rid WHERE r.name = '%s'", $role);
  if ($current = db_fetch_object($result)) {
    // Why the hell does Drupal store permissions separated by a comma and a space?
    $perms = explode(', ', $current->perm);
    $changed = false;
    // Add any new perms that aren't already in the list.
    foreach ($new_perms as $add) {
      if (!in_array($add, $perms)) {
        $changed = true;
        $perms[] = $add;
      }
    }
    // If any permissions were added, delete the old permissions row
    // and insert the updated values.
    if ($changed) {
      db_query('DELETE FROM {permission} WHERE rid = %d', $current->rid);
      db_query("INSERT INTO {permission} (rid, perm) VALUES (%d, '%s')", $current->rid, implode(', ', $perms));
    }
    if ($changed && $flush_cache) {
      cache_clear_all();
    }
    return true;
  }
  return false;  // no role found
}
