<?
/*
$base = '/home/ieure/Projects/Current/MyPear/HTML_Table_Matrix';

require $base.'/Filler/LRTB.php';
*/

require_once 'Numbers/Words.php';
require_once '//home/ieure/Projects/Current/madzap/include/compat.inc.php';
require_once 'HTML/Table/Matrix.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$m = new HTML_Table_Matrix;

$stop = 20;
for ($i = 1; $i <= $stop; $i++) {
    $data[] = Numbers_Words::toWords($i);
}

$m->setData($data);
$m->setTableSize(0, 5);
$f = &HTML_Table_Matrix_Filler::factory('LRTB', &$m);
$res = $m->accept(&$f);
print $m->toHtml();

$f->next(0);

?>