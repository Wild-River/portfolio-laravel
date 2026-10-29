<?php

namespace App\Models;

use App\Enums\PublishStatus;
use Database\Factories\WorkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['category_id', 'title', 'description', 'demo_url', 'github_url', 'thumbnail_path', 'slug', 'status', 'published_at'])]
#[RouteKey('slug')]
class Work extends Model
{
    /** @use HasFactory<WorkFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
            'published_at' => 'date',
        ];
    }

    /**
     * Limit the query to published works.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', PublishStatus::Published);
    }

    /**
     * Get the public URL of the thumbnail.
     *
     * @return Attribute<string|null, never>
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->thumbnail_path ? Storage::disk('public')->url($this->thumbnail_path) : null,
        );
    }

    /**
     * Get the admin who wrote the work.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the work.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags of the work.
     *
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the technologies used in the work, in display order.
     *
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->ordered();
    }

    /**
     * Get the gallery images in display order.
     *
     * @return HasMany<WorkImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(WorkImage::class)->orderBy('sort_order');
    }
}
