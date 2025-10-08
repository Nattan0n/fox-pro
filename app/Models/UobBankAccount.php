<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UobBankAccount extends Model
{
    protected $table = 'uob_bank_accounts';
    
    protected $fillable = [
        'org_id',
        'org_name',
        'bank_code',
        'bank_name',
        'branch_code',
        'account_number',
        'account_name',
        'account_type',
        'is_active',
        'is_default',
        'remark'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get default account for organization
     * 
     * @param string $orgId
     * @return UobBankAccount|null
     */
    public static function getDefaultAccount($orgId)
    {
        return self::where('org_id', $orgId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Get all active accounts for organization
     * 
     * @param string $orgId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOrgAccounts($orgId)
    {
        return self::where('org_id', $orgId)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Get account by ID with validation
     * 
     * @param int $accountId
     * @return UobBankAccount|null
     */
    public static function getAccountById($accountId)
    {
        return self::where('id', $accountId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Format account for display
     * 
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->org_name . ' (' . $this->account_number . ')';
    }

    /**
     * Check if this is default account
     * 
     * @return bool
     */
    public function isDefault()
    {
        return $this->is_default === true;
    }
}