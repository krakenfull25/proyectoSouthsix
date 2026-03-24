<!-- 
Esta es la página home, todas las páginas tienen header y footer iguales, en cuanto a esta página,
se muestra un display de los productos por "secciones", el titulo y la lista de productos se deben
pasar por cards.
-->

<script>
    import Header from "$lib/Components/Header.svelte";
    import DisplayProductsHome from "$lib/Components/DisplayProductsHome.svelte";
	  import Footer from "$lib/Components/Footer.svelte";
</script>



<Header/>
<DisplayProductsHome titulo={"Productos destacados"}/>
<DisplayProductsHome titulo={"Novedades"}/>
<DisplayProductsHome titulo={"Los más vendidos"}/>
<Footer/>


<style lang="scss">
    :global(body, html) {
    margin: 0;
    padding: 0;
  }
  :global(*){
    box-sizing: border-box;
  }
</style>