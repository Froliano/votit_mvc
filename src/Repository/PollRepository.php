<?php

namespace App\Repository;

use App\Db\Mysql;
use App\Entity\Poll;
use PDO;
use App\Repository\CategoryRepository;

class PollRepository
{
    public function __construct() {}


    public function findAll(?int $limit = null): array
    {
        $sql = 'SELECT * FROM poll ORDER BY id DESC';
        if ($limit !== null) {
            $sql .= ' LIMIT ' . intval($limit);
        }

        $stmt = Mysql::getInstance()->getPdo()->query($sql);
        $polls = [];
        // Précharge toutes les catégories pour éviter les requêtes multiples
        $catRepo = new CategoryRepository();
        $categories = [];
        foreach ($catRepo->findAll() as $cat) {
            $categories[$cat->getId()] = $cat;
        }
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = $categories[$data['category_id']] ?? null;
            $poll = new Poll(
                $data['id'],
                $data['title'],
                $data['description'],
                $data['user_id'],
                $data['category_id'],
                $category
            );
            $polls[] = $poll;
        }
        return $polls;
    }

    public function find(int $id): ?Poll
    {
        $stmt = Mysql::getInstance()->getPdo()->prepare('SELECT * FROM poll WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) return null;
        $catRepo = new \App\Repository\CategoryRepository();
        $category = $catRepo->findById($data['category_id']);
        return new Poll(
            $data['id'],
            $data['title'],
            $data['description'],
            $data['user_id'],
            $data['category_id'],
            $category
        );
    }

    public function findByCategoryId(int $id): array
    {
        $stmt = Mysql::getInstance()->getPdo()->prepare('SELECT * FROM poll WHERE category_id = ?');
        $stmt->execute([$id]);
        $polls = [];
        $catRepo = new CategoryRepository();
        $category = $catRepo->findById($id);
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $poll = new Poll(
                $data['id'],
                $data['title'],
                $data['description'],
                $data['user_id'],
                $data['category_id'],
                $category
            );
            $polls[] = $poll;
        }
        return $polls;
    }
    
    public function create(Poll $poll, array $options): Poll
    {
        $stmt = Mysql::getInstance()->getPdo()->prepare('INSERT INTO poll (title, description, user_id, category_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $poll->getTitle(),
            $poll->getDescription(),
            $poll->getUserId(),
            $poll->getCategoryId()
        ]);

        $pollId = Mysql::getInstance()->getPdo()->lastInsertId();

        foreach ($options as $option) {
            $option = trim($option);
            if ($option === '') continue;
            $stmt = Mysql::getInstance()->getPdo()->prepare('INSERT INTO poll_item (name, poll_id) VALUES (?, ?)');
            $stmt->execute([
                $option,
                $pollId
            ]);
        }
        return new Poll(
            $pollId,
            $poll->getTitle(),
            $poll->getDescription(),
            $poll->getUserId(),
            $poll->getCategoryId(),
            $poll->getCategory()
        );
    }


}
