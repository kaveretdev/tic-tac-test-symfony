<?php
/**
 * Created by PhpStorm.
 * User: ami
 * Date: 10/29/15
 * Time: 10:02 AM
 */

namespace App\Tic;


class Board
{
    private $grid;

    const NOTHING = '';
    const O = 'o';
    const X = 'x';

    /**
     * Board constructor.
     */
    public function __construct()
    {
        $this->initGrid();
        $this->clear();
    }

    private function initGrid()
    {
        $this->grid = array(
            array(),
            array(),
            array(),
        );
    }

    public function clear()
    {
        for($i = 0; $i < 3; $i++) {
            for($j = 0; $j < 3; $j++) {
                $this->setSquare($i, $j, self::NOTHING);
            }
        }
    }

    public function getSquare($row, $col)
    {
        return $this->grid[$row][$col];
    }

    public function setSquare($row, $col, $val)
    {
        $this->grid[$row][$col] = $val;
        return $this->getSquare($row, $col);
    }

    public function isFull()
    {
        for($i = 0; $i < 3; $i++) {
            for($j = 0; $j < 3; $j++) {
                if(self::NOTHING == $this->getSquare($i, $j)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function isEmpty()
    {
        for($i = 0; $i < 3; $i++) {
            for($j = 0; $j < 3; $j++) {
                if(self::NOTHING != $this->getSquare($i, $j)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function loadBoard($grid)
    {
        $this->grid = $grid;
    }

    public function isBoardWon()
    {
        $res = false;
        for($i = 0; $i < 3; $i++) {
            $res = $res || $this->isColWon($i) || $this->isRowWon($i);
        }
        $res = $res || $this->isMainDiagonWon();
        $res = $res || $this->isSecondDiagonWon();
        return $res;
    }

    public function isRowWon($row)
    {
        $square = $this->getSquare($row, 0);
        if(self::NOTHING == $square) {
            return false;
        }
        for($i = 1; $i < 3; $i++) {
            if($square != $this->getSquare($row, $i)) {
                return false;
            }
        }
        return true;
    }

    public function isColWon($col)
    {
        $square = $this->getSquare(0, $col);
        if(self::NOTHING == $square) {
            return false;
        }
        for($i = 1; $i < 3; $i++) {
            if($square != $this->getSquare($i, $col)) {
                return false;
            }
        }
        return true;
    }

    public function isMainDiagonWon()
    {
        $square = $this->getSquare(0, 0);
        if(self::NOTHING == $square) {
            return false;
        }
        for($i = 1; $i < 3; $i++) {
            if($square != $this->getSquare($i, $i)) {
                return false;
            }
        }
        return true;
    }

    public function isSecondDiagonWon()
    {
        $square = $this->getSquare(0, 2);
        if(self::NOTHING == $square) {
            return false;
        }
        for($i = 1; $i >= 0; $i--) {
            if($square != $this->getSquare($i, $i)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getGrid()
    {
        return $this->grid;
    }


}