<?php
/**
 * @filesource modules/index/views/language.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Language;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\Text;

/**
 * module=language
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\Adminview
{
  /**
   * ข้อมูลโมดูล
   */
  private $languages;

  /**
   * ตารางภาษา
   *
   * @return string
   */
  public function render(Request $request)
  {
    // ชนิดของภาษาที่เลือก php,js
    $js = $request->request('js')->toBoolean();
    $this->languages = \Gcms\Gcms::installedLanguage();
    // URL สำหรับส่งให้ตาราง
    $uri = $request->createUriWithGlobals(WEB_URL.'admin/index.php');
    // ตารางภาษา
    $table = new DataTable(array(
      'id' => 'language_table',
      /* Uri */
      'uri' => $uri,
      /* Model */
      'model' => 'Index\Language\Model',
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('type', 'js'),
      /* แบ่งหน้า */
      'perPage' => max(10, $request->cookie('language_perPage', 30)->toInt()),
      /* เรียงลำดับ */
      'sort' => $request->cookie('language_sort', 'id DESC')->toString(),
      'searchColumns' => array_merge(array('key'), $this->languages),
      'headers' => array(
        'id' => array(
          'text' => '{LNG_ID}',
          'sort' => 'id'
        ),
        'key' => array(
          'text' => '{LNG_Key}',
          'sort' => 'key'
        ),
        'owner' => array(
          'text' => '{LNG_Module}',
          'class' => 'center',
          'sort' => 'owner'
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'owner' => array(
          'class' => 'center'
        ),
      ),
      'action' => 'index.php/index/model/language/action?js='.$js,
      'actionCallback' => 'doFormSubmit',
      'actionConfirm' => 'confirmAction',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => '{LNG_With selected}',
          'options' => array(
            'delete' => '{LNG_Delete}'
          )
        ),
        array(
          'class' => 'button add icon-plus',
          'href' => $uri->createBackUri(array('module' => 'languageedit', 'id' => null, 'js' => $js)),
          'text' => '{LNG_Add New}'
        )
      ),
      'buttons' => array(
        array(
          'class' => 'icon-edit button green',
          'href' => $uri->createBackUri(array('module' => 'languageedit', 'id' => ':id', 'js' => $js)),
          'text' => '{LNG_Edit}'
        )
      ),
      'filters' => array(
        'js' => array(
          'name' => 'js',
          'text' => '{LNG_Type}',
          'options' => array(0 => 'php', 1 => 'js'),
          'value' => $js
        )
      )
    ));
    foreach ($this->languages as $lng) {
      $table->headers[$lng] ['sort'] = $lng;
    }
    // save cookie
    setcookie('language_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
    setcookie('language_sort', $table->sort, time() + 3600 * 24 * 365, '/');
    $table->script('initLanguageTable("language_table");');
    return $table->render();
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item, $o, $prop)
  {
    foreach ($this->languages as $lng) {
      if ($item['type'] == 'array') {
        if (!empty($item[$lng])) {
          $data = @unserialize($item[$lng]);
          if (is_array($data)) {
            $item[$lng] = implode(', ', $data);
          }
        }
      }
      $item[$lng] = empty($item[$lng]) ? '' : '<span title="'.htmlspecialchars($item[$lng]).'">'.self::toText($item[$lng]).'</span>';
    }
    $item['key'] = '<a class="icon-copy" title="'.htmlspecialchars($item['key']).'">'.self::toText($item['key']).'</a>';
    return $item;
  }

  private static function toText($text)
  {
    return Text::cut(str_replace(array("\r", "\n", '&'), array('', ' ', '&amp;'), strip_tags($text)), 50);
  }
}