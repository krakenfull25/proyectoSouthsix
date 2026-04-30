<!-- 
Esta es la página de detalle de producto, se mostrarán los datos de los productos mostrados en home.
-->

<script>
	import Header from '$lib/Components/Header.svelte';
	import Footer from '$lib/Components/Footer.svelte';
	import ImageDisplay from '$lib/Components/ImageDisplay.svelte';

	let { data } = $props();
	
	let producto = $derived(data.producto);
</script>

<Header />
<ImageDisplay {producto}/>

<div class="description">
	<h2>Descripcion del producto</h2>
	<p>
		{producto.descripcion}
	</p>
</div>

<div class="price">
	<h2>Precio: <span class="price-value">{producto.precio}&euro;</span></h2>
</div>

<div class="quantity">
	<h2>Cantidad:</h2>
	<select name="quantity" id="quantity">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
	</select>
</div>

<div class="buttons">
	<a href=".." class="whislist">Añadir a la lista de deseados</a>
	<a href=".." class="add-to-cart">Añadir al carrito</a>
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
		gap: 20px;
		

		> select {
			flex: 1;
			border-radius: 20px;
			
		}
	}

	.buttons {
		margin: 30px 18px;
		margin-top: 90px;
		display: flex;
		flex-direction: column;
		gap: 30px;

		>.whislist {
			text-decoration: none;
			color: black;
			background-color: #E4F2E7;
			height: 50px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 20px;
			border: 1px solid black;
			border-radius: 15px;
		}

		>.add-to-cart {
			text-decoration: none;
			color: black;
			background-color: #93BFB7;
			height: 50px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 20px;
			border: 1px solid black;
			border-radius: 15px;
		}
	}

	@media (min-width: 1024px) {
		
		.quantity {
			width: 40%;

			
		}
	}

</style>
