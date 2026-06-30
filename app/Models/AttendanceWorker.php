<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceWorker extends Model
{
    protected $fillable = [
        'attendance_id',
        'worker_id',
        'status',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
