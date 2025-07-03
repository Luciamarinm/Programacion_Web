//Filtro formulario 
document.getElementById('filtroUsuarios').addEventListener('input', function() {
    const filtro = this.value.toLowerCase(); //Contenido del filtro a minúsculas
    const opciones = document.getElementById('selectorUsuarios').options;
    //Recoge las opciones
    for (let i = 0; i < opciones.length; i++) {
      const texto = opciones[i].text.toLowerCase();
      opciones[i].style.display = texto.includes(filtro) ? '' : 'none';
    }
});
//Filtro formulario 
function filtrarOpciones() {
    let filtro = document.getElementById('filtroUsuarios').value.toLowerCase();
    let select = document.getElementById('selectorUsuarios');
    //Recoge las opciones
    let options = select.options;
    for (let i = 0; i < options.length; i++) {
      let texto = options[i].text.toLowerCase(); //Contenido del texto a minúsculas
      if (texto.indexOf(filtro) > -1 || options[i].value === "") {
        options[i].style.display = "";
      } else {
        options[i].style.display = "none";
      }
    }
  }




