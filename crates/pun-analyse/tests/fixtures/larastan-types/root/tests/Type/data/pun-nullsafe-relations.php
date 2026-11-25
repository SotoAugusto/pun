<?php

namespace PunNullsafeRelations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function PHPStan\Testing\assertType;

class Field extends Model
{
}

class Variant extends Model
{
    /** @return HasMany<Field, $this> */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
}

class Sheet extends Model
{
    /** @return BelongsTo<Variant, $this> */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    /** @return Collection<int, Field> */
    public function sortedFields(): Collection
    {
        // the nullsafe link short-circuits the whole chain, so the left side of `??` is nullable
        return $this->variant?->fields->sortBy('position')->values() ?? new Collection();
    }
}

function test(?Variant $variant, Sheet $sheet): void
{
    assertType('int<0, max>|null', $variant?->fields->count());
    assertType('Illuminate\Database\Eloquent\Collection<int, PunNullsafeRelations\Field>|null', $sheet->variant?->fields->sortBy('position')->values());
}
