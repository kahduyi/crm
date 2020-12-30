@extends('organization.layouts.master_auth')
@section('title','تایید')
@section('css')
    <style>
        .resendCodeBtn {
            font-size: 0.8vw;
        }

        @media only screen and (max-width: 800px) {
            .resendCodeBtn {
                font-size: 2.8vw;
            }
        }
    </style>
@endsection
@section('content')
    <div class="page">
        <div class="page-single">
            <div class="p-5">
                <div class="row">
                    <div class="col mx-auto">
                        <div class="row justify-content-center">
                            <div class="col-lg-9 col-xl-8" id="showMessage">
                                @if(session()->has('status'))
                                    @if (session()->get('status') == "resend")
                                        <div class="alert alert-light col-md-12" role="alert">
                                            {{session()->pull('message')}}
                                        </div>
                                    @elseif (session()->get('status') == "warning")
                                        <div class="alert alert-warning col-md-12" role="alert">
                                            {{session()->pull('message')}}
                                        </div>
                                    @elseif (session()->get('status') == "error")
                                        <div class="alert alert-danger col-md-12" role="alert">
                                            {{session()->pull('message')}}
                                        </div>
                                    @elseif(session()->pull('status') == "nothing")
                                    @endif
                                @endif
                                @if ($errors->has('mobile'))
                                    <div class="alert alert-danger col-md-12" role="alert">
                                        {{ $errors->first('mobile') }}
                                    </div>
                                @elseif ($errors->has('code'))
                                    <div class="alert alert-danger col-md-12" role="alert">
                                        {{ $errors->first('code') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mx-auto">
                        <div class="row justify-content-center">
                            <div class="col-lg-9 col-xl-8">
                                <div class="card-group mb-0">
                                    <div class="card p-4">
                                        <div class="row">
                                            <form id="formResend"
                                                  data-url="{{route('organization.auth.resend.verify')}}"
                                                  method="post" class="col-md-2" role="form">
                                                @csrf
                                                <input type="hidden" name="mobile"
                                                       @if($mobile)value="{{$mobile}}"@endif>
                                                <button type="submit" class="btn btn-outline-primary"><i
                                                        class="fe fe-refresh-cw"></i></button>
                                            </form>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4 ">
                                                <span class="avatar avatar-xxl  brround"
                                                      style="background-image: url({{URL::asset('assets/images/users/2.jpg')}})"></span>
                                            </div>
                                            <span class="m-4 d-none d-lg-block text-center">
													<span
                                                        class="fs-20"><strong>{{__('messages.user.Enter-code')}}</strong></span>
												</span>
                                            <form action="{{route('organization.auth.doVerify')}}" method="post"
                                                  id="smsvrifsunmit">
                                                @csrf
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="code"
                                                           placeholder="کد تایید" name="code"
                                                           style="text-align: center;"
                                                           onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                                                    <input type="hidden" name="mobile" value="{{$mobile}}">
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer" style="padding-bottom: 0">
                                            <a href="{{route('organization.auth.show.register')}}"
                                               class="btn btn-outline-primary">{{__('messages.user.Register')}}</a>
                                            <a href="{{route('organization.auth.show.login')}}"
                                               class="btn btn-outline-primary">{{__('messages.user.login')}}</a>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('#code').on('input', function () {
                //TODO hard code 6
                if (this.value.length === 6) {
                    console.log(this.value);
                    $('#smsvrifsunmit').submit();
                }
            });

            $('#formResend').on('submit', function (e) {
                e.preventDefault();
                let url_ = $(this).attr('data-url');
                let formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: url_,
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (data) {
                        $('#showMessage').html('');
                        let content = "";
                        if (data['status']) {
                            if (data['status'] == 'warning') {
                                content = "<div class='alert alert-warning col-md-12' role='alert'>"
                                    + data['message'] + "</div>";
                            } else if (data['status'] == 'error') {
                                content = "<div class='alert alert-danger col-md-12' role='alert'>"
                                    + data['message'] + "</div>";
                            } else if (data['status'] == 'success') {
                                content = "<div class='alert alert-success col-md-12' role='alert'>"
                                    + data['message'] + "</div>";
                            } else if (data['status'] == 'info') {
                                content = "<div class='alert alert-info col-md-12' role='alert'>"
                                    + data['message'] + "</div>";
                            }
                        } else if (data['errors']) { //  Error in verification parameter.
                            let content = "<div class='alert alert-danger col-md-12' role='alert'>"
                                + data['errors'] + "</div>";
                        }
                        $('#showMessage').append(content);
                    },
                    error: function (error) {
                    }
                }) // end of ajax form
            }) // end of id form
        }); //  end of jquery
        /*
        function disableAllForms() {
            let formSmsVerify = document.getElementById("smsvrifsunmit");
            let elements = formSmsVerify.elements;
            for (let i = 0, len = elements.length; i < len; ++i) {
                elements[i].readOnly = true;
            }
            let form = document.getElementById("formResend");
            elements = form.elements;
            for (let i = 0, len = elements.length; i < len; ++i) {
                elements[i].readOnly = true;
            }
        }
         */
    </script>
@endsection
