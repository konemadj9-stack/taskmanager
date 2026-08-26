<?php

class HomeController
{
    // Affiche la page d'accueil (vue : app/views/home/index.php)
    public function index(): void
    {
        require __DIR__ . '/../views/home/index.php';
    }
}
