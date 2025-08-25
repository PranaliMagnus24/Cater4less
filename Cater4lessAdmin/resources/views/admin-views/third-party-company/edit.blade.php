@extends('layouts.admin.app')

@section('title', translate('Edit_Company'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ dynamicAsset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>
                        {{ translate('messages.Edit_Company') }}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <form action="javascript:" method="post" id="company_edit_form" enctype="multipart/form-data">
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
                                            <a class="nav-link lang_link active" href="#" id="default-link">
                                                {{ translate('Default') }}
                                            </a>
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

                        <div class="card-body">
                            <div class="lang_form" id="default-form">
                                <div class="form-group">
                                    <label class="input-label" for="default_name">
                                        {{ translate('messages.company_name') }} ({{ translate('Default') }})
                                    </label>
                                    <input type="text" name="company_name" id="default_name" class="form-control"
                                        value="{{ $company->company_name }}"
                                        placeholder="{{ translate('messages.add_company_name') }}">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            </div>
                            @foreach (json_decode($language) as $lang)
                                <div class="d-none lang_form" id="{{ $lang }}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="{{ $lang }}_name">
                                            {{ translate('messages.company_name') }} ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" name="company_name_{{ $lang }}" id="{{ $lang }}_name"
                                            class="form-control"
                                            value="{{ $company->company_name }}"
                                            placeholder="{{ translate('messages.add_company_name') }}">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center gap-3">
                                <p class="mb-0">{{ translate('Company_Logo') }} </p>
                                <div class="image-box">
                                    <label for="image-input" class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                    <img class="upload-icon initial-26" src="{{ $company->image_full_url }}" alt="Upload Icon">
                                    <img src="#" alt="Preview Image" class="preview-image">
                                    </label>
                                    <button type="button" class="delete_image">
                                    <i class="tio-delete"></i>
                                    </button>
                                    <input type="file" id="image-input" name="image" accept="image/*" hidden>
                                </div>

                                <p class="opacity-75 max-w220 mx-auto text-center">
                                    {{ translate('Image format - jpg png jpeg gif Image Size -maximum size 2 MB Image Ratio - 1:1')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Email & Phone -->
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-dashboard-outlined"></i></span>
                                <span> {{ translate('Add_Phone_&_Email') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{ translate('messages.Email') }}</label>
                                        <input type="email" class="form-control" name="company_email"
                                            value="{{ $company->company_email }}"
                                            placeholder="Enter company email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{ translate('messages.Phone') }}</label>
                                        <input type="text" class="form-control" name="company_phone"
                                            value="{{ $company->company_phone }}"
                                            placeholder="Enter company phone">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address -->
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
                                value="{{ $company->company_address }}"
                                placeholder="Enter company address">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="col-lg-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.update') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        // Update Company
        $('#company_edit_form').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: '{{ route('admin.third-party-company.update', $company->id) }}',
                type: 'POST',
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
                        data.errors.forEach(function(error) {
                            toastr.error(error.message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        });
                    } else {
                        toastr.success('{{ translate('messages.company_updated_successfully') }}', {
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
    </script>
@endpush
