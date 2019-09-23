<?php /* Mbn v1.45 | https://mirkl.es/n/lib | Copyright (c) 2016-2019 Mikołaj Błajek | https://github.com/mblajek/Mbn/blob/master/LICENSE.txt */
namespace Mbn;
class MbnErr extends \Exception
{
   /**
    * Common error message object
    * @export
    * @constructor
    * @param string $fn Function name
    * @param string $msg Message
    * @param mixed $val Incorrect value to message, default null
    */
   public function __construct($fn, $msg, $val = null)
   {
      $ret = 'Mbn' . $fn . ' error: ' . $msg;
      if (func_num_args() !== 2) {
         if (is_array($val)) {
            $val = '[' . implode(',', $val) . ']';
         }
         $ret .= ': ' . ((strlen($val) > 20) ? (substr($val, 0, 18) . '..') : $val);
      }
      parent::__construct($ret);
   }

}
