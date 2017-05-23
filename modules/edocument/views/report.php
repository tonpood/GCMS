<?php
/**
 * @filesource edocument/views/report.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Report;

use \Kotchasan\Http\Request;
use \Kotchasan\DataTable;
use \Kotchasan\Login;
use \Kotchasan\Date;
use \Kotchasan\Http\Uri;
use \Kotchasan\Template;

/**
 * รายงานการดาวน์โหลด
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
  /**
   * ข้อมูลโมดูล
   */
  private $modules;

  /**
   * รายงานการดาวน์โหลด
   *
   * @param Request $request
   * @param object $index
   * @return object
   */
  public function render(Request $request, $index)
  {
    if (Login::isMember()) {
      // ตรวจสอบรายการที่ต้องการ
      $index = \Edocument\Write\Model::get($request->request('id')->toInt(), $index);
      // ตาราง
      $table = new DataTable(array(
        /* Model */
        'model' => 'Edocument\Admin\Report\Model',
        /* คอลัมน์ที่ไม่ต้องแสดงผล */
        'hideColumns' => array('lname', 'email', 'id'),
        /* Uri */
        'uri' => Uri::createFromUri(WEB_URL.'index.php?module=editprofile&tab='.$index->tab.'&id='.$index->id),
        /* รายการต่อหน้า */
        'perPage' => $request->cookie('edocument_perPage', 30)->toInt(),
        /* query where */
        'defaultFilters' => array(
          array('D.document_id', $index->id)
        ),
        /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
        'onRow' => array($this, 'onRow'),
        /* คอลัมน์ที่สามารถค้นหาได้ */
        'searchColumns' => array('fname', 'lname', 'email'),
        /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
        'headers' => array(
          'fname' => array(
            'text' => '{LNG_Name} {LNG_Surname}'
          ),
          'status' => array(
            'text' => '{LNG_Recipient}',
            'class' => 'center'
          ),
          'last_update' => array(
            'text' => '{LNG_Lastest}',
            'class' => 'center'
          ),
          'downloads' => array(
            'text' => '{LNG_Download}',
            'class' => 'center'
          )
        ),
        /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
        'cols' => array(
          'status' => array(
            'class' => 'center'
          ),
          'downloads' => array(
            'class' => 'center reply'
          ),
          'last_update' => array(
            'class' => 'date'
          )
        )
      ));
      // save cookie
      setcookie('edocument_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
      // /edocument/report.html
      $template = Template::create('edocument', 'edocument', 'report');
      $template->add(array(
        '/{TOPIC}/' => $index->module->topic,
        '/{NO}/' => $index->document_no,
        '/{DETAIL}/' => $index->detail,
        '/{LIST}/' => $table->render()
      ));
      // คืนค่า
      $index->topic = '{LNG_Download Details} '.$index->document_no;
      $index->detail = $template->render();
      return $index;
    }
    return null;
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item)
  {
    $name = trim($item['fname'].' '.$item['lname']);
    $item['fname'] = '<a href="'.WEB_URL.'index.php?module=member&amp;id='.$item['id'].'">'.( empty($name) ? $item['email'] : $name).'</a>';
    $item['last_update'] = Date::format($item['last_update'], 'd M Y H:i:s');
    $item['status'] = isset(self::$cfg->member_status[$item['status']]) ? '<span class=status'.$item['status'].'>'.self::$cfg->member_status[$item['status']].'</span>' : '';
    return $item;
  }
}