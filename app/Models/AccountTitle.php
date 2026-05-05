<?php

namespace App\Models;

use App\Filters\AccountTitleFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountTitle extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "account_title";

    protected string $default_filters = AccountTitleFilters::class;

    protected $fillable = [
        "code",
        "name",
        "account_group_id",
        "account_sub_group_id",
        "account_unit_id",
        "account_type_id",
        "financial_statement_id",
        "normal_balance_id",
        "credit_id",
        "allocation_id",
        "charge_id",
        "purchase_book",
        "vouchers_book",
        "cash_disbursement_book",
        "sales_journal_book",
        "cash_receipt_book",
        "last_updated_by",
    ];

    protected $hidden = [
        "account_group_id",
        "account_sub_group_id",
        "account_unit_id",
        "account_type_id",
        "financial_statement_id",
        "normal_balance_id",
        "credit_id",
        "allocation_id",
    ];

    public function account_group()
    {
        return $this->belongsTo(AccountGroup::class);
    }
    public function account_sub_group()
    {
        return $this->belongsTo(AccountSubGroup::class);
    }
    public function account_unit()
    {
        return $this->belongsTo(AccountUnit::class);
    }
    public function account_type()
    {
        return $this->belongsTo(AccountType::class);
    }
    public function financial_statement()
    {
        return $this->belongsTo(FinancialStatement::class);
    }
    public function normal_balance()
    {
        return $this->belongsTo(NormalBalance::class);
    }
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }
    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }
    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}
