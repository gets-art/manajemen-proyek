<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'client_id',
        'category_id',
        'start_date',
        'end_date',
        'status',
        'is_rab_auto_calculated',
        'contractor_fee_percentage',
        'final_total',
        'paid_total',
        'rest_total',
        'observation',
        'image',
        'note',
    ];

    protected static function booted()
    {
        static::saving(function ($project) {
            $project->rest_total = $project->final_total - $project->paid_total;
        });

        static::created(function ($project) {
            $project->paymentTerms()->createMany([
                ['name' => 'DP (Down Payment)', 'percentage' => 50, 'target_progress_percentage' => 0, 'status' => 'Pending'],
                ['name' => 'Termin 2', 'percentage' => 45, 'target_progress_percentage' => 45, 'status' => 'Pending'],
                ['name' => 'Pelunasan / Serah Terima', 'percentage' => 5, 'target_progress_percentage' => 100, 'status' => 'Pending'],
            ]);
        });
    }

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'is_rab_auto_calculated' => 'boolean',
            'contractor_fee_percentage' => 'decimal:2',
            'final_total' => 'decimal:2',
            'paid_total' => 'decimal:2',
            'rest_total' => 'decimal:2',
            'observation' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function projectBudgets(): HasMany
    {
        return $this->hasMany(ProjectBudget::class);
    }

    public function paymentTerms(): HasMany
    {
        return $this->hasMany(PaymentTerm::class);
    }

    public function changeOrders(): HasMany
    {
        return $this->hasMany(ChangeOrder::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function recalculateFinalTotal(): void
    {
        if ($this->is_rab_auto_calculated) {
            $budgetTotal = $this->projectBudgets()->sum('total_price') ?? 0;
            
            // Calculate Contractor Fee
            $feePercentage = $this->contractor_fee_percentage ?? 0;
            $contractorFee = $budgetTotal * ($feePercentage / 100);
            
            $baseTotal = $budgetTotal + $contractorFee;
            
            $addendumTotal = 0;
            $approvedChangeOrders = $this->changeOrders()->where('status', 'Approved')->get();
            foreach ($approvedChangeOrders as $co) {
                if ($co->type === 'Addition') {
                    $addendumTotal += $co->amount;
                } else {
                    $addendumTotal -= $co->amount;
                }
            }
            
            $this->final_total = $baseTotal + $addendumTotal;
            // Prevent recursive saving loops by using saveQuietly for the final_total change, 
            // but we might need to trigger the rest_total calculation which is in the saving hook.
            // Let's just update final_total and let the normal saving hook calculate rest_total.
            $this->save(); 
        }
    }
}
