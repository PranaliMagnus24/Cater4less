@extends('layouts.admin.app')

@section('title', translate('Restaurant Badge List'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <i class="tio-star"></i> {{ translate('messages.Restaurant Badges') }}
            <span class="badge badge-soft-dark ml-2" id="itemCount">{{ $restaurants->total() }}</span>
        </h1>
    </div>
    <!-- End Page Header -->


<!-- Filters -->
<div class="card shadow--card p-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.badge-cashback.restaurant_list') }}">
            <div class="row g-3 align-items-center">
                <!-- Search by name -->
                <div class="col-md-4">
                    <div class="input-group input-group-merge input-group-flush">
                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            value="{{ request('search', '') }}"
                            placeholder="{{ translate('Search by restaurant name') }}"
                            aria-label="{{ translate('Search by restaurant name') }}"
                        >
                        <button type="submit" class="btn btn--secondary">
                            <i class="tio-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Badge filter -->
                <div class="col-md-3">
                    <select
                        name="badge"
                        class="form-control js-select2-custom"
                        onchange="this.form.submit()"
                        aria-label="{{ translate('Filter by badge') }}"
                    >
                        <option value="all" {{ request('badge', 'all') === 'all' ? 'selected' : '' }}>
                            {{ translate('All Badges') }}
                        </option>
                        <option value="gold" {{ request('badge') === 'gold' ? 'selected' : '' }}>🟡 Gold</option>
                        <option value="platinum" {{ request('badge') === 'platinum' ? 'selected' : '' }}>⚪ Platinum</option>
                        <option value="caterstar" {{ request('badge') === 'caterstar' ? 'selected' : '' }}>🌟 CaterStar</option>
                        <option value="" {{ request()->has('badge') && request('badge') === '' ? 'selected' : '' }}>❌ None</option>
                    </select>
                </div>

                <!-- Reset -->
                <div class="col-md-2">
                    <a href="{{ route('admin.badge-cashback.restaurant_list') }}" class="btn btn--secondary w-100">
                        {{ translate('Reset') }}
                    </a>
                </div>

                <!-- Export -->
                <div class="col-md-3">
                    <div class="hs-unfold">
                        <a
                            class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40 w-100"
                            href="javascript:"
                            data-hs-unfold-options='{"target": "#usersExportDropdown","type": "css-animation"}'
                        >
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>

                        <div id="usersExportDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>

                            <a
                                id="export-excel"
                                class="dropdown-item"
                                href="{{ route('admin.badge-cashback.restaurant_export', array_merge(request()->all(), ['type' => 'excel'])) }}"
                            >
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg" alt="">
                                {{ translate('messages.excel') }}
                            </a>

                            <a
                                id="export-csv"
                                class="dropdown-item"
                                href="{{ route('admin.badge-cashback.restaurant_export', array_merge(request()->all(), ['type' => 'csv'])) }}"
                            >
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg" alt="">
                                {{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- End Filters -->

    <!-- Table -->
    <div class="card">
        <div class="card-header py-2 border-0">
            <h3 class="card-title">{{ translate('messages.Restaurant Badge List') }}</h3>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap card-table">
                <thead class="thead-light">
                    <tr>
                        <th class="text-uppercase w-90px">{{ translate('SL') }}</th>
                        <th>{{ translate('Restaurant Name') }}</th>
                        <th>{{ translate('Badge') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($restaurants as $key => $restaurant)
                        <tr>
                            <td>{{ $key + $restaurants->firstItem() }}</td>
                            <td>{{ $restaurant->name }}</td>
                            <td>
                                @if($restaurant->badge == 'gold')
                                    🟡 Gold
                                @elseif($restaurant->badge == 'platinum')
                                    ⚪ Platinum
                                @elseif($restaurant->badge == 'caterstar')
                                    🌟 CaterStar
                                @else
                                    ❌ None
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(count($restaurants) === 0)
                <div class="empty--data">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="public">
                    <h5>{{ translate('No data found') }}</h5>
                </div>
            @endif

            <div class="page-area px-4 pb-3">
                <div class="d-flex align-items-center justify-content-end">
                    <div>
                        {!! $restaurants->appends(request()->all())->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Table -->
</div>
@endsection
