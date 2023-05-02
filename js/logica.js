
const API_URL = "../php/index.php";

async function calcularDescuento() {
    const nombre = document.getElementById("nombre").value;
    const valor = document.getElementById("valor").value;

    const response = await fetch(`${API_URL}?console=${nombre}&valor=${valor}`)
    .then(response => response.json())
    .then(data => {
        const resultadoDiv = document.getElementById("resultado");

        if (data.valorCobrarCliente) {
            resultadoDiv.innerHTML = `Total a pagar: ${data.valorCobrarCliente}`;
        } else {
            resultadoDiv.innerHTML = "No se pudo obtener el valor a pagar";
        }
    })
    .catch(error => console.error(error));
}

async function obtenerSumaDescuentos() {

    const response = await fetch(`${API_URL}`)
    .then(response => response.json())
    .then(data => {
        const sumaDescuentosDiv = document.getElementById("suma_descuentos");

        if (data.totalDescuentos) {
            sumaDescuentosDiv.innerHTML = `Total de dinero: ${data.totalDescuentos}`;
        } else {
            sumaDescuentosDiv.innerHTML = "No se pudo obtener el total de descuentos";
        }
    })
    .catch(error => console.error(error));
}