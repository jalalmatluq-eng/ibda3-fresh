<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbCategory extends Model
{
    use HasFactory;

    protected $table = 'kb_categories';
    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function articles()
    {
        return $this->hasMany(KnowledgeArticle::class, 'category_id');
    }
}
