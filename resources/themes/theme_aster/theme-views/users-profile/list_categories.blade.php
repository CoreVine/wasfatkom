@php use App\Utils\Helpers; @endphp
@extends('theme-views.layouts.app')

@section('title', translate('choise_category').' | '.$web_config['name']->value.' '.translate('ecommerce'))

@section('content')
    <main class="main-content d-flex flex-column gap-3 py-3 mb-4">
        <div class="container">
            <div class="row g-3">
                @include('theme-views.partials._profile-aside')
                <div class="col-lg-9">
                    <div class="card h-100">
                        <div class="card-body p-lg-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                 <h5 class="text-capitalize">{{translate('select_category')}}</h5>
                                 </div>
                        <div class="mt-4">
                         <div class="col-5 ">
                            <ul class="dropdown-menu dropdown-menu--static bs-dropdown-min-width--auto">
                                @foreach($categories as $key=>$category)
                                    <li class="{{ $category->childes->count() > 0 ? 'menu-item-has-children' : '' }}">
                                         @if ($category->childes->isEmpty())
                                        <a href="{{route('account-products-create',['category_id'=> $category['id']])}}">
                                            {{$category['name']}}
                                        </a>
                                        @else
                                         {{$category['name']}}
                                        @endif
                                        @if ($category->childes->count() > 0)
                                            <ul class="sub-menu">
                                                @foreach($category['childes'] as $subCategory)
                                                    <li class="{{ $subCategory->childes->count()>0 ? 'menu-item-has-children' : '' }}">
                                                         @if ($subCategory->childes->isEmpty())
                                                        <a href="{{route('account-products-create',['category_id'=>$category['id'],'sub_category_id'=> $subCategory['id']])}}">
                                                            {{$subCategory['name']}}
                                                        </a>
                                                          @else
                                                             {{$subCategory['name']}}
                                                            @endif
                                                        @if($subCategory->childes->count()>0)
                                                            <ul class="sub-menu">
                                                                @foreach($subCategory['childes'] as $subSubCategory)
                                                                    <li>
                                                                        
                                                                        <a href="{{route('account-products-create',['category_id'=>$category['id'],'sub_category_id'=> $subCategory['id'],'sub_sub_category_id'=> $subSubCategory['id']])}}">
                                                                            {{$subSubCategory['name']}}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                              
                            </ul>
                        </div>
                             </div>
                               </div>
                                  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
