<?php
namespace App\Core;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $error = match ($rule) {
                    'required' => self::isEmpty($value) ? "O campo {$field} é obrigatório." : null,
                    'string'   => !self::isEmpty($value) && !is_string($value) ? "O campo {$field} deve ser texto." : null,
                    'numeric'  => !self::isEmpty($value) && !is_numeric($value) ? "O campo {$field} deve ser numérico." : null,
                    'integer'  => !self::isEmpty($value) && filter_var($value, FILTER_VALIDATE_INT) === false ? "O campo {$field} deve ser inteiro." : null,
                    'email'    => !self::isEmpty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL) ? "O campo {$field} deve ser um e-mail válido." : null,
                    'date'     => !self::isEmpty($value) && !self::isValidDate($value) ? "O campo {$field} deve ser uma data válida." : null,
                    'url'      => !self::isEmpty($value) && !filter_var($value, FILTER_VALIDATE_URL) ? "O campo {$field} deve ser uma URL válida." : null,
                    'max'      => !self::isEmpty($value) && mb_strlen((string) $value) > (int) $params[0]
                        ? "O campo {$field} deve ter no máximo {$params[0]} caracteres." : null,
                    'min'      => !self::isEmpty($value) && mb_strlen((string) $value) < (int) $params[0]
                        ? "O campo {$field} deve ter no mínimo {$params[0]} caracteres." : null,
                    'in'       => !self::isEmpty($value) && !in_array((string) $value, $params, true)
                        ? "O campo {$field} possui valor inválido." : null,
                    'nullable' => null,
                    default    => null,
                };

                if ($error !== null) {
                    $errors[$field][] = $error;
                    break;
                }

                if ($rule === 'nullable' && self::isEmpty($value)) {
                    break 2;
                }
            }
        }

        return $errors;
    }

    public static function fails(array $data, array $rules): bool
    {
        return !empty(self::validate($data, $rules));
    }

    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    private static function isValidDate(mixed $value): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', (string) $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}
