<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AdminstrativeController;

/*
|--------------------------------------------------------------------------
| Genaral Ledger Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\GenaralLedger\Transaction\JournalEntryController;

/*
|--------------------------------------------------------------------------
| Inventory Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Inventory\Primary\ItemMasterController;
use App\Http\Controllers\Inventory\Primary\ManufactureLocationController;
use App\Http\Controllers\Inventory\Primary\BrandController;
use App\Http\Controllers\Inventory\Primary\UnitController;

use App\Http\Controllers\Inventory\Transaction\ItemRequestNoteController;
use App\Http\Controllers\Inventory\Transaction\ItemIssueNoteController;
use App\Http\Controllers\Inventory\Transaction\ProductionNoteController;
use App\Http\Controllers\Inventory\Transaction\StockAdjustmentNoteController;

/*
|--------------------------------------------------------------------------
| Sales Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Sales\Primary\DebtorController;
use App\Http\Controllers\Sales\Primary\SalesCategoryController;
use App\Http\Controllers\Sales\Primary\SalesLocationController;
use App\Http\Controllers\Sales\Primary\SalesRepController;

/*
|--------------------------------------------------------------------------
| Purchase Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Purchase\Primary\CreditorController;
use App\Http\Controllers\Purchase\Primary\PurchasingCategoryController;
use App\Http\Controllers\Purchase\Primary\PurchasingLocationController;

use App\Http\Controllers\Purchase\Transaction\GoodReceiveNoteController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/genaral_ledger', [AdminstrativeController::class, 'genaralLedger'])->name('genaral_ledger');
Route::get('/sales_dashboard', [AdminstrativeController::class, 'getSalesDashboard'])->name('sales_dashboard');
Route::get('/purchasing_dashboard', [AdminstrativeController::class, 'getPurchasingDashboard'])->name('purchasing_dashboard');
Route::get('/inventory_dashboard', [AdminstrativeController::class, 'getInventoryDashboard'])->name('inventory_dashboard');

require __DIR__.'/auth.php';

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Sales Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/debtor', [DebtorController::class, 'loadView'])->name('debtor');
Route::post('/debtor_process', [DebtorController::class, 'debtorProcess'])->name('debtor_process');

Route::get('/sales_category', [SalesCategoryController::class, 'loadView'])->name('sales_category');
Route::post('/sales_category_process', [SalesCategoryController::class, 'salesCategoryProcess'])->name('sales_category_process');

Route::get('/sales_location', [SalesLocationController::class, 'loadView'])->name('sales_location');
Route::post('/sales_location_process', [SalesLocationController::class, 'salesLocationProcess'])->name('sales_location_process');

Route::get('/sales_rep', [SalesRepController::class, 'loadView'])->name('sales_rep');
Route::post('/sales_rep_process', [SalesRepController::class, 'salesRepProcess'])->name('sales_rep_process');



/*
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Purchase Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/creditor', [CreditorController::class, 'loadView'])->name('creditor');
Route::post('/creditor_process', [CreditorController::class, 'creditorProcess'])->name('creditor_process');

Route::get('/purchase_category', [PurchasingCategoryController::class, 'loadView'])->name('purchase_category');
Route::post('/purchase_category_process', [PurchasingCategoryController::class, 'purchasingCategoryProcess'])->name('purchase_category_process');

Route::get('/purchase_location', [PurchasingLocationController::class, 'loadView'])->name('purchase_location');
Route::post('/purchase_location_process', [PurchasingLocationController::class, 'purchasingLocationProcess'])->name('purchase_location_process');

Route::get('/grn', [GoodReceiveNoteController::class, 'loadView'])->name('grn');
Route::post('/grn_process', [GoodReceiveNoteController::class, 'grnProcess'])->name('grn_process');


/*
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Genaral Ledger Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/journal_entry', [JournalEntryController::class, 'getJournalEntry'])->name('journal_entry');
Route::post('/journal_entry_process', [JournalEntryController::class, 'journalEntryProcess'])->name('journal_entry_process');


/*
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Inventory Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/item_master', [ItemMasterController::class, 'loadView'])->name('item_master');
Route::post('/item_master_process', [ItemMasterController::class, 'itemMasterProcess'])->name('item_master_process');

Route::get('/manufacture_location', [ManufactureLocationController::class, 'loadView'])->name('manufacture_location');
Route::post('/manufacture_location_process', [ManufactureLocationController::class, 'manufactureLocationProcess'])->name('manufacture_location_process');

Route::get('/brand', [BrandController::class, 'loadView'])->name('brand');
Route::post('/brand_process', [BrandController::class, 'brandProcess'])->name('brand_process');

Route::get('/unit', [UnitController::class, 'loadView'])->name('unit');
Route::post('/unit_process', [UnitController::class, 'unitProcess'])->name('unit_process');

Route::get('/item_request_note/{referance}', [ItemRequestNoteController::class, 'loadView'])->name('item_request_note');
Route::post('/item_request_note_process', [ItemRequestNoteController::class, 'itemRequestNoteProcess'])->name('item_request_note_process');

Route::get('/item_issue_note', [ItemIssueNoteController::class, 'loadView'])->name('item_issue_note');
Route::post('/item_issue_note_process', [ItemIssueNoteController::class, 'itemIssueNoteProcess'])->name('item_issue_note_process');

Route::get('/production_note', [ProductionNoteController::class, 'loadView'])->name('production_note');
Route::post('/production_note_process', [ProductionNoteController::class, 'productionNoteProcess'])->name('production_note_process');

Route::get('/stock_adjustment_note', [StockAdjustmentNoteController::class, 'loadView'])->name('stock_adjustment_note');
Route::post('/stock_adjustment_note_process', [StockAdjustmentNoteController::class, 'stockAdjustmentNoteProcess'])->name('stock_adjustment_note_process');