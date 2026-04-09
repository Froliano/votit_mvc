<?php
namespace App\Controller;
use App\Repository\PollRepository;

class PageController extends Controller {
    public function home() {
        // TODO : récupérer les derniers sondages (limit 3)
        $pollRepo = new PollRepository();
        $polls = $pollRepo->findAll(3);
        $this->render('page/home', [
            'polls' => $polls
        ]);
    }

    public function about() {
        $this->render('page/about');
    }

    public function legal() {
        $this->render('page/legal');
    }
}
