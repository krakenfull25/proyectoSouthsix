<script>
  import hamburgerIcon from '$lib/assets/header-icons/hamburger-icon.svg';
  import searchIcon from '$lib/assets/header-icons/search-icon.svg';
  import shoppingCart from '$lib/assets/header-icons/shopping-cart.svg';
  import userProfile from '$lib/assets/header-icons/user-profile.svg';
</script>

<header>
    <div class="content">
        <img src={hamburgerIcon} alt="Icono hamburger">
        <img src={searchIcon} alt="Icono busqueda">
        <h2>EL LOGO AQUI</h2>
        <img src={shoppingCart} alt="Icono carrito">
        <img src={userProfile} alt="Icono perfil">
    </div>
</header>

<style lang="scss">
    header {
        background-color: #2D3E40;

        > .content {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: 26px;
            justify-content: space-between;
            padding: 30px 20px;
        }
    }
</style>