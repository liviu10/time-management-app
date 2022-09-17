<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Log;

class UserRoleType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_role_types';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     * 
     * @var string
     */
    protected $fillable = [
        'user_role_name',
        'user_role_description',
        'user_role_slug',
        'user_role_is_active',
    ];
    
    /**
    * The attributes that are mass assignable.
    * 
    * @var string
    */
    protected $attributes = [
        'user_role_is_active' => false,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Eloquent relationship between user_role_types and users.
     *
     */
    // TODO: Every time a new user register an account they should be automatically be 'Subscriber' and the 'Webmaster' can modify his role
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    /**
     * Eloquent polymorphic relationship between user_role_types and logs.
     *
     */
    public function log()
    {
        return $this->morphOne(Log::class, 'logable');
    }
}
