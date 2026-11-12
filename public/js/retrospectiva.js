(() => {
const form = document.querySelector('#revision');
if (!form) return;
const etapas = [...form.querySelectorAll('[data-etapa]')];
const indicadores = [...document.querySelectorAll('[data-indicador]')];
const atras = document.querySelector('#atras');
const siguiente = document.querySelector('#siguiente');
let paso = 0;
function mostrar() {
  etapas.forEach((el, i) => { el.hidden = i !== paso; });
  indicadores.forEach((el, i) => { if (i === paso) el.setAttribute('aria-current', 'step'); else el.removeAttribute('aria-current'); });
  atras.hidden = paso === 0;
  siguiente.hidden = paso === etapas.length - 1;
  const checks = [...form.querySelectorAll('input[type=checkbox]')];
  const hechas = checks.filter(el => el.checked).length;
  const adicionales = form.elements.adicionales.value.split(/\r?\n/).filter(v => v.trim()).length;
  document.querySelector('#balance-seleccion').textContent = 'Objetivos: ' + hechas + ' realizados y ' + (checks.length - hechas) + ' pendientes. Tareas adicionales realizadas: ' + adicionales + '.';
}
atras.addEventListener('click', () => { paso = Math.max(0, paso - 1); mostrar(); });
siguiente.addEventListener('click', () => { paso = Math.min(etapas.length - 1, paso + 1); mostrar(); });
form.addEventListener('submit', event => {
  if (paso !== etapas.length - 1) { event.preventDefault(); paso = Math.min(etapas.length - 1, paso + 1); mostrar(); }
});
mostrar();
})();
