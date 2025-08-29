@extends('layouts.app')
@section('title', 'Home')
@section('content')
<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="row">
                <!-- total customers -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('user.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-purple d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-users text-purple" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$customer}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Customers</p>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- total categories -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('category.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-blue d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-th-large text-blue" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$category}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Categories</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- total products -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('product.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-cyan d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-boxes text-cyan" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$product}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Product</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- total order -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('order.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-green d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-shopping-cart text-green" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$order}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Order</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Pending Order -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('order.index', ['status' => '1']) }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-yellow d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-clock text-yellow" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$pending}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Pending Order</p>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Total reject order -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('order.index', ['status' => '3']) }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-red d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-times-circle text-red" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$reject}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Reject Order</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Active Order -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('order.index', ['status' => '2']) }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-gray d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-check-circle text-gray" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$active}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Active Order</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Dispatch Order -->
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <a href="{{ route('order.index', ['status' => '4']) }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3" style="border-radius: 12px;">
                            <div class="icon-circle bg-light-orange d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                <i class="fas fa-truck text-orange" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-0 text-dark">{{$dispatch}}</h4>
                                <p class="font-weight-bold mb-0 text-dark">Total Dispatch Order</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->
@endsection

@section('pagescript')

<style>
    .card {
        background: #ffffff;
        border-radius: 12px;
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-light-purple {
        background: #F4E8FF;
    }

    .text-purple {
        color: #7F5AF0;
    }

    .bg-light-blue {
        background: #E6F3FF;
    }

    .text-blue {
        color: #4A90E2;
    }

    .bg-light-red {
        background: #FFE6E6;
    }

    .text-red {
        color: #E74C3C;
    }

    .bg-light-green {
        background: #E6F9E6;
    }

    .text-green {
        color: #28C76F;
    }

    .bg-light-yellow {
        background: #FFF3CD;
    }

    .text-yellow {
        color: #FFC107;
    }

    .bg-light-cyan {
        background: #E0F7FA;
    }

    .text-cyan {
        color: #17A2B8;
    }

    .bg-light-gray {
        background: #E9ECEF;
    }

    .text-gray {
        color: #6C757D;
    }

    .bg-light-orange {
        background: #FFE5D9;
    }

    .text-orange {
        color: #E67E22;
    }
</style>

@endsection