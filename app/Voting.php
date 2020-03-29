<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Voting
 *
 * @property int $id
 * @property int $admin
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property mixed $variants
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Participant[] $participants
 * @property-read int|null $participants_count
 * @property int $maxVotes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereMaxVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Voting whereVariants($value)
 */
class Voting extends Model
{
    protected $guarded = [];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'voting_id');
    }

    public function setVariantsAttribute(array $value)
    {
        $this->attributes['variants'] = json_encode($value);
    }

    public function getVariantsAttribute()
    {
        return json_decode($this->variants);
    }
}
