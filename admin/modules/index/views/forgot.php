<?php
/**
 * @filesource index/views/forgot.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Forgot;

use \Kotchasan\Html;
use \Kotchasan\Login;
use \Kotchasan\Template;

/**
 * ฟอร์ม forgot
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\Adminview
{

  public function render()
  {
    // form
    $form = Html::create('form', array(
        'id' => 'forgot_frm',
        'class' => 'login',
        'autocomplete' => 'off',
        'ajax' => false,
        'action' => '?action=forgot'
    ));
    // h1
    $form->add('h1', array(
      'class' => 'icon-password',
      'innerHTML' => '{LNG_Request new password}'
    ));
    // message
    if (!empty(Login::$login_message)) {
      $form->add('p', array(
        'class' => empty(Login::$login_input) ? 'message' : 'error',
        'innerHTML' => Login::$login_message
      ));
    }
    // fieldset
    $fieldset = $form->add('fieldset');
    // email
    $fieldset->add('email', array(
      'id' => 'login_username',
      'labelClass' => 'g-input icon-email',
      'placeholder' => '{LNG_Email}',
      'value' => isset(Login::$text_username) ? Login::$text_username : '',
      'autofocus',
      'required',
      'accesskey' => 'e',
      'maxlength' => 255,
      'comment' => '{LNG_New password will be sent to the email address registered. If you do not remember or do not receive emails. Please contact your system administrator (Please check in the Junk Box)}'
    ));
    // input-groups (div สำหรับจัดกลุ่ม input)
    $group = $fieldset->add('groups');
    // a
    $group->add('a', array(
      'href' => self::$request->getUri()->withParams(array('action' => 'login'), true),
      'class' => 'td',
      'title' => '{LNG_Administrator Area}',
      'innerHTML' => '{LNG_Sign In} ?'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large wide',
      'value' => '{LNG_Get new password}'
    ));
    $fieldset->add('hidden', array(
      'id' => 'action',
      'value' => 'forgot'
    ));
    // template
    $template = Template::create('', '', 'login');
    $template->add(array(
      '/{FORM}/' => $form->render()
    ));
    return $template->render();
  }
}