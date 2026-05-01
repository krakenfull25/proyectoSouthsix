export async function load({ fetch, url }) {
    const ids = url.searchParams.get('ids')?.split(',').filter(Boolean) ?? [];

    if (ids.length === 0) return { productos: [] };

    const results = await Promise.all(
        ids.map(id => 
            fetch(`https://pagamenos.net/api-forhim/productos/${id}`)
                .then(r => r.json())
                .catch(() => null)
        )
    );

    return {
        productos: results.map(d => d?.producto).filter(Boolean)
    };
}