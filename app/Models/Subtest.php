<?php
namespace app\Models;

use app\Core\Database;

class Subtest
{
    public function getActiveSubtests()
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT * FROM subtests WHERE is_active = 1');
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM subtests WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}