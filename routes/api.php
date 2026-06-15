<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AccountGroupController;
use App\Http\Controllers\Api\AccountSubGroupController;
use App\Http\Controllers\Api\AccountTitleController;
use App\Http\Controllers\Api\AccountTypeController;
use App\Http\Controllers\Api\AccountUnitController;
use App\Http\Controllers\Api\AllocationController;
use App\Http\Controllers\Api\AuditTrailController;
use App\Http\Controllers\Api\BarangayController;
use App\Http\Controllers\Api\BusinessUnitController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChargeController;
use App\Http\Controllers\Api\ChargeSyncController;
use App\Http\Controllers\Api\ChargingController;
use App\Http\Controllers\Api\CityMunicipalityController;
use App\Http\Controllers\Api\ColumnController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DepartmentUnitController;
use App\Http\Controllers\Api\FinancialStatementController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NormalBalanceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PasswordManagerController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\SubMunicipalityController;
use App\Http\Controllers\Api\SubUnitController;
use App\Http\Controllers\Api\SupplierBufferSeverityController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierReferenceController;
use App\Http\Controllers\Api\SupplierTypeController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\UomController;
use App\Http\Controllers\Api\UserSyncToSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// api outside
Route::middleware("api.key")->group(function () {
    Route::get("companies_api", [CompanyController::class, "companies"]);
    Route::get("business_unit_api", [
        BusinessUnitController::class,
        "business_unit",
    ]);
    Route::get("departments_api", [DepartmentController::class, "departments"]);
    Route::get("department_unit_api", [
        DepartmentUnitController::class,
        "department_unit",
    ]);
    Route::get("sub_unit_api", [SubUnitController::class, "sub_unit"]);
    Route::get("location_api", [LocationController::class, "location"]);
    Route::get("charging_api", [ChargingController::class, "charging_api"]);
    Route::get("account_title_external", [
        AccountTitleController::class,
        "account_title_api",
    ]);
});

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::post("check_password_other_system", [
        UserSyncToSystem::class,
        "testEncrypt",
    ]);
    //Masterlist
    Route::apiResource("user", AccountController::class);
    Route::apiResource("system", SystemController::class);
    Route::apiResource("notification", NotificationController::class);
    Route::apiResource("category", CategoryController::class);
    Route::apiResource("uom", UomController::class);
    Route::apiResource("item", ItemController::class);

    Route::apiResource("sample", UserSyncToSystem::class);
    Route::apiResource("audit", AuditTrailController::class);
    Route::apiResource("column", ColumnController::class);

    //Supplier
    Route::apiResource("supplier/type", SupplierTypeController::class);
    Route::apiResource(
        "supplier/buffer",
        SupplierBufferSeverityController::class
    );
    Route::apiResource(
        "supplier/reference",
        SupplierReferenceController::class
    );
    Route::apiResource("suppliers", SupplierController::class);

    //Charging of accounts masterlist
    Route::apiResource("companies", CompanyController::class);
    Route::apiResource("business_unit", BusinessUnitController::class);
    Route::apiResource("departments", DepartmentController::class);
    Route::apiResource("department_unit", DepartmentUnitController::class);
    Route::apiResource("sub_unit", SubUnitController::class);
    Route::apiResource("location", LocationController::class);
    Route::apiResource("charging", ChargingController::class);
    Route::apiResource("account_group", AccountGroupController::class);
    Route::apiResource("account_sub_group", AccountSubGroupController::class);
    Route::apiResource("account_type", AccountTypeController::class);
    Route::apiResource("account_unit", AccountUnitController::class);
    Route::apiResource("normal_balance", NormalBalanceController::class);
    Route::apiResource("sync_charging", ChargeSyncController::class);
    Route::apiResource("allocation", AllocationController::class);
    Route::apiResource("charge", ChargeController::class);
    Route::apiResource(
        "financial_statement",
        FinancialStatementController::class
    );
    Route::apiResource("credit", CreditController::class);
    Route::apiResource("account_title", AccountTitleController::class);
    Route::apiResource("customer", CustomerController::class);
    Route::apiResource("region", RegionController::class);
    Route::apiResource("province", ProvinceController::class);
    Route::apiResource("city_municipality", CityMunicipalityController::class);
    Route::apiResource("sub_municipality", SubMunicipalityController::class);
    Route::apiResource("barangay", BarangayController::class);

    Route::post("import/account_title", [
        AccountTitleController::class,
        "import",
    ]);

    Route::post("reset_all_password", [
        UserSyncToSystem::class,
        "resetAllPassword",
    ]);

    Route::post("change_all_password", [
        UserSyncToSystem::class,
        "changeAllPassword",
    ]);

    Route::post("existing_users_check", [
        AccountController::class,
        "importSync",
    ]);

    Route::post("login_all", [UserSyncToSystem::class, "systemLogin"]);

    Route::post("sync_arcana", [CustomerController::class, "sync_arcana"]);

    //password management

    Route::patch("change_password/{id}", [
        AccountController::class,
        "change_password",
    ]);

    Route::patch("reset_password/{id}", [
        AccountController::class,
        "reset_password",
    ]);
    //Store and get image
    Route::post("store_file", [SystemController::class, "store_file"]);
    Route::get("get_file/{id}", [SystemController::class, "get_file"]);

    Route::delete("delete_file/{id}", [SystemController::class, "delete_file"]);
    Route::post("logout", [AccountController::class, "logout"]);

    // Export
    Route::get("companies_export", [CompanyController::class, "export"]);
    Route::get("business_unit_export", [
        BusinessUnitController::class,
        "export",
    ]);

    // Import
    Route::post("companies_import", [CompanyController::class, "import"]);
    Route::post("business_unit/import", [
        BusinessUnitController::class,
        "import",
    ]);
    Route::post("department/import", [DepartmentController::class, "import"]);
    Route::post("department_unit/import", [
        DepartmentUnitController::class,
        "import",
    ]);
    Route::post("department_unit/import", [
        DepartmentUnitController::class,
        "import",
    ]);
    Route::post("sub_unit/import", [SubUnitController::class, "import"]);

    Route::post("location/import", [LocationController::class, "import"]);

    Route::post("charging/import", [ChargingController::class, "import"]);

    Route::get("sync_charge/{id}", [
        ChargeSyncController::class,
        "sync_charge",
    ]);

    Route::get("sync_c_sharp", [ChargeSyncController::class, "sync_c_sharp"]);
    Route::post("import/account_group", [
        AccountGroupController::class,
        "import",
    ]);
    Route::post("import/account_sub_group", [
        AccountSubGroupController::class,
        "import",
    ]);
    Route::post("import/account_type", [
        AccountTypeController::class,
        "import",
    ]);
    Route::post("import/account_unit", [
        AccountUnitController::class,
        "import",
    ]);
    Route::post("import/financial_statement", [
        FinancialStatementController::class,
        "import",
    ]);
    Route::post("import/normal_balance", [
        NormalBalanceController::class,
        "import",
    ]);
    Route::post("import/credit", [CreditController::class, "import"]);
    Route::post("import/allocation", [AllocationController::class, "import"]);
    Route::post("import/charge", [ChargeController::class, "import"]);

    Route::post("import/supplier_type", [
        SupplierTypeController::class,
        "import",
    ]);
    Route::post("import/supplier_reference", [
        SupplierReferenceController::class,
        "import",
    ]);
    Route::post("import/supplier_buffer", [
        SupplierBufferSeverityController::class,
        "import",
    ]);
    Route::post("import/supplier", [SupplierController::class, "import"]);
    Route::post("import/item", [ItemController::class, "importSync"]);
});

Route::post("login", [AccountController::class, "login"]);
