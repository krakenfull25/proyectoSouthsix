<!-- 
Esta es la página de detalle de producto, se mostrarán los datos de los productos mostrados en home.
-->

<script>
	import Header from '$lib/Components/Header.svelte';
	import Footer from '$lib/Components/Footer.svelte';
</script>

<Header />
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

<div class="description">
  <h2>Descripcion del producto</h2>
  <p>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been 
    the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley 
    of type and scrambled it to make a type specimen book. It has survived not only five centuries, 
    but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised 
    in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently 
    with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
  </p>
</div>

<div class="price">
  <h2>Precio: <span class="price-value">9.99&euro;</span></h2>
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

<a href=".." class="whislist">Añadir a la lista de deseados</a>
<a href=".." class="add-to-cart">Añadir al carrito</a>

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
</style>
