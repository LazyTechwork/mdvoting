<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Participant
 *
 * @property int $id
 * @property int $voting_id
 * @property string $name
 * @property string $group
 * @property string|null $vote
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Participant whereVotingId($value)
 * @mixin \Eloquent
 */
class Participant extends Model
{
    protected $guarded = [];
}
