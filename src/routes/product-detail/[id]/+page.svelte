<!-- 
Esta es la página de detalle de producto, se mostrarán los datos de los productos mostrados en home.
-->

<script>
	import { browser } from '$app/environment';
	import { onMount } from 'svelte';
	import Header from '$lib/Components/Header.svelte';
	import Footer from '$lib/Components/Footer.svelte';
	import ImageDisplay from '$lib/Components/ImageDisplay.svelte';

	let { data } = $props();
	let state = $state({
		quantity: 1,
		favorites: [],
		favoriteActive: false,
		cartMessage: false
	});

	onMount(() => {
		if (!browser) return;
		const favorites = JSON.parse(localStorage.getItem('favorites') || '[]').map(Number);
		const productId = Number(data.id || data.producto?.id);
		state.favorites = favorites;
		state.favoriteActive = favorites.includes(productId);
	});

	


	function toggleFavorite(event) {
		event.preventDefault();
		event.stopPropagation();

		if (!browser) return;

		const productId = Number(data.id || data.producto?.id);
		const stored = localStorage.getItem('favorites');
		let currentFavorites = stored ? JSON.parse(stored).map(Number) : [];

		if (currentFavorites.includes(productId)) {
			currentFavorites = currentFavorites.filter((fav) => fav !== productId);
			state.favoriteActive = false;
		} else {
			currentFavorites = [...currentFavorites, productId];
			state.favoriteActive = true;
		}

		currentFavorites = [...new Set(currentFavorites)];
		localStorage.setItem('favorites', JSON.stringify(currentFavorites));
		state.favorites = currentFavorites;
	}

function addToCart(e) {
	e.preventDefault();
	e.stopPropagation();

	if (!browser) return;

	const stored = localStorage.getItem('cart');
	let cart = stored ? JSON.parse(stored) : [];

	const productId = Number(data.id || data.producto?.id);
	const product = data.producto || {};
	const id = productId;
	const qty = Number(state.quantity) || 1;
	const index = cart.findIndex((p) => Number(p.id) === id);

	if (index !== -1) {
		cart[index] = {
			...cart[index],
			cantidad: cart[index].cantidad + qty
		};
	} else {
		cart.push({
			id,
			nombre: product.nombre_producto || product.nombre || '',
			precio: product.precio,
			imagen: product.imagenes?.[0],
			cantidad: qty
		});
	}

	localStorage.setItem('cart', JSON.stringify(cart));
	state.cartMessage = true;
	setTimeout(() => (state.cartMessage = false), 3000);
}
</script>

<Header />
<ImageDisplay producto={data.producto}/>

<div class="description">
	<h2>Descripcion del producto</h2>
	<p>
		{data.producto.descripcion}
	</p>
</div>

<div class="price">
	<h2>Precio: <span class="price-value">{data.producto.precio}&euro;</span></h2>
</div>

<div class="quantity">
	<h2>Cantidad:</h2>
	<select bind:value={state.quantity} name="quantity" id="quantity">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
	</select>
</div>

<div class="buttons">
	<button type="button" onclick={toggleFavorite}  class="whislist" class:active={state.favoriteActive}>
		{state.favoriteActive ? 'En favoritos' : 'Añadir a la lista de deseados'}
	</button>
	<button type="button" onclick={addToCart} class="add-to-cart">Añadir al carrito</button>
	{#if state.cartMessage}
		<p class="cart-message">Producto añadido al carrito</p>
	{/if}
</div>

<Footer />

<style lang="scss">
	:global(body, html) {
		margin: 0;
		padding: 0;
		font-family: 'PT Sans Narrow', sans-serif;
	}
	:global(*) {
		box-sizing: border-box;
	}

	.description {
		margin: 30px 18px;
		margin-top: 90px;
	}

	.price {
		margin: 30px 18px;
		margin-top: 90px;

		> h2 {
			border-bottom: 1px solid black;

			> .price-value {
				font-weight: 300;
			}
		}
	}

.quantity {
  margin: 30px 18px;
  margin-top: 90px;
  display: flex;
  align-items: center;
  gap: 20px;

  > select {
    width: 80px;
    border-radius: 20px;
    padding: 6px 10px;
    font-size: 15px;
  }
}

	.whislist:hover{
		cursor: pointer;
		
	}

	.add-to-cart:hover{
		cursor: pointer;
	}
	.buttons {
  margin: 30px auto;
  margin-top: 90px;
  width: 90%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: 30px;

  > .whislist {
    text-decoration: none;
    color: black;
    background-color: #E4F2E7;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid black;
    border-radius: 15px;
  }

  > .whislist.active {
    background-color: #c8e6c9;
    border-color: #43a047;
  }

  > .add-to-cart {
    text-decoration: none;
    color: black;
    background-color: #93BFB7;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid black;
    border-radius: 15px;
  }

  .cart-message {
    color: #43a047;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    margin-top: 0;
  }
}

	@media (min-width: 1024px) {
  .quantity {
    width: 100%;
  }

  .buttons {
    width: 33.333%;
    max-width: none;
  }
}

</style>
