<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'category_id',
        'created_by', 'keywords', 'views', 'helpful', 'not_helpful',
    ];

    protected $casts = [
        'views'      => 'integer',
        'helpful'    => 'integer',
        'not_helpful'=> 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(KbCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
