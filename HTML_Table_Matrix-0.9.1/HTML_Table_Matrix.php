<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ian Eure <ieure@debian.org>                                 |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'PEAR.php';
require_once 'HTML/Table.php';

define('HTM_FILL_LR', 1);				// Fill horizontally from left to right
define('HTM_FILL_RL', 2);				// Fill horizontally from right to left - Unimplemented
define('HTM_FILL_TB', 3);				// Fill vertically from top to bottom
define('HTM_FILL_BT', 4);				// Fill vertically from bottom to top - Unimplemented

/**
 * Fills a HTML table with data.
 *
 * @package HTML_Table_Matrix
 * @author Ian Eure <ieure@websprockets.com>
 * @version 0.9.1
 * @link http://people.debian.org/~ieure/HTML_Table_Matrix/
 * @bugs HTM_FILL_RL and HTM_FILL_BT are not implemented.
 */
class HTML_Table_Matrix extends HTML_Table {

    // {{{ properties
    /**
     * The table fill mode
     *
     * @access private
     * @var int
     * @see setFillMode()
     */
    var $_fillMode = HTM_FILL_LR;

    /**
     * The direction to fill the table in, either (H)orizontal or (V)ertical.
     *
     * @access private
     * @var string
     * @see setFillMode()
     */
    var $_fillDirection = 'H';

    /**
     * The row to start filling at. Useful if you want to put other stuff in
     * the table.
     *
     * @access private
     * @var int
     * @see setFillStart()
     */
    var $_fillStartRow = 0;

    /**
     * The column to start filling at. Useful if you want to put other stuff in
     * the table.
     *
     * @access private
     * @var int
     * @see setFillStart()
     */
    var $_fillStartCol = 0;

    /**
     * The number of rows in the table. 0 = Undefined.
     *
     * @access private
     * @var int
     * @see setTableSize()
     */
    var $_rows = 0;

    /**
     * The number of columns in the table. 0 = Undefined.
     *
     * @access private
     * @var int
     * @see setTableSize()
     */
    var $_cols = 10;

    /**
     * Has the table been filled?
     *
     * @access private
     * @var boolean
     */
    var $_isFilled = FALSE;

    /**
     * Data to fill table with
     *
     * @access private
     * @var array
     * @see setData()
     */
    var $_data = array();
    // }}}


    // {{{ setData()
    /**
     * Sets data to fill table with.
     *
     * @return void
     * @param array $data 1-dimensional array of matrix data
     */
    function setData(&$data) {
        $this->_data = $data;
    }
    // }}}

    // {{{ setFillMode()
    /**
     * Set the table fill mode.
     *
     * Must be one of:
     *  HTM_FILL_LR - Fill from left-to-right, top-to-bottom
     *  HTM_FILL_RL - Fill from right-to-left, top-to-bottom
     *  HTM_FILL_TB - Fill from top-to-bottom, right-to-left
     *  HTM_FILL_BT - Fill from bottom-to-top, right-to-left
     *
     * @return void
     * @param int $mode Table fill mode
     */
    function setFillMode($mode) {
        $this->_fillMode = $mode;

        if ($mode == HTM_FILL_LR || $mode == HTM_FILL_RL) {
            $this->_fillDirection = 'H';
        } else if ($mode == HTM_FILL_TB || $mode == HTM_FILL_BT) {
            $this->_fillDirection = 'V';
        }
    }
    // }}}

    // {{{ setFillStart()
    /**
     * Set the row & column to start filling at.
     *
     * Defaults to (0,0), which is the upper-left corner of the table. Setting
     * this to a larger value will leave other cells empty, e.g. if you want to
     * add a header or other information in the table in addition to the matrix
     * data.
     *
     * @param int $row Row to start filling at
     * @param int $col Column to start filling at
     * @return void
     */
    function setFillStart($row, $col) {
        $this->_fillStartRow = $row;
        $this->_fillStartCol = $col;
    }
    // }}}

    // {{{ setTableSize()
    /**
     * Set the size of the resulting table.
     *
     * The table will be forced to this size, regardless of whether or not
     * there is enough (or too much) data to fill it up. If the table size
     * (rows * cols) is smaller than the amount of data given to us, only
     * (rows * cols) items are laid out.
     *
     * @param int $rows Number of rows, or zero to auto-size.
     * @param int $cols Number of columns, or zero to auto-size.
     * @return void
     */
    function setTableSize($rows = 0, $cols = 0) {
        $this->_rows = $rows;
        $this->_cols = $cols;
    }
    // }}}

    // {{{ _calculateSize()
    /**
     * Calculates the size of the table based on the data provided.
     *
     * @access private
     * @return void
     * @see setData()
     */
    function _calculateSize() {
        reset($this->_data);
        $n = count($this->_data);

        if (!$this->_rows && $this->_cols) {
            $this->_rows = ceil($n / $this->_cols);
        } else if (!$this->_cols && $this->_rows) {
            $this->_cols = ceil($n / $this->_rows);
        }
    }
    // }}}

    // {{{ fillTable()
    /**
    * Fills table with provided data. RL & BT modes are not implemented yet.
    *
    * This function does the actual laying out of the data into the table.
    * It isn't necessary to call this unless you want to add or change something
    * in the table, as toHtml() calls this automatically if the table has not
    * yet been filled with data.
    *
    * @return void
    * @see setData()
    */
    function fillTable() {
        $this->_calculateSize();
        reset($this->_data);

        $rowStart = $this->_fillStartRow;
        $rowEnd = $rowStart + $this->_rows;
        $colStart = $this->_fillStartCol;
        $colEnd = $colStart + $this->_cols;

//		echo "Filling $this->_cols"."x$this->_rows block of data from $colStart,$rowStart to $colEnd,$rowEnd - mode $this->_fillMode ($this->_fillDirection)<Br>\n";

        if ($this->_fillMode == HTM_FILL_LR) {
            for ($x = $rowStart; $x < $rowEnd; $x++) {
                for ($y = $colStart; $y < $colEnd; $y++) {
                    $this->_fillCell($x, $y);
                }
            }
        } else if ($this->_fillDirection == 'V') {
            for ($y = $colStart; $y < $colEnd; $y++) {
                for ($x = $rowStart; $x < $rowEnd; $x++) {
                    $this->_fillCell($x, $y);
                }
            }
        }

        $this->_isFilled = TRUE;
    }
    // }}}

    // {{{ _fillCell()
    /**
     * Fills a cell with data.
     *
     * Note: this depends on the array pointer of $_data pointing at the
     * right item. Possibly not be the best way to handle this.
     *
     * @access private
     * @param int $row Row of cell to fill.
     * @param int $col Column of cell to fill.
     */
    function _fillCell($row, $col) {
        list($null, $data) = each($this->_data);
        $this->setCellContents($row, $col, $data);
    }
    // }}}


    // {{{ toHtml()
    /**
     * Returns HTML table. Calls fillTable() if the table has not already
     * been filled.
     *
     * @return string HTML Table
     * @see HTML_Table::toHtml()
     */
    function toHtml() {
        if (!$this->_isFilled) {
            $this->fillTable();
        }

        return(parent::toHtml());
    }
    // }}}
}
?>
