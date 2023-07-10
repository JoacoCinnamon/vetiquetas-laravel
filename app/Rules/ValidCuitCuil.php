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
        // Eliminar guiones, espacios y puntos del valor
        $cuitCuil = str_replace(['-', ' '], '', $value);
        // Eliminar caracteres no numéricos
        $cuitCuil = preg_replace('/[^0-9]/', '', $value);

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
        if (!$this->isValidAfipTangoCuit($cuitCuil)) {
            $fail($this->message());
            return;
        }
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
        $factor = 5;
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += ($cuit[$i] * $factor);
            $factor = ($factor == 2) ? 7 : $factor - 1;
        }

        $expectedChecksum = 11 - ($sum % 11);
        $expectedChecksum = ($expectedChecksum == 11) ? 0 : $expectedChecksum;

        return $cuit[10] == $expectedChecksum;
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
        $factor = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += ($cuil[$i] * $factor[$i]);
        }

        $expectedChecksum = 11 - ($sum % 11);
        $expectedChecksum = ($expectedChecksum == 11) ? 0 : $expectedChecksum;

        return $cuil[10] == $expectedChecksum;
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
