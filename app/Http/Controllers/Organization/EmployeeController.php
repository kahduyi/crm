<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
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
    use RegistersUsers, AuthenticatesUsers;

    protected $redirectTo = '/organization';

    public function __construct()
    {
        $this->middleware('guest:employee')->except('logout');
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
        $this->validator($request->all())->validate();

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
            $personnelCode = $this->generate_code(9);
        } while (Employee::where('personnelCode', $personnelCode)->exists());

        return Employee::create([
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'personnelCode' => $personnelCode,
            'verify_code' => $this->generate_code(6),
            'ip' => request()->ip(),
        ]);
    }

    protected function guard()
    {
        return Auth::guard('employee');
    }

    private function generate_code($len)
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
    protected function validator(array $data)
    {
        return Validator::make($data, [
            $this->username() => ['required', 'string', 'max:20', 'regex:/^(0098|0?|\+?98)9\d{9}$/', 'unique:employees'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree' => ['required', 'accepted'],
        ], [], [
            'agree' => 'توافقنامه'
        ]);
    }

    /*
    * Check either mobile or email.
    */
    public function username()
    {
        return 'mobile';
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
        $username = $this->usernameLogin();

        if ($username == 'undefined')
            return back()->with([
                'status' => 'undefinedLogin',
                'username' => 'شماره موبایل یا شماره پرسنلی را، وارد نمایید.',
            ]);
        $this->validateLogin($request, $username);

        dd($request->username . " ta inja");
        $field = $request->has('mobile') ? 'mobile' : 'personnelCode';
        $value = $request->input($field);
        dd($field, $value);
    }

    protected function validateLogin(Request $request, $username)
    {
        $this->validate(
            $request,
            [
                'username' => 'required',
            ],
            [
                'username.required' => __('messages.user.employee.Enter-mobile-or-personal-number'),
            ]
        );

        if ($username == 'mobile') {
            $this->validate(
                $request,
                [
                    'username' => ['required', 'string', 'max:14', 'regex:/^(0098|0?|\+?98)9\d{9}$/'],
                    'password' => 'required|string',
                ],
                [
                    "username.required" => __('messages.user.employee.Enter-the-username'),
                    'password.required' => __('messages.user.employee.Enter-the-password'),
                    'username.regex' => __('messages.user.employee.The-entered-mobile-number-has-an-inappropriate-format'),
                    "username.max" => __('messages.user.employee.Imported-characters-too-are-allowed'),
                ]
            );
        } else if ($username == 'personnelCode') {
            $this->validate(
                $request,
                [
                    'username' => 'required|string|digits:9',
                    'password' => 'required|string',
                ],
                [
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
    public function usernameLogin()
    {
        $value = request()->get('username');

        if (empty($value))
            return '';

        $fieldName = filter_var($value, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^(0098|09|\+?98)[\w \d]*/"))) ? 'mobile' : 'undefined';
        if ($fieldName == 'undefined') {
            $fieldName = filter_var($value, FILTER_VALIDATE_INT) ? 'personnelCode' : 'undefined';
        }

        request()->merge([$fieldName => $value]);
        return $fieldName;
    }

    public function whicheOfUserName($username)
    {
        $employee = static::where('mobile', $username)->orWhere('personnelCode', $username)->first();
        dd($employee);
        return $employee;
    }

    public function findEmployByUsername($username)
    {
        $employee = static::where('mobile', $username)->orWhere('personnelCode', $username)->first();

        return $employee;
    }
}
