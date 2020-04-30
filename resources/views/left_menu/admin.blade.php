<div class="sidebar sidebar-main sidebar-default">
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user-material">
            <div class="category-content">
                <div class="sidebar-user-material-content">
                    <a href="#"><img src="{{ asset('assets/themes/limitless/images/user.png') }}"
                                     class="img-circle img-responsive" alt=""></a>
                    <h6>{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</h6>
                    <span class="text-size-small"></span>
                </div>

                <div class="sidebar-user-material-menu">
                    <a href="#user-nav" data-toggle="collapse"><span>Mi Cuenta personal</span> <i class="caret"></i></a>
                </div>
            </div>

            <div class="navigation-wrapper collapse" id="user-nav">
                <ul class="navigation">
                    <li><a href="{{ url('user/profile') }}"><i class="icon-user-plus"></i>
                            <span>Mi Perfil</span></a></li>
                    <li class="divider"></li>
                    <li><a href="{{ url('logout') }}"><i class="icon-switch2"></i> <span>Cerrar Sección</span></a></li>
                </ul>
            </div>
        </div>
        <!-- /user menu -->
        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">
                    @if(!empty(\App\Models\Branch::find(session('branch_id'))))
                        <li class=" @if(Request::is('branch/*')) active @endif">
                            <a href="{{ url('branch/change') }}" title="Click to change Branch">
                                <i class="fa fa-check"></i> <span
                                        style="text-transform: uppercase">{{\App\Models\Branch::find(session('branch_id'))->name }}</span>
                            </a>
                        </li>
                    @endif
                    <li class="@if(Request::is('dashboard')) active @endif">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-dashboard"></i> <span>{{trans_choice('general.dashboard',1)}}</span>
                        </a>
                    </li>
                    @if(Sentinel::hasAccess('branches'))
                        <li class="treeview @if(Request::is('branch/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-briefcase"></i> <span>{{trans_choice('general.branch',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('branches.view'))
                                    <li><a href="{{ url('branch/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.branch',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('branches.create'))
                                    <li><a href="{{ url('branch/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.branch',1)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('borrowers'))
                        <li class="treeview @if(Request::is('borrower/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-users"></i> <span>{{trans_choice('general.borrower',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('borrowers.view'))
                                    <li><a href="{{ url('borrower/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.borrower',2)}}
                                        </a></li>
                                    <li>
                                        <a href="{{ url('borrower/pending') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.borrower',2)}} {{trans_choice('general.pending',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Borrower::where('branch_id', session('branch_id'))->where('active',0)->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                @endif
                                @if(Sentinel::hasAccess('borrowers.create'))
                                    <li><a href="{{ url('borrower/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.borrower',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('borrowers.groups'))
                                    <li><a href="{{ url('borrower/group/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.borrower',1)}} {{trans_choice('general.group',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('borrowers.groups'))
                                    <li><a href="{{ url('borrower/group/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.borrower',1)}} {{trans_choice('general.group',1)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('loans'))
                        <li class="treeview
                @if(Request::is('loan/*')) active @endif
                        @if(Request::is('guarantor/*')) active @endif
                                ">
                            <a href="#">
                                <i class="fa fa-money"></i> <span>{{trans_choice('general.loan',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('loans.view'))
                                    <li><a href="{{ url('loan/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.all',2)}} {{trans_choice('general.loan',2)}}
                                            <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->count() }}</span>
                                    </span>
                                        </a></li>
                                    <li>
                                        <a href="{{ url('loan/data?status=pending') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','pending')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan/data?status=approved') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-success pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','approved')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan/data?status=declined') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.declined',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','declined')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan/data?status=withdrawn') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.withdrawn',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','withdrawn')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan/data?status=written_off') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','written_off')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan/data?status=closed') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.closed',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-success pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','closed')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('loan/data?status=rescheduled') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.rescheduled',1)}}
                                            <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\Loan::where('branch_id', session('branch_id'))->where('status','rescheduled')->count() }}</span>
                                    </span>
                                        </a>
                                    </li>
                                @endif
                                @if(Sentinel::hasAccess('loans.create'))
                                    <li><a href="{{ url('loan/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.loan',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.update'))
                                    <li><a href="{{ url('loan/loan_application/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.application',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.products'))
                                    <li><a href="{{ url('loan/loan_product/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.product',2)}}
                                        </a></li>@endif
                                @if(Sentinel::hasAccess('loans.fees'))
                                    <li><a href="{{ url('charge/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}}  {{trans_choice('general.charge',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.update'))
                                    <li><a href="{{ url('loan/loan_disbursed_by/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.disbursed_by',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.update'))
                                    <li><a href="{{ url('loan/loan_repayment_method/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.method',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.update'))
                                    <li><a href="{{ url('loan/provision/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.provision',1)}} {{trans_choice('general.rate',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.loan_calculator'))
                                    <li><a href="{{ url('loan/loan_calculator/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.loan',1)}} {{trans_choice('general.calculator',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('loans.view'))
                                    <li><a href="{{ url('guarantor/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.guarantor',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('repayments'))
                        <li class="treeview @if(Request::is('repayment/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-dollar"></i> <span>{{trans_choice('general.repayment',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('repayments.view'))
                                    <li><a href="{{ url('repayment/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.repayment',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('repayments.create'))
                                    <li><a href="{{ url('repayment/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.repayment',1)}}
                                        </a></li>
                                @endif

                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('savings'))
                        <li class="treeview @if(Request::is('saving/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-bank"></i> <span>{{trans_choice('general.saving',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('savings.view'))
                                    <li><a href="{{ url('saving/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.account',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('savings.create'))
                                    <li><a href="{{ url('saving/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.account',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('savings.view'))
                                    <li><a href="{{ url('saving/savings_transaction/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('savings.products'))
                                    <li><a href="{{ url('saving/savings_product/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.product',2)}}
                                        </a></li>
                                @endif


                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('settingsff'))
                        <li class="treeview @if(Request::is('capital/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-bookmark"></i> <span>{{trans_choice('general.capital',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('settings'))
                                    <li><a href="{{ url('capital/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.capital',2)}}
                                        </a></li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if(Sentinel::hasAccess('stock'))
                        <li class="treeview
                @if(Request::is('stock/*')) active @endif
                        @if(Request::is('supplier/*')) active @endif
                        @if(Request::is('product/*')) active @endif
                        @if(Request::is('check_in/*')) active @endif
                        @if(Request::is('check_out/*')) active @endif
                        @if(Request::is('warehouse/*')) active @endif
                                ">
                            <a href="#">
                                <i class="fa fa-cubes"></i> <span>{{trans_choice('general.stock',1)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('product/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.product',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('supplier/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.supplier',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('warehouse/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.warehouse',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('product/category/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.category',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('check_in/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.check_in',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('check_out/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.check_out',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('stock.view'))
                                    <li><a href="{{ url('check_out/overview') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.overview',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('payroll'))
                        <li class="treeview @if(Request::is('payroll/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-paypal"></i> <span>{{trans_choice('general.payroll',1)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('payroll.view'))
                                    <li><a href="{{ url('payroll/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.payroll',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('payroll.create'))
                                    <li><a href="{{ url('payroll/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('payroll.update'))
                                    <li><a href="{{ url('payroll/template') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.payroll',1)}} {{trans_choice('general.template',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('expenses'))
                        <li class="treeview @if(Request::is('expense/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-share"></i> <span>{{trans_choice('general.expense',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('expenses.view'))
                                    <li><a href="{{ url('expense/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.expense',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('expenses.create'))
                                    <li><a href="{{ url('expense/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.expense',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('expenses.create'))
                                    <li><a href="{{ url('expense/type/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.expense',1)}} {{trans_choice('general.type',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('other_income'))
                        <li class="treeview @if(Request::is('other_income/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-plus"></i> <span>{{trans_choice('general.other_income',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('other_income.view'))
                                    <li><a href="{{ url('other_income/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.other_income',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('other_income.create'))
                                    <li><a href="{{ url('other_income/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.other_income',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('other_income.create'))
                                    <li><a href="{{ url('other_income/type/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.other_income',1)}} {{trans_choice('general.type',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('collateral'))
                        <li class="treeview @if(Request::is('collateral/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-list"></i> <span>{{trans_choice('general.collateral',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('collateral.view'))
                                    <li><a href="{{ url('collateral/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.collateral',2)}} {{trans_choice('general.register',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('collateral.create'))
                                    <li><a href="{{ url('collateral/type/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.collateral',2)}} {{trans_choice('general.type',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('reports'))
                        <li class="treeview @if(Request::is('report/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-bar-chart"></i> <span>{{trans_choice('general.report',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('report/borrower_report') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.borrower',1)}} {{trans_choice('general.report',2)}}
                                    </a></li>
                                <li><a href="{{ url('report/loan_report') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}
                                    </a></li>
                                <li><a href="{{ url('report/financial_report') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.financial',1)}} {{trans_choice('general.report',2)}}
                                    </a></li>
                                <li><a href="{{ url('report/company_report') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.organisation',1)}} {{trans_choice('general.report',2)}}
                                    </a></li>
                                <li><a href="{{ url('report/savings_report') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.saving',2)}} {{trans_choice('general.report',2)}}
                                    </a></li>

                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('reports'))
                        <li class="treeview
                    @if(Request::is('accounting/*')) active @endif
                        @if(Request::is('chart_of_account/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-credit-card"></i> <span>{{trans_choice('general.accounting',1)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('repayments.view'))
                                    <li><a href="{{ url('chart_of_account/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.chart_of_account',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('repayments.view'))
                                    <li><a href="{{ url('accounting/journal') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.journal',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('repayments.view'))
                                    <li><a href="{{ url('accounting/ledger') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.ledger',1)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('repayments.view'))
                                    <li><a href="{{ url('accounting/manual_entry/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.journal',1)}} {{trans_choice('general.manual_entry',1)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('communication'))
                        <li class="treeview @if(Request::is('asset/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-briefcase"></i> <span>{{trans_choice('general.asset',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('asset/data') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.asset',2)}}
                                    </a></li>
                                <li><a href="{{ url('asset/create') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.asset',1)}}
                                    </a></li>
                                <li><a href="{{ url('asset/type/data') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.asset',1)}} {{trans_choice('general.type',2)}}
                                    </a></li>
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('communication'))
                        <li class="treeview @if(Request::is('communication/*')) active @endif
                        @if(Request::is('sms_gateway/*')) active @endif
                                ">
                            <a href="#">
                                <i class="fa fa-envelope"></i> <span>{{trans_choice('general.communication',2)}} </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{ url('communication/email') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.email',1)}}
                                    </a></li>
                                <li><a href="{{ url('communication/sms') }}"><i
                                                class="fa fa-circle-o"></i> {{trans_choice('general.sms',2)}}
                                    </a></li>
                                @if(Sentinel::hasAccess('settings'))
                                    <li><a href="{{ url('sms_gateway/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.sms',1)}} {{trans_choice('general.gateway',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('custom_fields'))
                        <li class="treeview @if(Request::is('custom_field/*')) active @endif">
                            <a href="#">
                                <i class="fa fa-users"></i> <span>{{trans_choice('general.custom_field',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('custom_fields.view'))
                                    <li><a href="{{ url('custom_field/data') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.custom_field',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('custom_fields.create'))
                                    <li><a href="{{ url('custom_field/create') }}"><i
                                                    class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.custom_field',1)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('users'))
                        <li class="treeview @if(Request::is('user/*')) active @endif">
                            <a href="{{ url('user/data') }}">
                                <i class="fa fa-users"></i> <span>{{trans_choice('general.user',2)}}</span>
                            </a>
                            <ul class="treeview-menu">
                                @if(Sentinel::hasAccess('users.view'))
                                    <li><a href="{{ url('user/data') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span>{{trans_choice('general.view',2)}} {{trans_choice('general.user',2)}}</span>
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('users.roles'))
                                    <li><a href="{{ url('user/role/data') }}"><i
                                                    class="fa fa-circle-o"></i>{{trans_choice('general.manage',2)}} {{trans_choice('general.role',2)}}
                                        </a></li>
                                @endif
                                @if(Sentinel::hasAccess('users.create'))
                                    <li><a href="{{ url('user/create') }}"><i
                                                    class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.user',2)}}
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('audit_trail'))
                        <li class="@if(Request::is('audit_trail/*')) active @endif">
                            <a href="{{ url('audit_trail/data') }}">
                                <i class="fa fa-area-chart"></i> <span>{{trans_choice('general.audit_trail',2)}}</span>
                            </a>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('settings'))
                        <li class="@if(Request::is('setting/*')) active @endif">
                            <a href="{{ url('setting/data') }}">
                                <i class="fa fa-cog"></i> <span>{{trans_choice('general.setting',2)}}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
