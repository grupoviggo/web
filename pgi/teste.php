<?php
require 'SimpleXLSX.php';

if (class_exists('SimpleXLSX')) {
    echo "A classe SimpleXLSX foi carregada com sucesso!";
} else {
    echo "Falha ao carregar a classe SimpleXLSX.";
}
