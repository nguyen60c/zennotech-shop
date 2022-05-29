<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Contract\Auth as otpAuth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private $auth;
    private $user;

    public function show()
    {
        return view("auth.login");
    }

    public function __construct(otpAuth $auth)
    {
        $this->auth = $auth;
        $this->user = new User();
    }

    /**
     * Handle account login request
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->getCreadentials();

        if (!Auth::validate($credentials)) {
            return redirect()->to("login")
                ->withErrors(trans('auth.failed'));
        }

        $user = Auth::getProvider()
            ->retrieveByCredentials($credentials);

        /*Switch page for each role*/
        if ($user->hasRole("user")) {
            Auth::login($user);

            return $this->authenticated($request, $user);
        }

        Auth::login($user);

        return redirect()->route("admin.dashboard");
    }

    public function phoneAuth()
    {
        return view('auth.phoneAuth');
    }

    public function isExistPhoneNumber(Request $request)
    {
        $phoneNumberInput = $request['phone_number'];
        $areaInput = $request['area'];
        $phoneNumberInput = substr($phoneNumberInput,1);
        $phoneCheck = $areaInput . '' . $phoneNumberInput;
        $isExisted = $this->user->isPhoneNumberTaken($phoneCheck);
        return $isExisted;
    }

    /*
     * Login by phone
     */
    public function loginOtp(Request $request)
    {

        $tokenId = $request->all()['tokenId'];
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($tokenId);
        } catch (FailedToVerifyToken $e) {
            echo 'The token is invalid: ' . $e->getMessage();
        }
        $uid = $verifiedIdToken->claims()->get('sub');
        $user = $this->auth->getUser($uid);

        $result = [
            'uid' => $user->uid,
            'phoneNumber' => $user->phoneNumber
        ];

        $user = $this->user->getUserByOtp($result['phoneNumber'], $result['uid']);

        if ($user) {
            $roles = $user->getRoleNames()->first();
            Auth::login($user);

            if($roles === 'seller' || $roles === 'admin'){
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->route('users.products.index');
            }

        }

        return redirect()->back();
    }


    /**
     * Handle response after user authenticated
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended();
    }

}
