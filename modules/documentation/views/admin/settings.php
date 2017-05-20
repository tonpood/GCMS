<?php
/**
 * @filesource documentation/views/admin/settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Documentation\Admin\Settings;

use \Kotchasan\Html;
use \Kotchasan\HtmlTable;

/**
 * โมดูลสำหรับจัดการการตั้งค่า
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\Adminview
{

  /**
   * module=documentation-settings
   *
   * @param object $index
   * @return string
   */
  public function render($index)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/documentation/model/admin/settings/save',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => '{LNG_Role of Members}'
    ));
    // สถานะสมาชิก
    $table = new HtmlTable(array(
      'class' => 'responsive config_table'
    ));
    $table->addHeader(array(
      array(),
      array('text' => '{LNG_Writing}'),
      array('text' => '{LNG_Settings}')
    ));
    foreach (self::$cfg->member_status AS $i => $item) {
      if ($i > 1) {
        $row = array();
        $row[] = array(
          'scope' => 'col',
          'text' => $item
        );
        $check = in_array($i, $index->can_write) ? ' checked' : '';
        $row[] = array(
          'class' => 'center',
          'text' => '<label data-text="{LNG_Writing}"><input type=checkbox name=can_write[] title="{LNG_Members of this group can create the content}" value='.$i.$check.'></label>'
        );
        $check = in_array($i, $index->can_config) ? ' checked' : '';
        $row[] = array(
          'class' => 'center',
          'text' => '<label data-text="{LNG_Settings}"><input type=checkbox name=can_config[] title="{LNG_Members of this group can setting the module (not recommend)}" value='.$i.$check.'></label>'
        );
        $table->addRow($row, array(
          'class' => 'status'.$i
        ));
      }
    }
    $div = $fieldset->add('div', array(
      'class' => 'item'
    ));
    $div->appendChild($table->render());
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => '{LNG_Save}'
    ));
    // id
    $fieldset->add('hidden', array(
      'name' => 'id',
      'value' => $index->module_id
    ));
    return $form->render();
  }
}