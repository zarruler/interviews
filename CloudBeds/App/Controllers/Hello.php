<?php
namespace App\Controllers;

use Core\Controller;

class Hello extends Controller
{
    public function world()
    {
        $intervals = $this->getModel('Intervals');

        $data = $intervals->getAll();
        echo $this->twig->render('hello.twig', [
            'ranges' => $data,
        ]);

    }
}