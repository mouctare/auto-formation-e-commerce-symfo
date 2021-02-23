<?php
namespace App\Classes;

use App\Entity\Category;

class Search
{

  public $string;

    /**
      *
      * @var Category[]
      */
    public $categories = [];
    public function __toString()
    {
      return '';
    }
}