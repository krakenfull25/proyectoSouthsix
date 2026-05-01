export async function GET({ url }) {
	const idsParam = url.searchParams.get('ids');
	
	if (!idsParam) {
		return new Response(JSON.stringify({ productos: [] }), {
			headers: { 'Content-Type': 'application/json' }
		});
	}

	const ids = idsParam.split(',').filter(Boolean);

	try {
		// Traer todos los productos en paralelo
		const requests = ids.map(async (id) => {
			try {
				const res = await fetch(`https://pagamenos.net/api-forhim/productos/${id}`);
				if (!res.ok) return null;

				const data = await res.json();
				return data.producto ?? null;
			} catch {
				return null;
			}
		});

		const results = await Promise.all(requests);
		const productos = results.filter(Boolean);

		return new Response(JSON.stringify({ productos }), {
			headers: { 'Content-Type': 'application/json' }
		});
	} catch (error) {
		console.error('Error en GET /carrito:', error);
		return new Response(JSON.stringify({ productos: [] }), {
			status: 500,
			headers: { 'Content-Type': 'application/json' }
		});
	}
}