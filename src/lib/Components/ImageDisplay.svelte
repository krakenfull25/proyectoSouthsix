<!--
Este componente se utiliza en product-detail, se muestra las imagenes del producto seleccionado.
-->

<div class="image">
	<h2>Nombre del producto</h2>
	<div class="image-display">
		<div class="main-image">
			<p>IMAGEN PRINCIPAL SELECCIONADA</p>
		</div>
		<div class="other-images">
			<p>Imagen 1</p>
			<p>Imagen 2</p>
			<p>Imagen 3</p>
			<p>Imagen 4</p>
			<p>Imagen 5</p>
			<p>Imagen 6</p>
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
				border: 1px solid black;
				gap: 20px;
				> p {
					width: 20%;
					border: 1px solid black;
					width: 100%;
					margin: 0;
				}
			}
		}
    }
    
	@media (min-width:1024px) {
		.image {
			> .image-display {
				flex-direction: row;
			}
		}
	}
</style>