<script>
	import { onMount } from 'svelte';
	import Header from '$lib/Components/Header.svelte';
	import Footer from '$lib/Components/Footer.svelte';

	let cart = $state([]);            
	let cartIds = $state([]);
	let cartProducts = $state([]);
	let loading = $state(true);

	onMount(async () => {
		try {
			const stored = localStorage.getItem('cart');
			cart = stored ? JSON.parse(stored) : [];

			
			cartIds = cart.map(p => p.id);

			if (cartIds.length === 0) {
				loading = false;
				return;
			}

			const res = await fetch(`/carrito/api?ids=${cartIds.join(',')}`);
			if (!res.ok) throw new Error('Error API carrito');

			const data = await res.json();
			cartProducts = data.productos;

		} catch (err) {
			console.error(err);
			cartProducts = [];
		} finally {
			loading = false;
		}
	});

	
	function save(newCart) {
	cart = newCart;
	localStorage.setItem('cart', JSON.stringify(cart));

	
	cartProducts = cartProducts.filter(p =>
		cart.some(c => c.id === p.id)
	);
}

	
	function remove(id) {
		save(cart.filter(p => p.id !== id));
	}

	
	function increase(id) {
		const item = cart.find(p => p.id === id);
		if (item) {
			item.cantidad += 1;
			save([...cart]);
		}
	}

	
	function decrease(id) {
		const item = cart.find(p => p.id === id);

		if (!item) return;

		if (item.cantidad > 1) {
			item.cantidad -= 1;
			save([...cart]);
		} else {
			remove(id);
		}
	}

	
	function getCantidad(id) {
		return cart.find(p => p.id === id)?.cantidad || 1;
	}

	
	let total = $derived(
		cartProducts.reduce((sum, product) => {
			const cantidad = getCantidad(product.id);
			return sum + product.precio * cantidad;
		}, 0)
	);
</script>

<Header />

<section class="cart">
	<h1>Carrito</h1>

	{#if loading}
		<p>Cargando...</p>

	{:else if cartProducts.length === 0}
		<p>El carrito está vacío</p>

	{:else}
		{#each cartProducts as item (item.id)}
	<div class="item">
		<img src={item.imagenes?.[0]} />

		<div class="info">
			<p>{item.nombre_producto}</p>
			<p>{item.precio.toFixed(2)} €</p>
		</div>

		<div class="qty">
			<button on:click={() => decrease(item.id)}
            disabled={getCantidad(item.id) === 1}>-</button>
			<span>{getCantidad(item.id)}</span>
			<button on:click={() => increase(item.id)}>+</button>
		</div>

		<button on:click={() => remove(item.id)}>✕</button>
	</div>
{/each}

		<h2>Total: {total.toFixed(2)} €</h2>
	{/if}
    {#if cartProducts.length > 0}
	<a href="/checkout" class="buy">Comprar</a>
{/if}
</section>

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
	.cart {
		max-width: 800px;
		margin: 40px auto;
		padding: 0 20px;
		min-height: 70vh;
	}

	.item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px;
		border: 1px solid #ddd;
		border-radius: 10px;
		margin-bottom: 10px;
	}

	img {
		width: 70px;
		height: 70px;
		object-fit: cover;
	}

	.info {
		flex: 1;
	}

	.qty {
		display: flex;
		gap: 8px;
		align-items: center;
	}
    .buy {
	display: block;
	text-align: center;
	width: 100%;
	margin-top: 20px;
	padding: 14px;
	background: #387373;
	color: white;
	border-radius: 10px;
	font-size: 18px;
	text-decoration: none;
	transition: background 0.2s;
}

.buy:hover {
	background: #2d5f5f;
}
    button:disabled {
	opacity: 0.4;
	cursor: not-allowed;
}
</style>