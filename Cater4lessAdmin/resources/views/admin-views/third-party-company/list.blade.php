@extends('layouts.admin.app')

@section('title', translate('3rd_Party_Companies'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-auto mb-md-0 mb-3 mr-auto">
                    <h1 class="page-header-title"> {{ translate('messages.3rd_Party_Companies') }}
                        <span class="badge badge-soft-dark ml-2" id="foodCount">{{ $companies->total() }}</span>
                    </h1>
                </div>
            </div>

        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header border-0 py-2">
                        <div class="search--button-wrapper">
                            <h5 class="card-title d-none d-xl-block"></h5>
                            <form id="search-form">


                                <div class="input--group input-group input-group-merge input-group-flush">
                                    <input id="datatableSearch" name="search" type="search"
                                        value="{{ request()?->search ?? null }}" class="form-control"
                                        placeholder="{{ translate('Search_by_name') }}"
                                        aria-label="{{ translate('messages.search_here') }}">
                                    <button type="submit" class="btn btn--secondary">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                                <!-- End Search -->
                            </form>

                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                                    href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                    <a id="export-excel" class="dropdown-item"
                                        href="{{ route('admin.third-party-company.export', ['type' => 'excel', 'search' => request()->get('search')]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>

                                    <a id="export-csv" class="dropdown-item"
                                        href="{{ route('admin.third-party-company.export', ['type' => 'csv', 'search' => request()->get('search')]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>


                                </div>
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom" id="table-div">
                        <table id="datatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                    "columnDefs": [{
                                        "targets": [],
                                        "width": "5%",
                                        "orderable": false
                                    }],
                                    "order": [],
                                    "info": {
                                    "totalQty": "#datatableWithPaginationInfoTotalQty"
                                    },

                                    "entries": "#datatableEntries",

                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging":false
                                }'>
                            <thead class="thead-light">
                                <tr>
                                    <th class="w-60px">{{ translate('messages.sl') }}</th>
                                    <th class="w-100px">{{ translate('messages.company_name') }}</th>
                                    <th class="w-120px">{{ translate('messages.company_email') }}</th>
                                    <th class="w-120px">{{ translate('messages.company_phone') }}</th>
                                    <th class="w-100px">{{ translate('messages.company_address') }}</th>
                                    <th class="w-100px">{{ translate('messages.status') }}</th>
                                    <th class="w-120px text-center">
                                        {{ translate('messages.action') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($companies as $key => $company)
                                    <tr>
                                        <td>{{ $key + $companies->firstItem() }}</td>
                                        <td>{{ Str::limit($company->company_name, 30, '...') }}</td>
                                        <td>{{ $company->company_email }}</td>
                                        <td>{{ $company->company_phone }}</td>
                                        <td>{{ Str::limit($company->company_address, 40, '...') }}</td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm" for="status{{ $company->id }}">
                                                <input type="checkbox" class="toggle-switch-input"
                                                    id="status{{ $company->id }}" data-id="{{ $company->id }}"
                                                    {{ $company->status === 'active' ? 'checked' : '' }}
                                                    onchange="update_status(this)">
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>

                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                    href="{{ route('admin.third-party-company.edit', [$company->id]) }}"
                                                    title="{{ translate('messages.edit') }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-sm btn--warning btn-outline-warning action-btn form-alert"
                                                    href="javascript:" data-id="company-{{ $company->id }}"
                                                    data-message="{{ translate('messages.Want_to_delete_this_item') }}"
                                                    title="{{ translate('messages.delete') }}">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                            </div>
                                            <form action="{{ route('admin.third-party-company.delete', [$company->id]) }}"
                                                method="post" id="company-{{ $company->id }}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    @if (count($companies) === 0)
                        <div class="empty--data">
                            <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="public">
                            <h5>
                                {{ translate('no_data_found') }}
                            </h5>
                        </div>
                    @endif
                    <div class="page-area px-4 pb-3">
                        <div class="d-flex align-items-center justify-content-end">
                            <div>
                                {!! $companies->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";
        ///Update status
        function update_status(el) {
            let companyId = $(el).data('id');

            $.ajax({
                url: "{{ url('admin/third-party-company/status') }}/" + companyId,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error("Something went wrong!");
                    }
                },
                error: function() {
                    toastr.error("Failed to update status!");
                }
            });
        }
    </script>
@endpush
