<!--
Card de producto que se muestra en la página home.
-->

<script>
	let { data } = $props();

	import { onMount } from 'svelte';

	import cart from '$lib/assets/icons/cart.svg';
	import heart from '$lib/assets/icons/heart.svg';


	let favorites = $state([]);
	let id = $derived(Number(data.id));
	let isFavorite = $derived(favorites.includes(id));

	onMount(() => {
		const stored = localStorage.getItem('favorites');
		favorites = stored
		? [...new Set(JSON.parse(stored).map(Number))]
		: [];
	});

	function toggleFavorite(event) {
	event.preventDefault();
	event.stopPropagation();

	const stored = localStorage.getItem('favorites');
	let currentFavorites = stored
		? JSON.parse(stored).map(Number)
		: [];

	if (currentFavorites.includes(id)) {
		currentFavorites = currentFavorites.filter((fav) => fav !== id);
	} else {
		currentFavorites = [...currentFavorites, id];
	}

	
	currentFavorites = [...new Set(currentFavorites)];

	localStorage.setItem('favorites', JSON.stringify(currentFavorites));
	favorites = currentFavorites;



}

function addToCart(e) {
	e.preventDefault();
	e.stopPropagation();

	const stored = localStorage.getItem('cart');
	let cart = stored ? JSON.parse(stored) : [];

	const id = Number(data.id);

	const index = cart.findIndex((p) => Number(p.id) === id);

	if (index !== -1) {
		
		cart[index] = {
			...cart[index],
			cantidad: cart[index].cantidad + 1
		};
	} else {
		cart.push({
			id,
			nombre: data.nombre_producto,
			precio: data.precio,
			imagen: data.imagenes?.[0],
			cantidad: 1
		});
	}

	localStorage.setItem('cart', JSON.stringify(cart));
}
</script>

<a href="./product-detail/{data.id}">
	<div class="card">
		<div class="image">
			<img src={data.imagenes[0]} alt="Imagen del producto" />
		</div>

		<div class="card-data">
			<p>{data.nombre_producto}</p>
			<p>{data.tipo_producto}</p>

			<div class="price">
				<p>Precio (€)</p>
				<p>{data.precio.toFixed(2)}</p>
			</div>

			<div class="buttons">
				<button
					type="button"
					class="icon-button"
					class:active={isFavorite}
					onclick={(e) => {
						e.stopPropagation();
						toggleFavorite(e);
					}}
					aria-label="Agregar a favoritos"
				>
					<img src={heart} alt="" />
				</button>

				<button
	type="button"
	class="icon-button"
	onclick={addToCart}
	aria-label="Agregar al carrito"
>
	<img src={cart} alt="carrito" />
</button>
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
			transition:
				transform 0.25s ease,
				box-shadow 0.25s ease;

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
					transition:
						transform 0.2s ease,
						filter 0.2s ease;

					&:hover {
						transform: scale(1.1);
						filter: brightness(1.15);
					}
				}

				
				.icon-button {
					width: 36px;
					height: 36px;
					background: #97a6a0;
					border-radius: 50%;
					margin-top: 10px;
					padding: 6px;
					display: flex;
					align-items: center;
					justify-content: center;
					cursor: pointer;
					transition:
						transform 0.2s ease,
						filter 0.2s ease;
					border: none;
				}

				.icon-button img {
					width: 100%;
					height: 100%;
					object-fit: contain;
					transition: filter 0.2s ease;
				}

				
				.icon-button.active {
					background: #97a6a0;
				}

				.icon-button.active img {
					filter: invert(27%) sepia(86%) saturate(7470%) hue-rotate(348deg) brightness(95%);
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
