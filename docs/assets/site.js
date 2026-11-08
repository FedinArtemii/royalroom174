import wordHighlight from './word-highlight.js';
import * as moment from './moment-lib.js';
import './motion.js';

const statement = document.querySelector('[data-moment="word-highlight"]');
const statementCopy = statement?.querySelector('.mo-wh__text');
if (statementCopy && !moment.reduced()) {
  wordHighlight(statement, {}, moment);
  // The animation wraps whole words, so keep the original sentence in the accessibility tree.
  statementCopy.removeAttribute('aria-label');
  statementCopy.querySelectorAll('.mo-w[aria-hidden]').forEach(word => word.removeAttribute('aria-hidden'));
}

const menu = document.querySelector('.rr-nav');
if (menu) {
  const mobile = matchMedia('(max-width: 700px)');
  const toggle = document.createElement('button');
  toggle.type = 'button';
  toggle.className = 'rr-menu-toggle';
  toggle.textContent = 'Меню';
  toggle.setAttribute('aria-controls', menu.id);
  menu.before(toggle);

  function setOpen(open) {
    menu.hidden = mobile.matches && !open;
    toggle.hidden = !mobile.matches;
    toggle.setAttribute('aria-expanded', String(open && mobile.matches));
    toggle.textContent = open && mobile.matches ? 'Закрыть' : 'Меню';
  }

  setOpen(false);
  toggle.addEventListener('click', () => setOpen(menu.hidden));
  mobile.addEventListener('change', () => setOpen(false));

  menu.addEventListener('click', event => {
    const link = event.target.closest('a[href^="#"]');
    if (!link || !mobile.matches) return;
    setOpen(false);
    const destination = document.getElementById(link.hash.slice(1));
    if (destination) {
      destination.setAttribute('tabindex', '-1');
      destination.focus({ preventScroll: true });
    }
  });

  toggle.parentElement.addEventListener('keydown', event => {
    if (event.key === 'Escape' && mobile.matches && !menu.hidden) {
      setOpen(false);
      toggle.focus();
    }
  });
}
