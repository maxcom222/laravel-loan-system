<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('roles')->insert([
            [

                'slug' => 'admin',
                'name' => 'Admin',
                'permissions' => '{"borrowers":true,"borrowers.view":true,"borrowers.update":true,"borrowers.delete":true,"borrowers.create":true,"borrowers.approve":true,"borrowers.blacklist":true,"borrowers.groups":true,"loans":true,"loans.create":true,"loans.update":true,"loans.delete":true,"loans.view":true,"loans.products":true,"loans.fees":true,"loans.schedule":true,"loans.approve":true,"loans.disburse":true,"loans.withdraw":true,"loans.writeoff":true,"loans.reschedule":true,"loans.guarantor.create":true,"loans.guarantor.update":true,"loans.guarantor.delete":true,"loans.guarantor.savings":true,"loans.loan_calculator":true,"repayments":true,"repayments.view":true,"repayments.create":true,"repayments.delete":true,"repayments.update":true,"payroll":true,"payroll.view":true,"payroll.update":true,"payroll.delete":true,"payroll.create":true,"expenses":true,"expenses.view":true,"expenses.create":true,"expenses.update":true,"expenses.delete":true,"other_income":true,"other_income.view":true,"other_income.create":true,"other_income.update":true,"other_income.delete":true,"collateral":true,"collateral.view":true,"collateral.update":true,"collateral.create":true,"collateral.delete":true,"reports":true,"communication":true,"communication.create":true,"communication.delete":true,"custom_fields":true,"custom_fields.view":true,"custom_fields.create":true,"custom_fields.update":true,"custom_fields.delete":true,"users":true,"users.view":true,"users.create":true,"users.update":true,"users.delete":true,"users.roles":true,"settings":true,"audit_trail":true,"savings":true,"savings.create":true,"savings.update":true,"savings.delete":true,"savings.transactions.create":true,"savings.transactions.update":true,"savings.transactions.delete":true,"savings.view":true,"savings.transactions.view":true,"savings.products":true,"savings.fees":true,"dashboard":true,"dashboard.loans_released_monthly_graph":true,"dashboard.loans_collected_monthly_graph":true,"dashboard.registered_borrowers":true,"dashboard.total_loans_released":true,"dashboard.total_collections":true,"dashboard.loans_disbursed":true,"dashboard.loans_pending":true,"dashboard.loans_approved":true,"dashboard.loans_declined":true,"dashboard.loans_closed":true,"dashboard.loans_withdrawn":true,"dashboard.loans_written_off":true,"dashboard.loans_rescheduled":true,"capital":true,"capital.view":true,"capital.create":true,"capital.update":true,"capital.delete":true,"assets":true,"assets.create":true,"assets.view":true,"assets.update":true,"assets.delete":true,"branches":true,"branches.view":true,"branches.create":true,"branches.update":true,"branches.delete":true,"branches.assign":true,"stock":true,"stock.view":true,"stock.create":true,"stock.update":true,"stock.delete":true}'
            ]
        ]);
    }
}
