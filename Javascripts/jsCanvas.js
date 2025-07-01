const canvas = document.getElementById('canvasLogo');
const ctx = canvas.getContext('2d');

//Fondo
ctx.fillStyle = '#2b4ba1';
ctx.fillRect(0, 0, canvas.width, canvas.height);

//Texto
ctx.fillStyle = '#23CACA';
ctx.font = 'bold 28px Arial';
ctx.textAlign = 'center';
ctx.textBaseline = 'middle';
ctx.fillText('VETPET', canvas.width / 2, canvas.height / 2); //Centrar texto