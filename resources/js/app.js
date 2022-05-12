import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm';
import focus from '@alpinejs/focus'

Alpine.plugin(focus)
Alpine.plugin(FormsAlpinePlugin);

window.Alpine = Alpine

Alpine.start()
