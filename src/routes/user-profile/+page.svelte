<!-- 
Esta es la página de perfil de usuario, aqui se muestra un formulario con los datos del usuario seleccionado, todos
en un formulario con su formato respectivo.
-->

<script>
    import Header from "$lib/Components/Header.svelte";
	  import Footer from "$lib/Components/Footer.svelte";
    import UserForm from "$lib/Components/User-form.svelte";
</script>



<Header/>
<UserForm/>
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