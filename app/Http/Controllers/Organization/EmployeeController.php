<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\VerifyCode;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    use RegistersUsers, AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest.employee:employee', ['except' => [
            'logout',
            'index',
        ]]);
    }

    private $redirectTo = '/organization';

    /**
     * Used for registry and login.
     *
     * The value of this field is specified by the
     * following two methods:
     *      1) determineUsernameLogin
     *      2) determineUsernameRegister
     */
    private $username;

    /**
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('organization.index');
    }

    public function showRegistrationForm()
    {
        return view('organization.auth.register');
    }

    /**
     * The user has been registered.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }

    /**
     * Get the post register redirect path.
     *
     * @return string
     */
    public function redirectPathForRgister()
    {
        return 'organization.auth.verify';
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * Initially, the user is stored in the database but is not logged in.
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $employee = Employee::where('mobile', $request->mobile)->first();

        if ($employee) {
            if ($employee->verified_at) {
                return back()->with([
                    'status' => 'warning',
                    'message' => __('messages.user.This-mobileNumber-already-registered-and-not-possible-to-re-register')
                ]);
            }
        } else {
            $employee = $this->create($request->all());
        }
        $verifyCode = $employee->verifyCode;
        if (!$verifyCode) {
            $verifyCode = VerifyCode::create([
                'employee_id' => $employee->id
            ]);
        }

        $verifyCode->sendCode();

        event(new Registered($user = $employee));

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect()->route($this->redirectPathForRgister(), ['mobile' => $employee->mobile]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return Employee
     */
    protected function create(array $data)
    {
        do {
            $personnelCode = generateCode(9);
        } while (Employee::where('personnelCode', $personnelCode)->exists());

        return Employee::create([
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'personnelCode' => $personnelCode,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('employee');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $this->determineUsernameRegister();
        return Validator::make($data, [
            $this->username() => [
                'required', 'numeric',// 'max:13',
                'regex:/^(0098|0?|\+?98)9\d{9}$/',
            ],
            'password' => ['required', 'string', 'min:' . config('auth.length_employee_password'), 'confirmed'],
            'agree' => ['required', 'accepted'],
        ], [], [
            'agree' => 'توافقنامه'
        ]);
    }

    /*
    * Check either mobile.
    */
    public function determineUsernameRegister()
    {
        $this->username = 'mobile';
    }

    public function verify($mobile)
    {
        return view('organization.auth.verify', compact('mobile'));
    }

    public function resendVerify(Request $request)
    {
//        We use this type of volition when we use Ajax.
        $valid = Validator::make($request->except('_token'), [
            'mobile' => ['required', 'string', 'max:14', 'regex:/^(0098|0?|\+?98)9\d{9}$/'],
        ], [
            'mobile.*' => __('messages.user.There-problem-please-contact-support')
        ]);

        if ($valid->passes()) {
            $employee = Employee::where('mobile', $request->mobile)->first();
            if ($employee) {
                if ($employee->verified_at) {
//                    dd('s',$employee->verified_at);
                    return response([
                        'status' => 'warning',
                        'message' => __('messages.user.This-mobileNumber-already-registered-and-not-possible-to-re-register'),
                    ], 200);
                }
                $verifyCode = $employee->verifyCode;
                if ($verifyCode) {
                    $dateDiff = now()->diffInMinutes($verifyCode->updated_at);
                    //To make changes to table fields in the database.
                    $verifyCode->update([
                        'used' => false,
                    ]);

                    if ($dateDiff <= config('auth.resend-the-code-after-n-minutes', 1)) {
                        $watingTime = config('auth.resend-the-code-after-n-minutes', 1);
                        return response([
                            'status' => 'info',
                            'message' => "لطفا پس از {$watingTime} دقیقه، روی دکمه ارسال مجدد کد، کلیلک نمایید.",
                        ], 200);
                    }
                    if ($dateDiff > config('auth.resend_verification_code_time_diff', 60)) {
                        //The code is generated again.
                        $verifyCode->setCode();
                    }
                } else {
                    $verifyCode = VerifyCode::create([
                        'employee_id' => $employee->id
                    ]);
                }
                $verifyCode->sendCode();
                return response([
                    'status' => 'success',
                    'message' => __('messages.user.The-code-was-sent-to-you-again'),
                ], 200);
            } else {
                //employee not exist.
                return response([
                    'status' => 'error',
                    'message' => __('messages.user.There-is-no-user-with-such-specifications'),
                ], 200);
            }


        } else {  //  data dose not validation
            return response([
                'errors' => $valid->errors()->all(),
            ], 200); //I do not like 422, because I want to enter the success section of Ajax.
        }
    }

    public function doVerify(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|numeric|digits:' . config('auth.length_register_verify_code'),
            'mobile' => ['required', 'string', 'max:14', 'regex:/9\d{9}$/']
        ], [
            'code.*' => __('messages.user.The-submitted code-incorrect'),
            'mobile.*' => __('messages.user.There-problem-with-registration-please-re-register-or-contact-support')
        ]);

        $employee = Employee::where('mobile', $request->mobile)->first();
        if (!$employee) {
            return back()->with([
                'status' => 'error',
                'message' => __('messages.user.There-problem-with-registration-please-re-register-or-contact-support')
            ]);
        }

        if ($employee->verified_at) {
            return back()->with([
                'status' => 'warning',
                'message' => __('messages.user.This-mobileNumber-already-registered-and-not-possible-to-re-register')
            ]);
        }

        $verifyCode = $employee->verifyCode;

//        dd($verifyCode->isUsed());
        if ($verifyCode->isUsed())
            return back()->with([
                'status' => 'error',
                'message' => __('messages.user.There-problem-with-registration-please-re-register-or-contact-support')
            ]);

        if ($verifyCode->code !== (int)$request->input('code'))
            return back()->with([
                'status' => 'error',
                'message' => __('messages.user.The-submitted code-incorrect')
            ]);

        DB::beginTransaction();
        try {
            $verifyCode->update([
                'used' => true
            ]);
            $this->guard('employee')->login($employee);
            $employee->operationVerified();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with([
                'status' => 'error',
                'message' => __('messages.user.There-problem-with-registration-please-re-register-or-contact-support')
            ]);
        }
        DB::commit();

        return redirect()->route('organization.index');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('organization.auth.login');
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
    }

    protected function validateLogin(Request $request)
    {
        $this->validate(
            $request, [
            'username' => 'required',
        ], [
                'username.required' => __('messages.user.employee.Enter-mobile-or-personal-number'),
            ]
        );

        /**
         * username value can be undefined, mobile number, personnel number or undefined.
         */
        $this->determineUsernameLogin();
        $username = $this->username();
        $this->validate(
            $request, [
            'username' => 'different:undefined',
        ], [
                'username.different' => __('messages.user.employee.Enter-mobile-or-personal-number'),
            ]
        );

        if ($username == 'mobile') {
            $this->validate(
                $request, [
                'username' => ['required', 'string', 'max:14', 'regex:/^(0098|0?|\+?98)9\d{9}$/'],
                'password' => 'required|string|min:' . config('auth.length_employee_password'),
            ], [
                    "username.required" => __('messages.user.employee.Enter-the-username'),
                    'password.required' => __('messages.user.employee.Enter-the-password'),
                    'username.regex' => __('messages.user.employee.The-entered-mobile-number-has-an-inappropriate-format'),
                    "username.max" => __('messages.user.employee.Imported-characters-too-are-allowed'),
                ]
            );
        } else if ($username == 'personnelCode') {
            $this->validate(
                $request, [
                'username' => 'required|string|digits:9',
                'password' => 'required|string|min:' . config('auth.length_employee_password'),
            ], [
                    "username.required" => __('messages.user.employee.Enter-the-username'),
                    'password.required' => __('messages.user.employee.Enter-the-password'),
                    "username.digits" => __('messages.user.employee.The-entered-personnel-number-inappropriate-format'),
                ]
            );
        }
    }

    /*
     * Check either mobile or personnelCode.
     */
    public function determineUsernameLogin()
    {
        $value = request()->get('username');

        if (empty($value))
            return '';

        $fieldName = filter_var($value, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^(0098|09|\+?98)[\w \d]*/"))) ? 'mobile' : 'undefined';
        if ($fieldName == 'undefined') {
            $fieldName = filter_var($value, FILTER_VALIDATE_INT) ? 'personnelCode' : 'undefined';
        }

        if ($fieldName === 'mobile') {
            $value = toValidMobileNumber($value);
        }
        request()->merge([$fieldName => $value]);

        $this->username = $fieldName;
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withStatus('error')
            ->withMessage(trans('auth.failed'));
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('organization.auth.show.login');
    }

    /**
     * The user has logged out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }
}
