@extends('layouts.admin.app')

@section('title', translate('messages.Gift_Card_List'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <i class="tio-gift"></i> {{ translate('messages.digital_gift_cards') }}
                        <span class="badge badge-soft-dark ml-2" id="itemCount">{{ $giftCards->total() }}</span>
                    </h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn--primary" href="{{ route('admin.gift_cards.create') }}">
                        <i class="tio-add"></i> {{ translate('messages.add_new_gift_card') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <form>
                                <div class="input--group input-group input-group-merge input-group-flush">
                                    <input id="datatableSearch" type="search" name="search"
                                        value="{{ request()?->search ?? null }}" class="form-control"
                                        placeholder="{{ translate('Ex_:_Search_by_title...') }}" aria-label="Search here">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                            </form>
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                                    href="javascript:"
                                    data-hs-unfold-options='{"target": "#usersExportDropdown","type": "css-animation"}'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>

                                    <a id="export-excel" class="dropdown-item"
                                        href="{{ route('admin.gift_cards.export', ['type' => 'excel'] + request()->query()) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="">
                                        {{ translate('messages.excel') }}
                                    </a>

                                    <a id="export-csv" class="dropdown-item"
                                        href="{{ route('admin.gift_cards.export', ['type' => 'csv'] + request()->query()) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="">
                                        {{ translate('messages.csv') }}
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="font-size-sm table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{"order": [], "orderCellsTop": true, "paging": false}'>
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('messages.sl') }}</th>
                                    <th>{{ translate('messages.code') }}</th>
                                    <th>{{ translate('messages.amount') }}</th>
                                    <th>{{ translate('messages.balance') }}</th>
                                    <th>{{ translate('messages.expire date') }}</th>
                                    <th>{{ translate('messages.status') }}</th>
                                    <th class="text-center">{{ translate('messages.action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($giftCards as $key => $giftcard)
                                    <tr>
                                        <td>{{ $key + $giftCards->firstItem() }}</td>
                                        <td>
                                            <span class="d-block text-body">{{ $giftcard->code }}</span>
                                        </td>
                                        <td>$ {{ number_format($giftcard->amount, 2) }}</td>
                                        <td>
                                            $ {{ number_format($giftcard->balance, 2) }}
                                        </td>
                                        <td>
                                            {{ $giftcard->expiry_date }}
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm" for="status{{ $giftcard->id }}">
                                                <input type="checkbox" class="toggle-switch-input change-status"
                                                    id="status{{ $giftcard->id }}"
                                                    data-url="{{ route('admin.gift_cards.status', $giftcard->id) }}"
                                                    {{ $giftcard->status == 'active' ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>

                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                    href="{{ route('admin.gift_cards.edit', $giftcard->id) }}"
                                                    title="{{ translate('messages.edit') }}"><i class="tio-edit"></i></a>
                                                <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert"
                                                    href="javascript:" data-id="giftcard-{{ $giftcard->id }}"
                                                    data-message="{{ translate('messages.Want_to_delete_this_gift_card') }}"
                                                    title="{{ translate('messages.delete') }}">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{ route('admin.gift_cards.destroy', $giftcard->id) }}"
                                                    method="post" id="giftcard-{{ $giftcard->id }}">
                                                    @csrf @method('delete')
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if (count($giftCards) === 0)
                            <div class="empty--data">
                                <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="">
                                <h5>{{ translate('no_data_found') }}</h5>
                            </div>
                        @endif
                        <div class="page-area px-4 pb-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <div>
                                    {!! $giftCards->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
    @push('script_2')
        <script>
            "use strict";
            ///Change Status
            $(document).on('change', '.change-status', function() {
                let checkbox = $(this);
                let url = checkbox.data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);

                        if (response.status === 'active') {
                            checkbox.prop('checked', true);
                        } else {
                            checkbox.prop('checked', false);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong!');
                        console.error(xhr.responseText);
                    }
                });
            });
            // giftcard delete--
            $(document).on('submit', 'form[id^="giftcard-"]', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        toastr.success(response.message);

                        form.closest('tr').remove();
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong!');
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
    @endpush
@endsection
