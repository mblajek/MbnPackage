<?php /* Mbn v1.47 / 21.10.2019 | https://mirkl.es/n/lib | Copyright (c) 2016-2019 Mikołaj Błajek | https://mirkl.es/n/LICENSE */
namespace Mbn;
class MbnErr extends \Exception {
    /* Common error message object */
    private static $messages = [
       'invalid_argument' => 'invalid argument: %v%',
       'invalid_format' => 'invalid format: %v%',
       'limit_exceeded' => 'value exceeded %v% digits limit',
       'calc' => [
          'undefined' => 'undefined: %v%',
          'unexpected' => 'unexpected: %v%'
       ],
       'cmp' => [
          'negative_diff' => 'negative maximal difference: %v%'
       ],
       'def' => [
          'undefined' => 'undefined constant: %v%',
          'already_set' => 'constant already set: %v%',
          'invalid_name' => 'invalid name for constant: %v%'
       ],
       'div' => [
          'zero_divisor' => 'division by zero'
       ],
       'extend' => [
          'invalid_precision' => 'invalid precision (non-negative integer): %v%',
          'invalid_separator' => 'invalid separator (dot, comma): %v%',
          'invalid_truncation' => 'invalid truncation (bool): %v%',
          'invalid_evaluating' => 'invalid evaluating (bool, null): %v%',
          'invalid_formatting' => 'invalid formatting (bool): %v%',
          'invalid_limit' => 'invalid digit limit (positive int): %v%'
       ],
       'fact' => [
          'invalid_value' => 'factorial of invalid value (non-negative integer): %v%'
       ],
       'format' => ['_' => 'extend'],
       'pow' => [
          'unsupported_exponent' => 'only integer exponents supported: %v%'
       ],
       'reduce' => [
          'invalid_function' => 'invalid function name: %v%',
          'no_array' => 'no array given',
          'invalid_argument_count' => 'two arguments can be used only with two-argument functions',
          'different_lengths' => 'arrays have different lengths: %v%',
          'different_keys' => 'arrays have different keys: %v%'
       ],
       'split' => [
          'invalid_part_count' => 'only positive integer number of parts supported: %v%',
          'zero_part_sum' => 'cannot split value when sum of parts is zero'
       ],
       'sqrt' => [
          'negative_value' => 'square root of negative value: %v%'
       ]
    ];

    public $errorKey;
    public $errorValue;
    private static $translation = null;

    public static function translate($translation) {
        static::$translation = $translation;
    }

    /**
     * Convert value to readable string
     * @param mixed $val value to stringify
     * @param bool $implodeArr implode array (first level) or replace contents with ".."
     * @return string
     */
    private static function valToMsgString($val, $implodeArr = true) {
        if ($val === null) {
            return 'null';
        }
        if (is_scalar($val)) {
            return is_bool($val) ? ($val ? 'true' : 'false') : (string)$val;
        }
        if (is_array($val)) {
            return '[' . ($implodeArr ? implode(',', array_map('MbnErr::valToMsgString', $val, [false])) : '..') . ']';
        }
        if (is_object($val) && method_exists($val, '__toString')) {
            return (string)$val;
        }
        return '(' . gettype($val) . (is_object($val) ? (' ' . get_class($val)) : '') . ')';
    }

    /**
     * @export
     * @constructor
     * @param string $key key error code
     * @param mixed $val incorrect value to message
     */
    public function __construct($key, $val = null) {
        if ($val !== null) {
            $val = static::valToMsgString($val);
            $val = ((strlen($val) > 20) ? (substr($val, 0, 18) . '..') : $val);
        }
        $this->errorKey = 'mbn.' . $key;
        $this->errorValue = $val;

        $msg = null;
        $translation = static::$translation;
        if (is_callable($translation)) {
            try {
                $msg = $translation($this->errorKey, $this->errorValue);
            } catch (Exception $e) {
            }
        }
        if (!is_string($msg)) {
            $keyArr = explode('.', $key);
            $keyArrLength = count($keyArr);
            $msg = 'Mbn';
            if ($keyArrLength > 1) {
                $msg .= '.' . $keyArr[0];
            }
            $subMessages = &static::$messages;
            for ($i = 0; $i < $keyArrLength; $i++) {
                $word = $keyArr[$i];
                $nextSubMessages = &$subMessages[$word];
                if (is_array($nextSubMessages) && isset($nextSubMessages['_'])) {
                    $nextSubMessages = &$subMessages[$nextSubMessages['_']];
                }
                $subMessages = &$nextSubMessages;
            }
            $msg .= ' error: ' . $subMessages;
        }
        parent::__construct(str_replace('%v%', $val, $msg));
    }

}
