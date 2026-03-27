<script>
	import hamburgerIcon from '$lib/assets/header-icons/hamburger-icon.svg';
	import searchIcon from '$lib/assets/header-icons/search-icon.svg';
	import shoppingCart from '$lib/assets/header-icons/shopping-cart.svg';
	import userProfile from '$lib/assets/header-icons/user-profile.svg';
</script>

<header>
	<div class="content">
		<img src={hamburgerIcon} alt="Icono hamburger" />
		<img src={searchIcon} alt="Icono busqueda" />
		<a class="logo-link" href="..">
			<div class="logo">
				<p>FOR-HIM</p>
				<p>GROOMING & BEYOND</p>
			</div>
		</a>

		<img src={shoppingCart} alt="Icono carrito" />
		<a href="./user-profile">
			<img src={userProfile} alt="Icono perfil" />
		</a>
	</div>
	<div class="content-desktop">
		<div class="burger-container">
			<img src={hamburgerIcon} alt="Icono hamburger" />
		</div>
		<a class="logo-link" href="..">
			<div class="logo">
				<p>FOR-HIM</p>
				<p>GROOMING & BEYOND</p>
			</div>
		</a>
		<div class="other-icons">
			<img src={searchIcon} alt="Icono busqueda" />
			<img src={shoppingCart} alt="Icono carrito" />
			<a href="./user-profile">
				<img src={userProfile} alt="Icono perfil" />
			</a>
		</div>
	</div>
</header>

<style lang="scss">
	header {
		background-color: #2d3e40;
		margin-bottom: 60px;

		> .content {
			display: flex;
			flex-direction: row;
			flex-wrap: nowrap;
			gap: 26px;
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
				display: block;
				display: flex;
				padding: 30px 20px;
				align-items: center;
				justify-content: space-between;

				> .burger-container {
					> img {
						width: 40px;
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

                >.other-icons {
                    display: flex;
                    gap: 10px;

                    > img, >a>img {
                        width: 50px;
                    }
                }
			}
		}
	}
</style>
