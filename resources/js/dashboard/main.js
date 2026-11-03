import { initDialogs } from './dialogs.js';
import { initFilters } from './filters.js';
import { initForms } from './forms.js';
import { initShell } from './shell.js';
import { notify } from './toast.js';

initShell();
initDialogs();
initFilters();
initForms();

const flash = document.querySelector('#app-toast')?.dataset.flash;
if (flash) notify(flash);
