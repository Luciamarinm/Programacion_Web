document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('buscadorUsuarios'); //Recoge el contenido del input
    const tabla = document.getElementById('tablaUsuarios'); //Recoge el contenido de la tabla
    if (input && tabla) {
        input.addEventListener('keyup', function () {
            const filtro = input.value.toLowerCase(); //Contenido del input a minúsculas
            const filas = tabla.querySelectorAll('tbody tr'); //Contenido de los tr
            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase(); //Contenido de la fila a minúsculas
                fila.style.display = texto.includes(filtro) ? '' : 'none'; //Si el texto de la fila coincide se muestra o no
            });
        });
    }
});