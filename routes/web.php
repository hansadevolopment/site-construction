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

use App\Http\Controllers\SiteMM\Master\SiteController;
use App\Http\Controllers\SiteMM\Master\ItemController;
use App\Http\Controllers\SiteMM\Master\LabourCategoryController;
use App\Http\Controllers\SiteMM\Master\EmployeeController;
use App\Http\Controllers\SiteMM\Master\OverheadCostController;
use App\Http\Controllers\SiteMM\Master\UnitController;
use App\Http\Controllers\SiteMM\Master\CostSectionController;

use App\Http\Controllers\SiteMM\SiteForcast\SiteTaskController;
use App\Http\Controllers\SiteMM\SiteForcast\SiteSubTaskController;
use App\Http\Controllers\SiteMM\SiteForcast\SiteMaterialsController;
use App\Http\Controllers\SiteMM\SiteForcast\SiteLabourController;
use App\Http\Controllers\SiteMM\SiteForcast\SiteOverheadCostController;
use App\Http\Controllers\SiteMM\SiteForcast\SiteProfitController;

use App\Http\Controllers\SiteMM\SiteOperation\ItemIssueNoteController;
use App\Http\Controllers\SiteMM\SiteOperation\PaymentVoucherController;
use App\Http\Controllers\SiteMM\SiteOperation\EmployeeAdvanceController;
use App\Http\Controllers\SiteMM\SiteOperation\EmployeeSalaryController;
use App\Http\Controllers\SiteMM\SiteOperation\EmployeeSalaryTwoController;
use App\Http\Controllers\SiteMM\SiteOperation\EmployeeAttendanceOverTimeController;
use App\Http\Controllers\SiteMM\SiteOperation\DailyProgressReportController;

use App\Http\Controllers\SiteMM\InquiryList\MasterInquiryController;
use App\Http\Controllers\SiteMM\InquiryList\SiteTaskSubTaskController;
use App\Http\Controllers\SiteMM\InquiryList\SapInquiryController;
use App\Http\Controllers\SiteMM\InquiryList\SoInquiryController;

use App\Http\Controllers\SiteMM\Report\SapReportController;
use App\Http\Controllers\SiteMM\Report\SiteOperationReportController;

use App\Http\Controllers\GL\Primary\BankController;
use App\Http\Controllers\GL\Primary\TaxController;
use App\Http\Controllers\GL\Primary\MainAccountController;
use App\Http\Controllers\GL\Primary\ControllAccountController;
use App\Http\Controllers\GL\Primary\SubAccountController;

use App\Http\Controllers\GL\Transaction\JournalEntryController;
use App\Http\Controllers\GL\Transaction\PettyCashController;

use App\Http\Controllers\GL\ListInquiry\PrimaryListInquireController;
use App\Http\Controllers\GL\ListInquiry\TransactionListInquireController;

use App\Http\Controllers\GL\Report\TrialBalanceController;
use App\Http\Controllers\GL\Report\LedgerController;
use App\Http\Controllers\GL\Report\ProfitLostReportController;
use App\Http\Controllers\GL\Report\BalanceSheetController;


Route::get('/', function () {

    return view('welcome');
});

Route::get('/dashboard', function () {

    return view('SiteMM.site_dashboard');
})->middleware(['auth'])->name('dashboard');


require __DIR__.'/auth.php';

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Site Monitoring Module Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/

/*------------------------------------------------------------- Site Master -------------------------------------------------------------*/

Route::get('/site', [SiteController::class, 'loadView'])->name('site');
Route::post('/site_process', [SiteController::class, 'processSite'])->name('site_process');
Route::get('/get_site_wise_task', [SiteController::class, 'getSiteWiseTask'])->name('get_site_wise_task');
Route::post('/open_site', [SiteController::class, 'openSite'])->name('open_site');
Route::get('/site_excel', [SiteController::class, 'getExcel'])->name('site_excel');

Route::get('/item', [ItemController::class, 'loadView'])->name('item');
Route::post('/item_process', [ItemController::class, 'processItem'])->name('item_process');
Route::get('/get_item_for_sap_material', [ItemController::class, 'getItemForSapMaterial'])->name('get_item_for_sap_material');
Route::post('/open_item', [ItemController::class, 'openItem'])->name('open_item');

