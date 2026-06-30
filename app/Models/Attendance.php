<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'mandor_id',
        'date',
        'image',
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mandor()
    {
        return $this->belongsTo(Worker::class, 'mandor_id');
    }

    public function attendanceWorkers()
    {
        return $this->hasMany(AttendanceWorker::class);
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'attendance_workers')
            ->withPivot('status')
            ->withTimestamps();
    }
}
