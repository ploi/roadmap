import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm';
import focus from '@alpinejs/focus'
import Tooltip from "@ryangjchandler/alpine-tooltip";

Alpine.plugin(focus)
Alpine.plugin(Tooltip);
Alpine.plugin(FormsAlpinePlugin);

window.Alpine = Alpine

Alpine.start()
