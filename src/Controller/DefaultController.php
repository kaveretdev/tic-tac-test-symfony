<?php

namespace App\Controller;

use App\Model\GameModel;
use App\Tic\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function indexAction(Request $request)
    {
        return $this->render(
            'Default/index.html.twig'
        );
    }

    #[Route(path: '/start', name: 'start', methods: ['GET'])]
    public function startAction(GameModel $gameModel)
    {
        $gameModel->startGame();
        $game = $gameModel->getGame();

        return $this->render(
            'Default/start.html.twig', array(
            'grid' => $game->getBoard()->getGrid(),
            'currentPlayer' => $game->getCurrentPlayer(),
        ));
    }

    #[Route(path: '/play/{row}-{col}', name: 'play', methods: ['GET'])]
    public function playAction(GameModel $gameModel, $row, $col)
    {
        $messages = array();
        $game = $gameModel->getGame();

        if(!$game->isMoveLegal($row, $col)) {
            $messages []= 'illegal move';
        } else {
            $game->makeMove($row, $col);
            $gameModel->setGame($game);
            if($this->isGameOver($game)) {
                return $this->redirectToRoute('end');
            }
        }

        return $this->render(
            'Default/play.html.twig', array(
            'row' => $row,
            'col' => $col,
            'messages' => $messages,
            'grid' => $game->getBoard()->getGrid(),
            'currentPlayer' => $game->getCurrentPlayer(),
        ));
    }

    #[Route(path: '/end', name: 'end', methods: ['GET'])]
    public function endAction(GameModel $gameModel)
    {
        $game = $gameModel->getGame();

        if(Game::STATE_TIE == $game->getState()) {
            $message = 'Game Over: tie! how boring!';
        } else {
            $message = 'Game Over: ' . $game->getWinner() . ' has won!';
        }

        return $this->render(
            'Default/end.html.twig', [
            'message' => $message,
            'grid' => $game->getBoard()->getGrid(),
        ]);
    }

    private function isGameOver(Game $game)
    {
        return in_array($game->getState(), array(Game::STATE_TIE, Game::STATE_WON));
    }
}
