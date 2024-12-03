# Auth kontroler sa register/login/logout metodama (treba ga kreirati sa naredbom "php artisan make:controller AuthController")

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|Rules\Password::defaults()',
        ]);

        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json([
            'username' => $user->username,
            'email' => $user->email,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|Rule::unique('users')->ignore($this->route('user'))',
            'email' => 'required|email|max:255|Rule::unique('users')->ignore($this->route('user'))',
            'password' => 'required|Rules\Password::defaults()',
        ]);

        $user = User::where('username', $validatedData->username)->orWhere('email', $validatedData->email)->first();
        if (!$user || !Hash::check($validatedData->password, $user->password)) {
            return response()->json([
                'message' => ['Username or password incorrect'],
            ], 401);
        }

        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User logged in successfully',
            'name' => $user->name,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully'
        ], 201);
    }
}

?>



# Middleware koji provjerava korisnikov token (treba ga kreirati sa naredbom "php artisan make:middleware AuthToken")

<?php

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class AuthToken
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        if (User::where('auth_token', $token)->first()) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthenticated'
        ], 403);
    }
}

?>



# te potom registrirati u bootstrap/app.php, novu middleware klasu dodajemo u api grupu i dajemo joj alias (naredbu treba ulančati/nadodati između configure i create metoda klase Application)

<?php

->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [AuthToken::class,])
        ->alias(['auth.token' => AuthToken::class,]);
})

?>



# Rute za metode Auth kontrolera (treba omogućiti vidljivost, odnosno napraviti "publish" api.php filea u routes folderu sa naredbom "php artisan install:api")

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route:controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::login('/login', 'login')->name('login');
    Route::logout('/logout', 'logout')->name('logout')->middleware('auth.token');
});

?>