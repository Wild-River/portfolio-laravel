<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['language', 'bytes', 'fetched_at'])]
#[Table('github_language_stats', timestamps: false)]
class GitHubLanguageStat extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bytes' => 'integer',
            'fetched_at' => 'datetime',
        ];
    }
}
