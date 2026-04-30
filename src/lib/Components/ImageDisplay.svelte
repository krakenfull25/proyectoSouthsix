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
        flex-direction: row;
        justify-content: space-between;
        gap: 36px;

        .main-image {
            border: 1px solid black;
            width: 500px;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;

            > img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
        }

        .other-images {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 48px;
            height: 150px;

            > img {
                width: 150px;
                height: 150px;
                border: 1px solid black;
                margin: 0;
            }
        }
    }

    
    @media (max-width: 1024px) {
        .image-display {
            flex-direction: column;

            .main-image {
                width: 100%;
                
            }

			.other-images {
				grid-template-columns: repeat(4,1fr);
				height: auto;

				> img {
					width:100%;
					height: 100%;
					object-fit: cover;
					display: block;
				}
			}
        }
    }

	@media (max-width: 600px) {
		.image-display {

			.other-images {
				grid-template-columns: repeat(3,1fr);
			}
        }
	}
}
</style>