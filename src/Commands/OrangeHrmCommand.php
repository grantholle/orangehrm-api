<?php

namespace GrantHolle\OrangeHrm\Commands;

use Illuminate\Console\Command;

class OrangeHrmCommand extends Command
{
    public $signature = 'orangehrm-api';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
