import './bootstrap.js';
import * as bootstrap from 'bootstrap';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

document.addEventListener('modal:close', () => {
	const openModals = document.querySelectorAll('.modal.show');
	for (const openModal of openModals) {
		bootstrap.Modal.getInstance(openModal).hide();
	}
})
