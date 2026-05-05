<?php

namespace App\Models;

use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Filters\FinancialStatementFilters;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialStatement extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "financial_statement";

    protected string $default_filters = FinancialStatementFilters::class;

    protected $fillable = ["name", "last_updated_by"];
}
