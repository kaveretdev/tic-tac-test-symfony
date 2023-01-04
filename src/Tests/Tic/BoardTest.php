<?php
/**
 * Created by PhpStorm.
 * User: ami
 * Date: 10/29/15
 * Time: 10:14 AM
 */

namespace App\Tests\Tic;


use App\Tic\Board;

class BoardTest extends \PHPUnit_Framework_TestCase
{
    public function testSanity()
    {
        $this->assertEquals(1, 1, 'failed sanity check');
    }

    public function testBasicBoardActions()
    {
        $board = new Board();
        $this->assertFalse($board->isFull(), 'board is not full!');
        $this->assertTrue($board->isEmpty(), 'board is empty!');

        $board->setSquare(0, 0, Board::X);
        $this->assertFalse($board->isFull(), 'board is not full!');
        $this->assertFalse($board->isEmpty(), 'board is not empty!');

        $this->assertEquals(Board::X, $board->getSquare(0, 0));
    }

    public function testBoardLoad()
    {
        $board = new Board();
        $this->assertFalse($board->isFull(), 'board is not full!');
        $this->assertTrue($board->isEmpty(), 'board is empty!');

        $newGrid = [
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
        ];
        $board->loadBoard($newGrid);
        $this->assertTrue($board->isEmpty(), 'board is empty!');

        $newGrid = [
            [Board::X, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
        ];
        $board->loadBoard($newGrid);
        $this->assertFalse($board->isFull(), 'board is not full!');
        $this->assertFalse($board->isEmpty(), 'board is not empty!');
        $this->assertEquals(Board::X, $board->getSquare(0, 0));
    }

    public function testBoardClear()
    {
        $board = new Board();
        $this->assertFalse($board->isFull(), 'board is not full!');
        $this->assertTrue($board->isEmpty(), 'board is empty!');
        $newGrid = [
            [Board::X, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
        ];
        $board->loadBoard($newGrid);
        $this->assertFalse($board->isFull(), 'board is not full!');
        $this->assertFalse($board->isEmpty(), 'board is not empty!');
        $board->clear();
        $this->assertTrue($board->isEmpty(), 'board is empty!');
    }

    public function testBoardNotWon()
    {
        $board = new Board();
        $this->assertFalse($board->isColWon(0));
        $this->assertFalse($board->isColWon(1));
        $this->assertFalse($board->isColWon(2));
        $this->assertFalse($board->isRowWon(0));
        $this->assertFalse($board->isRowWon(1));
        $this->assertFalse($board->isRowWon(2));
        $this->assertFalse($board->isMainDiagonWon());
        $this->assertFalse($board->isSecondDiagonWon());
        $this->assertFalse($board->isBoardWon());
    }

    public function testRowWon()
    {
        $board = new Board();
        $newGrid = [
            [Board::X, Board::X, Board::X, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
            [Board::NOTHING, Board::NOTHING, Board::NOTHING, ],
        ];
        $board->loadBoard($newGrid);

        $this->assertTrue($board->isBoardWon());
        $this->assertTrue($board->isRowWon(0));
        $this->assertFalse($board->isRowWon(1));
    }

    public function testColWon()
    {
        $board = new Board();
        $newGrid = [
            [Board::O, Board::X, Board::X, ],
            [Board::O, Board::NOTHING, Board::NOTHING, ],
            [Board::O, Board::NOTHING, Board::NOTHING, ],
        ];
        $board->loadBoard($newGrid);

        $this->assertTrue($board->isBoardWon());
        $this->assertTrue($board->isColWon(0));
        $this->assertFalse($board->isColWon(1));
    }

    public function testMainDiagonWon()
    {
        $board = new Board();
        $newGrid = [
            [Board::O, Board::X, Board::X, ],
            [Board::O, Board::O, Board::NOTHING, ],
            [Board::X, Board::NOTHING, Board::O, ],
        ];
        $board->loadBoard($newGrid);

        $this->assertTrue($board->isBoardWon());
        $this->assertTrue($board->isMainDiagonWon());
        $this->assertFalse($board->isSecondDiagonWon());
    }

    public function testSecondDiagonWon()
    {
        $board = new Board();
        $newGrid = [
            [Board::O, Board::X, Board::X, ],
            [Board::O, Board::X, Board::NOTHING, ],
            [Board::X, Board::NOTHING, Board::O, ],
        ];
        $board->loadBoard($newGrid);

        $this->assertTrue($board->isBoardWon());
        $this->assertFalse($board->isMainDiagonWon());
        $this->assertTrue($board->isSecondDiagonWon());
    }

}