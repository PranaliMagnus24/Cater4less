@extends('layouts.vendor.app')

@section('title', translate('Promotion & Gift Points'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <i class="tio-dollar-outlined"></i> {{ translate('Promotion & Gift Points') }}
            </h1>
        </div>

        <div class="card">
            <div class="card-header py-2 border-0">
                <h3 class="card-title">{{ translate('Manage Your Bids & Gift Points') }}</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('vendor.restaurant.promotion.update') }}" method="POST">
                    @csrf

                    <!-- Enable/disable bidding -->
                    <div class="form-group d-flex align-items-center">
                        <label class="mb-0 me-3" for="is_bid_enabled">
                            {{ translate('Enable Bidding & Gift Points') }}
                        </label>

                        <label class="toggle-switch toggle-switch-sm mb-0">
    <input type="checkbox" id="is_bid_enabled" name="is_bid_enabled"
        class="toggle-switch-input"
        value="1"
        {{ old('is_bid_enabled', $restaurant->currentBid?->is_active || $restaurant->currentGiftPoint?->is_active ? true : false) ? 'checked' : '' }}>
    <span class="toggle-switch-label text">
        <span class="toggle-switch-indicator"></span>
    </span>
</label>

                    </div>



                    <!-- Inputs (toggle on checkbox) -->
                  <div id="bidInputs"
     style="{{ old('is_bid_enabled', $restaurant->currentBid?->is_active || $restaurant->currentGiftPoint?->is_active) ? '' : 'display:none;' }}">

    <div class="form-group">
        <label>{{ translate('Promotion Bid Percentage') }} (5–20)</label>
        <input type="number" name="promotion_bid" class="form-control" min="5" max="20"
            value="{{ old('promotion_bid', $restaurant->currentBid?->bid_percentage) }}">
    </div>

    <div class="form-group">
        <label>{{ translate('Gift Point Percentage') }} (2–10)</label>
        <input type="number" name="gift_point_percentage" class="form-control" min="2" max="10"
            value="{{ old('gift_point_percentage', $restaurant->currentGiftPoint?->gift_point_percentage) }}">
    </div>
</div>


                    <button type="submit" class="btn btn-primary">{{ translate('Save Changes') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        document.getElementById('is_bid_enabled').addEventListener('change', function() {
            document.getElementById('bidInputs').style.display = this.checked ? '' : 'none';
        });
    </script>
@endpush
