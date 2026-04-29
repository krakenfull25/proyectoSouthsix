export async function load({ fetch, params }) {
  const res = await fetch(`https://pagamenos.net/api-forhim/productos/${params.id}`);
  const data = await res.json();

  return {
    producto: data.producto
  };
}