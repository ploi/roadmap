<?php

use App\Enums\InboxWorkflow;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddInboxWorkflowSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.inbox_workflow', InboxWorkflow::WithoutBoardAndProject);
    }
}
