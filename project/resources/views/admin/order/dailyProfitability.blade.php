@extends('layouts.admin')
@section('title', 'Daily Profitability')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endsection


@section('scripts')



    {{--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>--}}
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>



    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src=" https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src=" https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>

    <script>



        $(document).ready(function() {
            $('#example').DataTable( {
                "order": [[ 0, "desc" ]],


                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'dailyProfitability'
                    },
                    {
                        extend: 'print',
                        title: 'dailyProfitability'
                    }
                ]
            } );
        } );
    </script>

@endsection


@section('content')

    <input type="hidden" id="headerdata" value="{{ __('ORDER') }}">

    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">{{ __('Daily Profitability') }}</h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Orders') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('daily-profitability') }}">{{ __('daily-profitability') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="product-area">
            <div class="row">
                <div class="col-lg-12">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th class="hidden">No</th>
                            <th>Date</th>
                            <th>Num Items</th>
                            <th>MRP Value</th>
                            <th>Revenue</th>
                            <th>Cost</th>
                            <th>Gross Profit</th>
                            <th>Margin</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dailyProfitability as $key=>$item)
                            <tr>
                                <td class="hidden">{{$key+1}}</td>
                                <td>{{ date('d-M-Y', strtotime($item->date)) }}</td>
                                <td>{{$item->num_items}}</td>

                                <td>{{round($item->total_pay_amount)}}</td>
                                <td>{{round($item->total_pay_amount)}}</td>
                                <td>{{''}}</td>
                                <td>{{''}}</td>
                                <td>{{''}}</td>
                            </tr>
                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </div>










@endsection

