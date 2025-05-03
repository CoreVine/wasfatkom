@php use App\Utils\Helpers; @endphp
@extends('theme-views.layouts.app')

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
                                <h5 class="text-capitalize">{{translate('my_products_list')}}</h5>
                                <a class="btn btn-sm btn-primary d-inline" href="{{route('account-get_categories')}}">+</a>

                                <div class="border rounded  custom-ps-3 py-2">
                                    <div class="d-flex gap-2">
                                        <div class="flex-middle gap-2">

                                            <i class="bi bi-sort-up-alt"></i>
                                            <span
                                            
                                                class="d-none d-sm-inline-block text-capitalize">{{translate('show_product').':'}}</span>
                                        </div>
                                        <div class="dropdown">
                                            <button type="button"
                                                    class="border-0 bg-transparent dropdown-toggle text-dark p-0 custom-pe-3"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                {{translate($order_by=='asc'?'old':'latest')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="d-flex" href="{{route('account-my-products')}}/?order_by=desc">
                                                        {{translate('latest')}}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="d-flex" href="{{route('account-my-products')}}/?order_by=asc">
                                                        {{translate('old')}}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                @if($products->count() > 0)
                                    <div class="table-responsive d-sm-block">
                                        <table class="table align-middle table-striped">
                                            <thead class="text-primary">
                                            <tr>
                                            <th>{{ translate('SL') }}</th>
                                        <th class="text-capitalize">{{ translate('product Name') }}</th>
                                        <th class="text-center">{{ translate('unit_price') }}</th>
                                        <th class="text-center">{{ translate('action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                    @foreach($products as $key=>$product)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>
                                  
                                        <a href="#"
                                           class="media align-items-center gap-2">
                                            <img src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/'.$product['thumbnail'], type: 'backend-product') }}"
                                                 class="avatar border" alt="">
                                            <span class="media-body title-color hover-c1">
                                            {{ Str::limit($product['name'], 20) }}
                                        </span>
                                        </a>
                               
                                    </td>
                             
                                    <td class="text-center">
                                        {{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $product['unit_price']), currencyCode: getCurrencyCode()) }}
                                    </td>
                                <td class="text-center">    
                                <a class="btn btn-outline-danger btn-sm square-btn"
                                               title="{{ translate('delete') }}"
                                               href="{{ route('account-products-delete',[$product['id']]) }}">
                                            X
                                            </a>
                                            </td>
                             
                                      </tr>
                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                @endif

                                @if($products->count()==0)
                                    <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-5 mt-5 w-100">
                                        <img width="80" class="mb-3" src="{{ theme_asset('assets/img/empty-state/empty-order.svg') }}" alt="">
                                        <h5 class="text-center text-muted">
                                            {{ translate('You_have_not_any_order_yet') }}!
                                        </h5>
                                    </div>
                                @endif

                                @if($products->count()>0)
                                    <div class="card-footer border-0">
                                        {{$products->links() }}
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
