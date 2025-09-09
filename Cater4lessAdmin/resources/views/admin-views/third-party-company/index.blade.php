@extends('layouts.admin.app')

@section('title', translate('Add_New_Company'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ dynamicAsset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush
<style>
    .upload-box,
.preview-wrapper {
    width: 120px;
    height: 120px;
    border: 2px dashed #ccc;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    background: #f9f9f9;
}

.preview-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-wrapper .delete-btn {
    position: absolute;
    top: 6px;
    right: 6px;
    background: rgba(255,0,0,0.85);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 14px;
    line-height: 22px;
    cursor: pointer;
}


</style>

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i>
                        {{ translate('messages.Add_New_Company') }}</h1>
                </div>
            </div>
        </div>

        <!-- End Page Header -->
        <form action="javascript:" method="post" id="company_form" enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-body pb-0">
                            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = str_replace('_', '-', app()->getLocale()))
                            @if ($language)
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active" href="#"
                                                id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link " href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        @if ($language)
                            <div class="card-body">

                                <div class="lang_form" id="default-form">

                                    <div class="form-group">
                                        <label class="input-label"
                                            for="default_name">{{ translate('messages.company_name') }}
                                            ({{ translate('Default') }}) <span class="form-label-secondary text-danger"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Required.') }}"> *
                                            </span>
                                        </label>
                                        <input type="text" name="company_name" id="default_name" class="form-control"
                                            placeholder="{{ translate('messages.add_company_name') }}">
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    {{-- <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('messages.short_description') }}
                                        ({{ translate('Default') }}) <span class="form-label-secondary text-danger"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.Required.')}}"> *
                                        </span></label>
                                    <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px"></textarea>
                                </div> --}}
                                </div>

                                @foreach (json_decode($language) as $lang)
                                    <div class="d-none lang_form" id="{{ $lang }}-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="{{ $lang }}_name">{{ translate('messages.company_name') }}
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="company_name" id="{{ $lang }}_name"
                                                class="form-control"
                                                placeholder="{{ translate('messages.add_company_name') }}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        {{-- <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.short_description') }}
                                                ({{ strtoupper($lang) }})</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px"></textarea>
                                        </div> --}}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="card-body">
                                <div id="default-form">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.company_name') }}
                                            ({{ translate('Default') }})</label>
                                        <input type="text" name="company_name" class="form-control"
                                            placeholder="{{ translate('messages.add_company_name') }}">
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    {{-- <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.short_description') }}</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px"></textarea>
                                    </div> --}}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center gap-3">
                                <p class="mb-0">{{ translate('Company_Logo') }}</p>

                                <div class="image-box">
                                    <label for="image-input"
                                        class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                        <img width="30" class="upload-icon"
                                            src="{{ dynamicAsset('public/assets/admin/img/upload-icon.png') }}"
                                            alt="Upload Icon">
                                        <span class="upload-text">{{ translate('Upload Image') }}</span>
                                        <img src="#" alt="Preview Image" class="preview-image">
                                    </label>
                                    <button type="button" class="delete_image">
                                        <i class="tio-delete"></i>
                                    </button>
                                    <input type="file" id="image-input" name="image" accept="image/*" hidden>
                                </div>

                                <p class="opacity-75 max-w220 mx-auto text-center">
                                    {{ translate('Image format - jpg png jpeg gif Image Size -maximum size 2 MB Image Ratio - 1:1') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span> {{ translate('Add_Phone_&_Email') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6 col-lg-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">{{ translate('messages.Email') }} <span
                                                class="form-label-secondary text-danger" data-toggle="tooltip"
                                                data-placement="right"
                                                data-original-title="{{ translate('messages.Required.') }}"> *
                                            </span><span class="input-label-secondary"></span></label>
                                        <input type="email" class="form-control" name="company_email"
                                            placeholder="Enter comapny email" data-role="emailsinput">
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">{{ translate('messages.Phone') }} <span
                                                class="form-label-secondary text-danger" data-toggle="tooltip"
                                                data-placement="right"
                                                data-original-title="{{ translate('messages.Required.') }}"> *
                                            </span><span class="input-label-secondary"></span></label>
                                        <input type="text" class="form-control" name="company_phone"
                                            placeholder="Enter comapny phone" data-role="phoneinput">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-sm-6 col-lg-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">{{ translate('messages.company_type') }} <span
                                                class="form-label-secondary text-danger" data-toggle="tooltip"
                                                data-placement="right"
                                                data-original-title="{{ translate('messages.Required.') }}"> *
                                            </span><span class="input-label-secondary"></span></label>
                                            <select name="company_type" class="form-control js-select2-custom h--45px" required
                                            data-placeholder="{{ translate('messages.select_comapny_type') }}">
                                            <option value="" readonly="true" hidden="true">{{ translate('Ex:_XYZ_comapny') }}</option>
                                            <option value="Delivery Partner">Delivery Partner</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-home-vs"></i></span>
                                <span>{{ translate('address') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-control" name="company_address"
                                placeholder="Enter comapny address">
                        </div>
                    </div>
                </div>
                <!-- Company Documents -->
<div class="col-lg-12">
    <div class="card shadow--card-2 border-0 h-100">
        <div class="card-body">
            <div class="d-flex flex-column align-items-center gap-3">
                <p class="mb-0">{{ translate('Company_Documents') }}</p>

                <div class="d-flex flex-wrap gap-3 documents-preview"></div>

                <label for="company_documents"
                       class="upload-box cursor-pointer">
                    <img width="30" class="upload-icon"
                         src="{{ dynamicAsset('public/assets/admin/img/upload-icon.png') }}"
                         alt="Upload Icon">
                    <span class="upload-text">{{ translate('Upload Image') }}</span>
                </label>
                <input type="file" id="company_documents" name="company_documents[]" accept="image/*" multiple hidden>

                <p class="opacity-75 max-w220 mx-auto text-center">
                    {{ translate('Image format - jpg png jpeg gif Image Size - maximum size 2 MB Image Ratio - 1:1') }}
                </p>
            </div>
        </div>
    </div>
</div>


                <div class="col-lg-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection


@push('script_2')
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/view-pages/product-index.js"></script>
    <script>
        "use strict";

        ///Store company code
        $('#company_form').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.post({
                url: '{{ route('admin.third-party-company.store') }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{ translate('messages.company_added_successfully') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function() {
                            location.href = '{{ route('admin.third-party-company.list') }}';
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    $('#loading').hide();
                    toastr.error('Something went wrong!', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        ///Name display
        document.addEventListener("DOMContentLoaded", function() {
            let defaultInput = document.getElementById("default_name");
            let englishInput = document.getElementById("en_name");

            if (defaultInput && englishInput) {
                englishInput.value = defaultInput.value;
                defaultInput.addEventListener("input", function() {
                    englishInput.value = defaultInput.value;
                });
            }
        });

        ///Company Documents preview
       let selectedDocuments = [];

document.getElementById('company_documents').addEventListener('change', function (e) {
    let previewBox = document.querySelector('.documents-preview');
    let files = Array.from(e.target.files);

    selectedDocuments = selectedDocuments.concat(files);
    previewBox.innerHTML = "";

    selectedDocuments.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            let reader = new FileReader();
            reader.onload = function (event) {
                let wrapper = document.createElement('div');
                wrapper.classList.add('preview-wrapper');

                let img = document.createElement('img');
                img.src = event.target.result;

                let delBtn = document.createElement('button');
                delBtn.innerHTML = "🗑";
                delBtn.classList.add('delete-btn');
                delBtn.onclick = function () {
                    selectedDocuments.splice(index, 1);
                    updateFileInput();
                    wrapper.remove();
                };

                wrapper.appendChild(img);
                wrapper.appendChild(delBtn);
                previewBox.appendChild(wrapper);
            }
            reader.readAsDataURL(file);
        }
    });

    updateFileInput();
});

function updateFileInput() {
    let dt = new DataTransfer();
    selectedDocuments.forEach(file => dt.items.add(file));
    document.getElementById('company_documents').files = dt.files;
}

    </script>
@endpush
