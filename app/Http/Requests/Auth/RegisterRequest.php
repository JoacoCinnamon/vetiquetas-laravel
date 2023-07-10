<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\ValidCuitCuil;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
   */
  public function rules(): array
  {
    return [
      'nombre' => ['required', 'string', 'max:255'],
      'apellido' => ['required', 'string', 'max:255'],
      'documento' => ['required', 'string', 'numeric', 'digits_between:7,8'],
      'cuit_cuil' => ['required', 'string', new ValidCuitCuil, 'unique:' . User::class],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
      'password' => ['required', 'confirmed', Password::defaults()],
    ];
  }

  /**
   * Get custom attributes for validator errors.
   *
   * @return array<string, string>
   */
  public function attributes(): array
  {
    return [
      'cuit_cuil' => 'CUIT/CUIL',
    ];
  }

  /**
   * Prepare the data for validation.
   */
  protected function prepareForValidation(): void
  {
    // Básicamente para que por las dudas siempre llegue un cuit-cuil sin guiones a la validacion
    // asegurandonos que por ejemplo sea único en la base de datos (ya que los almacenamos sin guiones)
    $this->merge([
      "cuit_cuil" => str_replace('-', '', $this->cuit_cuil)
    ]);
  }

  /**
   * Attempt to register the request's credentials.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function register(): User
  {
    $this->ensureIsNotRateLimited();

    $userData = $this->validated();
    if (!$userData) {
      RateLimiter::hit($this->throttleKey());

      throw ValidationException::withMessages([
        'email' => trans('auth.failed'),
      ]);
    }

    RateLimiter::clear($this->throttleKey());

    $userData["uuid"] = Str::uuid()->toString();
    return User::create($userData);
  }

  /**
   * Ensure the login request is not rate limited.
   *
   * @throws \Illuminate\Validation\ValidationException
   */
  public function ensureIsNotRateLimited(): void
  {
    if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
      return;
    }

    event(new Lockout($this));

    $seconds = RateLimiter::availableIn($this->throttleKey());

    throw ValidationException::withMessages([
      'email' => trans('auth.throttle', [
        'seconds' => $seconds,
        'minutes' => ceil($seconds / 60),
      ]),
    ]);
  }

  /**
   * Get the rate limiting throttle key for the request.
   */
  public function throttleKey(): string
  {
    return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
  }
}
