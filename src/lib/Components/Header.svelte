<script>
  import { browser } from '$app/environment';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';

  import hamburgerIcon from '$lib/assets/header-icons/hamburger-icon.svg';
  import searchIcon from '$lib/assets/header-icons/search-icon.svg';
  import shoppingCart from '$lib/assets/header-icons/shopping-cart.svg';
  import userProfile from '$lib/assets/header-icons/user-profile.svg';

	let token = browser ? localStorage.getItem('token') : null;
	let perfilUrl = token ? '/user-profile' : '/login';

	function cerrarSesion() {
		localStorage.removeItem('token');
		localStorage.removeItem('usuario');
		goto('/');
		location.reload();
	}
  /* SOLO MÓVIL */
  let searchOpen = $state(false);

  let searchQuery = $state('');

  function toggleSearch() {
    searchOpen = !searchOpen;

    if (!searchOpen) {
      searchQuery = '';
    }
  }

  function handleSearch(e) {
    e.preventDefault();

    if (searchQuery.trim()) {
      goto(`/productos?q=${encodeURIComponent(searchQuery.trim())}`);

      searchQuery = '';
      searchOpen = false;
    }
  }

  /* CART */
  let cartCount = $state(0);

  function updateCartCount() {
    if (browser) {
      const stored = localStorage.getItem('cart');
      const cart = stored ? JSON.parse(stored) : [];

      cartCount = cart.reduce(
        (sum, item) => sum + item.cantidad,
        0
      );
    }
  }

  onMount(() => {
    updateCartCount();

    const interval = setInterval(updateCartCount, 1000);

    return () => clearInterval(interval);
  });
</script>

