<?php
$defined_variable_names = array_keys(get_defined_vars());

sort($defined_variable_names);

echo implode(',', $defined_variable_names);