<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['description', 'amount', 'category_id', 'type','status', 'due_date', 'paid_at'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope para transações de entrada
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope para transações de saída
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Get the original transaction that was duplicated (if any)
     */
    public function originalTransaction()
    {
        return $this->belongsTo(Transaction::class, 'duplicated_from_id');
    }

    /**
     * Get duplicated transactions from this one
     */
    public function duplicatedTransactions()
    {
        return $this->hasMany(Transaction::class, 'duplicated_from_id');
    }

    /**
     * Check if this transaction is a duplicate
     */
    public function getIsDuplicateAttribute()
    {
        return !is_null($this->duplicated_from_id);
    }
}
