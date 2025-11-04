<?php
namespace app\Controllers;

class HomeController
{
    public function index()
    {
        require __DIR__ . '/../Views/home.php';
    }
}