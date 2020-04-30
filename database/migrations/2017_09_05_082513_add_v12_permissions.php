<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddV12Permissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->truncate();
        $statement = "INSERT INTO `permissions` (`id`, `parent_id`, `name`, `slug`, `description`) VALUES
(1, 0, 'Borrowers', 'borrowers', 'Access Borrowers Module'),
(2, 1, 'View borrowers', 'borrowers.view', 'View borrowers'),
(3, 1, 'Update borrowers', 'borrowers.update', 'Update Borrowers'),
(4, 1, 'Delete borrowers', 'borrowers.delete', 'Delete borrowers'),
(5, 1, 'Create borrowers', 'borrowers.create', 'Add new borrower'),
(6, 0, 'Loans', 'loans', 'Access Loans Module'),
(7, 6, 'Create Loans', 'loans.create', 'Create Loans'),
(9, 6, 'Update Loans', 'loans.update', 'Update Loans'),
(10, 6, 'Delete Loans', 'loans.delete', 'Delete Loans'),
(11, 6, 'View Loans', 'loans.view', 'View Loans'),
(12, 6, 'Loan Products', 'loans.products', 'Manage Loan Products'),
(13, 6, 'Loan Fees', 'loans.fees', 'Manage Loan Fees'),
(14, 6, 'Loan Schedule', 'loans.schedule', 'Manage loan schedule, including sending loan schedule emails'),
(15, 0, 'Repayments', 'repayments', 'View Repayments Module'),
(16, 15, 'View Repayments', 'repayments.view', 'View All repayments'),
(17, 15, 'Create Repayments', 'repayments.create', 'Add Repayments'),
(18, 15, 'Delete Repayments', 'repayments.delete', 'Delete Repayments'),
(19, 15, 'Update Repayments', 'repayments.update', 'Update Repayments'),
(20, 0, 'Payroll', 'payroll', 'Access Payroll Module'),
(21, 20, 'View Payroll', 'payroll.view', 'View Payroll'),
(22, 20, 'Update Payroll', 'payroll.update', 'Update Payroll'),
(23, 20, 'Delete Payroll', 'payroll.delete', 'Delete Payroll'),
(24, 20, 'Create Payroll', 'payroll.create', 'Create Payroll'),
(25, 0, 'Expenses', 'expenses', 'Access Expenses Module'),
(26, 25, 'View Expenses', 'expenses.view', 'View Expenses'),
(27, 25, 'Create Expenses', 'expenses.create', 'Create Expenses'),
(28, 25, 'Update Expenses', 'expenses.update', 'Update Expenses'),
(29, 25, 'Delete Expenses', 'expenses.delete', 'Delete Expenses'),
(30, 0, 'Other Income', 'other_income', 'Access Other Income Module'),
(31, 30, 'View Other Income', 'other_income.view', 'View Other income'),
(32, 30, 'Create Other Income', 'other_income.create', 'Create other income'),
(33, 30, 'Update Other Income', 'other_income.update', 'Update Other Incom'),
(34, 30, 'Delete Other Income', 'other_income.delete', 'Delete other income'),
(35, 0, 'Collateral', 'collateral', 'Access Collateral Module'),
(36, 35, 'View collateral', 'collateral.view', 'View Collateral'),
(37, 35, 'Update Collateral', 'collateral.update', 'Update Collateral'),
(38, 35, 'Create Collateral', 'collateral.create', 'Create Collateral'),
(39, 35, 'Delete Collateral', 'collateral.delete', 'Delete Collateral'),
(40, 0, 'Reports', 'reports', 'Access Reports Module'),
(41, 0, 'Communication', 'communication', 'Access Communication Module'),
(42, 41, 'Create Communication', 'communication.create', 'Send Emails & SMS'),
(43, 41, 'Delete Communication', 'communication.delete', 'Delete Communication'),
(44, 0, 'Custom Fields', 'custom_fields', 'Access Custom Fields Module'),
(45, 44, 'View Custom Fields', 'custom_fields.view', 'View Custom fields'),
(46, 44, 'Create Custom Fields', 'custom_fields.create', 'Create Custom Fields'),
(47, 44, 'Custom Fields', 'custom_fields.update', 'Update Custom Fields'),
(48, 44, 'Delete Custom Fields', 'custom_fields.delete', 'Delete Custom Fields'),
(49, 0, 'Users', 'users', 'Access Users Module'),
(50, 49, 'View Users', 'users.view', 'View Users '),
(51, 49, 'Create Users', 'users.create', 'Create users'),
(52, 49, 'Update Users', 'users.update', 'Update Users'),
(53, 49, 'Delete Users', 'users.delete', 'Delete Users'),
(54, 49, 'Manage Roles', 'users.roles', 'Manage user roles'),
(55, 0, 'Settings', 'settings', 'Manage Settings'),
(56, 0, 'Audit Trail', 'audit_trail', 'Access Audit Trail'),
(57, 0, 'Savings', 'savings', 'Access Savings Menu'),
(58, 57, 'Create Savings', 'savings.create', ''),
(59, 57, 'Update Savings', 'savings.update', ''),
(60, 57, 'Delete Savings', 'savings.delete', ''),
(61, 57, 'Create Savings Transaction', 'savings.transactions.create', ''),
(62, 57, 'Update Savings Transaction', 'savings.transactions.update', ''),
(63, 57, 'Delete Savings Transaction', 'savings.transactions.delete', ''),
(64, 57, 'View Savings', 'savings.view', ''),
(65, 57, 'View Savings Transaction', 'savings.transactions.view', ''),
(66, 57, 'Manage Savings Products', 'savings.products', 'Manage Savings Products'),
(67, 57, 'Manage Savings Fees', 'savings.fees', ''),
(68, 6, 'Approve Loans', 'loans.approve', 'Approve Loans'),
(69, 6, 'Disburse Loans', 'loans.disburse', 'Disburse Loans'),
(70, 1, 'Approve Borrowers', 'borrowers.approve', 'Approve Borrowers'),
(71, 6, 'Withdraw Loans', 'loans.withdraw', 'Withdraw Loans'),
(72, 6, 'Write Off Loans', 'loans.writeoff', 'Write off Loans'),
(73, 6, 'Reschedule Loans', 'loans.reschedule', 'Reschedule Loans'),
(74, 0, 'Dashboard', 'dashboard', 'Access Dashboard'),
(75, 74, 'Loans Released Monthly Graph', 'dashboard.loans_released_monthly_graph', 'Access Loans Released Monthly Graph'),
(76, 74, 'Loans Collected Monthly Graph', 'dashboard.loans_collected_monthly_graph', 'Access Loans Collected Monthly Graph'),
(77, 74, 'Registered Borrowers', 'dashboard.registered_borrowers', 'Access Registered Borrowers Statistics'),
(78, 74, 'Total Loans Released', 'dashboard.total_loans_released', 'Access Total Loans Released'),
(79, 74, 'Total Collections', 'dashboard.total_collections', 'Access Total Collections Statistics'),
(80, 74, 'Total Disbursed Loans', 'dashboard.loans_disbursed', 'Access Total Disbursed Loans Statistics'),
(81, 74, 'Total Loans Pending', 'dashboard.loans_pending', ''),
(82, 74, 'Loans Approved', 'dashboard.loans_approved', ''),
(83, 74, 'Loans Declined', 'dashboard.loans_declined', ''),
(84, 74, 'Loans Closed', 'dashboard.loans_closed', ''),
(85, 74, 'Loans Withdrawn', 'dashboard.loans_withdrawn', ''),
(86, 74, 'Loans Written Off', 'dashboard.loans_written_off', ''),
(87, 74, 'Loans Rescheduled', 'dashboard.loans_rescheduled', ''),
(88, 6, 'Create Guarantor', 'loans.guarantor.create', ''),
(89, 6, 'Update Guarantor', 'loans.guarantor.update', ''),
(90, 6, 'Delete Guarantor', 'loans.guarantor.delete', ''),
(91, 6, 'Guarantor Savings', 'loans.guarantor.savings', ''),
(92, 0, 'Capital', 'capital', 'Access Capital'),
(93, 92, 'View  Capital', 'capital.view', ''),
(94, 92, 'Create Capital', 'capital.create', ''),
(95, 92, 'Update Capital', 'capital.update', ''),
(96, 92, 'Delete Capital', 'capital.delete', ''),
(97, 0, 'Assets', 'assets', 'Access Assets Menu'),
(98, 97, 'Create Assets', 'assets.create', ''),
(99, 97, 'View Assets', 'assets.view', ''),
(100, 97, 'Update Assets', 'assets.update', ''),
(101, 97, 'Delete Assets', 'assets.delete', ''),
(102, 1, 'Blacklist Borrower', 'borrowers.blacklist', 'Blacklist borrower'),
(103, 1, 'Manage Borrower Groups', 'borrowers.groups', ''),
(104, 6, 'Use Loan Calculator', 'loans.loan_calculator', '');";
        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
