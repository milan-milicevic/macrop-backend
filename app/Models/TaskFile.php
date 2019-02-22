<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model
{
    protected $table = 'task_files';

    protected $fillable = [
        'name',
        'file',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
