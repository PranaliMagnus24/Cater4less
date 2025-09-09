<table class="table table-bordered table-hover">
    <thead class="thead-light">
        <tr>
            <th>{{ translate('SL') }}</th>
            <th>{{ translate('Restaurant Name') }}</th>
            <th>{{ translate('Enable Bidding & Gift Points') }}</th>
            <th>{{ translate('Promotion Bid (%)') }}</th>
            <th>{{ translate('Gift Points (%)') }}</th>
            <th>{{ translate('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($restaurants as $key => $restaurant)
            <tr>
                <td>{{ $key + $restaurants->firstItem() }}</td>
                <td>{{ $restaurant->name }}</td>
                <td>
                    <label class="toggle-switch toggle-switch-sm mb-0">
                        <input type="checkbox"
                               class="toggle-switch-input"
                               name="is_bid_enabled"
                               value="1"
                               {{ optional($restaurant->currentBid)->is_active ? 'checked' : '' }}>
                        <span class="toggle-switch-label text">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </td>
                <td>{{ optional($restaurant->currentBid)->bid_percentage ?? 'N/A' }}%</td>
                <td>{{ optional($restaurant->currentGiftPoint)->gift_point_percentage ?? 'N/A' }}%</td>
                <td>
                    <form action="{{ route('admin.promotion.update', $restaurant->id) }}" method="POST" class="d-flex">
                        @csrf
                        <label class="toggle-switch toggle-switch-sm mb-0 mx-1">
                            <input type="checkbox" class="toggle-switch-input"
                                   name="is_bid_enabled"
                                   value="1"
                                   {{ optional($restaurant->currentBid)->is_active ? 'checked' : '' }}>
                            <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>

                        <input type="number" name="promotion_bid" min="5" max="20" class="form-control mx-1"
                               value="{{ optional($restaurant->currentBid)->bid_percentage }}" placeholder="5-20">

                        <input type="number" name="gift_point_percentage" min="2" max="10" class="form-control mx-1"
                               value="{{ optional($restaurant->currentGiftPoint)->gift_point_percentage }}" placeholder="2-10">

                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="page-area px-4 pb-3">
    {{ $restaurants->links() }}
</div>
