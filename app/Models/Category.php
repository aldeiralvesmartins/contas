<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'color',
        'icon',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the accounts for the category.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get the transactions for the category.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope a query to only include income categories.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expense categories.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Get the display color for the category.
     * Retorna uma cor padrão se não estiver definida.
     */
    public function getDisplayColorAttribute(): string
    {
        return $this->color ?? $this->getDefaultColor();
    }

    /**
     * Get the display icon for the category.
     * Retorna um ícone padrão se não estiver definido.
     */
    public function getDisplayIconAttribute(): string
    {
        return $this->icon ?? $this->getDefaultIcon();
    }

    /**
     * Get default color based on category type.
     */
    protected function getDefaultColor(): string
    {
        return $this->type === 'income' ? '#10b981' : '#ef4444'; // Verde para entrada, vermelho para saída
    }

    /**
     * Get default icon based on category type.
     */
    protected function getDefaultIcon(): string
    {
        return $this->type === 'income' ? '💰' : '💸';
    }

    /**
     * Check if category can be deleted.
     * Não pode ser excluída se tiver transações ou contas associadas.
     */
    public function getCanBeDeletedAttribute(): bool
    {
        return $this->transactions_count == 0 && $this->accounts_count == 0;
    }

    /**
     * Get the total amount for transactions in this category.
     */
    public function getTransactionsTotalAttribute(): float
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * Get the total amount for accounts in this category.
     */
    public function getAccountsTotalAttribute(): float
    {
        return $this->accounts()->sum('amount');
    }

    /**
     * Get usage statistics for the category.
     */
    public function getUsageStatisticsAttribute(): array
    {
        return [
            'transactions_count' => $this->transactions_count ?? $this->transactions()->count(),
            'accounts_count' => $this->accounts_count ?? $this->accounts()->count(),
            'transactions_total' => $this->transactions_total,
            'accounts_total' => $this->accounts_total,
        ];
    }
}
