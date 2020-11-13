@extends('organization.layouts.master_employee')
@section('title','ورود')
@section('css')
@endsection
@section('content')
    <div class="page">
        <div class="page-single">
            <div class="p-5">
                <div class="row">
                    <div class="col mx-auto">
                        <div class="row justify-content-center">
                            <div class="col-lg-9 col-xl-8">
                                <div class="card-group mb-0">
                                    <div class="card p-4">
                                        <div class="card-body">
                                            <div class="text-center title-style mb-6">
                                                <h1>{{__('messages.user.login Employee')}}</h1>
                                                <hr class="nav-divider">
                                                <p class="text-muted">{{__('messages.site.cloud computing Avid')}}</p>
                                            </div>
                                            {{--												<div class="btn-list d-flex">--}}
                                            {{--													<a href="https://www.google.com/gmail/" class="btn btn-google btn-block"><i class="fa fa-google fa-1x mr-2"></i> Google</a>--}}
                                            {{--													<a href="https://twitter.com/" class="btn btn-twitter"><i class="fa fa-twitter fa-1x"></i></a>--}}
                                            {{--													<a href="https://www.facebook.com/" class="btn btn-facebook"><i class="fa fa-facebook fa-1x"></i></a>--}}
                                            {{--												</div>--}}
                                            <hr class="nav-divider my-6">
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-user"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Username">
                                            </div>
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-lock"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" placeholder="Password">
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="button" class="btn  btn-primary btn-block px-4">
                                                        {{__('messages.user.login')}}
                                                    </button>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <a href="{{url('/' . $page='forgot-password-3')}}"
                                                       class="btn btn-link box-shadow-0 px-0">{{__('messages.user.forgot-your-password?')}}</a>
                                                </div>
                                            </div>
                                            <div class="text-center pt-4">
                                                <div
                                                    class="font-weight-normal fs-16">{{__('messages.user.You Don\'t have an account')}}
                                                    <a class="btn-link font-weight-normal"
                                                       href="#">{{__('messages.user.Register Here')}}</a></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card text-white bg-primary py-5 d-md-down-none page-content mt-0">
                                        <div class="text-center justify-content-center page-single-content">
                                            <div class="box">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                            <img src="{{URL::asset('assets/images/png/login.png')}}" alt="img">
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
@endsection
@section('js')
@endsection
