<?php

namespace WpSpoilerAlert;

class OptionsController extends \Arrow\Options\Controller {

  function patch() {
    $validator = $this->getValidator();
    $validator->rule('required', 'max');
    $validator->rule('integer', 'max');

    $validator->rule('required', 'partial');
    $validator->rule('integer', 'partial');

    $validator->rule('safeText', 'tooltip')->message('Unsafe Tooltip value.');

    if ($validator->validate()) {
      return parent::patch();
    } else {
      return $this->error($validator->errors());
    }
  }

}
