<?php

/**
 * @file
 * Contains \Drupal\login_security\Form\LoginSecurityAdminSettings.
 */

namespace Drupal\login_security\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

class LoginSecurityAdminSettings extends ConfigFormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'login_destination_settings';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('login_security.settings');

    $form['track_time'] = array(
      '#type' => 'textfield',
      '#title' => t('Track time'),
      '#default_value' => $config->get('track_time'),
      '#element_validate' => array(array($this, 'validInteger')),
      '#size' => 3,
      '#description' => t('The time window to check for security violations: the time in hours the login information is kept to compute the login attempts count. A common example could be 24 hours. After that time, the attempt is deleted from the list, and will never be considered again.'),
      '#field_suffix' => t('Hours'),
    );
    $form['user_wrong_count'] = array(
      '#type' => 'textfield',
      '#title' => t('Maximum number of login failures before blocking a user'),
      '#default_value' => $config->get('user_wrong_count'),
      '#element_validate' => array(array($this, 'validInteger')),
      '#size' => 3,
      '#description' => t('Enter the number of login failures a user is allowed. After this amount is reached, the user will be blocked, no matter the host attempting to log in. Use this option carefully on public sites, as an attacker may block your site users. The user blocking protection will not disappear and should be removed manually from the <a href="!user">user management</a> interface.', array('!user' => '/admin/people')),
      '#field_suffix' => t('Failed attempts'),
    );
    $form['host_wrong_count'] = array(
      '#type' => 'textfield',
      '#title' => t('Maximum number of login failures before soft blocking a host'),
      '#default_value' => $config->get('host_wrong_count'),
      '#element_validate' => array(array($this, 'validInteger')),
      '#size' => 3,
      '#description' => t('Enter the number of login failures a host is allowed. After this amount is reached, the host will not be able to submit the log in form again, but can still browse the site contents as an anonymous user. This protection is effective during the time indicated at tracking time option.'),
      '#field_suffix' => t('Failed attempts'),
    );
    $form['host_wrong_count_hard'] = array(
      '#type' => 'textfield',
      '#title' => t('Maximum number of login failures before blocking a host'),
      '#default_value' => $config->get('host_wrong_count_hard'),
      '#element_validate' => array(array($this, 'validInteger')),
      '#size' => 3,
      '#description' => t('Enter the number of login failures a host is allowed. After this number is reached, the host will be blocked, no matter the username attempting to log in. The host blocking protection will not disappear automatically and should be removed manually from the <a href="!access">access rules</a> administration interface.', array('!access' => '/admin/config/people/ip-blocking')),
      '#field_suffix' => t('Failed attempts'),
    );
    $form['activity_threshold'] = array(
      '#type' => 'textfield',
      '#title' => t('Maximum number of login failures before detecting an ongoing attack'),
      '#default_value' => $config->get('activity_threshold'),
      '#element_validate' => array(array($this, 'validInteger')),
      '#size' => 3,
      '#description' => t('Enter the number of login failures before creating a warning log entry about this suspicious activity. If the number of invalid login events currently being tracked reach this number, and ongoing attack is detected.'),
      '#field_suffix' => t('Failed attempts'),
    );
    $form['login_messages'] = array(
      '#type' => 'fieldset',
      '#title' => t('Notifications'),
    );
    $form['login_messages']['disable_core_login_error'] = array(
      '#type' => 'checkbox',
      '#title' => t('Disable login failure error message'),
      '#description' => t('Checking this option prevents the display of login error messages. A user attempting to login will not be aware if the account exists, an invalid user name or password has been submitted, or if the account is blocked. The core messages "Sorry, unrecognized username or password. Have you forgotten your password?" and "The username {username} has not been activated or is blocked." are also hidden.'),
      '#default_value' => $config->get('disable_core_login_error'),
    );
    $form['login_messages']['notice_attempts_available'] = array(
      '#type' => 'checkbox',
      '#title' => t('Notify the user about the number of remaining login attempts'),
      '#default_value' => $config->get('notice_attempts_available'),
      '#description' => t('Checking this option, the user is notified about the number of remaining login attempts before the account gets blocked. Security tip: If you enable this option, try to not disclose as much of your login policies as possible in the message shown on any failed login attempt.'),
    );
    $form['login_messages']['last_login_timestamp'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display last login timestamp'),
      '#description' => t('Checking this option, when a user successfully logs in, a message will display the last time he logged into the site.'),
      '#default_value' => $config->get('last_login_timestamp'),
    );
    $form['login_messages']['last_access_timestamp'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display last access timestamp'),
      '#description' => t('Checking this option, when a user successfully logs in, a message will display the last site access with this account.'),
      '#default_value' => $config->get('last_access_timestamp'),
    );

    $form['login_messages']['user_blocked_email_user'] = array(
      '#type' => 'textfield',
      '#title' => t('Select who should get an email message when a user is blocked by this module'),
      '#description' => t('No notification will be sent if the field is blank'),
      '#default_value' => $config->get('user_blocked_email_user'),
      '#autocomplete_route_name' => 'user.autocomplete',
      '#element_validate' => array(array($this, 'validUser')),
    );
    $form['login_messages']['login_activity_email_user'] = array(
      '#type' => 'textfield',
      '#title' => t('Select who should get an email message when an ongoing attack is detected'),
      '#description' => t('No notification will be sent if the field is blank'),
      '#default_value' => $config->get('login_activity_email_user'),
      '#autocomplete_route_name' => 'user.autocomplete',
      '#element_validate' => array(array($this, 'validUser')),
    );

    $form['login_security']['Notifications'] = array(
      '#type' => 'fieldset',
      '#title' => t('Edit notification texts'),
      '#weight' => 3,
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#description' => t("You may edit the notifications used by the Login Security module. Allowed placeholders for all the notifications include the following: <ul><li>%date                  :  The (formatted) date and time of the event.</li><li>%ip                    :  The IP address tracked for this event.</li><li>%username              :  The username entered in the login form (sanitized).</li><li>%email                 :  If the user exists, this will be the email address.</li><li>%uid                   :  If the user exists, this will be the user uid.</li><li>%site                  :  The name of the site as configured in the administration.</li><li>%uri                   :  The base url of this Drupal site.</li><li>%edit_uri              :  Direct link to the user (based on the name entered) edit page.</li><li>%hard_block_attempts   :  Configured maximum attempts before hard blocking the IP address.</li><li>%soft_block_attempts   :  Configured maximum attempts before soft blocking the IP address.</li><li>%user_block_attempts   :  Configured maximum login attempts before blocking the user.</li><li>%user_ip_current_count :  The total attempts for this user name tracked from this IP address.</li><li>%ip_current_count      :  The total login attempts tracked from from this IP address.</li><li>%user_current_count    :  The total login attempts tracked for this user name .</li><li>%tracking_time         :  The tracking time value: in hours.</li><li>%tracking_current_count:  Total tracked events</li><li>%activity_threshold    :  Value of attempts to detect ongoing attack.</li></ul>"),
    );
    $form['login_security']['Notifications']['notice_attempts_message'] = array(
      '#type' => 'textarea',
      '#title' => t('Message to be shown on each failed login attempt'),
      '#rows' => 2,
      '#default_value' => $config->get('notice_attempts_message'),
      '#description' => t('Enter the message string to be shown if the login fails after the form is submitted. You can use any of the placeholders here.'),
    );
    $form['login_security']['Notifications']['host_soft_banned'] = array(
      '#type' => 'textarea',
      '#title' => t('Message for banned host (Soft IP ban)'),
      '#rows' => 2,
      '#default_value' => $config->get('host_soft_banned'),
      '#description' => t('Enter the soft IP ban message to be shown when a host attempts to log in too many times.'),
    );
    $form['login_security']['Notifications']['host_hard_banned'] = array(
      '#type' => 'textarea',
      '#rows' => 2,
      '#title' => t('Message for banned host (Hard IP ban)'),
      '#default_value' => $config->get('host_hard_banned'),
      '#description' => t('Enter the hard IP ban message to be shown when a host attempts to log in too many times.'),
    );
    $form['login_security']['Notifications']['user_blocked'] = array(
      '#type' => 'textarea',
      '#rows' => 2,
      '#title' => t('Message when user is blocked by uid'),
      '#default_value' => $config->get('user_blocked'),
      '#description' => t('Enter the message to be shown when a user gets blocked due to enough failed login attempts.'),
    );

    $form['login_security']['Notifications']['user_block_email'] = array(
      '#type' => 'fieldset',
      '#title' => t('Email to be sent to the defined user for blocked accounts.'),
      '#weight' => 3,
      '#description' => t('Configure the subject and body of the email message.'),
    );
    $form['login_security']['Notifications']['user_block_email']['user_blocked_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => t('Email subject'),
      '#default_value' => $config->get('user_blocked_email_subject'),
    );
    $form['login_security']['Notifications']['user_block_email']['user_blocked_email_body'] = array(
      '#type' => 'textarea',
      '#title' => t('Email body'),
      '#default_value' => $config->get('user_blocked_email_body'),
      '#description' => t('Enter the message to be sent to the administrator informing a user has been blocked.'),
    );

    $form['login_security']['Notifications']['login_activity_email'] = array(
      '#type' => 'fieldset',
      '#title' => t('Email to be sent to the defined user for ongoing attack detections.'),
      '#weight' => 3,
      '#description' => t('Configure the subject and body of the email message.'),
    );
    $form['login_security']['Notifications']['login_activity_email']['login_activity_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => t('Email subject'),
      '#default_value' => $config->get('login_activity_email_subject'),
    );
    $form['login_security']['Notifications']['login_activity_email']['login_activity_email_body'] = array(
      '#type' => 'textarea',
      '#title' => t('Email body'),
      '#default_value' => $config->get('login_activity_email_body'),
      '#description' => t('Enter the message to be sent to the administrator informing about supicious activity.'),
    );

    // Clean event tracking list.
    $form['buttons']['clean_tracked_events'] = array(
      '#type' => 'submit',
      '#value' => t('Clear event tracking information'),
      '#weight' => 20,
      '#submit' => array('_login_security_clean_tracked_events'),
    );
    return parent::buildForm($form, $form_state);
  }


  /**
   * Verify that element is a positive integer value.
   */
  public function validInteger($element, FormStateInterface $form_state) {
    if (!ctype_alnum($element['#value']) || intval($element['#value']) < 0) {
      $form_state->setError($element, $form_state, t('The @field field should be a positive integer value greater than or equal to 0.', array('@field' => $element['#title'])));
    }
  }

  /**
   * Verify that element is a valid username.
   */
  public function validUser($element, FormStateInterface $form_state) {
    if ($element['#value'] !== '') {
      $count = db_select('users_field_data', 'u')
      ->condition('name', $element['#value'])
      ->countQuery()
      ->execute()
      ->fetchField();
      if (intval($count) != 1) {
        $form_state->setError($element, $form_state, t('The @field field should be a valid username.', array('@field' => $element['#title'])));
      }
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   An associative array containing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('login_security.settings')
    ->set('track_time', $form_state->getValue('track_time'))
    ->set('user_wrong_count', $form_state->getValue('user_wrong_count'))
    ->set('host_wrong_count', $form_state->getValue('host_wrong_count'))
    ->set('host_wrong_count_hard', $form_state->getValue('host_wrong_count_hard'))
    ->set('activity_threshold', $form_state->getValue('activity_threshold'))
    ->set('disable_core_login_error', $form_state->getValue('disable_core_login_error'))
    ->set('notice_attempts_available', $form_state->getValue('notice_attempts_available'))
    ->set('last_login_timestamp', $form_state->getValue('last_login_timestamp'))
    ->set('last_login_timestamp', $form_state->getValue('last_login_timestamp'))
    ->set('last_access_timestamp', $form_state->getValue('last_access_timestamp'))
    ->set('login_activity_email_user', $form_state->getValue('login_activity_email_user'))
    ->set('user_blocked_email_user', $form_state->getValue('user_blocked_email_user'))
    ->set('notice_attempts_message', $form_state->getValue('notice_attempts_message'))
    ->set('host_soft_banned', $form_state->getValue('host_soft_banned'))
    ->set('host_hard_banned', $form_state->getValue('host_hard_banned'))
    ->set('user_blocked', $form_state->getValue('user_blocked'))
    ->set('user_blocked_email_subject', $form_state->getValue('user_blocked_email_subject'))
    ->set('user_blocked_email_body', $form_state->getValue('user_blocked_email_body'))
    ->set('login_activity_email_subject', $form_state->getValue('login_activity_email_subject'))
    ->set('login_activity_email_body', $form_state->getValue('login_activity_email_body'))
    ->save();

    parent::submitForm($form, $form_state);
  }
}