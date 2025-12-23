<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('activity_widget.enabled', false);
        $this->migrator->add('activity_widget.position', 'bottom-left');
        $this->migrator->add('activity_widget.allowed_domains', []);
        $this->migrator->add('activity_widget.primary_color', '#2563EB');
        $this->migrator->add('activity_widget.button_text', 'Recent activity');
        $this->migrator->add('activity_widget.hide_button', false);
        $this->migrator->add('activity_widget.modal_title', 'Recent activity');
        $this->migrator->add('activity_widget.items_limit', 10);
    }
};
