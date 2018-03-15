<?php

class eCampSetup
{
    private static function GetSetups() {
        return [
            new \eCamp\Core\Setup()
        ];
    }

    public static function Run($commands) {
        $setups = self::GetSetups();

        array_shift($commands);

        if (empty($commands)) {
            echo PHP_EOL;
            foreach ($setups as $setup) {
                /** @var $setup \eCamp\Lib\ISetup */
                echo get_class($setup) . ':' . PHP_EOL;
                foreach ($setup->GetCommands() as $command) {
                    echo '  ' . $command . PHP_EOL;
                }
                echo PHP_EOL;
            }
            echo PHP_EOL;

        } else {
            foreach ($commands as $command) {
                foreach ($setups as $setup) {
                    /** @var $setup \eCamp\Lib\ISetup */
                    if (in_array($command, $setup->GetCommands())) {
                        $setup->RunCommand($command);
                    }
                }
            }
        }
    }

}
