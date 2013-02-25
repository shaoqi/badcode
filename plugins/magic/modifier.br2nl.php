<?php

function magic_modifier_br2nl($string)
{
return preg_replace('/<br\\s*?\/??>/i','',$string);
}

?>