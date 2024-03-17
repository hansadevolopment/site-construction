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
      <link rel="stylesheet" href="{{ asset('css/gl.css') }}">
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
                                <li><a href={{route('sales_category')}}>Sales Category </a></li>
                                <li><a href={{route('sales_location')}}>Sales Location</a></li>
                                <li><a href={{route('sales_rep')}}>Sales Rep</a></li>
                                <li><a href={{route('debtor')}}>Debtor</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                               Transaction
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href="{{route('invoice')}}">Invoice</a></li>
                                <li><a href="{{route('receipt')}}">Receipt</a></li>
                                <li><a href="{{route('sales_return')}}">Sales Return</a></li>
                                <li><a href="{{route('credit_note')}}">Credit Note</a></li>
                                <li><a href="{{route('sales_settlement')}}">Sales Settlement</a></li>
                                <li><a href="{{route('custom_order')}}">Custom Order</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                List & Inquire
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('sales_primary_inquire')}}>Primary List & Inquire</a></li>
                                <li><a href={{route('sales_transaction_inquire')}}>Transaction List & Inquire</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Report
                            </a>
                            <ul class="dropdown-menu dropdown-single-menu-content">
                                <li><a href={{route('debtor_statement')}}>Debtor Statement & Age Analysis Report</a></li>
                                <li><a href={{route('debtor_ledger')}}>Debtor Ledger</a></li>
                                <li><a href={{route('debtor_transaction_report')}}>Transaction Report</a></li>
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
    <script src="{{ asset('js/gl.js') }}" defer></script>
</body>
</html>
