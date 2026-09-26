<?php

namespace App\Http\Controllers;

use App\Enums\PostCategory;

class ReflectionController extends PostSectionController
{
    protected function category(): PostCategory
    {
        return PostCategory::Reflection;
    }
}
