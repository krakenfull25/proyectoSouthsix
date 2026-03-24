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
        <div class="logo">
            <p>FOR-HIM</p>
            <p>GROOMING & BEYOND</p>
        </div>
        <img src={shoppingCart} alt="Icono carrito">
        <img src={userProfile} alt="Icono perfil">
    </div>
</header>

<style lang="scss">
    header {
        background-color: #2D3E40;
        margin-bottom: 60px;

        > .content {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: 26px;
            justify-content: space-between;
            padding: 30px 20px;

            > .logo {
                display: flex;
                flex-direction: column;
                align-items: center;
                color: #FFFFFF;

                > p {
                    margin: 0;
                }

                :nth-child(1) {
                    font-size: 40px;
                    font-weight: 700;
                }

                :nth-child(2) {
                    font-size: 15px;
                    font-weight: 400;
                }
            }
        }
    }
</style>