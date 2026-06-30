<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeOrder extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'type',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::created(function ($changeOrder) {
            if ($changeOrder->status === 'Approved') {
                $project = $changeOrder->project;
                if ($project->is_rab_auto_calculated) {
                    $project->recalculateFinalTotal();
                } else {
                    if ($changeOrder->type === 'Addition') {
                        $project->final_total += $changeOrder->amount;
                    } else {
                        $project->final_total -= $changeOrder->amount;
                    }
                    $project->save();
                }
            }
        });

        static::updated(function ($changeOrder) {
            $project = $changeOrder->project;
            if ($project->is_rab_auto_calculated) {
                if ($changeOrder->isDirty('status') || ($changeOrder->isDirty('amount') && $changeOrder->status === 'Approved')) {
                    $project->recalculateFinalTotal();
                }
            } else {
                if ($changeOrder->isDirty('status')) {
                    // If it changed TO Approved, add it
                    if ($changeOrder->status === 'Approved') {
                        if ($changeOrder->type === 'Addition') {
                            $project->final_total += $changeOrder->amount;
                        } else {
                            $project->final_total -= $changeOrder->amount;
                        }
                    } 
                    // If it changed FROM Approved to something else, revert it
                    elseif ($changeOrder->getOriginal('status') === 'Approved') {
                        if ($changeOrder->type === 'Addition') {
                            $project->final_total -= $changeOrder->amount;
                        } else {
                            $project->final_total += $changeOrder->amount;
                        }
                    }
                    $project->save();
                } elseif ($changeOrder->isDirty('amount') && $changeOrder->status === 'Approved') {
                    // If amount changed while it's already approved
                    $diff = $changeOrder->amount - $changeOrder->getOriginal('amount');
                    if ($changeOrder->type === 'Addition') {
                        $project->final_total += $diff;
                    } else {
                        $project->final_total -= $diff;
                    }
                    $project->save();
                }
            }
        });
        
        static::deleted(function ($changeOrder) {
            if ($changeOrder->status === 'Approved') {
                $project = $changeOrder->project;
                if ($project->is_rab_auto_calculated) {
                    $project->recalculateFinalTotal();
                } else {
                    if ($changeOrder->type === 'Addition') {
                        $project->final_total -= $changeOrder->amount;
                    } else {
                        $project->final_total += $changeOrder->amount;
                    }
                    $project->save();
                }
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
