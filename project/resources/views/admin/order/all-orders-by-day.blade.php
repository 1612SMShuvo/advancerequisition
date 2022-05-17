@extends('layouts.admin')
@section('title', 'all Orders by day')

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
                    title: 'all-Orders-by-day'
                },
                {
                    extend: 'print',
                    title: 'all-Orders-by-day'
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
                    <h4 class="heading">{{ __('all Orders by day') }}</h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('Orders') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('admin-all-order-by-day') }}">{{ __('all Orders by day') }}</a>
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
                            <th>Date</th>
                            <th>Num Orders</th>
                            <th>Customers</th>
                            <th>Num Cancelled Shipments</th>
                            <th>Order Value</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($days_order as $item)
                        <tr>
                            <td>{{ date('d-M-y', strtotime($item->date)) }}</td>
                            <td>{{$item->numder_of_order}}</td>
                            <td>{{$item->numder_of_customer}}</td>
                            <td>{{$item->cancel_order}}</td>
                            <td>{{round($item->total_pay_amount)}}</td>
                            <td>{{''}}</td>
                        </tr>
                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </div>










@endsection

@section('scripts')

    {{-- DATA TABLE --}}

    <script type="text/javascript">

        var table = $('#geniustable').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin-order-datatables','pending') }}',
            columns: [
                { data: 'customer_email', name: 'customer_email' },
                { data: 'id', name: 'id' },
                { data: 'totalQty', name: 'totalQty' },
                { data: 'pay_amount', name: 'pay_amount' },
                { data: 'action', searchable: false, orderable: false }
            ],
            language : {
                processing: '<img src="{{asset('assets/images/'.$gs->admin_loader)}}">'
            },
            drawCallback : function( settings ) {
                $('.select').niceSelect();
            }
        });

    </script>

    {{-- DATA TABLE --}}

@endsection