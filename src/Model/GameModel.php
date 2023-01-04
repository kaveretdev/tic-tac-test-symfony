<?php
/**
 * Created by PhpStorm.
 * User: ami
 * Date: 10/29/15
 * Time: 12:30 PM
 */

namespace App\Model;


use App\Tic\Game;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class GameModel
{
    /** @var  Session */
    private $session;

    /** @var  Game */
    private $game;

    /**
     * GameModel constructor.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
        $this->loadGame();
        $this->storeGame();
    }

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame($game)
    {
        $this->game = $game;
        $this->storeGame();
    }

    private function loadGame()
    {
        $json = $this->session->get('game', $this->emptyGameJson());
        $game = new Game();
        $game->unserialize($json);
        $this->game = $game;
        return $this->game;
    }

    private function storeGame()
    {
        $this->session->set('game', $this->game->serialize());
    }

    private function emptyGameJson()
    {
        $game = new Game();
        $game->start();
        return $game->serialize();
    }

    public function startGame()
    {
        $this->game->start();
        $this->storeGame();
    }
}