Route::get('/labour_category', [LabourCategoryController::class, 'loadView'])->name('labour_category');
Route::post('/labour_category_process', [LabourCategoryController::class, 'procesLlabourCategory'])->name('labour_category_process');
Route::get('/get_labour_category_for_sap_labour', [LabourCategoryController::class, 'getLabourCategoryForSapLabour'])->name('get_labour_category_for_sap_labour');
Route::post('/open_labour_category', [LabourCategoryController::class, 'openLabourCategory'])->name('open_labour_category');

Route::get('/employee', [EmployeeController::class, 'loadView'])->name('employee');
Route::post('/employee_process', [EmployeeController::class, 'processEmployee'])->name('employee_process');
Route::get('/get_employee', [EmployeeController::class, 'getEmployee'])->name('get_employee');
Route::post('/open_employee', [EmployeeController::class, 'openEmployee'])->name('open_employee');

Route::get('/overhead_cost', [OverheadCostController::class, 'loadView'])->name('overhead_cost');
Route::post('/overhead_cost_process', [OverheadCostController::class, 'processOverheadCost'])->name('overhead_cost_process');
Route::post('/open_overhead', [OverheadCostController::class, 'openOverhead'])->name('open_overhead');

Route::get('/unit', [UnitController::class, 'loadView'])->name('unit');
Route::post('/unit_process', [UnitController::class, 'processUnit'])->name('unit_process');
Route::post('/open_unit', [UnitController::class, 'openUnit'])->name('open_unit');

Route::get('/cost_section_item', [CostSectionController::class, 'getCostSectionItem'])->name('cost_section_item');


/*------------------------------------------------------------- Site Forcasting -------------------------------------------------------------*/

Route::get('/site_task', [SiteTaskController::class, 'loadView'])->name('site_task');
Route::post('/site_task_process', [SiteTaskController::class, 'processSiteTask'])->name('site_task_process');
Route::get('/get_site_wise_sub_task', [SiteTaskController::class, 'getTaskWiseSubTask'])->name('get_site_wise_sub_task');
Route::post('/open_task', [SiteTaskController::class, 'openTask'])->name('open_task');

Route::get('/site_sub_task', [SiteSubTaskController::class, 'loadView'])->name('site_sub_task');
Route::post('/site_sub_task_process', [SiteSubTaskController::class, 'processSiteSubTask'])->name('site_sub_task_process');
Route::post('/open_sub_task', [SiteSubTaskController::class, 'openSubTask'])->name('open_sub_task');

Route::get('/sap_material', [SiteMaterialsController::class, 'loadView'])->name('sap_material');
Route::post('/sap_material_add_process', [SiteMaterialsController::class, 'addSapMaterial'])->name('sap_material_add_process');
Route::post('/open_sap_material', [SiteMaterialsController::class, 'openSapMaterial'])->name('open_sap_material');

Route::get('/sap_labour', [SiteLabourController::class, 'loadView'])->name('sap_labour');
Route::post('/sap_labour_add_process', [SiteLabourController::class, 'addSapLabour'])->name('sap_labour_add_process');
Route::post('/open_sap_labour', [SiteLabourController::class, 'openSapLabour'])->name('open_sap_labour');

Route::get('/sap_overhead', [SiteOverheadCostController::class, 'loadView'])->name('sap_overhead');
Route::post('/sap_overhead_add_process', [SiteOverheadCostController::class, 'addSapOverheadcost'])->name('sap_overhead_add_process');
Route::post('/open_sap_overhead', [SiteOverheadCostController::class, 'openSapOverhead'])->name('open_sap_overhead');

Route::get('/sap_profit', [SiteProfitController::class, 'loadView'])->name('sap_profit');
Route::post('/sap_profit_add_process', [SiteProfitController::class, 'addSapProfit'])->name('sap_profit_add_process');
Route::get('/sap_profit_total', [SiteProfitController::class, 'getSiteWiseTotalCost'])->name('sap_profit_total');
Route::post('/open_sap_profit', [SiteProfitController::class, 'openSapProfit'])->name('open_sap_profit');

