<?php
namespace App\Controller;

use App\Repository\PollRepository;
use App\Repository\PollItemRepository;
use App\Repository\UserPollItemRepository;
use App\Entity\Poll;
use App\Entity\PollItem;
use App\Entity\UserPollItem;

class PollController extends Controller {
    
    public function list() {
        $pollRepo = new PollRepository();
        $polls = $pollRepo->findAll();
        $this->render('poll/list', ['polls' => $polls]);
    }
    
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /');
            exit;
        }
        $repo = new PollRepository();
        $poll = $repo->find($id);
        $itemRepo = new PollItemRepository();
        $items = $itemRepo->findByPollId($id);
        $voteRepo = new UserPollItemRepository();
        $results = $voteRepo->countVotes($id);
        $this->render('poll/show', ['poll' => $poll, 'items' => $items, 'results' => $results]);
    }
    public function create() {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        $this->render('poll/create');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // TODO : enregistrer le sondage et ses items
            $pollRepo = new PollRepository();
            $poll = new Poll(
                null, 
                $_POST['title'], 
                $_POST['description'], 
                $_SESSION['user']->getId(), 
                $_POST['category_id']);

            $options = explode("\n", trim($_POST['options']));
            $savedPoll = $pollRepo->create($poll, $options);
        }
        header('Location: /poll/?id=' . $savedPoll->getId());
    }
  
    public function vote() {
        if(empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        $userPoolItemRepo = new UserPollItemRepository();
        echo "User ID: " . $_SESSION['user']->getId() . " Poll Item ID: " . $_GET['id'] . "\n";
        $userPoolItemRepo->removeVotesForUserAndPoll($_SESSION['user']->getId(), $_GET['id']);
        $userPoolItemRepo->addVote(new UserPollItem($_SESSION['user']->getId(), $_POST['option']));
        header('Location: /poll/?id=' . $_GET['id']);
        exit;

    }
}
