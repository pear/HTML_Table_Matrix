<?php // $Id$
ini_set('include_path', '.:/usr/local/share/pear');

require_once 'PEAR.php';
require_once 'Numbers/Words.php';
require_once 'HTML/Table_Matrix.php';

$m = new HTML_Table_Matrix(array('border' => 1));
$m->setFillMode(HTM_FILL_LR);
$m->setTableSize(5, 4);

$nw = new Numbers_Words;
for ($i = 1; $i <= 20; $i++) {
	$data[] = $nw->toWords($i);
}

$m->setData(&$data);
print $m->toHtml();
?>
