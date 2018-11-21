<?php
/**
 * Criado por Maizer Aly de O. Gomes para api.quickepay.
 * Email: maizer.gomes@gmail.com / maizer.gomes@ekutivasolutions / maizer.gomes@outlook.com
 * Usuário: maizerg
 * Data: 11/21/18
 * Hora: 9:13 PM
 */

namespace Quick3Pay\Generator\Commands;


interface CommandsInterface
{
    public function handle();

    public function createFile($stubPath, $replacements, $filePath, $folderPath);
}