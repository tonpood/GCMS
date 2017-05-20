<?php
/**
 * @filesource index/views/address.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Address;

use \Kotchasan\Http\Request;
use \Kotchasan\Template;
use \Kotchasan\Orm\Recordset;
use \Kotchasan\Province;
use \Kotchasan\Country;

/**
 * หน้าแก้ไขข้อมูลส่วนตัว
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

  /**
   * แสดงรายละเอียดที่อยู่สมาชิก
   *
   * @param Request $request
   * @param object $index
   * @return object
   */
  public function render(Request $request, $index)
  {
    // อ่านข้อมูลสมาชิก
    $rs = Recordset::create('Index\User\Model');
    $user = $rs->where((int)$_SESSION['login']['id'])->first('id', 'provinceID', 'country', 'fname', 'lname', 'address1', 'address2', 'province', 'zipcode');
    $template = Template::create('member', 'member', 'address');
    $contents = array();
    // ข้อมูลฟอร์ม
    foreach ($user as $key => $value) {
      if ($key === 'provinceID' || $key === 'country') {
        // select
        if ($key == 'provinceID') {
          $source = Province::all();
        } elseif ($key == 'country') {
          $source = Country::all();
        }
        $datas = array();
        foreach ($source as $k => $v) {
          $sel = $k == $value ? ' selected' : '';
          $datas[] = '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
        }
        $contents['/{'.strtoupper($key).'}/'] = implode('', $datas);
      } else {
        $contents['/{'.strtoupper($key).'}/'] = $value;
      }
    }
    $template->add($contents);
    $index->detail = $template->render();
    // คืนค่า
    return $index;
  }
}