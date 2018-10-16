<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = array ('id', 'postid', 'title', 'published_at', 'content', 'slug');

    protected $table = 'blog';
 
}


// ALTER TABLE `blog` ADD `postid` INT(11) NULL DEFAULT NULL AFTER `meta_description`;
