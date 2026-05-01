<script>
	import { browser } from '$app/environment';
	import hamburgerIcon from '$lib/assets/header-icons/hamburger-icon.svg';
	import searchIcon from '$lib/assets/header-icons/search-icon.svg';
	import shoppingCart from '$lib/assets/header-icons/shopping-cart.svg';
	import userProfile from '$lib/assets/header-icons/user-profile.svg';

	let sesion = browser ? localStorage.getItem('sesion_activa') : null;
	let perfilUrl = sesion ? '/user-profile' : '/login';
</script>

<header>
	<input type="checkbox" id="menu-toggle" />

	<nav class="mobile-menu">
		<label for="menu-toggle" class="close-btn">✕</label>

		<a href="/">Inicio</a>
		<a href="#">Productos</a>
		<a href={perfilUrl}>Perfil</a>
		<a href="/lista-deseos">Lista de deseos</a>
	</nav>

	<label for="menu-toggle" class="overlay"></label>
	<div class="content">
		<label for="menu-toggle" class="hamburger">
			<img src={hamburgerIcon} alt="Icono hamburger" />
		</label>

		<img src={searchIcon} alt="Icono busqueda" />
		<a class="logo-link" href="/">
			<div class="logo">
				<p>FOR-HIM</p>
				<p>GROOMING & BEYOND</p>
			</div>
		</a>

		<img src={shoppingCart} alt="Icono carrito" />
		<a href={perfilUrl}>
			<img src={userProfile} alt="Icono perfil" />
		</a>
	</div>

	<div class="content-desktop">
		<div class="burger-container">
			<label for="menu-toggle" class="hamburger">
				<img src={hamburgerIcon} alt="Icono hamburger" />
			</label>
		</div>
		<a class="logo-link" href="/">
			<div class="logo">
				<p>FOR-HIM</p>
				<p>GROOMING & BEYOND</p>
			</div>
		</a>
		<div class="other-icons">
			<img src={searchIcon} alt="Icono busqueda" />
			<img src={shoppingCart} alt="Icono carrito" />
			<a href={perfilUrl}>
				<img src={userProfile} alt="Icono perfil" />
			</a>
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

		> .content {
			display: flex;
			flex-direction: row;
			flex-wrap: nowrap;
			justify-content: space-between;
			padding: 30px 20px;
			align-items: center;

			> .logo-link {
				text-decoration: none;

				> .logo {
					display: flex;
					flex-direction: column;
					align-items: center;
					color: #ffffff;

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

			> img {
				display: block;
				object-fit: cover;
			}
		}

		> .content-desktop {
			display: none;
		}
	}

	@media (min-width: 1024px) {
		header {
			.content {
				display: none;
			}

			.content-desktop {
				display: flex;
				padding: 30px 20px;
				align-items: center;
				justify-content: space-between;

				> .burger-container {
					> img {
						width: 30px;
					}
				}

				> .logo-link {
					text-decoration: none;

					> .logo {
						display: flex;
						flex-direction: column;
						align-items: center;
						color: #ffffff;

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

				> .other-icons {
					display: flex;
					gap: 10px;

					> img,
					> a > img {
						width: 30px;
					}
				}
			}
		}
	}
</style>