<script>
    import { onMount } from 'svelte';
    import Header from '$lib/Components/Header.svelte';
    import Footer from '$lib/Components/Footer.svelte';
    import Favs from '$lib/Components/FavsProducts.svelte';

    let productos = $state([]);
    let loading = $state(true);

    onMount(async () => {
        const stored = localStorage.getItem('favorites') ?? '[]';
        const ids = JSON.parse(stored).filter(Boolean).join(',');

        if (!ids) {
            loading = false;
            return;
        }

        try {
            const res = await fetch(`/lista-deseos/api?ids=${ids}`);
            const data = await res.json();
            productos = data.productos ?? [];
        } catch (e) {
            console.error(e);
        } finally {
            loading = false;
        }
    });
</script>

<Header />
<Favs  {productos} {loading} />
<Footer />


<style>
	
    

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

 


</style>
