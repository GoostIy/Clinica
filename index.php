<?php
// Las pantallas viven en pantallas/. Esto solo hace que la dirección corta
// (http://localhost/clinagenda/) siga llevando a la entrada del sistema.
header('Location: pantallas/');
exit;
