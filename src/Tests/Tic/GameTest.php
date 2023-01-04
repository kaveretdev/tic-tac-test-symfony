<?php
/**
 * Created by PhpStorm.
 * User: ami
 * Date: 10/29/15
 * Time: 11:19 AM
 */

namespace App\Tests\Tic;


use App\Tic\Board;
use App\Tic\Game;

class GameTest extends \PHPUnit_Framework_TestCase
{
    public function testStart()
    {
        $game = new Game();
        $game->start();

        $this->assertEquals(Game::STATE_NEW, $game->getState());
        $this->assertEquals(Board::X, $game->getCurrentPlayer());
    }

    public function testLegalMoves()
    {
        $game = new Game();
        $game->start();

        $this->assertTrue($game->isMoveLegal(0, 0));
        $game->makeMove(0, 0);
        $this->assertFalse($game->isMoveLegal(0, 0));
    }

    public function testPlayerSwitch()
    {
        $game = new Game();
        $game->start();

        $this->assertEquals(Board::X, $game->getCurrentPlayer(), 'X should have started');
        $game->makeMove(0, 0);
        $this->assertEquals(Board::O, $game->getCurrentPlayer(), 'now it\'s O\'s turn');
    }

    public function testGameState_InPlay()
    {
        $game = new Game();
        $game->start();

        $this->assertEquals(Game::STATE_NEW, $game->getState());
        $game->makeMove(0, 0);
        $this->assertEquals(Game::STATE_IN_PLAY, $game->getState());
    }

    public function testGameState_TIE()
    {
        $game = new Game();
        $game->start();

        $newGrid = [
            [Board::X, Board::X, Board::O],
            [Board::O, Board::X, Board::X],
            [Board::X, Board::O, Board::O],
        ];

        $game->getBoard()->loadBoard($newGrid);
        $this->assertEquals(Game::STATE_TIE, $game->getState());
    }

    public function testGameState_WIN()
    {
        $game = new Game();
        $game->start();

        $newGrid = [
            [Board::X, Board::X, Board::O],
            [Board::O, Board::X, Board::X],
            [Board::X, Board::NOTHING, Board::O],
        ];

        $game->getBoard()->loadBoard($newGrid);
        $game->setCurrentPlayer(Board::X);
        $game->makeMove(2,1);

        $this->assertEquals(Game::STATE_WON, $game->getState());
        $this->assertEquals(Board::X, $game->getWinner());
    }

    public function serializeTest()
    {
        $game = new Game();
        $game->start();

        $newGrid = [
            [Board::X, Board::X, Board::O],
            [Board::O, Board::X, Board::X],
            [Board::X, Board::NOTHING, Board::O],
        ];

        $game->getBoard()->loadBoard($newGrid);
        $game->setCurrentPlayer(Board::X);
        $game->makeMove(2,1);

        $json = $game->serialize();
        $newGame = new Game();
        $newGame->unserialize($json);

        $this->assertEquals(Game::STATE_WON, $newGame->getState());
        $this->assertEquals(Board::X, $newGame->getWinner());
    }


}