<?php

/**
 * Twitter API functions
 */

/**
 * Connect to the API using the 'proper' version (Oauth vs. standard)
 */
function twitter_connect($account) {
  module_load_include('php', 'oauth_common','lib/OAuth');
  $auth = $account->get_auth();
  if (_twitter_use_oauth() && $auth['oauth_token'] && $auth['oauth_token_secret']) {
    module_load_include('lib.php', 'oauth');
    return new TwitterOAuth(variable_get('twitter_consumer_key', ''), variable_get('twitter_consumer_secret', ''),
                            $auth['oauth_token'], $auth['oauth_token_secret']);
  }
  else {
    return new Twitter;
  }
}

/**
 * Saves a TwitterUser object to {twitter_account}
 */
function twitter_account_save($twitter_user, $save_auth = FALSE, $account = NULL) {
  $values = (array)$twitter_user;
  $values['twitter_uid'] = $values['id'];
  if ($save_auth) {
    $values += $twitter_user->get_auth();
    if (empty($account)) {
      global $user;
      $account = $user;
    }
    $values['uid'] = $account->uid;
  }
  if (db_result(db_query("SELECT 1 FROM {twitter_account} WHERE twitter_uid = %d", $values['id']))) {
    drupal_write_record('twitter_account', $values, array('twitter_uid'));
  }
  else {
    drupal_write_record('twitter_account', $values);
  }
}

function twitter_account_load($id) {
  $values = db_fetch_array(db_query("SELECT * FROM {twitter_account} WHERE twitter_uid = %d", $id));
  $values['id'] = $values['twitter_uid'];
  $account = new TwitterUser($values);
  $account->set_auth($values);
  $account->uid = $values['uid'];
  $account->import = $values['import'];
  $account->include_retweets = $values['include_retweets'];
  $account->is_global = $values['is_global'];

  return $account;
}

/**
 * Saves a TwitterStatus object to {twitter}
 */
function twitter_status_save($status) {
  $status->twitter_id = $status->id;
  $status->screen_name = $status->user->screen_name;
  $status->created_time = strtotime($status->created_at);

  if (db_result(db_query("SELECT 1 FROM {twitter} WHERE twitter_id = %s", $status->id))) {
    drupal_write_record('twitter', $status, array('twitter_id'));
  }
  else {
    drupal_write_record('twitter', $status);
  }
  // Let other modules know that an status has been just saved.
  module_invoke_all('twitter_status_save', $status);
}

/**
 * Post a message to twitter
 */
function twitter_set_status($twitter_account, $status) {
  $twitter = twitter_connect($twitter_account);
  return $twitter->status_update($status);
}

/**
 * Fetches a user's timeline
 */
function twitter_fetch_user_timeline($id) {
  $account = twitter_account_load($id);

  $since = db_result(db_query("SELECT MAX(twitter_id) FROM {twitter} WHERE screen_name = '%s'", $account->screen_name));

  $twitter = twitter_connect($account);

  $params = array();
  if ($since) {
    $params['since_id'] = $since;
  }

  if ($account->include_retweets) {
    $params['include_rts'] = true;
  }

  $statuses = $twitter->user_timeline($account->id, $params, $account->protected);
  foreach ($statuses as $status) {
    twitter_status_save($status);
  }

  if (count($statuses) > 0) {
    twitter_account_save($statuses[0]->user);
  }
  db_query("UPDATE {twitter_account} SET last_refresh = %d WHERE twitter_uid=%d", time(), $account->id);
}

/**
 * Deletes a Twitter account
 *
 * Removes entries from twitter_account and authmap tables
 */
function twitter_user_delete($twitter_uid) {
  $account = twitter_account_load($twitter_uid);

  $sql = "DELETE FROM {twitter_account} WHERE twitter_uid = %d";
  db_query($sql, $twitter_uid);

  $sql = "DELETE FROM {twitter} WHERE screen_name = '%s'";
  db_query($sql, $account->screen_name);

  $sql = "DELETE FROM {authmap} WHERE authname = %d AND module = 'twitter'";
  db_query($sql, $twitter_uid);
}
