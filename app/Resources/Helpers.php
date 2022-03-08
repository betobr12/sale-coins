<?php

namespace App\Resources;


class Helpers
{
    public $cpf_cnpj;

    public function validateCPF()
    {
        $cpf = preg_replace( '/[^0-9]/is', '', $this->cpf_cnpj);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public function validateCNPJ()
    {
        $cnpj = preg_replace( '/[^0-9]/', '', $this->cpf_cnpj );
        if (preg_match('/(\d)\1{10}/', $cnpj)) {
            return false;
        }
        $cnpj = (string)$cnpj;
        $cnpj_original = $cnpj;
        $primeiros_numeros_cnpj = substr( $cnpj, 0, 12 );
        $primeiro_calculo = $this->multiplica_cnpj( $primeiros_numeros_cnpj );
        $primeiro_digito = ( $primeiro_calculo % 11 ) < 2 ? 0 :  11 - ( $primeiro_calculo % 11 );
        $primeiros_numeros_cnpj .= $primeiro_digito;
        $segundo_calculo = $this->multiplica_cnpj( $primeiros_numeros_cnpj, 6 );
        $segundo_digito = ( $segundo_calculo % 11 ) < 2 ? 0 :  11 - ( $segundo_calculo % 11 );
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;
        if ( $cnpj === $cnpj_original ) {
            return true;
        } else {
            return false;
        }
    }

    function multiplica_cnpj( $cnpj, $posicao = 5 ) {
        $calculo = 0;
        for ( $i = 0; $i < strlen( $cnpj ); $i++ ) {
            $calculo = $calculo + ( $cnpj[$i] * $posicao );
            $posicao--;
            if ( $posicao < 2 ) {
                $posicao = 9;
            }
        }
        return $calculo;
    }
}
