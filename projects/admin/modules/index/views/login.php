<?php
/**
 * @filesource index/views/login.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Login;

use \Kotchasan\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * Login Form
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * แสดงผล
   */
  public function render()
  {
    // form login
    $form = Html::create('form', array(
        'id' => 'login_frm',
        'class' => 'login',
        'autocomplete' => 'off',
        'gform' => false
    ));
    // h1
    $form->add('h1', array(
      'class' => 'icon-customer',
      'innerHTML' => Language::get('Administrator Area')
    ));
    // message
    if (isset(Login::$login_message)) {
      $form->add('p', array(
        'class' => empty(Login::$login_input) ? 'message' : 'error',
        'innerHTML' => Login::$login_message
      ));
    }
    // fieldset
    $fieldset = $form->add('fieldset', array(
      'title' => 'Please enter Username and Password (admin+admin)'
    ));
    // username
    $fieldset->add('text', array(
      'id' => 'login_username',
      'labelClass' => 'g-input icon-user',
      'placeholder' => Language::get('Username'),
      'accesskey' => 'e',
      'maxlength' => 255,
      'value' => isset(Login::$text_username) ? Login::$text_username : '',
    ));
    // password
    $fieldset->add('password', array(
      'id' => 'login_password',
      'labelClass' => 'g-input icon-password',
      'placeholder' => Language::get('Password'),
      'value' => isset(Login::$text_password) ? Login::$text_password : ''
    ));
    // input-groups (div สำหรับจัดกลุ่ม input)
    $group = $fieldset->add('groups');
    // a
    $group->add('a', array(
      'href' => self::$request->getUri()->withParams(array('action' => 'forgot'), true),
      'class' => 'td',
      'title' => Language::get('Request new password'),
      'innerHTML' => ''.Language::get('Forgot').' ?'
    ));
    // checkbox
    $group->add('checkbox', array(
      'id' => 'login_remember',
      'checked' => self::$request->cookie('login_remember')->toBoolean(),
      'value' => 1,
      'label' => Language::get('Remember me'),
      'labelClass' => 'td right'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large wide',
      'value' => Language::get('Sign In')
    ));
    // คืนค่า form
    return $form->render();
  }

  /**
   * title bar
   */
  public function title()
  {
    return Language::get('Administrator Area');
  }
}
