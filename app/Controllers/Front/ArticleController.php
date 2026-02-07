<?php

namespace App\Controllers\Front;

class ArticleController
{
    public static function index()
    {
        return view('front/articles/index');
    }
}