<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('widget.enabled', false);
        $this->migrator->add('widget.position', 'bottom-right');
        $this->migrator->add('widget.allowed_domains', []);
        $this->migrator->add('widget.primary_color', '#2563EB');
        $this->migrator->add('widget.button_text', 'Feedback');
    }
};
