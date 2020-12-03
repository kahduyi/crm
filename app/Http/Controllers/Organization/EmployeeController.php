<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    use RegistersUsers, ThrottlesLogins;

    private $redirectTo = '/organization';

    public function __construct()
    {
        $this->middleware('guest:employee')->except(['organization.auth.logout', 'organization.index']);
    }

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

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function showRegistrationForm()
    {
        return view('organization.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $this->validatorRegister($request->all())->validate();

        $employee = $this->create($request->all());

        //TODO ارسال پیامک
        Log::info('SEND-REGISTER-CODE-MESSAGE-TO-USER', ['code' => '123456']);
//        event(new Registered($employee = $this->create($request->all())));

        $this->guard()->login($employee);

        if ($response = $this->registered($request, $employee)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
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
            $personnelCode = $this->generateCode(9);
        } while (Employee::where('personnelCode', $personnelCode)->exists());

        return Employee::create([
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'personnelCode' => $personnelCode,
            'verify_code' => $this->generateCode(6),
            'ip' => request()->ip(),
        ]);
    }

    protected function guard()
    {
        return Auth::guard('employee');
    }

    private function generateCode($len)
    {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorRegister(array $data)
    {
        $this->determineUsernameRegister();
        return Validator::make($data, [
            $this->username => [
                'required', 'string', 'max:20',
                'regex:/^(0098|0?|\+?98)9\d{9}$/', 'unique:employees'
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('organization.auth.login');
    }

    public function login(Request $request)
    {
        /**
         * username value can be blank, mobile number, personnel number or undefined.
         */
        $this->determineUsernameLogin();

        if ($this->username == 'undefined')
            return back()->with([
                'status' => 'undefinedLogin',
                'username' => 'شماره موبایل یا شماره پرسنلی را، وارد نمایید.',
            ]);
        $this->validateLogin($request, $this->username);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }
        return redirect()->intended($this->redirectPath());
        dd($this->redirectPath());

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
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
        //
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
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function validateLogin(Request $request, $username)
    {
        $this->validate(
            $request, [
            'username' => 'required',
        ], [
                'username.required' => __('messages.user.employee.Enter-mobile-or-personal-number'),
            ]
        );

        if ($username == 'mobile') {
            $this->validate(
                $request, [
                'username' => ['required', 'string', 'max:14', 'regex:/^(0098|0?|\+?98)9\d{9}$/'],
                'password' => 'required|string',
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
                'password' => 'required|string',
            ], [
                    "username.required" => __('messages.user.employee.Enter-the-username'),
                    'password.required' => __('messages.user.employee.Enter-the-password'),
                    "username.digits" => __('messages.user.employee.The-entered-personnel-number-inappropriate-format'),
                    "username.digits" => __('messages.user.employee.Imported-characters-too-are-allowed'),
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

        request()->merge([$fieldName => $value]);

        $this->username = $fieldName;
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        dd('ta inja');
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
