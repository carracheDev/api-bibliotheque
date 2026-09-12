<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'isbn', 'year'];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->whereNull('returned_at');
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->whereDoesntHave('loans', fn (Builder $loan) => $loan->whereNull('returned_at'));
    }

    public function scopeUnavailable(Builder $query): void
    {
        $query->whereHas('loans', fn (Builder $loan) => $loan->whereNull('returned_at'));
    }
}