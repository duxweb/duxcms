<?php
declare(strict_types=1);

namespace Dux\Database;

use Dux\App;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

class Model extends \Illuminate\Database\Eloquent\Model
{

    public function __construct(array $attributes = [])
    {
        App::db()->getConnection();
        $this->setConnection('default');
        parent::__construct($attributes);
    }

    protected $fillable = [];
    protected $guarded = [];

    public function migration(Blueprint $table)
    {
    }

    public function migrationAfter(Connection $db)
    {
    }

    public function seed(Connection $db)
    {
    }

    public function migrationGlobal(Blueprint $table)
    {
        $event = new DatabaseEvent();
        App::event()->dispatch($event, 'model.' . static::class);
        $event->run('migration', $table);
    }

    protected static function boot()
    {
        parent::boot();

        $event = new DatabaseEvent();
        App::event()->dispatch($event, 'model.' . static::class);

        static::retrieved(function ($model) use ($event) {
            $event->run('retrieved', $model);
        });

        static::saving(function($model) use ($event) {
            $event->run( 'saving', $model);
        });

        static::saved(function($model) use ($event) {
            $event->run( 'saved', $model);
        });

        static::updating(function($model) use ($event) {
            $event->run( 'updating', $model);
        });

        static::updated(function($model) use ($event) {
            $event->run( 'updated', $model);
        });

        static::creating(function ($model) use ($event) {
            $event->run('creating', $model);
        });

        static::created(function ($model) use ($event) {
            $event->run( 'created', $model);
        });

        static::replicating(function($model) use ($event) {
            $event->run( 'replicating', $model);
        });

        static::deleting(function($model) use ($event) {
            $event->run( 'deleting', $model);
        });

        static::deleted(function($model) use ($event) {
            $event->run( 'deleted', $model);
        });

    }

    protected function serializeDate($date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}