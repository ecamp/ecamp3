<?php

namespace eCamp\Lib;

interface ISetup
{
    function GetCommands();
    function RunCommand($command);
}
