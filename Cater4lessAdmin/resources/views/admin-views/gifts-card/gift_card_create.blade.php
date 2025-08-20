@extends('layouts.admin.app')

@section('title', translate('messages.Gift_Card'))

@push('css_or_js')
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="tio-gift"></i></div>
                    {{ translate('messages.Add_New_Gift_Card') }}
                </h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.gift_cards.store') }}" method="post" enctype="multipart/form-data" id="gift-card-form">
                @csrf

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.amount') }} (₹)</label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control h--45px" required>
                            @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.balance') }} (₹)</label>
                            <input type="number" step="0.01" name="balance" value="{{ old('balance') }}" class="form-control h--45px" required>
                            @error('balance') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.expiry_date') }}</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-control h--45px">
                            @error('expiry_date') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.status') }}</label>
                            <select name="status" class="form-control h--45px">
                                <option value="active">{{ translate('messages.active') }}</option>
                                <option value="redeemed">{{ translate('messages.redeemed') }}</option>
                                <option value="expired">{{ translate('messages.expired') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="d-flex flex-column align-items-center gap-3 mt-4">
                            <p class="mb-0">{{ translate('gift_card_image') }}</p>

                            <div class="image-box banner2">
                                <label for="image-input" class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                    <img width="30" class="upload-icon" src="{{ dynamicAsset('public/assets/admin/img/upload-icon.png') }}" alt="Upload Icon">
                                    <span class="upload-text">{{ translate('Upload Image')}}</span>
                                    <img src="#" alt="Preview Image" class="preview-image">
                                </label>
                                <button type="button" class="delete_image">
                                    <i class="tio-delete"></i>
                                </button>
                                <input type="file" id="image-input" name="image" accept="image/*" hidden>
                            </div>

                            <p class="opacity-75 max-w220 mx-auto text-center">
                                {{ translate('Image format - jpg png jpeg gif | Max size - 2 MB | Recommended ratio - 2:1') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                    <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
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
    };
    reader.readAsDataURL(event.target.files[0]);
});

document.querySelector(".delete_image").addEventListener("click", function () {
    document.querySelector(".preview-image").src = "";
    document.getElementById("image-input").value = "";
});


$('#gift-card-form').on('submit', function (e) {
    e.preventDefault();
    let formData = new FormData(this);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.post({
        url: '{{ route('admin.gift_cards.store') }}',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.errors) {
                for (let i = 0; i < data.errors.length; i++) {
                    toastr.error(data.errors[i].message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            } else {
                toastr.success(data.message, {
                    CloseButton: true,
                    ProgressBar: true
                });
                setTimeout(function () {
                    location.href = '{{ route('admin.gift_cards.index') }}';
                }, 1500);
            }
        }
    });
});

$('#reset_btn').click(function () {
    $('#gift-card-form')[0].reset();
});

</script>
@endpush
