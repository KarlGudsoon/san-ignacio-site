fetch("/luminary/api/estudiante/me.php")
  .then((res) => {
    if (!res.ok) {
      window.location.href = "/luminary/";
      return;
    }
    return res.json();
  })
  .then((data) => {
    document
      .querySelectorAll('[data-estudiante="nombre"]')
      .forEach((el) => (el.textContent = data.nombre));

    document
      .querySelectorAll('[data-estudiante="apellidos"]')
      .forEach((el) => (el.textContent = data.apellidos));
  })
  .catch(() => {
    window.location.href = "/luminary/";
  });
