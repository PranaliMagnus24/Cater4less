@extends('layouts.admin.app')

@section('title', translate('messages.Edit_Gift_Card'))

@section('content')
<style>
.image-box.banner2 {
  position: relative;
  width: 100%;
  max-width: 340px;
  min-height: 180px;
  border: 1px dashed #cbd5e1;
  border-radius: 12px;
  overflow: hidden;
}
.image-box.banner2 .preview-image {
  display: block;
  max-height: 240px;
  width: 100%;
  object-fit: contain;
}
.image-box.banner2 .upload-icon,
.image-box.banner2 .upload-text {
  display: none;
}
</style>

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="tio-gift"></i></div>
                    {{ translate('messages.Edit_Gift_Card') }}
                </h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="gift-card-edit-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.amount') }} ($)</label>
                            <input type="number" step="0.01" name="amount"
                                value="{{ $giftCard->amount }}"
                                class="form-control h--45px" required>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.balance') }} ($)</label>
                            <input type="number" step="0.01" name="balance"
                                value="{{ $giftCard->balance }}"
                                class="form-control h--45px" required>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.expiry_date') }}</label>
                            <input type="date" name="expiry_date"
                                value="{{ $giftCard->expiry_date }}"
                                class="form-control h--45px">
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.status') }}</label>
                            <select name="status" class="form-control h--45px">
                                <option value="active" {{ $giftCard->status == 'active' ? 'selected' : '' }}>
                                    {{ translate('messages.active') }}
                                </option>
                                <option value="redeemed" {{ $giftCard->status == 'redeemed' ? 'selected' : '' }}>
                                    {{ translate('messages.redeemed') }}
                                </option>
                                <option value="expired" {{ $giftCard->status == 'expired' ? 'selected' : '' }}>
                                    {{ translate('messages.expired') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="d-flex flex-column align-items-center gap-3 mt-4">
                            <p class="mb-0">{{ translate('gift_card_image') }}</p>
                            <div class="image-box banner2">
                                <label for="image-input" class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                    @if(!$giftCard->image)
                                    <img width="30" class="upload-icon" src="{{ dynamicAsset('public/assets/admin/img/upload-icon.png') }}" alt="Upload Icon">
                                    <span class="upload-text">{{ translate('Upload Image')}}</span>
                                    @endif
                                    <img src="{{ $giftCard->image_full_url }}" alt="Preview Image" class="preview-image">
                                </label>
                                <button type="button" class="delete_image">
                                    <i class="tio-delete"></i>
                                </button>
                                <input type="file" id="image-input" name="image" accept="image/*" hidden>
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                    <button type="submit" class="btn btn--primary">{{ translate('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
"use strict";

document.getElementById("image-input").addEventListener("change", function (event) {
    let reader = new FileReader();
    reader.onload = function () {
        document.querySelector(".preview-image").src = reader.result;
        document.getElementById("remove_image").value = "0";
    };
    if (event.target.files && event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
});

document.querySelector(".delete_image").addEventListener("click", function () {
    document.querySelector(".preview-image").src = "{{ \Illuminate\Support\Facades\Storage::disk('public')->url('gift-card/default.png') }}";
    document.getElementById("image-input").value = "";
    document.getElementById("remove_image").value = "1";
});



// AJAX Update
$('#gift-card-edit-form').on('submit', function (e) {
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('admin.gift_cards.update', $giftCard->id) }}",
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(data.message, { CloseButton: true, ProgressBar: true });
            setTimeout(function () {
                location.href = '{{ route('admin.gift_cards.index') }}';
            }, 1200);
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errs = xhr.responseJSON.errors;
                if (Array.isArray(errs)) {
                    errs.forEach(e => toastr.error(e.message || 'Validation error'));
                } else {
                    $.each(errs, function (key, value) { toastr.error(value[0]); });
                }
            } else {
                toastr.error('Something went wrong');
            }
        }
    });
});

</script>
@endpush
