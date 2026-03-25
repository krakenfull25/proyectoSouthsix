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
    }
    
</style>