<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ValidCuitCuil implements ValidationRule
{
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Eliminar guiones y caracteres no numericos
        $cuitCuil = preg_replace("/-/", "", preg_replace("/\D/", "", (string) $value));

        // Validar la longitud del CUIT/CUIL
        if (strlen($cuitCuil) !== 11) {
            $fail($this->message());
            return;
        }

        if (!$this->isValidCuit($cuitCuil) && !$this->isValidCuil($cuitCuil)) {
            $fail($this->message());
            return;
        }

        // Validacion final de si está en la AFIP "de dudosa procedencia"
        // if (!$this->isValidAfipTangoCuit($cuitCuil)) {
        //     $fail($this->message());
        //     return;
        // }
    }

    public function message()
    {
        return 'El número de CUIT/CUIL proporcionado no es válido.';
    }

    /**
     * Validate the CUIT.
     *
     * @param  string  $value
     * @return bool
     */
    private function isValidCuit(string $value)
    {
        $cuit = $value;
        $suma = 0;
        $factores = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];

        $numeros = substr($cuit, 0, -1);
        $verificador = (int) substr($cuit, -1);

        for ($i = 0; $i < 10; $i++) {
            $suma += (int) ($numeros[$i] * $factores[$i]);
        }

        $resto = $suma % 11;
        $resultado = 0;
        if ($resto === 0)
            $resultado = 0;
        elseif ($resto === 1)
            $resultado = 9;
        else
            $resultado = 11 - $resto;

        if ($resultado !== $verificador) return false;

        return true;
    }

    /**
     * Validate the CUIL.
     *
     * @param  string  $value
     * @return bool
     */
    private function isValidCuil(string $value)
    {
        $cuil = $value;
        $suma = 0;
        $factores = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];

        $numeros = substr($cuil, 0, -1);
        $verificador = (int) substr($cuil, -1);

        for ($i = 0; $i < 10; $i++) {
            $suma += (int) ($numeros[$i] * $factores[$i]);
        }

        $resto = $suma % 11;
        $resultado = 0;
        if ($resto === 0) $resultado = 0;
        elseif ($resto === 1) $resultado = 9;
        else $resultado = 11 - $resto;

        if ($resultado !== $verificador) return false;

        return true;
    }

    /**
     * Fuente de la api: https://www.tangofactura.com/Help/Api/GET-Rest-GetContribuyente_cuit
     * 
     * Debería de devolver si es válida o no con los datos del contribuyente desde la AFIP
     * @param string $cuitCuil
     * @return boolean
     */
    private function isValidAfipTangoCuit(string $cuitCuil): bool
    {
        $base_url = "https://afip.tangofactura.com/Rest/GetContribuyente";
        $response = Http::withoutVerifying()->get($base_url, ["cuit" => $cuitCuil]);
        $data = $response->json();
        return isset($data["errorGetData"]) && $data["errorGetData"] == false;
    }
}
