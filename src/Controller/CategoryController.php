<?php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PollRepository;


class CategoryController extends Controller {

    public function list() {
        $categoryRepo = new CategoryRepository();
        $categories = $categoryRepo->findAll();
        $this->render('category/list', [
            'categories' => $categories
        ]);
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /');
            exit;
        }
        $repo = new PollRepository();
        $polls = $repo->findByCategoryId($id);
        $categoryRepo = new CategoryRepository();
        $category = $categoryRepo->findById($id);
        $this->render('category/show', ['polls' => $polls, 'category' => $category]);
    }
}