<?php

namespace WpSpoilerAlert;

class OptionsValidator extends \Arrow\OptionsManager\OptionsValidator {

  function loadRules($validator) {
    $validator->rule('required', 'max');
    $validator->rule('integer', 'max');

    $validator->rule('required', 'partial');
    $validator->rule('integer', 'partial');
  }

}
