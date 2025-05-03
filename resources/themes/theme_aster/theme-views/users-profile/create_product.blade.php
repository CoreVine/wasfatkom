@php use App\Utils\Helpers; @endphp
@extends('theme-views.layouts.app')
@push('css_or_js')

    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/google-fonts.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/custom.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/style.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/toastr.css')}}">
   
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
    <style>
body[theme="dark"] .note-editor.note-frame .note-editing-area .note-editable {
    color: white !important;
}
body[theme="dark"] .note-editor .note-toolbar,
body[theme="dark"] .note-popover .popover-content {
    background: white !important;
}
    </style>
@endpush

@section('title', translate('my_products_list').' | '.$web_config['name']->value.' '.translate('ecommerce'))

@section('content')
    <main class="main-content d-flex flex-column gap-3 py-3 mb-4">
        <div class="container">
            <div class="row g-3">
                @include('theme-views.partials._profile-aside')
                <div class="col-lg-9">
                    <div class="card h-100">
                        <div class="card-body p-lg-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <h5 class="text-capitalize">{{translate('add_new_product')}}</h5>

                            </div>
                            <div class="mt-4">
                            <div class="">
                            <form class="product-form text-start" action="{{route('account-products-store')}}"
                                  method="POST" enctype="multipart/form-data"  id="product_form">
                                @csrf
                                    <div class="card">
                <div class="px-4 pt-3 d-flex justify-content-between">
                    <ul class="nav nav-tabs w-fit-content mb-4">
                        @foreach ($languages as $lang)
                        @if($lang==$defaultLanguage)
                            <li class="nav-item">
                                <span class="nav-link text-capitalize form-system-language-tab {{ $lang == $defaultLanguage ? 'active' : '' }} cursor-pointer"
                                      id="{{ $lang }}-link"></span>
                            </li>
                            @endif
                        @endforeach
                    </ul>
           
                </div>

                <div class="card-body">
                                    @foreach ($languages as $lang)
                                        <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                             id="{{ $lang }}-form">
                                            <div class="form-group">
                                                <label class="title-color"
                                                       for="{{ $lang }}_name">{{ translate('product_name') }}
                                                   
                                                </label>
                                                <input type="text" {{ $lang == $defaultLanguage ? '' : '' }} name="name[]"
                                                       id="{{ $lang }}_name" class="form-control" placeholder="New Product">
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                            <div class="form-group pt-4">
                                                <label class="title-color"
                                                       for="{{ $lang }}_description">{{ translate('description') }}
                                                   </label>
                                                <textarea name="description[]" class="summernote" style="color: blue !important;background:red !important; ">{{ old('details') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                                        <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">{{ translate('general_setup') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                       <input type="hidden" name="category_id" value="{{$request->category_id}}">
                            <input type="hidden" name="sub_category_id" value="{{$request->sub_category_id}}">
                               <input type="hidden" name="category_id" value="{{$request->category_id}}">
                            <input type="hidden" name="sub_sub_category_id" value="{{$request->sub_sub_category_id}}">

                        @if($brandSetting)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label class="title-color">{{ translate('brand') }}</label>
                                    <select class="js-select2-custom form-control" name="brand_id"  >
                                        <option value="{{ null }}" selected
                                                disabled>{{ translate('select_Brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand['id'] }}">{{ $brand['defaultName'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6 col-lg-4 col-xl-3 d-none">
                            <div class="form-group">
                                <label class="title-color">{{ translate('product_type') }}</label>
                                <select name="product_type" id="product_type" class="form-control"  >
                                    <option value="physical" selected>{{ translate('physical') }}</option>
                                 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3" id="digital_product_type_show">
                            <div class="form-group">
                                <label for="digital_product_type"
                                       class="title-color">{{ translate("delivery_type") }}</label>
                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                      title="{{ translate('for_“Ready_Product”_deliveries,_customers_can_pay_&_instantly_download_pre-uploaded_digital_products._For_“Ready_After_Sale”_deliveries,_customers_pay_first_then_vendor_uploads_the_digital_products_that_become_available_to_customers_for_download') }}">
                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                </span>
                                <select name="digital_product_type" id="digital_product_type" class="form-control"
                                         >
                                    <option value="{{ old('category_id') }}" selected disabled>
                                        ---{{ translate('select') }}---
                                    </option>
                                    <option value="ready_after_sell">{{ translate("ready_After_Sell") }}</option>
                                    <option value="ready_product">{{ translate("ready_Product") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3" id="digital_file_ready_show">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <label for="digital_file_ready" class="title-color mb-0">
                                        {{ translate("upload_file") }}
                                    </label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('upload_the_digital_products_from_here') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="digital_file_ready"
                                               id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                                        <label class="custom-file-label"
                                               for="inputGroupFile01">{{ translate('choose_file') }}</label>
                                    </div>
                                </div>

                                <div class="mt-2">{{ translate('file_type').': jpg, jpeg, png, gif, zip, pdf' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color d-flex justify-content-between gap-2">
                                    <span class="d-flex align-items-center gap-2">
                                        {{ translate('phone') }}
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                              title="{{ translate('create_a_unique_product_code_by_clicking_on_the_Generate_Code_button') }}">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                 alt="">
                                        </span>
                                    </span>
                                   
                                </label>
                                <input type="text" minlength="6" id="generate_number" name="code"
                                       class="form-control" value="{{ old('code') }}"
                                       placeholder="{{ translate('123412') }}"  >
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 physical_product_show">
                            <div class="form-group">
                                <label class="title-color">{{ translate('unit') }}</label>
                                <select class="js-example-basic-multiple form-control" name="unit">
                                    @foreach (units() as $unit)
                                        <option value="{{ $unit }}" {{ old('unit') == $unit ? 'selected' : '' }}>
                                            {{ $unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                             <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <i class="tio-user-big"></i>
                        <h4 class="mb-0">{{ translate('pricing_&_others') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-6 col-lg-4 col-xl-3 d-none">
                            <div class="form-group">
                                <div class="d-flex gap-2 mb-2">
                                    <label class="title-color mb-0">{{ translate('purchase_price') }}
                                        ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})
                                    </label>
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('add_the_purchase_price_for_this_product') }}.">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>
                                <input type="number" min="0" step="0.01"
                                       placeholder="{{ translate('purchase_price') }}"
                                       value="{{ old('purchase_price') }}" name="purchase_price"
                                       class="form-control"  >
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <div class="d-flex gap-2 mb-2">
                                    <label class="title-color mb-0">{{ translate('unit_price') }}
                                        ({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('set_the_selling_price_for_each_unit_of_this_product._This_Unit_Price_section_won’t_be_applied_if_you_set_a_variation_wise_price') }}.">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>
                                <input type="number" min="0" step="0.01"
                                       placeholder="{{ translate('unit_price') }}" name="unit_price"
                                       value="{{ old('unit_price') }}" class="form-control"  >
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="minimum_order_qty">
                            <div class="form-group">
                                <div class="d-flex gap-2 mb-2">
                                    <label class="title-color mb-0"
                                           for="minimum_order_qty">{{ translate('minimum_order_qty') }}</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('set_the_minimum_order_quantity_that_customers_must_choose._Otherwise,_the_checkout_process_won’t_start') }}.">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>

                                <input type="hidden" min="1" 
                                        value="1" name="minimum_order_qty"
                                       id="minimum_order_qty" class="form-control"  >
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 physical_product_show d-none" id="quantity">
                            <div class="form-group">
                                <div class="d-flex gap-2 mb-2">
                                    <label class="title-color mb-0"
                                           for="current_stock">{{ translate('current_stock_qty') }}</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('add_the_Stock_Quantity_of_this_product_that_will_be_visible_to_customers') }}.">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>

                                <input type="hidden" value="1"
                                       placeholder="{{ translate('quantity') }}" name="current_stock" id="current_stock"
                                       class="form-control"  >
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <div class="d-flex gap-2 mb-2">
                                    <label class="title-color mb-0" for="discount_Type">{{ translate('discount_Type') }}</label>
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('if_Flat,_discount_amount_will_be_set_as_fixed_amount._If_Percentage,_discount_amount_will_be_set_as_percentage.') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>

                                <select class="form-control" name="discount_type" id="discount_type">
                                    <option value="flat">{{ translate('flat') }}</option>
                                    <option value="percent">{{ translate('percent') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <div class="d-flex gap-2">
                                    <label class="title-color" for="discount">{{ translate('discount_amount') }} <span
                                            class="discount_amount_symbol">({{ getCurrencySymbol(currencyCode: getCurrencyCode()) }})</span></label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('add_the_discount_amount_in_percentage_or_a_fixed_value_here') }}.">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>
                                <input type="number" min="0" value="0" step="0.01"
                                       placeholder="{{ translate('ex: 5') }}"
                                       name="discount" id="discount" class="form-control"  >
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 d-none">
                            <div class="form-group">
                                <div class="d-flex gap-2">
                                    <label class="title-color" for="tax">{{ translate('tax_amount') }}(%)</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('set_the_Tax_Amount_in_percentage_here') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>

                                <input type="number" min="0" step="0.01"
                                       placeholder="{{ translate('ex: 5') }}" name="tax" id="tax"
                                       value="{{ old('tax') ?? 0 }}" class="form-control">
                                <input name="tax_type" value="percent" class="d-none">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 d-none">
                            <div class="form-group">
                                <div class="d-flex gap-2">
                                    <label class="title-color"
                                           for="tax_model">{{ translate('tax_calculation') }}</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('set_the_tax_calculation_method_from_here._Select_“Include_with_product”_to_combine_product_price_and_tax_on_the_checkout._Pick_“Exclude_from_product”_to_display_product_price_and_tax_amount_separately.') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>
                                <select name="tax_model" id="tax_model" class="form-control"  >
                                    <option value="include">{{ translate("include_with_product") }}</option>
                                    <option value="exclude">{{ translate("exclude_with_product") }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 physical_product_show d-none" id="shipping_cost">
                            <div class="form-group">
                                <div class="d-flex gap-2">
                                    <label class="title-color">{{ translate('shipping_cost') }}
                                        ({{ getCurrencySymbol(currencyCode: getCurrencyCode())  }})</label>

                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('set_the_shipping_cost_for_this_product_here._Shipping_cost_will_only_be_applicable_if_product-wise_shipping_is_enabled.') }}">
                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                    </span>
                                </div>

                                <input type="number" min="0" value="0" step="1"
                                       placeholder="{{ translate('shipping_cost') }}" name="shipping_cost"
                                       class="form-control" >
                            </div>
                        </div>

                        <div class="col-md-6 physical_product_show d-none" id="shipping_cost_multy">
                            <div class="form-group">
                                <div
                                    class="form-control h-auto min-form-control-height d-flex align-items-center flex-wrap justify-content-between gap-2">
                                    <div class="d-flex gap-2">
                                        <label class="title-color text-capitalize"
                                               for="shipping_cost">{{ translate('shipping_cost_multiply_with_quantity') }}</label>

                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                              title="{{ translate('if_enabled,_the_shipping_charge_will_increase_with_the_product_quantity') }}">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                 alt="">
                                        </span>
                                    </div>

                                    <div>
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher_input" name="multiply_qty">
                                            <span class="switcher_control"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="title-color d-flex align-items-center gap-2">
                                    {{ translate('search_tags') }}
                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                          title="{{ translate('add_the_product_search_tag_for_this_product_that_customers_can_use_to_search_quickly') }}">
                                        <img width="16" src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                             alt="">
                                    </span>
                                </label>
                                <input type="text" class="form-control" placeholder="{{ translate('enter_tag') }}"
                                       name="tags" data-role="tagsinput">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    <div class="mt-3 rest-part">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <div>
                                            <label for="name"
                                                   class="title-color text-capitalize font-weight-bold mb-0">{{ translate('product_thumbnail') }}</label>
                                            <span
                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                  title="{{ translate('add_your_product’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                     alt="">
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="custom_upload_input">
                                            <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id=""
                                                   data-imgpreview="pre_img_viewer"
                                                   accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                <img id="pre_img_viewer" class="h-auto aspect-1 bg-white d-none"
                                                     src="" alt="">
                                            </div>
                                            <div
                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div
                                                    class="d-flex flex-column justify-content-center align-items-center">
                                                    <img alt="" class="w-75"
                                                         src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="text-muted mt-2">
                                            {{ translate('image_format') }} : {{ "Jpg, png, jpeg, webp," }}
                                            <br>
                                            {{ translate('image_size') }} : {{ translate('max') }} {{ "2 MB" }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="color_image_column col-md-9 d-none">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <label for="name"
                                                   class="title-color text-capitalize font-weight-bold mb-0">{{ translate('colour_wise_product_image') }}</label>
                                            <span
                                                class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                                  title="{{ translate('add_color-wise_product_images_here') }}.">
                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                     alt="">
                                            </span>
                                        </div>

                                    </div>
                                    <p class="text-muted">{{ translate('must_upload_colour_wise_images_first._Colour_is_shown_in_the_image_section_top_right') }}
                                        . </p>

                                    <div id="color-wise-image-section" class="row g-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="additional_image_column col-md-9">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                    <div>
                                        <label for="name"
                                               class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                        <span
                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                              title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}.">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                        </span>
                                    </div>

                                </div>
                                <p class="text-muted">{{ translate('upload_additional_product_images') }}</p>

                                <div class="row g-2" id="additional_Image_Section">
                                    <div class="col-sm-12 col-md-4">
                                        <div class="custom_upload_input position-relative border-dashed-2 aspect-1">
                                            <input type="file" name="images[]" class="custom-upload-input-file action-add-more-image"
                                                   data-index="1" data-imgpreview="additional_Image_1"
                                                   accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                   data-target-section="#additional_Image_Section"
                                            >

                                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none "
                                                     src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" alt="">
                                            </div>
                                            <div
                                                class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div
                                                    class="d-flex flex-column justify-content-center align-items-center">
                                                    <img alt=""
                                                         src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                         class="w-75">
                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
               <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ translate('reset') }}</button>
                <button type="button" class="btn btn--primary px-5 product-add-requirements-check">{{ translate('submit') }}</button>
            </div>
        </form>
        
                </div>
                            </div>
                              <span id="route-vendor-products-sku-combination" data-url="{{ route('vendor.products.sku-combination') }}"></span>
    <span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-yes-word" data-text="{{ translate('yes') }}"></span>
    <span id="message-no-word" data-text="{{ translate('no') }}"></span>
    <span id="message-want-to-add-or-update-this-product" data-text="{{ translate('want_to_add_this_product') }}"></span>
    <span id="message-please-only-input-png-or-jpg" data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-product-added-successfully" data-text="{{ translate('product_added_successfully') }}"></span>
    <span id="message-discount-will-not-larger-then-variant-price" data-text="{{ translate('the_discount_price_will_not_larger_then_Variant_Price') }}"></span>
    <span id="system-currency-code" data-value="{{ getCurrencySymbol(currencyCode: getCurrencyCode()) }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@push('script')
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor.min.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/sweet_alert.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/custom.js')}}"></script>
<script src="{{dynamicAsset(path: 'public/assets/back-end/js/app-script.js')}}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/common-script.js') }}"></script>

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor/product-add-update.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/vendor/product-add-colors-img.js') }}"></script>
@endpush
