(() => {
  const editor = document.querySelector('#editor-objetivos');
  if (!editor) return;
  const campo = document.querySelector('#nuevo-objetivo');
  const datos = document.querySelector('#objetivos-datos');
  const lista = document.querySelector('#lista-objetivos');
  const mensaje = document.querySelector('#mensaje-objetivos');
  const form = editor.closest('form');
  const objetivos = datos.value.split(/\r?\n/).map(v => v.trim()).filter(Boolean);
  document.querySelector('#objetivos-alternativa').hidden = true;
  editor.hidden = false;
  function mostrar() {
    datos.value = objetivos.join('\n');
    lista.replaceChildren();
    objetivos.forEach((texto, indice) => {
      const li = document.createElement('li');
      const span = document.createElement('span');
      span.textContent = texto;
      const quitar = document.createElement('button');
      quitar.type = 'button';
      quitar.className = 'button light';
      quitar.textContent = 'Quitar';
      quitar.setAttribute('aria-label', 'Quitar objetivo ' + (indice + 1));
      quitar.addEventListener('click', () => {
        objetivos.splice(indice, 1);
        mostrar();
        mensaje.textContent = 'Objetivo eliminado. Quedan ' + objetivos.length + '.';
        campo.focus();
      });
      li.append(span, quitar);
      lista.append(li);
    });
  }
  function anadir() {
    const texto = campo.value.trim();
    if (!texto) {
      mensaje.textContent = 'Escribe un objetivo antes de añadirlo.';
      campo.focus();
      return false;
    }
    if (objetivos.includes(texto)) {
      mensaje.textContent = 'Este objetivo ya está en la lista.';
      campo.focus();
      return false;
    }
    if (objetivos.length >= 100) {
      mensaje.textContent = 'Puedes añadir hasta 100 objetivos por sprint.';
      return false;
    }
    objetivos.push(texto);
    campo.value = '';
    mostrar();
    mensaje.textContent = 'Objetivo añadido. Total: ' + objetivos.length + '.';
    campo.focus();
    return true;
  }
  document.querySelector('#anadir-objetivo').addEventListener('click', anadir);
  campo.addEventListener('keydown', event => {
    if (event.key === 'Enter') { event.preventDefault(); anadir(); }
  });
  form.addEventListener('submit', event => {
    // Incluye también el último objetivo si aún está escrito en el campo.
    if (campo.value.trim() && !anadir()) { event.preventDefault(); return; }
    const retomados = form.querySelectorAll('input[name="retomar[]"]:checked').length;
    if (!objetivos.length && !retomados) {
      event.preventDefault();
      mensaje.textContent = 'Añade al menos un objetivo o selecciona un pendiente para retomar.';
      campo.focus();
    }
  });
  mostrar();
})();
