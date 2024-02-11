<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <title> @yield('title') </title>

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

      <!-- Styles -->
      <link rel="stylesheet" href="{{ asset('css/common.css') }}">
      <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
</head>
<body>

    <div class="container-fluid unset-pl unset-pr">
        <nav class="navbar navbar-expand-lg bg-body-tertiary navbar-dark navbar-bgcolor">
            <div class="container-fluid">
                <a class="navbar-brand navbar-dashboard" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Primary Data
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('site')}}>Site</a></li>
                                <li><a href={{route('item')}}>Item </a></li>
                                <li><a href={{route('labour_category')}}>Labour Category</a></li>
                                <li><a href={{route('employee')}}>Employee</a></li>
                                <li><a href={{route('overhead_cost')}}>Overhead Cost</a></li>
                                <li><a href={{route('unit')}}>Unit</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Site Forcasting
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('site_task')}}>Task</a></li>
                                <li><a href={{route('site_sub_task')}}>Sub Task</a></li>
                                <li><a href={{route('sap_material')}}>Meterials</a></li>
                                <li><a href={{route('sap_labour')}}>Labour</a></li>
                                <li><a href={{route('sap_overhead')}}>Overhead Cost</a></li>
                                <li><a href={{route('sap_profit')}}>Profit</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Site Operation
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('item_issue_note')}}>Item Issue Note</a></li>
                                <li><a href={{route('payment_voucher')}}>Payment Voucher</a></li>
                                <li><a href={{route('employee_advance')}}>Employee Advance</a></li>
                                <li><a href={{route('employee_salary')}}>Employee Salary - Basic Salary</a></li>
                                <li><a href={{route('employee_salary_two')}}>Employee Salary - Target, Sub Contract</a></li>
                                <li><a href={{route('dpr')}}>Daily Progress Report</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                List & Inquire
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('master_inquire')}}>Master</a></li>
                                <li><a href={{route('site_task_subtask_inquiry')}}>Site, Task & Sub Task</a></li>
                                <li><a href={{route('sap_inquire')}}>Site Action Plan</a></li>
                                <li><a href={{route('so_inquire')}}>Site Operation</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Report
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('sap_report')}}>Site Action Plan</a></li>
                                <li><a href={{route('so_summary_report')}}>Site Operation Report</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="d-flex">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{Auth::user()->name}}
                        </a>
                        <ul class="dropdown-menu dropdown-single-menu-content navbar-menu-context-right">
                            <li><a href="#">Profile</a></li>
                            <li><a href="#">Password Reset</a></li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <li>
                                    <a href="{{route('logout')}}" onclick = "event.preventDefault(); this.closest('form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </form>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="container-fluid">
        @yield('body')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <!-- scripts -->
    <script src="{{ asset('js/common.js') }}" defer></script>
    <script src="{{ asset('js/site.js') }}" defer></script>
</body>
</html>
