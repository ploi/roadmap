import Alpine from 'alpinejs'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm';
import focus from '@alpinejs/focus'
import Tooltip from "@ryangjchandler/alpine-tooltip";
import Tribute from "tributejs";
window.axios = require('axios').default;

Alpine.plugin(focus)
Alpine.plugin(Tooltip);
Alpine.plugin(FormsAlpinePlugin);

window.Tribute = Tribute;
window.Alpine = Alpine

Alpine.start()

