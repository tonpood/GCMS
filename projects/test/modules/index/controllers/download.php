<?php
/**
 * @filesource modules/index/controllers/download.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Download;

use \Kotchasan\Http\Request;
use \Kotchasan\Http\Response;

/**
 * default Controller
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * คลาสสำหรับดาวน์โหลดไฟล์
   *
   * @param Request $request
   */
  public function index(Request $request)
  {
    // รับค่าจาก $_GET['id']
    $id = $request->get('id')->toInt();
    // อ่านข้อมูลจากฐานข้อมูล
    $download = \Index\Download\Model::get($id);
    // create Response
    $res = new Response();
    // set headers
    $res->withHeaders(array(
        'Content-Type' => 'application/octet-stream',
        'Content-disposition' => 'attachment; filename='.$download->name
      ))
      // set file contents จากคอลัมน์ text ของฐานข้อมูล
      ->withContent($download->text)
      // create download file
      ->send();
  }
}