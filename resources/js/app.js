
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'

Alpine.plugin(Clipboard)

Livewire.start()


import Tribute from "tributejs";
import axios from 'axios';

window.axios = axios;
window.Tribute = Tribute;
