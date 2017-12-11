<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

/**
 * KBox\RequestSearchQueryActivity
 *
 * @deprecated
 * @property int $id
 * @property string $terms
 * @property string $visibility
 * @method static \Illuminate\Database\Query\Builder|\KBox\RequestSearchQueryActivity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RequestSearchQueryActivity whereTerms($value)
 * @method static \Illuminate\Database\Query\Builder|\KBox\RequestSearchQueryActivity whereVisibility($value)
 * @mixin \Eloquent
 */
class RequestSearchQueryActivity extends Model
{
    /*
    id: bigIncrements
    terms: string
    visibility: string
    */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'request_search_query_activities';

    public $timestamps = false;
}
