<!-- 
Esta es la página home, todas las páginas tienen header y footer iguales, en cuanto a esta página,
se muestra un display de los productos por "secciones", el titulo y la lista de productos se deben
pasar por cards.
-->

<script>
    import Header from "$lib/Components/Header.svelte";
    import DisplayProductsHome from "$lib/Components/DisplayProductsHome.svelte";
	  import Footer from "$lib/Components/Footer.svelte";

    let { data } = $props();
</script>



<Header/>
<DisplayProductsHome titulo={"Productos destacados"} productos={data.productos}/>
<DisplayProductsHome titulo={"Novedades"} productos={data.productos}/>
<DisplayProductsHome titulo={"Los más vendidos"} productos={data.productos}/>
<Footer/>


<style lang="scss">
    :global(body, html) {
    margin: 0;
    padding: 0;
    font-family: 'PT Sans Narrow', sans-serif;
  }
  :global(*){
    box-sizing: border-box;
  }
</style>