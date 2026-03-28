<!--
Card de producto que se muestra en la página home.
-->

<script>
	import producto from '$lib/assets/icons/Gomina.png';
	import cart from '$lib/assets/icons/cart.svg';
	import heart from '$lib/assets/icons/heart.svg';
</script>

<a href="./product-detail">
	<div class="card">
		<div class="image">
			<img src={producto} alt="Imagen del producto" />
		</div>
		<div class="card-data">
			<p>Nombre de producto</p>
			<p>Tipo de producto</p>
			<div class="price">
				<p>Precio (€)</p>
				<p>8,99</p>
			</div>
			<div class="buttons">
				<img class="icon-button" src={heart} alt="Agregar a favoritos" />
				<img class="icon-button" src={cart} alt="Agregar al carrito" />
			</div>
		</div>
	</div>
</a>

<style lang="scss">

		
	a {
		text-decoration: none;

		.card {
			width: min(90vw, 360px);
			height: auto;
			background: #387373;
			border-radius: 16px;
			display: flex;
			padding: 12px;
			gap: 12px;
			align-items: center;
			overflow: hidden;
			box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
			transition: transform 0.25s ease, box-shadow 0.25s ease;

			&:hover {
				transform: translateY(-4px);
				box-shadow: 0 6px 18px rgba(0, 0, 0, 0.22);
			}
		}

		.image {
			width: 120px;
			height: 120px;
			background: #f2f2f2;
			border-radius: 14px;
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;

			img {
				width: 100%;
				height: 100%;
				object-fit: cover;
			}
		}

		.card-data {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			height: 100%;

			p {
				margin: 0;
				color: white;

				&:first-child {
					font-size: 14px;
					font-weight: 700;
				}

				&:nth-child(2) {
					font-size: 12px;
					opacity: 0.9;
				}
			}

			.price {
				margin-top: 6px;
				display: flex;
				justify-content: space-between;

				p:last-child {
					font-size: 15px;
					font-weight: 700;
				}
			}

			.buttons {
				display: flex;
				justify-content: center;
				gap: 12px;
				margin-top: auto;

				.icon-button {
					width: 36px;
					height: 36px;
					background: #97a6a0;
					border-radius: 50%;
					margin-top: 10px;
					padding: 4px;
					display: flex;
					align-items: center;
					justify-content: center;
					cursor: pointer;
					transition: transform 0.2s ease, filter 0.2s ease;

					&:hover {
						transform: scale(1.1);
						filter: brightness(1.15);
					}
				}
			}
		}
	}

	@media (min-width: 1024px) {
		
		
		a {
			.card {
				width: 420px;
				height: 200px;
				padding: 14px;
				gap: 16px;
			}

			.image {
				width: 140px;
				height: 140px;
			}

			.card-data p:first-child {
				font-size: 20px;
			}

			.card-data p:nth-child(2) {
				font-size: 15px;
			}

			.price p:last-child {
				font-size: 22px;
			}
		}
	}
</style>