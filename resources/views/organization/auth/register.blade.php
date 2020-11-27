@extends('organization.layouts.master_auth')
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
                                                <h1 class="mb-2">{{__('messages.user.Register')}}</h1>
                                                <hr class="nav-divider">
                                                <p class="text-muted">{{__('messages.site.cloud computing Avid')}}</p>
                                            </div>
                                            {{--                                            <div class="btn-list d-flex">--}}
                                            {{--                                                <a href="https://www.google.com/gmail/"--}}
                                            {{--                                                   class="btn btn-google btn-block"><i--}}
                                            {{--                                                        class="fa fa-google fa-1x mr-2"></i> Google</a>--}}
                                            {{--                                                <a href="https://twitter.com/" class="btn btn-twitter"><i--}}
                                            {{--                                                        class="fa fa-twitter fa-1x"></i></a>--}}
                                            {{--                                                <a href="https://www.facebook.com/" class="btn btn-facebook"><i--}}
                                            {{--                                                        class="fa fa-facebook fa-1x"></i></a>--}}
                                            {{--                                            </div>--}}
                                            {{--                                            <hr class="divider my-6">--}}
                                            <hr class="nav-divider my-6">
                                            <form action="{{route('organization.auto.register')}}" method="post">
                                                @csrf
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fe fe-user"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="mobile" value="{{old('mobile')}}"
                                                           class="form-control" autofocus autocomplete="mobile"
                                                           placeholder="{{__('messages.user.Number-mobile')}}">
                                                </div>
                                                @if ($errors->has('mobile'))
                                                    <div class="alert alert-light-danger" role="alert">
                                                        {{ $errors->first('mobile') }}
                                                    </div>
                                                @endif
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fe fe-lock"></i>
                                                        </div>
                                                    </div>
                                                    <input name="password" type="password" class="form-control"
                                                           placeholder="{{__('messages.user.Password')}}">
                                                </div>
                                                @if ($errors->has('password'))
                                                    <div class="alert alert-light-danger" role="alert">
                                                        {{ $errors->first('password') }}
                                                    </div>
                                                @endif
                                                <div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fe fe-repeat"></i>
                                                        </div>
                                                    </div>
                                                    <input name="password_confirmation" type="password"
                                                           class="form-control"
                                                           placeholder="{{__('messages.user.Password-confirm')}}">
                                                </div>
                                                @if ($errors->has('password_confirmation'))
                                                    <div class="alert alert-light-danger" role="alert">
                                                        {{ $errors->first('password_confirmation') }}
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label class="custom-control custom-checkbox">
                                                        <input name="agree" type="checkbox" class="custom-control-input"/>
                                                        <span class="custom-control-label"><a
                                                                href="{{url('/' . $page='terms')}}"
                                                                class="">{{__('messages.user.Agree-the-terms-and-policy')}}</a></span>
                                                    </label>
                                                </div>
                                                @if ($errors->has('agree'))
                                                    <div class="alert alert-light-danger" role="alert">
                                                        {{ $errors->first('agree') }}
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="submit"
                                                                class="btn  btn-primary btn-block px-4">{{__('messages.user.Create')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="text-center pt-4">
                                                <div
                                                    class="font-weight-normal fs-16">{{__('messages.user.You-already-have-an-account')}}
                                                    <a
                                                        class="btn-link font-weight-normal"
                                                        href="{{route('organization.auto.show.login')}}"> {{__('messages.user.Login-ere')}} </a>{{__('messages.user.do')}}
                                                </div>
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
