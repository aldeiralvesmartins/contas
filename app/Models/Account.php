<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['title', 'amount', 'due_date', 'category_id', 'status'];

    /**
     * Verifica se a conta está vencida.
     */
    public function isOverdue()
    {
        return Carbon::today()->gt(Carbon::parse($this->due_date)) && $this->status !== 'paid';
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope para contas pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para contas vencidas
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->whereDate('due_date', '<', Carbon::today());
    }

    /**
     * Scope para contas de entrada (recebíveis)
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope para contas de saída (pagáveis)
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Get the original account that was duplicated (if any)
     */
    public function originalAccount()
    {
        return $this->belongsTo(Account::class, 'duplicated_from_id');
    }

    /**
     * Get duplicated accounts from this one
     */
    public function duplicatedAccounts()
    {
        return $this->hasMany(Account::class, 'duplicated_from_id');
    }

    /**
     * Check if this account is a duplicate
     */
    public function getIsDuplicateAttribute()
    {
        return !is_null($this->duplicated_from_id);
    }

    /**
     * Get display type
     */
    public function getDisplayTypeAttribute()
    {
        return $this->type === 'income' ? 'Receber' : 'Pagar';
    }

    /**
     * Get display status color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'paid' => 'emerald',
            'pending' => $this->isOverdue() ? 'rose' : 'amber',
            default => 'slate'
        };
    }
}
