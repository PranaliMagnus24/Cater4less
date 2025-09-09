@extends('layouts.admin.app')

@section('title', translate('messages.Badge_Cashback'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h2 class="page-header-title text-capitalize">
                    <div class="card-header-icon d-inline-flex mr-2 img">
                        <img src="{{ dynamicAsset('public/assets/admin/img/category.png') }}" alt="">
                    </div>
                    <span>{{ translate('Badging Criteria') }}</span>
                </h2>
            </div>
        </div>
    </div>

    <div class="card resturant--cate-form">
        <div class="card-body">
            <form id="badgeCashbackForm">
                @csrf
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Badge</th>
                            <th>Percentage %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['gold', 'platinum', 'caterstar'] as $badge)
                            @php
                                $value = $badges->where('badge', $badge)->first()->cashback_percentage ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    @if ($badge == 'gold')
                                        🟡 Gold
                                    @elseif($badge == 'platinum')
                                        ⚪ Platinum
                                    @elseif($badge == 'caterstar')
                                        🌟 CaterStar
                                    @endif
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100"
                                           name="cashbacks[{{ $badge }}]" value="{{ $value }}"
                                           class="form-control">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>

            <div id="responseMessage" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
$(document).ready(function () {
    $('#badgeCashbackForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('admin.badge-cashback.update') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                }
            },
            error: function () {
                toastr.error("{{ translate('messages.something_went_wrong') }}");
            }
        });
    });
});
</script>
@endpush
