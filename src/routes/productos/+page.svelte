<script>
  import { page } from '$app/state';
  import Header from '$lib/Components/Header.svelte';
  import DisplayProductsHome from '$lib/Components/DisplayProductsHome.svelte';
  import Footer from '$lib/Components/Footer.svelte';

  const { data } = $props();

  const q = $derived(page.url.searchParams.get('q') || '');

  function normalizar(str) {
  return (str ?? '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');
}

const filtered = $derived(
  q
    ? data.productos.filter((p) =>
        normalizar(p.nombre).includes(normalizar(q)) ||
        normalizar(p.descripcion).includes(normalizar(q))
      )
    : data.productos
);
</script>

<svelte:head>
  <title>
    {q ? `Resultados para "${q}"` : 'Todos los productos'} — FOR-HIM
  </title>
</svelte:head>

<Header />

<main>
  <div class="search-header">
    <h1>{q ? `Resultados para "${q}"` : 'Todos los productos'}</h1>

    {#if q}
      {#if filtered.length === 0}
        <p class="no-results">No se encontraron productos para "<strong>{q}</strong>".</p>
      {:else}
        <p class="count">
          {filtered.length} producto{filtered.length !== 1 ? 's' : ''} encontrado{filtered.length !== 1 ? 's' : ''}
        </p>
      {/if}
    {/if}
  </div>

  <DisplayProductsHome titulo={q ? 'Resultados' : 'Todos los productos'} productos={filtered} />
</main>

<Footer />

<style lang="scss">
  main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px 60px;
    min-height: 30rem;
  }
  :global(footer) {
		margin-top: auto;
	}

	:global(body, html) {
		margin: 0;
		padding: 0;
        font-family: 'PT Sans Narrow', sans-serif;
	}

	:global(*) {
		box-sizing: border-box;
	}

  .search-header {
    margin-bottom: 16px;

    h1 {
      font-size: 24px;
      font-weight: 700;
      color: #2d3e40;
      margin: 0 0 8px;
    }
  }

  .count {
    color: #666;
    font-size: 14px;
    margin: 0;
  }

  .no-results {
    text-align: center;
    color: #666;
    margin-top: 60px;
    font-size: 18px;
  }
</style>