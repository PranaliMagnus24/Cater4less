@extends('layouts.admin.app')

@section('title', translate('Promotion & Gift Points'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <i class="tio-dollar-outlined"></i> {{ translate('Promotion & Gift Points') }}
        </h1>
    </div>

    <div class="card">
     <div class="card-header py-2 border-0 d-flex justify-content-between">
    <h3 class="card-title">{{ translate('Restaurants Promotion Settings') }}</h3>
    <div class="d-flex">
        <input type="text" id="search" class="form-control me-3"
               placeholder="{{ translate('Search restaurant...') }}">
        <button type="button" id="search-btn" class="btn btn-primary">
            <i class="tio-search"></i>
        </button>
    </div>
</div>



        <div class="table-responsive" id="restaurant-table">
            @include('admin-views.promotion.partials._table', ['restaurants' => $restaurants])
        </div>
    </div>
</div>

@endsection
@push('script_2')
<script>
     "use strict";
 $(document).ready(function () {
        $('#search-btn').on('click', function () {
            let search = $('#search').val();
            $.ajax({
                url: "{{ route('admin.promotion.list') }}",
                type: "GET",
                data: { search: search },
                success: function (data) {
                    $('#restaurant-table').html(data.html);
                },
                error: function (xhr) {
                    console.log("Error:", xhr.responseText);
                }
            });
        });
    });
</script>
@endpush