/*------------------------------------------------------------- Site Operation -------------------------------------------------------------*/

Route::get('/item_issue_note', [ItemIssueNoteController::class, 'loadView'])->name('item_issue_note');
Route::post('/item_issue_note_process', [ItemIssueNoteController::class, 'processItemIssueNote'])->name('item_issue_note_process');
Route::post('/open_item_issue_note', [ItemIssueNoteController::class, 'openItemIssueNote'])->name('open_item_issue_note');

Route::get('/payment_voucher', [PaymentVoucherController::class, 'loadView'])->name('payment_voucher');
Route::post('/payment_voucher_process', [PaymentVoucherController::class, 'processPaymentVoucher'])->name('payment_voucher_process');
Route::post('/open_payment_voucher', [PaymentVoucherController::class, 'openPaymentVoucher'])->name('open_payment_voucher');

Route::get('/employee_advance', [EmployeeAdvanceController::class, 'loadView'])->name('employee_advance');
Route::post('/employee_advance_process', [EmployeeAdvanceController::class, 'processEmployeeAdvance'])->name('employee_advance_process');
Route::post('/open_employee_advance', [EmployeeAdvanceController::class, 'openEmployeeAdvance'])->name('open_employee_advance');
Route::get('/get_employee_advance', [EmployeeAdvanceController::class, 'getEmployeeAdvance'])->name('get_employee_advance');

Route::get('/employee_attendance', [EmployeeAttendanceOverTimeController::class, 'loadView'])->name('employee_attendance');
Route::post('/employee_attendance_process', [EmployeeAttendanceOverTimeController::class, 'processEmployeeAttendanceOvertime'])->name('employee_attendance_process');

Route::get('/employee_salary', [EmployeeSalaryController::class, 'loadView'])->name('employee_salary');
Route::post('/employee_salary_process', [EmployeeSalaryController::class, 'processEmployeeSalary'])->name('employee_salary_process');
Route::post('/open_employee_salary', [EmployeeSalaryController::class, 'openEmployeeSalary'])->name('open_employee_salary');

Route::get('/employee_salary_two', [EmployeeSalaryTwoController::class, 'loadView'])->name('employee_salary_two');
Route::post('/employee_salary_two_process', [EmployeeSalaryTwoController::class, 'processEmployeeSalary'])->name('employee_salary_two_process');

Route::get('/dpr', [DailyProgressReportController::class, 'loadView'])->name('dpr');
Route::post('/dpr_process', [DailyProgressReportController::class, 'processDPR'])->name('dpr_process');
Route::post('/open_dpr', [DailyProgressReportController::class, 'openDPR'])->name('open_dpr');

/*------------------------------------------------------------- Inquiry & List -------------------------------------------------------------*/

Route::get('/master_inquire', [MasterInquiryController::class, 'loadView'])->name('master_inquire');
Route::post('/master_inquire_process', [MasterInquiryController::class, 'inquireMaster'])->name('master_inquire_process');

Route::get('/site_task_subtask_inquiry', [SiteTaskSubTaskController::class, 'loadView'])->name('site_task_subtask_inquiry');
Route::post('/site_task_subtask_inquiry_process', [SiteTaskSubTaskController::class, 'processSiteTaskSubTaskInquire'])->name('site_task_subtask_inquiry_process');

Route::get('/sap_inquire', [SapInquiryController::class, 'loadView'])->name('sap_inquire');
Route::post('/sap_inquire_process', [SapInquiryController::class, 'inquireSiteActionPlan'])->name('sap_inquire_process');

Route::get('/so_inquire', [SoInquiryController::class, 'loadView'])->name('so_inquire');
Route::post('/so_inquire_process', [SoInquiryController::class, 'getInquireSiteOperationResults'])->name('so_inquire_process');

/*------------------------------------------------------------- Report -------------------------------------------------------------*/

Route::get('/sap_report', [SapReportController::class, 'loadView'])->name('sap_report');
Route::post('/sap_report_process', [SapReportController::class, 'sapReport'])->name('sap_report_process');

