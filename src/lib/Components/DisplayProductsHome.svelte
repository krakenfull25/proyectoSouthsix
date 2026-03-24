<!-- 
Este componente sirve para mostrar las listas de productos de la página
home, se pasan por props el texto del h2, y la lista de productos, los 
cuales se guardaran inicialmente en un JSON, la cuál se hará mas tarde.
-->

<script>
	let { titulo } = $props();
</script>

<div class="container">
	<h2>{titulo}</h2>
	<div class="product-container">
		<p>Card de producto (componente)</p>
		<p>Card de producto (componente)</p>
		<p>Card de producto (componente)</p>
		<p>Card de producto (componente)</p>
	</div>
</div>

<style lang="scss">
	.container {
		margin: 30px 18px;

		> h2 {
			border-bottom: 1px solid #2d3e40;
		}

        > .product-container {
            display: flex;
            flex-direction: column;
            gap: 22px;

            > p {
                margin: auto;
            }
        }
	}
</style>
