<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title> Seelan Finance System </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <style>
		* {
	          box-sizing: border-box;
	    }

        body {

              margin: 0;
        }

        .navbar_mega_menu {

              overflow: hidden;
              background-color: #333;
              font-family: consolas;
        }

        .navbar_mega_menu_right_align {

              float: right;
        }

        .navbar_mega_menu a {

              float: left;
              font-size: 16px;
              color: white;
              text-align: center;
              padding: 14px 16px;
              text-decoration: none;
        }

        .dropdown_mega_menu {

              float: left;
              overflow: hidden;
        }

        .dropdown_mega_menu .dropbtn {

              font-size: 16px;
              border: none;
              outline: none;
              color: white;
              padding: 14px 16px;
              background-color: inherit;
              font: inherit;
              margin: 0;
        }

        .navbar_mega_menu a:hover, .dropdown_mega_menu:hover .dropbtn {

              background-color: red;
        }

        .dropdown_mega_menu_content {

              display: none;
              position: absolute;
              background-color: #f9f9f9;
              width: 98%;
              left: 1%;
              box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
              z-index: 1;
        }

        .dropdown_mega_menu_content .header {

              background: red;
              padding: 16px;
              color: white;
        }

        .dropdown_mega_menu:hover .dropdown_mega_menu_content  {

              display: block;
        }

        /* Create three equal columns that floats next to each other */
        .column {

              float: left;
              width: 25%;
              padding: 10px;
              background-color: #ccc;
              height: 400px;
        }

        .column a {

              float: none;
              color: black;
              padding: 16px;
              text-decoration: none;
              display: block;
              text-align: left;
        }

        .column a:hover {

              background-color: #ddd;
        }

        /* Clear floats after the columns */
        .row:after {

              content: "";
              display: table;
              clear: both;
        }

        .dropdown_single_menu_content {

              display: none;
              position: absolute;
              background-color: #f9f9f9;
              min-width: 160px;
              box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
              z-index: 1;
        }

        .dropdown_single_menu_content a {

              float: none;
              color: black;
              padding: 12px 16px;
              text-decoration: none;
              display: block;
              text-align: left;
        }

        .dropdown_single_menu_content a:hover {

              background-color: #ddd;
              color: black;
        }

        .dropdown_mega_menu:hover .dropdown_single_menu_content {

              display: block;
        }

	</style>

</head>
<body>

    <div id="tbldiv" style="width: 100%;">

        <div class="navbar_mega_menu">

            <div class="dropdown_mega_menu">

				<button class="dropbtn">
					Dashboard <i class="fa fa-caret-down"></i>
				</button>

			</div>


			<div class="navbar_mega_menu_right_align">
                <div class="dropdown_mega_menu">
                	<button class="dropbtn" style="width: 100%;">
		                {{Auth::user()->name}}
		                <i class="fa fa-caret-down"></i>
                	</button>
					<div class="dropdown_single_menu_content">
	                	<a href="#">Profile</a>
	                	<a href="#">Password Reset</a>
	                    <form method="POST" action="{{ route('logout') }}">
	                          @csrf
	                          <a href="{{route('logout')}}" onclick = "event.preventDefault(); this.closest('form').submit();">Logout</a>
	                    </form>
              		</div>
            	</div>

		        <div class="dropdown_mega_menu">
		        </div>

  			</div>

		</div>

        <div id="content" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">

            <br>
            <div class="row">

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header">Sales Module</div>
                        <div class="card-body">
                            <ul>
                                <li>Invoice</li>
                                <li>Receipt</li>
                                <li>Sales Return Note</li>
                                <li>Credit Note</li>
                                <li>Sales Settlement Note</li>
                                <li>Customer Order Note</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header">Purchasing Module </div>
                        <div class="card-body">
                            <ul>
                                <li>Good Receive Note</li>
                                <li>Payment Voucher</li>
                                <li>Purchase Return Note</li>
                                <li>Debit Note</li>
                                <li>Purchase Settlement Note</li>
                                <li>Purchase Order Note</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header">Inventory Module </div>
                        <div class="card-body">
                            <ul>
                                <li>Item Request Note</li>
                                <li>Item Issue Note</li>
                                <li>Production Note</li>
                                <li>Stock Adjustment Note</li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
            <br>

            <div class="row">

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">

                        <div class="card-header"> <a href="{{route('genaral_ledger')}}"> Genaral Ledger </a> </div>
                        <div class="card-body">
                            <ul>
                                <li> Journal Entry</li>
                                <li>Petty Cash Note</li>
                                <li>Bank Reconsilation</li>
                            </ul>
                        </div>
                    </div>

                </div>


                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header"> Fixed Asset Module </div>
                        <div class="card-body">
                            <h2 class="card-title" style="text-align: center;">
                                0
                            </h2>
                        </div>
                    </div>

                </div>

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header">Payroll Module</div>
                        <div class="card-body">
                            <h2 class="card-title" style="text-align: center;">
                                0
                            </h2>
                        </div>
                    </div>

                </div>

            </div>
            <br>

            <div class="row">

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header"> Tracking Module </div>
                        <div class="card-body">
                            <h2 class="card-title" style="text-align: center;">
                                0
                            </h2>
                        </div>
                    </div>

                </div>

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header"> Management Module </div>
                        <div class="card-body">
                            <h2 class="card-title" style="text-align: center;">
                                0
                            </h2>
                        </div>
                    </div>

                </div>

                <div class="col-sm-4">

                    <div class="card bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header"> Admin Module </div>
                        <div class="card-body">
                            <h2 class="card-title" style="text-align: center;">
                                0
                            </h2>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
