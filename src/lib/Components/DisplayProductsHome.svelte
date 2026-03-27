<!-- 
Este componente sirve para mostrar las listas de productos de la página
home, se pasan por props el texto del h2, y la lista de productos, los 
cuales se guardaran inicialmente en un JSON, la cuál se hará mas tarde.
-->

<script>
	let { titulo } = $props();
	import ProductCard from '$lib/Components/ProductCard.svelte';
</script>

<div class="container">
	<h2>{titulo}</h2>
	<div class="product-container">
		<ProductCard />
		<ProductCard />
		<ProductCard />
		<ProductCard />
	</div>
</div>

<style lang="scss">
	.container {
		margin: 30px 18px;

		> h2 {
			border-bottom: 1px solid #2d3e40;
			padding-bottom: 8px;
		}

		.product-container {
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 22px;

			> :global(.card) {
				width: 95%;       // móvil: ocupa casi todo el ancho
				max-width: 420px;
				min-width: 250px;
				flex-shrink: 1;   // permite encoger si la ventana es más pequeña
			}
		}

		@media (min-width: 768px) {
			.product-container {
				flex-direction: row;
				flex-wrap: wrap;
				justify-content: center;
				gap: 20px;

				> :global(.card) {
					width: 90%;      // desktop: ancho flexible
					max-width: 420px;
					min-width: 250px;
				}
			}
		}
	}
</style>
