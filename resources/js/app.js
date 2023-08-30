import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'
import Tooltip from "@ryangjchandler/alpine-tooltip";
import Clipboard from "@ryangjchandler/alpine-clipboard"
import Tribute from "tributejs";
import axios from 'axios';

window.axios = axios;

Alpine.plugin(focus)
Alpine.plugin(Tooltip);
Alpine.plugin(Clipboard);

window.Tribute = Tribute;
window.Alpine = Alpine

Alpine.start()

