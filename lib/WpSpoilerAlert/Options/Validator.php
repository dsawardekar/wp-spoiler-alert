<?php

namespace WpSpoilerAlert\Options;

class Validator extends \Arrow\Options\Validator {

  function loadRules($validator) {
    $validator->rule('required', 'max');
    $validator->rule('integer', 'max');

    $validator->rule('required', 'partial');
    $validator->rule('integer', 'partial');

    $validator->rule('safeText', 'tooltip')->message('Unsafe Tooltip value.');
  }

}
