<!--
Este componente se utiliza en product-detail, se muestra las imagenes del producto seleccionado.
-->
<script>
	let { producto } = $props();
</script>

<div class="image">
	<h2>{producto.nombre_producto}</h2>
	<div class="image-display">
		<div class="main-image">
			<img src={producto.imagenes[0]} alt="imagen"/>
		</div>
		<div class="other-images">
			{#each producto.imagenes as imagen}
				<img src={imagen} alt="imagen"/>
			{/each}
		</div>
	</div>
</div>

<style lang="scss">
    .image {
        margin: 30px 18px;

        > h2 {
            border-bottom: 1px solid black;
        }

		.image-display {
			display: flex;
			flex-direction: column;
			gap: 36px;

			.main-image {
				width: 100%;
				border: 1px solid black;

				> img {
					width: 100%;
					height: 100%;
					object-fit: cover;
					display: block;
				}
			}

			.other-images {
				display: grid;
				grid-template-columns: repeat(3,1fr);
				gap: 20px;
				
				> img {
					position: relative;
					width: 150px;
					height: 150px;
					border: 1px solid black;
					margin: 0;
				}
			}
		}
    }
    
	@media (min-width:1024px) {
		.image {
			> .image-display {
				flex-direction: row;
				gap: 50px;

				.other-images {
				display: grid;
				> img {
					
					border: 1px solid black;
					margin: 0;
				}
			}
			}
		}
	}
</style>