export async function load({ fetch }) {
  const res = await fetch('https://pagamenos.net/api-forhim/productos');
  const data = await res.json();

  return {
    productos: data.productos
  };
}