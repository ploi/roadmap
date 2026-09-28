
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'

Alpine.plugin(Clipboard)

import { themeToggle } from './theme-toggle.js';
Alpine.data('themeToggle', themeToggle);

import { mentions } from './mentions.js';
Alpine.directive('mentions', mentions);

Livewire.start()


import Tribute from "tributejs";

window.Tribute = Tribute;