<header>
  <input type="checkbox" id="menu-toggle" />

  <!-- MOBILE MENU -->
  <nav class="mobile-menu">
    <label for="menu-toggle" class="close-btn">✕</label>

    <a href="/">Inicio</a>
    <a href="/productos">Productos</a>
    <a href={perfilUrl}>Perfil</a>
    <a href="/lista-deseos">Lista de deseos</a>
	<a href="/carrito">Carrito</a>
		{#if token}
			<button class="btn-logout-menu" onclick={cerrarSesion}>Cerrar sesión</button>
		{/if}
  </nav>

  <label for="menu-toggle" class="overlay"></label>

  <!-- MOBILE -->
  <div class="content">
    <label for="menu-toggle" class="hamburger">
      <img src={hamburgerIcon} alt="Icono hamburger" />
    </label>

    <button class="search-btn" onclick={toggleSearch} aria-label="Buscar">
      <img src={searchIcon} alt="Icono busqueda" />
    </button>

    <a class="logo-link" href="/">
      <div class="logo">
        <p>FOR-HIM</p>
        <p>GROOMING & BEYOND</p>
      </div>
    </a>

    <a href="/carrito" class="cart-link">
      <img src={shoppingCart} alt="Icono carrito" />

      {#if cartCount > 0}
        <span class="cart-count">{cartCount}</span>
      {/if}
    </a>

    <a href={perfilUrl}>
      <img src={userProfile} alt="Icono perfil" />
    </a>
  </div>

  <!-- MOBILE SEARCH -->
  {#if searchOpen}
    <div class="search-bar-mobile">
      <form onsubmit={handleSearch}>
        <input
          type="text"
          bind:value={searchQuery}
          placeholder="Buscar productos..."
          autofocus
        />

        <button type="submit" aria-label="Buscar">
          <img src={searchIcon} alt="Buscar" />
        </button>

        <button
          type="button"
          class="close"
          onclick={toggleSearch}
        >
          ✕
        </button>
      </form>
    </div>
  {/if}

  <!-- DESKTOP -->
  <div class="content-desktop">
    <!-- LEFT -->
    <div class="left-section">
      <label for="menu-toggle" class="hamburger">
        <img src={hamburgerIcon} alt="Icono hamburger" />
      </label>
    </div>

    <!-- CENTER -->
    <a class="logo-link" href="/">
      <div class="logo">
        <p>FOR-HIM</p>
        <p>GROOMING & BEYOND</p>
      </div>
    </a>

    <!-- RIGHT -->
    <div class="other-icons">
      <!-- SEARCH -->
      <form class="search-desktop" onsubmit={handleSearch}>
        <input
          type="text"
          bind:value={searchQuery}
          placeholder="Buscar..."
        />

        <button type="submit" aria-label="Buscar">
          <img src={searchIcon} alt="Icono busqueda" />
        </button>
      </form>

      <!-- CART -->
      <a href="/carrito" class="cart-link">
        <img src={shoppingCart} alt="Icono carrito" />

        {#if cartCount > 0}
          <span class="cart-count">{cartCount}</span>
        {/if}
      </a>

      <!-- PROFILE -->
      <a href={perfilUrl}>
        <img src={userProfile} alt="Icono perfil" />
      </a>
	  {#if token}
				<button class="btn-logout-desktop" onclick={cerrarSesion}>Cerrar sesión</button>
			{/if}
    </div>
  </div>
</header>

<style lang="scss">
  header {
    background-color: #2d3e40;
    margin-bottom: 60px;

    #menu-toggle {
      display: none;
    }

    .hamburger {
      cursor: pointer;
      display: flex;
    }

    .search-btn {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0;

      display: flex;
      align-items: center;
    }

    /* =========================
       MOBILE SEARCH
    ========================== */

    .search-bar-mobile {
      background-color: #1e2d2f;
      padding: 12px 20px;

      animation: slideDown 0.25s ease forwards;

      form {
        display: flex;
        gap: 8px;
        align-items: center;

        input {
          flex: 1;
          padding: 8px 12px;

          border-radius: 6px;
          border: none;

          font-size: 15px;
          outline: none;

          background: #fff;
        }

        button {
          background: none;
          border: none;
          cursor: pointer;

          padding: 4px;

          display: flex;
          align-items: center;

          img {
            width: 24px;
          }

          &.close {
            color: white;
            font-size: 18px;

            img {
              display: none;
            }
          }
        }
      }
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* =========================
       MOBILE MENU
    ========================== */

    .mobile-menu {
      position: fixed;
      top: 0;
      left: -260px;

      width: 260px;
      height: 100vh;

      background: #387373;

      display: flex;
      flex-direction: column;

      padding: 20px;
      gap: 20px;

      transition: left 0.3s ease;

      z-index: 1000;
    }

    .mobile-menu a {
      color: white;
      text-decoration: none;
      font-size: 18px;
    }

    .close-btn {
      color: white;
      font-size: 26px;

      cursor: pointer;

      align-self: flex-start;
      margin-bottom: 10px;
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;

      width: 100%;
      height: 100vh;

      background: rgba(0, 0, 0, 0.4);

      opacity: 0;
      pointer-events: none;

      transition: opacity 0.3s ease;

      z-index: 900;
    }

    #menu-toggle:checked ~ .mobile-menu {
      left: 0;
    }

    #menu-toggle:checked ~ .overlay {
      opacity: 1;
      pointer-events: all;
    }

    /* =========================
       MOBILE HEADER
    ========================== */

    .content {
      display: flex;
      justify-content: space-between;
      align-items: center;

      padding: 30px 20px;

      .logo-link {
        text-decoration: none;

        .logo {
          display: flex;
          flex-direction: column;
          align-items: center;

          color: white;

          p {
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

      img {
        display: block;
        object-fit: cover;
      }
	  > a > img {
				display: block;
				object-fit: cover;
			}
    }

    /* CART */
    .cart-link {
      position: relative;
      display: flex;
      align-items: center;

      .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;

        background: #ff6b6b;
        color: white;

        border-radius: 50%;

        width: 18px;
        height: 18px;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 12px;
        font-weight: bold;
      }
    }

    /* DESKTOP HIDDEN */
    .content-desktop {
      display: none;
    }
  }
  .btn-logout-menu {
		background: transparent;
		border: 1px solid white;
		border-radius: 6px;
		color: white;
		font-size: 16px;
		padding: 8px 12px;
		cursor: pointer;
		font-family: 'PT Sans Narrow', sans-serif;
		text-align: left;

		&:hover {
			background: rgba(255, 255, 255, 0.15);
		}
	}
	.btn-logout-desktop {
		background: transparent;
		border: 1px solid white;
		border-radius: 6px;
		color: white;
		font-size: 14px;
		padding: 6px 12px;
		cursor: pointer;
		font-family: 'PT Sans Narrow', sans-serif;
	&:hover {
			background: rgba(255, 255, 255, 0.15);
		}
	}

  /* =========================================
     DESKTOP
  ========================================== */

  @media (min-width: 1024px) {
    header {
      .content {
        display: none;
      }

      .search-bar-mobile {
        display: none;
      }

      .content-desktop {
        display: grid;
        grid-template-columns: 1fr auto 1fr;

        align-items: center;

        padding: 30px 40px;
      }

      /* LEFT */
      .left-section {
        display: flex;
        justify-content: flex-start;
        align-items: center;
      }

      /* CENTER */
      .logo-link {
        text-decoration: none;
        justify-self: center;

        .logo {
          display: flex;
          flex-direction: column;
          align-items: center;

          color: white;

          p {
            margin: 0;
          }

          :nth-child(1) {
            font-size: 40px;
            font-weight: 700;
          }

          :nth-child(2) {
            font-size: 15px;
            font-weight: 400;
            letter-spacing: 2px;
          }
        }
      }

      /* RIGHT */
      .other-icons {
        display: flex;
        justify-content: flex-end;
        align-items: center;

        gap: 14px;
      }

      .other-icons > a > img,
      .hamburger img {
        width: 30px;
      }

      /* SEARCH DESKTOP */
      .search-desktop {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 6px 12px;

        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 999px;

        background: rgba(255, 255, 255, 0.05);

        input {
          width: 180px;

          border: none;
          outline: none;

          background: transparent;

          color: white;
          font-size: 15px;

          &::placeholder {
            color: rgba(255, 255, 255, 0.6);
          }
        }

        button {
          background: none;
          border: none;

          cursor: pointer;

          display: flex;
          align-items: center;

          padding: 0;

          img {
            width: 22px;
          }
        }
      }
    }
  }
</style>