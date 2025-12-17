<?php 
namespace App\Controllers;

Class Dashboard extends BaseController
{
    public function dashboard(): string
    {
        return view('dashboard');
    }
}
