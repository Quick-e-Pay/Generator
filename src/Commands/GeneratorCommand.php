<?php
/**
 * Criado por Maizer Aly de O. Gomes para api.quickepay.
 * Email: maizer.gomes@gmail.com / maizer.gomes@ekutivasolutions / maizer.gomes@outlook.com
 * Usuário: maizerg
 * Data: 11/21/18
 * Hora: 12:52 PM
 */

namespace Quick3Pay\Generator\Commands;


use Illuminate\Console\Command;

abstract class GeneratorCommand extends Command
{
    /**
     * @param $contract
     * @param $replacements
     * @param $filePath
     * @param $folderPath
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createFile($contract, $replacements, $filePath, $folderPath): void
    {
        $content = $this->fileManager->get($contract);

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        if (!$this->fileManager->exists($filePath)) {

            if (!$this->fileManager->exists($folderPath)) {
                $this->fileManager->makeDirectory($folderPath, 0755, true);
            }
            $this->fileManager->put($filePath, $content);

            $this->line("Created {$filePath} with success!");
        }
    }
}