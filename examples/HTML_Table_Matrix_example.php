/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ian Eure <ieure@php.net>                                    |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'PEAR.php';
require_once 'HTML/QuickForm.php';
require_once 'Numbers/Words.php';
require_once 'HTML/Table/Matrix.php';

$m = new HTML_Table_Matrix(array('border' => 1));
for($i = 5; $i <= 20; $i +=5) {
    $nSel[$i] = $i;
}

for($i = 0; $i <= 20; $i ++) {
    $sel[$i] = $i;
}

$fm['RLTB'] = 'Right to left, top to bottom';
$fm['LRTB'] = 'Left to right, top to bottom';

$def['number'] = 5;
$def['cols'] = 5;


$form = new HTML_QuickForm('formMatrixExample');
if($_POST) {
    $form->setDefaults($_POST);
} else {
    $form->setDefaults($def);
}


$form->addElement('header', '', "HTML_Table_Matrix Example");
$form->addElement('select', 'number', "Number of items", $nSel);
$form->addElement('select', 'cols', "Columns", $sel);
$form->addElement('select', 'rows', "Rows", $sel);
$form->addElement('select', 'startcol', "X Offset", $sel);
$form->addElement('select', 'startrow', "Y Offset", $sel);
$form->addElement('select', 'fillmode', "Fill", $fm);
$form->addElement('submit', "Submit");

$form->display();

if($_POST) {
    echo "<br><hr>";
    $nw = new Numbers_Words;
    for($i = 1; $i <= $_POST['number']; $i++) {
        $data[] = $nw->toWords($i);
    }

    $m->setData($data);
    $m->setTableSize($_POST['rows'], $_POST['cols']);
    $m->setFillStart($_POST['startrow'], $_POST['startcol']);
    $f = HTML_Table_Matrix_Filler::factory($_POST['fillmode'], $m);
    $m->accept($f);
    print $m->toHtml();

}

?>