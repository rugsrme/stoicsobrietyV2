<?php

namespace App\Http\Controllers;

use App\Enums\PostCategory;

/**
 * The private journal. Routes are admin-only (see routes/web.php).
 */
class JournalController extends PostSectionController
{
    protected function category(): PostCategory
    {
        return PostCategory::Journal;
    }
}
