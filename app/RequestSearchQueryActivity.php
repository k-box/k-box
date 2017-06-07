<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

/**
 * KlinkDMS\RequestSearchQueryActivity
 *
 * @deprecated 
 * @property int $id
 * @property string $terms
 * @property string $visibility
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\RequestSearchQueryActivity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\RequestSearchQueryActivity whereTerms($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\RequestSearchQueryActivity whereVisibility($value)
 * @mixin \Eloquent
 */
class RequestSearchQueryActivity extends Model {
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
