<?php
/**
 * @filesource index/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Export;

use \Kotchasan\Http\Request;

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
   * ส่งออกเป็น PDF
   *
   * @param Request $request
   */
  public function index(Request $request)
  {
    $pdf = new \Kotchasan\Pdf;
    $pdf->AddPage();
    $pdf->WriteHTML($request->post('content')->toString());
    $pdf->Output();
  }
}