Route::get('/so_summary_report', [SiteOperationReportController::class, 'loadView'])->name('so_summary_report');
Route::post('/so_summary_report_process', [SiteOperationReportController::class, 'soReport'])->name('so_summary_report_process');

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| Genaral Ledger Module Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/

/*------------------------------------------------------------- Primary -------------------------------------------------------------*/

Route::get('/main_account', [MainAccountController::class, 'loadView'])->name('main_account');
Route::post('/main_account_process', [MainAccountController::class, 'saveMainAccount'])->name('main_account_process');
Route::get('/open_main_account', [MainAccountController::class, 'openMainAccount'])->name('open_main_account');

Route::get('/controll_account', [ControllAccountController::class, 'loadView'])->name('controll_account');
Route::post('/controll_account_process', [ControllAccountController::class, 'saveControllAccount'])->name('controll_account_process');
Route::get('/open_controll_account', [ControllAccountController::class, 'openControllAccount'])->name('open_controll_account');

Route::get('/sub_account', [SubAccountController::class, 'loadView'])->name('sub_account');
Route::post('/sub_account_process', [SubAccountController::class, 'saveSubAccount'])->name('sub_account_process');
Route::get('/open_sub_account', [SubAccountController::class, 'openSubAccount'])->name('open_sub_account');

Route::get('/tax', [TaxController::class, 'loadView'])->name('tax');
Route::post('/tax_process', [TaxController::class, 'saveTax'])->name('tax_process');
Route::get('/open_tax', [TaxController::class, 'openTax'])->name('open_tax');

Route::get('/bank', [BankController::class, 'loadView'])->name('bank');
Route::post('/bank_process', [BankController::class, 'saveBank'])->name('bank_process');
Route::get('/open_bank', [BankController::class, 'openBank'])->name('open_bank');

/*------------------------------------------------------------- Transaction -------------------------------------------------------------*/

Route::get('/journal_entry', [JournalEntryController::class, 'loadView'])->name('journal_entry');
Route::post('/journal_entry_process', [JournalEntryController::class, 'saveJournalEntry'])->name('journal_entry_process');
Route::get('/open_journal_entry', [JournalEntryController::class, 'openJournalEntry'])->name('open_journal_entry');

Route::get('/pettycash', [PettyCashController::class, 'loadView'])->name('pettycash');
Route::post('/pettycash_process', [PettyCashController::class, 'savePettyCash'])->name('pettycash_process');
Route::get('/open_pettycash', [PettyCashController::class, 'openPettyCash'])->name('open_pettycash');

/*------------------------------------------------------------- Inquiry & List -------------------------------------------------------------*/

Route::get('/primary_inquire', [PrimaryListInquireController::class, 'loadView'])->name('primary_inquire');
Route::post('/primary_inquire_process', [PrimaryListInquireController::class, 'processPrimaryListInquire'])->name('primary_inquire_process');

Route::get('/transaction_inquire', [TransactionListInquireController::class, 'loadView'])->name('transaction_inquire');
Route::post('/transaction_inquire_process', [TransactionListInquireController::class, 'processTransactionListInquire'])->name('transaction_inquire_process');

/*------------------------------------------------------------- Report -------------------------------------------------------------*/

Route::get('/trial_balance', [TrialBalanceController::class, 'loadView'])->name('trial_balance');
Route::post('/trial_balance_process', [TrialBalanceController::class, 'generateTrialBalance'])->name('generate_trial_balance');

Route::get('/ledger', [LedgerController::class, 'loadView'])->name('ledger');
Route::post('/ledger_process', [LedgerController::class, 'generateLedger'])->name('generate_ledger');

Route::get('/profit_loss_account', [ProfitLostReportController::class, 'loadView'])->name('profit_loss_account');
Route::post('/profit_loss_account_process', [ProfitLostReportController::class, 'generateProfitLossAccount'])->name('generate_profit_loss_account');

Route::get('/balance_sheet', [BalanceSheetController::class, 'loadView'])->name('balance_sheet');
Route::post('/balance_sheet_process', [BalanceSheetController::class, 'generateBalanceSheet'])->name('generate_balance_sheet');
