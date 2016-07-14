<?php
/**
 * Switch to this template for an input to be prepended with a dollar sign.
 * Then switch back to the normal Bootstrap template if any additional
 * inputs don't need the dollar sign.
 */

$bootstrapTemplate = require(ROOT.DS.'config'.DS.'bootstrap_form.php');
$currencyTemplate = $bootstrapTemplate;
$currencyTemplate['input'] = '<span class="input-group-addon">$</span>'.$currencyTemplate['input'];
$currencyTemplate['input'] = '<div class="input-group">'.$currencyTemplate['input'].'</div>';
return $currencyTemplate;
